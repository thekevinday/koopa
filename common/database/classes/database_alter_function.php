<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER FUNCTION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_argument_type.php');
require_once('common/database/traits/database_depends_on_extension.php');
require_once('common/database/traits/database_function_action.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_restrict.php');
require_once('common/database/traits/database_set_schema.php');

/**
 * The class for building and returning a Postgresql ALTER FUNCTION query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterfunction.html
 */
class c_database_alter_coalation extends c_database_query {
  use t_database_argument_type;
  use t_database_depends_on_extension;
  use t_database_function_action;
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_restrict;
  use t_database_set_schema;

  protected const p_QUERY_COMMAND = 'alter function';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->argument_type        = NULL;
    $this->depends_on_extension = NULL;
    $this->function_action      = NULL;
    $this->name                 = NULL;
    $this->owner_to             = NULL;
    $this->rename_to            = NULL;
    $this->restrict             = NULL;
    $this->set_schema           = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->argument_type);
    unset($this->depends_on_extension);
    unset($this->function_action);
    unset($this->name);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->restrict);
    unset($this->set_schema);

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
    if (!is_string($this->name)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name();

    if (is_array($this->argument_type) && !empty($this->argument_type)) {
      $value .= ' ' . $this->p_do_build_argument_type();
    }

    if (is_array($this->function_action)) {
      $value .= ' ' . $this->p_do_build_function_action();

      if ($this->restrict) {
        $value .= ' ' . $this->p_do_build_restrict();
      }
    }
    else if (is_string($this->rename_to)) {
      $value .= $this->p_do_build_rename_to();
    }
    else if (is_string($this->owner_to)) {
      $value .= $this->p_do_build_owner_to();
    }
    else if (is_string($this->set_schema)) {
      $value .= $this->p_do_build_set_schema();
    }
    else if (is_string($this->depends_on_extension)) {
      $value .= $this->p_do_build_depends_on_extension();
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
