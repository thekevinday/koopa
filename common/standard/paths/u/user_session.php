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
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = []) {
    return '';
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    return '';
  }

  /**
   * Execution of the view path.
   *
   * @param c_base_path_executed &$executed
   *   The execution results to be returned.
   *
   * @return null|array
   *   NULL is returned if no errors are found.
   *   An array of errors are returned if found.
   */
  protected function pr_do_execute_build_content(&$executed) {
    $errors = NULL;


    // initialize the content as HTML.
    $this->pr_create_html(TRUE, $this->arguments);


    // main content.
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, [static::CSS_AS_FIELD_SET_CONTENT]);

    $tag = c_theme_html::s_create_tag_text(c_base_markup_tag::TYPE_SPAN, 'Not Yet Implemented.');
    $content->set_tag($tag);
    unset($tag);


    // build main section.
    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($content);
    unset($content);

    $this->html->set_tag($wrapper);
    unset($content);


    // remaining additions
    $this->pr_add_menus();


    $executed->set_output($this->html);
    unset($this->html);

    return $errors;
  }
}
