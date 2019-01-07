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
 * Provide the sql role specification functionality.
 */
trait t_database_role_specification {
  protected $role_specification;

  /**
   * Set the role specification.
   *
   * @param int|string|null $name
   *   A string representing the role name to use.
   *   May be an integer of either e_database_role::CURRENT or e_database_role::SESSION.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_role_specification($name) {
    if (is_null($name)) {
      $this->role_specification = NULL;
      return new c_base_return_true();
    }

    if (is_string($name)) {
      $placeholder = $this->add_placeholder($name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->role_specification = $placeholder;
      unset($placeholder);
    }
    else if ($name !== e_database_role::CURRENT && $name !== e_database_role::SESSION) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);

      $this->role_specification = $name;
    }

    return new c_base_return_true();
  }

  /**
   * Get the role specification.
   *
   * @return c_base_return_int|i_database_query_placeholder|c_base_return_null
   *   A role name query placeholder or an integer representing either e_database_role::CURRENT or e_database_role::SESSION on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_role_specification($index = NULL) {
    if (is_null($this->role_specification)) {
      return new c_base_return_null();
    }

    if ($this->role_specification === e_database_role::CURRENT || $this->role_specification === e_database_role::SESSION) {
      return c_base_return_int::s_new($this->role_specification);
    }
    else if (isset($this->role_specification)) {
      return clone($this->role_specification);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'role_specification', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_role_specification() {
    $value = NULL;
    if (is_string($this->role_specification)) {
      $value = $this->role_specification;
    }
    else if ($this->role_specification === e_database_role::CURRENT) {
      $value = c_database_string::CURRENT;
    }
    else if ($this->role_specification === e_database_role::SESSION) {
      $value = c_database_string::SESSION;
    }

    return strval($value);
  }
}
