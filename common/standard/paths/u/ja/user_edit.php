<?php
/**
 * @file
 * Provides path handler for the user edit.
 */

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
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを編集:{user_name}';
        }
        else {
          $string = 'ユーザーを編集する';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}