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

require_once('common/database/enumerations/database_role.php');

/**
 * Provide the sql TO ROLES functionality.
 */
trait t_database_to_role {
  protected $to_role;

  /**
   * Assign the settings.
   *
   * @param int|null $role_type
   *   The role type to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   * @param string|null $role_name
   *   The role name to use.
   *   This may be NULL if not used by the $role_type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_to_role($role_type, $role_name = NULL) {
    if (is_null($role_type)) {
      $this->to_role = NULL;
      return new c_base_return_true();
    }

    if (is_int($role_type)) {
      if ($role_type === e_database_role::NAME && !is_string($role_name) || !is_null($role_name) && !is_string($role_name)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'role_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
      else if ($role_type !== e_database_role::GROUP && $role_type !== e_database_role::PUBLIC) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'role_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_array($this->to_role)) {
        $this->to_role = [];
      }

      $placeholder = $this->add_placeholder($role_name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->to_role[] = [
        'type' => $role_type,
        'name' => $placeholder,
      ];
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'to_role', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of to role arrays or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_to_role() {
    if (is_null($this->to_role)) {
      return new c_base_return_null();
    }

    if (is_array($this->to_role)) {
      return c_base_return_array::s_new($this->to_role);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'to_role', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_to_role() {
    $values = [];
    foreach ($this->to_role as $to_role) {
      if ($this->to_role['type'] === e_database_role::PUBLIC) {
        $values[] = c_database_string::PUBLIC;
      }
      else if ($this->to_role['type'] === e_database_role::GROUP) {
        $values[] = c_database_string::GROUP;
      }
      else if ($this->to_role['type'] === e_database_role::NAME) {
        $values[] = $this->to_role['name'];
      }
    }

    return c_database_string::TO . ' ' . implode(', ', $values);
  }
}
