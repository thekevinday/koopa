<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');
require_once('common/standard/classes/standard_path_user.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for user dashboard.
 *
 * This listens on: /u/dashboard
 */
class c_standard_path_user_dashboard extends c_standard_path_user {
  public const PATH_SELF = 'u/dashboard';

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
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    // @todo: this function needs to check to see if the user has administer (or manager?) roles (c_base_roles::MANAGER, c_base_roles::ADMINISTER) and if they do, set administrative to TRUE when calling do_load().
    $user = $this->session->get_user_current();
    $roles_current = $user->get_roles()->get_value_exact();

    $id_user = NULL;
    $arguments = $this->pr_get_path_arguments(static::PATH_SELF);
    if (!empty($arguments)) {
      $arguments_total = count($arguments);
      $argument = reset($arguments);

      if (is_numeric($argument)) {
        $id_user = (int) $argument;

        // do not allow view access to reserved/special accounts.
        if ($id_user < static::ID_USER_MINIMUM) {
          $id_user = FALSE;
        }

        // @todo: check to see if user id is valid and accessible.
        //        If the current viewer cannot access the user, then deny access to this page as appropriate.
      }
      else {
        unset($arguments_total);
        unset($argument);
        unset($id_user);
        unset($user);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
        $executed->set_error($error);

        unset($error);
        unset($arguments);

        return $executed;
      }

      if ($arguments_total > 1) {
        $argument = next($arguments);

        if ($argument == 'print') {
          // @todo: execute custom print function and then return.
          $id_user = NULL;
        }
        #elseif ($argument == 'pdf') {
        #  // @todo: execute custom pdf function and then return.
        #  $id_user = NULL;
        #}
        #elseif ($argument == 'ps') {
        #  // @todo: execute custom postscript function and then return.
        #  $id_user = NULL;
        #}
        else {
          $id_user = FALSE;
        }
      }
      unset($arguments_total);
      unset($argument);

      if ($id_user === FALSE) {
        unset($user);
        unset($id_user);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
        $executed->set_error($error);

        unset($error);
        unset($arguments);

        return $executed;
      }
    }

    $user = NULL;
    if (is_null($id_user)) {
      $user = $this->session->get_user_current();
      $id_user = $user->get_id()->get_value_exact();

      // do not allow view access to reserved/special accounts.
      if ($id_user < static::ID_USER_MINIMUM) {
        $id_user = FALSE;
      }
    }
    else {
      $user = new c_standard_users_user();

      // @todo: handle database errors.
      $loaded = $user->do_load($this->database, $id_user);
      if ($loaded instanceof c_base_return_false) {
        $id_user = FALSE;
      }
      unset($loaded);
    }

    if ($id_user === FALSE) {
      unset($id_user);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);

      unset($error);

      return $executed;
    }
    unset($arguments);
    unset($id_user);

    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));

    $roles = $session->get_user_current()->get_roles()->get_value_exact();

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
    unset($wrapper);

    $this->pr_add_menus();

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
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . self::PATH_SELF);
    $this->html->set_header($tag);

    unset($tag);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
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
