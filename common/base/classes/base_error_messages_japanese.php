<?php
/**
 * @file
 * Implements japanese language support for common error messages.
 *
 * Disclaimer: This has been added as a test case.
 *             I used translate.google.com to translate this.
 *             I have also noticed that the presence of a period '.' at the end of the string changes translation.
 *             This may require a completely different approach to generating than what works with english.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_languages.php');

/**
 * English language version of common error messages.
 */
final class c_base_error_messages_japanese implements i_base_error_messages {

  /**
   * Converts a given error message into a processed string.
   *
   * @param c_base_error $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are provided.
   *   All placeholders should begin with a single colon ':'.
   * @param bool $function_name
   *   (optional) When TRUE, the function name is included with the message.
   *   When FALSE, no funciton name is provided.
   * @param null|string $additional_message
   *   (optional) Any additional messages to display.
   * @param bool $use_html
   *   (optional) When TRUE, the message is escaped and then wrapped in HTML.
   *   When FALSE, no HTML wrapping or escaping is peformed.
   *
   * @return c_base_return_string
   *   A processed string is returned on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: s_get_message()
   */
  static function s_render_error_message($error, $arguments = TRUE, $function_name = FALSE, $additional_message = NULL, $html = TRUE) {
    if (!($error instanceof c_base_error)) {
      return c_base_return_string::s_new('');
    }

    $code = $error->get_code();
    if (is_null($code)) {
      unset($code);
      return c_base_return_string::s_new('');
    }

    $message = self::s_get_message($code, $arguments, $function_name)->get_value_exact();
    if (is_string($additional_message)) {
      $message .= $additional_message;
    }

    if (empty($message)) {
      unset($message);
      unset($code);
      return c_base_return_string::s_new('');
    }
    unset($code);

    if ($arguments === FALSE) {
      unset($arguments);

      if ($html) {
        return c_base_return_string::s_new('<div class="error_message error_message-' . $code . '">' . $message . '</div>');
      }

      return c_base_return_string::s_new($message);
    }

    $details = $error->get_details();
    if (isset($details['arguments']) && is_array($details['arguments'])) {
      if ($html) {
        foreach ($details['arguments'] as $detail_name => $detail_value) {
          $detail_name_css = 'error_message-argument-' . preg_replace('/[^[:word:]-]/i', '', $detail_name);
          $message = preg_replace('/' . preg_quote($detail_name, '/') . '\b/i', '<div class="error_message-argument ' . $detail_name_css . '">' . htmlspecialchars($detail_value, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</div>', $message);
        }
        unset($detail_name_css);
      }
      else {
        foreach ($details as $detail_name => $detail_value) {
          $message = preg_replace('/' . preg_quote($detail_name, '/') . '\b/i', $detail_value, $message);
        }
      }
      unset($detail_name);
      unset($detail_value);
      unset($details);

      if ($html) {
        return c_base_return_string::s_new('<div class="error_message error_message-' . $code . '">' . $message . '</div>');
      }

      return c_base_return_string::s_new($message);
    }
    unset($details);

    if ($html) {
      return c_base_return_string::s_new('<div class="error_message error_message-' . $code . '">' . $message . '</div>');
    }

    return c_base_return_string::s_new($message);
  }

  /**
   * Returns a standard error message associated with the given code.
   *
   * @param int $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are provided.
   *   All placeholders should begin with a single colon ':'.
   * @param bool $function_name
   *   (optional) When TRUE, the function name is included with the message.
   *   When FALSE, no funciton name is provided.
   *
   * @return c_base_return_string
   *   A processed string is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  static function s_get_message($code, $arguments = TRUE, $function_name = FALSE) {
    $function_name_string = NULL;
    if ($function_name) {
      $function_name_string = ' :{function_name} を呼び出している間';
    }

    if ($code === self::INVALID_ARGUMENT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('無効な引数 :{argument_name} が指定されています' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('無効な引数が指定されています' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
    }
    elseif ($code === self::INVALID_FORMAT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('引数 :{format_name} の形式が無効です' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。:{expected_format}');
      }
      else {
        return c_base_return_string::s_new('無効な形式が指定されています。');
      }
    }
    elseif ($code === self::INVALID_SESSION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('要求されたセッションは無効です' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('要求されたセッションは無効です。');
      }
    }
    elseif ($code === self::INVALID_VARIABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('変数 :{variable_name} は無効です' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('無効な変数が指定されています。');
      }
    }
    elseif ($code === self::OPERATION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('操作の実行に失敗しました :{operation_name}' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('操作を実行できませんでした。');
      }
    }
    elseif ($code === self::OPERATION_UNECESSARY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('不要な操作を実行しませんでした :{operation_name}' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('不要な操作を実行しませんでした。');
      }
    }
    elseif ($code === self::FUNCTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('関数 :{function_name} は実行に失敗しました' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('関数の実行に失敗しました。');
      }
    }
    elseif ($code === self::NOT_FOUND) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('見つかりません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('見つかりません。');
      }
    }
    elseif ($code === self::NOT_FOUND_ARRAY_INDEX) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('配列 :{index_name} に索引 :{array_name} が見つかりませんでした' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('指定された配列内のインデックスの検索に失敗しました。');
      }
    }
    elseif ($code === self::NOT_FOUND_FILE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ファイル :{file_name} が見つかりませんでした、またはアクセスできません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('ファイルが見つからないか、アクセスできません。');
      }
    }
    elseif ($code === self::NOT_FOUND_DIRECTORY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ディレクトリ :{directory_name} が見つかりませんでした、またはアクセスできません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('ファイルが見つからないか、アクセスできません。');
      }
    }
    elseif ($code === self::NOT_FOUND_FILE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('パス ：{path_name} が見つかりませんでした' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('パスが見つかりません。');
      }
    }
    elseif ($code === self::NOT_DEFINED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('要求されたデータ：{data_name}は定義されていません' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('要求されたデータは定義されていません。');
      }
    }
    elseif ($code === self::NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('リソース :{resource_name} は接続されていません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('リソースが接続されていません。');
      }
    }
    elseif ($code === self::NO_SUPPORT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('機能 :{functionality_name} は現在サポートされていません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('要求された機能はサポートされていません。');
      }
    }
    elseif ($code === self::POSTGRESQL_CONNECTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベースへの接続に失敗しました。 :{database_name}' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースに接続できませんでした。');
      }
    }
    elseif ($code === self::POSTGRESQL_NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベース :{database_name} は接続されていません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースが接続されていません。');
      }
    }
    elseif ($code === self::POSTGRESQL_NO_RESOURCE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベースリソースがありません' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースリソースは使用できません。.');
      }
    }
    elseif ($code === self::SOCKET_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ソケット操作の実行に失敗しました。 :{operation_name} 、ソケットエラー（:{socket_error} \':{socket_error_message}\'' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('ソケット操作を実行できませんでした。');
      }
    }
    elseif ($code === self::ACCESS_DENIED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('アクセスが拒否されました。 :{operation_name}' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('アクセスは拒否されました。');
      }
    }
    elseif ($code === self::ACCESS_DENIED_UNAVAILABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('利用できないためアクセスが拒否されましたが、 :{operation_name} ' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('利用できないためアクセスが拒否されました。');
      }
    }
    elseif ($code === self::ACCESS_DENIED_USER) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ユーザーのアクセスが拒否されました :name_machine_user (:{id_user}), :{operation_name} ' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('ユーザーのアクセスが拒否されました。');
      }
    }
    elseif ($code === self::ACCESS_DENIED_ADMINISTRATION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('管理上の理由でアクセスが拒否されました, :{operation_name} ' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('管理上の理由からアクセスが拒否されました。');
      }
    }
    elseif ($code === self::SERVER_ERROR) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('サーバーエラーが発生しました, :{operation_name} ' . (is_null($function_name_string) ? '' : '、') . $function_name_string . '。');
      }
      else {
        return c_base_return_string::s_new('サーバーエラーが発生しました。');
      }
    }

    return c_base_return_string::s_new('');
  }
}
