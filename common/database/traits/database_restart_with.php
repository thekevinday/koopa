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
 * Provide the sql RESTART WITH functionality.
 */
trait t_database_restart_with {
  protected $restart_with;

  /**
   * Set the RESTART WITH settings.
   *
   * @param int|null $value
   *   A number representing the start with value.
   *   Set to FALSE to use the default start with value.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_restart_with($value) {
    if (is_null($value)) {
      $this->restart_with = NULL;
      return new c_base_return_true();
    }

    if (is_int($value) || $value === FALSE) {
      $this->restart_with = $value;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned restart with value.
   *
   * @return c_base_return_int|c_base_return_bool|c_base_return_null
   *   A number representing the start with value.
   *   FALSE is returned when default START WITH value is to be used.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_restart_with() {
    if (is_null($this->restart_with)) {
      return new c_base_return_null();
    }

    if (is_int($this->restart_with)) {
      return c_base_return_int::s_new($this->restart_with);
    }
    else if (is_bool($this->restart_with)) {
      return c_base_return_bool::s_new($this->restart_with);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'restart_with', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned on success.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_restart_with() {
    return c_database_string::RESTART_WITH . ' ' . $this->restart_with;
  }
}
