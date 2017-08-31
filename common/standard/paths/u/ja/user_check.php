<?php
/**
 * @file
 * Provides path handler for the user checks.
 */

/**
 * Implements c_standard_path_user_check().
 */
class c_standard_path_user_check_ja extends c_standard_path_user_check {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = '照合:{user_name}';
        }
        else {
          $string = '照合';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}