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

require_once('common/base/enumerations/database_alter_column.php');

/**
 * Provide the sql action ALTER COLUMN functionality.
 */
trait t_database_action_alter_column {
  protected $action_alter_column;

  /**
   * Set the action ALTER COLUMN settings.
   *
   * @param string|null $column_name
   *   The column name to use.
   *   Set to NULL to disable.
   * @param int|null $type
   *   (optional) An e_database_alter_column code.
   *   Required when $column_name is not NULL.
   *   Ignored when $column_name is NULL.
   * @param string|int|bool|null $value
   *   (optional) The value dependent on the $type.
   *   Ignored when $column_name is NULL.
   * @param string||null $value2
   *   (optional) The value dependent on the $value.
   *   If $value is SET_DATA, then this is a string representing the collation.
   *   Ignored when $value does not utilize this.
   *   Ignored when $column_name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_alter_column($column_name, $type = NULL, $value = NULL, $value2 = NULL) {
    if (is_null($column_name)) {
      $this->action_alter_column = NULL;
      return new c_base_return_true();
    }

    if (!is_string($column_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'column_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $valid_value = false;
    switch ($type) {
      case e_database_alter_column::DROP:
      case e_database_alter_column::SET:
        $valid_value = is_null($value) || is_bool($value);
        break;

      case e_database_alter_column::DROP_DEFAULT:
        break;

      case e_database_alter_column::SET_DATA:
        $valid_value = is_string($value);

        if (!is_null($value2) && !is_string($value2)) {
          unset($valid_value);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value2', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
        break;

      case e_database_alter_column::SET_DEFAULT:
        $valid_value = is_string($value);
        break;

      case e_database_alter_column::SET_STATISTICS:
        $valid_value = is_int($value);
        break;

      case e_database_alter_column::SET_STORAGE:
        switch($value) {
          case e_database_set_storage::EXTENDED:
          case e_database_set_storage::EXTERNAL:
          case e_database_set_storage::MAIN:
          case e_database_set_storage::PLAIN:
            $valid_value = true;
            break;
        }
        break;

      default:
        unset($valid_value);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!$valid_value) {
      unset($valid_value);
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    unset($invalid_value);

    $placeholder_column_name = $this->add_placeholder($column_name);
    if ($placeholder_column_name->has_error()) {
      return c_base_return_error::s_false($placeholder_column_name->get_error());
    }

    $placeholder_value = $value;
    if (is_string($value)) {
      $placeholder_value = $this->add_placeholder($value);
      if ($placeholder_value->has_error()) {
        unset($placeholder_column_name);
        return c_base_return_error::s_false($placeholder_value->get_error());
      }
    }

    $placeholder_value2 = NULL;
    if ($type === e_database_alter_column::SET_DATA) {
      $placeholder_value2 = $this->add_placeholder($value2);
      if ($placeholder_value2->has_error()) {
        unset($placeholder_column_name);
        unset($placeholder_value);
        return c_base_return_error::s_false($placeholder_value2->get_error());
      }
    }

    $this->action_alter_column = [
      'column_name' => $placeholder_column_name,
      'type' => $type,
      'value' => $placeholder_value,
      'value2' => $placeholder_value2,
    ];
    unset($placeholder_column_name);
    unset($placeholder_value);
    unset($placeholder_value2);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned action alter column settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_alter_column() {
    if (is_null($this->action_alter_column)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_alter_column)) {
      return c_base_return_array::s_new($this->action_alter_column);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_alter_column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_alter_column() {
    $value = c_database_string::ALTER_COLUMN . ' ' . $this->action_alter_column['column_name'];

    switch ($this->action_alter_column['type']) {
      case e_database_alter_column::DROP:
        $value .= ' ' . c_database_string::DROP;
        if ($this->action_alter_column['value']) {
          $value .= ' ' . c_database_string::NOT_NULL;
        }
        break;

      case e_database_alter_column::SET:
        $value .= ' ' . c_database_string::SET;
        if ($this->action_alter_column['value']) {
          $value .= ' ' . c_database_string::NOT_NULL;
        }
        break;

      case e_database_alter_column::DROP_DEFAULT:
        $value .= ' ' . c_database_string::DROP_DEFAULT;
        break;

      case e_database_alter_column::SET_DATA:
        $value .= ' ' . c_database_string::SET_DATA;
        $value .= ' ' . c_database_string::TYPE;
        $value .= ' ' . strval($this->action_alter_column['value']);
        if (isset($this->action_alter_column['value2'])) {
          $value .= ' ' . c_database_string::COLLATE;
          $value .= ' ' . strval($this->action_alter_column['value2']);
        }
        break;

      case e_database_alter_column::SET_DEFAULT:
        $value .= ' ' . c_database_string::SET_DEFAULT;
        $value .= ' ' . strval($this->action_alter_column['value']);
        break;

      case e_database_alter_column::SET_STATISTICS:
        $value .= ' ' . c_database_string::SET_STATISTICS;
        $value .= ' ' . $this->action_alter_column['value'];
        break;

      case e_database_alter_column::SET_STORAGE:
        $value .= ' ' . c_database_string::SET_STORAGE;
        if ($this->action_alter_column['value'] === e_database_set_storage::EXTENDED) {
          $value .= ' ' . c_database_string::EXTENDED;
        }
        else if ($this->action_alter_column['value'] === e_database_set_storage::EXTERNAL) {
          $value .= ' ' . c_database_string::EXTERNAL;
        }
        else if ($this->action_alter_column['value'] === e_database_set_storage::MAIN) {
          $value .= ' ' . c_database_string::MAIN;
        }
        else if ($this->action_alter_column['value'] === e_database_set_storage::PLAIN) {
          $value .= ' ' . c_database_string::PLAIN;
        }
        break;
    }

    return $value;
  }
}
