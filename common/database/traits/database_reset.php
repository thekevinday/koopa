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

require_once('common/database/classes/database_string.php');

require_once('common/database/enumerations/database_reset.php');

/**
 * Provide the sql RESET functionality.
 */
trait t_database_reset {
  protected $reset;

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
  public function set_reset($reset, $parameter = NULL) {
    if (is_null($reset)) {
      $this->reset = NULL;
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

      $this->reset = [
        'type' => $reset,
        'value' => $parameter,
      ];

      return new c_base_return_true();
    }
    else if ($reset == e_database_reset::ALL) {
      $this->reset = [
        'type' => $reset,
        'value' => NULL,
      ];

      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned reset settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing reset settings on success.
   *   NULL is returned if not set (reset tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_reset() {
    if (is_null($this->reset)) {
      return new c_base_return_null();
    }

    if (is_array($this->reset)) {
      return c_base_return_array::s_new($this->reset);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_reset() {
    $value = NULL;
    if ($this->reset['type'] === e_database_reset::PARAMETER) {
      if (is_string($this->reset['value'])) {
        $value = c_database_string::RESET . ' ' . $this->reset['value'];
      }
    }
    else if ($this->reset['type'] === e_database_reset::ALL) {
      $value = c_database_string::RESET . ' ' . c_database_string::ALL;
    }

    return $value;
  }
}
