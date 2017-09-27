<?php
/**
 * @file
 * Provides path handler for the login process.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_user_login().
 */
class c_standard_path_user_login_ja extends c_standard_path_user_login {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'システムへのログイン';
        break;
      case 1:
        $string = 'ユーザー名';
        break;
      case 2:
        $string = 'パスワード';
        break;
      case 3:
        $string = 'ログインした';
        break;
      case 4:
        $string = '現在、システムに@{user}としてログインしています。';
        break;
      case 5:
        $string = 'してもいいです';
        break;
      case 6:
        $string = 'ログアウト';
        break;
      case 7:
        $string = 'いつでも';
        break;
      case 8:
        $string = 'ログイン失敗';
        break;
      case 9:
        $string = 'あなたは既にログインしています。';
        break;
      case 10:
        $string = 'ログインできません。間違ったユーザー名またはパスワードが指定されています。';
        break;
      case 11:
        $string = 'フォームをリセット';
        break;
      case 12:
        $string = 'ログイン';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
