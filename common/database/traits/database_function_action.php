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

require_once('common/database/enumerations/database_argument_function_action.php');

/**
 * Provide the sql argument type functionality.
 */
trait t_database_function_action {
  protected $function_action;

  /**
   * Set the argument type settings.
   *
   * @param int|null $function_action
   *   The function action code to assign.
   *   Set to NULL to disable.
   *   Setting to NULL removes all existing values.
   * @param string|int|null $parameter_1
   *   Value for the first parameter if required by the $function_action_code.
   *   A string representing the configuration parameter for SET_TO, SET_EQUAL, SET_FROM, or RESET.
   *   An integer representing execution cost for COST.
   *   An integer representing result rows for ROWS.
   * @param string|null $parameter_2
   *   Value for the second parameter if required by the $function_action_code.
   *   SET_TO and SET_FROM use this as value, but if left as NULL then DEFAULT is used.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_function_action($function_action, $parameter_1 = NULL, $parameter_2 = NULL) {
    if (is_null($function_action)) {
      $this->function_action = NULL;
      return new c_base_return_true();
    }

    switch ($function_action) {
      case e_database_function_action::CALLED_ON_NULL_INPUT:
      case e_database_function_action::COST:
      case e_database_function_action::IMMUTABLE:
      case e_database_function_action::LEAKPROOF:
      case e_database_function_action::NOT_LEAKPROOF:
      case e_database_function_action::PARALLEL_RESTRICTED:
      case e_database_function_action::PARALLEL_SAFE:
      case e_database_function_action::PARALLEL_UNSAFE:
      case e_database_function_action::RESET:
      case e_database_function_action::RESET_ALL:
      case e_database_function_action::RETURNS_NULL_ON_NULL_INPUT:
      case e_database_function_action::ROWS:
      case e_database_function_action::SECURITY_DEFINER:
      case e_database_function_action::SECURITY_INVOKER:
      case e_database_function_action::SET_EQUAL:
      case e_database_function_action::SET_FROM:
      case e_database_function_action::SET_TO:
      case e_database_function_action::STABLE:
      case e_database_function_action::STRICT:
      case e_database_function_action::VOLATILE:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'function_action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $action = [
      'type' => $function_action,
      'parameter_1' => NULL,
      'parameter_2' => NULL,
    ];

    if ($function_action === e_database_function_action::COST || $function_action === e_database_function_action::ROWS) {
      if (!is_int($parameter_1)) {
        unset($action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter_1', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $action['parameter_1'] = $parameter_1;
    }
    else if ($function_action === e_database_function_action::SET_TO || $function_action === e_database_function_action::SET_EQUAL) {
      if (!is_string($parameter_1)) {
        unset($action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter_1', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($parameter_2) && !is_string($parameter_2)) {
        unset($action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter_2', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $placeholder = $this->add_placeholder($parameter_1);
      if ($placeholder->has_error()) {
        unset($action);
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $action['parameter_1'] = $placeholder;

      $placeholder = $this->add_placeholder($parameter_2);
      if ($placeholder->has_error()) {
        unset($action);
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $action['parameter_2'] = $placeholder;
      unset($placeholder);
    }
    else if ($function_action === e_database_function_action::SET_FROM || $function_action === e_database_function_action::RESET) {
      if (!is_string($parameter_1)) {
        unset($action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter_1', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $placeholder = $this->add_placeholder($parameter_1);
      if ($placeholder->has_error()) {
        unset($action);
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $action['parameter_1'] = $placeholder;
      unset($placeholder);
    }

    $this->function_action = $action;
    unset($action);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned sql functionn action.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array representing the function action on success.
   *   NULL is returned if not set (function action tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_function_action($index = NULL) {
    if (is_null($this->function_action)) {
      return new c_base_return_null();
    }

    if (is_array($this->function_action)) {
      return c_base_return_array::s_new($this->function_action);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'function_action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_function_action() {
    $values = [];
    foreach ($this->function_action as $function_action) {
      if ($function_action['type'] === e_database_function_action::CALLED_ON_NULL_INPUT) {
        $values[] = c_database_string::CALLED_ON_NULL_INPUT;
      }
      else if ($function_action['type'] === e_database_function_action::COST) {
        $values[] = c_database_string::COST . ' ' . $function_action['parameter_1'];
      }
      else if ($function_action['type'] === e_database_function_action::IMMUTABLE) {
        $values[] = c_database_string::IMMUTABLE;
      }
      else if ($function_action['type'] === e_database_function_action::LEAKPROOF) {
        $values[] = c_database_string::LEAKPROOF;
      }
      else if ($function_action['type'] === e_database_function_action::NOT_LEAKPROOF) {
        $values[] = c_database_string::NOT_LEAKPROOF;
      }
      else if ($function_action['type'] === e_database_function_action::PARALLEL_RESTRICTED) {
        $values[] = c_database_string::PARALLEL_RESTRICTED;
      }
      else if ($function_action['type'] === e_database_function_action::PARALLEL_SAFE) {
        $values[] = c_database_string::PARALLEL_SAFE;
      }
      else if ($function_action['type'] === e_database_function_action::PARALLEL_UNSAFE) {
        $values[] = c_database_string::PARALLEL_UNSAFE;
      }
      else if ($function_action['type'] === e_database_function_action::RESET) {
        $values[] = c_database_string::RESET;
      }
      else if ($function_action['type'] === e_database_function_action::RESET_ALL) {
        $values[] = c_database_string::RESET_ALL;
      }
      else if ($function_action['type'] === e_database_function_action::RETURNS_NULL_ON_NULL_INPUT) {
        $values[] = c_database_string::RETURNS_NULL_ON_NULL_INPUT;
      }
      else if ($function_action['type'] === e_database_function_action::ROWS) {
        $values[] = c_database_string::ROWS . ' ' . $function_action['parameter_1'];
      }
      else if ($function_action['type'] === e_database_function_action::SECURITY_DEFINER) {
        $values[] = c_database_string::SECURITY_DEFINER;
      }
      else if ($function_action['type'] === e_database_function_action::SECURITY_INVOKER) {
        $values[] = c_database_string::SECURITY_INVOKER;
      }
      else if ($function_action['type'] === e_database_function_action::SET_EQUAL) {
        $value = is_null($function_action['parameter_2']) ? c_database_string::DEFAULT : $function_action['parameter_2'];
        $values[] = c_database_string::SET . ' ' . $function_action['parameter_1'] . ' = ' . $value;
        unset($value);
      }
      else if ($function_action['type'] === e_database_function_action::SET_FROM) {
        $values[] = c_database_string::SET . ' ' . $function_action['parameter_1'] . ' ' . c_database_string::FROM_CURRENT;
        unset($value);
      }
      else if ($function_action['type'] === e_database_function_action::SET_TO) {
        $value = is_null($function_action['parameter_2']) ? c_database_string::DEFAULT : $function_action['parameter_2'];
        $values[] = c_database_string::SET . ' ' . $function_action['parameter_1'] . ' ' . c_database_string::TO . ' ' . $value;
        unset($value);
      }
      else if ($function_action['type'] === e_database_function_action::STABLE) {
        $values[] = c_database_string::STABLE;
      }
      else if ($function_action['type'] === e_database_function_action::STRICT) {
        $values[] = c_database_string::STRICT;
      }
      else if ($function_action['type'] === e_database_function_action::VOLATILE) {
        $values[] = c_database_string::VOLATILE;
      }
    }
    unset($function_action);

    return implode(', ', $values);
  }
}
