<?php
/**
 * @file
 * Provides reservation session functions.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_session.php');
require_once('common/base/classes/base_cookie.php');

require_once('program/reservation/reservation_database.php');


/**
 * Process session information.
 *
 * @param c_base_http &$http
 *   Http object.
 * @param array &$settings
 *   System settings.
 *
 * @param c_base_session
 *   Session information is returned on success.
 *   Session information with error bit set is returned on error.
 */
function reservation_process_sessions(&$http, &$settings) {
  $cookie_login = $http->get_request(c_base_http::REQUEST_COOKIE, $settings['cookie_name']);

  $no_session = FALSE;
  if (!($cookie_login instanceof c_base_cookie)) {
    $cookie_login = new c_base_cookie();

    $no_session = TRUE;
  }

  // create a session object regardless of login session cookie.
  $session = new c_base_session();
  $session->set_socket_directory($settings['session_socket']);
  $session->set_system_name($settings['session_system']);

  // the requester should not have any control over specifying/changing these settings, so overwrite whatever is defined by the request cookie.
  $cookie_login->set_name($settings['cookie_name']);
  $cookie_login->set_path($settings['cookie_path']);
  $cookie_login->set_domain($settings['cookie_domain']);
  $cookie_login->set_http_only($settings['cookie_http_only']);
  $cookie_login->set_host_only($settings['cookie_host_only']);
  $cookie_login->set_same_site($settings['cookie_same_site']);
  $cookie_login->set_secure(TRUE);

  if (empty($_SERVER['REMOTE_ADDR'])) {
    $session->set_host('0.0.0.0');
  }
  else {
    $session->set_host($_SERVER['REMOTE_ADDR']);
  }

  // no session cookie has been defined, so there is no existing session to load.
  if ($no_session) {
    $session->set_cookie($cookie_login);
    unset($cookie_login);
    unset($no_session);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':session_name' => $settings['cookie_name'], ':function_name' => __FUNCTION__)), i_base_error_messages::NO_SESSION);
    $session->set_error($error);
    unset($error);

    return $session;
  }
  unset($no_session);

  $cookie_data = $cookie_login->get_value_exact();
  if (!($cookie_login->validate() instanceof c_base_return_true) || empty($cookie_data['session_id'])) {
    $session->set_cookie($cookie_login);
    unset($cookie_login);

    // cookie_login failed validation or the cookie contains no session id.
    $error = c_base_error::s_log(NULL, array('arguments' => array(':session_name' => $settings['cookie_name'], ':function_name' => __FUNCTION__)), i_base_error_messages::SESSION_INVALID);
    $session->set_error($error);
    unset($error);

    return $session;
  }

  $session->set_session_id($cookie_data['session_id']);


  // connect to the session using the given session id.
  $session_connection = $session->do_connect();
  if (c_base_return::s_has_error($session_connection)) {
    $session->set_cookie($cookie_login);
    unset($cookie_login);

    $session->set_error($session_connection->get_error());
    unset($error);

    return $session;
  }

  $result = $session->do_pull();
  $session->do_disconnect();

  if ($result instanceof c_base_return_true) {
    $user_name = $session->get_name()->get_value();
    $password = $session->get_password()->get_value();

    if (is_string($user_name) && is_string($password)) {
      $settings['database_user'] = $user_name;
      $settings['database_password'] = $password;
    }
  }


  // check to see if the session timeout has been extended and if so, then update the cookie.
  $session_expire = $session->get_timeout_expire()->get_value_exact();
  $session_seconds = $session_expire - time();
  if ($session_seconds == 0) {
    $session_seconds = -1;
  }

  if ($session_expire > $cookie_data['expire']) {
    $cookie_data['expire'] = gmdate("D, d-M-Y H:i:s T", $session_expire);
    $cookie_login->set_value($cookie_data);
    $cookie_login->set_expires($session_expire);
  }

  $session->set_cookie($cookie_login);
  unset($cookie_login);

  return $session;
}

/**
 * Attempt to auto-create a postgresql account.
 *
 * @param array $settings
 *   System settings.
 * @param string $username
 *   The name of the postgresql account to auto-create.
 *
 * @return c_base_return_int|c_base_return_status
 *   An integer is returned, whose codes represent the transaction result.
 *   FALSE with error bit set is returned on error.
 */
function reservation_ensure_user_account($settings, $user_name) {
  if (!is_array($settings)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_string($user_name)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'user_name', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  if (!is_resource($socket)) {
    unset($socket);

    $socket_error = @socket_last_error();
    @socket_clear_error();

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_create', ':socket_error' => $socket_error, ':socket_error_message' => @socket_strerror($socket_error), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
    unset($socket_error);

    return c_base_return_error::s_false($error);
  }

  $connected = @socket_connect($socket, $settings['database_create_account_host'], $settings['database_create_account_port']);
  if ($connected === FALSE) {
    $socket_error = @socket_last_error($socket);

    @socket_close($socket);
    @socket_clear_error();

    unset($socket);
    unset($connected);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_connect', ':socket_error' => $socket_error, ':socket_error_message' => @socket_strerror($socket_error), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
    unset($socket_error);

    return c_base_return_error::s_false($error);
  }

  $packet_size_target = 63;
  $packet_size_client = 1;

  $name_length = strlen(trim($user_name));
  $difference = $packet_size_target - $name_length;

  if ($difference > 0) {
    // the packet expects a packet to be NULL terminated or at most $packet_size_target.
    $packet = pack('a' . $name_length . 'x' . $difference, trim($user_name));
  }
  else {
    $packet = pack('a' . $name_length, $user_name);
  }

  $written = @socket_write($socket, $packet, $packet_size_target);

  unset($packet);
  unset($packet_size_target);
  unset($name_length);
  unset($difference);

  if ($written === FALSE) {
    unset($written);
    unset($packet_size_client);

    $socket_error = @socket_last_error($socket);

    @socket_close($socket);
    @socket_clear_error();

    unset($socket);
    unset($connected);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_write', ':socket_error' => $socket_error, ':socket_error_message' => @socket_strerror($this->socket_error), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
    unset($socket_error);

    return c_base_return_error::s_false($error);
  }
  unset($written);

  $response = @socket_read($socket, $packet_size_client);
  if ($response === FALSE) {
    unset($response);
    unset($packet_size_client);

    $socket_error = @socket_last_error($socket);

    @socket_close($socket);
    @socket_clear_error();

    unset($socket);
    unset($connected);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_read', ':socket_error' => $socket_error, ':socket_error_message' => @socket_strerror($this->socket_error), ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
    unset($socket_error);

    return c_base_return_error::s_false($error);
  }

  @socket_close($socket);
  unset($socket);
  unset($packet_size_client);

  if (!is_string($response) || strlen($response) == 0) {
    unset($response);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'socket_read', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  // an integer is expected to be returned by the socket.
  $response_packet = unpack('C', $response);
  $response_value = (int) $response_packet[1];

  unset($response);

  // response codes as defined in the c source file:
  //    0 = no problems detected.
  //    1 = invalid user name, bad characters, or name too long.
  //    2 = failed to connect to the ldap server and could not query the ldap name.
  //    3 = user name not found in ldap database.
  //    4 = failed to connect to the database.
  //    5 = error returned while executing the SQL command.
  //    6 = error occured while reading input from the user (such as via recv()).
  //    7 = error occured while writing input from the user (such as via send()).
  //    8 = the received packet is invalid, such as wrong length.
  //    9 = connection timed out when reading or writing.
  //   10 = the connection is being forced closed.
  //   11 = the connection is closing because the service is quitting.
  return c_base_return_int::s_new($response_value);
}
