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
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    if (!is_object($this->path_tree)) {
      return parent::pr_build_breadcrumbs();
    }

    $handler_settings = $this->path_tree->get_item_reset();
    if ($handler_settings instanceof c_base_return_false) {
      unset($handler_settings);
      return parent::pr_build_breadcrumbs();
    }

    $handler_settings = $handler_settings->get_value();

    if (!isset($handler_settings['include_name']) || !is_string($handler_settings['include_name'])) {
      return parent::pr_build_breadcrumbs();
    }

    if (!isset($handler_settings['include_directory']) || !is_string($handler_settings['include_directory'])) {
      return parent::pr_build_breadcrumbs();
    }

    if (!isset($handler_settings['handler']) || !is_string($handler_settings['handler'])) {
      return parent::pr_build_breadcrumbs();
    }

    require_once($handler_settings['include_directory'] . $handler_settings['include_name'] . self::SCRIPT_EXTENSION);


    $handler = NULL;
    if (is_string($this->language_alias)) {
      @include_once($handler_settings['include_directory'] . $this->language_alias . '/' . $handler_settings['include_name'] . self::SCRIPT_EXTENSION);

      $handler_class = $handler_settings['handler'] . '_' . $this->language_alias;
      if (class_exists($handler_class)) {
        $handler = new $handler_class();
      }
      unset($handler_class);
    }

    if (is_null($handler)) {
      if (class_exists($handler_settings['handler'])) {
        $handler = new $handler_settings['handler']();
      }
      else {
        unset($handler);
        return parent::pr_build_breadcrumbs();
      }
    }

    $this->breadcrumbs = $handler->get_breadcrumbs();
    unset($handler);

    return new c_base_return_true();
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
    $http->set_response_status(c_base_http_status::FORBIDDEN);


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
