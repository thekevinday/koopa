<?php
/**
 * @file
 * Provides path handler for the user edit.
 */

/**
 * Implements c_standard_path_user_edit().
 */
class c_standard_path_user_edit_ja extends c_standard_path_user_edit {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'ダッシュボード';
        break;
      case 1:
        $string = '';
        break;
      case 2:
        $string = '';
        break;
      case 3:
        $string = '';
        break;
      case 4:
        $string = 'パブリック';
        break;
      case 5:
        $string = 'ユーザー';
        break;
      case 6:
        $string = 'リクエスタ';
        break;
      case 7:
        $string = 'ドレイター';
        break;
      case 8:
        $string = '編集者';
        break;
      case 9:
        $string = 'レビューア';
        break;
      case 10:
        $string = 'ファイナンサー';
        break;
      case 11:
        $string = '保険会社';
        break;
      case 12:
        $string = '出版社';
        break;
      case 13:
        $string = '審査員';
        break;
      case 14:
        $string = 'マネージャー';
        break;
      case 15:
        $string = '管理者';
        break;
      case 16:
        $string = 'Account Information';
        break;
      case 17:
        $string = 'Personal Information';
        break;
      case 18:
        $string = 'Access Information';
        break;
      case 19:
        $string = 'History Information';
        break;
      case 20:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを表示 :{user_name}';
        }
        else {
          $string = 'ユーザーを表示';
        }
        break;
      case 21:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーの編集 :{user_name}';
        }
        else {
          $string = 'ユーザーの編集';
        }
        break;
      case 22:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーをキャンセルする :{user_name}';
        }
        else {
          $string = 'ユーザーをキャンセルする';
        }
        break;
      case 23:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを削除 :{user_name}';
        }
        else {
          $string = 'ユーザーを削除';
        }
        break;
      case 24:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを確認する :{user_name}';
        }
        else {
          $string = 'ユーザーを確認する';
        }
        break;
      case 25:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを更新する :{user_name}';
        }
        else {
          $string = 'ユーザーを更新する';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
