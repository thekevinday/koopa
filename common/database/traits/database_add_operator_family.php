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

require_once('common/database/enumerations/database_operator_family.php');
require_once('common/database/enumerations/database_operator_for.php');

/**
 * Provide the sql ADD/DROP OPERATOR/FUNCTION functionality.
 */
trait t_database_add_operator_family {
  protected $add_operator_family;

  /**
   * Set the add user or drop user.
   *
   * @param bool|null $add
   *   Set to TRUE for ADD OPERATOR.
   *   Set to FALSE for DROP OPERATOR.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   * @param int|null $type
   *   The operator type from e_database_operator_family.
   *   This is required when $add is not NULL.
   * @param int|null $strategy
   *   (optional) The strategy number.
   *   This is required when $add is not NULL.
   * @param string|null $name
   *   (optional) The operator name.
   *   This is required when $add is TRUE.
   * @param string|null $left_type
   *   (optional) The (left) operator type.
   *   This is required when $add is not NULL.
   * @param string|null $ight_type
   *   (optional) The (right) operator type.
   *   This is required when $add is TRUE and $type is OPERATOR.
   * @param int|null $for_type
   *   (optional) The for type from e_database_operator_for.
   *   This is required when $add is TRUE and $type is OPERATOR.
   * @param string|null $sort_family_name
   *   (optional) The sort family name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_add_operator_family($add, $type = NULL, $strategy = NULL, $name = NULL, $left_type = NULL, $right_type = NULL, $for_type = NULL, $sort_family_name = NULL) {
    if (is_null($add)) {
      $this->add_operator_family = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($add)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'add', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($type) {
      case e_database_operator_family::OPERATOR:
      case e_database_operator_family::FUNCTION:
        break;

      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_int($strategy)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'strategy', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!($add && is_string($name)) || !(!$add && is_null($name))) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($add && $type === e_database_operator_family::OPERATOR) {
      switch ($for_type) {
        case e_database_operator_for::FOR_ORDER_BY:
        case e_database_operator_for::FOR_SEARCH:
          break;

        default:
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'for_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
      }
    }

    if (!is_string($left_type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'left_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($right_type) && !(!$add && $type === e_database_operator_family::OPERATOR && is_null($right_type))) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'right_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($sort_family_name) && !is_string($sort_family_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'sort_family_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $value = [
      'type' => $type,
      'strategy' => $strategy,
      'name' => NULL,
      'left_type' => NULL,
      'right_type' => NULL,
      'for_type' => NULL,
      'sort_family_name' => NULL,
    ];

    if ($add) {
      $placeholder = $this->add_placeholder($name);
      if ($placeholder->has_error()) {
        unset($value);
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $value['name'] = $placeholder;
    }

    $placeholder = $this->add_placeholder($left_type);
    if ($placeholder->has_error()) {
      unset($add_operator_family);
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $value['left_type'] = $placeholder;

    if (is_string($right_type)) {
      $placeholder = $this->add_placeholder($right_type);
      if ($placeholder->has_error()) {
        unset($add_operator_family);
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $value['right_type'] = $placeholder;
    }

    if ($add) {
      $placeholder = $this->add_placeholder($for_type);
      if ($placeholder->has_error()) {
        unset($value);
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $value['for_type'] = $placeholder;

      if (is_string($sort_family_name)) {
        $placeholder = $this->add_placeholder($sort_family_name);
        if ($placeholder->has_error()) {
          unset($value);
          return c_base_return_error::s_false($placeholder->get_error());
        }

        $value['sort_family_name'] = $placeholder;
      }
    }
    unset($placeholder);

    if (!is_array($this->add_operator_family)) {
      $this->add_operator_family = [
        'add' => TRUE,
        'values' => [],
      ];
    }

    $this->add_operator_family['add'] = $add;
    $this->add_operator_family['values'][] = $value;
    unset($value);

    return new c_base_return_true();
  }

  /**
   * Get the add/drop user settings.
   *
   * @param int|null $index
   *   (optional) Get the argument type array at the specified index.
   *   When NULL, all argument type are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array representing the add/drop operator settings at the $index.
   *   An array representing all add/drop operator settings when $index is NULL.
   *   NULL is returned if not set (add/drop operator is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_add_operator_family($index = NULL) {
    if (is_null($this->add_operator_family)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      return c_base_return_array::s_new($this->add_operator_family);
    }
    else if (isset($this->add_operator_family['values'][$index]) && is_array($this->add_operator_family['values'][$index])) {
      return c_base_return_array::s_new($this->add_operator_family['values'][$index]);
    }
    else {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'add_operator_family', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_add_operator_family() {
    $value = NULL;

    if ($this->add_operator_family['add']) {
      $value = c_database_string::ADD;
    }
    else {
      $value = c_database_string::DROP;
    }

    $values = [];
    foreach ($this->add_operator_family['values'] as $add_operator_family) {
      if ($add_operator_family['for_type'] === e_database_operator_family::FUNCTION) {
        $value .= ' ' . c_database_string::FUNCTION;
      }
      else if ($add_operator_family['for_type'] === e_database_operator_family::OPERATOR) {
        $value .= ' ' . c_database_string::OPERATOR;
      }

      $value .= ' ' . $add_operator_family['strategy'];

      if ($add_operator_family['add']) {
        $value .= ' ' . $add_operator_family['name'];
      }

      $value .= ' (' . $add_operator_family['left_type'];
      if (is_string($add_operator_family['right_type'])) {
        $value .= ' (' . $add_operator_family['right_type'];
      }
      $value .= ')';

      if ($add_operator_family['add'] && $add_operator_family['type'] === e_database_operator_family::OPERATOR) {
        if ($add_operator_family['for_type'] === e_database_operator_for::FOR_ORDER_BY) {
          $value .= ' ' . c_database_string::FOR_ORDER_BY;
          if (isset($add_operator_family['sort_family_name'])) {
            $value .= ' ' . $add_operator_family['sort_family_name'];
          }
        }
        else if ($add_operator_family['for_type'] === e_database_operator_for::FOR_SEARCH) {
          $value .= ' ' . c_database_string::FOR_SEARCH;
        }
      }

      $values[] = $add_operator_family;
    }
    unset($add_operator_family);

    return $value . implode(', ', $values);
  }
}
