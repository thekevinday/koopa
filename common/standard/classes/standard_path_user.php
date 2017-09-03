<?php
/**
 * @file
 * Provides the standard path handling class with user-path specific parts.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_database.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides user-specific extensions to standard paths.
 */
class c_standard_path_user extends c_standard_path {
  protected const ID_USER_MINIMUM = 1000;

  protected const CLASS_ID_USER          = 'id-user';
  protected const CLASS_ID_USER_EXTERNAL = 'id-user-external';

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return $this->pr_get_text(0, $arguments);
  }
}
