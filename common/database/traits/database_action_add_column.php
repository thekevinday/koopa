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
 * Provide the sql action ADD COLUMN functionality.
 */
trait t_database_action_add_column {
  protected $action_add_column;

  /**
   * Set the action ADD COLUMN settings.
   *
   * @param string|null $column_name
   *   The column name to use.
   *   Set to NULL to disable.
   * @param string|null $data_type
   *   (optional) The data type to use.
   *   Required when $column_name is not NULL and $column_constraint is NULL.
   *   Ignored when $column_name is NULL.
   * @param string|null $collate
   *   (optional) The collate to use.
   *   Ignored when $column_name is NULL.
   * @param array|null $column_constraints
   *   (optional) An array of column constraint strings to add.
   *   Ignored when $column_name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_add_column($column_name, $data_type = NULL, $collate = NULL, $column_constraints = NULL) {
    if (is_null($column_name)) {
      $this->action_add_column = NULL;
      return new c_base_return_true();
    }

    if (!is_string($column_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'column_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($data_type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'data_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($collate) && !is_string($collate)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'collate', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($column_constraints) && !is_array($column_constraints)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'column_constraints', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder_column_name = $this->add_placeholder($column_name);
    if ($placeholder_column_name->has_error()) {
      return c_base_return_error::s_false($placeholder_column_name->get_error());
    }

    $placeholder_data_type = NULL;
    if (!is_null($data_type)) {
      $placeholder_data_type = $this->add_placeholder($data_type);
      if ($placeholder_data_type->has_error()) {
        unset($placeholder_column_name);
        return c_base_return_error::s_false($placeholder_data_type->get_error());
      }
    }

    $placeholder_collate = NULL;
    if (!is_null($collate)) {
      $placeholder_collate = $this->add_placeholder($collate);
      if ($placeholder_collate->has_error()) {
        unset($placeholder_column_name);
        unset($placeholder_data_type);
        return c_base_return_error::s_false($placeholder_collate->get_error());
      }
    }

    $placeholder_constraints = NULL;
    if (!is_null($column_constraint)) {
      $placeholder_constraints = [];
      foreach ($column_constraint as $column_constraint) {
        if (!is_string($column_constraint)) {
          unset($column_constraint);
          unset($placeholder_column_name);
          unset($placeholder_data_type);
          unset($placeholder_collate);
          unset($placeholder_constraints);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'column_constraints', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }

        $placeholder_constraint = $this->add_placeholder($column_constraint);
        if ($placeholder_constraint->has_error()) {
          unset($column_constraint);
          unset($placeholder_column_name);
          unset($placeholder_data_type);
          unset($placeholder_collate);
          return c_base_return_error::s_false($placeholder_constraint->get_error());
        }

        $placeholder_constraints[] = $placeholder_constraint;
      }
      unset($column_constraint);
    }

    $this->action_add_column = [
      'column_name' => $placeholder_column_name,
      'data_type' => $placeholder_data_type,
      'collate' => $placeholder_collate,
      'column_constraints' => $placeholder_constraints,
    ];
    unset($placeholder_column_name);
    unset($placeholder_data_type);
    unset($placeholder_collate);
    unset($placeholder_constraints);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned action add column settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_add_column() {
    if (is_null($this->action_add_column)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_add_column)) {
      return c_base_return_array::s_new($this->action_add_column);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_add_column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_add_column() {
    $value = c_database_string::ADD_COLUMN . ' ' . $this->action_add_column['column_name'] . ' ' . $this->action_add_column['data_type'];
    if (!is_null($this->action_add_column['collate'])) {
      $value .= ' ' . $this->action_add_column['collate'];
    }

    if (isset($this->action_add_column['column_constraints'])) {
      $value .= ' ' . implode(' ', $this->action_add_column['column_constraints']);
    }

    return $value;
  }
}
