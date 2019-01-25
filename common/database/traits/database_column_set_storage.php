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

require_once('common/database/enumerations/database_column_set_storage.php');

/**
 * Provide the sql COLUMN .. SET STORAGE functionality.
 */
trait t_database_column_set_storage {
  protected $column_set_storage;

  /**
   * Set the COLUMN .. SET STORAGE settings.
   *
   * @param string|null $name
   *   The column name.
   *   Set to NULL to disable.
   * @param int|null $type
   *   The integer representing the storage setting.
   *   May be NULL only when column name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_column_set_storage($name, $type = NULL) {
    if (is_null($name)) {
      $this->column_set_storage = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($type) {
      case e_database_set_storage::EXTENDED:
      case e_database_set_storage::EXTERNAL:
      case e_database_set_storage::MAIN:
      case e_database_set_storage::PLAIN:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->column_set_storage = [
      'type' => $type,
      'name' => $placeholder,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned column_set_storage.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the column_set_storage setting on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_column_set_storage() {
    if (is_null($this->column_set_storage)) {
      return new c_base_return_null();
    }

    if (is_array($this->column_set_storage)) {
      return c_base_return_array::s_new($this->column_set_storage);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_set_storage', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_column_set_storage() {
    $value = c_database_string::COLUMN . ' ' . $this->column_set_storage['name'] . ' ' . c_database_string::SET_STORAGE . ' ';
    if ($this->column_set_storage['type'] === e_database_column_set_storage::EXTENDED) {
      return $value . c_database_string::EXTENDED;
    }
    else if ($this->column_set_storage['type'] === e_database_column_set_storage::EXTERNAL) {
      return $value . c_database_string::EXTERNAL;
    }
    else if ($this->column_set_storage['type'] === e_database_column_set_storage::MAIN) {
      return $value . c_database_string::MAIN;
    }
    else if ($this->column_set_storage['type'] === e_database_column_set_storage::PLAIN) {
      return $value . c_database_string::PLAIN;
    }
    unset($value);

    return NULL;
  }
}
