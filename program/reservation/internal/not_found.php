<?php
/**
 * @file
 * Provides path handler for the not found pages.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_session.php');

require_once('common/theme/classes/theme_html.php');

final class c_reservation_path_not_found extends c_base_path {
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
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'error-path', 'error-path', 'error-path-not_found'));


    // H1
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text('Page Not Found');
    $wrapper->set_tag($tag);
    unset($tag);


    // Content
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text('The page you requested is not available.');
    $wrapper->set_tag($tag);
    unset($tag);


    $html->set_tag($wrapper);
    unset($wrapper);


    // assign HTTP response status.
    $http->set_response_status(c_base_http_status::NOT_FOUND);

    return $executed;
  }
}
