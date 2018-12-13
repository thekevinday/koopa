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

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql PROCEDURAL functionality.
 */
trait t_database_procedural {
  protected $procedural;

  /**
   * Set the PROCEDURAL value.
   *
   * @param bool|null $procedural
   *   Set to TRUE for PROCEDURAL.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_procedural($procedural) {
    if (is_null($procedural)) {
      $this->procedural = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($procedural)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'procedural', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->procedural = $procedural;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned with grant option value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for PROCEDURAL on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_procedural() {
    if (is_null($this->procedural)) {
      return new c_base_return_null();
    }

    if (is_bool($this->procedural)) {
      return c_base_return_bool::s_new($this->procedural);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'procedural', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_procedural() {
    return $this->procedural ? c_database_string::PROCEDURAL : NULL;
  }
}
