<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_reset_configuration_parameter.php');
require_once('common/database/traits/database_set_configuration_parameter.php');


/**
 * The class for building and returning a Postgresql ALTER SYSTEM query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altersystem.html
 */
class c_database_alter_system extends c_database_query {
  use t_database_reset_configuration_parameter;
  use t_database_set_configuration_parameter;

  protected const p_QUERY_COMMAND = 'alter system';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action_validate_constraint    = NULL;
    $this->reset_configuration_parameter = NULL;
    $this->set_configuration_parameter   = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->reset_configuration_parameter);
    unset($this->set_configuration_parameter);

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
    if (isset($this->set_configuration_parameter)) {
      $value = $this->p_do_build_set_configuration_parameter();
    }
    else if (isset($this->set_configuration_parameter)) {
      $value = $this->p_do_build_set_configuration_parameter();
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
