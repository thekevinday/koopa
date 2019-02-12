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
 * Provide the sql MAXVALUE functionality.
 */
trait t_database_max_value {
  protected $max_value;

  /**
   * Assign the settings.
   *
   * @param int|false|null $value
   *   A number representing the max value.
   *   Set to FALSE for NO MAXVALUE.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_max_value($value) {
    if (is_null($value)) {
      $this->max_value = NULL;
      return new c_base_return_true();
    }

    if (is_int($value) || $value === FALSE) {
      $this->max_value = $value;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned max value.
   *
   * @return c_base_return_string|c_base_return_bool|c_base_return_null
   *   A number representing the max value.
   *   FALSE for no max value.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_max_value() {
    if (is_null($this->max_value)) {
      return new c_base_return_null();
    }

    if (is_int($this->max_value)) {
      return c_base_return_int::s_new($this->max_value);
    }
    else if ($this->max_value === FALSE) {
      return c_base_return_bool::s_new($this->max_value);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'max_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_max_value() {
    if ($this->max_value === FALSE) {
      return c_database_string::NO . ' ' . c_database_string::MAXVALUE;
    }

    return c_database_string::MAXVALUE . ' ' . $this->max_value;
  }
}
