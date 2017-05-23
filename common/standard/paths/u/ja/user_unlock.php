<?php
/**
 * @file
 * Provides path handler for the user unlock.
 */

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
        $string = '';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
