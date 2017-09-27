<?php
/**
 * @file
 * Provides path handler for the user view.
 */
namespace n_koopa;

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
  protected function pr_get_text($code, $arguments = []) {
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
        $string = '画像';
        break;
      case 2:
        $string = 'ユーザー情報';
        break;
      case 3:
        $string = '画像がありません';
        break;
      case 4:
        $string = 'プロフィールの写真';
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
