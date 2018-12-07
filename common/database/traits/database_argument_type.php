<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with argument_type.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_string.php');

require_once('common/database/enumerations/database_argument_mode.php');

/**
 * Provide the sql argument type functionality.
 */
trait t_database_argument_type {
  protected $argument_type;

  /**
   * Set the argument type settings.
   *
   * @param string|null $argument_type
   *   The argument type to assign.
   *   Set to NULL to disable.
   *   Setting to NULL removes all existing values.
   * @param string|null $argument_name
   *   When $argument_type is not NULL, this specifies the name of the argument.
   * @param int|null $argument_mode
   *   When $argument_type is not NULL, this specifies the argument mode code of the argument.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_type($argument_type, $argument_name = NULL, $argument_mode = NULL) {
    if (is_null($argument_type)) {
      $this->argument_type = NULL;
      return new c_base_return_true();
    }

    if (!is_string($argument_type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($argument_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch($argument_mode) {
      case e_database_argument_mode::IN:
      case e_database_argument_mode::INOUT:
      case e_database_argument_mode::OUT:
      case e_database_argument_mode::VARIADIC:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_mode', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_array($this->argument_type)) {
      $this->argument_type = [];
    }

    $this->argument_type[] = [
      'type' => $argument_type,
      'name' => $argument_name,
      'mode' => $argument_mode,
    ];

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned sql argument type at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the argument type array at the specified index.
   *   When NULL, all argument type are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   A code representing the argument_type on success.
   *   NULL is returned if not set (argument_type tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_type($index = NULL) {
    if (is_null($this->argument_type)) {
      return new c_base_return_null();
    }

    if (is_null($this->argument_type['type'])) {
      return c_base_return_array::s_new($this->argument_type);
    }
    else if (isset($this->argument_type[$index]) && is_array($this->argument_type[$index])) {
      return c_base_return_int::s_new($this->argument_type[$index]);
    }
    else {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_argument_type() {
    if (is_null($this->argument_type)) {
      return NULL;
    }

    $values = [];
    foreach ($this->argument_type as $argument_type) {
      $value = '';

      switch($argument_type['mode']) {
        case e_database_argument_mode::IN:
          $value = c_database_string::IN;
          break;
        case e_database_argument_mode::INOUT:
          $value = c_database_string::INOUT;
          break;
        case e_database_argument_mode::OUT:
          $value = c_database_string::OUT;
          break;
        case e_database_argument_mode::VARIADIC:
          $value = c_database_string::VARIADIC;
          break;
      }

      $value .= ' ' . $argument_type['name'];
      $value .= ' ' . $argument_type['type'];

      $values[] = $value;
    }
    unset($argument_type);
    unset($value);

    return implode(', ', $values);
  }
}
