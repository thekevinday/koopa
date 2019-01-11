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
 * Provide the sql ENABLE functionality.
 */
trait t_database_enable {
  protected $enable;

  /**
   * Set the ENABLE settings.
   *
   * @param bool|null $enable
   *   Set to TRUE for ENABLE.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_enable($enable) {
    if (is_null($enable)) {
      $this->enable = NULL;
      return new c_base_return_true();
    }

    if (is_bool($enable)) {
      $this->enable = $enable;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'enable', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned data type.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean with TRUE repesenting ENABLE.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_enable() {
    if (is_null($this->enable)) {
      return new c_base_return_null();
    }

    if (is_bool($this->enable)) {
      return c_base_return_bool::s_new($this->enable);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'enable', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_enable() {
    if ($this->enable) {
      return c_database_string::ENABLE;
    }

    return NULL;
  }
}
