<?php
/**
 * @file
 * Provides path handler for the not found pages.
 */

/**
 * Implements c_standard_path_not_found().
 */
final class c_standard_path_not_found_ja extends c_standard_path_not_found {

  /**
   * Implements pr_get_text_breadcrumbs().
   */
  protected function pr_get_text_breadcrumbs($code, $arguments = array()) {
    switch ($code) {
      case 0:
        $string = 'ホームページ';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '見つかりません';
        break;
      case 1:
        $string = 'リクエストしたページは利用できません。';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
