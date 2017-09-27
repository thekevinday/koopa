<?php
/**
 * @file
 * Provides path handler for the index page.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_index().
 */
final class c_standard_path_index_ja extends c_standard_path_index {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '標準システム';
        break;
      case 1:
        $string = 'これは標準のシステムインデックスページです。';
        break;
      case 2:
        $string = 'ホームページ';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
