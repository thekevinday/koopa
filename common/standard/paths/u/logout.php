<?php
/**
 * @file
 * Provides path handler for the user logout process.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_log.php');

require_once('common/standard/classes/standard_path.php');
require_once('common/standard/classes/standard_database.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a form for the user logout.
 *
 * This listens on: /u/logout
 */
class c_standard_path_user_logout extends c_standard_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $this->pr_do_logout($http, $database, $session, $settings);

    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));


    // initialize the content as HTML.
    $this->pr_create_html();
    $this->html->set_tag($wrapper);
    unset($wrapper);

    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);

    return $executed;
  }

  /**
   * Logout of the session.
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
  protected function pr_do_logout(&$http, &$database, &$session, $settings) {
    $http_status = $http->get_response_status()->get_value_exact();
    if (!is_int($http_status)) {
      $http_status = c_base_http_status::OK;
    }


    // disconnect from the database.
    if ($database->is_connected() instanceof c_base_return_true) {
      if ($database instanceof c_standard_database) {
        $database->do_log_user(c_base_log::TYPE_DISCONNECT, $http_status);
      }

      $database->do_disconnect();
    }
    unset($http_status);

    $connection_string = $database->get_connection_string();
    $connection_string->set_user('');
    $connection_string->set_password(NULL);

    if (isset($settings['database_user_public']) && is_string($settings['database_user_public'])) {
      $connection_string->set_user($settings['database_user_public']);
    }

    $database->set_connection_string($connection_string);
    unset($connection_string);


    // disconnect from the session.
    $session->do_terminate();
    $session->do_disconnect();
    $session->is_logged_in(FALSE);
    $session->set_user_current(NULL);
    $session->set_user_session(NULL);


    // delete the login cookie.
    $cookie_login = $session->get_cookie();
    if ($cookie_login instanceof c_base_cookie) {
      $cookie_login->set_expires(-1);
      $cookie_login->set_max_age(-1);
      $result = $session->set_cookie($cookie_login);
    }
    unset($cookie_login);

    return TRUE;
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'You Have Logged Out';
        break;
      case 1:
        $string = 'You have been logged out of the system.';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
