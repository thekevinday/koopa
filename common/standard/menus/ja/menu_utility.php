<?php
/**
 * @file
 * Provides a language specific class.
 */
require_once('common/standard/menus/menu_utility.php');

/**
 * A generic class for managing a utility menu.
 */
class c_standard_menu_utility_ja extends c_standard_menu_utility {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'ユーティリティメニュー';
        break;
      case 1:
        $string = 'ホームページ';
        break;
      case 2:
        $string = 'ログイン';
        break;
      case 3:
        $string = 'ダッシュボード';
        break;
      case 4:
        $string = '管理';
        break;
      case 5:
        $string = 'サイト管理';
        break;
      case 6:
        $string = '設定';
        break;
      case 7:
        $string = 'ログアウト';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
