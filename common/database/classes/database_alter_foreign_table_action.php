<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER FOREIGN TABLE action.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_add_column.php');
require_once('common/database/traits/database_alter_column.php');
require_once('common/database/traits/database_constraint.php');
require_once('common/database/traits/database_drop_column.php');
require_once('common/database/traits/database_enable_trigger.php');
require_once('common/database/traits/database_inherit.php');
require_once('common/database/traits/database_options.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_set_with_oids.php');

/**
 * The class for building and returning a Postgresql ALTER FOREIGN TABLE action query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterforeigntable.html
 */
class c_database_alter_foreign_table_action extends c_database_query {
  use t_database_add_column;
  use t_database_alter_column;
  use t_database_constraint;
  use t_database_drop_column;
  use t_database_enable_trigger;
  use t_database_inherit;
  use t_database_options;
  use t_database_owner_to;
  use t_database_set_with_oids;

  protected const p_QUERY_COMMAND = '';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->add_column     = NULL;
    $this->alter_column   = NULL;
    $this->constraint     = NULL;
    $this->drop_column    = NULL;
    $this->enable_trigger = NULL;
    $this->inherit        = NULL;
    $this->options        = NULL;
    $this->owner_to       = NULL;
    $this->set_with_oids  = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->add_column);
    unset($this->alter_column);
    unset($this->constaint);
    unset($this->drop_column);
    unset($this->enable_trigger);
    unset($this->inherit);
    unset($this->options);
    unset($this->owner_to);
    unset($this->set_with_oids);
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
    if (is_array($this->add_column)) {
      $value = $this->p_do_build_add_column();
    }
    else if (is_array($this->alter_column)) {
      $value = $this->p_do_build_alter_column();
    }
    else if (is_array($this->constraint)) {
      $value = $this->p_do_build_constraint();
    }
    else if (is_array($this->drop_column)) {
      $value = $this->p_do_build_drop_column();
    }
    else if (is_array($this->enable_trigger)) {
      $value = $this->p_do_build_enable_trigger();
    }
    else if (is_array($this->inherit)) {
      $value = $this->p_do_build_inherit();
    }
    else if (is_array($this->options)) {
      $value = '(' . $this->p_do_build_options() . ')';
    }
    else if (is_array($this->owner_to)) {
      $value = $this->p_do_build_owner_to();
    }
    else if (is_bool($this->set_with_oids)) {
      $value = $this->p_do_build_set_with_oids();
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
