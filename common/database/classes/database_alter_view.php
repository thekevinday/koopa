<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_alter_column.php');
require_once('common/database/traits/database_if_exists.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_reset_view_option.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_set_view_option.php');

/**
 * The class for building and returning a Postgresql ALTER VIEW query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterview.html
 */
class c_database_alter_view extends c_database_query {
  use t_database_alter_column;
  use t_database_if_exists;
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_reset_view_option;
  use t_database_set_schema;
  use t_database_set_view_option;

  protected const p_QUERY_COMMAND = 'alter view';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->alter_column      = NULL;
    $this->if_exists         = NULL;
    $this->name              = NULL;
    $this->owner_to          = NULL;
    $this->rename_to         = NULL;
    $this->reset_view_option = NULL;
    $this->set_schema        = NULL;
    $this->set_view_option   = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->alter_column);
    unset($this->if_exists);
    unset($this->name);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->reset_view_option);
    unset($this->set_schema);
    unset($this->set_view_option);

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

    if (isset($this->if_exists)) {
      $value = $this->p_do_build_if_exists() . ' ' . $value;
    }

    if (isset($this->alter_column)) {
      $value .= ' ' . $this->p_do_build_alter_column();
    }
    else if (isset($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (isset($this->set_schema)) {
      $value .= ' ' . $this->p_do_build_set_schema();
    }
    else if (isset($this->set_view_option)) {
      $value .= ' ' . $this->p_do_build_set_view_option();
    }
    else if (isset($this->reset_view_option)) {
      $value .= ' ' . $this->p_do_build_reset_view_option();
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
