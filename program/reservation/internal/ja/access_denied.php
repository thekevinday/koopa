<?php
/**
 * @file
 * Provides path handler for the access denied pages.
 */

final class c_reservation_path_access_denied_ja extends c_reservation_path_access_denied {
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
        return 'アクセス拒否';
      case 1:
        return 'このリソースにアクセスする権限がありません。';
    }

    return '';
  }
}
