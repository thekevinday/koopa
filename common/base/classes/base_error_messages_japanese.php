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
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_languages.php');

/**
 * English language version of common error messages.
 */
final class c_base_error_messages_japanese implements i_base_error_messages {

  /**
   * Implements i_base_error_messages().
   */
  static function s_render_error_message($error, $arguments = TRUE, $error_message = NULL, $function_name = FALSE, $additional_message = NULL, $html = TRUE) {
    if (!($error instanceof c_base_error)) {
      return c_base_return_string::s_new('');
    }

    $code = $error->get_code();
    if (is_null($code)) {
      unset($code);
      return c_base_return_string::s_new('');
    }

    $display_error_message = $error_message;
    if (is_null($error_message)) {
      if (strlen($error->get_message()) > 0) {
        $display_error_message = TRUE;
      }
      else {
        $display_error_message = FALSE;
      }
    }

    $message = self::s_get_message($code, $arguments, $display_error_message, $function_name)->get_value_exact();
    if (is_string($additional_message)) {
      $message .= $additional_message;
    }
    unset($display_error_message);

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

    // replace the reserved ':{error_message}', with the message assigned to the error object.
    $error_message = $error->get_message();
    if (!is_string($error_message)) {
      $error_message = '';
    }

    if ($html) {
      $processed_message = preg_replace('/:{error_message}/i', '<div class="error_message-error_message">' . htmlspecialchars($error_message, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</div>', $message);
    }
    else {
      $processed_message = preg_replace('/:{error_message}/i', $error_message, $message);
    }
    unset($error_message);

    if (is_string($processed_message)) {
      $message = $processed_message;
    }
    unset($processed_message);

    $details = $error->get_details();
    if (isset($details['arguments']) && is_array($details['arguments'])) {
      if ($html) {
        foreach ($details['arguments'] as $detail_name => $detail_value) {
          if (!is_string($detail_value)) {
            $detail_value = '';
          }

          $detail_name_css = 'error_message-argument-' . preg_replace('/[^[:word:]-]/i', '', $detail_name);
          $processed_message = preg_replace('/' . preg_quote($detail_name, '/') . '/i', '<div class="error_message-argument ' . $detail_name_css . '">' . htmlspecialchars($detail_value, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</div>', $message);
          if (is_string($processed_message)) {
            $message = $processed_message;
          }
        }
        unset($processed_message);
        unset($detail_name_css);
      }
      else {
        foreach ($details['arguments'] as $detail_name => $detail_value) {
          if (!is_string($detail_value)) {
            $detail_value = '';
          }

          $processed_message = preg_replace('/' . preg_quote($detail_name, '/') . '/i', $detail_value, $message);
          if (is_string($processed_message)) {
            $message = $processed_message;
          }
        }
        unset($processed_message);
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
   * @Implements s_get_message().
   */
  static function s_get_message($code, $arguments = TRUE, $error_message = TRUE, $function_name = FALSE) {
    $function_name_prepend = NULL;
    $function_name_string = NULL;
    if ($function_name) {
      $function_name_prepend = '、';
      $function_name_string = ' :{function_name} を呼び出している間';
    }

    $error_message_string = NULL;
    if ($error_message) {
      $error_message_string = '、理由: :{error_message}';
    }

    if ($code === static::INVALID_ARGUMENT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('無効な引数 :{argument_name} が指定されています' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('無効な引数が指定されています' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::INVALID_FORMAT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('引数 :{format_name} の形式が無効です' . $function_name_prepend . $function_name_string . $error_message_string . '。:{expected_format}');
      }
      else {
        return c_base_return_string::s_new('無効な形式が指定されています' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::INVALID_SESSION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('要求されたセッションは無効です' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('要求されたセッションは無効です' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::INVALID_VARIABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('変数 :{variable_name} は無効です' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('無効な変数が指定されています' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::OPERATION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('操作の実行に失敗しました :{operation_name}' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('操作を実行できませんでした' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::OPERATION_UNECESSARY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('不要な操作を実行しませんでした :{operation_name}' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('不要な操作を実行しませんでした' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::FUNCTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('関数 :{function_name} は実行に失敗しました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('関数の実行に失敗しました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NOT_FOUND) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('見つかりません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('見つかりません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NOT_FOUND_ARRAY_INDEX) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('配列 :{index_name} に索引 :{array_name} が見つかりませんでした' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('指定された配列内のインデックスの検索に失敗しました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NOT_FOUND_FILE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ファイル :{file_name} が見つかりませんでした、またはアクセスできません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('ファイルが見つからないか、アクセスできません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NOT_FOUND_DIRECTORY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ディレクトリ :{directory_name} が見つかりませんでした、またはアクセスできません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('ファイルが見つからないか、アクセスできません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NOT_FOUND_FILE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('パス ：{path_name} が見つかりませんでした' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('パスが見つかりません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NOT_DEFINED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('要求されたデータ：{data_name}は定義されていません' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('要求されたデータは定義されていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('リソース :{resource_name} は接続されていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('リソースが接続されていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::NO_SUPPORT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('機能 :{functionality_name} は現在サポートされていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('要求された機能はサポートされていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::POSTGRESQL_CONNECTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベースへの接続に失敗しました。 :{database_name}' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースに接続できませんでした' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::POSTGRESQL_NO_ACCOUNT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベースアクセスが拒否されました：アカウント：{database_account}が存在しないか、必要なアクセス権' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースアクセスが拒否されました：アカウントが存在しないか、必要なアクセス権がありません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::POSTGRESQL_NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベース :{database_name} は接続されていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースが接続されていません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::POSTGRESQL_NO_RESOURCE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('データベースリソースがありません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('データベースリソースは使用できません' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::SOCKET_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ソケット操作の実行に失敗しました。 :{operation_name} 、ソケットエラー（:{socket_error} \':{socket_error_message}\'' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('ソケット操作を実行できませんでした' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::ACCESS_DENIED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('アクセスが拒否されました。 :{operation_name}' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('アクセスは拒否されました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::ACCESS_DENIED_UNAVAILABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('利用できないためアクセスが拒否されましたが、 :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('利用できないためアクセスが拒否されました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::ACCESS_DENIED_USER) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('ユーザーのアクセスが拒否されました :name_machine_user (:{id_user}), :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('ユーザーのアクセスが拒否されました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::ACCESS_DENIED_ADMINISTRATION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('管理上の理由でアクセスが拒否されました, :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('管理上の理由からアクセスが拒否されました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }
    elseif ($code === static::SERVER_ERROR) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('サーバーエラーが発生しました, :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
      else {
        return c_base_return_string::s_new('サーバーエラーが発生しました' . $function_name_prepend . $function_name_string . $error_message_string . '。');
      }
    }

    return c_base_return_string::s_new('');
  }
}
