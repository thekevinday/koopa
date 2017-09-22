<?php
/**
 * @file
 * Provides path handler for the files by id.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for files by id.
 *
 * This listens on: /f/i
 */
class c_standard_path_file_by_checksum extends c_standard_path {
  public const PATH_SELF = 'f/i';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    // @todo

    return $executed;
  }
}
