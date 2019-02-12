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

/**
 * Provide the sql RENAME COLUMN functionality.
 */
trait t_database_rename_column {
  protected $rename_column;

  /**
   * Assign the settings.
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

    $placeholder_from = $this->add_placeholder($from_name);
    if ($placeholder_from->has_error()) {
      return c_base_return_error::s_false($placeholder_from->get_error());
    }

    $placeholder_to = $this->add_placeholder($to_name);
    if ($placeholder_to->has_error()) {
      unset($placeholder_from);
      return c_base_return_error::s_false($placeholder_to->get_error());
    }

    $this->rename_column = [
      'from' => $placeholder_from,
      'to' => $placeholder_to,
    ];
    unset($placeholder_from);
    unset($placeholder_to);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned rename from settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing rename from settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_rename_column() {
    if (is_null($this->rename_column)) {
      return new c_base_return_null();
    }

    if (is_array($this->rename_column)) {
      return c_base_return_array::s_new($this->rename_column);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'rename_column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_rename_column() {
    return c_database_string::RENAME . ' ' . c_database_string::COLUMN . ' ' . $this->rename_column['from'] . ' ' . c_database_string::TO . ' ' . $this->rename_column['to'];
  }
}
