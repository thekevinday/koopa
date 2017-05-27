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
   * Implements pr_create_tag_spacer().
   */
  protected function pr_create_tag_spacer(&$tag) {
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザーを表示:{user_name}';
        }
        else {
          $string = 'ユーザーを表示';
        }
        break;
      case 1:
        $string = 'パブリック';
        break;
      case 2:
        $string = 'システム';
        break;
      case 3:
        $string = 'ユーザー';
        break;
      case 4:
        $string = 'リクエスタ';
        break;
      case 5:
        $string = 'ドレイター';
        break;
      case 6:
        $string = '編集者';
        break;
      case 7:
        $string = 'レビューア';
        break;
      case 8:
        $string = 'ファイナンサー';
        break;
      case 9:
        $string = '保険会社';
        break;
      case 10:
        $string = '出版社';
        break;
      case 11:
        $string = '審査員';
        break;
      case 12:
        $string = 'マネージャー';
        break;
      case 13:
        $string = '管理者';
        break;
      case 14:
        $string = '口座情報';
        break;
      case 15:
        $string = '個人情報';
        break;
      case 16:
        $string = 'アクセス情報';
        break;
      case 17:
        $string = '履歴情報';
        break;
      case 18:
        $string = '身元';
        break;
      case 19:
        $string = '外部身元';
        break;
      case 20:
        $string = '名';
        break;
      case 21:
        $string = 'Eメール';
        break;
      case 22:
        $string = 'ロール';
        break;
      case 23:
        $string = 'ロールマネージャ';
        break;
      case 24:
        $string = 'ロックされている';
        break;
      case 25:
        $string = '削除されました';
        break;
      case 26:
        $string = 'パブリックです';
        break;
      case 27:
        $string = 'プライベートです';
        break;
      case 28:
        $string = '作成日';
        break;
      case 29:
        $string = '日付変更';
        break;
      case 30:
        $string = '日付同期';
        break;
      case 31:
        $string = 'ロックされた日付';
        break;
      case 32:
        $string = '削除された日付';
        break;
      case 33:
        $string = 'はい';
        break;
      case 34:
        $string = 'いいえ';
        break;
      case 35:
        $string = '有効';
        break;
      case 36:
        $string = '無効';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
