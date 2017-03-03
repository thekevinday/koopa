<?php
/**
 * @file
 * Provides reservation session functions.
 */
  require_once('common/base/classes/base_error.php');
  require_once('common/base/classes/base_return.php');
  require_once('common/base/classes/base_markup.php');
  require_once('common/base/classes/base_html.php');
  require_once('common/base/classes/base_charset.php');
  require_once('common/base/classes/base_form.php');

  require_once('program/reservation/reservation_database.php');
  require_once('program/reservation/reservation_session.php');

/**
 * Build the login page.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param array $settings
 *   The system settings array.
 * @param c_base_session &$session
 *   The current session.
 */
function reservation_build_login_page(&$html, $settings, $session) {
  $problem_fields = array();
  $problem_messages = array();

  // @fixme: create a form problems array in session and use that.
  $problems = $session->get_problems();
  if (is_array($problems)) {
    foreach ($problems as $problem) {
      if (!empty($problem['fields']) && is_array($problem['fields'])) {
        foreach ($problem['fields'] as $problem_field) {
          $problem_fields[$problem_field] = $problem_field;
        }
        unset($problem_field);
      }

      if (!empty($problem['messages']) && is_string($problem['messages'])) {
        $problem_messages[] = $problem['messages'];
      }
    }
    unset($problem);
  }
  unset($problems);

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

  // login form
  $form = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_FORM, 'login_form', array('login_form'));
  $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_METHOD, 'post');
  $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ROLE, 'form');
  $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET, c_base_charset::UTF_8);


  // H1
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
  $tag->set_text('Login to System');
  $form->set_tag($tag);
  unset($tag);


  // hidden form data
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_HIDDEN, 'form_id', array('login_form-id'));
  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, 'login_form');
  $form->set_tag($tag);
  unset($tag);


  // label: username
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-username'));
  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_FOR, 'login_form-username');
  $tag->set_text('Username');
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
  $form->set_tag($tag);
  unset($tag);


  // label: password
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-password'));
  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_FOR, 'login_form-password');
  $tag->set_text('Password');
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
  $form->set_tag($tag);
  unset($tag);
  unset($problem_fields);

  $html->set_tag($form);
  unset($form);
}

/**
 * Validate the login form.
 *
 * @param c_base_database &$database
 *   The database object.
 * @param array &$settings
 *   The system settings array.
 * @param c_base_session &$session
 *   The current session.
 *
 * @return c_base_return_array|c_base_return_status
 *   FALSE on success.
 *   An array of problems on failure.
 *   FALSE with error bit set is returned on error.
 */
function reservation_attempt_login(&$database, &$settings, &$session) {
  $problems = array();
  if (empty($_POST['login_form-username'])) {
    $problem = new c_base_form_problem();
    $problem->set_field('login_form-username');
    $problem->set_value('No valid username has been supplied.');

    $problems[] = $problem;
    unset($problem);
  }

  if (empty($_POST['login_form-password'])) {
    $problem = new c_base_form_problem();
    $problem->set_field('login_form-password');
    $problem->set_value('No valid password has been supplied.');

    $problems[] = $problem;
    unset($problem);
  }

  // explicitly deny access to internal user accounts
  if ($_POST['login_form-username'] == 'public_user') {
    $problem = new c_base_form_problem();
    $problem->set_field('login_form-username');
    $problem->set_value('Unable to login, an incorrect user name or password has been specified.');

    $problems[] = $problem;
    unset($problem);
  }

  // return current list of problems before continuing to login attempt with credentials.
  if (!empty($problems)) {
    return c_base_return_array::s_new($problems);
  }

  $session->set_name($_POST['login_form-username']);
  $session->set_password($_POST['login_form-password']);
  $settings['database_user'] = $_POST['login_form-username'];
  $settings['database_password'] = $_POST['login_form-password'];

  // the database string must be rebuilt using the new username and password.
  reservation_database_string($database, $settings);

  $connected = reservation_database_connect($database);
  if (!($connected instanceof c_base_return_true)) {
    // it is possible the user name might not exist, so try to auto-create the username if the username does not exist.
    $ensure_result = reservation_ensure_user_account($settings, $_POST['login_form-username']);
    // @todo: process the $ensure_result return codes.

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
      else {
        // @todo: possibly handle errors resulting from not being able to auto-create account.
      }
    }
    unset($ensure_result);
  }

  if ($connected instanceof c_base_return_false) {
    $problem = new c_base_form_problem();
    $problem->set_field('login_form-username');
    $problem->set_value('Unable to login, an incorrect user or password has been specified.');

    $problems[] = $problem;
    unset($problem);
  }
  else {
    // @todo: add log entry.
    #set_log_user($database, 'login');

    // @todo: load and store custom settings (loaded from the database and/or ldap).
    #$session->set_id_user($user_id);
    #$session->set_settings($user_data);

    // the session needs to be opened and the data needs to be saved on successful login.
    $result = $session->do_connect();
    if (c_base_return::s_has_error($result)) {
      // @todo: process each error message.
      $problem = new c_base_form_problem();

      $socket_error = $session->get_socket_error()->get_value();
      if ($socket_error instanceof c_base_return_int) {
        $problem->set_value('Failed to load session, due to socket error (' . $socket_error . '): ' . @socket_strerror($socket_error) . '.');
      }
      else {
        $problem->set_value('Failed to load session.');
      }
      unset($socket_error);

      $problems[] = $problem;
      unset($problem);
    }
    else {
      $ldap = reservation_database_load_ldap_data($settings, $_POST['login_form-username'])->get_value();
      if ($ldap instanceof c_base_return_false || !is_array($ldap)) {
        $ldap = array(
          'data' => NULL,
        );
      }

      if (isset($ldap['status']) && $ldap['status'] instanceof c_base_return_false) {
        // @todo: handle error situation.
        $problem = new c_base_form_problem();
        $problem->set_field('login_form-username');
        $problem->set_value('Failed to retrieve ldap information for specified user.');

        $problems[] = $problem;
        unset($problem);
      }

      $user_data = reservation_database_get_user_data($database, $_POST['login_form-username'], $ldap['data'])->get_value();

      if (is_array($user_data) && isset($user_data['id_user'])) {
        $session->set_id_user($user_data['id_user']);
      }

      // @todo: get and use user id from $user_data.

      $pushed = $session->do_push($settings['session_expire'], $settings['session_max']);
      $session->do_disconnect();

      $session_expire = $session->get_timeout_expire()->get_value_exact();
      $cookie_login = $session->get_cookie();

      if (c_base_return::s_has_error($pushed)) {
        $problem = new c_base_form_problem();

        $socket_error = $session->get_socket_error()->get_value();
        if ($socket_error instanceof c_base_return_int) {
          $problem->set_value('Failed to push session, due to socket error (' . $socket_error . '): ' . @socket_strerror($socket_error) . '.');
        }
        else {
          $problem->set_value('Failed to push session.');
        }
        unset($socket_error);

        $problems[] = $problem;
        unset($problem);
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

  }
  unset($connected);

  if (empty($problems)) {
    unset($problems);
    return new c_base_return_false();
  }

  return c_base_return_array::s_new($problems);
}

/**
 * Build the HTTPS requirement page.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param array $settings
 *   The system settings array.
   * @param c_base_session &$session
   *   The current session.
 */
function reservation_build_page_require_https(&$html, $settings, &$session) {
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
  $tag->set_text('HTTPS Connection is Required');
  $html->set_tag($tag);
  unset($tag);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
  $tag->set_text('Please use a secure connection to access this website.');
  $html->set_tag($tag);
  unset($tag);
}

/**
 * Build the dashboard page.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param array $settings
 *   The system settings array.
 * @param c_base_session &$session
 *   The current session.
 */
function reservation_build_page_dashboard(&$html, $settings, &$session) {
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
  $tag->set_text('Dashboard');
  $html->set_tag($tag);
  unset($tag);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
  $tag->set_text('All links will go here.');
  $html->set_tag($tag);
  unset($tag);

  $roles = array();
  $roles_object = $session->get_setting('roles');
  if ($roles_object instanceof c_base_roles) {
    $roles = $roles_object->get_roles()->get_value_exact();
  }
  unset($roles_object);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
  $tag->set_text('You are currently logged in as: ' . $settings['database_user']);
  $html->set_tag($tag);
  unset($tag);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
  $tag->set_text('You are currently assigned the following roles:');
  $html->set_tag($tag);
  unset($tag);

  $tag_ul = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_UNORDERED_LIST);

  foreach ($roles as $role) {
    $tag_li = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LIST_ITEM);

    switch ($role) {
      case c_base_roles::PUBLIC:
        $tag_li->set_text('Public');
        break;
      case c_base_roles::USER:
        $tag_li->set_text('User');
        break;
      case c_base_roles::REQUESTER:
        $tag_li->set_text('Requester');
        break;
      case c_base_roles::DRAFTER:
        $tag_li->set_text('Drafter');
        break;
      case c_base_roles::EDITOR:
        $tag_li->set_text('Editor');
        break;
      case c_base_roles::REVIEWER:
        $tag_li->set_text('Reviewer');
        break;
      case c_base_roles::FINANCER:
        $tag_li->set_text('Financer');
        break;
      case c_base_roles::INSURER:
        $tag_li->set_text('Insurer');
        break;
      case c_base_roles::PUBLISHER:
        $tag_li->set_text('Publisher');
        break;
      case c_base_roles::AUDITOR:
        $tag_li->set_text('Auditor');
        break;
      case c_base_roles::MANAGER:
        $tag_li->set_text('Manager');
        break;
      case c_base_roles::ADMINISTER:
        $tag_li->set_text('Administer');
        break;
    }

    $tag_ul->set_tag($tag_li);
    unset($tag_li);
  }
  unset($role);

  $html->set_tag($tag_ul);
}

/**
 * Process and build requested forms.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param array $settings
 *   The system settings array.
 * @param c_base_session &$session
 *   The current session.
 */
function reservation_process_forms(&$html, $settings, &$session) {
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
  $tag->set_text('Form Processing');
  $html->set_tag($tag);
  unset($tag);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
  $tag->set_text('This function is called to process specific forms.');
  $html->set_tag($tag);
  unset($tag);
}

/**
 * Process request path and determine what to do.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param array $settings
 *   The system settings array.
 * @param c_base_session &$session
 *   The current session.
 */
function reservation_process_path(&$html, $settings, &$session) {
  reservation_build_page_dashboard($html, $settings, $session);
}

/**
 * Process request path for public users and determine what to do.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param array $settings
 *   The system settings array.
 * @param c_base_session &$session
 *   The current session.
 */
function reservation_process_path_public(&$html, $settings, &$session) {
  reservation_build_login_page($html, $settings, $session);
}
