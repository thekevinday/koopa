<?php
/**
 * @file
 * Provides a class for a path-specific content menu.
 */
require_once('common/base/classes/base_markup.php');

require_once('common/standard/classes/standard_menu.php');
require_once('common/standard/classes/standard_paths.php');
require_once('common/standard/menus/menu_content.php');

/**
 * A path-specific class for managing a content menu.
 */
class c_standard_menu_content_user_view extends c_standard_menu_content {
  protected const CLASS_USER_CHECK     = 'user-check';
  protected const CLASS_USER_CREATE    = 'user-create';
  protected const CLASS_USER_COPY      = 'user-copy';
  protected const CLASS_USER_DASHBOARD = 'user-dashboard';
  protected const CLASS_USER_DELETE    = 'user-delete';
  protected const CLASS_USER_EDIT      = 'user-edit';
  protected const CLASS_USER_LOCK      = 'user-lock';
  protected const CLASS_USER_REFRESH   = 'user-refresh';
  protected const CLASS_USER_SETTINGS  = 'user-settings';
  protected const CLASS_USER_UNLOCK    = 'user-unlock';
  protected const CLASS_USER_VIEW      = 'user-view';

  /**
   * Implements do_build().
   */
  public function do_build(&$http, &$database, &$session, $settings, $items = NULL) {
    $result = parent::do_build($http, $database, $session, $settings);
    if (c_base_return::s_has_error($result)) {
      return $result;
    }
    unset($result);

    if ($session->is_logged_in() instanceof c_base_return_false) {
      return new c_base_return_false();
    }

    // @todo: this path should either have no trailing id if current user is viewing their own profile or it should have the user id appended for all urls below.
    $path_id_user = '';

    $menu = $this->pr_create_html_create_menu($settings['base_css'] . static::CLASS_NAME, $this->pr_get_text(0));

    $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(1), $settings['base_path'] . c_standard_paths::URI_USER_VIEW . $path_id_user);
    $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_VIEW);
    $menu->set_tag($item);
    unset($item);

    $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(2), $settings['base_path'] . c_standard_paths::URI_USER_DASHBOARD . $path_id_user);
    $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_DASHBOARD);
    $menu->set_tag($item);
    unset($item);

    $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(3), $settings['base_path'] . c_standard_paths::URI_USER_SETTINGS . $path_id_user);
    $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_SETTINGS);
    $menu->set_tag($item);
    unset($item);

    // @todo: add access check to this menu item and only make it appear to authorized users.
    $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(4), $settings['base_path'] . c_standard_paths::URI_USER_EDIT . $path_id_user);
    $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_EDIT);
    $menu->set_tag($item);
    unset($item);

    $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(5), $settings['base_path'] . c_standard_paths::URI_USER_CHECK . $path_id_user);
    $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_CHECK);
    $menu->set_tag($item);
    unset($item);

    $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(6), $settings['base_path'] . c_standard_paths::URI_USER_REFRESH . $path_id_user);
    $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_REFRESH);
    $menu->set_tag($item);
    unset($item);

    $roles = $session->get_user_current()->get_roles()->get_value_exact();
    if (array_key_exists(c_base_roles::MANAGER, $roles) || array_key_exists(c_base_roles::ADMINISTER, $roles)) {
      // @todo: only show lock user if account is unlocked.
      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(7), $settings['base_path'] . c_standard_paths::URI_USER_LOCK . $path_id_user);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_LOCK);
      $menu->set_tag($item);
      unset($item);

      // @todo: only show unlock user if account is locked.
      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(8), $settings['base_path'] . c_standard_paths::URI_USER_UNLOCK . $path_id_user);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_UNLOCK);
      $menu->set_tag($item);
      unset($item);

      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(9), $settings['base_path'] . c_standard_paths::URI_USER_DELETE . $path_id_user);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_DELETE);
      $menu->set_tag($item);
      unset($item);

      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(10), $settings['base_path'] . c_standard_paths::URI_USER_COPY . $path_id_user);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_COPY);
      $menu->set_tag($item);
      unset($item);

      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(11), $settings['base_path'] . c_standard_paths::URI_USER_CREATE);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_USER_CREATE);
      $menu->set_tag($item);
      unset($item);
    }
    unset($roles);

    return $menu;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'User Menu';
        break;
      case 1:
        $string = 'Profile';
        break;
      case 2:
        $string = 'Dashboard';
        break;
      case 3:
        $string = 'Settings';
        break;
      case 4:
        $string = 'Edit';
        break;
      case 5:
        $string = 'Check';
        break;
      case 6:
        $string = 'Refresh';
        break;
      case 7:
        $string = 'Lock';
        break;
      case 8:
        $string = 'Unlock';
        break;
      case 9:
        $string = 'Delete';
        break;
      case 10:
        $string = 'Copy'; // @todo: implement this in the project.
        break;
      case 11:
        $string = 'Create New User';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
