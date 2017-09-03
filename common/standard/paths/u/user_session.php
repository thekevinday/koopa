<?php
/**
 * @file
 * Provides path handler for the user session actions.
 *
 * This is generally intended to be used to trigger one or more session actions against a user account or related data.
 * This could be a simple reaction as is common with ajax but could also be a page containing forms.
 */

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

    // @todo: json responses are expected to be returned for ajax purposes.
    //        this will very likely support u/session/(ajax_action_name) such as u/session/ping for keeping the session and therefore session cookie alive.

    return $executed;
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = array()) {
    return '';
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }
}
