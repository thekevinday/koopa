<?php
/**
 * @file
 * Provides path handler for the user session actions.
 *
 * This is generally intended to be used to trigger one or more session actions against a user account or related data.
 * This could be a simple reaction as is common with ajax but could also be a page containing forms.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');
require_once('common/standard/classes/standard_path_user.php');
require_once('common/standard/paths/u/user_view.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for user creation.
 *
 * This listens on: /u/session
 */
class c_standard_path_user_session extends c_standard_path_user {
  public const PATH_SELF = 'u/session';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    if (!$this->pr_process_arguments($executed)) {
      return $executed;
    }

    // only support HTML output unless otherwise needed.
    // @todo: eventually all HTML output will be expected to support at least print and PDF formats (with print being the string 'print').
    if ($this->output_format !== c_base_mime::TYPE_TEXT_HTML) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);
      unset($error);

      return $executed;
    }

    // @todo: this function needs to check to see if the user has administer (or manager?) roles (c_base_roles::MANAGER, c_base_roles::ADMINISTER) and if they do, set administrative to TRUE when calling do_load().
    #$user = $this->session->get_user_current();
    #$roles_current = $user->get_roles()->get_value_exact();

    // @todo: this function is currently disabled, so return a path not found.
    $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
    $executed->set_error($error);
    unset($error);

    return $executed;
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return '';
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }
}
