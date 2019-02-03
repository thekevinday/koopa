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

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql action NOT OF functionality.
 */
trait t_database_action_not_of {
  protected $action_not_of;

  /**
   * Set the action NOT OF settings.
   *
   * @param bool|null $not_of
   *   Set to TRUE to enable.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_not_of($not_of) {
    if (is_null($enable)) {
      $this->action_not_of = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($not_of)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'not_of', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->action_not_of = $enable;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned NOT OF setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean representing whether or NOT OF is to be used.
   *   NULL is returned if not set (this is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_not_of() {
    if (is_null($this->action_not_of)) {
      return new c_base_return_null();
    }

    if (is_bool($this->action_not_of)) {
      return c_base_return_bool::s_new($this->action_not_of);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_not_of', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_not_of() {
    return $this->action_not_of ? c_database_string::NOT_OF : NULL;
  }
}
