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

require_once('common/database/enumerations/database_grant.php');

/**
 * Provide the sql GRANT functionality.
 * @fixme: doesn't group by support multiple arguments?
 */
trait t_database_grant {
  protected $grant;

  /**
   * Set the GRANT settings.
   *
   * @param int|null $grant
   *   The grant/revoke code to assign.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_grant($grant) {
    if (is_null($grant)) {
      $this->grant = NULL;
      return new c_base_return_true();
    }

    if ($grant === e_database_grant::GRANT || $grant === e_database_grant::REVOKE) {
      $this->grant = $grant;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'grant', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned grant code.
   *
   * @return c_base_return_int|c_base_return_null
   *   A grant code on success.
   *   NULL is returned if not set (grant is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_grant() {
    if (is_null($this->grant)) {
      return new c_base_return_null();
    }

    if (is_int($this->grant)) {
      return c_base_return_int::s_new($this->grant);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'grant', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_grant() {
    $value = NULL;
    if ($this->grant === e_database_grant::GRANT) {
      $value = c_database_string::GRANT;
    }
    else if ($this->grant === e_database_grant::REVOKE) {
      $value = c_database_string::REVOKE;
    }

    return $value;
  }
}
