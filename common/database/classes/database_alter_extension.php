<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER EXTENSION.
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
 * @see: https://www.postgresql.org/docs/current/static/sql-alterextension.html
 */
class c_database_alter_extension extends c_database_query {
  use t_database_action;
  use t_database_action_parameter;
  use t_database_name;

  protected const p_QUERY_COMMAND = 'alter extension';

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

    $value = $this->p_do_build_name() . ' ';
    switch($this->action) {
      case e_database_action::UPDATE:
        $value .= c_database_string::UPDATE;
        if (is_string($this->action_parameter)) {
          $value .= ' ' . c_database_string::TO . ' ' . $this->action_parameter;
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_action::SET_SCHEMA:
        if (is_string($this->set_schema)) {
          $value = $this->p_do_build_set_schema();
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_action::ADD:
        $value .= c_database_string::ADD;
        if ($this->action_parameter instanceof c_database_member_object && $this->action_parameter->do_build() instanceof c_base_return_true) {
          $value .= ' ' . $this->action_parameter->get_value();
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_action::DROP:
        $value .= c_database_string::DROP;
        if ($this->action_parameter instanceof c_database_member_object) {
          if ($this->action_parameter->do_build() instanceof c_base_return_true) {
            $value .= ' ' . $this->action_parameter->get_value();
          }
          else {
            unset($value);
            return new c_base_return_false();
          }
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      default:
        unset($value);
        return new c_base_return_false();
    }
    unset($value);

    $this->value = $value;
    return new c_base_return_true();
  }
}
