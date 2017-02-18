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
   * @param array &$settings
   *   System settings.
   * @param c_base_cookie &$cookie
   *   Cookie setting.
   *
   * @param c_base_return_status|c_base_session
   *   Session information is returned on success.
   *   FALSE is returned when no session is defined.
   *   FALSE with error bit set is returned on error.
   */
  function reservation_process_sessions(&$settings, &$cookie) {
    // cookie is used to determine whether or not the user is logged in.
    $cookie->set_name($settings['cookie_name']);
    $cookie->set_path($settings['cookie_path']);
    $cookie->set_domain($settings['cookie_domain']);
    $cookie->set_secure(TRUE);

    $pulled = $cookie->do_pull();
    if ($pulled instanceof c_base_return_true) {
      $cookie_data = $cookie->get_data()->get_value_exact();

      if (!($cookie->validate() instanceof c_base_return_true) || empty($cookie_data['session_id'])) {
        // cookie failed validation or the cookie contains no session id.
        return new c_base_return_false();
      }

      $session = new c_base_session();
      $session->set_system_name($settings['session_system']);
      $session->set_session_id($cookie_data['session_id']);

      if (empty($_SERVER['REMOTE_ADDR'])) {
        $session->set_host('0.0.0.0');
      }
      else {
        $session->set_host($_SERVER['REMOTE_ADDR']);
      }

      $session_connection = $session->do_connect();
      if (c_base_return::s_has_error($session_connection)) {
        return $session_connection;
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
        $cookie->set_data($value);
        $cookie->set_expires($session_expire);
      }

      return c_base_session_return::s_new($session);
    }

    return new c_base_return_false();
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
    $socket_family = AF_INET;
    $socket_protocol = SOL_TCP;
    $socket_type = SOCK_STREAM;

    $packet_size_target = 63;
    $packet_size_client = 1;

    $socket = socket_create($socket_family, $socket_type, $socket_protocol);
    if ($socket === FALSE) {
      socket_close($socket);
      return c_base_return_error::s_false();
    }

    $connected = socket_connect($socket, $settings['database_create_account_host'], $settings['database_create_account_port']);
    if ($connected === FALSE) {
      socket_close($socket);
      return c_base_return_error::s_false();
    }

    $name_length = strlen(trim($user_name));
    $difference = $packet_size_target - $name_length;

    if ($difference > 0) {
      // the packet expects a packet to be NULL terminated or at most $packet_size_target.
      $packet = pack('a' . $name_length . 'x' . $difference, trim($user_name));
    }
    else {
      $packet = pack('a' . $name_length, $user_name);
    }

    $written = socket_write($socket, $packet, $packet_size_target);
    if ($written === FALSE) {
      socket_close($socket);
      return c_base_return_error::s_false();
    }

    $response = socket_read($socket, $packet_size_client);
    socket_close($socket);
    if (!is_string($response) || strlen($response) == 0) {
      return c_base_return_error::s_false();
    }

    // an integer is expected to be returned by the socket.
    $response_packet = unpack('C', $response);
    $response_value = (int) $response_packet[1];

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
    //   10 = connection timed out when reading or writing.
    //   11 = the connection is being forced closed.
    //   12 = the connection is closing because the service is quitting.
    return c_base_return_int::s_new($response_value);
  }
