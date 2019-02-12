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

require_once('common/database/interfaces/database_query_placeholder.php');

/**
 * Provide the sql GROUP BY functionality.
 */
trait t_database_column_names {
  protected $column_names;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The column name to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_column_names($name) {
    if (is_null($name)) {
      $this->column_names = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->column_names)) {
      $this->column_names = [];
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->column_names[] = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of group by values or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_column_names() {
    if (is_null($this->column_names)) {
      return new c_base_return_null();
    }

    if (is_array($this->column_names)) {
      return c_base_return_array::s_new($this->column_names);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_names', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_column_names() {
    return implode(', ', $this->column_names);
  }
}
