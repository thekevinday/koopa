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
 * Provide the sql COLUMN SET STATISTICS functionality.
 */
trait t_database_column_set_statistics {
  protected $column_set_statistics;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The column name.
   *   Set to NULL to disable.
   * @param int|null $value
   *   The integer representing the statistics setting.
   *   May be NULL only when column name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_column_set_statistics($name, $value = NULL) {
    if (is_null($name)) {
      $this->column_set_storage = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_int($column_set_statistics)) {
      $placeholder = $this->add_placeholder($name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->column_set_statistics = [
        'name' => $placeholder,
        'value' => $value,
      ];
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned set statistics integer.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the column_set_statistics setting on success.on success.
   *   NULL is returned if not set (is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_column_set_statistics() {
    if (is_null($this->column_set_statistics)) {
      return new c_base_return_null();
    }

    if (is_array($this->column_set_statistics)) {
      return c_base_return_array::s_new($this->column_set_statistics);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_set_statistics', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_column_set_statistics() {
    return c_database_string::COLUMN . ' ' . $this->column_set_statistics['name'] . ' ' . c_database_string::SET . ' ' . c_database_string::STATISTICS . ' ' . $this->column_set_statistics['value'];
  }
}
