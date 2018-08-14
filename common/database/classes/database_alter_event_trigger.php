<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER EVENT TRIGGER.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_query.php');

/**
 * The class for building and returning a Postgresql ALTER EVENT TRIGGER query string.
 *
 * When no argument mode is specified, then a wildcard * is auto-provided for the aggregate_signature parameter.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alteraggregate.html
 */
class c_database_alter_coalation extends c_database_query {
  use t_database_action;
  use t_database_action_property;
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;

  protected const pr_QUERY_COMMAND = 'alter event trigger';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->query_action          = NULL;
    $this->query_action_property = NULL;
    $this->query_name            = NULL;
    $this->query_owner_to        = NULL;
    $this->query_rename_to       = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->query_action);
    unset($this->query_action_property);
    unset($this->query_name);
    unset($this->query_owner_to);
    unset($this->query_rename_to);

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
    $this->value = NULL;

    // @todo

    return new c_base_return_true();
  }
}
