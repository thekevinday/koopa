<?php
/**
 * @file
 * Provides path handler for submit forms.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for files by checksum.
 *
 * This listens on: /s/form_id
 */
class c_standard_path_submit_form_id extends c_standard_path {
  public const PATH_SELF = 's/form_id';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = []) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    // @todo

    return $executed;
  }
}
