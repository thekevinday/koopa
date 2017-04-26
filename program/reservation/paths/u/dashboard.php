<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/theme/classes/theme_html.php');

class c_reservation_path_user_dashboard extends c_base_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }


    // Wrapper
    $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_base_defaults_global::CSS_BASE . c_base_defaults_global::CSS_BASE . 'content-wrapper', array(c_base_defaults_global::CSS_BASE . 'dashboard-user', 'dashboard-user'));


    // Dashboard Content
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1);
    $tag->set_text($this->pr_get_text(0));
    $wrapper->set_tag($tag);
    unset($tag);

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text($this->pr_get_text(1));
    $wrapper->set_tag($tag);
    unset($tag);

    $roles = array();
    $roles_object = $session->get_setting('roles');
    if ($roles_object instanceof c_base_roles) {
      $roles = $roles_object->get_roles()->get_value_exact();
    }
    unset($roles_object);

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text($this->pr_get_text(2) . ' ' . $settings['database_user']);
    $wrapper->set_tag($tag);
    unset($tag);

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER);
    $tag->set_text($this->pr_get_text(3));
    $wrapper->set_tag($tag);
    unset($tag);

    $tag_ul = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_UNORDERED_LIST);

    foreach ($roles as $role) {
      $tag_li = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LIST_ITEM);

      switch ($role) {
        case c_base_roles::PUBLIC:
          $tag_li->set_text($this->pr_get_text(4));
          break;
        case c_base_roles::USER:
          $tag_li->set_text($this->pr_get_text(5));
          break;
        case c_base_roles::REQUESTER:
          $tag_li->set_text($this->pr_get_text(6));
          break;
        case c_base_roles::DRAFTER:
          $tag_li->set_text($this->pr_get_text(7));
          break;
        case c_base_roles::EDITOR:
          $tag_li->set_text($this->pr_get_text(8));
          break;
        case c_base_roles::REVIEWER:
          $tag_li->set_text($this->pr_get_text(9));
          break;
        case c_base_roles::FINANCER:
          $tag_li->set_text($this->pr_get_text(10));
          break;
        case c_base_roles::INSURER:
          $tag_li->set_text($this->pr_get_text(11));
          break;
        case c_base_roles::PUBLISHER:
          $tag_li->set_text($this->pr_get_text(12));
          break;
        case c_base_roles::AUDITOR:
          $tag_li->set_text($this->pr_get_text(13));
          break;
        case c_base_roles::MANAGER:
          $tag_li->set_text($this->pr_get_text(14));
          break;
        case c_base_roles::ADMINISTER:
          $tag_li->set_text($this->pr_get_text(15));
          break;
      }

      $tag_ul->set_tag($tag_li);
      unset($tag_li);
    }
    unset($role);

    $wrapper->set_tag($tag_ul);

    // initialize the content as HTML.
    $html = c_reservation_build::s_create_html($http, $database, $session, $settings);
    $html->set_tag($wrapper);

    $executed = new c_base_path_executed();
    $executed->set_output($html);
    unset($html);

    return $executed;
  }

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'Dashboard';
      case 1:
        return 'All links will go here.';
      case 2:
        return 'You are currently logged in as:';
      case 3:
        return 'You are currently assigned the following roles:';
      case 4:
        return 'Public';
      case 5:
        return 'User';
      case 6:
        return 'Requester';
      case 7:
        return 'Drafter';
      case 8:
        return 'Editor';
      case 9:
        return 'Reviewer';
      case 10:
        return 'Financer';
      case 11:
        return 'Insurer';
      case 12:
        return 'Publisher';
      case 13:
        return 'Auditor';
      case 14:
        return 'Manager';
      case 15:
        return 'Administer';
    }

    return '';
  }
}
