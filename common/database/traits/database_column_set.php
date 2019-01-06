<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with SET index attribute_option.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_attribute_option.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql COLUMN .. SET functionality.
 */
trait t_database_column_set {
  protected $column_set;

  /**
   * Set the SET index (attribute_option ...) settings.
   *
   * @param int|null $attribute_option
   *   The attribute option code to assign.
   *   Should be one of: e_database_attribute_option.
   *   When both this and $name are NULL, then column reset is disabled.
   * @param string|null $value
   *   The value associated with the parameter.
   *   May be NULL only when $attribute_option or $name is NULL.
   * @param string|null $name
   *   The column name.
   *   Must be specified before any attributes may be assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_column_set($attribute_option, $value = NULL, $name = NULL) {
    if (is_null($name) && is_null($attribute_option)) {
      $this->column_set = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name) && !isset($this->column_set['name'])) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($attribute_option) {
      case e_database_attribute_option::N_DISTINCT:
      case e_database_attribute_option::N_DISTINCT_INHERITED:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_null($attribute_option) && !is_string($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_string($name)) {
      if (is_array($this->column_set)) {
        $this->column_set['name'] = $name;
      }
      else {
        $this->column_set = [
          'name' => $name,
          'values' => [],
        ];
      }
    }

    if (!is_null($attribute_option)) {
      $this->column_set['values'][] = [
        'type' => $attribute_option,
        'value' => $value,
      ];
    }

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned COLUMN .. SET index attribute option at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the index attribute option type at the specified index.
   *   When NULL, all index attribute option types are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the set index attribute option settings on success.
   *   NULL is returned if not set (set index attribute option not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_column_set($index = NULL) {
    if (is_null($this->column_set)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->column_set)) {
        return c_base_return_array::s_new($this->column_set);
      }
    }
    else {
      if (is_int($index) && isset($index, $this->column_set['values']) && is_array($this->column_set['values'][$index])) {
        return c_base_return_array::s_new($this->column_set['values'][$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_set[values][index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_set', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_column_set() {
    $values = [];
    foreach ($this->column_set['values'] as $attribute_option => $value) {
      if ($attribute_option === e_database_attribute_option::N_DISTINCT) {
        $values[] = c_database_string::N_DISTINCT . ' = ' . $value;
      }
      else if ($attribute_option === e_database_attribute_option::N_DISTINCT_INHERITED) {
        $values[] = c_database_string::N_DISTINCT_INHERITED . ' = ' . $value;
      }
    }
    unset($attribute_option);
    unset($value);

    return c_database_string::COLUMN . ' ' . $this->column_set['name'] . ' ' . c_database_string::SET . ' ' . implode(', ', $values);
  }
}