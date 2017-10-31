<?php
/**
 * @file
 * Provides path handler for the user create/copy.
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
 * By supplying a user id argument, this create instead functions as a create from the given user (aka: copy).
 *
 * This listens on: /u/create
 */
class c_standard_path_user_create extends c_standard_path_user {
  public const PATH_SELF = 'u/create';

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    $path_user_view = new c_standard_path_user_view();
    $path_user_view->set_parameters($this->http, $this->database, $this->session, $this->settings);
    $path_user_view->set_path_tree($this->get_path_tree($this->path_tree));
    $this->breadcrumbs = $path_user_view->get_breadcrumbs();
    unset($path_user_view);

    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $result = parent::pr_build_breadcrumbs();
      if ($result instanceof c_base_return_false) {
        unset($result);
        return new c_base_return_false();
      }
      unset($result);
    }

    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $this->breadcrumbs = new c_base_menu_item();
    }

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text(0), self::PATH_SELF);
    $this->breadcrumbs->set_item($item);
    unset($item);

    return new c_base_return_true();
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
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'Copy User: :{user_name}';
        }
        else {
          $string = 'Create User';
        }
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
