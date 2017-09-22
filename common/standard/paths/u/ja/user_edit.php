<?php
/**
 * @file
 * Provides path handler for the user edit.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_user_edit().
 */
class c_standard_path_user_edit_ja extends c_standard_path_user_edit {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを編集:{user_name}';
        }
        else {
          $string = 'ユーザーを編集する';
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
