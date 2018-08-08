<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DEFAULT PRIVILEGES.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_in_schema.php');
require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_option.php');

/**
 * The class for building and returning a Postgresql ALTER DEFAULT PRIVILEGES query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdefaultpriveleges.html
 */
class c_database_alter_default_priveleges extends c_database_query {
  use t_database_in_schema;
  use t_database_action;
  use t_database_option;

  public const ACTION_NONE   = 0;
  public const ACTION_GRANT  = 1;
  public const ACTION_REVOKE = 2;

  public const ON_NONE      = 0;
  public const ON_TABLES_TO = 1;
  public const ON_SEQUENCES = 2;
  public const ON_FUNCTIONS = 3;
  public const ON_TYPES     = 4;
  public const ON_SCHEMAS   = 5;

  public const PRIVILEGE_NONE       = 0;
  public const PRIVILEGE_SELECT     = 1;
  public const PRIVILEGE_INSERT     = 2;
  public const PRIVILEGE_UPDATE     = 3;
  public const PRIVILEGE_DELETE     = 4;
  public const PRIVILEGE_TRUNCATE   = 5;
  public const PRIVILEGE_REFERENCES = 6;
  public const PRIVILEGE_TRIGGER    = 7;
  public const PRIVILEGE_USAGE      = 8;
  public const PRIVILEGE_EXECUTE    = 9;
  public const PRIVILEGE_CREATE     = 10;
  public const PRIVILEGE_ALL        = 11;

  public const OPTION_NONE     = 0;
  public const OPTION_CASCADE  = 1;
  public const OPTION_RESTRICT = 2;

  protected const pr_QUERY_COMMAND = 'alter default privileges';

  protected $abbreviated;
  protected $option_grant;
  protected $on;
  protected $privileges;
  protected $role_names;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->query_in_schema = NULL;
    $this->query_action    = NULL;
    $this->query_option    = NULL;
    $this->for_targets     = NULL;

    $this->abbreviated  = NULL;
    $this->option_grant = NULL;
    $this->on           = NULL;
    $this->privileges   = NULL;
    $this->role_names   = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->query_in_schema);
    unset($this->query_action);

    unset($this->abbreviated);
    unset($this->option_grant);
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
   * Set the FOR ROLE role targets.
   *
   * @param string|bool|null $target
   *   The for role target to use.
   *   Set to TRUE to use (only) the current role, $append is considered FALSE.
   *   Set to NULL to disable.
   *   When NULL, this will remove all for role targets regardless of the $append parameter.
   * @param bool $append
   *   (optional) When TRUE, the for role target will be appended.
   *   When FALSE, any existing for role targets will be cleared before appending the for role target.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_for_role_targets($target, $append = TRUE) {
    if (is_null($target)) {
      $this->target = NULL;
      return new c_base_return_true();
    }

    if (is_string($target)) {
      if ($append) {
        if (!is_array($this->for_role_targets)) {
          $this->for_role_targets = [];
        }

        $this->for_role_targets[] = $target;
      }
      else {
        $this->for_role_targets = [$target];
      }

      return new c_base_return_true();
    }
    else if ($target === TRUE) {
      $this->for_role_targets = TRUE;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'target', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Enables/Disables the SQL WITH GRANT OPTION.
   *
   * @param bool|null $option_grant
   *   Set to TRUE to append the with grant option or grant option for.
   *   Set to FALSE to not append the with grant option or the grant option for.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_option_grant($option_grant) {
    if (!is_null($option_grant) && !is_bool($option_grant)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option_grant', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->option_grant = $option_grant;
    return new c_base_return_true();
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
      case static::ON_TABLES_TO:
      case static::ON_SEQUENCES:
      case static::ON_FUNCTIONS:
      case static::ON_TYPES:
      case static::ON_SCHEMAS:
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
   *   When NULL, this will remove all privileges regardless of the $append parameter.
   * @param bool $append
   *   (optional) When TRUE, the aggregate signatures will be appended.
   *   When FALSE, any existing aggregate signatures will be cleared before appending the aggregate signature.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_privilege($privilege, $append = TRUE) {
    if (is_null($privilege)) {
      $this->privileges = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_int($privilege)) {
      if ($append) {
        if (!is_array($this->privilege)) {
          $this->privileges = [];
        }

        $this->privileges[] = $privilege;
      }
      else {
        $this->privileges = [$privilege];
      }

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
   *   When NULL, this will remove all role names regardless of the $append parameter.
   * @param bool $append
   *   (optional) When TRUE, the role name will be appended.
   *   When FALSE, any existing role names will be cleared before appending the role name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_role_name($role_name, $append = TRUE) {
    if (is_null($role_name)) {
      $this->role_names = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($role_name instanceof c_database_argument_role_name) {
      if ($append) {
        if (!is_array($this->role_names)) {
          $this->role_names = [];
        }

        $this->role_names[] = $role_name;
      }
      else {
        $this->role_names = [$role_name];
      }

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
        return c_bse_return_array::s_new($this->role_names);
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
   * Get assigned option.
   *
   * @param int|null $index
   *   (optional) Get the argument signature at the specified index.
   *   When NULL, all argument signatures are returned.
   *   This is always considered NULL when for role targets is set to the current role.
   *
   * @return c_base_return_array|c_base_return_string|c_base_return_bool|c_base_return_null
   *   An array of for role targets or NULL if not defined.
   *   TRUE is returned if the current role is to be used as the for role target.
   *   A string of a single for role target is returned if the $index is not NULL.
   *   NULL with the error bit set is returned on error.
   */
  public function get_for_role_targets($index = NULL) {
    if (is_null($this->for_role_targets)) {
      return new c_base_return_null();
    }

    if ($this->for_role_targets === TRUE) {
      return c_base_return_bool::s_new(TRUE);
    }

    if (is_null($index)) {
      if (is_array($this->for_role_targets)) {
        return c_base_return_array::s_new($this->for_role_targets);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->for_role_targets) && is_string($this->for_role_targets[$index])) {
        return c_base_return_string::s_new($this->for_role_targets[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'for_role_targets[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'for_role_targets', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the WITH GRANT OPTION status.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE is returned if the with grant option or grant option for is enabled.
   *   FALSE is returned if the with grant option or grant option for is disabled.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_option_grant() {
    if (is_null($this->option_grant)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->option_grant);
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
        return c_base_return_array::s_new($this->aggregate_signatures);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->aggregate_signatures) && $this->aggregate_signatures[$index] instanceof c_database_argument_aggregate_signature) {
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

    switch ($this->query_action) {
        case static::ACTION_GRANT:
        case static::ACTION_REVOKE:
          break;
        default:
          return new c_base_return_false();
    }

    $this->value = static::pr_QUERY_COMMAND;

    // [ FOR ROLE target_role [, ... ] ]
    if (is_array($this->for_role_targets) && !empty($this->for_role_targets)) {
      $this->value .= ' ' . c_database_string::FOR . ' ' . c_database_string::ROLE;

      $names = NULL;
      foreach ($this->for_role_targets as $schema_name) {
        $names .= ', ' . $schema_name;
      }

      $this->value .= ltrim($names, ',');
      unset($names);
    }

    // [ IN SCHEMA schema_name [, ... ] ]
    if (is_array($this->query_in_schema) && !empty($this->query_in_schema)) {
      $this->value .= ' ' . c_database_string::IN . ' ' . c_database_string::SCHEMA;

      $names = NULL;
      foreach ($this->query_in_schema as $schema_name) {
        $names .= ', ' . $schema_name;
      }

      $this->value .= ltrim($names, ',');
      unset($names);
    }

    if ($this->query_action === static::ACTION_GRANT) {
      $this->value .= ' ' . c_database_string::GRANT;
    }
    else if ($this->query_action === static::ACTION_REVOKE) {
      $this->value .= ' ' . c_database_string::REVOKE;

      if ($this->option_grant) {
        $this->value .= ' ' . c_database_string::GRANT_OPTION_FOR;
      }
    }

    // [ { SELECT | INSERT | UPDATE | DELETE | TRUNCATE | REFERENCES | TRIGGER | USAGE | EXECUTE | CREATE } [, ...] | ALL ]
    $privileges = '';
    foreach ($this->privileges as $privilege) {
      $privileges .= ', ';
      switch ($privilege) {
        case static::PRIVILEGE_SELECT:
          $privileges .= c_database_string::SELECT;
          break;
        case static::PRIVILEGE_INSERT:
          $privileges .= c_database_string::INSERT;
          break;
        case static::PRIVILEGE_UPDATE:
          $privileges .= c_database_string::UPDATE;
          break;
        case static::PRIVILEGE_DELETE:
          $privileges .= c_database_string::DELETE;
          break;
        case static::PRIVILEGE_TRUNCATE:
          $privileges .= c_database_string::TRUNCATE;
          break;
        case static::PRIVILEGE_REFERENCES:
          $privileges .= c_database_string::REFERENCES;
          break;
        case static::PRIVILEGE_TRIGGER:
          $privileges .= c_database_string::TRIGGER;
          break;
        case static::PRIVILEGE_USAGE:
          $privileges .= c_database_string::USAGE;
          break;
        case static::PRIVILEGE_EXECUTE:
          $privileges .= c_database_string::EXECUTE;
          break;
        case static::PRIVILEGE_CREATE:
          $privileges .= c_database_string::CREATE;
          break;
        case static::PRIVILEGE_ALL:
          $privileges .= c_database_string::ALL;
          break;
        default:
          break;
      }
    }

    $this->value .= ltrim($privileges, ',');
    unset($privileges);

    // ON ...
    switch($this->on) {
      case static::ON_TABLES_TO:
        $this->value .= ' ' . c_database_string::ON_TABLES_TO;
        break;
      case static::ON_SEQUENCES:
        $this->value .= ' ' . c_database_string::ON_SEQUENCES;
        break;
      case static::ON_FUNCTIONS:
        $this->value .= ' ' . c_database_string::ON_FUNCTIONS;
        break;
      case static::ON_TYPES:
        $this->value .= ' ' . c_database_string::ON_TYPES;
        break;
      case static::ON_SCHEMAS:
        $this->value .= ' ' . c_database_string::ON_SCHEMAS;
        break;
    }

    // [ TO | FROM ] ... role names ...
    if ($this->query_action === static::ACTION_GRANT) {
      $this->value .= ' ' . c_database_string::TO;
    }
    else if ($this->query_action === static::ACTION_REVOKE) {
      $this->value .= ' ' . c_database_string::FROM;
    }

    foreach ($this->role_names as $role_name) {
      if (!($role_name instanceof c_database_argument_role_name)) {
        continue;
      }

      $role_name->do_build_argument();
      $this->value .= ' ' . $role_name->get_value_exact();
    }
    unset($role_name);

    if ($this->query_action === static::ACTION_GRANT) {
      // [ WITH GRANT OPTION ]
      if ($this->option_grant) {
        $this->value .= ' ' . c_database_string::WITH_GRANT_OPTION;
      }
    }
    else if ($this->query_action === static::ACTION_REVOKE) {
      // [ CASCADE | RESTRICT ]
      if ($this->query_option === static::OPTION_CASCADE) {
        $this->value .= ' ' . c_database_string::CASCADE;
      }
      else if ($this->query_option === static::OPTION_RESTRICT) {
        $this->value .= ' ' . c_database_string::RESTRICT;
      }
    }

    return new c_base_return_true();
  }
}
