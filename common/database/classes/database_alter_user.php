<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
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
 * The class for building and returning a Postgresql ALTER USER query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alteruser.html
 */
class c_database_alter_user extends c_database_query {
  use t_database_in_database;
  use t_database_name;
  use t_database_rename_to;
  use t_database_reset_configuration_parameter;
  use t_database_role_specification;
  use t_database_set_configuration_parameter;
  use t_database_with_role_option;

  protected const p_QUERY_COMMAND = 'alter user';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->in_database                   = NULL;
    $this->name                          = NULL;
    $this->rename_to                     = NULL;
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
    unset($this->rename_to);
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
    if (isset($this->name)) {
      if (isset($this->rename_to)) {
        $this->value = static::p_QUERY_COMMAND;
        $this->value .= ' ' . $this->p_do_build_name();
        $this->value .= ' ' . $this->p_do_build_rename_to();
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }
    else if (!isset($this->role_specification)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_role_specification();

    if (isset($this->with_role_option)) {
      $value .= ' ' . $this->p_do_build_with_role_option();
    }
    else if (isset($this->set_configuration_parameter)) {
      $value .= ' ' . $this->p_do_build_set_configuration_parameter();
    }
    else if (isset($this->reset_configuration_parameter)) {
      $value .= ' ' . $this->p_do_build_reset_configuration_parameter();
    }
    else {
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
