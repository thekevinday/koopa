<?php
/**
 * @file
 * Provides path handler for the not found pages.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_bad_method().
 */
final class c_standard_path_bad_method_ja extends c_standard_path_bad_method {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '悪い方法';
        break;
      case 1:
        $string = '指定されたHTTP要求メソッドは、要求パスに対してサポートされていないか無効です。';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
