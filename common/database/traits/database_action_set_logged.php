<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * Provide the sql action SET LOGGED / SET UNLOGGED functionality.
 */
trait t_database_action_set_logged {
  protected $action_set_logged;

  /**
   * Assign the settings.
   *
   * @param bool|null $logged
   *   Set to TRUE for logged.
   *   Set to FALSE for unlogged.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_set_logged($logged) {
    if (is_null($logged)) {
      $this->action_set_logged = NULL;
      return new c_base_return_true();
    }

    if (is_bool($logged)) {
      $this->action_set_logged = $logged;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'logged', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned start with value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A number representing the start with value.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_set_logged() {
    if (is_null($this->action_set_logged)) {
      return new c_base_return_null();
    }

    if (is_bool($this->action_set_logged)) {
      return c_base_return_bool::s_new($this->action_set_logged);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_set_logged', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_action_set_logged() {
    if ($this->action_set_logged) {
      return c_database_string::SET . ' ' . c_database_string::LOGGED;
    }

    return c_database_string::SET . ' ' . c_database_string::UNLOGGED;
  }
}
