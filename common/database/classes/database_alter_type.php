<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_add_value.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_attribute.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_rename_value.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_type_action.php');

/**
 * The class for building and returning a Postgresql ALTER TYPE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altertype.html
 */
class c_database_alter_type extends c_database_query {
  use t_database_add_value;
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_attribute;
  use t_database_rename_to;
  use t_database_rename_value;
  use t_database_set_schema;
  use t_database_type_action;

  protected const p_QUERY_COMMAND = 'alter type';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->add_value        = NULL;
    $this->name             = NULL;
    $this->owner_to         = NULL;
    $this->rename_attribute = NULL;
    $this->rename_to        = NULL;
    $this->rename_value     = NULL;
    $this->set_schema       = NULL;
    $this->type_action      = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->add_value);
    unset($this->name);
    unset($this->owner_to);
    unset($this->rename_attribute);
    unset($this->rename_to);
    unset($this->rename_value);
    unset($this->set_schema);
    unset($this->type_action);

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

    $value = $this->p_do_build_name();
     if (isset($this->add_value)) {
      $value .= ' ' . $this->p_do_build_add_value();
    }
    else if (isset($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->rename_attribute)) {
      $value .= ' ' . $this->p_do_build_rename_attribute();
    }
    else if (isset($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (isset($this->rename_value)) {
      $value .= ' ' . $this->p_do_build_rename_value();
    }
    else if (isset($this->set_schema)) {
      $value .= ' ' . $this->p_do_build_set_schema();
    }
    else if (isset($this->type_action)) {
      $value .= ' ' . $this->p_do_build_type_action();
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
