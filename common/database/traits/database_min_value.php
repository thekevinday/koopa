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
 * Provide the sql MINVALUE functionality.
 */
trait t_database_min_value {
  protected $min_value;

  /**
   * Set the MINVALUE data type settings.
   *
   * @param int|false|null $value
   *   A number representing the min value.
   *   Set to FALSE for NO MINVALUE.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_min_value($value) {
    if (is_null($value)) {
      $this->min_value = NULL;
      return new c_base_return_true();
    }

    if (is_int($value) || $value === FALSE) {
      $this->min_value = $value;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned min value.
   *
   * @return c_base_return_string|c_base_return_bool|c_base_return_null
   *   A number representing the min value.
   *   FALSE for no min value.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_min_value() {
    if (is_null($this->min_value)) {
      return new c_base_return_null();
    }

    if (is_int($this->min_value)) {
      return c_base_return_int::s_new($this->min_value);
    }
    else if ($this->min_value === FALSE) {
      return c_base_return_bool::s_new($this->min_value);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'min_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_min_value() {
    if ($this->min_value === FALSE) {
      return c_database_string::NO . ' ' . c_database_string::MINVALUE;
    }

    return c_database_string::MINVALUE . ' ' . $this->min_value;
  }
}
