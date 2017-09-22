<?php
/**
 * @file
 * Provides path handler for the server error pages.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_server_error().
 */
final class c_standard_path_server_error_ja extends c_standard_path_server_error {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'サーバーエラー';
        break;
      case 1:
        $string = 'リクエストの処理中に問題が発生しました。しばらくしてからもう一度お試しください。';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
