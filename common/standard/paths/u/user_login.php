<?php
/**
 * @file
 * Provides path handler for the login process.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_ldap.php');
require_once('common/base/classes/base_log.php');

require_once('common/standard/classes/standard_index.php');
require_once('common/standard/classes/standard_path.php');
require_once('common/standard/classes/standard_paths.php');
require_once('common/standard/classes/standard_users.php');

require_once('common/theme/classes/theme_html.php');


/**
 * Provides a form for the user login.
 *
 * This listens on: /u/login
 */
class c_standard_path_user_login extends c_standard_path {
  public const SESSION_DATE_FORMAT = 'D, d-M-Y H:i:s T';
  public const PATH_SELF           = 'u/login';

  protected const USER_PUBLIC = 'u_standard_public';

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
    $this->pr_create_html();

    $logged_in = $session->is_logged_in()->get_value_exact();
    if ($logged_in) {
      $method = $this->pr_get_method($http);

      if ($method == c_base_http::HTTP_METHOD_POST) {
        unset($method);


        // forbid POST request on login pages for already logged in users.
        $this->http->set_response_status(c_base_http_status::FORBIDDEN);


        // Content
        $wrapper = $this->pr_create_tag_section(array(1 => 8));
        $wrapper->set_tag($this->pr_create_tag_text_block(9));
      }
      else {
        unset($method);


        // Content
        $wrapper = $this->pr_create_tag_section(array(1 => 3));
        $wrapper->set_tag($this->pr_create_tag_text_block(4, array('@{user}' => $session->get_name()->get_value_exact())));

        $wrapper->set_tag($this->pr_create_tag_break());

        $wrapper->set_tag($this->pr_create_tag_text_block(9));

        $block = $this->pr_create_tag_text_block(NULL);
        $block->set_tag($this->pr_create_tag_text(5));

        $href = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_A);
        $href->set_text($this->pr_get_text(6));
        $href->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $settings['base_path'] . c_standard_paths::URI_USER_LOGOUT);
        $block->set_tag($href);
        unset($href);

        $block->set_tag(c_theme_html::s_create_tag_text(c_base_markup_tag::TYPE_SPAN, $this->pr_get_text(7)));
        $wrapper->set_tag($block);
        unset($block);
      }

      $this->html->set_tag($wrapper);
      unset($wrapper);

      $this->pr_add_menus();

      $executed->set_output($this->html);
      unset($this->html);

      return $executed;
    }
    else {
      $wrapper = $this->pr_create_tag_section(array(1 => 0));
    }


    // handle any resulting errors.
    $problem_fields = array();
    $problem_messages = array();

    // perform login if session information is provided.
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
      $login_result = $this->pr_do_login($http, $database, $session, $settings);

      if (is_array($login_result)) {
        foreach ($login_result as $problem) {
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
      }
      elseif ($login_result === TRUE) {
        // successfully logged in.
        $destination = $this->pr_do_login_redirect();

        // note: by using a SEE OTHER redirect, the client knows to make a GET request and that the redirect is temporary.
        $redirect = c_standard_path::s_create_redirect($destination, c_base_http_status::SEE_OTHER, FALSE);
        unset($destination);

        return $redirect->do_execute($http, $database, $session, $settings);
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

        $this->html->set_tag($messages);
        unset($messages);
      }
      unset($problem_messages);
    }
    else {
      $form_defaults = array();
    }
    unset($logged_in);

    // login form
    $form = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_FORM, 'login_form', array('login_form'));
    $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_METHOD, 'post');
    $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ROLE, 'form');
    $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET, c_base_charset::UTF_8);


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
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-user_name'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_FOR, 'login_form-user_name');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'field-label-user_name');
    $tag->set_text($this->pr_get_text(1));
    $form->set_tag($tag);
    unset($tag);


    // field: username
    $class = array(
      'login_form-input-user_name',
    );
    if (array_key_exists('login_form-user_name', $problem_fields)) {
      $class[] = 'field_has_problem';
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TEXT, 'login_form-user_name', $class);
    unset($class);

    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REQUIRED, TRUE);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, $this->pr_sanitize('login_form-user_name', 0)->get_value());
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'field-input-user_name');
    $form->set_tag($tag);
    unset($tag);


    // label: password
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-password'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_FOR, 'login_form-password');
    $tag->set_text($this->pr_get_text(2));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'field-label-password');
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
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'field-input-password');
    $form->set_tag($tag);
    unset($tag);


    // button: reset
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_RESET, 'login_form-reset', array('login_form-button-reset'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, $this->pr_get_text(11));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'field-button-reset');
    $form->set_tag($tag);
    unset($tag);


    // button: submit
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SUBMIT, 'login_form-login', array('login_form-button-login'));
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, $this->pr_get_text(12));
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_ACTION, $settings['base_path'] . 's/u/login'); // custom submit destination, but would require /s/u/login to redirect back to here.
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'field-button-submit');
    $form->set_tag($tag);
    unset($tag);
    unset($problem_fields);


    // Wrapper
    $wrapper->set_tag($form);
    unset($form);


    // assing the content.
    $this->html->set_tag($wrapper);
    unset($wrapper);

    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);

    return $executed;
  }

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    $result = parent::pr_build_breadcrumbs();
    if ($result instanceof c_base_return_false) {
      unset($result);
      return new c_base_return_false();
    }
    unset($result);

    // @todo: using $this->path_tree->get_item_end(), get the current path and render the breadcrumb based on that path instead of this path.
    //        consider adding this with additional customization, because it may be desired for some paths to remain hidden until logged in.

    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $this->breadcrumbs = new c_base_menu_item();
    }

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text(12), self::PATH_SELF);
    $this->breadcrumbs->set_item($item);
    unset($item);

    return new c_base_return_true();
  }

  /**
   * Validate and Perform the login.
   *
   * @param c_base_http &$http
   *   The HTTP settings.
   * @param c_base_database &$database
   *   The database object.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   The system settings array.
   *
   * @return array|bool
   *   TRUE on success.
   *   An array of problems on failure.
   */
  protected function pr_do_login(&$http, &$database, &$session, $settings) {
    $problems = array();
    if (empty($_POST['login_form-user_name']) || !is_string($_POST['login_form-user_name'])) {
      $problems[] = c_base_form_problem::s_create_error('login_form-user_name', self::pr_get_text(10));
    }
    elseif ($_POST['login_form-user_name'] == static::USER_PUBLIC) {
      // explicitly deny access to internal user accounts
      $problems[] = c_base_form_problem::s_create_error('login_form-user_name', self::pr_get_text(10));
    }
    elseif (empty($_POST['login_form-password']) || !is_string($_POST['login_form-user_name'])) {
      $problems[] = c_base_form_problem::s_create_error('login_form-password', self::pr_get_text(10));
    }

    // return current list of problems before continuing to login attempt with credentials.
    if (!empty($problems)) {
      return $problems;
    }

    // disconnect from the database and assign a new database string.
    if ($database->is_connected() instanceof c_base_return_true) {
      $database->do_disconnect();
    }

    $connection_string = $database->get_connection_string();
    $connection_string->set_user($_POST['login_form-user_name']);
    $connection_string->set_password($_POST['login_form-password']);

    $database->set_connection_string($connection_string);
    unset($connection_string);


    $access_denied = FALSE;
    $error_messages = array();

    $connected = $database->do_connect();
    if (c_base_return::s_has_error($connected)) {
      // try to determine what the warning is.
      // this is not very accurate/efficient, but scanning the string appears to be the only way to identify the error.
      $errors = $connected->get_error();

      // @todo: walk through all errors instead of just getting only the first.
      $error = reset($errors);
      unset($errors);

      $details = $error->get_details();
      unset($error);

      if (isset($details['arguments'][':{failure_reasons}'][0]['message'])) {
        // in the case where the database cannot be connected to, do not attempt to ensure user account.
        if (preg_match('/could not connect to server: connection refused/i', $details['arguments'][':{failure_reasons}'][0]['message']) > 0) {
          // @todo: separate messages for admin users and public users.
          #foreach ($details['arguments'][':{failure_reasons}'] as $error_message) {
          #  $error_messages[] = $error_message;
          #}
          #unset($error_message);
          unset($details);

          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login, cannot connect to the database.');
          return $problems;
        }
        elseif (preg_match('/no pg_hba\.conf entry for host/i', $details['arguments'][':{failure_reasons}'][0]['message']) > 0) {
          // the account either does note exist or is not authorized.
          // it is a pity that postgresql doesn't differentiate the two.
          $access_denied = TRUE;
        }
        elseif (preg_match('/password authentication failed for user /i', $details['arguments'][':{failure_reasons}'][0]['message']) > 0) {
          $access_denied = TRUE;
        }
        else {
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login, reason: ' . $details['arguments'][':{failure_reasons}'][0]['message'] . '.');
          unset($details);

          return $problems;
        }
      }
      unset($details);

      if ($access_denied) {
        // it is possible the user name might not exist, so try to auto-create the username if the username does not exist.
        $ensure_result = $this->pr_do_ensure_user_account($settings, $_POST['login_form-user_name']);
        if ($ensure_result instanceof c_base_return_int) {
          $ensure_result = $ensure_result->get_value_exact();

          $connected = new c_base_return_false();
          if ($ensure_result === 0) {
            // try again now that the system has attempted to ensure the user account exists.
            $connected = $database->do_connect();
            if ($connected instanceof c_base_return_true) {
              c_standard_index::s_do_initialize_database($database);

              if ($database instanceof c_standard_database) {
                $database->do_log_user(c_base_log::TYPE_CREATE, c_base_http_status::OK, array('user_name' => $_POST['login_form-user_name']));
              }
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
            // failed to connect to the database.
          }
          elseif ($ensure_result === 5) {
            // error returned while executing the SQL command.
          }
          elseif ($ensure_result === 6) {
            // error occured while reading input from the user (such as via recv()).
          }
          elseif ($ensure_result === 7) {
            // error occured while writing input from the user (such as via send()).
          }
          elseif ($ensure_result === 8) {
            // the received packet is invalid, such as wrong length.
          }
          elseif ($ensure_result === 9) {
            // connection timed out when reading or writing.
          }
          elseif ($ensure_result === 10) {
            // the connection is being forced closed.
          }
          elseif ($ensure_result === 11) {
            // the connection is closing because the service is quitting.
          }
        }
        unset($ensure_result);

        // report login attempt and failure using public user account.
        if ($connected instanceof c_base_return_false && isset($settings['database_user_public']) && is_string($settings['database_user_public'])) {
          $connection_string = $database->get_connection_string();
          $connection_string->set_user($settings['database_user_public']);
          $connection_string->set_password(NULL);

          $database->set_connection_string($connection_string);
          unset($connection_string);

          $connected = $database->do_connect();
          if ($connected instanceof c_base_return_true) {
            c_standard_index::s_do_initialize_database($database);

            $result = $database->do_log_user(c_base_log::TYPE_CONNECT, c_base_http_status::FORBIDDEN, array('user_name' => $_POST['login_form-user_name']));
            $database->do_disconnect();

            $connected = new c_base_return_false();
          }
        }
      }
    }
    else {
      c_standard_index::s_do_initialize_database($database);

      // if LDAP is available, make sure the account information exists.
      $ldap = $this->pr_load_ldap_data($settings, $_POST['login_form-user_name']);

      if ($ldap['status']) {
        $this->pr_update_user_data($database, $ldap);
      }
      else {
        $this->pr_update_user_data($database);
      }
      unset($ldap);
    }

    if (c_base_return::s_has_error($connected) || $connected instanceof c_base_return_false) {
      // @todo: rewrite this to handle multiple errors.
      if ($access_denied) {
        $problems[] = c_base_form_problem::s_create_error('login_form-user_name', self::pr_get_text(10));
      }
      else {
        $errors = $connected->get_error();

        $error = reset($errors);
        unset($errors);

        $details = $error->get_details();
        unset($error);

        // @todo: not just database errors, but also session create errors need to be checked.
        if (isset($details['arguments'][':{failure_reasons}'][0]['message']) && is_string($details['arguments'][':{failure_reasons}'][0]['message'])) {
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login, ' . $details['arguments'][':{failure_reasons}'][0]['message']);
        }
        else {
          // here the reason for failure is unknown.
          $problems[] = c_base_form_problem::s_create_error(NULL, 'Unable to login.');
        }
        unset($details);
      }
      unset($access_denied);

      // connection was established but errors have occured.
      if ($connected instanceof c_base_return_true && $database instanceof c_standard_database) {
        $database->do_log_user(c_base_log::TYPE_CONNECT, c_base_http_status::FORBIDDEN);
        $database->do_disconnect();
      }
      unset($connected);

      if (empty($problems)) {
        unset($problems);
        return new c_base_return_false();
      }

      return $problems;
    }
    unset($access_denied);

    // @todo: load and store custom settings (loaded from the database and/or ldap).
    #$session->set_settings($user_data);

    $session->set_name($_POST['login_form-user_name']);
    $session->set_password($_POST['login_form-password']);

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
      $ldap = $this->pr_load_ldap_data($settings, $_POST['login_form-user_name']);
      if (!$ldap['status']) {
        $problems[] = c_base_form_problem::s_create_error('login_form-user_name', 'Failed to retrieve ldap information for specified user.');
      }

      $pushed = $session->do_push($settings['session_expire'], $settings['session_max']);
      $session->do_disconnect();

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

        $cookie_login = new c_base_cookie();
        $cookie_login->set_name($settings['cookie_name']);
        $cookie_login->set_path($settings['cookie_path']);
        $cookie_login->set_domain($settings['cookie_domain']);
        $cookie_login->set_http_only($settings['cookie_http_only']);
        $cookie_login->set_host_only($settings['cookie_host_only']);
        $cookie_login->set_same_site($settings['cookie_same_site']);
        $cookie_login->set_secure($settings['cookie_secure']);
        $cookie_login->set_expires($session_expire);
        $cookie_login->set_max_age(NULL);

        $data = array(
          'session_id' => $session->get_session_id()->get_value_exact(),
          'expire' => gmdate(static::SESSION_DATE_FORMAT, $session_expire), // unnecessary, but provided for debug purposes.
        );

        $cookie_login->set_value($data);
        $session->set_cookie($cookie_login);

        unset($cookie_login);
      }
      unset($session_expire);
      unset($pushed);
    }
    unset($result);

    // now that any session/cookie information is loaded and processed, log any login connections.
    if ($connected instanceof c_base_return_true && $database instanceof c_standard_database) {
      $database->do_log_user(c_base_log::TYPE_CONNECT, c_base_http_status::OK, array('expires' => $session->get_timeout_expire()->get_value_exact()));
    }
    unset($connected);

    if (empty($problems)) {
      unset($problems);

      $session->is_logged_in(TRUE);

      // load database session information.
      $user_current = new c_standard_users_user();
      if ($user_current->do_load($database) instanceof c_base_return_true) {
        $session->set_user_current($user_current);
      }
      unset($user_current);

      $user_session = new c_standard_users_user();
      if ($user_session->do_load($database, TRUE) instanceof c_base_return_true) {
        $session->set_user_current($user_session);
      }
      unset($user_session);

      return TRUE;
    }

    return $problems;
  }

  /**
   * Provide a path to redirect to on successful login.
   *
   * @param c_base_http &$http
   *   The HTTP settings.
   *
   * @return array
   *   A array of destination url parts is always returned.
   */
  protected function pr_do_login_redirect() {
    $request_uri = $this->http->get_request(c_base_http::REQUEST_URI)->get_value_exact();
    if (isset($request_uri['data']['path'])) {
      return $request_uri['data'];
    }
    unset($request_uri);

    return array(
      'scheme' => NULL,
      'authority' => NULL,
      'path' => $this->settings['base_path'] . c_standard_paths::URI_USER_DASHBOARD,
      'query' => NULL,
      'fragment' => NULL,
      'url' => TRUE,
      'current' => $start,
      'invalid' => FALSE,
    );
  }

  /**
   * Loads LDAP information for the given user (if available).
   *
   * @param array $settings
   *   System settings array.
   * @param string $user_name
   *   The user name to load
   *
   * @return array|bool
   *   An array of ldap data associated with the given user.
   *   The array structure is:
   *     'title': a Title for any error that occured.
   *     'message': The detailed ldap error message.
   *     'status': c_base_return_true if there were no problems and c_base_return_false if there were problems.
   *     'data': Any ldap data found for the given user name.
   */
  protected function pr_load_ldap_data($settings, $user_name) {
    $return_data = array(
      'title' => NULL,
      'message' => NULL,
      'status' => TRUE,
      'data' => NULL,
    );


    // ldap support is disabled if ldap_server is set to NULL (or is not a string).
    if (!isset($settings['ldap_server']) || !is_string($settings['ldap_server'])) {
      return $return_data;
    }


    $ldap = new c_base_ldap();
    $ldap->set_name($settings['ldap_server']);

    if (isset($settings['ldap_bind_name']) && is_string($settings['ldap_bind_name'])) {
      $ldap->set_bind_name($settings['ldap_bind_name']);
    }

    if (isset($settings['ldap_bind_password']) && is_string($settings['ldap_bind_password'])) {
      $ldap->set_bind_password($settings['ldap_bind_password']);
    }

    $connected = $ldap->do_connect();
    if (c_base_return::s_has_error($connected)) {
      $message = $ldap->get_error_message();

      $return_data['title'] = 'Connection Failed';
      $return_data['message'] = $message->get_value_exact();
      $return_data['status'] = FALSE;
      unset($message);

      return c_base_return_array::s_new($return_data);
    }

    $read = $ldap->do_search($settings['ldap_base_dn'], '(uid=' . $user_name . ')', $settings['ldap_fields']);
    if (c_base_return::s_has_error($read)) {
      $message = $ldap->get_error_message();

      $return_data['title'] = 'Search Failed';
      $return_data['message'] = $message->get_value_exact();
      $return_data['status'] = FALSE;
      unset($message);

      $ldap->do_disconnect();

      return c_base_return_array::s_new($return_data);
    }

    $entries = $read->get_entry_all();
    if ($entries instanceof c_base_return_array) {
      $entries = $entries->get_value();
    }
    else {
      $entries = array();
    }

    if ($entries['count'] > 0) {
      $return_data['data'] = array(
        'uid' => $user_name,
      );

      foreach ($settings['ldap_fields'] as $ldap_field) {
        $return_data['data'][$ldap_field] = $entries[0][$ldap_field][0];
      }
      unset($ldap_field);
    }
    else {
      $return_data['title'] = 'Username Not Found';
      $return_data['message'] = 'The user \'' . $user_name . '\' was not found.';
      $return_data['status'] = FALSE;
    }
    unset($entries);

    return $return_data;
  }

  /**
   * Attempt to auto-create a postgresql user account if it does not already exist.
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
  protected function pr_do_ensure_user_account($settings, $user_name) {
    $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (!is_resource($socket)) {
      unset($socket);

      $socket_error = @socket_last_error();
      @socket_clear_error();

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'socket_create', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
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

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'socket_connect', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
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

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'socket_write', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($this->socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
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

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'socket_read', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($this->socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::SOCKET_FAILURE);
      unset($socket_error);

      return c_base_return_error::s_false($error);
    }

    @socket_close($socket);
    unset($socket);
    unset($packet_size_client);

    if (!is_string($response) || strlen($response) == 0) {
      unset($response);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'socket_read', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
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

  /**
   * Ensure that the user data exists and is up to date.
   *
   * @param c_base_database &$database
   *   The database object.
   * @param array|null $ldap
   *   (optional) When NULL, the user data is only ensure to exist.
   *   When an array, the given ldap information is used to update the account.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  protected function pr_update_user_data(&$database, $ldap = NULL) {
    $query_result = $database->do_query('select id from v_users_self_exists');
    if ($query_result instanceof c_base_database_result) {
      if (is_array($ldap)) {
        $query_arguments = array();

        $email = explode('@', $ldap['data']['mail']);
        if (count($email) != 2) {
          $email[0] = NULL;
          $email[1] = NULL;
        }

        $query_arguments[] = isset($ldap['data']['employeenumber']) && is_numeric($ldap['data']['employeenumber']) ? (int) $ldap['data']['employeenumber'] : NULL;;
        $query_arguments[] = isset($ldap['data']['givenname']) && is_string($ldap['data']['givenname']) ? $ldap['data']['givenname'] : NULL;
        $query_arguments[] = isset($ldap['data']['sn']) && is_string($ldap['data']['sn']) ? $ldap['data']['sn'] : NULL;
        $query_arguments[] = isset($ldap['data']['gecos']) && is_string($ldap['data']['gecos']) ? $ldap['data']['gecos'] : NULL;
        $query_arguments[] = $email[0];
        $query_arguments[] = $email[1];
        unset($email);

        // if the user account does not exist, then create it.
        if ($query_result->fetch_row()->get_value() === FALSE) {
          $query_string = 'insert into v_users_self_insert (id_external, name_human.first, name_human.last, name_human.complete, address_email.name, address_email.domain, address_email.private) values ($1, $2, $3, $4, $5, $6, $7)';
          $query_arguments[] = 't';
        }
        else {
          $query_string = 'update v_users_self_update set id_external = $1, name_human.first = $2, name_human.last = $3, name_human.complete = $4, address_email.name = $5, address_email.domain = $6';
        }

        $database->do_query($query_string, $query_arguments);
        unset($query_string);
        unset($query_arguments);
      }
      else {
        if ($query_result->fetch_row()->get_value() === FALSE) {
          $database->do_query('insert into v_users_self_insert (id_external, name_human.first, name_human.last, name_human.complete, address_email.name, address_email.domain, address_email.private) values (null, null, null, null, null, null, true)');
        }
      }
    }
    unset($query_result);
  }

  /**
   * Implementation of pr_create_html_add_header_link_canonical().
   */
  protected function pr_create_html_add_header_link_canonical() {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF,  $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . c_standard_paths::URI_USER_LOGIN);
    $this->html->set_header($tag);

    unset($tag);
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return self::pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Login to System';
        break;
      case 1:
        $string = 'Username';
        break;
      case 2:
        $string = 'Password';
        break;
      case 3:
        $string = 'Logged In';
        break;
      case 4:
        $string = 'You are currently logged in to the system as @{user}.';
        break;
      case 5:
        $string = 'You may ';
        break;
      case 6:
        $string = 'logout';
        break;
      case 7:
        $string = ' at any time.';
        break;
      case 8:
        $string = 'Login Failure';
        break;
      case 9:
        $string = 'You are already logged in.';
        break;
      case 10:
        $string = 'Unable to login, an incorrect user name or password has been specified.';
        break;
      case 11:
        $string = 'Reset';
        break;
      case 12:
        $string = 'Login';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
