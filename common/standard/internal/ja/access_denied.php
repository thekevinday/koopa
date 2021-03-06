<?php
/**
 * @file
 * Provides path handler for the access denied pages.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_access_denied().
 */
final class c_standard_path_access_denied_ja extends c_standard_path_access_denied {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'アクセス拒否';
        break;
      case 1:
        $string = 'このリソースにアクセスする権限がありません。';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
