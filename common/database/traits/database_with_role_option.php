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

require_once('common/database/enumerations/database_role_option.php');

/**
 * Provide the sql WITH (role) option functionality.
 */
trait t_database_with_role_option {
  protected $with_role_option;

  /**
   * Set the with role option.
   *
   * @param int|null $type
   *   The option type from e_database_role_option.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   * @param string|null $value
   *   (optional) A value string associated with the option type.
   *   Required by some option types, ignored by others.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_with_role_option($type, $value = NULL) {
    if (is_null($type)) {
      $this->with_role_option = NULL;
      return new c_base_return_true();
    }

    $role_option = [
      'type' => $type,
      'value' => NULL,
    ];

    switch ($type) {
      case e_database_role_option::BYPASSRLS:
      case e_database_role_option::CREATEDB:
      case e_database_role_option::CREATEROLE:
      case e_database_role_option::INHERIT:
      case e_database_role_option::LOGIN:
      case e_database_role_option::NOBYPASSRLS:
      case e_database_role_option::NOCREATEDB:
      case e_database_role_option::NOCREATEROLE:
      case e_database_role_option::NOINHERIT:
      case e_database_role_option::NOLOGIN:
      case e_database_role_option::NOREPLICATION:
      case e_database_role_option::NOSUPERUSER:
      case e_database_role_option::REPLICATION:
      case e_database_role_option::SUPERUSER:
        break;

      case e_database_role_option::CONNECTION_LIMIT:
        if (is_int($value)) {
          $role_option['value'] = $value;
        }
        else if (!is_null($value)) {
          unset($role_option);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
        break;

      case e_database_role_option::PASSWORD:
      case e_database_role_option::PASSWORD_ENCRYPTED:
      case e_database_role_option::VALIDUNTIL:
        if (is_string($value)) {
          $placeholder = $this->add_placeholder($value);
          if ($placeholder->has_error()) {
            unset($role_option);
            return c_base_return_error::s_false($placeholder->get_error());
          }

          $role_option['value'] = $placeholder;
          unset($placeholder);
        }
        else if (!is_null($value)) {
          unset($role_option);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
        break;

      default:
        unset($role_option);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_array($this->with_role_option)) {
      $this->with_role_option = [];
    }

    $this->with_role_option[] = $role_option;
    unset($role_option);

    return new c_base_return_true();
  }

  /**
   * Get the with role option settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of with role option settings.
   *   NULL is returned if not set (with role option is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_with_role_option() {
    if (is_null($this->with_role_option)) {
      return new c_base_return_null();
    }

    if (is_array($this->with_role_option)) {
      return c_base_return_array::s_new($this->with_role_option);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'with_role_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_with_role_option() {
    $values = [];
    foreach ($this->with_role_option as $role_option) {
      if ($role_option['type'] === e_database_role_option::BYPASSRLS) {
        $values[] = c_database_string::BYPASSRLS;
      }
      else if ($role_option['type'] === e_database_role_option::CREATEDB) {
        $values[] = c_database_string::CREATEDB;
      }
      else if ($role_option['type'] === e_database_role_option::CREATEROLE) {
        $values[] = c_database_string::CREATEROLE;
      }
      else if ($role_option['type'] === e_database_role_option::INHERIT) {
        $values[] = c_database_string::INHERIT;
      }
      else if ($role_option['type'] === e_database_role_option::LOGIN) {
        $values[] = c_database_string::LOGIN;
      }
      else if ($role_option['type'] === e_database_role_option::NOBYPASSRLS) {
        $values[] = c_database_string::NOBYPASSRLS;
      }
      else if ($role_option['type'] === e_database_role_option::NOCREATEDB) {
        $values[] = c_database_string::NOCREATEDB;
      }
      else if ($role_option['type'] === e_database_role_option::NOCREATEROLE) {
        $values[] = c_database_string::NOCREATEROLE;
      }
      else if ($role_option['type'] === e_database_role_option::NOINHERIT) {
        $values[] = c_database_string::NOINHERIT;
      }
      else if ($role_option['type'] === e_database_role_option::NOLOGIN) {
        $values[] = c_database_string::NOLOGIN;
      }
      else if ($role_option['type'] === e_database_role_option::NOREPLICATION) {
        $values[] = c_database_string::NOREPLICATION;
      }
      else if ($role_option['type'] === e_database_role_option::NOSUPERUSER) {
        $values[] = c_database_string::NOSUPERUSER;
      }
      else if ($role_option['type'] === e_database_role_option::REPLICATION) {
        $values[] = c_database_string::REPLICATION;
      }
      else if ($role_option['type'] === e_database_role_option::SUPERUSER) {
        $values[] = c_database_string::SUPERUSER;
      }
      else if ($role_option['type'] === e_database_role_option::CONNECTION_LIMIT) {
        $values[] = c_database_string::CONNECTION_LIMIT . ' ' . $role_option['value'];
      }
      else if ($role_option['type'] === e_database_role_option::PASSWORD) {
        $values[] = c_database_string::PASSWORD . ' ' . $role_option['value'];
      }
      else if ($role_option['type'] === e_database_role_option::PASSWORD_ENCRYPTED) {
        $values[] = c_database_string::PASSWORD_ENCRYPTED . ' ' . $role_option['value'];
      }
      else if ($role_option['type'] === e_database_role_option::VALIDUNTIL) {
        $values[] = c_database_string::VALIDUNTIL . ' ' . $role_option['value'];
      }
    }
    unset($role_option);

    $value = c_database_string::WITH;
    $value .= ' ' . implode(', ', $values);
    unset($values);

    return $value;
  }
}
