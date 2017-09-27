<?php
/**
 * @file
 * Provides a language specific class.
 */
namespace n_koopa;

require_once('common/standard/menus/menu_content.php');

/**
 * A path-specific class for managing a content menu.
 */
class c_standard_menu_content_user_view_ja extends c_standard_menu_content_user_view {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'User Menu';
        break;
      case 1:
        $string = 'Profile';
        break;
      case 2:
        $string = 'Dashboard';
        break;
      case 3:
        $string = 'Settings';
        break;
      case 4:
        $string = 'Edit';
        break;
      case 5:
        $string = 'Check';
        break;
      case 6:
        $string = 'Refresh';
        break;
      case 7:
        $string = 'Lock';
        break;
      case 8:
        $string = 'Unlock';
        break;
      case 9:
        $string = 'Delete';
        break;
      case 10:
        $string = 'Copy'; // @todo: implement this in the project.
        break;
      case 11:
        $string = 'Create New User';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
