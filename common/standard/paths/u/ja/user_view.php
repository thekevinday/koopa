<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

/**
 * Implements c_standard_path_user_dashboard().
 */
class c_standard_path_user_dashboard_ja extends c_standard_path_user_dashboard {

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
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
