<?php
/**
 * @file
 * Provides path handler for the user view.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_user_view extends c_standard_path {
  protected const PATH_SELF = 'u/view';

  protected const ID_USER_MINIMUM = 1000;

  protected const CLASS_USER_VIEW_ACCOUNT  = 'user_settings-account';
  protected const CLASS_USER_VIEW_PERSONAL = 'user_settings-personal';
  protected const CLASS_USER_VIEW_ACCESS   = 'user_settings-access';
  protected const CLASS_USER_VIEW_HISTORY  = 'user_settings-history';

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

    // @todo: this function needs to check to see if the user has administer (or manager?) roles and if they do, set administrative to TRUE when calling do_load().

    $id_user = NULL;
    $arguments = $this->pr_get_path_arguments(self::PATH_SELF);
    if (!empty($arguments)) {
      $arguments_total = count($arguments);
      $argument = reset($arguments);

      if (is_numeric($argument)) {
        $id_user = (int) $argument;

        // do not allow view access to reserved/special accounts.
        if ($id_user < self::ID_USER_MINIMUM) {
          $id_user = NULL;
        }
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
          $id_user = NULL;
        }
        elseif ($argument == 'pdf') {
          // @todo: execute custom pdf function and then return.
          $id_user = NULL;
        }
        elseif ($argument == 'ps') {
          // @todo: execute custom postscript function and then return.
          $id_user = NULL;
        }
        else {
          $id_user = NULL;
        }
      }
      unset($arguments_total);
      unset($argument);

      if (is_null($id_user)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => self::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
        $executed->set_error($error);

        unset($error);
        unset($arguments);

        return $executed;
      }
    }

    if (is_null($id_user)) {
      // load current user.
      $user_current = $this->session->get_user_current();
      if ($user_current instanceof c_base_users_user && $user_current->get_id()->get_value_exact() > 0) {
        $user = $user_current;
      }
      unset($user_current);
    }
    else {
      $user = new c_standard_users_user();

      // @todo: handle database errors.
      $loaded = $user->do_load($this->database, $id_user);
      if ($loaded instanceof c_base_return_false) {
        $user = NULL;
      }
      unset($loaded);
    }
    unset($id_user);

    // user is set to NULL on error.
    if (is_null($user)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => self::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);

      unset($error);

      return $executed;
    }
    unset($arguments);

    $this->p_do_execute_view($executed, $user);
    unset($user);

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
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'View User: :{user_name}';
        }
        else {
          $string = 'View User';
        }
        break;
      case 1:
        $string = 'Public';
        break;
      case 2:
        $string = 'User';
        break;
      case 3:
        $string = 'Requester';
        break;
      case 4:
        $string = 'Drafter';
        break;
      case 5:
        $string = 'Editor';
        break;
      case 6:
        $string = 'Reviewer';
        break;
      case 7:
        $string = 'Financer';
        break;
      case 8:
        $string = 'Insurer';
        break;
      case 9:
        $string = 'Publisher';
        break;
      case 10:
        $string = 'Auditor';
        break;
      case 11:
        $string = 'Manager';
        break;
      case 12:
        $string = 'Administer';
        break;
      case 13:
        $string = 'Account Information';
        break;
      case 14:
        $string = 'Personal Information';
        break;
      case 15:
        $string = 'Access Information';
        break;
      case 16:
        $string = 'History Information';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Execution of the view path.
   *
   * @param c_base_path_executed &$executed
   *   The execution results to be returned.
   * @param c_base_users_user $user_id
   *   An object representing the user to view.
   */
  private function p_do_execute_view(&$executed, $user) {
    $arguments = array();
    $arguments[':{user_name}'] = $user->get_name_human()->get_first()->get_value_exact() . ' ' . $user->get_name_human()->get_last()->get_value_exact();
    if (mb_strlen($arguments[':{user_name}']) == 0) {
      unset($arguments[':{user_name}']);
    }

    $wrapper = $this->pr_create_tag_section(array(1 => 0), $arguments);
    unset($arguments);

    // initialize the content as HTML.
    $this->pr_create_html();
    $this->html->set_tag($wrapper);
    unset($wrapper);


    // account information
    $fieldset = $this->pr_create_tag_fieldset(13, array(), self::CLASS_USER_VIEW_ACCOUNT, self::CLASS_USER_VIEW_ACCOUNT);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // personal information
    $fieldset = $this->pr_create_tag_fieldset(14, array(), self::CLASS_USER_VIEW_PERSONAL, self::CLASS_USER_VIEW_PERSONAL);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // access information
    $fieldset = $this->pr_create_tag_fieldset(15, array(), self::CLASS_USER_VIEW_ACCESS, self::CLASS_USER_VIEW_ACCESS);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // history information
    $fieldset = $this->pr_create_tag_fieldset(16, array(), self::CLASS_USER_VIEW_HISTORY, self::CLASS_USER_VIEW_HISTORY);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // @todo add edit, cancel, etc.. links.


    $executed->set_output($this->html);
    unset($this->html);
  }
}
