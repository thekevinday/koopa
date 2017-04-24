<?php
/**
 * @file
 * Provides path handler for the user logout process.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_http_status.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a form for the user logout.
 *
 * This listens on: /s/u/logout
 */
final class c_reservation_path_form_user_logout extends c_base_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, &$html, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $html, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }


    // Wrapper
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'content-wrapper', 'content-wrapper'));


    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text('You Have Logged Out');
    $wrapper->set_tag($tag);
    unset($tag);

    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text('You have been logged out of the system.');
    $wrapper->set_tag($tag);
    unset($tag);

    $html->set_tag($wrapper);
    unset($wrapper);


    reservation_session_logout($database, $session, $settings);

    return $executed;
  }


  /**
   * Logout of the session.
   *
   * @fixme: much of this is just a carbon copy of the login form and needs to be rewritten accordingly.
   *
   * @param c_base_database &$database
   *   The database object.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   The system settings array.
   *
   * @return c_base_return_array|c_base_return_status
   *   TRUE on success.
   *   An array of problems on failure.
   */
  private function p_do_logout(&$database, &$session, $settings) {
    // @fixme: below is a copy and paste of the login form, it needs to be replaced with the logout code!
    $problems = array();
    if (empty($_POST['login_form-username'])) {
      $problems[] = c_base_form_problem::s_create_error('login_form-username', 'No valid username has been supplied.');
    }

    if (empty($_POST['login_form-password'])) {
      $problems[] = c_base_form_problem::s_create_error('login_form-password', 'No valid password has been supplied.');
    }

    // explicitly deny access to internal user accounts
    if ($_POST['login_form-username'] == 'u_reservation_public') {
      $problems[] = c_base_form_problem::s_create_error('login_form-username', 'Unable to login, an incorrect user name or password has been specified.');
    }

    // return current list of problems before continuing to login attempt with credentials.
    if (!empty($problems)) {
      return c_base_return_array::s_new($problems);
    }

    $session->set_name($_POST['login_form-username']);
    $session->set_password($_POST['login_form-password']);

    // the database string must be rebuilt using the new username and password.
    reservation_database_string($database, $settings, $_POST['login_form-username'], $_POST['login_form-password']);

    $access_denied = FALSE;
    $error_messages = array();
    $connected = reservation_database_connect($database);
    if (c_base_return::s_has_error($connected)) {
      // try to determine what the warning is.
      // this is not very accurate/efficient, but scanning the string appears to be the only way to identify the error.
      $errors = $connected->get_error();

      // @todo: walk through all errors instead of just checking the first.
      $error = reset($errors);
      unset($errors);

      $details = $error->get_details();
      unset($error);

      if (isset($details['arguments'][':failure_reasons'][0]['message'])) {
        // in the case where the database cannot be connected to, do not attempt to ensure user account.
        if (preg_match('/could not connect to server: connection refused/i', $details['arguments'][':failure_reasons'][0]['message']) > 0) {
          // @todo: separate messages for admin users and public users.
          #foreach ($details['arguments'][':failure_reasons'] as $error_message) {
          #  $error_messages[] = $error_message;
          #}
          #unset($error_message);
          unset($details);

          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login, cannot connect to the database.');
          return c_base_return_array::s_new($problems);
        }
        elseif (preg_match('/no pg_hba\.conf entry for host/i', $details['arguments'][':failure_reasons'][0]['message']) > 0) {
          // the account either does note exist or is not authorized.
          // it is a pity that postgresql doesn't differentiate the two.
          $access_denied = TRUE;
        }
        else {
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login, reason: ' . $details['arguments'][':failure_reasons'][0]['message'] . '.');
          unset($details);

          return c_base_return_array::s_new($problems);
        }
      }
      unset($details);

      if ($access_denied) {
        // it is possible the user name might not exist, so try to auto-create the username if the username does not exist.
        $ensure_result = reservation_ensure_user_account($settings, $_POST['login_form-username']);
        if ($ensure_result instanceof c_base_return_int) {
          $ensure_result = $ensure_result->get_value_exact();

          $connected = new c_base_return_false();
          if ($ensure_result === 0) {
            // try again now that the system has attempted to ensure the user account exists.
            $connected = reservation_database_connect($database);
            if ($connected instanceof c_base_return_true) {
              // @todo: add log entry.
              #set_log_user($database, 'create_user');
            }
          }
          elseif ($ensure_result === 1) {
            // invalid user name, bad characters, or name too long.
          }
          elseif ($ensure_result === 2) {
            // failed to connect to the ldap server and could not query the ldap name.
          }
          elseif ($ensure_result === 3) {
            // user name not found in ldap database.
          }
          elseif ($ensure_result === 4) {
            //    4 = failed to connect to the database.
          }
          elseif ($ensure_result === 5) {
            //    5 = error returned while executing the SQL command.
          }
          elseif ($ensure_result === 6) {
            //    6 = error occured while reading input from the user (such as via recv()).
          }
          elseif ($ensure_result === 7) {
            //    7 = error occured while writing input from the user (such as via send()).
          }
          elseif ($ensure_result === 8) {
            //    8 = the received packet is invalid, such as wrong length.
          }
          elseif ($ensure_result === 9) {
            //   10 = connection timed out when reading or writing.
          }
          elseif ($ensure_result === 10) {
            //   10 = the connection is being forced closed.
          }
          elseif ($ensure_result === 11) {
            //   11 = the connection is closing because the service is quitting.
          }
        }
        unset($ensure_result);
      }
    }

    if (c_base_return::s_has_error($connected) || $connected instanceof c_base_return_false) {
      // @todo: rewrite this function to handle multiple errors.
      if ($access_denied) {
        $problems[] = c_base_form_problem::s_create_error('login_form-username', 'Unable to login, an incorrect user or password has been specified.');
      }
      else {
        $errors = $connected->get_error();

        $error = reset($errors);
        unset($errors);

        $details = $error->get_details();
        unset($error);

        // @todo: not just database errors, but also session create errors need to be checked.
        if (isset($details['arguments'][':failure_reasons'][0]['message']) && is_string($details['arguments'][':failure_reasons'][0]['message'])) {
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login, ' . $details['arguments'][':failure_reasons'][0]['message']);
        }
        else {
          // here the reason for failure is unknown.
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login,');
        }
        unset($details);
      }

      unset($access_denied);
      unset($connected);

      if (empty($problems)) {
        unset($problems);
        return new c_base_return_false();
      }

      return c_base_return_array::s_new($problems);
    }
    unset($access_denied);

    // @todo: add log entry.
    #set_log_user($database, 'login');

    // @todo: load and store custom settings (loaded from the database and/or ldap).
    #$session->set_settings($user_data);

    // the session needs to be opened and the data needs to be saved on successful login.
    $result = $session->do_connect();
    if (c_base_return::s_has_error($result)) {
      $socket_error = $session->get_error_socket();
      if ($socket_error instanceof c_base_return_int) {
        $problems[] = c_base_form_problem::s_create_error(NULL, 'Failed to load session, due to socket error (' . $socket_error->get_value_exact() . '): ' . @socket_strerror($socket_error->get_value_exact()) . '.');
      }
      else {
        $problems[] = c_base_form_problem::s_create_error(NULL, 'Failed to load session.');
      }
      unset($socket_error);
    }
    else {
      $ldap = reservation_database_load_ldap_data($settings, $_POST['login_form-username'])->get_value();
      if ($ldap instanceof c_base_return_false || !is_array($ldap)) {
        $ldap = array(
          'data' => NULL,
        );
      }

      if (isset($ldap['status']) && $ldap['status'] instanceof c_base_return_false) {
        $problems[] = c_base_form_problem::s_create_error('login_form-username', 'Failed to retrieve ldap information for specified user.');

        // @todo: handle error situation.
      }

      $user_data = reservation_database_get_user_data($database, $_POST['login_form-username'], $ldap['data'])->get_value();

      // @todo: get and use user id from $user_data.

      $pushed = $session->do_push($settings['session_expire'], $settings['session_max']);
      $session->do_disconnect();

      $cookie_login = NULL;
      if (c_base_return::s_has_error($pushed)) {
        $socket_error = $session->get_error_socket();
        if ($socket_error instanceof c_base_return_int) {
          $problems = c_base_form_problem::s_create_error(NULL, 'Failed to push session, due to socket error (' . $socket_error->get_value_exact() . '): ' . @socket_strerror($socket_error->get_value_exact()) . '.');
        }
        else {
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Failed to push session.');
        }
        unset($socket_error);
      }
      else {
        $session_expire = $session->get_timeout_expire()->get_value_exact();
        $cookie_login = $session->get_cookie();
      }

      if ($cookie_login instanceof c_base_cookie) {
        $cookie_login->set_expires($session_expire);
        $cookie_login->set_max_age(NULL);

        if ($pushed instanceof c_base_return_true) {
          $data = array(
            'session_id' => $session->get_session_id()->get_value_exact(),
            'expire' => gmdate("D, d-M-Y H:i:s T", $session_expire), // unnecessary, but provided for debug purposes.
          );

          $cookie_login->set_value($data);
          $session->set_cookie($cookie_login);
        }
      }
      unset($cookie_login);
      unset($session_expire);
      unset($pushed);
    }
    unset($result);
    unset($connected);

    if (empty($problems)) {
      unset($problems);
      return new c_base_return_true();
    }

    return c_base_return_array::s_new($problems);
  }
}
