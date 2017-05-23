<?php
/**
 * @file
 * Provides path handler for the user pdf.
 */

/**
 * Implements c_standard_path_user_pdf().
 */
class c_standard_path_user_pdf_ja extends c_standard_path_user_pdf {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
