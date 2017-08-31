<?php
/**
 * @file
 * Provides path handler for the user view.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_database.php');

require_once('common/standard/classes/standard_path.php');
require_once('common/standard/paths/u/user_settings.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for user viewing (normal view).
 *
 * This listens on: /u/view
 */
class c_standard_path_user_view extends c_standard_path_user_settings {
  protected const PATH_SELF = 'u/view';

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'View User: :{user_name}';
        }
        else {
          $string = 'View User';
        }
        break;
      default:
        // otherwise
        return parent::pr_get_text($code, $arguments);
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
