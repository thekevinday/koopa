<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER LANGUAGE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_procedural.php');
require_once('common/database/traits/database_rename_to.php');


/**
 * The class for building and returning a Postgresql ALTER LANGUAGE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alteraggregate.html
 */
class c_database_alter_language extends c_database_query {
  use t_database_name;
  use t_database_owner_to;
  use t_database_procedural;
  use t_database_rename_to;

  protected const p_QUERY_COMMAND = 'alter language';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name       = NULL;
    $this->owner_to   = NULL;
    $this->procedural = NULL;
    $this->rename_to  = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->owner_to);
    unset($this->procedural);
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
    if (!is_string($this->name)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name();
    if (is_string($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (is_array($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else {
      unset($value);
      return new c_base_return_false();
    }

    if (is_bool($this->procedural)) {
      $this->value = c_database_string::ALTER . ' ' . $this->p_do_build_procedural() . ' ' . c_database_string::LANGUAGE;
    }
    else {
      $this->value = static::p_QUERY_COMMAND;
    }

    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
