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

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a form for the user logout.
 *
 * This listens on: /u/logout
 */
class c_reservation_path_user_logout extends c_reservation_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $this->pr_assign_defaults($settings);

    $result = $this->p_do_logout($database, $session, $settings);

    $wrapper = $this->pr_create_tag_wrapper();
    $wrapper->set_tag($this->pr_create_tag_title(0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));


    // initialize the content as HTML.
    $html = $this->pr_create_html($http, $database, $session, $settings);
    $html->set_tag($wrapper);
    unset($wrapper);

    $executed->set_output($html);
    unset($html);

    return $executed;
  }

  /**
   * Logout of the session.
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
    if (!$database->is_connected()->get_value_exact()) {
      $connected = reservation_database_connect($database);
      if (c_base_return::s_has_error($connected)) {
        unset($connected);
        unset($already_connected);
        return FALSE;
      }
      unset($connected);
    }

    // @todo: write to database logout log entry.

    $cookie_login = $session->get_cookie();

    $database->do_disconnect();
    $session->do_terminate();
    $session->do_disconnect();
    $session->set_logged_in(FALSE);

    // remove username and password from database string.
    reservation_database_string($database, $settings);

    // delete the login cookie.
    if ($cookie_login instanceof c_base_cookie) {
      $cookie_login->set_expires(-1);
      $cookie_login->set_max_age(-1);
      $result = $session->set_cookie($cookie_login);
    }
    unset($cookie_login);

    return TRUE;
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
