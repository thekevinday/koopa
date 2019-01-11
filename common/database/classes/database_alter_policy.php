<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER POLICY.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_on_table.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_to_role.php');
require_once('common/database/traits/database_using_expression.php');
require_once('common/database/traits/database_with_check.php');


/**
 * The class for building and returning a Postgresql ALTER POLICY query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterpolicy.html
 */
class c_database_alter_policy extends c_database_query {
  use t_database_name;
  use t_database_on_table;
  use t_database_rename_to;
  use t_database_to_role;
  use t_database_using_expression;
  use t_database_with_check_expression;

  protected const p_QUERY_COMMAND = 'alter policy';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name                  = NULL;
    $this->on_table              = NULL;
    $this->rename_to             = NULL;
    $this->to_role               = NULL;
    $this->using_expression      = NULL;
    $this->with_check_expression = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->on_table);
    unset($this->rename_to);
    unset($this->to_role);
    unset($this->using_expression);
    unset($this->with_check_expression);

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
    if (is_null($this->name) || !isset($this->on_table)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name() . ' ' . $this->p_do_build_on_table();
    if (isset($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (isset($this->to_role)) {
      $value .= ' ' . $this->p_do_build_to_role();

      if (isset($this->using_expression)) {
        $value .= ' ' . $this->p_do_build_using_expression();
      }

      if (isset($this->with_check_expression)) {
        $value .= ' ' . $this->p_do_build_with_check_expression();
      }
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
