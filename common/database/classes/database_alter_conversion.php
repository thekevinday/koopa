<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER CONVERSION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_set_schema.php');

/**
 * The class for building and returning a Postgresql ALTER CONVERSION query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterconversion.html
 */
class c_database_alter_conversion extends c_database_query {
  use t_database_name;
  use t_database_rename_to;
  use t_database_owner_to;
  use t_database_set_schema;

  protected const pr_QUERY_COMMAND = 'alter conversion';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name       = NULL;
    $this->rename_to  = NULL;
    $this->owner_to   = NULL;
    $this->set_schema = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->rename_to);
    unset($this->owner_to);
    unset($this->set_schema);

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
    // the collation name is required.
    if (!is_string($this->name)) {
      return new c_base_return_false();
    }

    $action = NULL;
    if (is_string($this->rename_to)) {
      $action = $this->p_do_build_rename_to();
    }
    else if (is_string($this->owner_to)) {
      $action = $this->p_do_build_owner_to();
    }
    else if (is_string($this->set_schema)) {
      $action = $this->p_do_build_set_schema();
    }
    else {
      unset($action);
      return new c_base_return_false();
    }

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;
    $this->value .= ' ' . $action;
    unset($action);

    return new c_base_return_true();
  }
}