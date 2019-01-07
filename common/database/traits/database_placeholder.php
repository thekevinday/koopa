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
 * Provide the placeholder name functionality.
 *
 * This expects the implementing class to have $this->value assigned, which is assumed to be from t_base_return_value.
 */
trait t_database_placeholder {
  private $assigned;
  private $id;
  private $prefix;


  /**
   * Implements do_reset().
   */
  public function do_reset() {
    $this->assigned = FALSE;
    $this->id = NULL;
    $this->prefix = NULL;
    $this->value = NULL;

    return new c_base_return_true();
  }

  /**
   * Implements get_id().
   */
  public function get_id() {
    if (is_int($this->id)) {
      return c_base_return_int::s_new($this->id);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements get_name().
   */
  public function get_name() {
    if (!is_int($this->id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    if (!is_string($this->prefix)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'prefix', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    return c_base_return_string::s_new($this->prefix . $this->id);
  }

  /**
   * Implements get_prefix().
   */
  public function get_prefix() {
    if (is_string($this->prefix)) {
      return c_base_return_string::s_new($this->prefix);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'prefix', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements has_value().
   */
  public function has_value() {
    return $this->assigned === TRUE;
  }

  /**
   * Implements set_id().
   */
  public function set_id($id) {
    if (!is_int($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Implements set_prefix().
   */
  public function set_prefix($prefix) {
    if (!is_string($prefix)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'prefix', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->prefix = $prefix;
    return new c_base_return_true();
  }
}
