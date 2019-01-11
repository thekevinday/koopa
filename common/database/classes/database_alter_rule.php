<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_on_table.php');
require_once('common/database/traits/database_rename_to.php');


/**
 * The class for building and returning a Postgresql ALTER RULE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterrule.html
 */
class c_database_alter_rule extends c_database_query {
  use t_database_name;
  use t_database_on_table;
  use t_database_rename_to;

  protected const p_QUERY_COMMAND = 'alter rule';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name      = NULL;
    $this->on_table  = NULL;
    $this->rename_to = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->on_table);
    unset($this->rename_to);

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
    if (is_null($this->name) || is_null($this->on_table) || is_null($this->rename_to)) {
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->p_do_build_name();
    $this->value .= ' ' . $this->p_do_build_on_table();
    $this->value .= ' ' . $this->p_do_build_rename_to();

    return new c_base_return_true();
  }
}
