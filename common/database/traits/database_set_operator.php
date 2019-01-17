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

require_once('common/database/enumerations/database_set_operator.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql (operator) SET functionality.
 */
trait t_database_set_operator {
  protected $set_operator;

  /**
   * Set the (operator) SET settings.
   *
   * @param int|null $parameter
   *   The SET code to assign.
   *   Should be one of: e_database_set_operator.
   *   Set to NULL to disable.
   *   When NULLm this will remove all values.
   * @param string|null $value
   *   (optional) The value associated with the parameter.
   *   When NULL, a value of 'NONE' is used.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set_operator is returned on error.
   */
  public function set_set_operator($parameter, $value = NULL) {
    if (is_null($set_operator)) {
      $this->set_operator = NULL;
      return new c_base_return_true();
    }

    if ($parameter !== e_database_set_operator::RESTRICT && $parameter !== e_database_set_operator::JOIN) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'paramete', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($value) && !is_null($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->set_operator)) {
      $this->set_operator = [];
    }

    $set_operator = [
      'parameter' => $parameter,
      'value' => NULL,
    ];

    if (is_string($value)) {
      $placeholder = $this->add_placeholder($value);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $set_operator['value'] = $placeholder;
      unset($placeholder);
    }

    $this->set_operator[] = $set_operator;
    unset($set_operator);

    return new c_base_return_true();
  }

  /**
   * Get the (operator) SET values.
   *
   * @param int|null $index
   *   (optional) Get the set parameter and value at the specified index.
   *   When NULL, all parameters and values are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of parameters and values or NULL if not defined.
   *   A single parameters and value array is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_operator($index = NULL) {
    if (is_null($this->set_operator)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->set_operator)) {
        return c_base_return_array::s_new($this->set_operator);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->set_operator)) {
        return c_base_return_array::s_new($this->set_operator[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_operator[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_operator', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_operator() {
    $value = NULL;

    if (is_array($this->set_operator) && !empty($this->set_operator)) {
      $values = [];
      foreach ($this->set_operator as $set) {
        if (is_null($set['value'])) {
          $values[] = $set['parameter'] . ' = ' . c_database_string::NONE;
        }
        else {
          $values[] = $set['parameter'] . ' = ' . $set['value'];
        }
      }
      unset($set);

      $value = c_database_string::SET . ' ' . implode(', ', $values);
      unset($values);
    }

    return $value;
  }
}
