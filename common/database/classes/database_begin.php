<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_transaction_action.php');
require_once('common/database/traits/database_transaction_mode.php');

/**
 * The class for building and returning a Postgresql BEGIN query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-begin.html
 */
class c_database_begin extends c_database_query {
  use t_database_transaction_action;
  use t_database_transaction_mode;

  protected const p_QUERY_COMMAND = 'begin';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->transaction_action = NULL;
    $this->transaction_mode   = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->transaction_action);
    unset($this->transaction_mode);

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
    $value = NULL;

    if (isset($this->transaction_action)) {
      $value = $this->p_do_build_transaction_action();
    }

    if (isset($this->transaction_mode)) {
      if (is_null($value)) {
        $value = $this->p_do_build_transaction_mode();
      }
      else {
        $value = ' ' . $this->p_do_build_transaction_mode();
      }
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
