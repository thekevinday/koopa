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
 * Provide the sql * functionality.
 */
trait t_database_wildcard {
  protected $wildcard;

  /**
   * Set the * value.
   *
   * @param bool|null $wildcard
   *   Set to TRUE for '*'.
   *   Set to FALSE to not use '*'.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_wildcard($wildcard) {
    if (is_null($wildcard)) {
      $this->wildcard = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($wildcard)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'wildcard', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->wildcard = $wildcard;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned set with oids status.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for '*' and FALSE for not using '*'.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_wildcard() {
    if (is_null($this->wildcard)) {
      return new c_base_return_null();
    }

    if (is_bool($this->wildcard)) {
      return c_base_return_bool::s_new($this->wildcard);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'wildcard', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_wildcard() {
    return $this->wildcard ? '*' : NULL;
  }
}
