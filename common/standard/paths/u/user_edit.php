<?php
/**
 * @file
 * Provides path handler for the user edit.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_user_edit extends c_standard_path {
  protected const PATH_SELF = 'u/edit';

  protected const CLASS_USER_EDIT_ACCOUNT  = 'user_settings-account';
  protected const CLASS_USER_EDIT_PERSONAL = 'user_settings-personal';
  protected const CLASS_USER_EDIT_ACCESS   = 'user_settings-access';
  protected const CLASS_USER_EDIT_HISTORY  = 'user_settings-history';

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    parent::pr_build_breadcrumbs();

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text_breadcrumbs(1), self::PATH_SELF);
    $this->breadcrumbs->set_item($item);
    unset($item);

    return new c_base_return_true();
  }

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    };

    $this->pr_assign_defaults($http, $database, $session, $settings);

    $id_user = NULL;
    $arguments = $this->pr_get_path_arguments(self::PATH_SELF);
    if (!empty($arguments)) {
      $arguments_total = count($arguments);
      $argument = reset($arguments);

      if (is_numeric($argument)) {
        $id_user = (int) $argument;
      }
      else {
        unset($arguments_total);
        unset($argument);
        unset($id_user);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => self::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
        $executed->set_error($error);

        unset($error);
        unset($arguments);

        return $executed;
      }

      if ($arguments_total > 1) {
        $argument = next($arguments);

        if ($argument == 'print') {
          // @todo: execute custom print function and then return.
        }
        elseif ($argument == 'pdf') {
          // @todo: execute custom pdf function and then return.
        }
        elseif ($argument == 'ps') {
          // @todo: execute custom postscript function and then return.
        }
      }
      unset($arguments_total);
      unset($argument);
      unset($id_user);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => self::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);

      unset($error);
      unset($arguments);

      return $executed;
    }
    unset($arguments);

    if (is_null($id_user)) {
      // load current user
    }
    else {
      // @todo: validate if user exists.unset($arguments_total);

      // @todo: on not found, provide page not found.
    }

    $this->p_do_execute_view($executed, $this->p_get_user_id_current());
    unset($id_user);

    return $executed;
  }

  /**
   * Implementation of pr_create_html_add_header_link_canonical().
   */
  protected function pr_create_html_add_header_link_canonical() {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . self::PATH_SELF);
    $this->html->set_header($tag);

    unset($tag);
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'User Settings';
        break;
      case 1:
        $string = '';
        break;
      case 2:
        $string = '';
        break;
      case 3:
        $string = '';
        break;
      case 4:
        $string = 'Public';
        break;
      case 5:
        $string = 'User';
        break;
      case 6:
        $string = 'Requester';
        break;
      case 7:
        $string = 'Drafter';
        break;
      case 8:
        $string = 'Editor';
        break;
      case 9:
        $string = 'Reviewer';
        break;
      case 10:
        $string = 'Financer';
        break;
      case 11:
        $string = 'Insurer';
        break;
      case 12:
        $string = 'Publisher';
        break;
      case 13:
        $string = 'Auditor';
        break;
      case 14:
        $string = 'Manager';
        break;
      case 15:
        $string = 'Administer';
        break;
      case 16:
        $string = 'Account Information';
        break;
      case 17:
        $string = 'Personal Information';
        break;
      case 18:
        $string = 'Access Information';
        break;
      case 19:
        $string = 'History Information';
        break;
      case 20:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'View User: :{user_name}';
        }
        else {
          $string = 'View User';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Load and return the user argument
   *
   * @param array &$arguments
   *   The array of arguments to process.
   * @param int $arguments_total
   *   The total number of arguments.
   * @param bool &$found
   *   Boolean designating if the path is valid, otherwise page not found is returned.
   *
   * @return int
   *   The user id integer.
   */
  private function p_get_argument_user(&$arguments, $arguments_total, &$found) {
    $argument = 0;
    if ($arguments_total == 1) {
      // @todo: load current user id.
    }
    else {
      $argument = next($arguments);
      if (is_numeric($argument)) {
        $argument = (int) $argument;
      }
      else {
        $found = FALSE;
      }

      // @todo: check the user id in the database.
    }

    // if user id is 0, invalid, or a special case, then provide page not found.
    if ($argument == 0) {
      $found = FALSE;
    }

    return $argument;
  }
}
