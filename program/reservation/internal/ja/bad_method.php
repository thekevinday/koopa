<?php
/**
 * @file
 * Provides path handler for the not found pages.
 */

/**
 * Implements c_reservation_path_bad_method().
 */
final class c_reservation_path_bad_method_ja extends c_reservation_path_bad_method {

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
        return '悪い方法';
      case 1:
        return '指定されたHTTP要求メソッドは、要求パスに対してサポートされていないか無効です。';
    }

    return '';
  }
}
