<?php
/**
 * @file
 * Provides path handler for the site index.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_index extends c_standard_path {

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    $this->breadcrumbs = new c_base_menu_item();

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text(2), '');
    $this->breadcrumbs->set_item($item);
    unset($item);

    return new c_base_return_true();
  }

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = []) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));


    // initialize the content as HTML.
    $this->pr_create_html();
    $this->html->set_tag($wrapper);
    unset($wrapper);

    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);

    return $executed;
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = []) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Standard System';
        break;
      case 1:
        $string = 'This is the standard system index page.';
        break;
      case 2:
        $string = 'Home';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
