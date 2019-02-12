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

require_once('common/database/traits/database_action_deprecated.php');
require_once('common/database/traits/database_name.php');

/**
 * The class for building and returning a Postgresql MEMBER OBJECT query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterextension.html
 */
class c_database_member_object extends c_database_query {
  use t_database_action_deprecated;
  use t_database_action_parameter;
  use t_database_name;

  protected const p_QUERY_COMMAND = '';


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

    $value = NULL;
    switch($this->action) {
      case e_database_method_object::ACCESS_METHOD:
        $value = c_database_string::ACCESS . ' ' . c_database_string::METHOD . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::AGGREGATE:
        $value = c_database_string::AGGREGATE . ' ' . $this->p_do_build_name();
        // TODO: as a query argument?
        if ($this->action_parameter instanceof c_database_alter_aggregate && $this->action_parameter->do_build()) {
          $value .= ' ( ' . $this->action_parameter->get_value() . ' )';
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::CAST:
        // uses 'name' property to represent AS 'source_type'
        $value = c_database_string::CAST;
        if (is_string($this->action_parameter)) {
          $value .= ' ' . $this->p_do_build_name() . ' ' . c_database_string::AS . ' ( ' . $this->action_parameter . ' )';
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::COLLATION:
        $value = c_database_string::COLLATION . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::CONVERSION:
        $value = c_database_string::CONVERSION . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::DOMAIN:
        $value = c_database_string::DOMAIN . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::EVENT_TRIGGER:
        $value = c_database_string::EVENT . ' ' . c_database_string::TRIGGER . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::FOREIGN_DATA_WRAPPER:
        $value = c_database_string::FOREIGN . ' ' . c_database_string::DATA . ' ' . c_database_string::WRAPPER . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::FOREIGN_TABLE:
        $value = c_database_string::FOREIGN . ' ' . c_database_string::TABLE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::FUNCTION:
        $value = c_database_string::FUNCTION . ' ' . $this->p_do_build_name();
        // TODO: finish adding support for function
        // TODO: as a query argument?
        /*if ($this->action_parameter instanceof c_database_function && $this->action_parameter->do_build()) {
          $value .= ' ( ' . $this->action_parameter->get_value() . ' )';
        }
        else {
          unset($value);
          return new c_base_return_false();
        }*/
        break;
      case e_database_method_object::MATERIALIZED_VIEW:
        $value = c_database_string::MATERIALIZED . ' ' . c_database_string::VIEW . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::OPERATOR:
        $value = c_database_string::OPERATOR . ' ' . $this->p_do_build_name();
        if (isset($this->action_parameter[0]) && is_string($this->action_parameter[0]) && isset($this->action_parameter[1]) && is_string($this->action_parameter[1])) {
          $value .= ' ' . $this->action_parameter[0] . ' ( ' . $this->action_parameter[0] . ', ' . $this->action_parameter[1] . ' )';
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::OPERATOR_CLASS:
        $value = c_database_string::OPERATOR . ' ' . c_database_string::CLASS_ . ' ' . $this->p_do_build_name();
        if (is_string($this->action_parameter)) {
          $value .= ' ' . c_database_string::USING . ' ' . $this->action_parameter;
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::OPERATOR_FAMILY:
        $value = c_database_string::OPERATOR . ' ' . c_database_string::FAMILY . ' ' . $this->p_do_build_name();
        if (is_string($this->action_parameter)) {
          $value .= ' ' . c_database_string::USING . ' ' . $this->action_parameter;
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::LANGUAGE:
        $value = c_database_string::LANGUAGE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::PROCEDURAL:
        $value = c_database_string::PROCEDURAL . ' ' . c_database_string::LANGUAGE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::SCHEMA:
        $value = c_database_string::SCHEMA . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::SEQUENCE:
        $value = c_database_string::SEQUENCE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::SERVER:
        $value = c_database_string::SERVER . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::TABLE:
        $value = c_database_string::TABLE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::TEXT_SEARCH_CONFIGURATION:
        $value = c_database_string::TEXT . ' ' . c_database_string::SEARCH . ' ' . c_database_string::CONFIGURATION . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::TEXT_SEARCH_DICTIONARY:
        $value = c_database_string::TEXT . ' ' . c_database_string::SEARCH . ' ' . c_database_string::DICTIONARY . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::TEXT_SEARCH_PARSER:
        $value = c_database_string::TEXT . ' ' . c_database_string::SEARCH . ' ' . c_database_string::PARSER . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::TEXT_SEARCH_TEMPLATE:
        $value = c_database_string::TEXT . ' ' . c_database_string::SEARCH . ' ' . c_database_string::TEMPLATE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::TRANSFORM_FOR:
        $value = c_database_string::TRANSFORM . ' ' . c_database_string::FOR . ' ' . $this->p_do_build_name();
        if (is_string($this->action_parameter)) {
          $value .= ' ' . c_database_string::LANGUAGE . ' ' . $this->action_parameter;
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
        break;
      case e_database_method_object::TYPE:
        $value = c_database_string::TYPE . ' ' . $this->p_do_build_name();
        break;
      case e_database_method_object::VIEW:
        $value = c_database_string::VIEW . ' ' . $this->p_do_build_name();
        break;
      default:
        unset($value);
        return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
