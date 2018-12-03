<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide action support for an SQL query.
 *
 * An action is a class-specific action such as SELECT, INSERT, etc...
 * A query only performs one action.
 */
trait t_database_action {
  protected $action;

  /**
   * Assigns this query action.
   *
   * @param int|null $action
   *   Whether or not to use a class-specific action such as SELECT, INSERT, etc...
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_action($action) {
    if (is_null($action)) {
      $this->action = NULL;
      return new c_base_return_true();
    }

    if (is_int($action)) {
      $this->action = $action;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned action.
   *
   * @return c_base_return_int|c_base_return_null
   *   Integer representing the action is returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_action() {
    if (is_null($this->action)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->action);
  }
}

/**
 * Provide action property support for an SQL query.
 *
 * A single property that is associated with a particular action.
 */
trait t_database_action_property {
  protected $action_property;

  /**
   * Assigns this query action property.
   *
   * @param int|null $action_property
   *   Whether or not to use a action property associated.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_action_property($action_property) {
    if (is_null($action_property)) {
      $this->action_property = NULL;
      return new c_base_return_true();
    }

    if (is_int($action_property)) {
      $this->action_property = $action_property;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action_property', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned action property.
   *
   * @return c_base_return_int|c_base_return_null
   *   Integer representing the action property is returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_action_property() {
    if (is_null($this->action_property)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->action_property);
  }
}

/**
 * Provide parameter(s) associated with an action or action property support for an SQL query.
 *
 * A single parameter, a database query object, or an array of parameters that are associated with a particular action or action property.
 */
trait t_database_action_parameter {
  protected $action_parameter;

  /**
   * Assigns this query action parameter(s).
   *
   * @param c_base_return_string|c_base_return_array|c_database_query|null $action_parameter
   *   Whether or not to use a specified action parameter or array of parameters.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_action_parameter($action_parameter) {
    if (is_null($action_parameter)) {
      $this->action_parameter = NULL;
      return new c_base_return_true();
    }

    if (is_string($action_parameter)) {
      $this->action_parameter = $action_parameter;
      return new c_base_return_true();
    }

    if (is_array($action_parameter)) {
      $this->action_parameter = $action_parameter;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned action parameter.
   *
   * @return c_base_return_string|c_base_return_array|c_database_query|c_base_return_null
   *   String or array representing the action parameters are returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_action_parameter() {
    if (is_null($this->action_parameter)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_parameter)) {
      return c_base_return_array::s_new($this->action_parameter);
    }

    if ($this->action_parameter instanceof c_database_query) {
      return clone($this->action_parameter);
    }

    return c_base_return_string::s_new($this->action_parameter);
  }
}
