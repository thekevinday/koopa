<?php
/**
 * @file
 * Provides path handler for the server rror pages.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_session.php');

require_once('common/theme/classes/theme_html.php');

class c_reservation_path_server_error extends c_base_path {
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
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'error-path', 'error-path', 'error-path-not_found'));


    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text($this->pr_get_text(0));
    $wrapper->set_tag($tag);
    unset($tag);


    // Content
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text($this->pr_get_text(1));
    $wrapper->set_tag($tag);
    unset($tag);


    // initialize the content as HTML.
    $html = c_reservation_build::s_create_html($http, $database, $session, $settings, $this->pr_get_title());
    $html->set_tag($wrapper);
    unset($wrapper);

    $executed = new c_base_path_executed();
    $executed->set_output($html);
    unset($html);


    // assign HTTP response status.
    $http->set_response_status(c_base_http_status::INTERNAL_SERVER_ERROR);


    return $executed;
  }

  /**
   * Load the title text associated with this page.
   *
   * This is provided here as a means for a language class to override with a custom language for the title.
   *
   * @return string|null
   *   A string is returned as the custom title.
   *   NULL is returned to enforce default title.
   */
  protected function pr_get_title() {
    return NULL;
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
        return 'Server Error';
      case 1:
        return 'Something went wrong while processing your request, please try again later.';
    }

    return '';
  }
}
