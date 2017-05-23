<?php
/**
 * @file
 * Provides a class for a standard utility menu.
 *
 * A utility menu is intended to be a menu that provides links to logged in uses.
 * This includes dashboard links, profile, links, etc..
 * This is not intended for site navigation.
 */
require_once('common/base/classes/base_markup.php');

require_once('common/standard/classes/standard_menu.php');
require_once('common/standard/classes/standard_paths.php');

/**
 * A generic class for managing a utility menu.
 */
class c_standard_menu_utility extends c_standard_menu {
  protected const CLASS_NAME                 = 'menu-utility';

  protected const CLASS_HOME                 = 'home';
  protected const CLASS_LOGIN                = 'login';
  protected const CLASS_LOGOUT               = 'logout';
  protected const CLASS_DASHBOARD_USER       = 'dashboard-user';
  protected const CLASS_DASHBOARD_MANAGEMENT = 'dashboard-management';
  protected const CLASS_DASHBOARD_ADMINISTER = 'dashboard-administer';
  protected const CLASS_USER_SETTINGS        = 'settings';

  /**
   * Implements do_prepare().
   */
  public function do_build(&$http, &$database, &$session, $settings, $items = NULL) {
    $result = parent::do_build($http, $database, $session, $settings);
    if (c_base_return::s_has_error($result)) {
      return $result;
    }
    unset($result);

    $roles = array();
    $session_user = $session->get_user_current();
    if ($session_user instanceof c_base_session) {
      $roles = $session_user->get_roles()->get_value_exact();
    }
    unset($session_user);

    $menu = $this->pr_create_html_create_menu($settings['base_css'] . self::CLASS_NAME, $this->pr_get_text(0));

    if ($session->is_logged_in() instanceof c_base_return_true) {
      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(3), $settings['base_path'] . c_standard_paths::URI_DASHBOARD_USER);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_DASHBOARD_USER);
      $menu->set_tag($item);
      unset($item);

      if (array_key_exists(c_base_roles::MANAGER, $roles) || array_key_exists(c_base_roles::ADMINISTER, $roles)) {
        $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(4), $settings['base_path'] . c_standard_paths::URI_DASHBOARD_MANAGEMENT);
        $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_DASHBOARD_MANAGEMENT);
        $menu->set_tag($item);
        unset($item);
      }

      if (array_key_exists(c_base_roles::ADMINISTER, $roles)) {
        $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(5), $settings['base_path'] . c_standard_paths::URI_DASHBOARD_ADMINISTER);
        $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_DASHBOARD_ADMINISTER);
        $menu->set_tag($item);
        unset($item);
      }

      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(6), $settings['base_path'] . c_standard_paths::URI_USER_VIEW);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_USER_SETTINGS);
      $menu->set_tag($item);
      unset($item);

      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(7), $settings['base_path'] . c_standard_paths::URI_LOGOUT);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_LOGOUT);
      $menu->set_tag($item);
      unset($item);
    }
    else {
      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(1), $settings['base_path'] . c_standard_paths::URI_HOME);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_HOME);
      $menu->set_tag($item);
      unset($item);

      $item = $this->pr_create_html_add_menu_item_link($this->pr_get_text(2), $settings['base_path'] . c_standard_paths::URI_LOGIN);
      $item->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, self::CLASS_LOGIN);
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
        $string = 'Utility Menu';
        break;
      case 1:
        $string = 'Home';
        break;
      case 2:
        $string = 'Login';
        break;
      case 3:
        $string = 'Dashboard';
        break;
      case 4:
        $string = 'Management';
        break;
      case 5:
        $string = 'Administration';
        break;
      case 6:
        $string = 'Settings';
        break;
      case 7:
        $string = 'Logout';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
