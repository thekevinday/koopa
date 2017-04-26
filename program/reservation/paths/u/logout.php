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
class c_reservation_path_form_user_logout extends c_base_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }


    // Wrapper
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'content-wrapper', 'content-wrapper'));


    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text($this->pr_get_text(0));
    $wrapper->set_tag($tag);
    unset($tag);

    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text($this->pr_get_text(1));
    $wrapper->set_tag($tag);
    unset($tag);


    // initialize the content as HTML.
    $html = c_reservation_build::s_create_html($http, $database, $session, $settings);
    $html->set_tag($wrapper);
    unset($wrapper);

    $executed = new c_base_path_executed();
    $executed->set_output($html);
    unset($html);

    reservation_session_logout($database, $session, $settings);

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
        return 'You Have Logged Out';
      case 1:
        return 'You have been logged out of the system.';
    }

    return '';
  }
}
