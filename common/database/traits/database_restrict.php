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
 * Provide the sql RESTRICT functionality.
 */
trait t_database_restrict {
  protected $restrict;

  /**
   * Assign the settings.
   *
   * @param bool|null $restrict
   *   Set to TRUE for RESTRICT.
   *   Set to FALSE for not using RESTICT.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_restrict($restrict) {
    if (is_null($restrict)) {
      $this->restrict = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($restrict)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'restrict', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->restrict = $restrict;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned restrict status.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for RESTRICT or FALSE for not using RESTRICT on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_restrict() {
    if (is_null($this->restrict)) {
      return new c_base_return_null();
    }

    if (is_bool($this->restrict)) {
      return c_base_return_bool::s_new($this->restrict);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'restrict', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_restrict() {
    return $this->restrict ? c_database_string::RESTRICT : NULL;
  }
}
