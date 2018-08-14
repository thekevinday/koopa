<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with reset.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_reset.php');

require_once('common/database/classes/database_code.php');

/**
 * Provide the sql RESET functionality.
 */
trait t_database_reset {
  protected $query_reset;
  protected $query_reset_parameter;

  /**
   * Set the RESET settings.
   *
   * @param int|null $reset
   *   The reset code to assign.
   *   Should be one of: e_database_reset.
   *   Set to NULL to disable.
   * @param string|null $parameter
   *   (optional) When non-NULL this is the configuration parameter.
   *   When NULL, DEFAULT is used if applicablem otherwise this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_reset($reset, $parameter = NULL) {
    if (is_null($reset)) {
      $this->query_reset = NULL;
      $this->query_reset_parameter = NULL;
      return new c_base_return_true();
    }

    if (!is_int($reset)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($reset == e_database_reset::PARAMETER) {
      if (!is_null($parameter) || !is_string($parameter)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->query_reset = $reset;
      $this->query_reset_parameter = $parameter;
      return new c_base_return_true();
    }
    else if ($reset == e_database_reset::ALL) {
      $this->query_reset = $reset;
      $this->query_reset_parameter = NULL;
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned sql reset.
   *
   * @return c_base_return_int|c_base_return_null
   *   A (c_database_reset) code representing the reset on success.
   *   NULL is returned if not set (reset tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_reset() {
    if (is_null($this->query_reset)) {
      return new c_base_return_null();
    }

    if (is_int($this->query_reset)) {
      return c_base_return_int::s_new($this->query_reset);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned sql reset parameter value.
   *
   * @return c_base_return_string|c_base_return_null
   *   A reset parameter value on success.
   *   NULL without error bit reset is returned if not assigned.
   *   NULL with the error bit reset is returned on error.
   */
  public function get_query_reset_parameter() {
    if (is_null($this->query_reset_parameter)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_reset_parameter)) {
      return c_base_return_string::s_new($this->query_reset_parameter);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_reset_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}
