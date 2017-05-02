<?php
/**
 * @file
 * Provides path handler for the server rror pages.
 */

require_once('common/base/classes/base_return.php');

require_once('common/standard/classes/standard_path.php');

/**
 * Provide the HTTP options response.
 *
 * This does not provide any content body.
 */
final class c_standard_path_options_method extends c_standard_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }


    // assign HTTP response status.
    $allowed_methods = $this->allowed_methods;
    $allowed_method = array_shift($allowed_methods);
    $http->set_response_allow($allowed_method, TRUE);

    if (!empty($allowed_methods)) {
      foreach ($allowed_methods as $allowed_method) {
        $http->set_response_allow($allowed_method);
      }
    }
    unset($allowed_method);
    unset($allowed_methods);

    return $executed;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }
}
