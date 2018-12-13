<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * Provide the sql NAME functionality.
 */
trait t_database_oid {
  protected $oid;

  /**
   * Set the OID settings.
   *
   * @param string|null $oid
   *   The oid to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_oid($oid) {
    if (is_null($oid)) {
      $this->oid = NULL;
      return new c_base_return_true();
    }

    if (is_string($oid)) {
      $this->oid = $oid;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'oid', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned oid.
   *
   * @return c_base_return_string|c_base_return_null
   *   A oid on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_oid() {
    if (is_null($this->oid)) {
      return new c_base_return_null();
    }

    if (is_string($this->oid)) {
      return c_base_return_string::s_new($this->oid);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'oid', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_oid() {
    return $this->oid;
  }
}
