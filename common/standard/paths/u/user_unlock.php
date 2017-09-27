<?php
/**
 * @file
 * Provides path handler for the user unlock.
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
 * Provides a path handler for user unlocking.
 *
 * This listens on: /u/unlock
 */
class c_standard_path_user_unlock extends c_standard_path_user {
  public const PATH_SELF = 'u/unlock';

  protected const NAME_MENU_CONTENT    = 'menu_content_user_view';
  protected const HANDLER_MENU_CONTENT = '\n_koopa\c_standard_menu_content_user_view';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = []) {
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
      $error = c_base_error::s_log(NULL, ['arguments' => [':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);
      unset($error);

      return $executed;
    }

    // @todo: this function needs to check to see if the user has administer (or manager?) roles (c_base_roles::MANAGER, c_base_roles::ADMINISTER) and if they do, set administrative to TRUE when calling do_load().
    #$user = $this->session->get_user_current();
    #$roles_current = $user->get_roles()->get_value_exact();

    // @todo: this function is currently disabled, so return a path not found.
    $error = c_base_error::s_log(NULL, ['arguments' => [':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::NOT_FOUND_PATH);
    $executed->set_error($error);
    unset($error);

    return $executed;
  }

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
          $string = 'Unlock User: :{user_name}';
        }
        else {
          $string = 'Unlock User';
        }
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
