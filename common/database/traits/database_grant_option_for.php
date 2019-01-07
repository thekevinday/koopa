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
 * Provide the sql GANT OPTION FOR functionality.
 */
trait t_database_grant_option_for {
  protected $grant_option_for;

  /**
   * Set the GANT OPTION FOR value.
   *
   * @param bool|null $grant_option_for
   *   Set to TRUE for GANT OPTION FOR.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_grant_option_for($grant_option_for) {
    if (is_null($grant_option_for)) {
      $this->grant_option_for = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($grant_option_for)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'grant_option_for', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->grant_option_for = $grant_option_for;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned grant option for value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for GANT OPTION FOR on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_grant_option_for() {
    if (is_null($this->grant_option_for)) {
      return new c_base_return_null();
    }

    if (is_bool($this->grant_option_for)) {
      return c_base_return_bool::s_new($this->grant_option_for);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'grant_option_for', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_grant_option_for() {
    return $this->grant_option_for ? c_database_string::GRANT_OPTION_FOR : NULL;
  }
}
