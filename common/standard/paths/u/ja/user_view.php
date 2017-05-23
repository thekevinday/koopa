<?php
/**
 * @file
 * Provides path handler for the user view.
 */

/**
 * Implements c_standard_path_user_dashboard().
 */
class c_standard_path_user_view_ja extends c_standard_path_user_view {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを表示 :{user_name}';
        }
        else {
          $string = 'ユーザーを表示';
        }
        break;
      case 1:
        $string = 'パブリック';
        break;
      case 2:
        $string = 'ユーザー';
        break;
      case 3:
        $string = 'リクエスタ';
        break;
      case 4:
        $string = 'ドレイター';
        break;
      case 5:
        $string = '編集者';
        break;
      case 6:
        $string = 'レビューア';
        break;
      case 7:
        $string = 'ファイナンサー';
        break;
      case 8:
        $string = '保険会社';
        break;
      case 9:
        $string = '出版社';
        break;
      case 10:
        $string = '審査員';
        break;
      case 11:
        $string = 'マネージャー';
        break;
      case 12:
        $string = '管理者';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
