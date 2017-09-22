<?php
/**
 * @file
 * Provides a class for a standard content menu.
 *
 * This is the navigation menu that is intended to be used for navigating the site.
 * This is (generally) specific to each page or url path.
 */
namespace n_koopa;

require_once('common/base/classes/base_markup.php');

require_once('common/standard/classes/standard_menu.php');
require_once('common/standard/classes/standard_paths.php');

/**
 * A generic class for managing a content menu.
 */
class c_standard_menu_content extends c_standard_menu {
  protected const CLASS_NAME = 'menu-content';

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Content Menu';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
