<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ROLLBACK.
 *
 * ROLLBACK replaces the ABORT command.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');


/**
 * The class for building and returning a Postgresql ROLLBACK query string.
 *
 * This does not implement the optional keywords WORK and TRANSACTION because they have no effect.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-rollback.html
 */
class c_database_rollback extends c_database_query {
  protected const p_QUERY_COMMAND  = 'rollback';

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
}
