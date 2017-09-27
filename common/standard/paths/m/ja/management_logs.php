<?php
/**
 * @file
 * Provides a language specific class.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_management_logs().
 */
class c_standard_path_management_logs_ja extends c_standard_path_management_logs {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '管理ログ';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
