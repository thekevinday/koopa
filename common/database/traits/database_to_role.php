<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
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
   * Set the in schema, to roles.
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

      $this->to_role[] = [
        'type' => $role_type,
        'name' => $role_name,
      ];
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'to_role', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the in schema, to roles.
   *
   * @param int|null $index
   *   (optional) Get the schema name at the specified index.
   *   When NULL, all to roles are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of to role arrays or NULL if not defined.
   *   A single to role array is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_to_role($index = NULL) {
    if (is_null($this->to_role)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->to_role)) {
        return c_base_return_array::s_new($this->to_role);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->to_role) && is_array($this->to_role[$index])) {
        return c_base_return_array::s_new($this->to_role[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'to_role[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
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
   *   A string is returned on success.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_to_role() {
    if (is_null($this->to_role)) {
      return NULL;
    }

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
