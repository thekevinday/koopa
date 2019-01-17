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

require_once('common/database/enumerations/database_validator.php');

/**
 * Provide the sql VALIDATOR/NO VALIDATOR functionality.
 */
trait t_database_validator {
  protected $validator;

  /**
   * Set the VALIDATOR settings.
   *
   * @param int|null $validator
   *   The integer representing validator/no-validator.
   *   Set to NULL to disable.
   * @param string|null $validator_function
   *   The validator function name or null when NO_VALIDATOR is specified.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_validator($validator, $validator_function) {
    if (is_null($validator)) {
      $this->validator = NULL;
      return new c_base_return_true();
    }

    if ($validator === e_database_validator::VALIDATOR) {
      if (is_string($validator_function)) {
        $placeholder = $this->add_placeholder($validator_function);
        if ($placeholder->has_error()) {
          return c_base_return_error::s_false($placeholder->get_error());
        }

        $this->validator = [
          'type' => $validator,
          'name' => $placeholder,
        ];
        unset($placeholder);

        return new c_base_return_true();
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'validator_function', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    else if ($validator === e_database_validator::NO_VALIDATOR) {
      $this->validator = [
        'type' => $validator,
        'name' => null,
      ];

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'validator', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned validator.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the validator data on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_validator() {
    if (is_null($this->validator)) {
      return new c_base_return_null();
    }

    if (is_array($this->validator)) {
      return c_base_return_array::s_new($this->validator);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'validator', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_validator() {
    $value = NULL;
    if ($this->validator['type'] == e_database_validator::VALIDATOR) {
      if (isset($this->validator['name'])) {
        $value = c_database_string::VALIDATOR . ' ' . $this->validator['name'];
      }
    }
    else if ($this->validator['type'] == e_database_validator::NO_VALIDATOR) {
      $value = c_database_string::NO_VALIDATOR;
    }

    return $value;
  }
}
