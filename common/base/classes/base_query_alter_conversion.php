<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER CONVERSION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_query.php');

require_once('common/base/traits/base_query.php');

/**
 * The class for building and returning a Postgresql ALTER CONVERSION query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterconversion.html
 */
class c_base_query_alter_conversion extends c_base_query {
  use t_base_query_name;
  use t_base_query_rename_to;
  use t_base_query_owner_to;
  use t_base_query_set_schema;

  protected const pr_QUERY_COMMAND = 'alter conversion';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->query_name       = NULL;
    $this->query_rename_to  = NULL;
    $this->query_owner_to   = NULL;
    $this->query_set_schema = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->query_name);
    unset($this->query_rename_to);
    unset($this->query_owner_to);
    unset($this->query_set_schema);

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
    if (!is_string($this->query_name)) {
      return new c_base_return_false();
    }

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $this->query_name;

    if (is_string($this->query_rename_to)) {
      $this->value .= ' ' . $this->pr_QUERY_RENAME_TO . ' (' . $this->query_rename_to . ')';
    }
    elseif (is_string($this->query_owner_to_user_name)) {
      $this->value .= ' ' . $this->pr_QUERY_OWNER_TO . ' (' . $this->query_owner_to_user_name . ')';
    }
    elseif (is_string($this->query_set_schema)) {
      $this->value .= ' ' . $this->pr_QUERY_SET_SCHEMA . ' (' . $this->query_set_schema . ')';
    }
    else {
      $this->value = NULL;
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }
}
