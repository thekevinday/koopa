<?php
/**
 * @file
 * Provides a language specific class.
 */
namespace n_koopa;

require_once('common/standard/menus/menu_footer.php');

/**
 * A generic class for managing a footer menu.
 */
class c_standard_menu_footer_ja extends c_standard_menu_footer {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'フッターメニュー';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
