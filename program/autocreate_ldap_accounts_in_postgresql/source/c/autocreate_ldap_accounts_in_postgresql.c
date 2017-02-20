/**
 * Helper program for auto-creating ldap accounts.
 *
 * This was written originally using sockets, but it makes more sense to run this on the database server (for security reasons).
 * - The original socket code is left alone, but is not used.
 *
 * The program expects the following parameters: [user_name] [group_name] [database_name] [listen_port].
 *
 * The system will listen on the socket waiting on a valid username to create.
 * This only accept usernames with alphanumeric, '-', or '_' in their name.
 * - All other characters will result in an error.
 * - Some username patterns will be blacklisted. (@todo: implement this via regex.)
 *
 * A packet size of PACKET_SIZE_INPUT is defined to ensure that the string is operated on only after all data is received.
 * - A NULL byte before the PACKET_SIZE_INPUT is reached will also terminate the packet.
 *
 * @todo: review this functionality "http://www.postgresql.org/docs/current/static/libpq-notice-processing.html".
 *
 * Compiled with:
 *   gcc  -lpq -lldap autocreate_ldap_accounts_in_postgresql.c -o autocreate_ldap_accounts_in_postgresql
 *
 * Role created with:
 *   create role create_ldap_users createrole;
 *   alter role create_ldap_users login;
 *
 * @todo: when implementing the init script, the pid can be created via: ps --no-headers -o pid -p $(cat fcs.pid)
 *
 * Copyright Kevin Day, lgpl v2.1 or later.
 */
#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <signal.h>
#include <sched.h>
#include <netdb.h>
#include <limits.h>
#include <syslog.h>
#include <time.h>

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/un.h>
#include <sys/stat.h>
#include <sys/signalfd.h>
#include <sys/syscall.h>
#include <sys/wait.h>

#include <netinet/in.h>

#include <ldap.h>
#include <libpq-fe.h>

// select either socket or network method.
//#define USE_SOCKET     1
#define USE_NETWORK    1
//#define DEBUG_ENABLED  1

#define LOG_ID    "autocreate_ldap_accounts_in_postgresql: "
#define PATH_PID  "/var/run/autocreate_ldap_accounts_in_postgresql/%s.pid"

// by granting a postgresql user the same access as a specified role, one can easily manage access by only setting permissions on the role.
// for consistency purposes, I suggest individual users have something like 'fcs_user' while the role/group should be something like 'fcs_users'.
// with this design admin users need to be manually updated on the database to have access to create users as well.
// admin users would then need something like this: "grant fcs_users to kday with admin option;".
#define PSQL_SELECT             "select rolname from pg_roles where rolname = '%s';"
#define PSQL_SELECT_LENGTH      48
#define PSQL_CREATE             "create role %s with login inherit;"
#define PSQL_CREATE_LENGTH      32
#define PSQL_GRANT              "grant %s to %s;"
#define PSQL_GRANT_LENGTH       11
//#define PSQL_CONNECTION         "host=127.0.0.1 port=5433 dbname=%s connect_timeout=2 sslmode=require user= password="
#define PSQL_CONNECTION         "port=5433 dbname=%s connect_timeout=2 sslmode=disable user=%s password=%s"
#define PSQL_CONNECTION_LENGTH  73

#define PARAMETER_LENGTH_MAX 96

#define LDAP_SERVER            "ldaps://ldap.example.com:1636"
#define LDAP_SEARCH_DN         "uid=%s,ou=users,ou=People"
#define LDAP_SEARCH_DN_LENGTH  47

#define LDAP_RETRY_BIND_RETRY      4
#define LDAP_RETRY_BIND_TIMEOUT    200000 // (microseconds) 0.2 second timeout.
#define LDAP_RETRY_SEARCH_RETRY    4
#define LDAP_RETRY_SEARCH_TIMEOUT  200000 // (microseconds) 0.2 second timeout.

#define PACKET_SIZE_INPUT   63
#define PACKET_SIZE_OUTPUT  1

// if stack size is too small, then on some systems (generally glibc based ones) will segfault/illegal-instruction under certain circumstances.
// in this case it the circumstance happens with clone(), ldap_initialize(), and glibc.
//#define STACK_SIZE 8192
#define STACK_SIZE 65536

#define PROTOCOL_NULL    0
#define PROTOCOL_SOCKET  SOL_SOCKET
#define PROTOCOL_TCP     6
#define PROTOCOL_UDP     17

#define SOCKET_TYPE     SOCK_STREAM
#define SOCKET_BACKLOG  15

#ifdef USE_NETWORK
  #define SOCKET_FAMILY    AF_INET // 'family' is also called 'domain' in this case.
  #define SOCKET_PROTOCOL  PROTOCOL_TCP
  #define SOCKET_TIMEOUT   160000 // 0.16 seconds.
#elif defined USE_SOCKET
  #define SOCKET_FAMILY       AF_UNIX // 'family' is also called 'domain' in this case.
  #define SOCKET_PATH         "/var/www/sockets/autocreate_ldap_accounts_in_postgresql/%s/%s.socket"
  #define SOCKET_PATH_LENGTH  64
  #define SOCKET_PROTOCOL     PROTOCOL_NULL
  #define SOCKET_PORT         0
  #define SOCKET_TIMEOUT      10000 // (microseconds) 0.01 seconds.
#endif // USE_SOCKET

#define FLAGS_RECEIVE  0
#define FLAGS_SEND     MSG_NOSIGNAL
#define FLAGS_CLONE    CLONE_FILES | CLONE_FS | CLONE_IO | CLONE_VM | CLONE_SIGHAND | CLONE_THREAD


// environment variables used.
#define ENVIRONMENT_CONNECT_USER      "alap_connect_user"
#define ENVIRONMENT_CONNECT_PASSWORD  "alap_connect_password"

#define ENVIRONMENT_MAX_CONNECT_USER      128 // maximum characters to be supported for the connect name.
#define ENVIRONMENT_MAX_CONNECT_PASSWORD  512 // maximum characters to be supported for the connect password.


// note: these are strings instead of integers so that they act as binary data when passed as a string via the socket.
#define ERROR_NONE      "\x00"
#define ERROR_NAME      "\x01" // invalid user name, bad characters, or name too long.
#define ERROR_LDAP      "\x02" // failed to connect to the ldap server and could not query the ldap name.
#define ERROR_USER      "\x03" // user name not found in ldap database.
#define ERROR_DATABASE  "\x04" // failed to connect to the database.
#define ERROR_SQL       "\x05" // error returned while executing the SQL command.
#define ERROR_READ      "\x06" // error occured while reading input from the user (such as via recv()).
#define ERROR_WRITE     "\x07" // error occured while writing input from the user (such as via send()).
#define ERROR_PACKET    "\x08" // the received packet is invalid, such as wrong length.
#define ERROR_TIMEOUT   "\x09" // connection timed out when reading or writing.
#define ERROR_CLOSE     "\x0a" // the connection is being forced closed.
#define ERROR_QUIT      "\x0b" // the connection is closing because the service is quitting.

#define PROBLEM_COUNT_MAX_SIGNAL_SIZE  10

#ifdef USE_NETWORK
  #define MACRO_EXIT_STANDARD_1(shared, stack, exit_code) \
    if (shared.socket_id_client > 0) { \
      send(shared.socket_id_client, ERROR_QUIT, PACKET_SIZE_OUTPUT, FLAGS_SEND); \
      shutdown(shared.socket_id_client, SHUT_RDWR); \
    } \
    \
    if (shared.socket_id_target > 0) { \
      shutdown(shared.socket_id_target, SHUT_RDWR); \
    } \
    \
    if (shared.socket_bound > 0) { \
      shared.socket_bound = 0; \
    } \
    \
    if (stack != NULL) { \
      free(stack); \
      stack = NULL; \
    } \
    \
    if (shared.pid_path != NULL) { \
      unlink(shared.pid_path); \
      free(shared.pid_path); \
      shared.pid_path = NULL; \
    } \
    \
    memset(&shared, 0, sizeof(shared_data)); \
    \
    return exit_code;

  typedef struct {
    char parameter_system[PARAMETER_LENGTH_MAX];
    char parameter_group[PARAMETER_LENGTH_MAX];
    char parameter_database[PARAMETER_LENGTH_MAX];
    char parameter_connect_name[ENVIRONMENT_MAX_CONNECT_USER];
    char parameter_connect_password[ENVIRONMENT_MAX_CONNECT_PASSWORD];

    int socket_id_target;
    int socket_id_client;
    int socket_bound;

    int parameter_port;

    pid_t pid_parent;
    pid_t pid_child;
    char *pid_path;
  } shared_data;
#elif defined USE_SOCKET
  #define MACRO_EXIT_STANDARD_1(shared, stack, exit_code) \
    if (shared.socket_id_client > 0) { \
      send(shared.socket_id_client, ERROR_QUIT, PACKET_SIZE_OUTPUT, FLAGS_SEND); \
      shutdown(shared.socket_id_client, SHUT_RDWR); \
    } \
    \
    if (shared.socket_id_target > 0) { \
      shutdown(shared.socket_id_target, SHUT_RDWR); \
    } \
    \
    if (shared.socket_bound > 0) { \
      shared.socket_bound = 0; \
    } \
    \
    if (stack != NULL) { \
      free(stack); \
      stack = NULL; \
    } \
    \
    if (shared.socket_path != NULL) { \
      unlink(shared.socket_path); \
    } \
    \
    if (shared.pid_path != NULL) { \
      unlink(shared.pid_path); \
      free(shared.pid_path); \
      shared.pid_path = NULL; \
    } \
    \
    memset(&shared, 0, sizeof(shared_data)); \
    \
    return exit_code;

  typedef struct {
    char parameter_system[PARAMETER_LENGTH_MAX];
    char parameter_group[PARAMETER_LENGTH_MAX];
    char parameter_database[PARAMETER_LENGTH_MAX];
    char parameter_connect_name[ENVIRONMENT_MAX_CONNECT_USER];
    char parameter_connect_password[ENVIRONMENT_MAX_CONNECT_PASSWORD];

    int socket_id_target;
    int socket_id_client;
    int socket_bound;

    char *socket_path;

    pid_t pid_parent;
    pid_t pid_child;
    char pid_path[PATH_MAX];
  } shared_data;
#endif // USE_SOCKET

#define MACRO_EXIT_STANDARD_2(pid_child, shared, stack, exit_code) \
  if (pid_child > 0) { \
    kill(pid_child, SIGQUIT); \
    pid_child = 0; \
  } \
  \
  MACRO_EXIT_STANDARD_1(shared, stack, exit_code)


/**
 * Immediately writes to system logger.
 *
 * @param const unsigned level
 *   The log messages level.
 *   LOG_ERR is the most common choice here.
 *   This is not the 'priority', which is auto-generated.
 *
 * @param const char *message
 *   The complete message string to write to the logger.
 *
 * @see vsyslog()
 * @see printf()
 */
void log_write(const int level, const char *message, ...) {
  va_list arguments;

  #ifdef DEBUG_ENABLED
    va_start(arguments, message);
    vprintf(message, arguments);
    fflush(stdout);
    va_end(arguments);
  #endif // DEBUG_ENABLED

  va_start(arguments, message);

  openlog(LOG_ID, LOG_PID | LOG_CONS, LOG_DAEMON);
  vsyslog(level | LOG_DAEMON, message, arguments);
  closelog();

  va_end(arguments);
}

/**
 * Grants the user access to the specified group in the postgresql database.
 *
 * @param char *user_name
 *   Name of the user/role to grant access to..
 * @param char *user_password
 *   Password for the database user.
 * @param char *group_name
 *   Name of the group.
 * @param char *database_name
 *   Name of the database.
  * @param char *connect_name
 *   Name of the role used to connect to the database.
 * @param char *connect_password
 *   Password for the role used to connect to the database.
 *
 * @return int
 *   1 on success and -1 on error.
 */
int grant_role_in_database(const char *user_name, const char *group_name, const char *database_name, const char *connect_name, const char *connect_password) {
  PGconn *connection = NULL;
  char *connection_information = NULL;

  {
    int connection_information_length = PSQL_CONNECTION_LENGTH + 1;

    connection_information_length += strnlen(database_name, PARAMETER_LENGTH_MAX);
    connection_information_length += strnlen(connect_name, PARAMETER_LENGTH_MAX);
    connection_information_length += strnlen(connect_password, PARAMETER_LENGTH_MAX);
    connection_information = malloc(sizeof(char) * connection_information_length);

    if (connection_information == NULL) {
      log_write(LOG_ERR, "ERROR: failed to allocate memory when building the postgresql connection information string while processing user '%s', group '%s', and database '%s'.\n", user_name, group_name, database_name);
      return -1;
    }

    memset(connection_information, 0, sizeof(char) * connection_information_length);
    snprintf(connection_information, connection_information_length, PSQL_CONNECTION, database_name, connect_name, connect_password);
  }

  connection = PQconnectdb(connection_information);

  if (connection == NULL) {
    log_write(LOG_ERR, "ERROR: failed to establish the postgresql connection while processing user '%s', group '%s', and database '%s', reason: NULL returned.\n", user_name, group_name, database_name);
    return -1;
  }
  else if (PQstatus(connection) != CONNECTION_OK) {
    log_write(LOG_ERR, "ERROR: failed to establish the postgresql connection while processing user '%s', group '%s', and database '%s', reason (%u): %s.\n", user_name, group_name, database_name, PQstatus(connection), PQerrorMessage(connection));
    PQfinish(connection);
    free(connection_information);
    connection_information = NULL;
    return -1;
  }

  // check to see if role exists and has
  short role_exists = 0;
  {
    char *query = NULL;
    int query_length = PSQL_SELECT_LENGTH + 1;

    query_length += strnlen(user_name, PARAMETER_LENGTH_MAX);
    query = malloc(sizeof(char) * query_length);

    if (query == NULL) {
      log_write(LOG_ERR, "ERROR: failed to allocate memory when building the postgresql select query string while processing user '%s', group '%s', and database '%s'.\n", user_name, group_name, database_name);

      PQfinish(connection);
      free(connection_information);
      connection_information = NULL;
      return -1;
    }

    memset(query, 0, sizeof(char) * query_length);
    snprintf(query, query_length, PSQL_SELECT, user_name);

    {
      PGresult *result = NULL;

      result = PQexec(connection, query);

      int status = PQresultStatus(result);

      if (status == PGRES_EMPTY_QUERY) {
        //role_exists = 0;
      }
      else if (status == PGRES_COMMAND_OK || status == PGRES_TUPLES_OK) {
        if (PQnfields(result) > 0 && PQntuples(result) > 0) {
          role_exists = 1;
        }
      }
      else {
        PQclear(result);
        PQfinish(connection);

        free(query);
        free(connection_information);

        result = NULL;
        query = NULL;
        connection_information = NULL;

        return -1;
      }

      PQclear(result);
      result = NULL;
    }

    free(query);
    query = NULL;
  }

  // Create the specified role.
  if (role_exists == 0) {
    char *query = NULL;
    int query_length = PSQL_CREATE_LENGTH + 1;

    query_length += strnlen(user_name, PARAMETER_LENGTH_MAX);
    query = malloc(sizeof(char) * query_length);

    if (query == NULL) {
      log_write(LOG_ERR, "ERROR: failed to allocate memory when building the postgresql creates query string while processing user '%s', group '%s', and database '%s'.\n", user_name, group_name, database_name);

      PQfinish(connection);
      free(connection_information);
      connection_information = NULL;
      return -1;
    }

    memset(query, 0, sizeof(char) * query_length);
    snprintf(query, query_length, PSQL_CREATE, user_name);

    {
      PGresult *result = NULL;

      result = PQexec(connection, query);

      int status = PQresultStatus(result);

      if (status != PGRES_EMPTY_QUERY && status != PGRES_COMMAND_OK && status != PGRES_TUPLES_OK) {
        log_write(LOG_ERR, "ERROR: failed to process sql query '%s', reason (%u): %s.\n", query, status, PQerrorMessage(connection));

        PQclear(result);
        PQfinish(connection);

        free(query);
        free(connection_information);

        result = NULL;
        query = NULL;
        connection_information = NULL;

        return -1;
      }

      PQclear(result);
      result = NULL;
    }

    free(query);
    query = NULL;
  }

  // grant the user access to the specified role.
  {
    char *query = NULL;
    int query_length = PSQL_GRANT_LENGTH + 1;

    query_length += strnlen(group_name, PARAMETER_LENGTH_MAX);
    query_length += strnlen(user_name, PARAMETER_LENGTH_MAX);
    query = malloc(sizeof(char) * query_length);

    if (query == NULL) {
      log_write(LOG_ERR, "ERROR: failed to allocate memory when building the postgresql grant query string while processing user '%s', group '%s', and database '%s'.\n", user_name, group_name, database_name);

      PQfinish(connection);
      free(connection_information);
      connection_information = NULL;
      return -1;
    }

    memset(query, 0, sizeof(char) * query_length);
    snprintf(query, query_length, PSQL_GRANT, group_name, user_name);

    {
      PGresult *result = NULL;

      result = PQexec(connection, query);

      int status = PQresultStatus(result);

      if (status != PGRES_EMPTY_QUERY && status != PGRES_COMMAND_OK && status != PGRES_TUPLES_OK) {
        log_write(LOG_ERR, "ERROR: failed to process sql query '%s', reason (%u): %s.\n", query, status, PQerrorMessage(connection));

        PQclear(result);
        PQfinish(connection);

        free(query);
        free(connection_information);

        result = NULL;
        query = NULL;
        connection_information = NULL;

        return -1;
      }

      PQclear(result);
      result = NULL;
    }

    free(query);
    query = NULL;
  }

  if (connection_information != NULL) {
    PQfinish(connection);
    free(connection_information);
  }

  return 1;
}

/**
 * Queries the name in the ldap server to see if it exists.
 *
 * @param const char *user_name
 *   The user name to query in the ldap database.
 *
 * @return bool
 *   1 on found, 0 on not found, and -1 on error.
 */
int does_name_exist_in_ldap(const char *user_name) {
  int user_name_length = 0;
  int ldap_name_length = 0;
  int ldap_status = 0;
  LDAP *ldap_settings = NULL;
  char *ldap_name = NULL;

  user_name_length = strnlen(user_name, PACKET_SIZE_INPUT);
  ldap_name_length = user_name_length + LDAP_SEARCH_DN_LENGTH;

  ldap_name = malloc(sizeof(char) * (ldap_name_length + 1));
  if (ldap_name == NULL) {
    log_write(LOG_ERR, "ERROR: failed to allocate memory when building the ldap user name for the user: '%s'.\n", user_name);
    return -1;
  }

  memset(ldap_name, 0, sizeof(char) * (ldap_name_length + 1));
  sprintf(ldap_name, LDAP_SEARCH_DN, user_name);


  ldap_status = ldap_initialize(&ldap_settings, LDAP_SERVER);

  if (ldap_status != LDAP_SUCCESS) {
    log_write(LOG_ERR, "ERROR: failed to initialize ldap settings for the ldap server '%s' with the ldap name '%s' with the ldap error (%d): %s.\n", LDAP_SERVER, ldap_name, ldap_status, ldap_err2string(ldap_status));

    if (ldap_name != NULL) {
      free(ldap_name);
    }
    return -1;
  }


  // a bind is ldap's way of saying 'login' or 'authenticate', do no use string to search with bind.
  {
    int tries = 0;
    for (; tries < LDAP_RETRY_BIND_RETRY; tries++) {
      ldap_status = ldap_simple_bind_s(ldap_settings, "", "");

      if (ldap_status == LDAP_SUCCESS) {
        break;
      }
      else if (ldap_status == LDAP_SERVER_DOWN) {
        if (tries + 1 < LDAP_RETRY_BIND_RETRY) {
          continue;
        }
      }
      else if (ldap_status == LDAP_TIMEOUT) {
        if (tries + 1 < LDAP_RETRY_BIND_RETRY) {
          continue;
        }
      }

      log_write(LOG_ERR, "ERROR: failed to connect and bind to the ldap server '%s' with the ldap name '%s' with the ldap error (%d): %s\n", LDAP_SERVER, ldap_name, ldap_status, ldap_err2string(ldap_status));

      if (ldap_name != NULL) {
        free(ldap_name);
      }
      return -1;
    }
  }


  // once bound, perform the search.
  {
    struct timeval ldap_timeout;
    int ldap_sizelimit = 1;
    int ldap_message_type = 0;
    LDAPMessage *ldap_message = NULL;
    int ldap_matched = 0;

    memset(&ldap_timeout, 0, sizeof(struct timeval));
    ldap_timeout.tv_sec = 0;
    ldap_timeout.tv_usec = LDAP_RETRY_SEARCH_TIMEOUT;

    {
      int tries = 0;
      for (; tries < LDAP_RETRY_SEARCH_RETRY; tries++) {
        ldap_status = ldap_search_ext_s(ldap_settings, ldap_name, LDAP_SCOPE_BASE, NULL, NULL, 0, NULL, NULL, &ldap_timeout, ldap_sizelimit, &ldap_message);

        ldap_message_type = ldap_msgtype(ldap_message);

        if (ldap_status == LDAP_SUCCESS) {
          ldap_matched = ldap_count_entries(ldap_settings, ldap_message);

          // From manpage: "Note that res parameter of ldap_search_ext_s() and ldap_search_s() should be freed with ldap_msgfree() regardless of return value of these functions"
          ldap_msgfree(ldap_message);

          break;
        }

        // From manpage: "Note that res parameter of ldap_search_ext_s() and ldap_search_s() should be freed with ldap_msgfree() regardless of return value of these functions"
        ldap_msgfree(ldap_message);

        if (ldap_status == LDAP_SERVER_DOWN) {
          if (tries + 1 < LDAP_RETRY_SEARCH_RETRY) {
            continue;
          }
        }
        else if (ldap_status == LDAP_TIMEOUT) {
          if (tries + 1 < LDAP_RETRY_SEARCH_RETRY) {
            continue;
          }
        }

        log_write(LOG_ERR, "ERROR: failed to find '%s' on the ldap server '%s' with the ldap name '%s' with the ldap error (%d): %s\n", user_name, LDAP_SERVER, ldap_name, ldap_status, ldap_err2string(ldap_status));

        if (ldap_settings != NULL) {
          ldap_unbind(ldap_settings);
        }

        if (ldap_name != NULL) {
          free(ldap_name);
        }
        return -1;
      }
    }

    // Ldap is no longer needed.
    if (ldap_settings != NULL) {
      ldap_unbind(ldap_settings);
    }

    if (ldap_name != NULL) {
      free(ldap_name);
    }

    if (ldap_matched == 0) {
      return 0;
    }
  }

  return 1;
}

/**
 * Handles network connections.
 *
 * This is called by clone().
 *
 * @param void *argument
 *   The data shared between the parent and cloned child.
 *
 * @see: clone()
 */
int handler_child(void *argument) {
  // do no accept/allow signals in the child handler.
  sigset_t signal_mask;
  sigemptyset(&signal_mask);
  sigprocmask(SIG_BLOCK, &signal_mask, NULL);

  shared_data *shared;
  shared = (shared_data *) argument;

  //shared->pid_child = syscall(SYS_gettid);
  shared->pid_child = getpid();

  log_write(LOG_DEBUG, "DEBUG: after clone (child) pid = %u, child pid = %u, target socket id = %u\n", shared->pid_parent, shared->pid_child, shared->socket_id_target);

  const unsigned structure_socket_length = sizeof(struct sockaddr_un);

  #ifdef USE_NETWORK
    {
      // bind the socket to port.
      struct addrinfo *port_information = NULL;
      struct addrinfo port_setup;

      memset(&port_setup, 0, sizeof(struct addrinfo));

      port_setup.ai_family = INADDR_ANY;
      port_setup.ai_socktype = SOCKET_TYPE;
      port_setup.ai_flags = AI_PASSIVE;

      {
        char string_port[16];
        memset(&string_port, 0, sizeof(char) * 16);

        sprintf(string_port, "%u", shared->parameter_port);

        int addressed = getaddrinfo(NULL, string_port, &port_setup, &port_information);
        if (addressed != 0) {
          log_write(LOG_ERR, "ERROR: failed to process the port '%u' using protocol '%u', 'socket id = '%i': error %i (%u).\n", shared->parameter_port, SOCKET_PROTOCOL, shared->pid_child, addressed, errno);

          freeaddrinfo(port_information);
          port_information = NULL;

          // send SIGCHLD signal to parent process.
          if (shared->pid_parent > 0) {
            kill(shared->pid_parent, SIGCHLD);
          }
          return -1;
        }
      }

      if (shared->socket_bound == 0) {
        shared->socket_bound = bind(shared->socket_id_target, port_information->ai_addr, port_information->ai_addrlen);
        if (shared->socket_bound < 0) {
          log_write(LOG_ERR, "ERROR: failed to bind the port '%u' using protocol '%u', 'socket id = '%i': error %i (%u).\n", shared->parameter_port, SOCKET_PROTOCOL, shared->pid_child, shared->socket_bound, errno);
          shared->socket_bound = 0;

          freeaddrinfo(port_information);
          port_information = NULL;

          // send SIGQUIT signal to parent process.
          if (shared->pid_parent > 0) {
            kill(shared->pid_parent, SIGQUIT);
          }
          return -1;
        }

        shared->socket_bound = 1;
      }

      freeaddrinfo(port_information);
      port_information = NULL;
    }
  #elif defined USE_SOCKET
    {
      // bind the socket to the shared.socket_path so that the
      struct sockaddr_un socket_address;

      memset(&socket_address, 0, structure_socket_length);
      socket_address.sun_family = SOCKET_FAMILY;
      strncpy(socket_address.sun_path, shared->socket_path, sizeof(socket_address.sun_path) - 1);

      {
        int bound = bind(shared->socket_id_target, (struct sockaddr *) &socket_address, structure_socket_length);
        if (bound < 0) {
          log_write(LOG_ERR, "ERROR: failed to bind the socket '%s' using protocol '%u', 'socket id = '%i'\n", shared->socket_path, SOCKET_PROTOCOL, shared->socket_id_target);

          // send SIGQUIT signal to parent process.
          if (shared->pid_parent > 0) {
            kill(shared->pid_parent, SIGQUIT);
          }
          return -1;
        }
      }
    }
  #endif // USE_SOCKET

  {
    int listening = listen(shared->socket_id_target, SOCKET_BACKLOG);

    if (listening < 0) {
      #ifdef USE_NETWORK
        log_write(LOG_ERR, "ERROR: failed to listen to the port '%u' using protocol '%u', 'socket id = '%i', error %i (%u).\n", shared->parameter_port, SOCKET_PROTOCOL, shared->pid_child, listening, errno);
      #elif defined USE_SOCKET
        log_write(LOG_ERR, "ERROR: failed to listen to the socket '%s' using protocol '%u', 'socket id = '%i', error %i (%u).\n", shared->socket_path, SOCKET_PROTOCOL, shared->pid_child, listening, errno);
      #endif // USE_SOCKET

      // send SIGQUIT signal to parent process.
      if (shared->pid_parent > 0) {
        kill(shared->pid_parent, SIGQUIT);
      }
      return -1;
    }
  }

  int socket_error = 0;
  socklen_t socket_error_length = 0;

  int message_length = 0;
  int i = 0;
  int processed = 0;

  socklen_t length = 0;
  ssize_t sent = 0;

  char buffer[PACKET_SIZE_INPUT];
  char user_name[PACKET_SIZE_INPUT];
  char *error_receive = ERROR_NONE;

  struct sockaddr_un socket_client_address;

  struct timeval timeout;
  timeout.tv_sec = 0;
  timeout.tv_usec = SOCKET_TIMEOUT;

  while (1) {
    length = structure_socket_length;
    processed = 0;
    error_receive = ERROR_NONE;

    // make sure that socket_id_client is always closed before continuing.
    if (shared->socket_id_client != 0) {
      sent = send(shared->socket_id_client, ERROR_CLOSE, PACKET_SIZE_OUTPUT, FLAGS_SEND);
      close(shared->socket_id_client);
      shared->socket_id_client = 0;
    }

    memset(&socket_client_address, 0, structure_socket_length);

    shared->socket_id_client = accept(shared->socket_id_target, (struct sockaddr *) &socket_client_address, &length);

    if (shared->socket_id_client < 0) {
      #ifdef USE_NETWORK
        log_write(LOG_ERR, "ERROR: failed to accept connections on the port '%u' using protocol '%u': error %i (%u).\n", shared->parameter_port, SOCKET_PROTOCOL, shared->socket_id_client, errno);
      #elif defined USE_SOCKET
        log_write(LOG_ERR, "ERROR: failed to accept connections on the socket '%s' using protocol '%u': error %i (%u).\n", shared->socket_path, SOCKET_PROTOCOL, shared->socket_id_client, errno);
      #endif // USE_SOCKET

      shared->socket_id_client = 0;

      // send SIGQUIT signal to parent process.
      if (shared->pid_parent > 0) {
        kill(shared->pid_parent, SIGQUIT);
      }

      return -1;
    }

    memset(&buffer, 0, sizeof(char) * PACKET_SIZE_INPUT);
    memset(&user_name, 0, sizeof(char) * PACKET_SIZE_INPUT);

    // define a very short timeout for fast responses (both send and receive).
    // the socket is expected to be a local connection, so it should be very fast.
    setsockopt(shared->socket_id_client, PROTOCOL_SOCKET, SO_RCVTIMEO, (char *)&timeout, sizeof(timeout));
    setsockopt(shared->socket_id_client, PROTOCOL_SOCKET, SO_SNDTIMEO, (char *)&timeout, sizeof(timeout));

    // linger connection for at most 2 seconds to help properly close connections.
    {
      struct linger linger_value = {1, 2};
      setsockopt(shared->socket_id_target, PROTOCOL_SOCKET, SO_LINGER, &linger_value, sizeof(struct linger));
    }

    // receive the user name from the client.
    do {
      error_receive = ERROR_NONE;
      message_length = recv(shared->socket_id_client, buffer, PACKET_SIZE_INPUT, FLAGS_RECEIVE);

      if (message_length == 0) {
        // this happens on proper client connection termination.
        close(shared->socket_id_client);
        shared->socket_id_client = 0;
        break;
      }
      else if (message_length < 0) {
        // this is not working as expected, socket_error is 0 when something like EAGAIN is expected.
        // the idea here is to detect a timeout and report ERROR_TIMEOUT instead of ERROR_READ.
        // until this is figured out, assume that ERROR_READ is the same as ERROR_TIMEOUT.
        // look into recvmsg().
        //socket_error = 0;
        //socket_error_length = sizeof(int);
        //getsockopt(shared->socket_id_client, PROTOCOL_SOCKET, SO_ERROR, (void *) &socket_error, &socket_error_length);
        send(shared->socket_id_client, ERROR_READ, PACKET_SIZE_OUTPUT, FLAGS_SEND);
        break;
      }

      // require a valid packet length.
      if (message_length > PACKET_SIZE_INPUT) {
        sent = send(shared->socket_id_client, ERROR_PACKET, PACKET_SIZE_OUTPUT, FLAGS_SEND);
        break;
      }

      // only allow the following ASCII characters in the user name (utf8 should be fine for all codes that match the ASCII table).
      for (i = 0; i < message_length && processed < PACKET_SIZE_INPUT; i++) {
        // if a NULL char is reached, then the packet is finished.
        if (buffer[i] == 0) {
          // force the loop to terminate without processing any more packet data.
          processed = PACKET_SIZE_INPUT;
          break;
        }

        if ((buffer[i] < 'a' || buffer[i] > 'z') && (buffer[i] < 'A' || buffer[i] > 'Z') && (buffer[i] < '0' || buffer[i] > '9')) {
          if (buffer[i] != '-' && buffer[i] != '_') {
            error_receive = ERROR_NAME;
            break;
          }
        }

        if (processed + 1 > PACKET_SIZE_INPUT) {
          sent = send(shared->socket_id_client, ERROR_PACKET, PACKET_SIZE_OUTPUT, FLAGS_SEND);
          break;
        }

        user_name[processed] = buffer[i];
        processed++;
      } // for

      if (error_receive != ERROR_NONE) {
        sent = send(shared->socket_id_client, error_receive, PACKET_SIZE_OUTPUT, FLAGS_SEND);
        break;
      }

      // require processed to be populated in some manner at this point.
      if (processed == 0) {
        error_receive = ERROR_NAME;
        break;
      }
    } while (processed < PACKET_SIZE_INPUT);

    if (error_receive == ERROR_NONE) {
      int ldap_name_exists = 0;
      ldap_name_exists = does_name_exist_in_ldap(user_name);

      if (ldap_name_exists < 0) {
        sent = send(shared->socket_id_client, ERROR_LDAP, PACKET_SIZE_OUTPUT, FLAGS_SEND);
        shutdown(shared->socket_id_client, SHUT_RDWR);
        shared->socket_id_client = 0;
        continue;
      }
      else if (ldap_name_exists == 0) {
        sent = send(shared->socket_id_client, ERROR_NAME, PACKET_SIZE_OUTPUT, FLAGS_SEND);
        shutdown(shared->socket_id_client, SHUT_RDWR);
        shared->socket_id_client = 0;
        continue;
      }

      {
        int status = 0;
        status = grant_role_in_database(user_name, shared->parameter_group, shared->parameter_database, shared->parameter_connect_name, shared->parameter_connect_password);

        if (status < 0) {
          sent = send(shared->socket_id_client, ERROR_SQL, PACKET_SIZE_OUTPUT, FLAGS_SEND);
          shutdown(shared->socket_id_client, SHUT_RDWR);
          shared->socket_id_client = 0;
          continue;
        }
      }
    }

    if (shared->socket_id_client > 0) {
      // respond to the client for success or failure and then close the connection
      if (processed > 0) {
        sent = send(shared->socket_id_client, error_receive, PACKET_SIZE_OUTPUT, FLAGS_SEND);
      }

      shutdown(shared->socket_id_client, SHUT_RDWR);
      shared->socket_id_client = 0;
    }
  } // while

  // send SIGQUIT signal to parent process.
  if (shared->pid_parent > 0) {
    kill(shared->pid_parent, SIGQUIT);
  }

  return 0;
}

/**
 * Handle command line arguments
 *
 * @param int argc
 *   Total of command line arguments.
 * @param char *argv[]
 *   Array of command line argument strings.
 * @param char *parameter_system
 *   Name of the system, this value will be updated.
 * @param char *parameter_group
 *   Name of the group, this value will be updated.
 * @param char *parameter_database
 *   Name of the database, this value will be updated.
 * @param char *parameter_connect_name
 *   Name of the user to connect to the database as.
 * @param char *parameter_connect_password
 *   Password of the user to connect to the database as.
 * @param int *parameter_port
 *   Number of the port, this value will be updated.
 *   This only appears when USE_NETWORK is defined.
 *
 * @return int
 *   1 is returned on success, 0 on success but exit, and -1 on error.
 */
#ifdef USE_NETWORK
  int populate_parameters(int argc, char *argv[], char *parameter_system, char *parameter_group, char *parameter_database, char *parameter_connect_name, char *parameter_connect_password, int *parameter_port) {
#elif defined USE_SOCKET
  int populate_parameters(int argc, char *argv[], char *parameter_system, char *parameter_group, char *parameter_database, char *parameter_connect_name, char *parameter_connect_password) {
#endif // USE_SOCKET
  {
    int do_help = 0;
    char *program_name = "(program_name)";

    if (argc > 0) {
      program_name = argv[0];
    }

    if (argc >= 2) {
      int i = 0;

      for (; i < argc; i++) {
        if (strcmp(argv[i], "-h") == 0 || strcmp(argv[i], "--help") == 0) {
          do_help = 1;
          break;
        }
      }
    }

    #ifdef USE_NETWORK
      if (do_help == 0 && !(argc == 4 || argc == 5)) {
        printf("ERROR: This program requires four or five arguments (with max lengths of %u) 'system name', 'group name', 'database name', 'listen port', example: %s fcs fcs_users fcs_database 125.\n", PARAMETER_LENGTH_MAX, program_name);
        do_help = 2;
      }
    #elif defined USE_SOCKET
      if (do_help == 0 && !(argc == 3 || argc == 4)) {
        printf("ERROR: This program requires three or four arguments (with max lengths of %u) 'system name', 'group name', 'database name', example: %s fcs fcs_users fcs_database.\n", PARAMETER_LENGTH_MAX, program_name);
        do_help = 2;
      }
    #endif // USE_SOCKET

    if (do_help > 0) {
      printf("\n");

      #ifdef USE_NETWORK
        printf("%s [ system name ] [ group name ] [ database name ] [ listen port ]\n", program_name);
      #elif defined USE_SOCKET
        printf("%s [ system name ] [ group name ] [ database name ]\n", program_name);
      #endif // USE_SOCKET

      printf("  [ system name ]    This argument is used as the name of the socket file, which will end in '.socket'.\n");
      printf("  [ group name ]     This argument is used as the postgresql role to grant access for in the specified database.\n");
      printf("  [ database name ]  This argument is used as the postgresql database.\n");

      #ifdef USE_NETWORK
        printf("  [ listen port ]    This argument is the port to listen on to accept user names.\n");
      #endif // USE_NETWORK

      printf("\n");
      printf("Environment Variables:\n");
      printf("  The following environment variables must be defined:\n");
      printf("    %s      This parameter is used as the user to connect to the database as to perform operations.\n", ENVIRONMENT_CONNECT_USER);
      printf("    %s  This parameter is used as the password for the user connecting to the database.\n", ENVIRONMENT_CONNECT_PASSWORD);

      printf("\n");
      printf("Notes:\n");
      printf("  All names are limited to a max length of %u.\n", PARAMETER_LENGTH_MAX);
      printf("  Only Alphanumeric values and '_' or '-' are allowed in the names.\n");
      printf("  Specifically '_' and '-' are not allowed in the beginning or ending in a name.\n");
      printf("  The username to connect to as for the current user is used. Make sure the current user has access to the database in question.\n");
      printf("\n");

      if (do_help == 2) {
        return -1;
      }

      return 0;
    }
  }

  // make sure the system name and role names are valid names, restricted to alphanumeric and - or _, but does not begin with - or _.
  {
    int i = 0;
    int j = 0;


    // process system name.
    for (; i < PARAMETER_LENGTH_MAX; i++) {
      if ((argv[1][i] >= 'a' || argv[1][i] <= 'z') || (argv[1][i] >= 'A' || argv[1][i] <= 'Z') || (argv[1][i] >= '0' || argv[1][i] <= '9')) {
        parameter_system[j] = argv[1][i];
        j++;
        i++;
        break;
      }
      else if (argv[1][i] == 0) {
        break;
      }
      else {
        printf("ERROR: an invalid character '%c' has been specified in the supplied system name '%s'.\n", argv[1][i], argv[1]);
        return -1;
      }
    }

    for (; i < PARAMETER_LENGTH_MAX; i++) {
      if ((argv[1][i] >= 'a' || argv[1][i] <= 'z') || (argv[1][i] >= 'A' || argv[1][i] <= 'Z') || (argv[1][i] >= '0' || argv[1][i] <= '9')) {
        parameter_system[j] = argv[1][i];
        j++;
      }
      else if (argv[1][i] == '-' || argv[1][i] == '_') {
        parameter_system[j] = argv[1][i];
        j++;
      }
      else if (argv[1][i] == 0) {
        break;
      }
      else {
        printf("ERROR: an invalid character '%c' has been specified in the supplied system name '%s'.\n", argv[1][i], argv[1]);
        return -1;
      }
    }

    if (argv[1][j - 1] == '-' || argv[1][j - 1] == '_') {
      printf("ERROR: an invalid character '%c' has been specified in the supplied system name '%s'.\n", argv[1][i], argv[1]);
      return -1;
    }

    if (j == 0) {
      printf("ERROR: system name must not be an empty string.\n", argv[3][i], argv[3]);
      return -1;
    }


    // process group name.
    for (i = 0, j = 0; i < PARAMETER_LENGTH_MAX; i++) {
      if ((argv[2][i] >= 'a' || argv[2][i] <= 'z') || (argv[2][i] >= 'A' || argv[2][i] <= 'Z') || (argv[2][i] >= '0' || argv[2][i] <= '9')) {
        parameter_group[j] = argv[2][i];
        j++;
        i++;
        break;
      }
      else if (argv[2][i] == 0) {
        break;
      }
      else {
        printf("ERROR: an invalid character '%c' has been specified in the supplied group name '%s'.\n", argv[2][i], argv[2]);
        return -1;
      }
    }

    for (; i < PARAMETER_LENGTH_MAX; i++) {
      if ((argv[2][i] >= 'a' || argv[2][i] <= 'z') || (argv[2][i] >= 'A' || argv[2][i] <= 'Z') || (argv[2][i] >= '0' || argv[2][i] <= '9')) {
        parameter_group[j] = argv[2][i];
        j++;
      }
      else if (argv[2][i] == '-' || argv[2][i] == '_') {
        parameter_group[j] = argv[2][i];
        j++;
      }
      else if (argv[2][i] == 0) {
        break;
      }
      else {
        printf("ERROR: an invalid character '%c' has been specified in the supplied group name '%s'.\n", argv[2][i], argv[2]);
        return -1;
      }
    }

    if (argv[2][j - 1] == '-' || argv[2][j - 1] == '_') {
      printf("ERROR: an invalid character '%c' has been specified in the supplied group name '%s'.\n", argv[2][i], argv[2]);
      return -1;
    }

    if (j == 0) {
      printf("ERROR: group name must not be an empty string.\n", argv[3][i], argv[3]);
      return -1;
    }


    // process database name.
    for (i = 0, j = 0; i < PARAMETER_LENGTH_MAX; i++) {
      if ((argv[3][i] >= 'a' || argv[3][i] <= 'z') || (argv[3][i] >= 'A' || argv[3][i] <= 'Z') || (argv[3][i] >= '0' || argv[3][i] <= '9')) {
        parameter_database[j] = argv[3][i];
        j++;
        i++;
        break;
      }
      else if (argv[3][i] == 0) {
        break;
      }
      else {
        printf("ERROR: an invalid character '%c' has been specified in the supplied database name '%s'.\n", argv[3][i], argv[3]);
        return -1;
      }
    }

    for (; i < PARAMETER_LENGTH_MAX; i++) {
      if ((argv[3][i] >= 'a' || argv[3][i] <= 'z') || (argv[3][i] >= 'A' || argv[3][i] <= 'Z') || (argv[3][i] >= '0' || argv[3][i] <= '9')) {
        parameter_database[j] = argv[3][i];
        j++;
      }
      else if (argv[3][i] == '-' || argv[3][i] == '_') {
        parameter_database[j] = argv[3][i];
        j++;
      }
      else if (argv[3][i] == 0) {
        break;
      }
      else {
        printf("ERROR: an invalid character '%c' has been specified in the supplied database name '%s'.\n", argv[3][i], argv[3]);
        return -1;
      }
    }

    if (argv[3][j - 1] == '-' || argv[3][j - 1] == '_') {
      printf("ERROR: an invalid character '%c' has been specified in the supplied database name '%s'.\n", argv[3][i], argv[3]);
      return -1;
    }

    if (j == 0) {
      printf("ERROR: database name must not be an empty string.\n", argv[3][i], argv[3]);
      return -1;
    }


    #ifdef USE_NETWORK
      // first sanitize the parameter and ensure that only numbers are allowed.
      for (; i < PARAMETER_LENGTH_MAX; i++) {
        if (argv[4][i] >= '0' || argv[4][i] <= '9') {
          continue;
        }
        else if (argv[4][i] == 0) {
          break;
        }

        printf("ERROR: an invalid character '%c' has been specified in the supplied local port '%s' (only numbers are allowed).\n", argv[4][i], argv[4]);
        return -1;
      }

      *parameter_port = atoi(argv[4]);
    #endif // USE_NETWORK

    // process database connection user name.
    {
      char *user_name = getenv(ENVIRONMENT_CONNECT_USER);

      if (user_name) {
        size_t user_name_length = strnlen(user_name, ENVIRONMENT_MAX_CONNECT_USER);

        for (i = 0, j = 0; i < user_name_length; i++) {
          if ((user_name[i] >= 'a' || user_name[i] <= 'z') || (user_name[i] >= 'A' || user_name[i] <= 'Z') || (user_name[i] >= '0' || user_name[i] <= '9')) {
            parameter_connect_name[j] = user_name[i];
            j++;
            i++;
            break;
          }
          else if (user_name[i] == 0) {
            break;
          }
          else {
            printf("ERROR: an invalid character '%c' has been specified in the supplied database name '%s'.\n", user_name[i], user_name);
            return -1;
          }
        }

        for (; i < user_name_length; i++) {
          if ((user_name[i] >= 'a' || user_name[i] <= 'z') || (user_name[i] >= 'A' || user_name[i] <= 'Z') || (user_name[i] >= '0' || user_name[i] <= '9')) {
            parameter_connect_name[j] = user_name[i];
            j++;
          }
          else if (user_name[i] == '_') {
            parameter_connect_name[j] = user_name[i];
            j++;
          }
          else if (user_name[i] == 0) {
            break;
          }
          else {
            printf("ERROR: an invalid character '%c' has been specified in the supplied database name '%s'.\n", user_name[i], user_name);
            return -1;
          }
        }
      }
      else {
        printf("ERROR: Failed to load required environment variable '%s'.\n", ENVIRONMENT_CONNECT_USER);
        return -1;
      }
    }


    // process database connection password.
    {
      char *password = getenv(ENVIRONMENT_CONNECT_PASSWORD);

      if (password) {
        size_t password_length = strnlen(password, ENVIRONMENT_MAX_CONNECT_PASSWORD);

        if (password_length > 0) {
          strncpy(parameter_connect_password, password, password_length);
        }
      }
      else {
        printf("ERROR: Failed to load required environment variable '%s'.\n", ENVIRONMENT_CONNECT_PASSWORD);
        return -1;
      }
    }
  }

  return 1;
}

/**
 * Main Function
 *
 * @param int argc
 *   Total of command line arguments.
 * @param char *argv[]
 *   Array of command line argument strings.
 *
 * @return int
 *   The return status of the program.
 */
int main(int argc, char *argv[]) {
  shared_data shared;
  char *stack = NULL;

  memset(&shared, 0, sizeof(shared_data));

  // this pid will change once daemonized, but until then record the current pid.
  shared.pid_parent = getpid();

  {
    int populated = 0;

    #ifdef USE_NETWORK
      populated = populate_parameters(argc, argv, shared.parameter_system, shared.parameter_group, shared.parameter_database, shared.parameter_connect_name, shared.parameter_connect_password, &shared.parameter_port);
    #elif defined USE_SOCKET
      populated = populate_parameters(argc, argv, shared.parameter_system, shared.parameter_group, shared.parameter_database, shared.parameter_connect_name, shared.parameter_connect_password);
    #endif // USE_SOCKET


    if (populated == 0) {
      MACRO_EXIT_STANDARD_1(shared, stack, 0);
    }
    else if (populated < 0) {
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }
  }


  // load the pid_path.
  shared.pid_path = malloc(sizeof(char) * PATH_MAX);
  if (shared.pid_path == NULL) {
    log_write(LOG_ERR, "ERROR: failed to allocate memory for the pid path.\n");
    MACRO_EXIT_STANDARD_1(shared, stack, -1);
  }


  // check to see if an existing pid file exists before doing anything else.
  {
    struct stat pid_stat;
    int result_stat = 0;

    memset(&pid_stat, 0, sizeof(struct stat));
    result_stat = stat(shared.pid_path, &pid_stat);
    if (result_stat > -1 || errno != ENOENT) {
      if (result_stat > -1) {
        printf("ERROR: the pid file already exists at '%s', this pid: %u.'\n", shared.pid_path, shared.pid_parent);
      }
      else {
        printf("ERROR: while calling stat() on '%s', this pid: %u.'\n", shared.pid_path, shared.pid_parent);
      }

      // do not attempt to unlink the path, so manually free and reset before MACRO_EXIT_STANDARD_1() is called.
      memset(shared.pid_path, 0, sizeof(char) * PATH_MAX);
      free(shared.pid_path);
      shared.pid_path = NULL;

      memset(&pid_stat, 0, sizeof(struct stat));
      result_stat = 0;
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }
  }


  #ifdef USE_NETWORK
    shared.socket_id_target = socket(SOCKET_FAMILY, SOCKET_TYPE, SOCKET_PROTOCOL);

    if (shared.socket_id_target < 0) {
      printf("ERROR: failed to initiailize the port '%u' using protocol '%u': error %i (%u).'\n", shared.parameter_port, SOCKET_PROTOCOL, shared.socket_id_target, errno);
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }
  #elif defined USE_SOCKET
    {
      int socket_path_length = SOCKET_PATH_LENGTH + 1;
      socket_path_length += strnlen(shared.parameter_system, PARAMETER_LENGTH_MAX);
      socket_path_length += strnlen(shared.parameter_group, PARAMETER_LENGTH_MAX);
      socket_path_length *= sizeof(char);

      shared.socket_path = malloc(socket_path_length);
      if (shared.socket_path == NULL) {
        printf("ERROR: failed to allocate enough memory for the socket path string.\n");
        MACRO_EXIT_STANDARD_1(shared, stack, -1);
      }

      memset(shared.socket_path, 0, socket_path_length);
      snprintf(shared.socket_path, socket_path_length, SOCKET_PATH, shared.parameter_system, shared.parameter_group);
    }

    // make sure that no file exists at shared.socket_path before attempt to create a socket.
    {
      struct stat file_stat;

      if (stat(shared.socket_path, &file_stat) >= 0) {
        printf("ERROR: failed to initiailize the socket '%s' because a file already exists at that path, exiting.\n", shared.socket_path);

        if (shared.socket_path != NULL) {
          free(shared.socket_path);
          shared.socket_path = NULL;
        }

        MACRO_EXIT_STANDARD_1(shared, stack, -1);
      }
    }

    shared.socket_id_target = socket(SOCKET_FAMILY, SOCKET_TYPE, SOCKET_PROTOCOL);

    if (shared.socket_id_target < 0) {
      printf("ERROR: failed to initiailize the socket '%s' using protocol '%u', 'socket id = '%i, exiting.'\n", shared.socket_path, SOCKET_PROTOCOL, shared.socket_id_target);
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }
  #endif // USE_SOCKET


  // now run the process in the background before cloning and before blocking for signals.
  {
    #ifdef DEBUG_ENABLED
      int daemonized = daemon(0, 1);
    #else
      int daemonized = daemon(0, 0);
    #endif // DEBUG_ENABLED

    if (daemonized < 0) {
      printf("ERROR: failed to daemonize, error: %i.\n", errno);
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }
  }


  // The stack must be malloced after the process has daemonized because daemon() calls fork().
  stack = malloc(STACK_SIZE);
  if (stack == NULL) {
    log_write(LOG_ERR, "ERROR: failed to allocate the stack for cloning, error: %i.\n", errno);
    MACRO_EXIT_STANDARD_1(shared, stack, -1);
  }
  memset(stack, 0, sizeof(char) * STACK_SIZE);


  // create the pid file or fail if one already exists.
  shared.pid_parent = getpid();
  if (snprintf(shared.pid_path, sizeof(char) * PATH_MAX, PATH_PID, shared.parameter_system) < 0) {
    log_write(LOG_ERR, "ERROR: failed to setup the pid string '%s' using system name '%s', this pid: %u.'\n", PATH_PID, shared.parameter_system, shared.pid_parent);
    MACRO_EXIT_STANDARD_1(shared, stack, -1);
  }

  {
    FILE *pid_file = NULL;

    pid_file = fopen(shared.pid_path, "w");
    if (pid_file <= 0) {
      log_write(LOG_ERR, "ERROR: failed to create pid file '%s', this pid: %u.'\n", shared.pid_path, shared.pid_parent);
      pid_file = NULL;
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }

    if (fprintf(pid_file, "%u\n", shared.pid_parent) < 0) {
      log_write(LOG_ERR, "ERROR: failed to create pid file '%s', this pid: %u.'\n", shared.pid_path, shared.pid_parent);
      fclose(pid_file);
      pid_file = NULL;
      MACRO_EXIT_STANDARD_1(shared, stack, -1);
    }

    fclose(pid_file);
    pid_file = NULL;
  }


  pid_t pid_child = clone(handler_child, stack + STACK_SIZE, FLAGS_CLONE, &shared);

  if (pid_child < 0) {
    log_write(LOG_ERR, "ERROR: failed to clone the process, error: %i.\n", errno);
    MACRO_EXIT_STANDARD_1(shared, stack, -1);
  }

  // signal blocking is used to help the program safely quit on interrupt.
  sigset_t signal_mask;
  siginfo_t signal_information_parent;
  //siginfo_t signal_information_child;
  int signal_result = 0;
  short signal_problem_count = 0;
  int process_child = 0, process_wait = 0;

  memset(&signal_mask, 0, sizeof(sigset_t));
  memset(&signal_information_parent, 0, sizeof(siginfo_t));
  //memset(&signal_information_child, 0, sizeof(siginfo_t));

  // block signals.
  sigemptyset(&signal_mask);
  sigaddset(&signal_mask, SIGHUP);
  sigaddset(&signal_mask, SIGINT);
  sigaddset(&signal_mask, SIGQUIT);
  sigaddset(&signal_mask, SIGTERM);
  sigaddset(&signal_mask, SIGSEGV);
  sigaddset(&signal_mask, SIGBUS);
  sigaddset(&signal_mask, SIGILL);
  sigaddset(&signal_mask, SIGFPE);
  sigaddset(&signal_mask, SIGABRT);
  sigaddset(&signal_mask, SIGPWR);
  sigaddset(&signal_mask, SIGXCPU);
  sigaddset(&signal_mask, SIGCHLD);

  sigprocmask(SIG_BLOCK, &signal_mask, NULL);

  // sit and wait for signals.
  while(1) {
    signal_result = sigwaitinfo(&signal_mask, &signal_information_parent);

    if (signal_result < 0) {
      if (errno == EAGAIN) {
        // do nothing.
        continue;
      }
      else if (errno != EINTR) {
        log_write(LOG_ERR, "ERROR: sigwaitinfo() failed: %i.\n", errno);

        signal_problem_count++;
        if (signal_problem_count > PROBLEM_COUNT_MAX_SIGNAL_SIZE) {
          log_write(LOG_ERR, "ERROR: max signal problem count has been reached, exiting.\n");
          MACRO_EXIT_STANDARD_2(pid_child, shared, stack, -1);
        }

        continue;
      }
    }

    signal_problem_count = 0;

    if (signal_information_parent.si_signo == SIGHUP) {
      // do nothing.
    }
    else if (signal_information_parent.si_signo == SIGINT || signal_information_parent.si_signo == SIGQUIT || signal_information_parent.si_signo == SIGTERM) {
      MACRO_EXIT_STANDARD_2(pid_child, shared, stack, 0);
    }
    else if (signal_information_parent.si_signo == SIGSEGV || signal_information_parent.si_signo == SIGBUS || signal_information_parent.si_signo == SIGILL || signal_information_parent.si_signo == SIGFPE) {
      MACRO_EXIT_STANDARD_2(pid_child, shared, stack, 0);
    }
    else if (signal_information_parent.si_signo == SIGABRT || signal_information_parent.si_signo == SIGIOT || signal_information_parent.si_signo == SIGPWR || signal_information_parent.si_signo == SIGXCPU) {
      MACRO_EXIT_STANDARD_2(pid_child, shared, stack, 0);
    }
    else if (signal_information_parent.si_signo == SIGCHLD) {
      // do nothing
    }

    memset(&signal_information_parent, 0, sizeof(siginfo_t));
    continue;
  }

  // failsafe, but should not get here.
  MACRO_EXIT_STANDARD_2(pid_child, shared, stack, 0);
}
