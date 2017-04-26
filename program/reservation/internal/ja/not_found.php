<?php
/**
 * @file
 * Provides path handler for the not found pages.
 */

/**
 * Implements c_reservation_path_not_found().
 */
final class c_reservation_path_not_found_ja extends c_reservation_path_not_found {

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
        return '見つかりません';
      case 1:
        return 'リクエストしたページは利用できません。';
    }

    return '';
  }
}
