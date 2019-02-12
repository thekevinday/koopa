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

require_once('common/database/enumerations/database_view_option.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql RESET view option functionality.
 */
trait t_database_reset_view_option {
  protected $reset_view_option;

  /**
   * Assign the settings.
   *
   * @param int|null $type
   *   The view option type, one of e_database_view_option.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_reset_view_option($type) {
    if (is_null($type)) {
      $this->reset_view_option = NULL;
      return new c_base_return_true();
    }

    switch ($type) {
      case e_database_view_option::CHECK_OPTION:
      case e_database_view_option::SECURITY_BARRIER:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_array($this->reset_view_option)) {
      $this->reset_view_option = [];
    }

    $this->reset_view_option[] = [
      'type' => $type,
    ];

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned set configuration parameter settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_reset_view_option() {
    if (is_null($this->reset_view_option)) {
      return new c_base_return_null();
    }

    if (is_array($this->reset_view_option)) {
      return c_base_return_array::s_new($this->reset_view_option);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'reset_view_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_reset_view_option() {
    $values = [];
    foreach ($this->reset_view_option as $value) {
      if ($value['type'] === e_database_view_option::CHECK_OPTION) {
        $values[] = c_database_string::CHECK_OPTION;
      }
      else if ($value['type'] === e_database_view_option::SECURITY_BARRIER) {
        $values[] = c_database_string::SECURITY_BARRIER;
      }
    }

    return c_database_string::RESET . ' (' . implode(', ', $values) . ')';
  }
}
