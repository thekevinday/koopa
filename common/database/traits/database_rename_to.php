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
 * Provide the sql RENAME TO functionality.
 */
trait t_database_rename_to {
  protected $rename_to;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $rename_to
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_rename_to($rename_to) {
    if (!is_null($rename_to) && !is_string($rename_to)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'rename_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->rename_to = $rename_to;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_rename_to() {
    if (is_null($this->rename_to)) {
      return new c_base_return_null();
    }

    if (is_string($this->rename_to)) {
      return c_base_return_string::s_new($this->rename_to);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'rename_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_rename_to() {
    if (is_null($this->rename_to)) {
      return NULL;
    }

    return c_database_string::RENAME_TO . ' ' . $this->rename_to;
  }
}
