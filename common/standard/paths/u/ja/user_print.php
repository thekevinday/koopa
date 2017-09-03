<?php
/**
 * @file
 * Provides path handler for the user print.
 */

/**
 * Implements c_standard_path_user_print().
 */
class c_standard_path_user_print_ja extends c_standard_path_user_print {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '';
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
