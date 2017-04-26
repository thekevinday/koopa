<?php
/**
 * @file
 * Provides path handler for the logout process.
 */


/**
 * Implements c_reservation_path_user_logout().
 */
class c_reservation_path_user_logout_ja extends c_reservation_path_user_logout {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'あなたはログアウトしました';
      case 1:
        return 'あなたはシステムからログアウトされています。';
    }

    return '';
  }
}
