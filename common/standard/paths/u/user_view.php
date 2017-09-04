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
require_once('common/standard/classes/standard_path_user.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for user viewing (normal view).
 *
 * This listens on: /u/view
 */
class c_standard_path_user_view extends c_standard_path_user {
  public const PATH_SELF = 'u/view';

  protected const NAME_MENU_CONTENT    = 'menu_content_user_view';
  protected const HANDLER_MENU_CONTENT = 'c_standard_menu_content_user_view';

  protected const CLASS_USER_VIEW_IMAGE       = 'user_view-image';
  protected const CLASS_USER_VIEW_INFORMATION = 'user_view-information';

  protected const IMAGE_CROPPED_HEIGHT = '192';
  protected const IMAGE_CROPPED_WIDTH  = '192';

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

    $this->pr_do_execute_view($executed);

    return $executed;
  }

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    $result = parent::pr_build_breadcrumbs();
    if ($result instanceof c_base_return_false) {
      unset($result);
      return new c_base_return_false();
    }
    unset($result);

    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $this->breadcrumbs = new c_base_menu_item();
    }

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text(0), self::PATH_SELF);
    $this->breadcrumbs->set_item($item);
    unset($item);

    return new c_base_return_true();
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
        $string = 'Picture';
        break;
      case 2:
        $string = 'User Information';
        break;
      case 3:
        $string = 'No Image Available';
        break;
      case 4:
        $string = 'Profile Picture';
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
  protected function pr_do_execute_view(&$executed) {
    $errors = NULL;

    $arguments = array();
    $arguments[':{user_name}'] = $this->path_user->get_name_human()->get_first()->get_value_exact() . ' ' . $this->path_user->get_name_human()->get_last()->get_value_exact();
    if (mb_strlen($arguments[':{user_name}']) == 0) {
      unset($arguments[':{user_name}']);
    }

    $roles_current = $this->session->get_user_current()->get_roles()->get_value_exact();
    $is_private = $this->path_user->is_private()->get_value_exact();

    $full_view_access = FALSE;
    if ($this->path_user_id === $this->session->get_user_current()->get_id()->get_value_exact()) {
      $full_view_access = TRUE;
    }
    elseif (isset($roles_current[c_base_roles::MANAGER]) || isset($roles_current[c_base_roles::ADMINISTER])) {
      $full_view_access = TRUE;
    }
    unset($roles_current);


    // do not show pages for private users.
    if ($is_private && !$full_view_access) {
      unset($is_private);
      unset($full_view_access);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);
      unset($error);

      return $executed;
    }


    // initialize the content as HTML.
    $this->pr_create_html(TRUE, $arguments);

    if (is_int($this->path_user_id)) {
      $text_id_user = $this->pr_create_tag_text('[id: ' . $this->path_user_id . ']', array(), NULL, static::CLASS_ID_USER);
      $wrapper = $this->pr_create_tag_section(array(1 => array('text' => 0, 'append-inside' => $text_id_user)), $arguments);
      unset($text_id_user);
    }
    else {
      $wrapper = $this->pr_create_tag_section(array(1 => 0), $arguments);
    }

    $this->html->set_tag($wrapper);
    unset($wrapper);
    unset($arguments);


    // profile picture
    $fieldset = $this->pr_create_tag_fieldset(1, array(), static::CLASS_USER_VIEW_IMAGE, static::CLASS_USER_VIEW_IMAGE);

    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, array(static::CSS_AS_FIELD_SET_CONTENT));

    $image = $this->path_user->get_image_cropped()->get_value();
    if (is_int($image)) {
      // @todo: image file paths should be stored as constants in a base or standard class, such as 'f' and 'i' for the '/f/i/' uri.
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_IMAGE);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE, $this->settings['base_path'] . 'f/i/' . $image);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH, self::IMAGE_CROPPED_WIDTH);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT, self::IMAGE_CROPPED_HEIGHT);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_ALTERNATE, $this->pr_get_text(4));
      $content->set_tag($tag);
      unset($tag);
    }
    else {
      $tag = c_theme_html::s_create_tag_text(c_base_markup_tag::TYPE_SPAN, $this->pr_get_text(3));
      $content->set_tag($tag);
      unset($tag);
    }
    unset($image);

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // account information
    $fieldset = $this->pr_create_tag_fieldset(2, array(), static::CLASS_USER_VIEW_INFORMATION, static::CLASS_USER_VIEW_INFORMATION);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, array(static::CSS_AS_FIELD_SET_CONTENT));

    // @todo: implement basic user profile information.
    $tag = c_theme_html::s_create_tag_text(c_base_markup_tag::TYPE_SPAN, $this->path_user->get_name_human()->get_first()->get_value() . ' ' . $this->path_user->get_name_human()->get_last()->get_value());
    $content->set_tag($tag);
    unset($tag);

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    // remaining additions
    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);

    return $errors;
  }
}
