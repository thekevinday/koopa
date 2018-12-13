<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with set.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_set.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql SET functionality.
 */
trait t_database_set {
  protected $set;

  /**
   * Set the SET settings.
   *
   * @param int|null $set
   *   The SET code to assign.
   *   Should be one of: e_database_set.
   *   Set to NULL to disable.
   * @param string|null $parameter
   *   (optional) When non-NULL this is the configuration parameter.
   *   When NULL, DEFAULT is used if applicablem otherwise this is ignored.
   * @param string|null $value
   *   (optional) When non-NULL this is the value associated with the parameter.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set($set, $parameter = NULL, $value = NULL) {
    if (is_null($set)) {
      $this->set = NULL;
      return new c_base_return_true();
    }

    if (!is_int($set)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($set === e_database_set::TO || $set === e_database_set::EQUAL) {
      if (!is_null($parameter) || !is_string($parameter)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($value) && !is_string($value)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->set = [
        'type' => $set,
        'parameter' => $parameter,
        'value' => $value,
      ];

      return new c_base_return_true();
    }
    else if ($set == e_database_set::FROM_CURRENT) {
      $this->set = [
        'type' => $set,
        'parameter' => NULL,
        'value' => NULL,
      ];
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned set settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing set settings on success.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_set() {
    if (is_null($this->set)) {
      return new c_base_return_null();
    }

    if (is_array($this->set)) {
      return c_base_return_array::s_new($this->set);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set() {
    $value = NULL;
    if ($this->set['type'] === e_database_set::TO) {
      if (is_null($this->set['parameter'])) {
        $value = c_database_string::SET . ' ' . $this->set['parameter'] . ' ' . c_database_string::TO . ' ' . c_database_string::DEFAULT;
      }
      else if (is_string($this->set['parameter']) && is_string($this->set['value'])) {
        $value = c_database_string::SET . ' ' . $this->set['parameter'] . ' ' . c_database_string::TO . ' ' . $this->set['value'];
      }
    }
    else if ($this->set['type'] === e_database_set::EQUAL) {
      if (is_null($this->set['parameter'])) {
        $value = c_database_string::SET . ' ' . $this->set['parameter'] . ' = ' . c_database_string::DEFAULT;
      }
      else if (is_string($this->set['parameter']) && is_string($this->set['value'])) {
        $value = c_database_string::SET . ' ' . $this->set['parameter'] . ' = ' . $this->set['value'];
      }
    }
    else if ($this->set['type'] == e_database_set::FROM_CURRENT) {
      if (is_string($this->set['parameter'])) {
        $value = c_database_string::SET . ' ' . $this->set['parameter'] . ' = ' . c_database_string::FROM_CURRENT;
      }
    }

    return $value;
  }
}
