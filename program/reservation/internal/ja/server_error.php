<?php
/**
 * @file
 * Provides path handler for the server error pages.
 */

/**
 * Implements c_reservation_path_server_error().
 */
final class c_reservation_path_server_error_ja extends c_reservation_path_server_error {

  /**
   * Implements pr_get_title().
   */
  protected function pr_get_title() {
    return '予約システム';
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'サーバーエラー';
      case 1:
        return 'リクエストの処理中に問題が発生しました。しばらくしてからもう一度お試しください。';
    }

    return '';
  }
}
