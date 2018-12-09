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

require_once('common/database/enumerations/database_user.php');

/**
 * Provide the sql ADD USER / DROP USER functionality.
 */
trait t_database_add_user {
  protected $add_user;

  /**
   * Set the add user or drop user.
   *
   * @param string|int|bool|null $role_type
   *   The user name (role name) to use.
   *   Set to TRUE to toggle to ADD USER (default).
   *   Set to FALSE to toggle to DROP USER.
   *   SET TO e_database_user::CURRENT or SET TO e_database_user::SESSION for reserved names.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_add_user($name) {
    if (is_null($name)) {
      $this->add_user = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($name) && !is_string($name) && $name !== e_database_user::CURRENT && $name !== e_database_user::SESSION) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->add_user)) {
      $this->add_user = [
        'type' => TRUE,
        'names' => [],
      ];
    }

    if (is_bool($name)) {
      $this->add_user['type'] = $name;
    }
    else {
      $this->add_user['names'][] = $name;
    }

    return new c_base_return_true();
  }

  /**
   * Get the add/drop user settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of add/drop user settings on success.
   *   NULL is returned if not set (add/drop user is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_add_user() {
    if (is_null($this->add_user)) {
      return new c_base_return_null();
    }

    if (is_array($this->add_use)) {
        return c_base_return_array::s_new($this->add_user);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'add_user', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_add_user() {
    if (is_null($this->add_user)) {
      return NULL;
    }

    $values = [];
    foreach ($this->add_user['names'] as $name) {
      if ($name === e_database_user::CURRENT) {
        $values[] = c_database_string::CURRENT;
      }
      else if ($name === e_database_user::SESSION) {
        $values[] = c_database_string::SESSION;
      }
      else if (is_string($name)) {
        $values[] = $name;
      }
    }

    $value = $this->add_user['type'] ? c_database_string::ADD : c_database_string::DROP;
    $value .= ' ' . c_database_string::USER;
    $value .= ' ' . implode(', ', $values);
    return $value;
  }
}
