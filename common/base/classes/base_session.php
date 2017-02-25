<?php
/**
 * @file
 * Provides a class for managing sessions.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_form.php');

/**
 * A class for managing sessions.
 *
 * This utilizes the custom session project called 'sessionize_accounts' and does not use PHP's session management.
 * The current design does not store session variables, only the session key, username, ip address, and password.
 * This session key can be used to retrieve a password between requests and to access the database.
 * The database can then be used to retrieve any session variables.
 */
class c_base_session extends c_base_return {
  const PACKET_MAX_LENGTH = 8192;
  const SOCKET_PATH_PREFIX = '/programs/sockets/sessionize_accounts/';
  const SOCKET_PATH_SUFFIX = '/sessions.socket';
  const PASSWORD_CLEAR_TEXT_LENGTH = 2048;

  private $socket;
  private $socket_directory;
  private $socket_path;
  private $socket_timeout;

  private $system_name;

  private $cookie;

  private $name;
  private $id_user;
  private $host;
  private $password;
  private $session_id;
  private $settings;

  private $timeout_expire;
  private $timeout_max;

  private $problems;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->socket = NULL;
    $this->socket_directory = NULL;
    $this->socket_path = NULL;
    $this->socket_timeout = NULL;

    $this->cookie = NULL;

    $this->system_name = NULL;

    $this->name = NULL;
    $this->id_user = NULL;
    $this->host = NULL;
    $this->password = NULL;
    $this->session_id = NULL;
    $this->settings = NULL;

    $this->timeout_expire = NULL;
    $this->timeout_max = NULL;

    $this->problems = NULL;
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
    unset($this->socket_directory);
    unset($this->socket_path);
    unset($this->socket_timeout);

    unset($this->cookie);

    unset($this->system_name);

    unset($this->name);
    unset($this->id_user);
    unset($this->host);
    unset($this->password);
    unset($this->session_id);
    unset($this->special);
    unset($this->settings);

    unset($this->timeout_expire);
    unset($this->timeout_max);

    unset($this->problems);

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
   * Assigns the socket directory name, which is used to create the socket path.
   *
   * If not specified, then self::SOCKET_PATH_PREFIX will be used.
   *
   * @param string|null $socket_directory
   *   A directory name.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_socket_directory($socket_directory) {
    if (!is_null($socket_directory) && (!is_string($socket_directory) || empty($socket_directory))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'socket_directory', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($socket_directory)) {
      $this->socket_directory = NULL;
      return new c_base_return_true();
    }

    if (!is_dir($socket_directory)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':direction_name' => $socket_directory, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_DIRECTORY);
      return c_base_return_error::s_false($error);
    }

    // require a single closing '/' at the end of the path.
    $this->socket_directory = preg_replace('@/*$@i', '', $socket_directory) . '/';

    return new c_base_return_true();
  }

  /**
   * Returns the stored socket directory name.
   *
   * @return c_base_return_string|c_base_return_null
   *   The system name string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_socket_directory() {
    if (is_null($this->socket_directory)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->socket_directory);
  }

  /**
   * Assigns the cookie associated with this session.
   *
   * @param c_base_cookie|null $cookie
   *   The session cookie.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_cookie($cookie) {
    if (!is_null($cookie) && !($cookie instanceof c_base_cookie)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'cookie', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->cookie = $cookie;

    return new c_base_return_true();
  }

  /**
   * Returns the stored system name.
   *
   * @return c_base_cookie|c_base_return_null
   *   The session cookie or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_cookie() {
    if (is_null($this->cookie)) {
      return new c_base_return_null();
    }

    return $this->cookie;
  }

  /**
   * Assigns the system name, which is used to create the socket path.
   *
   * @param string|null $system_name
   *   A system name string.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_system_name($system_name) {
    if (!is_null($system_name) && (!is_string($system_name) || empty($system_name))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'system_name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->socket_directory)) {
      $this->socket_directory = NULL;
      return new c_base_return_true();
    }

    $this->system_name = basename($system_name);
    $this->socket_path = $this->socket_directory . $this->system_name . self::SOCKET_PATH_SUFFIX;

    return new c_base_return_true();
  }

  /**
   * Returns the stored system name.
   *
   * @return c_base_return_string
   *   The system name string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_system_name() {
    if (is_null($this->system_name)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->system_name);
  }

  /**
   * Assigns the user name associated with the session.
   *
   * @param string|null $name
   *   The user name.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_name($name) {
    if (!is_null($name) && (!is_string($name) || empty($name))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($name)) {
      $this->name = NULL;
      return new c_base_return_true();
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
   * @return c_base_return_string|c_base_return_null
   *   The user name string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_name() {
    if (is_null($this->name)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->name);
  }

  /**
   * Assigns the user id associated with the session.
   *
   * @param int|null $id_user
   *   The user id.
   *   This must be greater than or equal to 0.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_id_user($id_user) {
    if (!is_null($id_user) && ((is_int($id_user) && $id_user < 0))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id_user', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_user = $id_user;

    return new c_base_return_true();
  }

  /**
   * Returns the stored user id.
   *
   * @return c_base_return_int|c_base_return_null
   *   The user id_user integer or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_id_user() {
    if (is_null($this->id_user)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->id_user);
  }

  /**
   * Assigns the host ip address associated with the session.
   *
   * @param string|null $host
   *   The host ip address.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_host($host) {
    if (!is_null($host) && (!is_string($host) || empty($host))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'host', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($host)) {
      $this->host = nULL;
      return new c_base_return_true();
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
   * @return c_base_return_string|c_base_return_null
   *   The host ip address string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_host() {
    if (is_null($this->host)) {
      return new c_base_return_null();
    }

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
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
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
   * @return c_base_return_string|c_base_return_null
   *   The password string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_password() {
    if (is_null($this->password)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->password);
  }

  /**
   * Assigns the settings associated with the session.
   *
   * The settings provides optional information that a service may want to store with a particular session.
   *
   * @param $setting
   *   A value to assign at the specified delta.
   *   Can be any variable type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_setting($delta, $setting) {
    if (!is_int($delta) && !is_string($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'delta', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->settings)) {
      $this->settings = array();
    }

    $this->settings[$delta] = $setting;
    return new c_base_return_true();
  }

  /**
   * Returns a specific index within the stored settings.
   *
   * @param int|string $delta
   *   (optional) If an integer or a string, represents a specific index in the given settings array.
   *
   * @return c_base_return_value|c_base_return_null
   *   The settings array value at the specified delta or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_setting($delta) {
    if (!is_int($delta) && !is_string($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'delta', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->settings)) {
      return new c_base_return_null();
    }

    if (!array_key_exists($delta, $this->settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':index_name' => $delta, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    if ($this->settings[$delta] instanceof c_base_return) {
      return $this->settings[$delta];
    }

    return c_base_return_value::s_new($this->settings[$delta]);
  }

  /**
   * Assigns the settings associated with the session.
   *
   * The settings provides optional information that a service may want to store with a particular session.
   *
   * @param array|null $settings
   *   The settings array to assign.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_settings($settings) {
    if (!is_null($settings) && !is_array($settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->settings = $settings;
    return new c_base_return_true();
  }

  /**
   * Returns the stored settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   The settings array or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_settings() {
    if (is_null($this->settings)) {
      return new c_base_return_null();
    }

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
   * @param string|null $session_id
   *   The session id string.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: c_base_session::save()
   */
  public function set_session_id($session_id) {
    if (!is_null($session_id) && (!is_string($session_id) || empty($session_id))) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'session_id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($session_id)) {
      $this->session_id = NULL;
      return new c_base_return_true();
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
   * @return c_base_return_string|c_base_return_null
   *   The session id string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_session_id() {
    if (is_null($this->session_id)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->session_id);
  }

  /**
   * Assigns the session expiration timeout.
   *
   * @param int|null $timeout_expire
   *   The unix timestamp for the expiration timeout.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: c_base_session::save()
   */
  public function set_timeout_expire($timeout_expire) {
    if (!is_null($timeout_expire) && !is_int($timeout_expire)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'timeout_expire', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->timeout_expire = $timeout_expire;
    return new c_base_return_true();
  }

  /**
   * Returns the unix timestamp for the session expiration timeout.
   *
   * @return c_base_return_int|c_base_return_null
   *   The unix timestamp for the session expiration timeout or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_timeout_expire() {
    if (is_null($this->timeout_expire)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->timeout_expire);
  }

  /**
   * Assigns the max session timeout.
   *
   * @param int|null $timeout_max
   *   The unix timestamp for the max session timeout.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: c_base_session::save()
   */
  public function set_timeout_max($timeout_max) {
    if (!is_null($timeout_max) && !is_int($timeout_max)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'timeout_max', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->timeout_max = $timeout_max;
    return new c_base_return_true();
  }

  /**
   * Returns the unix timestamp for the max timeout.
   *
   * @return c_base_return_int|c_base_return_null
   *   The unix timestamp for the max timeout or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_timeout_max() {
    if (is_null($this->timeout_max)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->timeout_max);
  }

  /**
   * Assigns an array of form problems.
   *
   * @param array|null $problems
   *   An array of form problems.
   *   Set to NULL to remove any existing values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_problems($problems) {
    if (!is_null($problems) && !is_array($problems)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'problems', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->problems = array();

    if (is_null($problems)) {
      return new c_base_return_true();
    }

    foreach ($problems as $problem) {
      if ($problem instanceof c_base_form_problem) {
        $this->problems[] = $problem;
      }
    }
    unset($problem);

    $this->problems = $problems;
    return new c_base_return_true();
  }

  /**
   * Returns the unix timestamp for the max timeout.
   *
   * @return c_base_return_array
   *   An array containing any problems associated with forms for this session.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_problems() {
    if (is_null($this->problems)) {
      $this->problems = array();
    }

    return c_base_return_array::s_new($this->problems);
  }

  /**
   * Assigns the max session timeout.
   *
   * @param int|null $seconds
   *   Number of seconds until timeout is reached.
   *   Set to NULL to remove any existing values.
   * @param int $microseconds
   *   (optional) Number of microseconds until timeout is reached.
   * @param bool $receive
   *   (optional) When TRUE, the receive timeout is assigned.
   *   When FALSE, the send timeout is assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: socket_set_option()
   */
  public function set_socket_timeout($seconds, $microseconds = 0, $receive = TRUE) {
    if (!is_null($seconds) && (!is_int($seconds) || $seconds < 0)) {
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

    if (is_null($seconds)) {
      $this->socket_timeout = NULL;
      return new c_base_return_true();
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
   * @return c_base_return_int|c_base_return_null
   *   The unix timestamp for the max timeout or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: socket_get_option()
   */
  public function get_socket_timeout() {
    if (is_null($this->socket_timeout)) {
      return new c_base_return_null();
    }

    return c_base_return_array::s_new($this->socket_timeout);
  }

  /**
   * This returns the error code reported by the socket itself.
   *
   * Use self::get_error() to get the error reported in the packet and not the socket.
   *
   * @return c_base_return_int
   *   Number representing the socket error or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
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
   *   FALSE with the error bit set is returned on error.
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
   *   FALSE with the error bit set is returned on error.
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
   *   FALSE with the error bit set is returned on error.
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
   *   FALSE with the error bit set is returned on error.
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
      return c_base_return_error::s_false($response->get_error());
    }

    $response = $response->get_value_exact();
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
   *   FALSE with the error bit set is returned on error.
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
   *   FALSE with the error bit set is returned on error.
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
   *   FALSE with the error bit set is returned on error.
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
      return c_base_return_error::s_false($response->get_error());
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
   *   FALSE with the error bit set is returned on error.
   *
   * @see: c_base_session::do_connect()
   */
  private function p_transfer($request) {
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

    if ($response === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'json_decode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($response);
  }
}
