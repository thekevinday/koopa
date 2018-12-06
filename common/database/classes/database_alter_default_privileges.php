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

require_once('common/database/traits/database_in_schema.php');
require_once('common/database/traits/database_action.php');

/**
 * The class for building and returning a Postgresql ALTER DEFAULT PRIVILEGES query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdefaultpriveleges.html
 */
class c_database_alter_default_priveleges extends c_database_query {
  use t_database_in_schema;
  use t_database_action;

  protected const p_QUERY_COMMAND = 'alter default privileges';

  protected $abbreviated;
  protected $cascade;
  protected $option_grant;
  protected $on;
  protected $privileges;
  protected $role_names;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action    = NULL;
    $this->in_schema = NULL;

    $this->abbreviated  = NULL;
    $this->cascade      = NULL;
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

    unset($this->action);
    unset($this->in_schema);

    unset($this->abbreviated);
    unset($this->cascade);
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
   * Assigns the SQL CASCADE/RESTRICT option.
   *
   * @param int|null $cascade
   *   Whether or not to use CASCADE/RESTRICT in the query.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_cascade($cascade) {
    if (is_null($cascade)) {
      $this->cascade = NULL;
      return new c_base_return_true();
    }

    switch ($cascade) {
      case e_database_cascade::CASCADE:
      case e_database_cascade::RESTRICT:
        $this->cascade = $cascade;
        return new c_base_return_true();
      default:
        break;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the FOR ROLE role targets.
   *
   * @param string|bool|null $target
   *   The for role target to use.
   *   Set to TRUE to use (only) the current role, $append is considered FALSE.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_for_role_targets($target) {
    if (is_null($target)) {
      $this->target = NULL;
      return new c_base_return_true();
    }

    if (is_string($target)) {
      if (!is_array($this->for_role_targets)) {
        $this->for_role_targets = [];
      }

      $this->for_role_targets[] = $target;

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
   * Get the CASCADE/RESTRICT operation status.
   *
   * @return c_base_return_int|c_base_return_null
   *   Integer representing the on operation is returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_cascade() {
    if (is_null($this->cascade)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->cascade);
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

    // [ FOR ROLE target_role [, ... ] ]
    $action = NULL;
    if (is_array($this->for_role_targets) && !empty($this->for_role_targets)) {
      $action = c_database_string::FOR . ' ' . c_database_string::ROLE;
      $action .= ' ' . implode(', ', $this->for_role_targets);
    }

    // [ IN SCHEMA schema_name [, ... ] ]
    if (is_array($this->in_schema) && !empty($this->in_schema)) {
      $action .= is_null($action) ? '' : ' ';
      $action .= $this->p_do_build_in_schema();
    }

    if ($this->action === e_database_action::ACTION_GRANT) {
      $action .= is_null($action) ? '' : ' ';
      $action .= c_database_string::GRANT;
    }
    else if ($this->action === e_database_action::ACTION_REVOKE) {
      $action .= is_null($action) ? '' : ' ';
      $action .= c_database_string::REVOKE;

      if ($this->option_grant) {
        $action .= ' ' . c_database_string::GRANT_OPTION_FOR;
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

    $action .= is_null($action) ? '' : ' ';
    $action .= ltrim($privileges, ', ');
    unset($privileges);

    // ON ...
    switch($this->on) {
      case e_database_on::TABLES_TO:
        $action .= ' ' . c_database_string::ON_TABLES_TO;
        break;
      case e_database_on::SEQUENCES:
        $action .= ' ' . c_database_string::ON_SEQUENCES;
        break;
      case e_database_on::FUNCTIONS:
        $action .= ' ' . c_database_string::ON_FUNCTIONS;
        break;
      case e_database_on::TYPES:
        $action .= ' ' . c_database_string::ON_TYPES;
        break;
      case e_database_on::SCHEMAS:
        $action .= ' ' . c_database_string::ON_SCHEMAS;
        break;
    }

    // [ TO | FROM ] ... role names ...
    if ($this->action === e_database_action::GRANT) {
      $action .= ' ' . c_database_string::TO;
    }
    else if ($this->action === e_database_action::REVOKE) {
      $action .= ' ' . c_database_string::FROM;
    }

    foreach ($this->role_names as $role_name) {
      if (!($role_name instanceof c_database_argument_role_name)) {
        continue;
      }

      $role_name->do_build_argument();
      $action .= ' ' . $role_name->get_value_exact();
    }
    unset($role_name);

    if ($this->action === e_database_action::GRANT) {
      // [ WITH GRANT OPTION ]
      if ($this->option_grant) {
        $action .= ' ' . c_database_string::WITH_GRANT_OPTION;
      }
    }
    else if ($this->action === e_database_action::REVOKE) {
      // [ CASCADE | RESTRICT ]
      if ($this->cascade === e_database_option::CASCADE) {
        $value .= ' ' . c_database_string::CASCADE;
      }
      else if ($this->cascade === e_database_option::RESTRICT) {
        $value .= ' ' . c_database_string::RESTRICT;
      }
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;
    $this->value .= ' ' . $action;
    unset($action);

    return new c_base_return_true();
  }
}
