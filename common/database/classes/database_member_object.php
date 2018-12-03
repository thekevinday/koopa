<?php
/**
 * @file
 * Provides a class for specific Postgesql query: MEMBER_OBJECT.
 *
 * TODO: this should either be an argument parameter or a database query.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_name.php');

/**
 * The class for building and returning a Postgresql MEMBER OBJECT query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterextension.html
 */
class c_database_member_object extends c_database_query {
  use t_database_action;
  use t_database_action_parameter;
  use t_database_name;

  protected const pr_QUERY_COMMAND = '';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action           = NULL;
    $this->action_parameter = NULL;
    $this->name             = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->action);
    unset($this->action_parameter);
    unset($this->name);

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
      case e_database_method_object::ACCESS_METHOD:
        $action = c_database_string::ACCESS_METHOD . ' ' . $this->name;
        break;
      case e_database_method_object::AGGREGATE:
        $action = c_database_string::AGGREGATE . ' ' . $this->name;
        // TODO: as a query argument?
        if ($this->action_parameter instanceof c_database_alter_aggregate && $this->action_parameter->do_build()) {
          $action .= ' ( ' . $this->action_parameter->get_value() . ' )';
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::CAST:
        // uses 'name' property to represent AS 'source_type'
        $action = c_database_string::CAST;
        if (is_string($this->action_parameter)) {
          $action .= ' ' . $this->name . ' ' . c_database_string::AS . ' ( ' . $this->action_parameter . ' )';
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::COLLATION:
        $action = c_database_string::COLLATION . ' ' . $this->name;
        break;
      case e_database_method_object::CONVERSION:
        $action = c_database_string::CONVERSION . ' ' . $this->name;
        break;
      case e_database_method_object::DOMAIN:
        $action = c_database_string::DOMAIN . ' ' . $this->name;
        break;
      case e_database_method_object::EVENT_TRIGGER:
        $action = c_database_string::EVENT_TRIGGER . ' ' . $this->name;
        break;
      case e_database_method_object::FOREIGN_DATA_WRAPPER:
        $action = c_database_string::FOREIGN_DATA_WRAPPER . ' ' . $this->name;
        break;
      case e_database_method_object::FOREIGN_TABLE:
        $action = c_database_string::FOREIGN_TABLE . ' ' . $this->name;
        break;
      case e_database_method_object::FUNCTION:
        $action = c_database_string::FUNCTION . ' ' . $this->name;
        // TODO: finish adding support for function
        // TODO: as a query argument?
        /*if ($this->action_parameter instanceof c_database_function && $this->action_parameter->do_build()) {
          $action .= ' ( ' . $this->action_parameter->get_value() . ' )';
        }
        else {
          unset($action);
          return new c_base_return_false();
        }*/
        break;
      case e_database_method_object::MATERIALIZED_VIEW:
        $action = c_database_string::MATERIALIZED_VIEW . ' ' . $this->name;
        break;
      case e_database_method_object::OPERATOR:
        $action = c_database_string::OPERATOR . ' ' . $this->name;
        if (isset($this->action_parameter[0]) && is_string($this->action_parameter[0]) && isset($this->action_parameter[1]) && is_string($this->action_parameter[1])) {
          $action .= ' ' . $this->action_parameter[0] . ' ( ' . $this->action_parameter[0] . ', ' . $this->action_parameter[1] . ' )';
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::OPERATOR_CLASS:
        $action = c_database_string::OPERATOR_CLASS . ' ' . $this->name;
        if (is_string($this->action_parameter)) {
          $action .= ' ' . c_database_string::USING . ' ' . $this->action_parameter;
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::OPERATOR_FAMILY:
        $action = c_database_string::OPERATOR_FAMILY . ' ' . $this->name;
        if (is_string($this->action_parameter)) {
          $action .= ' ' . c_database_string::USING . ' ' . $this->action_parameter;
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::LANGUAGE:
        $action = c_database_string::LANGUAGE . ' ' . $this->name;
        break;
      case e_database_method_object::PROCEDURAL:
        $action = c_database_string::PROCEDURAL . ' ' . c_database_string::LANGUAGE . ' ' . $this->name;
        break;
      case e_database_method_object::SCHEMA:
        $action = c_database_string::SCHEMA . ' ' . $this->name;
        break;
      case e_database_method_object::SEQUENCE:
        $action = c_database_string::SEQUENCE . ' ' . $this->name;
        break;
      case e_database_method_object::SERVER:
        $action = c_database_string::SERVER . ' ' . $this->name;
        break;
      case e_database_method_object::TABLE:
        $action = c_database_string::TABLE . ' ' . $this->name;
        break;
      case e_database_method_object::TEXT_SEARCH_CONFIGURATION:
        $action = c_database_string::TEXT_SEARCH_CONFIGURATION . ' ' . $this->name;
        break;
      case e_database_method_object::TEXT_SEARCH_DICTIONARY:
        $action = c_database_string::TEXT_SEARCH_DICTIONARY . ' ' . $this->name;
        break;
      case e_database_method_object::TEXT_SEARCH_PARSER:
        $action = c_database_string::TEXT_SEARCH_PARSER . ' ' . $this->name;
        break;
      case e_database_method_object::TEXT_SEARCH_TEMPLATE:
        $action = c_database_string::TEXT_SEARCH_TEMPLATE . ' ' . $this->name;
        break;
      case e_database_method_object::TRANSFORM_FOR:
        $action = c_database_string::TRANSFORM_FOR . ' ' . $this->name;
        if (is_string($this->action_parameter)) {
          $action .= ' ' . c_database_string::LANGUAGE . ' ' . $this->action_parameter;
        }
        else {
          unset($action);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::TYPE:
        $action = c_database_string::TYPE . ' ' . $this->name;
        break;
      case e_database_method_object::VIEW:
        $action = c_database_string::VIEW . ' ' . $this->name;
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
