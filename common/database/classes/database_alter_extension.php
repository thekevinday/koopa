<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');
require_once('common/database/classes/database_member_object.php');

require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_name.php');


/**
 * The class for building and returning a Postgresql ALTER COALATION query string.
 *
 * When no argument mode is specified, then a wildcard * is auto-provided for the aggregate_signature parameter.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterextension.html
 */
class c_database_alter_extension extends c_database_query {
  use t_database_action;
  use t_database_action_parameter;
  use t_database_name;

  protected const pr_QUERY_COMMAND = 'alter extension';

  protected $member_object;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action           = NULL;
    $this->action_parameter = NULL;

    $this->member_object = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->action);
    unset($this->action_parameter);

    unset($this->member_object);

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

    $action = $this->name . ' ';
    switch($this->action) {
      case e_database_action::UPDATE:
        $action .= c_database_string::UPDATE;
        if (is_string($this->action_parameter)) {
          $action .= ' ' . c_database_string::TO . ' ' . $this->action_parameter;
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_action::SET_SCHEMA:
        if (is_string($this->set_schema)) {
          $action = $this->p_do_build_set_schema();
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_action::ADD:
        $action .= c_database_string::ADD;
        if ($this->action_parameter instanceof c_database_member_object && $this->action_parameter->do_build() instanceof c_base_return_true) {
          $action .= ' ' . $this->action_parameter->get_value();
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_action::DROP:
        $action .= c_database_string::DROP;
        if ($this->action_parameter instanceof c_database_member_object) {
          if ($this->action_parameter->do_build() instanceof c_base_return_true) {
            $action .= ' ' . $this->action_parameter->get_value();
          }
          else {
            unset($action);
            return new c_base_return_false();
          }
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
    unset($action);

    $this->value = $action;
    return new c_base_return_true();
  }
}
