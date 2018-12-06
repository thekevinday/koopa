<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DEFAULT PRIVILEGES.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_action.php');
require_once('common/database/enumerations/database_cascade.php');
require_once('common/database/enumerations/database_on.php');
require_once('common/database/enumerations/database_privilege.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/enumerations/database_cascade.php');

require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_cascade.php');
require_once('common/database/traits/database_for_role.php');
require_once('common/database/traits/database_grant_option_for.php');
require_once('common/database/traits/database_in_schema.php');
require_once('common/database/traits/database_with_grant_option.php');

/**
 * The class for building and returning a Postgresql ALTER DEFAULT PRIVILEGES query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdefaultpriveleges.html
 */
class c_database_alter_default_priveleges extends c_database_query {
  use t_database_action;
  use t_database_cascade;
  use t_database_for_role;
  use t_database_grant_option_for;
  use t_database_in_schema;
  use t_database_with_grant_option;

  protected const p_QUERY_COMMAND = 'alter default privileges';

  protected $abbreviated;
  protected $on;
  protected $privileges;
  protected $role_names;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action            = NULL;
    $this->cascade           = NULL;
    $this->for_role          = NULL;
    $this->grant_option_for  = NULL;
    $this->in_schema         = NULL;
    $this->with_grant_option = NULL;

    $this->abbreviated = NULL;
    $this->on          = NULL;
    $this->privileges  = NULL;
    $this->role_names  = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->action);
    unset($this->cascade);
    unset($this->for_role);
    unset($this->grant_option_for);
    unset($this->in_schema);
    unset($this->with_grant_option);

    unset($this->abbreviated);
    unset($this->on);
    unset($this->privileges);
    unset($this->role_names);
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Assigns the SQL ON operation.
   *
   * @param int|null $on
   *   Whether or not to use the ON operation in the query.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_on($on) {
    if (is_null($on)) {
      $this->on = NULL;
      return new c_base_return_true();
    }

    switch ($on) {
      case e_database_on::TABLES_TO:
      case e_database_on::SEQUENCES:
      case e_database_on::FUNCTIONS:
      case e_database_on::TYPES:
      case e_database_on::SCHEMAS:
        $this->on = $on;
        return new c_base_return_true();
      default:
        break;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'on', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assigns the SQL privileges.
   *
   * @param int|null $privilege
   *   Whether or not to use the ON operation in the query.
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
      if (!is_array($this->privilege)) {
        $this->privileges = [];
      }

      $this->privileges[] = $privilege;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'privilege', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the role names.
   *
   * @param c_database_argument_role_name|null $role_name
   *   The role name to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_role_name($role_name) {
    if (is_null($role_name)) {
      $this->role_names = NULL;
      return new c_base_return_true();
    }

    if ($role_name instanceof c_database_argument_role_name) {
      if (!is_array($this->role_names)) {
        $this->role_names = [];
      }

      $this->role_names[] = $role_name;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'role_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the role names.
   *
   * @param int|null $index
   *   (optional) Get the role name at the specified index.
   *   When NULL, all role names are returned.
   *
   * @return c_database_argument_role_name|c_base_return_array|c_base_return_null
   *   An array of role names or NULL if not defined.
   *   A single role name is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_role_name($index = NULL) {
    if (is_null($this->role_names)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->role_names)) {
        return c_base_return_array::s_new($this->role_names);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->role_names) && $this->role_names[$index] instanceof c_database_argument_role_name) {
        return $this->role_names[$index];
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'role_names[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'role_names', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the ON operation status.
   *
   * @return c_base_return_int|c_base_return_null
   *   Integer representing the on operation is returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_on() {
    if (is_null($this->on)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->on);
  }

  /**
   * Get the privileges.
   *
   * @param int|null $index
   *   (optional) Get the privilege at the specified index.
   *   When NULL, all privileges are returned.
   *
   * @return int|c_base_return_array|c_base_return_null
   *   An array of privileges or NULL if not defined.
   *   A single privilege is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_privilege($index = NULL) {
    if (is_null($this->privileges)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->privileges)) {
        return c_base_return_array::s_new($this->privileges);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->privileges) && $this->privileges[$index] instanceof c_database_argument_aggregate_signature) {
        return clone($this->privileges[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'privileges[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'privileges', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->grant) || !(is_array($this->role_names) && !empty($this->role_names))) {
      return new c_base_return_false();
    }

    switch ($this->action) {
        case e_database_action::_GRANT:
        case e_database_action::_REVOKE:
          break;
        default:
          return new c_base_return_false();
    }

    $value = NULL;
    if ($this->for_role) {
      $value .= $this->p_do_build_for_role();
    }

    // [ IN SCHEMA schema_name [, ... ] ]
    if (is_array($this->in_schema) && !empty($this->in_schema)) {
      $value .= is_null($value) ? '' : ' ';
      $value .= $this->p_do_build_in_schema();
    }

    if ($this->action === e_database_action::ACTION_GRANT) {
      $value .= is_null($value) ? '' : ' ';
      $value .= c_database_string::GRANT;
    }
    else if ($this->action === e_database_action::ACTION_REVOKE) {
      $value .= is_null($value) ? '' : ' ';
      $value .= c_database_string::REVOKE;

      if ($this->grant_option_for) {
        $value .= ' ' . $this->p_do_build_grant_option_for();
      }
    }

    // [ { SELECT | INSERT | UPDATE | DELETE | TRUNCATE | REFERENCES | TRIGGER | USAGE | EXECUTE | CREATE } [, ...] | ALL ]
    $privileges = '';
    foreach ($this->privileges as $privilege) {
      $privileges .= ', ';
      switch ($privilege) {
        case e_database_privilege::SELECT:
          $privileges .= c_database_string::SELECT;
          break;
        case e_database_privilege::INSERT:
          $privileges .= c_database_string::INSERT;
          break;
        case e_database_privilege::UPDATE:
          $privileges .= c_database_string::UPDATE;
          break;
        case e_database_privilege::DELETE:
          $privileges .= c_database_string::DELETE;
          break;
        case e_database_privilege::TRUNCATE:
          $privileges .= c_database_string::TRUNCATE;
          break;
        case e_database_privilege::REFERENCES:
          $privileges .= c_database_string::REFERENCES;
          break;
        case e_database_privilege::TRIGGER:
          $privileges .= c_database_string::TRIGGER;
          break;
        case e_database_privilege::USAGE:
          $privileges .= c_database_string::USAGE;
          break;
        case e_database_privilege::EXECUTE:
          $privileges .= c_database_string::EXECUTE;
          break;
        case e_database_privilege::CREATE:
          $privileges .= c_database_string::CREATE;
          break;
        case e_database_privilege::ALL:
          $privileges .= c_database_string::ALL;
          break;
        default:
          break;
      }
    }

    $value .= is_null($value) ? '' : ' ';
    $value .= ltrim($privileges, ', ');
    unset($privileges);

    // ON ...
    switch($this->on) {
      case e_database_on::TABLES_TO:
        $value .= ' ' . c_database_string::ON_TABLES_TO;
        break;
      case e_database_on::SEQUENCES:
        $value .= ' ' . c_database_string::ON_SEQUENCES;
        break;
      case e_database_on::FUNCTIONS:
        $value .= ' ' . c_database_string::ON_FUNCTIONS;
        break;
      case e_database_on::TYPES:
        $value .= ' ' . c_database_string::ON_TYPES;
        break;
      case e_database_on::SCHEMAS:
        $value .= ' ' . c_database_string::ON_SCHEMAS;
        break;
    }

    // [ TO | FROM ] ... role names ...
    if ($this->action === e_database_action::GRANT) {
      $value .= ' ' . c_database_string::TO;
    }
    else if ($this->action === e_database_action::REVOKE) {
      $value .= ' ' . c_database_string::FROM;
    }

    foreach ($this->role_names as $role_name) {
      if (!($role_name instanceof c_database_argument_role_name)) {
        continue;
      }

      $role_name->do_build_argument();
      $value .= ' ' . $role_name->get_value_exact();
    }
    unset($role_name);

    if ($this->action === e_database_action::GRANT) {
      if ($this->with_grant_option) {
        $value .= ' ' . $this->p_do_build_with_grant_option();
      }
    }
    else if ($this->action === e_database_action::REVOKE) {
      if (is_int($this->cascade)) {
        $value .= ' ' . $this->p_do_build_cascade();
      }
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
