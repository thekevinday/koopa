<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with function_action.
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

    switch($function_action) {
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

      $action['parameter_1'] = $parameter_1;
      $action['parameter_2'] = $parameter_2;
    }
    else if ($function_action === e_database_function_action::SET_FROM || $function_action === e_database_function_action::RESET) {
      if (!is_string($parameter_1)) {
        unset($action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter_1', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $action['parameter_1'] = $parameter_1;
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
   *   A string is returned on success.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_function_action() {
    if (is_null($this->function_action)) {
      return NULL;
    }

    // @todo
  }
}
