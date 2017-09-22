<?php
/**
 * @file
 * Provides a class for a standard footer menu.
 *
 * A footer menu is a menu that is intended to be displayed in the footer of a page.
 * This is (generally) a global menu that is displayed on most pages.
 */
namespace n_koopa;

require_once('common/base/classes/base_markup.php');

require_once('common/standard/classes/standard_menu.php');
require_once('common/standard/classes/standard_paths.php');

/**
 * A generic class for managing a footer menu.
 */
class c_standard_menu_footer extends c_standard_menu {
  protected const CLASS_NAME = 'menu-footer';

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Footer Menu';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
