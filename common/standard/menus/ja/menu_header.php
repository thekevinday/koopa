<?php
/**
 * @file
 * Provides a language specific class.
 */
require_once('common/standard/menus/menu_header.php');

/**
 * A generic class for managing a header menu.
 */
class c_standard_menu_header_ja extends c_standard_menu_header {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'ヘッダーメニュー';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
