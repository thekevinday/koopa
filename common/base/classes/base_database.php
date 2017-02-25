<?php
/**
 * @file
 * Provides a class for managing SQL database communication.
 *
 * Following SQL strictly and avoiding non-standard SQL is normally the preferred design.
 * This is being deviated from for the following reasons:
 * - SQL fails to define a standard of communication, so connecting to and talking to databases is inconsistent.
 * - SQL fails to be unclear in certain cases that cause different SQL engines to process the code differently.
 * - PHP's PDO randomly decides not to support some important functionality with PostgreSQL that it supports across other databases and with non-PDO PHP postgresql code.
 * - A vast majority of open source projects out there use non-standard MySQL-specific code as a basis, so it is about time that some project optimized to PostgreSQL instead.
 *   - There are numerous cases where MySQL appears to perform better than Postgresql in tests with projects like Drupal.
 *   - This is a flawed logic given that extra, wasteful, code is added to work undo non-standard which will obviously make Postgresql perform slower.
 * - Much of the advanced functionality of PostgreSQL is going to be used to write a far more secure and well rounded product that other open source databases ever could.
 * - This project is not like Drupal and others in that it is not designed around PHP and uses SQL, instead, it is designed around SQL and uses PHP.
 *
 * One of the particular designs is to use persistent connections as much as possible.
 * - At any point in time a transaction needs to be performed, do not use the persistent connection, instead create a new connection.
 * - At any point in time a lock needs to be used, do not use the persistent connection, instead create a new connection.
 * - For connection re-cycling, persistent connections should be used because they carry over.
 *   - Anonymous connections should in general use persistent.
 *   - Non-anonymous connections should not use persistent connections.
 *   - Non-anonymous connections may keep an anonymous persistent connection for use such as "public preview" of something.
 * - A reason against persistent connections is the inability to directly close them.
 *   - This is a major weakness and may prevent me from using this persistent connection design (much testing is required).
 *
 * @see: http://us.php.net/manual/en/features.persistent-connections.php
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for storing and creating a database connection string.
 *
 * @see: http://us.php.net/manual/en/function.pg-pconnect.php
 */
class c_base_connection_string extends c_base_return_string {
  const DATA_CLEAR_TEXT_LENGTH = 4096;

  private $host;
  private $host_addr;
  private $port;
  private $database;
  private $user;
  private $password;
  private $connect_timeout;
  private $options;
  private $ssl_mode;
  private $service;

  private $error;

  /**
   * Class destructor.
   */
  public function __construct() {
    parent::__construct();

    $this->host = NULL;
    $this->host_addr = NULL;
    $this->port = NULL;
    $this->database = NULL;
    $this->user = NULL;
    $this->password = NULL;
    $this->connect_timeout = NULL;
    $this->options = NULL;
    $this->ssl_mode = NULL;
    $this->service = NULL;

    $this->error = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    $this->clear();

    unset($this->host);
    unset($this->host_addr);
    unset($this->port);
    unset($this->database);
    unset($this->user);
    unset($this->password);
    unset($this->connect_timeout);
    unset($this->options);
    unset($this->ssl_mode);
    unset($this->service);

    unset($this->error);

    parent::__destruct();
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Assign host information.
   *
   * @param string $host
   *   Host information string, such as hostname or ip address.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_host($host) {
    if (!is_string($host)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->host = $host;
    return new c_base_return_true();
  }

  /**
   * Returns the host information.
   *
   * @return c_base_return_string
   *   The host information string.
   *   The error bit set is on error.
   */
  public function get_host() {
    if (!is_string($this->host)) {
      $this->host = '';
    }

    return c_base_return_string::s_new($this->host);
  }

  /**
   * Assign host address information.
   *
   * @param string $host_addr
   *   Host address information string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_host_addr($host_addr) {
    if (!is_string($host_addr)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'host_addr', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->host_addr = $host_addr;
    return new c_base_return_true();
  }

  /**
   * Returns the host address information.
   *
   * @return c_base_return_string
   *   The host address information string.
   *   The error bit set is on error.
   */
  public function get_host_addr() {
    if (!is_string($this->host_addr)) {
      $this->host_addr = '';
    }

    return c_base_return_string::s_new($this->host_addr);
  }

  /**
   * Assign port number.
   *
   * @param int $port
   *   Port number.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_port($port) {
    if (!is_int($port)) {
      if (is_string($port) && is_numeric($port)) {
        $port = (int) $port;
        if ($port < 0) {
          $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'port', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
      }
      else {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'port', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    $this->port = $port;
    return new c_base_return_true();
  }

  /**
   * Returns the port number.
   *
   * @return c_base_return_int
   *   The port number on success.
   *   The error bit set is on error.
   */
  public function get_port() {
    if (!is_int($this->port)) {
      $this->port = 0;
    }

    return c_base_return_int::s_new($this->port);
  }

  /**
   * Assign database name.
   *
   * @param string $database
   *   The database name string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_database($database) {
    if (!is_string($database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->database = $database;
    return new c_base_return_true();
  }

  /**
   * Returns the database name.
   *
   * @return c_base_return_string
   *   The database name string on success.
   *   The error bit set is on error.
   */
  public function get_database() {
    if (!is_string($this->database)) {
      $this->database = '';
    }

    return c_base_return_string::s_new($this->database);
  }

  /**
   * Assign user name.
   *
   * @param string $user
   *   The user name string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_user($user) {
    if (!is_string($user)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'user', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->user = $user;
    return new c_base_return_true();
  }

  /**
   * Returns the user name.
   *
   * @return c_base_return_string
   *   The user name string on success.
   *   The error bit set is on error.
   */
  public function get_user() {
    if (!is_string($this->user)) {
      $this->user = '';
    }

    return c_base_return_string::s_new($this->user);
  }

  /**
   * Assign password.
   *
   * @param string|null $password
   *   The password string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_password($password) {
    if (!is_null($password) && !is_string($password)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'password', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->password = $password;
    return new c_base_return_true();
  }

  /**
   * Returns the password.
   *
   * @return c_base_return_string
   *   The password string.
   *   FALSE is returned if there is no assigned password.
   *   The error bit set is on error.
   */
  public function get_password() {
    if (is_null($this->password)) {
      return new c_base_return_false();
    }

    if (!is_string($this->password)) {
      $this->password = '';
    }

    return c_base_return_string::s_new($this->password);
  }

  /**
   * Assign connect timeout.
   *
   * @param int $connect_timeout
   *   Connect timeout number.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_connect_timeout($connect_timeout) {
    if (!is_int($connect_timeout)) {
      if (is_string($connect_timeout) && is_numeric($connect_timeout)) {
        $connect_timeout = (int) $connect_timeout;
        if ($connect_timeout < 0) {
          $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'connect_timeout', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
      }
      else {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'connect_timeout', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    $this->connect_timeout = $connect_timeout;
    return new c_base_return_true();
  }

  /**
   * Returns the connect timeout.
   *
   * @return c_base_return_int
   *   The connect timeout number.
   *   The error bit set is on error.
   */
  public function get_connect_timeout() {
    if (!is_int($this->connect_timeout)) {
      $this->connect_timeout = 0;
    }

    return c_base_return_int::s_new($this->connect_timeout);
  }

  /**
   * Assign options information.
   *
   * @param string $options
   *   Options information string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_options($options) {
    if (!is_string($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'options', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->options = $options;
    return new c_base_return_true();
  }

  /**
   * Returns the options information.
   *
   * @return c_base_return_string
   *   The options information string on success.
   *   The error bit set is on error.
   */
  public function get_options() {
    if (!is_string($this->options)) {
      $this->options = '';
    }

    return c_base_return_string::s_new($this->options);
  }

  /**
   * Assign ssl mode information.
   *
   * @param string $ssl_mode
   *   SSL Mode information string.
   *   One of the following:
   *   - 'disable'
   *   - 'allow'
   *   - 'prefer'
   *   - 'require'
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_ssl_mode($ssl_mode) {
    if (!is_string($ssl_mode)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ssl_mode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->ssl_mode = $ssl_mode;
    return new c_base_return_true();
  }

  /**
   * Returns the ssl mode information.
   *
   * @return c_base_return_string
   *   The ssl mode information string.
   *   The error bit set is on error.
   */
  public function get_ssl_mode() {
    if (!is_string($this->ssl_mode)) {
      $this->ssl_mode = '';
    }

    return c_base_return_string::s_new($this->ssl_mode);
  }

  /**
   * Assign service information.
   *
   * @param string $service
   *   Service information string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_service($service) {
    if (!is_string($service)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'service', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->service = $service;
    return new c_base_return_true();
  }

  /**
   * Returns the service information.
   *
   * @return c_base_return_string
   *   The service information string.
   *   The error bit set is on error.
   */
  public function get_service() {
    if (!is_string($this->service)) {
      $this->service = '';
    }

    return c_base_return_string::s_new($this->service);
  }

  /**
   * Builds the connection string based on the current settings.
   *
   * The built string is stored inside of this objects 'value' parameter.
   */
  public function build() {
    $this->value = '';

    if (!empty($this->host)) {
      $this->value .= ' host=' . $this->p_escape_string($this->host);
    }

    if (!empty($this->host_addr)) {
      $this->value .= ' host_addr=' . $this->p_escape_string($this->host_addr);
    }

    if (!empty($this->port)) {
      $this->value .= ' port=' . $this->p_escape_string($this->port);
    }

    if (!empty($this->database)) {
      $this->value .= ' dbname=' . $this->p_escape_string($this->database);
    }

    if (!empty($this->user)) {
      $this->value .= ' user=' . $this->p_escape_string($this->user);
    }

    if (!empty($this->password)) {
      $this->value .= ' password=' . $this->p_escape_string($this->password);
    }

    if (!empty($this->connect_timeout)) {
      $this->value .= ' connect_timeout=' . $this->p_escape_string($this->connect_timeout);
    }

    if (!empty($this->options)) {
      $this->value .= ' options=' . $this->p_escape_string($this->options);
    }

    if (!empty($this->ssl_mode)) {
      $this->value .= ' sslmode=' . $this->p_escape_string($this->ssl_mode);
    }

    if (!empty($this->service)) {
      $this->value .= ' service=' . $this->p_escape_string($this->service);
    }
  }

  /**
   * Clears the connection string.
   *
   * This should be cleared after use because the string generally contains a password.
   * Uses an unproven technique in an attempt to 'delete' the string from memory and then unallocating the resource.
   *
   * This does not perform the garbage collection, but it is suggested that the caller consider calling gc_collect_cycles().
   *
   * @see: gc_collect_cycles()
   */
  public function clear() {
    $this->value = str_repeat(' ', self::DATA_CLEAR_TEXT_LENGTH);
    unset($this->value);
  }

  /**
   * Escape the postgresql connection string.
   *
   * According to the documentation, both ' and \ must be escaped with a \.
   *
   * @param string string
   *   The string to escape.
   *
   * @return string
   *   The escaped string.
   */
  private function p_escape_string($string) {
    $escaped = str_replace('\\', '\\\\', $string);
    $escaped = str_replace('\'', '\\\'', $escaped);

    return $escaped;
  }
}

/**
 * A generic class for managing database connections.
 *
 * @require class c_base_return
 * @require class c_base_session
 */
class c_base_database extends c_base_return {
  private $session;
  private $persistent;
  private $database;
  private $connection_string;
  private $asynchronous;

  private $connected;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->session = NULL;
    $this->persistent = NULL;
    $this->database = NULL;
    $this->connection_string = NULL;
    $this->asynchronous = NULL;

    $this->connected = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    if (is_resource($this->database)) {
      $this->do_disconnect();
    }

    if (is_object($this->connection_string) && $this->connection_string instanceof c_base_connection_string) {
      $this->connection_string->clear();
    }

    unset($this->session);
    unset($this->persistent);
    unset($this->database);
    unset($this->connection_string);

    unset($this->connected);

    parent::__destruct();
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Assign a session to the database.
   *
   * @param c_base_session $session
   *   An already processed and configured session object.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_session($session) {
    if (!is_object($session) || !($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'session', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->session = $session;
    return new c_base_return_true();
  }

  /**
   * Returns the session information.
   *
   * @return c_base_session
   *   A session object on success.
   *   The error bit set is on error.
   */
  public function get_session() {
    if (!is_object($session) || !($session instanceof c_base_session)) {
      $this->session = new c_base_session();
    }

    return $this->session;
  }

  /**
   * Assign a connection string to the database.
   *
   * @param c_base_connection_string $connection_string
   *   An already processed and configured connection string object.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_connection_string($connection_string) {
    if (!is_object($connection_string) || !($connection_string instanceof c_base_connection_string)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'connection_string', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->connection_string = $connection_string;
    $this->connection_string->build();

    return new c_base_return_true();
  }

  /**
   * Returns the connection string.
   *
   * @return c_base_connection_string
   *   A connection string object on success.
   *   The error bit set is on error.
   */
  public function get_connection_string() {
    if (!is_object($this->connection_string) || !($this->connection_string instanceof c_base_connection_string)) {
      $this->connection_string = new c_base_connection_string();
    }

    return $this->connection_string;
  }

  /**
   * Enable or Disable a persistent database connection.
   *
   * When using persistent connections, make sure that there is at least 2*max_connections of shared_buffers.
   * Where max_connections is the number of connections you intend to allow for all systems and services to access the database.
   *
   * PHP's php.ini option 'pgsql.max_persistent' should be used to control the number of persistent connection limits (with -1 being infinite).
   *
   * If a persistent connection is created, then it cannot be closed with the normal close function.
   * However, the close() function still cleans up some resources related to persistent connection and should still be executed.
   *
   * Avoid using persistent connection for cases where there is a table lock or transaction in use.
   *
   * @param bool $persistent
   *   TRUE to enable a persistent connection, FALSE otherwise.
   *
   * @param c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_persistent($persistent) {
    if (!is_bool($persistent)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'persistent', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->persistent = $persistent;

    return new c_base_return_true();
  }

  /**
   * Return the persistent connection status.
   *
   * @return c_base_return_status
   *   TRUE on enabled, FALSE on disabled.
   *   The error bit set is on error.
   */
  public function get_persistent() {
    if (!is_bool($this->persistent)) {
      $this->persistent = FALSE;
    }

    if ($this->persistent) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Enable or Disable an asynchronous database connection.
   *
   * @param bool $asynchronous
   *   TRUE to enable a asynchronous connection, FALSE otherwise.
   *
   * @param c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_asynchronous($asynchronous) {
    if (!is_bool($asynchronous)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'asynchronous', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->asynchronous = $asynchronous;
  }

  /**
   * Return the asynchronous connection status.
   *
   * @return c_base_return_status
   *   TRUE on enabled, FALSE on disabled.
   *   The error bit set is on error.
   */
  public function get_asynchronous() {
    if (!is_bool($this->asynchronous)) {
      $this->asynchronous = FALSE;
    }

    if ($this->asynchronous) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Open the connection to the database.
   *
   * This will create a new connection if persistent connection is not enabled.
   *
   * @param bool $force
   *   (optional) When TRUE, passes PGSQL_CONNECT_FORCE_NEW to force a new connection to be made.
   *
   * @param c_base_return_status
   *   TRUE on success, FALSE otherwise
   *   c_base_return_true is returned if the database is connected.
   *   c_base_return_false is returned if the database is disconnected.
   *   FALSE with the error bit set is returned on error.
   *   If the database is already connected when this is called, c_base_return_true is returned with the error bit set.
   *
   * @see: pg_connect()
   */
  public function do_connect($force = FALSE) {
    if (!is_bool($force)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'force', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->connection_string)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'connection_string', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_resource($this->database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_UNECESSARY);
      return c_base_return_error::s_true($error);
    }

    $type = 0;
    if ($force) {
      $type += PGSQL_CONNECT_FORCE_NEW;
    }

    if ($this->asynchronous) {
      $type += PGSQL_CONNECT_ASYNC;
    }

    // make sure the connection string is built before using.
    $this->connection_string->build();

    // Both pg_connect() and pg_pconnect() throw errors and the functions do not support try {} .. catch { statements.
    // the only way to prevent unwanted error reporting (allowing the caller to the reporting) is to use @.
    // The @ is considered bad practice, but there is no alternative in this case.
    if ($this->persistent) {
      $database = @pg_pconnect($this->connection_string->get_value_exact(), $type);
    }
    else {
      $database = @pg_connect($this->connection_string->get_value_exact(), $type);
    }

    unset($type);
    if ($database === FALSE) {
      unset($database);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $this->connection_string->get_database()->get_value_exact(), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_CONNECTION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->database = $database;
    unset($database);

    // set the default encoding to unicode.
    pg_set_client_encoding($this->database, 'UTF8');

    $this->connected = TRUE;
    return new c_base_return_true();
  }

  /**
   * Close the connection to the database.
   *
   * The PHP documentation states the following:
   *   If there is open large object resource on the connection, do not close the connection before closing all large object resources.
   *
   * @param c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_close()
   */
  public function do_disconnect() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (pg_close($this->database)) {
      $this->connected = FALSE;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_close', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Flush outbound query data.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on partial flush.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_flush()
   */
  public function do_flush() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $result = pg_flush($this->database);
    if ($result === TRUE) {
      return new c_base_return_true();
    }

    if ($result === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_flush', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return new c_base_return_false();
  }

  /**
   * Returns whether or not a connection has been established to the database.
   *
   * @return c_base_return_status
   *   TRUE on connected, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function is_connected() {
    if ($this->connected === TRUE && pg_connection_status($this->database) === PGSQL_CONNECTION_OK) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Returns the connection busy status.
   *
   * This is used for asynchronous connections.
   *
   * @return c_base_return_status
   *   TRUE on busy, FALSE when not busy.
   *   Error flag is set on error.
   *
   * @see: pg_connection_busy()
   */
  public function is_busy() {
    if (!$this->asynchronous) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->asynchronous', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (pg_connection_busy($this->database)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Returns the parameter status.
   *
   * @param string $name
   *   Name of the parameter to get the status of.
   *
   * @return c_base_return_status|c_base_return_string
   *   String containing the status or FALSE on failure.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_parameter_status()
   */
  public function get_parameter_status($name) {
    if (!is_string($name) || empty($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_parameter_status($this->database, $name);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_parameter_status', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Returns the connection busy status.
   *
   * This is used for asynchronous connections.
   *
   * @return c_base_return_int|c_base_return_status
   *   The integer is returned on success or failure.
   *   The failure flag will be set accordingly.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_connect_poll()
   */
  public function do_poll() {
    if (!$this->asynchronous) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->database', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_value_exact(pg_connect_poll($this->database));
  }

  /**
   * Resets the connection (perform a reconnect).
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_connection_reset()
   */
  public function do_reset() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (pg_connection_reset($this->database)) {
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_connection_reset', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Returns the connection status.
   *
   * @return c_base_return_int
   *   PGSQL_CONNECTION_OK or PGSQL_CONNECTION_BAD.
   *   The error bit set is on error.
   *
   * @see: pg_connection_status()
   */
  public function get_status() {
    return c_base_return_int::s_new(pg_connection_status($this->database));
  }

  /**
   * Ping the connection and attempt to reconnect if broken.
   *
   * This calls pg_status() and then pg_connection_reset() instead of pg_ping() so that a more granular return result can be provided.
   *
   * @return c_base_return_status
   *   TRUE if ping was successful, FALSE if ping failed, but was successfully restarted.
   *   FALSE with error flag set is returned on error or if ping failed and restart failed.
   *
   * @see: pg_ping()
   * @see: pg_status()
   * @see: pg_connection_reset()
   */
  public function do_ping() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (pg_status($this->database) === PGSQL_CONNECTION_OK) {
      return new c_base_return_true();
    }

    if (pg_connection_reset($this->database) === TRUE) {
      return new c_base_return_false();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::FUNCTION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Escape an SQL literal (an SQL string).
   *
   * PostgreSQL requires the database connection to be used (or provides its own) when escaping something.
   * Use this function so that the associated connection gets used instead of some unknown connection.
   *
   * @param string $literal
   *   The string to escape.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string to be returned.
   *   FALSE is returned on error
   *
   * @see: pg_escape_literal()
   */
  public function escape_literal($literal) {
    if (!is_string($literal)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'literal', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_value_exact(pg_escape_literal($this->database, $literal));
  }

  /**
   * Escape an SQL bytea, a blob like-type specific to PostgreSQL.
   *
   * PostgreSQL requires the database connection to be used (or provides its own) when escaping something.
   * Use this function so that the associated connection gets used instead of some unknown connection.
   *
   * @param string $bytea
   *   The string to escape.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string to be returned.
   *   FALSE is returned on error
   *
   * @see: pg_escape_bytea()
   */
  public function escape_bytea($bytea) {
    if (!is_string($bytea)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'bytea', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_value_exact(pg_escape_bytea($this->database, $bytea));
  }

  /**
   * Escape an SQL identifier (such as a column or table name).
   *
   * PostgreSQL requires the database connection to be used (or provides its own) when escaping something.
   * Use this function so that the associated connection gets used instead of some unknown connection.
   *
   * @param string $identifier
   *   The string to escape.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string to be returned.
   *   FALSE is returned on error
   *
   * @see: pg_escape_identifier()
   */
  public function escape_identifier($identifier) {
    if (!is_string($identifier)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'identifier', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_value_exact(pg_escape_identifier($this->database, $identifier));
  }

  /**
   * Unescape an SQL bytea, a blob like-type specific to PostgreSQL.
   *
   * PostgreSQL requires the database connection to be used (or provides its own) when escaping something.
   * Use this function so that the associated connection gets used instead of some unknown connection.
   *
   * @param string $bytea
   *   The string to unescape.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string to be returned.
   *   FALSE is returned on error
   *
   * @see: pg_unescape_bytea()
   */
  public function unescape_bytea($bytea) {
    if (!is_string($bytea)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'bytea', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_value_exact(pg_unescape_bytea($this->database, $bytea));
  }

  /**
   * Assigns the client encoding.
   *
   * @param string $encoding
   *   The client encoding to assign
   *
   * @return c_base_return_string|c_base_return_status
   *   The string to be returned.
   *   FALSE is returned on error
   *
   * @see: pg_set_client_encoding()
   */
  public function set_client_encoding($encoding) {
    if (!is_string($encoding)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'encoding', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    // this function has a strange return status.
    // 0 is returned instead of TRUE on success, -1 is returned instead of FALSE on error.
    if (pg_set_client_encoding($this->database, $encoding) === 0) {
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_set_client_encoding', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Returns the client encoding.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string to be returned.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_client_encoding()
   */
  public function get_client_encoding() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $encoding = pg_client_encoding($this->database);
    if ($encoding === FALSE) {
      unset($encoding);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_client_encoding', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_value_exact($encoding);
  }

  /**
   * Flushes input on the connection.
   *
   * Do not confuse this with the flush command.
   * The flush command flushsehs the query output.
   * This flushes input.
   *
   * This is likely useful for asynchronous connections.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: pg_consume_input()
   */
  public function consume_input() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (pg_consume_input($this->database)) {
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_consume_input', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Converts an associated array into a suitable SQL statement.
   *
   * @param string $table
   *   The name of the table.
   * @param string $array
   *   The associative array to convert.
   * @param int $options
   *   (optional) Additional options, such as:
   *   - PGSQL_CONV_IGNORE_DEFAULT
   *   - PGSQL_CONV_FORCE_NULL
   *   - PGSQL_CONV_IGNORE_NOT_NULL
   *
   * @return c_base_return_array|c_base_return_status
   *   The converted array or FALSE on error.
   *
   * @see: pg_convert()
   */
  public function do_convert($table, $array, $options = 0) {
    if (!is_string($table) || empty($table)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($array)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'array', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'options', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $converted = pg_connect_status($this->database, $table, $array, $options);
    if ($converted === FALSE) {
      unset($converted);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_connect_status', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_value_exact($converted);
  }

  /**
   * Execute a given SQL query statement and wait for results.
   *
   * When set to asynchronous, this effectively calls pg_send_execute().
   *
   * @param string $name
   *   The unique name of a query prepared by self::prepare().
   * @param array $parameters
   *   (optional) Parameters to be substituted.
   *   These are all converted to strings.
   *
   * @return c_base_return_query|c_base_return_status
   *   Query resource is returned on success, FALSE otherwise.
   *
   * @see: self::query()
   * @see: self::prepare()
   * @see: pg_execute()
   * @see: pg_send_execute()
   * @see: pg_query()
   * @see: pg_query_params()
   * @see: pg_query_send()
   * @see: pg_query_send_params()
   * @see: pg_prepare()
   * @see: pg_send_prepare()
   */
  public function do_execute($name, $parameters = array()) {
    if (!is_string($name) || empty($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($parameters)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'parameters', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if ($this->asynchronous) {
      $result = pg_send_execute($this->database, $name, $parameters);
    }
    else {
      $result = pg_execute($this->database, $name, $parameters);
    }

    if (is_resource($result)) {
      return c_base_database_result::s_new($result);
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => ($this->asynchronous ? 'pg_send_execute' : 'pg_execute'), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Process a single SQL query statement.
   *
   * When set to asynchronous, this effectively calls pg_send_query() or pg_query_send_params().
   *
   * @param string $query
   *   The query statement to execute.
   * @param array $parameters
   *   (optional) Parameters to be substituted.
   *   These are all converted to strings.
   *
   *   Any bytea field must not be used as a parameter.
   *   Instead, use pg_escape_bytea() or a large object function.
   *
   * @return c_base_database_result|c_base_return_status
   *   Query resource is returned on success, FALSE otherwise.
   *
   * @see: self::execute()
   * @see: self::prepare()
   * @see: pg_execute()
   * @see: pg_send_execute()
   * @see: pg_query()
   * @see: pg_query_params()
   * @see: pg_query_send()
   * @see: pg_query_send_params()
   * @see: pg_prepare()
   * @see: pg_send_prepare()
   */
  public function do_query($query, $parameters = array()) {
    if (!is_string($query) || empty($query)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'query', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($parameters)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'parameters', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if ($this->asynchronous) {
      if (empty($parameters)) {
        $result = pg_send_query($this->database, $query);
      }
      else {
        $result = pg_send_query_params($this->database, $query, $parameters);
      }
    }
    else {
      if (empty($parameters)) {
        $result = pg_query($this->database, $query);
      }
      else {
        $result = pg_query_params($this->database, $query, $parameters);
      }
    }

    if (is_resource($result)) {
      return c_base_database_result::s_new($result);
    }
    unset($result);

    if ($this->asynchronous) {
      if (empty($parameters)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_send_query', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      }
      else {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_send_query_params', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      }
    }
    else {
      if (empty($parameters)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_query', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      }
      else {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_query_params', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      }
    }

    return c_base_return_error::s_false($error);
  }

  /**
   * Prepares an SQL statement for multiple uses.
   *
   * When set to asynchronous, this effectively calls pg_send_prepare().
   *
   * @param string $name
   *   A unique name for the prepared statement so that it can be executed via self::execute().
   * @param string $query
   *   The query statement to execute.
   *
   * @return c_base_database_result|c_base_return_status
   *   Query resource is returned on success, FALSE otherwise.
   *
   * @see: self::execute()
   * @see: self::query()
   * @see: pg_execute()
   * @see: pg_send_execute()
   * @see: pg_prepare()
   * @see: pg_send_prepare()
   */
  public function do_prepare($name, $query) {
    if (!is_string($name) || empty($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($query) || empty($query)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'query', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if ($this->asynchronous) {
      $result = pg_send_prepare($this->database, $name, $query);
    }
    else {
      $result = pg_prepare($this->database, $name, $query);
    }

    if (is_resource($result)) {
      return c_base_database_result::s_new($result);
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => ($this->asynchronous ? 'pg_send_prepare' :'pg_prepare'), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the result of an asynchronous query.
   *
   * This is only useful when a query is asynchronous.
   *
   * @return c_base_database_result|c_base_return_status
   *   A database result is returned on success.
   *   FALSE with the error bit set is returned on error.
   *   When asynchronous is not enabled, FALSE is returned without an error flag set.
   */
  public function get_result() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (!$this->asynchronous) {
      return new c_base_return_false();
    }

    $result = pg_get_result($this->database);
    if (is_resource($result)) {
      return c_base_database_result::s_new($result);
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_get_result', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Cancel an asynchronous query.
   *
   * This is only useful when a query is asynchronous.
   *
   * It is unspecific as to whether this cancels the last query sent.
   * This appears to be the case, so it is difficulty to cancel multiple queries or even know which query is or is not still running (or waiting).
   * It is further difficult to cancel all queries as there is no way to know if FALSe means there was an error or if there are no longer any queries to cancel.
   * With any luck, I will come across documentation that clarifies this behavior and discover functions to better handle this.
   *
   * The documentation does suggest that it prints out messages such as:
   *   "First call to pg_get_result(): Resource id #3
   *    Resource id #3 has 3 records"
   * But this is rather useless as it is not a return value to process.
   * Furthermore, it clobbers output, which may be a problem.
   *
   * Due to the immaturity or limitations of functions like this, it is recommended that asynchronous calls not be used for anything that needs to be reliably controlled.
   *
   * One possible approach is to implement ones own caching mechanisms.
   * Implement an array "stack" that stores all queries planned to be executed.
   * Then use pg_connection_busy() to check if the execution is available.
   * And pop an item when not busy and send the query.
   * This design, however, means that only a single a synchronous query operation may be performed at any given time.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE is returned on failure.
   *   When asynchronous is not enabled, FALSE is returned without an error flag set.
   *
   * @see: pg_cancel_query()
   */
  public function do_cancel() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (!$this->asynchronous) {
      return new c_base_return_false();
    }

    if (pg_cancel_query($this->database)) {
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_cancel_query', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Perform an SQL INSERT query.
   *
   * The asynchronous behavior is inconsistent with other functions.
   * Instead of having an insert_send() function, it uses an option called PGSQL_DML_ASYNC.
   * But that then triggers the call to pg_convert() so suddenly a new option PGSQL_DML_NO_CONV must be used to ensure this does not unintentionally happen.
   *
   * This is better documentation from PHP's pg_last_oid() on insert:
   *   To get the value of a SERIAL field in an inserted row, it is necessary to use the PostgreSQL CURRVAL function, naming the sequence whose last value is required.
   *   If the name of the sequence is unknown, the pg_get_serial_sequence PostgreSQL 8.0 function is necessary.
   *   PostgreSQL 8.1 has a function LASTVAL that returns the value of the most recently used sequence in the session.
   *   This avoids the need for naming the sequence, table or column altogether.
   *
   * @param string $table
   *   The name of the table to insert into.
   * @param array $values
   *   An associative array of values to insert.
   * @param null|int $options
   *   (optional) If not NULL, pg_convert() is called against $values with these options passed to it.
   *   The exception to this is when any of the following are passed:
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *
   *   All options are:
   *   - PGSQL_CONV_OPTS
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *   - PGSQL_DML_EXEC
   *   - PGSQL_DML_ASYNC (auto-added or auto-removed when asynchronous is enabled or disabled)
   *   - PGSQL_DML_STRING
   *
   * @return c_base_return_status|c_base_return_string
   *   TRUE on success, FALSE on failure.
   *   If PGSQL_DML_STRING is set, a string is returned on success.
   *
   *   Its unclear as to what the returned string is, but it can be assumed to be a value, such as an serialized number that was incremented by this operation.
   *
   * @see: pg_insert()
   * @see: pg_convert()
   * @see: pg_last_oid()
   */
  public function do_insert($table, $values, $options = NULL) {
    if (!is_string($table) || empty($table)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($values)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'values', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($options) && !is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'options', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $this->p_handle_asynchronous_options_parameter($options);

    if (is_null($options)) {
      $result = pg_insert($this->database, $table, $values);
    }
    else {
      $result = pg_insert($this->database, $table, $values, $options);
    }

    if (!is_null($options) && $options & PGSQL_DML_STRING) {
      if (is_string($result)) {
        return c_base_return_string::s_new($result);
      }
    }
    elseif ($result === TRUE) {
      unset($result);
      return new c_base_return_true();
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_insert', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Perform an SQL UPDATE.
   *
   * The asynchronous behavior is inconsistent with other functions.
   * Instead of having an update_send() function, it uses an option called PGSQL_DML_ASYNC.
   * But that then triggers the call to pg_convert() so suddenly a new option PGSQL_DML_NO_CONV must be used to ensure this does not unintentionally happen.
   *
   * @param string $table
   *   The name of the table to insert into.
   * @param array $values
   *   An associative array of values to insert.
   * @param array $conditions
   *   An associative array of conditions that each row must meet to be updated.
   * @param null|int $options
   *   (optional) If not NULL, pg_convert() is called against $values with these options passed to it.
   *   The exception to this is when any of the following are passed:
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *
   *   All options are:
   *   - PGSQL_CONV_FORCE_NULL
   *   - PGSQL_CONV_OPTS
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *   - PGSQL_DML_EXEC
   *   - PGSQL_DML_ASYNC (auto-added or auto-removed when asynchronous is enabled or disabled)
   *   - PGSQL_DML_STRING
   *
   * @return c_base_return_status|c_base_return_string
   *   TRUE on success, FALSE on failure.
   *   If PGSQL_DML_STRING is set, a string is returned on success.
   *
   *   Its unclear as to what the returned string is, but it can be assumed to be a value, such as an serialized number that was incremented by this operation.
   *
   * @see: pg_update()
   * @see: pg_convert()
   */
  function do_update($table, $values, $conditions, $options = NULL) {
    if (!is_string($table) || empty($table)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($values)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'values', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($conditions)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'conditions', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($options) && !is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'options', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $this->p_handle_asynchronous_options_parameter($options);

    if (is_null($options)) {
      $result = pg_update($this->database, $table, $values, $conditions);
    }
    else {
      $result = pg_update($this->database, $table, $values, $conditions, $options);
    }

    if (!is_null($options) && $options & PGSQL_DML_STRING) {
      if (is_string($result)) {
        return c_base_return_string::s_new($result);
      }
    }
    elseif ($result === TRUE) {
      unset($result);
      return new c_base_return_true();
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_update', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Perform an SQL SELECT.
   *
   * The asynchronous behavior is inconsistent with other functions.
   * Instead of having a select_send() function, it uses an option called PGSQL_DML_ASYNC.
   * But that then triggers the call to pg_convert() so suddenly a new option PGSQL_DML_NO_CONV must be used to ensure this does not unintentionally happen.
   *
   * @param string $table
   *   The name of the table to insert into.
   * @param array $conditions
   *   An associative array of conditions that each row must meet to be updated.
   * @param null|int $options
   *   (optional) If not NULL, pg_convert() is called against $values with these options passed to it.
   *   The exception to this is when any of the following are passed:
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *
   *   All options are:
   *   - PGSQL_CONV_FORCE_NULL
   *   - PGSQL_CONV_OPTS
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *   - PGSQL_DML_EXEC
   *   - PGSQL_DML_ASYNC (auto-added or auto-removed when asynchronous is enabled or disabled)
   *   - PGSQL_DML_STRING
   *
   * @return c_base_return_status|c_base_return_string|c_base_return_array
   *   TRUE or an array on success, FALSE on failure.
   *   If PGSQL_DML_STRING is set, a string is returned on success.
   *
   *   Its unclear as to what the returned string is, but it can be assumed to be a value, such as an serialized number that was incremented by this operation.
   *
   *   The PHP documentation states that the return value is TRUE on success but it also states that it returns an array containing all selected records on success.
   *   This is confusing and a return value of an array makes the most sense.
   *
   * @see: pg_select()
   * @see: http://us.php.net/manual/en/function.pg-select.php
   */
  function do_select($table, $conditions, $options = NULL) {
    if (!is_string($table) || empty($table)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($conditions)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'conditions', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($options) && !is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'options', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $this->p_handle_asynchronous_options_parameter($options);

    if (is_null($options)) {
      $result = pg_select($this->database, $table, $conditions);
    }
    else {
      $result = pg_select($this->database, $table, $conditions, $options);
    }

    if (!is_null($options) && $options & PGSQL_DML_STRING) {
      if (is_string($result)) {
        return c_base_return_string::s_new($result);
      }
    }
    elseif (is_array($result)) {
      return c_base_return_array::s_new($result);
    }
    elseif ($result === TRUE) {
      unset($result);
      return new c_base_return_true();
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_select', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Perform an SQL DELETE.
   *
   * The asynchronous behavior is inconsistent with other functions.
   * Instead of having a delete_send() function, it uses an option called PGSQL_DML_ASYNC.
   * But that then triggers the call to pg_convert() so suddenly a new option PGSQL_DML_NO_CONV must be used to ensure this does not unintentionally happen.
   *
   * @param string $table
   *   The name of the table to insert into.
   * @param array $conditions
   *   An associative array of conditions that each row must meet to be updated.
   * @param null|int $options
   *   (optional) If not NULL, pg_convert() is called against $values with these options passed to it.
   *   The exception to this is when any of the following are passed:
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *
   *   All options are:
   *   - PGSQL_CONV_FORCE_NULL
   *   - PGSQL_CONV_OPTS
   *   - PGSQL_DML_NO_CONV
   *   - PGSQL_DML_ESCAPE
   *   - PGSQL_DML_EXEC
   *   - PGSQL_DML_ASYNC (auto-added or auto-removed when asynchronous is enabled or disabled)
   *   - PGSQL_DML_STRING
   *
   * @return c_base_return_status|c_base_return_string|c_base_return_array
   *   TRUE on success, FALSE on failure.
   *   If PGSQL_DML_STRING is set, a string is returned on success.
   *
   *   Its unclear as to what the returned string is, but it can be assumed to be a value, such as an serialized number that was incremented by this operation.
   *
   * @see: pg_delete()
   */
  function do_delete($table, $conditions, $options = NULL) {
        if (!is_string($table) || empty($table)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($conditions)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'conditions', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($options) && !is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'options', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $this->p_handle_asynchronous_options_parameter($options);

    if (is_null($options)) {
      $result = pg_select($this->database, $table, $conditions);
    }
    else {
      $result = pg_select($this->database, $table, $conditions, $options);
    }

    if (!is_null($options) && $options & PGSQL_DML_STRING) {
      if (is_string($result)) {
        return c_base_return_string::s_new($result);
      }
    }
    elseif ($result === TRUE) {
      unset($result);
      return new c_base_return_true();
    }
    unset($result);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_select', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Request for additional information provided by a table.
   *
   * This may includes things like comments and similar database-style documentation.
   *
   * @param string $table
   *   The name of the table.
   * @param bool $extended
   *   TRUE for extended additional information, FALSE for normal additional information.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array containing the additional information.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_meta_data()
   */
  public function get_meta_data($table, $extended = FALSE) {
    if (!is_string($table) || empty($table)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($extended)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'extended', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $result = pg_meta_data($this->database, $table, $extended);
    if ($result === FALSE) {
      unset($result);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_meta_data', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($result);
  }

  /**
   * Sets the verbosity of reported errors.
   *
   * @param int $verbosity
   *   One of the following:
   *   - PGSQL_ERRORS_TERSE
   *   - PGSQL_ERRORS_DEFAULT
   *   - PGSQL_ERRORS_VERBOSE
   *
   * @return c_base_return_status|c_base_return_int
   *   The previous verbosity level on success, FALSE otherwise.
   *
   * @see: pg_set_error_verbosity()
   */
  public function set_error_verbosity($verbosity) {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($verbosity)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'verbosity', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new(pg_set_error_verbosity($this->database, $verbosity));
  }

  /**
   * Returns the last error message string.
   *
   * @return c_base_return_status|c_base_return_string
   *   Message string on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_last_error()
   */
  public function get_last_error() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $result = pg_last_error($this->database);
    if ($result === FALSE) {
      unset($result);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_last_error', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Returns the last notice message string.
   *
   * @return c_base_return_status|c_base_return_string
   *   Message string on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_last_notice()
   */
  public function get_last_notice() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $result = pg_last_notice($this->database);
    if ($result === FALSE) {
      unset($result);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_last_notice', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Returns the state of a transaction in the database.
   *
   * @return c_base_return_status|c_base_return_int
   *   Transaction state success, FALSE otherwise.
   *   The returned transaction state integer is expected to be one of the following:
   *   - PGSQL_TRANSACTION_IDLE: currently idle.
   *   - PGSQL_TRANSACTION_ACTIVE: command is currently executing.
   *   - PGSQL_TRANSACTION_INTRANS: idle, in a valid transaction block.
   *   - PGSQL_TRANSACTION_INERROR: idle, in a failed transaction block.
   *   - PGSQL_TRANSACTION_UNKNOWN: invalid connection.
   *   - PGSQL_TRANSACTION_ACTIVE: query sent to server, but not yet completed.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_transaction_status()
   */
  public function get_transaction_status() {
    if (!is_resource($this->database)) {
      $database = ($this->connection_string instanceof c_base_connection_string) ? $this->connection_string->get_database()->get_value_exact() : '';
      $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
      unset($database);
      return c_base_return_error::s_false($error);
    }

    $result = pg_transaction_status($this->database);
    if ($result === FALSE) {
      unset($result);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_transaction_status', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Make sure the options parameter has PGSQL_DML_ASYNC set appropriately.
   *
   * @param int|null $options
   *   The options number to add or remove PGSQL_DML_ASYNC based on asynchronous status.
   */
  private function p_handle_asynchronous_options_parameter(&$options) {
    if ($this->asynchronous) {
      if (is_null($options)) {
        $options = PGSQL_DML_ASYNC | PGSQL_DML_NO_CONV;
      }
      else {
        if ($options & PGSQL_DML_ASYNC == 0) {
          $options += PGSQL_DML_ASYNC;
        }
      }
    }
    else {
      if (!is_null($options)) {
        if ($options & PGSQL_DML_ASYNC > 0) {
          $options -= PGSQL_DML_ASYNC;
        }
      }
    }
  }
}

/**
 * A generic class for managing database query results.
 *
 * It is important to note that the fetch_*() functions may return NULL as the database result.
 * This NULL is not stored as NULL, but is instead stored as c_base_return_value with the value set to NULL.
 * Therefore, one should use caution when using a c_base_return_value::get_value_exact() call as NULL values will not be returned.
 * It is recommended that only the c_base_return_value::get_value() call be used when accessing the result value.
 */
class c_base_database_result extends c_base_return_resource {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    if (is_resource($this->value)) {
      pg_free_result($this->value);
    }

    parent::__destruct();
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }


  /**
   * Fetch all columns in result set.
   *
   * This is not fetching all columns in the result set as in all column names.
   * Instead it will fetch all values from every row that belongs to a single column.
   *
   * This would be more aptly named something like fetch_all_column_rows($column).
   * But even that is not the best of names.
   *
   * To determine a columns number by name, use pg_field_num().
   *
   * @param int $column
   *   The column number to fetch.
   *
   * @return c_base_return_status|c_base_return_value
   *   The value on success, FALSE otherwise.
   *
   * @see: self::field_number()
   * @see: pg_fetch_all_columns()
   * @see: pg_field_num()
   */
  public function fetch_all_columns($column) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($column) || $column < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'column', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_fetch_all_columns($this->value, $column);
    if ($result === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_fetch_all_columns', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($result);
  }

  /**
   * Fetch all rows from result set as an array.
   *
   * @return c_base_return_status|c_base_return_array
   *   The value on success, FALSE otherwise.
   *
   * @see: pg_fetch_all()
   */
  public function fetch_all() {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    $result = pg_fetch_all($this->value);
    if ($result === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_fetch_all', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($result);
  }

  /**
   * Fetch array from result set.
   *
   * @param null|int $row
   *   (optional) The number of the row to fetch.
   *   If NULL, the next row is fetched.
   * @param int $type
   *   (optional) Specify the type of array returned:
   *   - PGSQL_ASSOC = return an associative array (just like when calling pg_fetch_assoc()).
   *   - PGSQL_NUM = return an array with numerical indeces.
   *   - PGSQL_BOTH = return with both associated indeces and numeric indeces.
   *
   * @return c_base_return_status|c_base_return_array
   *   The value on success, FALSE otherwise.
   *
   * @see: pg_fetch_array()
   */
  public function fetch_array($row = NULL, $type = PGSQL_ASSOC) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($row) && (!is_int($row) || $row < 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'row', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'type', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_fetch_array($this->value, $row, $type);
    if ($result === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_fetch_array', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($result);
  }

  /**
   * Fetch object from result set.
   *
   * @param null|int $row
   *   (optional) The number of the row to fetch.
   *   If NULL, the next row is fetched.
   * @param null|string $class
   *   When not NULL, then is a string represnting the name of a type of class.
   *   The return object will be returned as this type of object.
   * @param null|array $parameters
   *   When $class is not NULL and this is not NULL, then this contains parameters to pass to the object of type $class during initialization.
   *
   * @return c_base_return_status|c_base_return_object
   *   The value on success, FALSE otherwise.
   *   Even if a custom class is specified, a valid object is always returned as c_base_return_object.
   *   This is done for simplicity purposes.
   *   For types that inherit c_base_return_object, it should be simple to cast this return result to that child class.
   *
   * @see: pg_fetch_object()
   */
  public function fetch_object($row = NULL, $class = NULL, $parameters = NULL) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($row) && (!is_int($row) || $row < 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'row', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($class) && !is_string($class)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'class', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($parameters) && !is_array($parameters)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'parameters', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_string($class) && !empty($class)) {
      $result = pg_fetch_object($this->value, $row, $class, $parameters);
    }
    else {
      $result = pg_fetch_object($this->value, $row);
    }

    if ($result === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_fetch_object', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_object::s_new($result);
  }

  /**
   * Fetch a result from result set.
   *
   * @param null|int $row
   *   The number of the row to fetch.
   *   If NULL, the next row is fetched.
   * @param string|int $column
   *   A string representing the column name or an integer representing the column number.
   *
   * @return c_base_return_status|c_base_return_value
   *   The value on success, FALSE otherwise.
   *
   * @see: pg_fetch_result()
   */
  public function fetch_result($row, $column) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($row) && (!is_int($row) || $row < 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'row', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($column) && !is_string($column) || is_int($column) && $column < 0 || is_string($column) && empty($column)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'column', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($row)) {
      $result = pg_fetch_result($this->value, $row, $column);
    }
    else {
      $result = pg_fetch_result($this->value, $column);
    }

    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_fetch_result', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_value::s_new($result);
  }

  /**
   * Fetch row from result set.
   *
   * @param null|int $row
   *   (optional) The number of the row to fetch.
   *   If NULL, the next row is fetched.
   *
   * @return c_base_return_status|c_base_return_value
   *   The value on success, FALSE otherwise.
   *
   * @see: pg_fetch_row()
   */
  public function fetch_row($row = NULL) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($row) && (!is_int($row) || $row < 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'row', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_fetch_row($this->value, $row);
    if ($result === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_fetch_row', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_value::s_new($result);
  }

  /**
   * Returns an individual field code of an error port.
   *
   * @param null|int $code
   *   When not NULL this is a field code, such as:
   *   - PGSQL_DIAG_SEVERITY
   *   - PGSQL_DIAG_SQLSTATE
   *   - PGSQL_DIAG_MESSAGE_PRIMARY
   *   - PGSQL_DIAG_MESSAGE_DETAIL
   *   - PGSQL_DIAG_MESSAGE_HINT
   *   - PGSQL_DIAG_STATEMENT_POSITION
   *   - PGSQL_DIAG_INTERNAL_POSITION
   *   - PGSQL_DIAG_INTERNAL_QUERY
   *   - PGSQL_DIAG_CONTEXT
   *   - PGSQL_DIAG_SOURCE_FILE
   *   - PGSQL_DIAG_SOURCE_LINE
   *   - PGSQL_DIAG_SOURCE_FUNCTION
   *
   * @return c_base_return_status|c_base_return_string
   *   String is returned if found, a NULL is returned if not found, and FALSE is returned on error (with error flag set).
   *
   * @see: pg_result_error()
   * @see: pg_result_error_field()
   */
  public function error($code = NULL) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($code) && !is_int($code)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'code', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($code)) {
      $result = pg_result_error($this->value);
    }
    else {
      $result = pg_result_error_field($this->value, $code);
    }

    if (is_null($result) || is_string($result) && mb_strlen($result) == 0) {
      unset($result);
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Returns the number of rows affected by the SQL statement associated with this result set.
   *
   * @return c_base_return_int|c_base_return_status
   *   The number of items (be it instances, records, or rows) that are affected by any INSERT, UPDATE, DELETE, or SELECT queries.
   *   FALSE with error bit set is returned on error.
   *
   * @see: pg_affected_rows()
   */
  public function affected_rows() {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new(pg_affected_rows($this->value));
  }

  /**
   * Get the number of rows in the result set.
   *
   * @return c_base_return_status|c_base_return_int
   *   The number of rows or FALSE on failure.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_num_rows()
   */
  public function number_of_rows() {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    $result = pg_num_rows($this->value);
    if ($result < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_num_rows', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($result);
  }

  /**
   * Get the number of columns in the result set.
   *
   * @return c_base_return_status|c_base_return_int
   *   The number of rows or FALSE on failure.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_num_fields()
   */
  public function number_of_columns() {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    $result = pg_num_fields($this->value);
    if ($result < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_num_fields', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($result);
  }

  /**
   * Returns the name of a column at the given location in the result set.
   *
   * @param int $number
   *   The column number of the field to get the name of.
   *
   * @return c_base_return_status|c_base_return_string
   *   The name of the field or FALSE on error.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_field_name()
   */
  public function field_name($number) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($number) || $number < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'number', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_field_name($this->value, $number);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_field_name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Returns the number of a column with the given name in the result set.
   *
   * @param string $name
   *   The column name of the field to get the number of.
   *
   * @return c_base_return_status|c_base_return_int
   *   The number of the field or FALSE on error.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_field_num()
   */
  public function field_number($name) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($name) || mb_strlen($name) == 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // this returnes -1 on error and >= 0 on success, so translate the codes appropriately.
    $result = pg_field_number($this->value, $name);
    if ($result < 0) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_field_number', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($result);
  }

  /**
   * Returns the 'printed length' of a field in the result set.
   *
   * This is the number of characters used to represent a result value.
   *
   * @param int|null $row
   *   The row number to fetch.
   * @param string|int $name_or_number
   *   The column name or column number of the field to get the printed length of.
   *   When passed as an integer, this is the column number.
   *   When passed as a string, this is a column name.
   *   Therefore a value of '0' would be a column named 0 and a value of 0 would be column number 0.
   *
   * @return c_base_return_status|c_base_return_int
   *   The printed length of the 'field' or FALSE on error.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: self::field_bytes()
   * @see: pg_field_prtlen()
   */
  public function field_length($row, $name_or_number) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($row) && (!is_int($row) || $row < 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'row', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($name_or_number) && !(is_string($name_or_number) && mb_strlen($name) > 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name_or_number', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($row)) {
      $result = pg_field_prtlen($this->value, $name_or_number);
    }
    else {
      $result = pg_field_prtlen($this->value, $row, $name_or_number);
    }

    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_field_prtlen', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($result);
  }

  /**
   * Return the size of the column.
   *
   * @param int $column
   *   The column number to return the size of.
   *
   * @return c_base_return_status|c_base_return_int
   *   The size of the column or FALSE on error.
   *   The returned size may be -1, in which case means the size is variable.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: self::field_length()
   * @see: pg_field_size()
   */
  public function field_bytes($column) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($column) || $column < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'column', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_size($this->value, $column);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_size', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($result);
  }

  /**
   * Return the name or oid of the tables field.
   *
   * The word usage here is confusing.
   * Documentation states that this function 'returns the name or oid of the "Tables Field"'.
   * Really, what this appears to do is return name or oid of the "Columns Table".
   *
   * @param int $column
   *   The column number to return the table name for.
   * @param bool $oid
   *   (optional) When TRUE, the oid is retuned instead of the table name.
   *
   * @return c_base_return_status|c_base_return_int|c_base_return_string
   *   The name of the table that the given column belongs to or FALSE on error.
   *   If oid is set to TRUE, then the oid.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_field_table()
   * @see: http://www.postgresql.org/docs/current/static/datatype-oid.html
   */
  public function field_table($column, $oid = FALSE) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($column) || $column < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'column', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($oid)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'oid', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_field_table($this->value, $column, $oid);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_field_table', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if ($oid) {
      return c_base_return_int::s_new($result);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Return the oid of the column.
   *
   * @param int $column
   *   The column number to return the oid of.
   *
   * @return c_base_return_status|c_base_return_int
   *   The oid of the requested column to or FALSE on error.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_field_type_oid()
   * @see: http://www.postgresql.org/docs/current/static/datatype-oid.html
   */
  public function field_type_oid($column) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($column) || $column < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'column', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_field_type_oid($this->value, $column);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_field_type_oid', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($result);
  }

  /**
   * Returns the last row's oid.
   *
   * This is used to retrieve an OID assigned to an inserted row.
   * Oid is now optional, so try using pg_result_status() instead.
   *
   * @return c_base_return_status|c_base_return_string
   *   A string containing the oid assigned to the most recently inserted row on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_last_oid()
   * @see: pg_result_status()
   * @see: http://www.postgresql.org/docs/current/static/datatype-oid.html
   */
  public function last_oid() {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    $result = pg_last_oid($this->database);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_last_oid', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Return the storage type of the column.
   *
   * @param int $column
   *   The column number to return the oid of.
   *
   * @return c_base_return_status|c_base_return_int
   *   The oid of the requested column to or FALSE on error.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_field_type()
   */
  public function field_type($column) {
    if (!is_resource($this->value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_RESOURCE);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($column) || $column < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'column', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $result = pg_field_type($this->value, $column);
    if ($result === FALSE) {
      unset($result);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'pg_field_type', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($result);
  }

  /**
   * Free's memory allocated to the class.
   *
   * @return c_base_return_status
   *   TRUE is returned on success, FALSE is returned if nothing to free.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: pg_free_result()
   */
  public function free_result() {
    if (!is_resource($this->value)) {
      return new c_base_return_false();
    }

    if (pg_free_result($this->value)) {
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::FUNCTION_FAILURE);
    return c_base_return_error::s_false($error);
  }
}

/**
 * A generic class for building a query array.
 *
 * A query array is essentially an array whose keys define how the query is to be generated.
 * Future work is planned, but this is essentially being provided a stub until then.
 */
class c_base_database_query extends c_base_return_array {
  const OPERATION_NONE = 0;
  const OPERATION_SELECT = 1;

  const ALIAS_PREFIX_TABLE = 't_';
  const ALIAS_PREFIX_COLUMN = 'c_';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->p_initialize();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Import and process an exported array.
   *
   * This string will be validated and processed before being saved.
   * Invalid data will be lost.
   *
   * @param string $import
   *   A json encoded array reflecting the contents of this object.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function import($import) {
    if (!is_string($import) || empty($import)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'import', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $decoded = json_decode($import, TRUE);
    if (!is_array($decoded) || empty($decoded)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'json_decode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->p_initialize();

    $this->value = $decoded;

    return new c_base_return_true();
  }

  /**
   * Export the values of this object into a json string.
   *
   * @return c_base_return_status|c_base_return_string
   *   FALSE is returned when there is no content to export.
   *   Otherwise, a json encoded string is returned.
   *   FALSE with the error bit set is returned on error.
   */
  public function export() {
    if ($this->count_table == 0 || empty($this->value)) {
      return new c_base_return_false();
    }

    // only the value array needs to be exported.
    // everything else has to be re-created on import for security reasons.
    $encoded = json_encode($this->value);
    if (!is_string($encoded)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'json_encode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($encoded);
  }

  /**
   * Perform initialization of all variables in this class.
   */
  private function p_initialize() {
    unset($this->value);
    $this->value = array();
  }
}
