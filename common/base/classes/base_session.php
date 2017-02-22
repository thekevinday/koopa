<?php
/**
 * @file
 * Provides a class for managing sessions.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A class for managing sessions.
 *
 * This utilizes the custom session project called 'sessionize_accounts' and does not use PHP's session management.
 * The current design does not store session variables, only the session key, username, ip address, and password.
 * This session key can be used to retrieve a password between requests and to access the database.
 * The database can then be used to retrieve any session variables.
 */
class c_base_session {
  const PACKET_MAX_LENGTH = 8192;
  const SOCKET_PATH_PREFIX = '/programs/sockets/sessionize_accounts/';
  const SOCKET_PATH_SUFFIX = '/sessions.socket';
  const PASSWORD_CLEAR_TEXT_LENGTH = 2048;

  private $socket;
  private $socket_path;
  private $socket_timeout;

  private $system_name;

  private $name;
  private $id_user;
  private $host;
  private $password;
  private $session_id;
  private $settings;

  private $error;

  private $timeout_expire;
  private $timeout_max;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->socket = NULL;
    $this->socket_path = NULL;
    $this->socket_timeout = NULL;

    $this->system_name = NULL;

    $this->name = NULL;
    $this->id_user = NULL;
    $this->host = NULL;
    $this->password = NULL;
    $this->session_id = NULL;
    $this->settings = NULL;

    $this->error = NULL;

    $this->timeout_expire = NULL;
    $this->timeout_max = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    $this->clear_password();

    if (is_resource($this->socket)) {
      @socket_close($this->socket);
    }

    unset($this->socket);
    unset($this->socket_path);
    unset($this->socket_timeout);

    unset($this->system_name);

    unset($this->name);
    unset($this->id_user);
    unset($this->host);
    unset($this->password);
    unset($this->session_id);
    unset($this->special);
    unset($this->settings);

    unset($this->error);

    unset($this->timeout_expire);
    unset($this->timeout_max);
  }

  /**
   * Assigns the system name, which is used to create the socket path.
   *
   * @param string $system_name
   *   A system name string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_system_name($system_name) {
    if (!is_string($system_name) || empty($system_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'system_name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->system_name = basename($system_name);
    $this->socket_path = self::SOCKET_PATH_PREFIX . $this->system_name . self::SOCKET_PATH_SUFFIX;

    return new c_base_return_true();
  }

  /**
   * Returns the stored system name.
   *
   * @return c_base_return_string
   *   The system name string or NULL if undefined.
   */
  public function get_system_name() {
    return c_base_return_string::s_new($this->system_name);
  }

  /**
   * Assigns the user name associated with the session.
   *
   * @param string $name
   *   The user name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_name($name) {
    if (!is_string($name) || empty($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (mb_strlen($name) == 0 || preg_match('/^(\w|-)+$/i', $name) != 1) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':format_name' => 'name', ':expected_format' => '. Alphanumeric and dash characters only', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    $this->name = $name;
    return new c_base_return_true();
  }

  /**
   * Returns the stored user name.
   *
   * @return c_base_return_string
   *   The user name string.
   */
  public function get_name() {
    return c_base_return_string::s_new($this->name);
  }

  /**
   * Assigns the user id associated with the session.
   *
   * @param int $id_user
   *   The user id.
   *   This must be greater than or equal to 0.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_id_user($id_user) {
    if ((is_int($id_user) && $id_user < 0) || !is_int($id_user) && (!is_string($id_user) || !(is_numeric($id_user) && (int) $id_user >= 0))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id_user', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_user = (int) $id_user;
    return new c_base_return_true();
  }

  /**
   * Returns the stored user id.
   *
   * @return c_base_return_int
   *   The user id_user integer.
   */
  public function get_id_user() {
    return c_base_return_int::s_new($this->id_user);
  }

  /**
   * Assigns the host ip address associated with the session.
   *
   * @param string $host
   *   The host ip address.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_host($host) {
    if (!is_string($host) || empty($host)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (mb_strlen($host) == 0 || ip2long($host) === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->host = $host;
    return new c_base_return_true();
  }

  /**
   * Returns the stored host ip address.
   *
   * @return c_base_return_string
   *   The host ip address string.
   */
  public function get_host() {
    return c_base_return_string::s_new($this->host);
  }

  /**
   * Assigns the password associated with a user name.
   *
   * Manually assign this only for new sessions only.
   * When existing sessions are loaded, this will be auto-populated.
   *
   * @param string|null $password
   *   The password.
   *   Assigning null disable the password.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_session::load()
   */
  public function set_password($password) {
    if (!is_null($password) && (!is_string($password) || empty($password))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'password', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($password)) {
      $this->password = NULL;
      return new c_base_return_true();
    }

    // deny 0-length passwords.
    if (mb_strlen($password) == 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'password', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->password = $password;
    return new c_base_return_true();
  }

  /**
   * Returns the stored password.
   *
   * @return c_base_return_string
   *   The password string.
   */
  public function get_password() {
    return c_base_return_string::s_new($this->password);
  }

  /**
   * Assigns the settings associated with the session.
   *
   * The settings provides optional information that a service may want to store with a particular session.
   *
   * @param array $settings
   *   The settings array to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_settings($settings) {
    if (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->settings = $settings;
    return new c_base_return_true();
  }

  /**
   * Returns the stored settings.
   *
   * @return c_base_return_array
   *   The settings array.
   */
  public function get_settings() {
    return c_base_return_array::s_new($this->settings);
  }

  /**
   * Uses an unproven technique in an attempt to 'delete' a password from memory and then unallocating the resource.
   *
   * The password will be set to a hopefully large enough string of whitespaces.
   * The password variable will then be unset.
   *
   * This does not perform the garbage collection, but it is suggested that the caller consider calling gc_collect_cycles().
   *
   * @see: gc_collect_cycles()
   */
  public function clear_password() {
    $this->password = str_repeat(' ', self::PASSWORD_CLEAR_TEXT_LENGTH);
    unset($this->password);
  }

  /**
   * Assigns the session id associated with a session.
   *
   * Manually assign this for existing sessions only.
   * This should be auto-populated when a new session is saved.
   *
   * @param string $session_id
   *   The session id string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_session::save()
   */
  public function set_session_id($session_id) {
    if (!is_string($session_id) || empty($session_id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'session_id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // deny 0-length session_id.
    if (mb_strlen($session_id) == 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'session_id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->session_id = $session_id;
    return new c_base_return_true();
  }

  /**
   * Returns the stored session id.
   *
   * @return c_base_return_string
   *   The session id string.
   */
  public function get_session_id() {
    return c_base_return_string::s_new($this->session_id);
  }

  /**
   * Assigns the session expiration timeout.
   *
   * @param int $timeout_expire
   *   The unix timestamp for the expiration timeout.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_session::save()
   */
  public function set_timeout_expire($timeout_expire) {
    if (!is_int($timeout_expire)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'timeout_expire', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->timeout_expire = $timeout_expire;
    return new c_base_return_true();
  }

  /**
   * Returns the unix timestamp for the session expiration timeout.
   *
   * @return c_base_return_int
   *   The unix timestamp for the session expiration timeout.
   */
  public function get_timeout_expire() {
    return c_base_return_int::s_new($this->timeout_expire);
  }

  /**
   * Assigns the max session timeout.
   *
   * @param int $timeout_max
   *   The unix timestamp for the max session timeout.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_session::save()
   */
  public function set_timeout_max($timeout_max) {
    if (!is_int($timeout_max)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'timeout_max', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->timeout_max = $timeout_max;
    return new c_base_return_true();
  }

  /**
   * Returns the unix timestamp for the max timeout.
   *
   * @return c_base_return_int
   *   The unix timestamp for the max timeout.
   */
  public function get_timeout_max() {
    return c_base_return_int::s_new($this->timeout_max);
  }

  /**
   * Assigns the max session timeout.
   *
   * @param int $seconds
   *   Number of seconds until timeout is reached.
   * @param int $microseconds
   *   (optional) Number of microseconds until timeout is reached.
   * @param bool $receive
   *   (optional) When TRUE, the receive timeout is assigned.
   *   When FALSE, the send timeout is assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: socket_set_option()
   */
  public function set_socket_timeout($seconds, $microseconds = 0, $receive = TRUE) {
    if (!is_int($seconds) || $seconds < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'seconds', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($microseconds) || $microseconds < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'microseconds', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($receive)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'receive', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->socket_timeout)) {
      $this->socket_timeout = array(
        'send' => NULL,
        'receive' => NULL,
      );
    }

    if ($receive) {
      $this->socket_timeout['receive'] = array('seconds' => $seconds, 'microseconds' => $microseconds);
      if (is_resource($this->socket)) {
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, $seconds, $microseconds);
      }
    }
    else {
      $this->socket_timeout['send'] = array('seconds' => $seconds, 'microseconds' => $microseconds);
      if (is_resource($this->socket)) {
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, $seconds, $microseconds);
      }
    }

    return new c_base_return_true();
  }

  /**
   * Returns the unix timestamp for the max timeout.
   *
   * @return c_base_return_int
   *   The unix timestamp for the max timeout.
   *
   * @see: socket_get_option()
   */
  public function get_socket_timeout() {
    return c_base_return_array::s_new($this->socket_timeout);
  }

  /**
   * Returns the stored error array.
   *
   * This should be called after a load() or a save() command to check to see if the socket returned any error.
   *
   * This does not return the socket error, for that use self::get_error_socket()
   *
   * @return c_base_return_array|c_base_return_status
   *   The error array or boolean returned by the socket when transferring data or NULL if there are no socket errors.
   *   A value of FALSE means that no error was returned by the socket.
   *   A value of an array() for both load() and save() would contain the socket error message.
   *
   * @see: self::get_error_socket()
   */
  public function get_error() {
    if (is_bool($this->error)) {
      c_base_return_bool::s_new($this->error);
    }

    return c_base_return_array::s_new($this->error);
  }

  /**
   * This returns the error code reported by the socket itself.
   *
   * Use self::get_error() to get the error reported in the packet and not the socket.
   *
   * @return c_base_return_int
   *   Number representing the socket error.
   *
   * @see: self::get_error()
   * @see: socket_last_error()
   */
  public function get_error_socket() {
    if (!is_resource($this->socket)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new(@socket_last_error($this->socket));
  }

  /**
   * This clears the error on the socket if any exist.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: self::get_error_socket()
   * @see: socket_clear_error()
   */
  public function clear_error_socket() {
    if (!is_resource($this->socket)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    @socket_clear_error($this->socket);

    return new c_base_return_true();
  }

  /**
   * Opens a socket connection for later loading.
   *
   * The system name must be defined before this call to ensure a valid socket path exists.
   * The socket should be closed with c_base_session::do_disconnect() when finished.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on failure.
   *
   * @see: c_base_session::set_system_name()
   * @see: c_base_session::do_disconnect()
   */
  function do_connect() {
    if (is_resource($this->socket)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->system_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->system_name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
    if ($this->socket === FALSE) {
      $this->do_disconnect();

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_create', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $connected = @socket_connect($this->socket, $this->socket_path, 0);
    if ($connected === FALSE) {
      unset($connected);
      $this->do_disconnect();

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_connect', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }
    unset($connected);


    // assign any pre-defined timeouts.
    if (isset($this->socket_timeout['receive']['seconds'])) {
      socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, $this->socket_timeout['receive']['seconds'], $this->socket_timeout['receive']['microseconds']);
    }

    if (isset($this->socket_timeout['send']['seconds'])) {
      socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, $this->socket_timeout['send']['seconds'], $this->socket_timeout['send']['microseconds']);
    }

    return new c_base_return_true();
  }

  /**
   * Close an opened socket.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function do_disconnect() {
    if (!is_resource($this->socket)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    @socket_close($this->socket);

    $this->socket = NULL;
    return new c_base_return_true();
  }

  /**
   * Returns the connected status.
   *
   * This represents whether or not the self::do_connect() function was successfully called.
   * The state of the connection should still be checked.
   *
   * @return c_base_return_status
   *   TRUE when connected, FALSE otherwise.
   */
  public function is_connected() {
    if (is_resource($this->socket)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Loads the session information from an open socket.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on failure.
   *
   * @see: c_base_session::do_connect()
   * @see: c_base_session::p_transfer()
   */
  public function do_pull() {
    if (is_null($this->host)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->session_id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->session_id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->socket) || @socket_last_error($this->socket) != 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $response = $this->p_transfer(array('ip' => $this->host, 'session_id' => $this->session_id));
    if (c_base_return::s_has_error($response)) {
      return $response->get_error();
    }

    $response = c_base_return_array::s_value_exact($response);
    if (empty($response['result']) || !is_array($response['result'])) {
      unset($response);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_transfer', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->name = NULL;
    if (isset($response['result']['name']) && is_string($response['result']['name'])) {
      $this->name = $response['result']['name'];
    }

    $this->id_user = NULL;
    if (isset($response['result']['id_user']) && is_int($response['result']['id_user'])) {
      $this->id_user = $response['result']['id_user'];
    }

    if (!is_null($this->password)) {
      $this->password = str_repeat(' ', self::PASSWORD_CLEAR_TEXT_LENGTH);
    }

    $this->password = NULL;
    if (isset($response['result']['password']) && is_string($response['result']['password'])) {
      $this->password = $response['result']['password'];
    }

    $this->timeout_expire = NULL;
    if (isset($response['result']['expire']) && is_int($response['result']['expire'])) {
      $this->timeout_expire = $response['result']['expire'];
    }

    $this->timeout_max = NULL;
    if (isset($response['result']['max']) && is_int($response['result']['max'])) {
      $this->timeout_max = $response['result']['max'];
    }

    $this->settings = NULL;
    if (isset($response['result']['settings']) && is_array($response['result']['settings'])) {
      $this->settings = $response['result']['settings'];
    }

    unset($response);
    return new c_base_return_true();
  }

  /**
   * Saves the session information to an open socket.
   *
   * This function accepts interval expire and max, but these should be considered soft limits.
   * The server is allowed to impose its own hard limits that prevent expire and max from being set longer than.
   *
   * @param int|null $interval_expire
   *   (optional) Number of seconds a session should wait while idling before expiring the session.
   * @param int|null $interval_max
   *   (optional) The maximum time a session is allowed to exist.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on failure.
   *
   * @see: c_base_session::set_name()
   * @see: c_base_session::set_host()
   * @see: c_base_session::set_password()
   * @see: c_base_session::do_connect()
   * @see: c_base_session::p_transfer()
   */
  public function do_push($interval_expire = NULL, $interval_max = NULL) {
    if (is_null($this->name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->id_user)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->id_user', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->host)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->password)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->password', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->socket) || @socket_last_error($this->socket) != 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($interval_expire) && (!is_int($interval_expire) || $interval_expire < 1)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'interval_expires', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($interval_max) && (!is_int($interval_max) || $interval_max < 1)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'interval_max', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // settings is allowed to be undefined, so send it as an empty array.
    if (is_null($this->settings)) {
      $this->settings = array();
    }

    $response = $this->p_transfer(array('name' => $this->name, 'id_user' => $this->id_user, 'ip' => $this->host, 'password' => $this->password, 'expire' => $interval_expire, 'max' => $interval_max, 'settings' => $this->settings));
    if (c_base_return::s_has_error($response)) {
      return c_base_return_error::s_false($response->get_error(0));
    }

    $response = c_base_return_array::s_value_exact($response);
    if (empty($response['result']) || !is_array($response['result'])) {
      unset($response);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_transfer', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->session_id = NULL;
    if (isset($response['result']['session_id'])) {
      $this->session_id = $response['result']['session_id'];
    }

    $this->timeout_expire = NULL;
    if (isset($response['result']['expire'])) {
      $this->timeout_expire = $response['result']['expire'];
    }

    $this->timeout_max = NULL;
    if (isset($response['result']['max'])) {
      $this->timeout_max = $response['result']['max'];
    }

    unset($response);
    return new c_base_return_true();
  }

  /**
   * Terminates a session from an open socket.
   *
   * Unlike self::do_disconnect(), this does not close the connection to the socket, it closes the session itself.
   *
   * This is used to terminate a session before the expiration date and time is reached.
   * Use this on logout operations.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on failure.
   *
   * @see: self::do_connect()
   * @see: self::p_transfer()
   */
  public function do_terminate() {
    if (is_null($this->host)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->session_id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->session_id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (!is_resource($this->socket) || @socket_last_error($this->socket) != 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $response = $this->p_transfer(array('ip' => $this->host, 'session_id' => $this->session_id, 'close' => TRUE));
    if (c_base_return::s_has_error($response)) {
      return c_base_return_error::s_false($response->get_error(0));
    }

    $response = c_base_return_array::s_value_exact($response);
    if (empty($response['result']) || !is_array($response['result'])) {
      unset($response);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_transfer', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($this->password)) {
      $this->password = str_repeat(' ', self::PASSWORD_CLEAR_TEXT_LENGTH);
    }

    $this->name = NULL;
    $this->password = NULL;
    $this->timeout_expire = NULL;
    $this->timeout_max = NULL;

    unset($response);
    return new c_base_return_true();
  }

  /**
   * Closes (terminates) a session from an open socket.
   *
   * Unlike self::do_disconnect(), this does not close the connection to the socket, it closes the session itself.
   *
   * This is used to terminate a session before the expiration date and time is reached.
   * Use this on logout operations.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on failure.
   *
   * @see: self::do_connect()
   * @see: self::p_transfer()
   */
  public function do_flush() {
    if (!is_resource($this->socket) || @socket_last_error($this->socket) != 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->socket', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $response = $this->p_transfer(array('flush' => TRUE));
    if (c_base_return::s_has_error($response)) {
      return $response->get_error();
    }

    $response = c_base_return_array::s_value_exact($response);
    if (empty($response['result']) || !is_array($response['result'])) {
      unset($response);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_transfer', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    unset($response);
    return new c_base_return_true();
  }

  /**
   * Transfer a request packet through the socket.
   *
   * @param array $request
   *   A request array defined as required by the socket.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array is returned on success.
   *   FALSE is returned otherwise.
   *
   * @see: c_base_session::do_connect()
   */
  private function p_transfer($request) {
    unset($this->error);
    $this->error = NULL;

    $json = json_encode($request);

    $written = @socket_write($this->socket, $json);
    unset($json);

    if ($written === FALSE || $written == 0) {
      unset($written);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_write', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }
    unset($written);

    $json = @socket_read($this->socket, self::PACKET_MAX_LENGTH);
    if (!is_string($json) || mb_strlen($json) == 0) {
      unset($json);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_read', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $response = json_decode($json, TRUE);
    unset($json);

    if (isset($response['error'])) {
      $this->error = $response['error'];
    }

    if ($response === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'json_decode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($response);
  }
}

/**
 * A return class whose value is represented as a c_base_session.
 */
class c_base_session_return extends c_base_return_value {
  use t_base_return_value_exact;

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
    return self::p_s_value_exact($return, __CLASS__, new c_base_session());
  }

  /**
   * Assign the value.
   *
   * @param c_base_session $value
   *   Any value so long as it is a c_base_session object.
   *   NULL is not allowed.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!($value instanceof c_base_session)) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return c_base_session $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!($this->value instanceof c_base_session)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return c_base_session $value
   *   The value c_base_session stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof c_base_session)) {
      $this->value = new c_base_session();
    }

    return $this->value;
  }
}
