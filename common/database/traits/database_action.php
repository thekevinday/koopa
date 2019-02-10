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

/**
 * Provide the sql action names functionality.
 */
trait t_database_action {
  protected $action;

  /**
   * Set the settings.
   *
   * @param string|null $name
   *   The type action name.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action($name) {
    if (is_null($option)) {
      $this->action = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->action)) {
      $this->action = [];
    }

    $this->action[] = $name;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of type action names.
   *   NULL is returned if not set (publication name not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action() {
    if (is_null($this->action)) {
      return new c_base_return_null();
    }

    if (is_array($this->action)) {
      return c_base_return_array::s_new($this->action);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action() {
    return implode(', ', $this->action);
  }
}
