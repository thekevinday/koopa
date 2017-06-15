<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_user_settings extends c_standard_path {
  protected const PATH_SELF = 'u/settings';

  protected const CLASS_USER_SETTINGS_ACCOUNT  = 'user_settings-account';
  protected const CLASS_USER_SETTINGS_PERSONAL = 'user_settings-personal';
  protected const CLASS_USER_SETTINGS_ACCESS = 'user_settings-access';
  protected const CLASS_USER_SETTINGS_HISTORY = 'user_settings-history';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    };

    $arguments = $this->pr_get_path_arguments(self::PATH_SELF);
    if (!empty($arguments)) {
      // @todo: return $this->p_do_execute_X($executed);
    }

    $this->p_do_execute($executed);

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
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Execution of the main path, without arguments.
   *
   * @param c_base_path_executed &$executed
   *   The execution results to be returned.
   */
  private function p_do_execute(&$executed) {
    $wrapper = $this->pr_create_tag_section(array(1 => 0));

    // initialize the content as HTML.
    $this->pr_create_html();
    $this->html->set_tag($wrapper);
    unset($wrapper);


    // account information
    $fieldset = $this->pr_create_tag_fieldset(16, array(), self::CLASS_USER_SETTINGS_ACCOUNT, self::CLASS_USER_SETTINGS_ACCOUNT);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // personal information
    $fieldset = $this->pr_create_tag_fieldset(17, array(), self::CLASS_USER_SETTINGS_PERSONAL, self::CLASS_USER_SETTINGS_PERSONAL);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // access information
    $fieldset = $this->pr_create_tag_fieldset(18, array(), self::CLASS_USER_SETTINGS_ACCESS, self::CLASS_USER_SETTINGS_ACCESS);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, self::CSS_AS_FIELD_SET_CONTENT, array(self::CSS_AS_FIELD_SET_CONTENT));

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // history information
    $fieldset = $this->pr_create_tag_fieldset(18, array(), self::CLASS_USER_SETTINGS_HISTORY, self::CLASS_USER_SETTINGS_HISTORY);
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
