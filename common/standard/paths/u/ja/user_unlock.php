<?php
/**
 * @file
 * Provides path handler for the user unlock.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_user_unlock().
 */
class c_standard_path_user_unlock_ja extends c_standard_path_user_unlock {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーをロック解除:{user_name}';
        }
        else {
          $string = 'ユーザーをロック解除する';
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
