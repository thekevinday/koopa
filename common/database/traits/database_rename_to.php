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
 * Provide the sql RENAME TO functionality.
 */
trait t_database_rename_to {
  protected $rename_to;

  /**
   * Assign the settings.
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
    if (is_null($rename_to)) {
      $this->rename_to = NULL;
      return new c_base_return_true();
    }

    if (!is_string($rename_to)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'rename_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($rename_to);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->rename_to = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A name query placeholder.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_rename_to() {
    if (is_null($this->rename_to)) {
      return new c_base_return_null();
    }

    if (isset($this->rename_to)) {
      return clone($this->rename_to);
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
   *   A string is returned.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_rename_to() {
    return c_database_string::RENAME . ' ' . c_database_string::TO . ' ' . $this->rename_to;
  }
}
