<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */
namespace n_koopa;

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
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'ユーザー設定:{user_name}';
        }
        else {
          $string = 'ユーザー設定';
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
      case 37:
        $string = '接頭辞';
        break;
      case 38:
        $string = '最初';
        break;
      case 39:
        $string = '中間';
        break;
      case 40:
        $string = '最終';
        break;
      case 41:
        $string = 'サフィックス';
        break;
      case 42:
        $string = 'フル';
        break;
      case 43:
        $string = '未公開';
        break;
      case 44:
        $string = 'ユーザーID';
        break;
      case 45:
        $string = 'タイトル';
        break;
      case 46:
        $string = 'タイプ';
        break;
      case 47:
        $string = 'サブタイプ';
        break;
      case 48:
        $string = '重大度';
        break;
      case 49:
        $string = '施設';
        break;
      case 50:
        $string = '詳細';
        break;
      case 51:
        $string = '日付';
        break;
      case 52:
        $string = 'クライアント';
        break;
      case 53:
        $string = 'レスポンスコード';
        break;
      case 54:
        $string = 'セッションユーザーID';
        break;
      case 55:
        $string = 'リクエストパス';
        break;
      case 56:
        $string = '引数を要求する';
        break;
      case 57:
        $string = 'リクエストクライアント';
        break;
      case 58:
        $string = 'リクエスト日';
        break;
      case 59:
        $string = 'リクエストヘッダー';
        break;
      case 60:
        $string = '応答ヘッダー';
        break;
      case 61:
        $string = 'レスポンスコード';
        break;
      case 62:
        $string = 'ユーザー履歴';
        break;
      case 63:
        $string = 'アクセス履歴';
        break;
      default:
        unset($string);
        return parent::pr_get_text($code, $arguments);
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
