<?php
/**
 * @file
 * Provides path handler for the management dashboard.
 */

/**
 * Implements c_standard_path_management_dashboard().
 */
class c_standard_path_management_dashboard_ja extends c_standard_path_management_dashboard {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '管理ダッシュボード';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
