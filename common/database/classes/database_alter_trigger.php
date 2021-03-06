<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_depends_on_extension.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');


/**
 * The class for building and returning a Postgresql ALTER TRIGGER query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altertrigger.html
 */
class c_database_alter_trigger extends c_database_query {
  use t_database_depends_on_extension;
  use t_database_name;
  use t_database_rename_to;

  protected const p_QUERY_COMMAND = 'alter trigger';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->depends_on_extension = NULL;
    $this->name                 = NULL;
    $this->rename_to            = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->depends_on_extension);
    unset($this->name);
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
    if (is_null($this->name)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name();
     if (isset($this->depends_on_extension)) {
      $value .= ' ' . $this->p_do_build_depends_on_extension();
    }
    else if (isset($this->rename_to)) {
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
