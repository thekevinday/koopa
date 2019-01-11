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
 * Provide the sql START WITH functionality.
 */
trait t_database_start_with {
  protected $start_with;

  /**
   * Set the START WITH settings.
   *
   * @param int|null $value
   *   A number representing the start with value.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_start_with($value) {
    if (is_null($value)) {
      $this->start_with = NULL;
      return new c_base_return_true();
    }

    if (is_int($value)) {
      $this->start_with = $value;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned start with value.
   *
   * @return c_base_return_string|c_base_return_null
   *   A number representing the start with value.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_start_with() {
    if (is_null($this->start_with)) {
      return new c_base_return_null();
    }

    if (is_int($this->start_with)) {
      return c_base_return_int::s_new($this->start_with);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'start_with', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_start_with() {
    return c_database_string::START_WITH . ' ' . $this->start_with;
  }
}
