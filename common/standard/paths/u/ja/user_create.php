<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

/**
 * Implements c_standard_path_user_create().
 */
class c_standard_path_user_create_ja extends c_standard_path_user_create {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーをコピー:{user_name}';
        }
        else {
          $string = 'ユーザーを作成する';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
