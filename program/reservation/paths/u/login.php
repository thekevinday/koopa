<?php
/**
 * @file
 * Provides path handler for the login process.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_http_status.php');

require_once('common/theme/classes/theme_html.php');


/**
 * Provides a form for the user login.
 *
 * This listens on: /u/login
 */
class c_reservation_path_user_login extends c_base_path {
  private const PATH_REDIRECTS = 'program/reservation/reservation_redirects.php';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    // initialize the content as HTML.
    $html = c_reservation_build::s_create_html($http, $database, $session, $settings);


    // handle any resulting errors.
    $problem_fields = array();
    $problem_messages = array();

    // perform login if session information is provided.
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
      $login_result = $this->p_do_login($database, $session, $settings);

      if ($login_result instanceof c_base_return_array) {
        $problems = $login_result->get_value_exact();

        foreach ($problems as $problem) {
          $fields = $problem->get_fields();
          if ($fields instanceof c_base_return_array) {
            foreach ($fields->get_value_exact() as $field) {
              $problem_fields[] = $field;
            }
            unset($field);
          }
          unset($fields);

          $problem_messages[] = $problem->get_value_exact();
        }
        unset($problem);
        unset($problems);
      }
      elseif ($login_result instanceof c_base_return_true) {
        // successfully logged in.
        require_once(self::PATH_REDIRECTS);

        $destination = $settings['uri'];
        $destination['path'] = $settings['base_path'] . '/u/dashboard';

        // note: by using a SEE OTHER redirect, the client knows to make a GET request and that the redirect is temporary.
        $redirect = c_reservation_path_redirect::s_create_redirect($destination, c_base_http_status::SEE_OTHER, FALSE);
        unset($destination);

        return $redirect->do_execute($http, $database, $session, $html, $settings);
      }

      unset($login_result);

      if (!empty($problem_messages)) {
        $messages = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, 'form_problems', array('form_problems'));
        foreach ($problem_messages as $problem_delta => $problem_message) {
          $class = array(
            'form_problems-problem',
            'form_problems-problem-' . $problem_delta,
          );

          $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, 'form_problems-problem-' . $problem_delta, $class);
          $tag->set_text($problem_message);
          unset($class);

          $messages->set_tag($tag);
          unset($tag);
        }
        unset($problem_message);
        unset($problem_delta);

        $html->set_tag($messages);
        unset($messages);
      }
      unset($problem_messages);
    }
    else {
      $form_defaults = array();
    }

    // login form
    $form = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_FORM, 'login_form', array('login_form'));
    $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_METHOD, 'post');
    $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ROLE, 'form');
    $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET, c_base_charset::UTF_8);


    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text($this->pr_get_text(0));
    $form->set_tag($tag);
    unset($tag);


    // form id: represents the form.
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_HIDDEN, 'form_id', array('form-id', 'login_form-id'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, 'login_form');
    $form->set_tag($tag);
    unset($tag);

    // form unique id: uniquely identifies the form.
    $unique_id = mt_rand(1, 16);
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_HIDDEN, 'form_id-unique', array('form-id_unique', 'login_form-id_unique'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, '' . $unique_id);
    $form->set_tag($tag);
    unset($tag);


    // @todo: The $unique_id should be stored in the database for the duration of the session and the submit process.
    // data should include form id, unique id, form source, and form destination and then these values will not need to be stored on the form itself where they can be modified.
    // the data should also include the user id and optionally the session id (in cases where forms are session-sensitive or if they should span across sessions).
    // revisions to each form, for dynamic changes, should also be supported.
    unset($unique_id);


    // label: username
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-username'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_FOR, 'login_form-username');
    $tag->set_text($this->pr_get_text(1));
    $form->set_tag($tag);
    unset($tag);


    // field: username
    $class = array(
      'login_form-input-username',
    );
    if (array_key_exists('login_form-username', $problem_fields)) {
      $class[] = 'field_has_problem';
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TEXT, 'login_form-username', $class);
    unset($class);

    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REQUIRED, TRUE);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, $this->pr_sanitize('login_form-username', 0)->get_value());
    $form->set_tag($tag);
    unset($tag);


    // label: password
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-password'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_FOR, 'login_form-password');
    $tag->set_text($this->pr_get_text(2));
    $form->set_tag($tag);
    unset($tag);


    // field: password
    $class = array(
      'login_form-input-password',
    );
    if (array_key_exists('login_form-password', $problem_fields)) {
      $class[] = 'field_has_problem';
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_PASSWORD, 'login_form-password', $class);
    unset($class);

    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REQUIRED, TRUE);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, $this->pr_sanitize('login_form-password', 0)->get_value());
    $form->set_tag($tag);
    unset($tag);


    // button: reset
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_RESET, 'login_form-reset', array('login_form-button-reset'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, 'Reset');
    $form->set_tag($tag);
    unset($tag);


    // button: submit
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SUBMIT, 'login_form-login', array('login_form-button-login'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, 'Login');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_ACTION, $settings['base_path'] . '/s/u/login'); // custom submit destination, but would require /s/u/login to redirect back to here.
    $form->set_tag($tag);
    unset($tag);
    unset($problem_fields);


    // Wrapper
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'content-wrapper', 'content-wrapper'));
    $wrapper->set_tag($form);
    unset($form);


    // assing the content.
    $html->set_tag($wrapper);
    unset($wrapper);

    $executed = new c_base_path_executed();
    $executed->set_output($html);
    unset($html);

    return $executed;
  }

  /**
   * Validate and Perform the login.
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
  private function p_do_login(&$database, &$session, $settings) {
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

      reservation_get_current_roles($database, $session, $settings);

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

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'Login to System';
      case 1:
        return 'Username';
      case 2:
        return 'Password';
    }

    return '';
  }
}
