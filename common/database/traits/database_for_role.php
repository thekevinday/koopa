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
 * Provide the sql FOR ROLE functionality.
 */
trait t_database_for_role {
  protected $for_role;

  /**
   * Assign the settings.
   *
   * @param string|null $for_role
   *   Append a role name to the list.
   *   Set to NULL to disable.
   *   When NULL, this resets the for_role to an empty array.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_for_role($for_role) {
    if (is_null($for_role)) {
      $this->for_role = NULL;
      return new c_base_return_true();
    }

    if (!is_string($for_role)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'for_role', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->for_role)) {
      $this->for_role = [];
    }

    $placeholder = $this->add_placeholder($for_role);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->for_role[] = $placeholder;
    unset($placeholder);
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned for role value.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of for roles or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_for_role() {
    if (is_null($this->for_role)) {
      return new c_base_return_null();
    }

    if (is_array($this->for_role)) {
      return c_base_return_array::s_new($this->for_role);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'for_role', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_for_role() {
    return c_database_string::FOR . ' ' . c_database_string::ROLE . ' ' . implode(', ', $this->for_role);
  }
}
