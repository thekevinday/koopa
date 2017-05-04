<?php
/**
 * @file
 * Provides path handler for the administer dashboard.
 */

/**
 * Implements c_standard_path_administer_dashboard().
 */
class c_standard_path_administer_dashboard_ja extends c_standard_path_administer_dashboard {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'ダッシュボードの管理';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
