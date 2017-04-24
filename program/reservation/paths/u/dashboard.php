<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_session.php');

require_once('common/theme/classes/theme_html.php');

final class c_reservation_path_user_dashboard extends c_base_path {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->is_root = TRUE;
  }

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, &$html, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $html, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }


    // Wrapper
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'dashboard-user', 'dashboard-user'));


    // Dashboard Content
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text('Dashboard');
    $wrapper->set_tag($tag);
    unset($tag);

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text('All links will go here.');
    $wrapper->set_tag($tag);
    unset($tag);

    $roles = array();
    $roles_object = $session->get_setting('roles');
    if ($roles_object instanceof c_base_roles) {
      $roles = $roles_object->get_roles()->get_value_exact();
    }
    unset($roles_object);

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text('You are currently logged in as: ' . $settings['database_user']);
    $wrapper->set_tag($tag);
    unset($tag);

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text('You are currently assigned the following roles:');
    $wrapper->set_tag($tag);
    unset($tag);

    $tag_ul = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_UNORDERED_LIST);

    foreach ($roles as $role) {
      $tag_li = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LIST_ITEM);

      switch ($role) {
        case c_base_roles::PUBLIC:
          $tag_li->set_text('Public');
          break;
        case c_base_roles::USER:
          $tag_li->set_text('User');
          break;
        case c_base_roles::REQUESTER:
          $tag_li->set_text('Requester');
          break;
        case c_base_roles::DRAFTER:
          $tag_li->set_text('Drafter');
          break;
        case c_base_roles::EDITOR:
          $tag_li->set_text('Editor');
          break;
        case c_base_roles::REVIEWER:
          $tag_li->set_text('Reviewer');
          break;
        case c_base_roles::FINANCER:
          $tag_li->set_text('Financer');
          break;
        case c_base_roles::INSURER:
          $tag_li->set_text('Insurer');
          break;
        case c_base_roles::PUBLISHER:
          $tag_li->set_text('Publisher');
          break;
        case c_base_roles::AUDITOR:
          $tag_li->set_text('Auditor');
          break;
        case c_base_roles::MANAGER:
          $tag_li->set_text('Manager');
          break;
        case c_base_roles::ADMINISTER:
          $tag_li->set_text('Administer');
          break;
      }

      $tag_ul->set_tag($tag_li);
      unset($tag_li);
    }
    unset($role);

    $wrapper->set_tag($tag_ul);


    $html->set_tag($wrapper);
    unset($wrapper);

    return $executed;
  }
}
