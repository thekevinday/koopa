<?php
/**
 * @file
 * Provides path handler for the logout process.
 */


/**
 * Implements c_standard_path_user_logout().
 */
class c_standard_path_user_logout_ja extends c_standard_path_user_logout {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'あなたはログアウトしました';
        break;
      case 1:
        $string = 'あなたはシステムからログアウトされています。';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
