<?php
/**
 * @file
 * Provides path handler for the login process.
 */


/**
 * Implements c_reservation_path_user_login().
 */
class c_reservation_path_user_login_ja extends c_reservation_path_user_login {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'システムへのログイン';
      case 1:
        return 'ユーザー名';
      case 2:
        return 'パスワード';
    }

    return '';
  }
}
