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
 * Provide the sql INCREMENT BY functionality.
 */
trait t_database_increment_by {
  protected $increment_by;

  /**
   * Set the INCREMENT BY settings.
   *
   * @param int|null $by
   *   A positive or negative number to increment by.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_increment_by($by) {
    if (is_null($by)) {
      $this->increment_by = NULL;
      return new c_base_return_true();
    }

    if (is_int($by)) {
      $this->increment_by = $by;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned increment by value.
   *
   * @return c_base_return_int|c_base_return_null
   *   An increment by number.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_increment_by() {
    if (is_null($this->increment_by)) {
      return new c_base_return_null();
    }

    if (is_int($this->increment_by)) {
      return c_base_return_int::s_new($this->increment_by);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'increment_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_increment_by() {
    return c_database_string::INCREMENT_BY . ' ' . $this->increment_by;
  }
}
