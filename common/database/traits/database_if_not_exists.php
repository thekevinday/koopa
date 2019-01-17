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
 * Provide the sql IF NOT EXISTS functionality.
 */
trait t_database_if_not_exists {
  protected $if_not_exists;

  /**
   * Set the IF NOT EXISTS value.
   *
   * @param bool|null $if_not_exists
   *   Set to TRUE for 'IF NOT EXISTS'.
   *   Set to FALSE to not use 'IF NOT EXISTS'.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_if_not_exists($if_not_exists) {
    if (is_null($if_not_exists)) {
      $this->if_not_exists = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($if_not_exists)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'if_not_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->if_not_exists = $if_not_exists;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned set with oids status.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for 'IF NOT EXISTS' and FALSE for not using 'IF NOT EXISTS'.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_if_not_exists() {
    if (is_null($this->if_not_exists)) {
      return new c_base_return_null();
    }

    if (is_bool($this->if_not_exists)) {
      return c_base_return_bool::s_new($this->if_not_exists);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'if_not_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_if_not_exists() {
    return $this->if_not_exists ? c_database_string::IF_NOT_EXISTS : NULL;
  }
}
