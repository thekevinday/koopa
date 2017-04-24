<?php
/**
 * @file
 * Provides reservation redirect classes.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_http_status.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Handles redirects to a specified destination.
 */
final class c_reservation_path_redirect extends c_base_path {
  const PREFIX_ID    = '';
  const PREFIX_CLASS = 'reservation-';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, &$html, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $html, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $http->set_response_location($this->field_destination);
    $http->set_response_status($this->field_response_code);

    return $executed;
  }
}
