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
 * Provide the sql action ALTER COLUMN SET attribute_option functionality.
 */
trait t_database_action_alter_column_set {
  protected $action_alter_column_set;

  /**
   * Set the SET attribute option settings.
   *
   * @param string|null $column_name
   *   The column name to use.
   *   Set to NULL to disable.
   * @param string|null $option
   *   (optional) The configuration option.
   *   This is required when $column_name is not NULL.
   * @param string|null $value
   *   (optional) The configuration options value.
   *   This is required when $column_name is not NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_alter_column_set($column_name, $option = NULL, $value = NULL) {
    if (is_null($column_name)) {
      $this->action_alter_column_set = NULL;
      return new c_base_return_true();
    }

    if (!is_string($option)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder_name = $this->add_placeholder($name);
    if ($placeholder_name->has_error()) {
      return c_base_return_error::s_false($placeholder_name->get_error());
    }

    $placeholder_option = $this->add_placeholder($option);
    if ($placeholder_option->has_error()) {
      unset($placeholder_name);
      return c_base_return_error::s_false($placeholder_option->get_error());
    }

    $placeholder_value = $this->add_placeholder($value);
    if ($placeholder_value->has_error()) {
      unset($placeholder_name);
      unset($placeholder_option);
      return c_base_return_error::s_false($placeholder_value->get_error());
    }

    if (!is_array($this->action_alter_column_set)) {
      $this->action_alter_column_set = [
        'column_name' => $placeholder_name,
        'values' => [],
      ];
    }

    $this->action_alter_column_set[] = [
      'option' => $placeholder_option,
      'value' => $placeholder_value,
    ];
    unset($placeholder_name);
    unset($placeholder_option);
    unset($placeholder_value);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of settings or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_alter_column_set() {
    if (is_null($this->action_alter_column_set)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_alter_column_set)) {
      return c_base_return_array::s_new($this->action_alter_column_set);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_alter_column_set', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_alter_column_set() {
    $value = c_database_string::ALTER_COLUMN . ' ' . $this->action_alter_column_set['column_name'] . ' ' . c_database_string::SET;

    $values = [];
    foreach ($this->action_alter_column_set['values'] as $set) {
      $values[] = $set['option'] . ' = ' . $set['value'];
    }
    unset($set);

    return $value . ' ' . implode(', ', $values);
  }
}
