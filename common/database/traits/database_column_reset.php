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

require_once('common/database/enumerations/database_attribute_option.php');

/**
 * Provide the sql COLUMN .. RESET (attribute_option ...) functionality.
 */
trait t_database_column_reset {
  protected $column_reset;

  /**
   * Set the COLUMN_RESET (attribute_option ...) settings.
   *
   * @param int|null $attribute_option
   *   The attribute option code to assign.
   *   Should be one of: e_database_attribute_option.
   *   When both this and $name are NULL, then column reset is disabled.
   * @param string|null $name
   *   The column name.
   *   Must be specified before any attributes may be assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_column_reset($attribute_option, $name = NULL) {
    if (is_null($name) && is_null($attribute_option)) {
      $this->column_reset = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name) && !isset($this->column_reset['name'])) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($attribute_option) {
      case e_database_attribute_option::N_DISTINCT:
      case e_database_attribute_option::N_DISTINCT_INHERITED:
        break;
      case NULL:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (is_string($name)) {
      $placeholder = $this->add_placeholder($name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      if (is_array($this->column_reset)) {
        $this->column_reset['name'] = $placeholder;
      }
      else {
        $this->column_reset = [
          'name' => $name,
          'values' => [],
        ];
      }
    }
    unset($placeholder);

    if (is_int($attribute_option)) {
      $this->column_reset['values'][] = $attribute_option;
    }

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned COLUMN_RESET attribute option at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the index attribute option type at the specified index.
   *   When NULL, all index attribute option types are returned.
   *
   * @return c_base_return_array|c_base_return_int|c_base_return_null
   *   A code or an array of codes representing the argument_type on success.
   *   NULL is returned if not set (column_reset tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_column_reset($index = NULL) {
    if (is_null($this->column_reset)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->column_reset)) {
        return c_base_return_array::s_new($this->column_reset);
      }
    }
    else {
      if (is_int($index) && isset($this->column_reset['values'][$index]) && is_int($this->column_reset['values'][$index])) {
        return c_base_return_int::s_new($this->column_reset['values'][$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_reset[values][index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column_reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_column_reset() {
    $values = [];
    foreach ($this->column_reset['values'] as $attribute_option) {
      if ($attribute_option === e_database_attribute_option::N_DISTINCT) {
        $values[] = c_database_string::N_DISTINCT;
      }
      else if ($attribute_option === e_database_attribute_option::N_DISTINCT_INHERITED) {
        $values[] = c_database_string::N_DISTINCT_INHERITED;
      }
    }
    unset($attribute_option);

    return c_database_string::COLUMN . ' ' . $this->column_reset['name'] . ' ' . c_database_string::RESET . ' ' . implode(', ', $values);
  }
}
