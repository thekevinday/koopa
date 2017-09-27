<?php
/**
 * @file
 * Provides path handler for the user refreshes.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_user_refresh().
 */
class c_standard_path_user_refresh_ja extends c_standard_path_user_refresh {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = '晴らす:{user_name}';
        }
        else {
          $string = '晴らす';
        }
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
