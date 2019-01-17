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
 * Provide the sql action ALTER COLUMN RESET attribute_option functionality.
 */
trait t_database_action_alter_column_reset {
  protected $action_alter_column_reset;

  /**
   * Set the RESET attribute option settings.
   *
   * @param string|null $column_name
   *   The column name to use.
   *   Set to NULL to disable.
   * @param string|null $option
   *   (optional) The configuration option.
   *   This is required when $column_name is not NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit reset is returned on error.
   */
  public function reset_action_alter_column_reset($column_name, $option = NULL) {
    if (is_null($column_name)) {
      $this->action_alter_column_reset = NULL;
      return new c_base_return_true();
    }

    if (!is_string($option)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
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

    if (!is_array($this->action_alter_column_reset)) {
      $this->action_alter_column_reset = [
        'column_name' => $placeholder_name,
        'values' => [],
      ];
    }

    $this->action_alter_column_reset[] = [
      'option' => $placeholder_option,
    ];
    unset($placeholder_name);
    unset($placeholder_option);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @param int|null $index
   *   (optional) Get the settings at the specified index.
   *   When NULL, all settings are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of settings or NULL if not defined.
   *   A single settings is returned if $index is an integer.
   *   NULL with the error bit reset is returned on error.
   */
  public function get_action_alter_column_reset($index = NULL) {
    if (is_null($this->action_alter_column_reset)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->action_alter_column_reset)) {
        return c_base_return_array::s_new($this->action_alter_column_reset);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->action_alter_column_reset)) {
        return c_base_return_array::s_new($this->action_alter_column_reset[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_alter_column_reset[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_alter_column_reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_alter_column_reset() {
    $value = c_database_string::ALTER_COLUMN . ' ' . $this->action_alter_column_reset['column_name'] . ' ' . c_database_string::RESET;

    $values = [];
    foreach ($this->action_alter_column_reset['values'] as $reset) {
      $values[] = $reset['option'];
    }
    unset($reset);

    return $value . ' ' . implode(', ', $values);
  }
}
