<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER GROUP.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_add_user.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_role_specification.php');

/**
 * The class for building and returning a Postgresql ALTER GROUP query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altergroup.html
 */
class c_database_alter_group extends c_database_query {
  use t_database_add_user;
  use t_database_name;
  use t_database_rename_to;
  use t_database_role_specification;

  protected const p_QUERY_COMMAND = 'alter group';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->add_user           = NULL;
    $this->name               = NULL;
    $this->rename_to          = NULL;
    $this->role_specification = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->add_user);
    unset($this->name);
    unset($this->rename_to);
    unset($this->role_specification);

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
    if (is_array($this->add_user) && isset($this->role_specification)) {
      $value = $this->p_do_build_role_specification();
      $value .= ' ' . $this->p_do_build_add_user();
    }
    else if (isset($this->name) && is_null($this->rename_to)) {
      $value = $this->p_do_build_name();
      $value .= ' ' . $this->p_do_build_rename_to();
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
