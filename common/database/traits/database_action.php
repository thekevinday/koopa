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

/**
 * Provide action support for an SQL query.
 *
 * An action is a class-specific action such as SELECT, INSERT, etc...
 * A query only performs one action.
 */
trait t_database_action {
  protected $query_action;

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
  public function set_query_action($action) {
    if (is_null($action)) {
      $this->query_action = NULL;
      return new c_base_return_true();
    }

    if (is_int($action)) {
      $this->query_action = $action;
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
 * Provide property support for an SQL query.
 *
 * A single property that is associated with a particular class-specific property such as SELECT, INSERT, etc...
 */
trait t_database_action_property {
  protected $query_action_property;

  /**
   * Assigns this query action property.
   *
   * @param int|null $property
   *   Whether or not to use a property associated with a particular class-specific action such as SELECT, INSERT, etc...
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_query_action_property($property) {
    if (is_null($property)) {
      $this->query_action_property = NULL;
      return new c_base_return_true();
    }

    if (is_int($property)) {
      $this->query_action_property = $property;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'property', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
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
    if (is_null($this->query_action_property)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->query_action_property);
  }
}
