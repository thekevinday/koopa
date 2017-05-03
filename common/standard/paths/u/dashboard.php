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

class c_standard_path_user_dashboard extends c_standard_path {
  protected const PATH_DASHBOARD_USER = 'u/dashboard';

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

    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));

    $roles = array();
    $current_user = $session->get_user_current();
    #$session_user = $session->get_user_session();


    $roles = array();
    if ($current_user instanceof c_base_users_user) {
      $roles = $current_user->get_roles()->get_value_exact();
    }
    unset($current_user);
    #unset($session_user);

    $wrapper->set_tag($this->pr_create_tag_text_block($this->pr_get_text(2, array('@{user}' => $session->get_name()->get_value_exact()))));

    $block = $this->pr_create_tag_text_block(NULL);
    $block->set_tag($this->pr_create_tag_text(3));

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

    $block->set_tag($tag_ul);
    unset($tag_ul);

    $wrapper->set_tag($block);
    unset($block);

    // initialize the content as HTML.
    $this->pr_create_html();
    $this->html->set_tag($wrapper);

    $executed->set_output($this->html);
    unset($this->html);

    return $executed;
  }

  /**
   * Implementation of pr_create_html_add_header_link_canonical().
   */
  protected function pr_create_html_add_header_link_canonical() {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . self::PATH_DASHBOARD_USER);
    $this->html->set_header($tag);

    unset($tag);
  }

  /**
   * Implements pr_get_title().
   */
  protected function pr_get_title($arguments = array()) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    switch ($code) {
      case 0:
        $string = 'Dashboard';
        break;
      case 1:
        $string = 'All links will go here.';
        break;
      case 2:
        $string = 'You are currently logged in as: @{user}.';
        break;
      case 3:
        $string = 'You are currently assigned the following roles:';
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
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
