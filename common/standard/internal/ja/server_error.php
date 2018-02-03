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
   * Implements pr_get_error_text().
   */
  protected function pr_get_error_text($error, $arguments = TRUE, $function_name = FALSE, $additional_message = NULL, $html = FALSE) {
    if (!($error instanceof c_base_error)) {
      return new c_base_return_false();
    }

    require_once('common/base/classes/base_error_messages_japanese.php');
    return c_base_error_messages_japanese::s_render_error_message($error, $arguments, $function_name, $additional_message, $html);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'サーバーエラー';
        break;
      case 1:
        $string = 'リクエストの処理中に問題が発生しました。しばらくしてからもう一度お試しください。';
        break;
      case 2:
        $string = 'エラーメッセージ';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
