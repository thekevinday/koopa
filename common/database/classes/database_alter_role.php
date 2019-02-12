<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER ROLE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_in_database.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_reset_configuration_parameter.php');
require_once('common/database/traits/database_role_specification.php');
require_once('common/database/traits/database_set_configuration_parameter.php');
require_once('common/database/traits/database_with_role_option.php');


/**
 * The class for building and returning a Postgresql ALTER ROLE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterrole.html
 */
class c_database_alter_role extends c_database_query {
  use t_database_in_database;
  use t_database_name;
  use t_database_rename_to;
  use t_database_reset_configuration_parameter;
  use t_database_role_option;
  use t_database_role_specification;
  use t_database_set_configuration_parameter;

  protected const p_QUERY_COMMAND = 'alter role';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->in_database                   = NULL;
    $this->name                          = NULL;
    $this->rename                        = NULL;
    $this->reset_configuration_parameter = NULL;
    $this->role_specification            = NULL;
    $this->set_configuration_parameter   = NULL;
    $this->with_role_option              = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->in_database);
    unset($this->name);
    unset($this->rename);
    unset($this->reset_configuration_parameter);
    unset($this->role_specification);
    unset($this->set_configuration_parameter);
    unset($this->with_role_option);

    parent::__destruct();
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
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->role_specification) && is_null($this->name)) {
      return new c_base_return_false();
    }

    if (isset($this->role_specification)) {
      $value = $this->p_do_build_role_specification();

      if (isset($this->with_role_option)) {
        $value .= ' ' . $this->p_do_build_with_role_option();
      }
      else {
        unset($value);
        return new c_base_return_false();
      }

      if (isset($this->in_database)) {
        $value .= ' ' . $this->p_do_build_in_database();
      }

      if (isset($this->set_configuration_parameter)) {
        $value .= ' ' . $this->p_do_build_set_configuration_parameter();
      }
      else if (isset($this->reset_configuration_parameter)) {
        $value .= ' ' . $this->p_do_build_reset_configuration_parameter();
      }
      else {
        unset($value);
        return new c_base_return_false();
      }
    }
    else if (isset($this->rename_to)) {
      $value = $this->p_do_build_name();
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else {
      unset($value);
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
