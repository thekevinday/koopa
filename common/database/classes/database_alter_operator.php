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
require_once('common/database/traits/database_operand_left.php');
require_once('common/database/traits/database_operand_right.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_set_operator.php');
require_once('common/database/traits/database_set_schema.php');


/**
 * The class for building and returning a Postgresql ALTER OPERATOR query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alteroperator.html
 */
class c_database_alter_operator extends c_database_query {
  use t_database_name;
  use t_database_operand_left;
  use t_database_operand_right;
  use t_database_owner_to;
  use t_database_set_operator;
  use t_database_set_schema;

  protected const p_QUERY_COMMAND = 'alter operator';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name          = NULL;
    $this->operand_left  = NULL;
    $this->operand_right = NULL;
    $this->owner_to      = NULL;
    $this->set_operator  = NULL;
    $this->set_schema    = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->operand_left);
    unset($this->operand_right);
    unset($this->owner_to);
    unset($this->set_operator);
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
    if (is_null($this->name) || is_null($this->operand_left) || is_null($this->operand_right)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name() . ' (' . $this->p_do_build_operand_left() . ', ' . $this->p_do_build_operand_right() . ')';
    if (isset($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->set_schema)) {
      $value .= ' ' . $this->p_do_build_set_schema();
    }
    else if (is_array($this->set_operator)) {
      $value .= ' ' . $this->p_do_build_set_operator();
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
