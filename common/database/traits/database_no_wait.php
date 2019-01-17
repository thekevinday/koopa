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
 * Provide the sql NOWAIT functionality.
 */
trait t_database_no_wait {
  protected $no_wait;

  /**
   * Set the NOWAIT value.
   *
   * @param bool|null $no_wait
   *   Set to TRUE for NOWAIT.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_no_wait($no_wait) {
    if (is_null($no_wait)) {
      $this->no_wait = NULL;
      return new c_base_return_true();
    }

    if (is_bool($no_wait)) {
      $this->no_wait = $no_wait;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'no_wait', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned no wait value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for NOWAIT on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_no_wait() {
    if (is_null($this->no_wait)) {
      return new c_base_return_null();
    }

    if (is_bool($this->no_wait)) {
      return c_base_return_bool::s_new($this->no_wait);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'no_wait', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_no_wait() {
    return $this->no_wait ? c_database_string::NO_WAIT : NULL;
  }
}
