<?php
/**
 * @file
 * Provides path handler for the not found pages.
 */

require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_http_status.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_not_found extends c_standard_path {

  /**
   * Build the breadcrumb.
   *
  protected function pr_build_breadcrumbs() {
    $this->breadcrumbs = new c_base_menu_item();

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text_breadcrumbs(0), '');
    $this->breadcrumbs->set_item($item);
    unset($item);

    // @todo: check the url path and attempt to get a breadcrumb for the current path.
    //        this will require external functions because the breadcrumb language specific text must be loaded.
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

    $this->pr_assign_defaults($http, $database, $session, $settings);

    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));


    // initialize the content as HTML.
    $this->pr_create_html(FALSE);
    $this->html->set_tag($wrapper);
    unset($wrapper);

    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);


    // assign HTTP response status.
    $http->set_response_status(c_base_http_status::NOT_FOUND);


    return $executed;
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text_breadcrumbs().
   */
  protected function pr_get_text_breadcrumbs($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Page Not Found';
        break;
      case 1:
        $string = 'The page you requested is not available.';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
