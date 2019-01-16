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

require_once('common/database/enumerations/database_reset.php');

/**
 * Provide the sql RESET configuration_parameter functionality.
 */
trait t_database_reset_configuration_parameter {
  protected $reset_configuration_parameter;

  /**
   * Set the RESET configuration_parameter settings.
   *
   * @param int|null $type
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
  public function set_reset_configuration_parameter($type, $parameter = NULL) {
    if (is_null($type)) {
      $this->reset_configuration_parameter = NULL;
      return new c_base_return_true();
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($type === e_database_reset::PARAMETER) {
      if (!is_null($parameter) || !is_string($parameter)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $placeholder = $this->add_placeholder($parameter);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->reset_configuration_parameter = [
        'type' => $type,
        'value' => $placeholder,
      ];
      unset($placeholder);

      return new c_base_return_true();
    }
    else if ($type == e_database_reset::ALL) {
      $this->reset_configuration_parameter = [
        'type' => $type,
        'value' => NULL,
      ];

      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned reset configuration_parameter settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing reset settings on success.
   *   NULL is returned if not set (reset tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_reset_configuration_parameter() {
    if (is_null($this->reset_configuration_parameter)) {
      return new c_base_return_null();
    }

    if (is_array($this->reset_configuration_parameter)) {
      return c_base_return_array::s_new($this->reset_configuration_parameter);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'reset_configuration_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_reset_configuration_parameter() {
    $value = NULL;
    if ($this->reset_configuration_parameter['type'] === e_database_reset::PARAMETER) {
      $value = c_database_string::RESET . ' ' . $this->reset_configuration_parameter['value'];
    }
    else if ($this->reset_configuration_parameter['type'] === e_database_reset::ALL) {
      $value = c_database_string::RESET . ' ' . c_database_string::ALL;
    }

    return $value;
  }
}
