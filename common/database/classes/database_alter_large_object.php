<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_oid.php');
require_once('common/database/traits/database_owner_to.php');

/**
 * The class for building and returning a Postgresql ALTER LARGE OBJECT query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterlargeobject.html
 */
class c_database_alter_large_object extends c_database_query {
  use t_database_oid;
  use t_database_owner_to;

  protected const p_QUERY_COMMAND = 'alter large object';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->oid      = NULL;
    $this->owner_to = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->oid);
    unset($this->owner_to);

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
    if (is_null($this->oid) || !is_array($this->owner_to)) {
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->p_do_build_oid();
    $this->value .= ' ' . $this->p_do_build_owner_to();
    unset($value);

    return new c_base_return_true();
  }
}
