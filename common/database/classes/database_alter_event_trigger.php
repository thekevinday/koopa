<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER EVENT TRIGGER.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');

/**
 * The class for building and returning a Postgresql ALTER EVENT TRIGGER query string.
 *
 * When no argument mode is specified, then a wildcard * is auto-provided for the aggregate_signature parameter.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altereventtrigger.html
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

    $this->action          = NULL;
    $this->action_property = NULL;
    $this->name            = NULL;
    $this->owner_to        = NULL;
    $this->rename_to       = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->action);
    unset($this->action_property);
    unset($this->name);
    unset($this->owner_to);
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

    $action = NULL;
    switch($this->action) {
      case e_database_action::DISABLE:
        $this->action = c_database_string::DISABLE;
        break;
      case e_database_action::ENABLE:
        $action = c_database_string::ENABLE;
        if ($this->action_property === e_database_property::REPLICA) {
          $action .= ' ' . c_database_string::REPLICA;
        }
        else if ($this->action_property === e_database_property::ALWAYS) {
          $action .= ' ' . c_database_string::ALWAYS;
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_action::OWNER_TO:
        if (is_string($this->owner_to)) {
          $action = $this->p_do_build_owner_to();
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_action::RENAME_TO:
        if (is_string($this->rename_to)) {
          $action = $this->p_do_build_rename_to();
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      default:
        unset($action);
        return new c_base_return_false();
    }

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $action;
    unset($action);

    return new c_base_return_true();
  }
}
