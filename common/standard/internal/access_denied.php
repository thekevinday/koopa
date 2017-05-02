<?php
/**
 * @file
 * Provides path handler for the access denied pages.
 */

require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_http_status.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_access_denied extends c_standard_path {

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

    $wrapper = $this->pr_create_tag_wrapper();
    $wrapper->set_tag($this->pr_create_tag_title(0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));


    // initialize the content as HTML.
    $html = $this->pr_create_html($http, $database, $session, $settings);
    $html->set_tag($wrapper);
    unset($wrapper);

    $executed->set_output($html);
    unset($html);


    // assign HTTP response status.
    $http->set_response_status(c_base_http_status::FORBIDDEN);


    return $executed;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Access Denied';
        break;
      case 1:
        $string = 'You are not authorized to access this resource.';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
