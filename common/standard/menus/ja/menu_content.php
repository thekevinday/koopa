<?php
/**
 * @file
 * Provides a language specific class.
 */
namespace n_koopa;

require_once('common/standard/menus/menu_content.php');

/**
 * A generic class for managing a content menu.
 */
class c_standard_menu_content_ja extends c_standard_menu_content {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'コンテンツメニュー';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
