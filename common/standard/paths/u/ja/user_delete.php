<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

/**
 * Implements c_standard_path_user_delete().
 */
class c_standard_path_user_delete_ja extends c_standard_path_user_delete {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
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
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}