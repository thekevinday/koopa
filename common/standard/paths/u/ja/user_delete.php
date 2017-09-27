<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_user_delete().
 */
class c_standard_path_user_delete_ja extends c_standard_path_user_delete {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを削除:{user_name}';
        }
        else {
          $string = 'ユーザーを削除する';
        }
        break;
      default:
        unset($string);
        return parent::pr_get_text($code, $arguments);
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
