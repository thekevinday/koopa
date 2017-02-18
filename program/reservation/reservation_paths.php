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

  require_once('program/reservation/reservation_database.php');
  require_once('program/reservation/reservation_session.php');

/**
 * Build the login page.
 *
 * @param c_base_html &$html
 *   The html page object.
 * @param null|array $problems
 *   (optional) An array of problems to report when building the form.
 *   This is specified only after a form is submitted.
 *
 * @return c_base_html_return
 *   The markup tags object.
 */
function reservation_build_login_page(&$html, $problems = NULL) {
  $problem_fields = array();
  $problem_messages = array();

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

  if (!empty($problem_messages)) {
    $messages = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, 'form_problems', array('form_problems'))->get_value_exact();
    foreach ($problem_messages as $problem_delta => $problem_message) {
      $class = array(
        'form_problems-problem',
        'form_problems-problem-' . $problem_delta,
      );

      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, 'form_problems-problem-' . $problem_delta, $class)->get_value_exact();
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
  $form = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_FORM, 'login_form', array('login_form'))->get_value_exact();
  $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_METHOD, 'post');
  $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ROLE, 'form');
  $form->set_attribute(c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET, c_base_charset::UTF_8);


  // H1
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1)->get_value_exact();
  $tag->set_text('Login to System');
  $form->set_tag($tag);
  unset($tag);


  // hidden form data
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_HIDDEN, 'form_id', array('login_form-id'))->get_value_exact();
  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, 'login_form');
  $form->set_tag($tag);
  unset($tag);


  // label: username
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-username'))->get_value_exact();
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

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TEXT, 'login_form-username', $class)->get_value_exact();
  unset($class);

  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REQUIRED, TRUE);
  $form->set_tag($tag);
  unset($tag);


  // label: password
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LABEL, NULL, array('login_form-label-password'))->get_value_exact();
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

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_PASSWORD, 'login_form-password', $class)->get_value_exact();
  unset($class);

  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REQUIRED, TRUE);
  $form->set_tag($tag);
  unset($tag);


  // button: reset
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_RESET, 'login_form-reset', array('login_form-button-reset'))->get_value_exact();
  $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE, 'Reset');
  $form->set_tag($tag);
  unset($tag);


  // button: submit
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SUBMIT, 'login_form-login', array('login_form-button-login'))->get_value_exact();
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
 * @param c_base_cookie &$cookie
 *   Session cookie.
 *
 * @return c_base_return_array|c_base_return_status
 *   FALSE on success.
 *   An array of problems on failure.
 *   FALSE with error bit set is returned on error.
 */
function reservation_attempt_login(&$database, &$settings, &$session, &$cookie) {
  $problems = array();
  if (empty($_POST['login_form-username'])) {
    $problems[] = array(
      'fields' => array('login_form-username'),
      'messages' => 'No valid username has been supplied.',
    );
  }

  if (empty($_POST['login_form-password'])) {
    $problems[] = array(
      'fields' => array('login_form-password'),
      'messages' => 'No valid password has been supplied.',
    );
  }

  // explicitly deny access to internal user accounts
  if ($_POST['login_form-username'] == 'public_user') {
    $problems[] = array(
      'fields' => array('login_form-username'),
      'messages' => 'Unable to login, an incorrect user name or password has been specified.',
    );
  }

  // return current list of problems before continuing to login attempt with credentials.
  if (!empty($problems)) {
    return c_base_return_array::s_new($problems);
  }


  // assign username and password to both session and database.
  if (!($session instanceof c_base_session)) {
    $session = new c_base_session();
  }

  if (empty($_SERVER['REMOTE_ADDR'])) {
    $session->set_host('0.0.0.0');
  }
  else {
    $session->set_host($_SERVER['REMOTE_ADDR']);
  }

  $session->set_system_name($settings['session_system']);
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
    $problems[] = array(
      'fields' => array('login_form-username'),
      'messages' => 'Unable to login, an incorrect user or password has been specified.',
    );
  }
  else {
    // @todo: add log entry.
    #set_log_user($database, 'login');

    // @todo: load and store custom settings (loaded from the database and/or ldap).
    #$session->set_id_user($user_id);
    #$session->set_settings($user_data);

    // the session needs to be opened and the data needs to be saved on successful login.
    $result = $session->do_connect();
    if (!c_base_return::s_has_error($result)) {
      $ldap = reservation_database_load_ldap_data($settings, $_POST['login_form-username'])->get_value();
      if ($ldap instanceof c_base_return_false || !is_array($ldap)) {
        $ldap = array(
          'data' => NULL,
        );
      }

      if (isset($ldap['status']) && $ldap['status'] instanceof c_base_return_false) {
        // @todo: handle error situation.
      }

      $user_data = reservation_database_get_user_data($database, $_POST['login_form-username'], $ldap['data'])->get_value();

      if (is_array($user_data) && isset($user_data['id_user'])) {
        $session->set_id_user($user_data['id_user']);
      }

      // @todo: get and use user id from $user_data.

      $result = $session->do_push($settings['session_expire'], $settings['session_max']);
      $session->do_disconnect();

      $session_expire = $session->get_timeout_expire()->get_value_exact();
      $cookie->set_expires($session_expire);
      $cookie->set_max_age(NULL);

      if ($result instanceof c_base_return_true) {
        $data = array(
          'session_id' => $session->get_session_id()->get_value_exact(),
          'expire' => gmdate("D, d-M-Y H:i:s T", $session_expire), // unecessary, but provided for debug purposes.
        );
        $cookie->set_data($data);
      }
      unset($result);
      unset($session_expire);
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
 * @param c_base_html &$html
 *   The html page object.
 *
 * @return c_base_html_return
 *   The markup tags object.
 */
function reservation_build_page_require_https(&$html) {
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1)->get_value_exact();
  $tag->set_text('HTTPS Connection is Required');
  $html->set_tag($tag);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER)->get_value_exact();
  $tag->set_text('Please use a secure connection to access this website.');
  $html->set_tag($tag);
}

/**
 * Build the dashboard page.
 * @param c_base_html &$html
 *   The html page object.
 *
 * @return c_base_html_return
 *   The markup tags object.
 */
function reservation_build_page_dashboard(&$html) {
  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1)->get_value_exact();
  $tag->set_text('Dashboard');
  $html->set_tag($tag);

  $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER)->get_value_exact();
  $tag->set_text('All links will go here.');
  $html->set_tag($tag);
}
