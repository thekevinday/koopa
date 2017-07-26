<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

/**
 * Implements c_standard_path_user_lock().
 */
class c_standard_path_user_lock_ja extends c_standard_path_user_lock {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーをロック:{user_name}';
        }
        else {
          $string = 'ユーザーをロックする';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}