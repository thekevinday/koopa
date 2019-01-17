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
 * Provide the sql PRIVILEGE functionality.
 */
trait t_database_privilege {
  protected $privilege;

  /**
   * Assigns the SQL privileges.
   *
   * @param int|null $privilege
   *   Set a privilege code.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_privilege($privilege) {
    if (is_null($privilege)) {
      $this->privileges = NULL;
      return new c_base_return_true();
    }

    if (is_int($privilege)) {
      // no reason to add any privilege once ALL is present.
      if ($this->privilege === e_database_privilege::ALL) {
        return new c_base_return_true();
      }

      if ($privilege === e_database_privilege::ALL) {
        $this->privilege = e_database_privilege::ALL;
      }
      else {
        if (!is_array($this->privilege)) {
          $this->privilege = [];
        }

        $this->privilege[] = $privilege;
      }

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'privilege', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the privileges.
   *
   * @param int|null $index
   *   (optional) Get the privilege at the specified index.
   *   When NULL, all privileges are returned.
   *
   * @return c_base_return_int|c_base_return_array|c_base_return_null
   *   An array of privileges or NULL if not defined.
   *   A single privilege is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_privilege($index = NULL) {
    if (is_null($this->privilege)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if ($this->privilege === e_database_privilege::ALL) {
        return c_base_return_array::s_new([$this->privilege]);
      }
      else if (is_array($this->privilege)) {
        return c_base_return_array::s_new($this->privilege);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->privilege) && is_int($this->privilege[$index])) {
        return clone($this->privilege[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'privilege[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'privilege', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_privilege() {
    if ($this->privilege === e_database_privilege::ALL) {
      return c_database_string::ALL;
    }

    $privileges = [];
    foreach ($this->privilege as $privilege) {
      switch ($privilege) {
        case e_database_privilege::SELECT:
          $privileges[] = c_database_string::SELECT;
          break;
        case e_database_privilege::INSERT:
          $privileges[] = c_database_string::INSERT;
          break;
        case e_database_privilege::UPDATE:
          $privileges[] = c_database_string::UPDATE;
          break;
        case e_database_privilege::DELETE:
          $privileges[] = c_database_string::DELETE;
          break;
        case e_database_privilege::TRUNCATE:
          $privileges[] = c_database_string::TRUNCATE;
          break;
        case e_database_privilege::REFERENCES:
          $privileges[] = c_database_string::REFERENCES;
          break;
        case e_database_privilege::TRIGGER:
          $privileges[] = c_database_string::TRIGGER;
          break;
        case e_database_privilege::USAGE:
          $privileges[] = c_database_string::USAGE;
          break;
        case e_database_privilege::EXECUTE:
          $privileges[] = c_database_string::EXECUTE;
          break;
        case e_database_privilege::CREATE:
          $privileges[] = c_database_string::CREATE;
          break;
        default:
          break;
      }
    }

    return implode(', ', $privileges);
  }
}
