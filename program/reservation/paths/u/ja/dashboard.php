<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

/**
 * Implements c_reservation_path_user_dashboard().
 */
class c_reservation_path_user_dashboard_ja extends c_reservation_path_user_dashboard {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'ダッシュボード';
      case 1:
        // note: currently not converting this because all text in this area will undergo major changes.
        return parent::pr_get_text($code);
      case 2:
        return 'あなたは現在次のようにログインしています：';
      case 3:
        return '現在、次の役割が割り当てられています。';
      case 4:
        return 'パブリック';
      case 5:
        return 'ユーザー';
      case 6:
        return 'リクエスタ';
      case 7:
        return 'ドレイター';
      case 8:
        return '編集者';
      case 9:
        return 'レビューア';
      case 10:
        return 'ファイナンサー';
      case 11:
        return '保険会社';
      case 12:
        return '出版社';
      case 13:
        return '審査員';
      case 14:
        return 'マネージャー';
      case 15:
        return '管理者';
    }

    return '';
  }
}
