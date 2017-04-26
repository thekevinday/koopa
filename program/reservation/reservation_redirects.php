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
   * Create a redirect path.
   *
   * Defaults are silently forced on invalid parameters.
   *
   * @param string $field_destination
   *   A destination URL to redirect to.
   * @param int $response_code
   *   The HTTP code to use when performing the redirect.
   *   Should be one of 3xx error code integers.
   * @param int $field_response_code
   *   The redirect response code.
   *   Should be a 3xx url code.
   *   Usually one of:
   *   - 300 (Multiple Choices):
   *   - 301 (Moved Permanently):
   *   - 303 (See Other):
   *   This is not assigned on parameter error.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_redirect($field_destination, $field_response_code, $is_private = TRUE) {
    $class = __CLASS__;
    $path = new $class();
    unset($class);

    // @todo: store all errors on return.
    $errors = array();

    if (is_string($field_destination) || is_array($field_destination)) {
      $path->set_field_destination($field_destination);
    }

    if (is_int($field_response_code)) {
      $path->set_field_response_code($field_response_code);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    $path->set_is_redirect(TRUE);

    $timestamp_session = c_base_defaults_global::s_get_timestamp_session();
    $path->set_date_created($timestamp_session);
    $path->set_date_changed($timestamp_session);
    unset($timestamp_session);

    return $path;
  }

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $http->set_response_location($this->field_destination);
    $http->set_response_status($this->field_response_code);

    return $executed;
  }
}
