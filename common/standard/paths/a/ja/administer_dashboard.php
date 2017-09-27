<?php
/**
 * @file
 * Provides a language specific class.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_administer_dashboard().
 */
class c_standard_path_administer_dashboard_ja extends c_standard_path_administer_dashboard {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
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
