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
 * Provide the sql RENAME COLUMN functionality.
 */
trait t_database_rename_column {
  protected $rename_column;

  /**
   * Set the RENAME COLUMN settings.
   *
   * @param string|null $from_name
   *   The column name to rename from.
   *   Set to NULL to disable.
   * @param string|null $to_name
   *   The column name to rename to.
   *   Required when $from_name is not NULL.
   *   Ignored when $from_name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_rename_column($from_name, $to_name = NULL) {
    if (is_null($from_name)) {
      $this->rename_column = NULL;
      return new c_base_return_true();
    }

    if (!is_string($from_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'from_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($to_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'to_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->rename_column = [
      'from' => $from_name,
      'to' => $to_name,
    ];
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned name to rename from.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_rename_column_from_name() {
    if (is_null($this->rename_column)) {
      return new c_base_return_null();
    }

    if (is_string($this->rename_column['from'])) {
      return c_base_return_string::s_new($this->rename_column['from']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'rename_column[from]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_rename_column_to_name() {
    if (is_null($this->rename_column)) {
      return new c_base_return_null();
    }

    if (is_string($this->rename_column['to'])) {
      return c_base_return_string::s_new($this->rename_column['to']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'rename_column[to]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_rename_column() {
    if (is_null($this->rename_column)) {
      return NULL;
    }

    return c_database_string::RENAME_COLUMN . ' ' . $this->rename_column['from'] . ' ' . c_database_string::TO . $this->rename_column['to'];
  }
}
