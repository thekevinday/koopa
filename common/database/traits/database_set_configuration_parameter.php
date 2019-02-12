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

require_once('common/database/enumerations/database_set.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql SET functionality.
 */
trait t_database_set_configuration_parameter {
  protected $set_configuration_parameter;

  /**
   * Assign the settings.
   *
   * @param int|null $type
   *   The SET code to assign.
   *   Should be one of: e_database_set.
   *   Set to NULL to disable.
   * @param string|null $parameter
   *   (optional) When non-NULL this is the configuration parameter.
   *   When NULL, this and $value are ignored.
   *   Some values of $set may require this to be a non-NULL.
   * @param string|null $value
   *   (optional) When non-NULL this is the value associated with the parameter.
   *   When NULL, DEFAULT is used, if applicable, otherwise this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_configuration_parameter($type, $parameter = NULL, $value = NULL) {
    if (is_null($type)) {
      $this->set_configuration_parameter = NULL;
      return new c_base_return_true();
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($type === e_database_set::TO || $type === e_database_set::EQUAL) {
      if (!is_string($parameter)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($value) && !is_string($value)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $placeholder = $this->add_placeholder($parameter);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $configuration_parameter = [
        'type' => $type,
        'parameter' => $placeholder,
        'value' => NULL,
      ];

      if (is_string($value)) {
        $placeholder = $this->add_placeholder($value);
        if ($placeholder->has_error()) {
          unset($configuration_parameter);
          return c_base_return_error::s_false($placeholder->get_error());
        }

        $set['value'] = $placeholder;
      }
      unset($placeholder);

      $this->set_configuration_parameter = $configuration_parameter;
      unset($configuration_parameter);

      return new c_base_return_true();
    }
    else if ($type == e_database_set::FROM_CURRENT) {
      $this->set_configuration_parameter = [
        'type' => $type,
        'parameter' => NULL,
        'value' => NULL,
      ];

      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned set configuration parameter settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing set configuration parameter settings.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_configuration_parameter() {
    if (is_null($this->set_configuration_parameter)) {
      return new c_base_return_null();
    }

    if (is_array($this->set_configuration_parameter)) {
      return c_base_return_array::s_new($this->set_configuration_parameter);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_configuration_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_configuration_parameter() {
    $value = NULL;
    if ($this->set_configuration_parameter['type'] === e_database_set::TO) {
      $value = c_database_string::SET . ' ' . $this->set_configuration_parameter['parameter'] . ' ' . c_database_string::TO . ' ';
      if (is_null($this->set_configuration_parameter['value'])) {
        $value .= c_database_string::DEFAULT;
      }
      else {
        $value = $this->set_configuration_parameter['value'];
      }
    }
    else if ($this->set_configuration_parameter['type'] === e_database_set::EQUAL) {
      $value = c_database_string::SET . ' ' . $this->set_configuration_parameter['parameter'] . ' = ';
      if (is_null($this->set_configuration_parameter['value'])) {
        $value .= c_database_string::DEFAULT;
      }
      else if (isset($this->set_configuration_parameter['parameter']) && isset($this->set_configuration_parameter['value'])) {
        $value .= $this->set_configuration_parameter['value'];
      }
    }
    else if ($this->set_configuration_parameter['type'] == e_database_set::FROM_CURRENT) {
      $value = c_database_string::SET . ' ' . $this->set_configuration_parameter['parameter'] . ' = ' . c_database_string::FROM . ' ' . c_database_string::CURRENT;
    }
    else if ($this->set_configuration_parameter['type'] == e_database_set::TO_DEFAULT) {
      $value = c_database_string::SET . ' ' . $this->set_configuration_parameter['parameter'] . ' ' . c_database_string::TO . ' ' . c_database_string::DEFAULT;
    }

    return $value;
  }
}
