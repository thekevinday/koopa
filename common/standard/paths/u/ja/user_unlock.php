<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

/**
 * Implements c_standard_path_user_dashboard().
 */
class c_standard_path_user_dashboard_ja extends c_standard_path_user_dashboard {

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
