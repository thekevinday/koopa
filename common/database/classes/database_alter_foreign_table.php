<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_column_to.php');
require_once('common/database/traits/database_rename_to.php');

/**
 * The class for building and returning a Postgresql ALTER FOREIGN TABLE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterforeigntable.html
 */
class c_database_alter_foreign_table extends c_database_query {
  protected const pr_QUERY_COMMAND = 'alter foreign table';
  use t_database_action;
  use t_database_name;
  use t_database_rename_column_to;
  use t_database_rename_to;

  protected $if_exists;
  protected $include_descendents; // The '*' following 'name'
  protected $only;

// sub-forms:
// ADD COLUMN
// DROP COLUMN
// SET DATA TYPE
// SET DEFAULT
// SET NOT NULL
// SET STATISTICS
// SET
// RESET
// SET STORAGE
// ADD ...
// VALIDATE CONSTRAINT
// DROP CONSTRAINT
// DISABLE/ENABLE
// SET WITH OIDS
// SET WITHOUT OIDS
// INHERIT
// NO INHERIT
// OWNER
// OPTIONS
// RENAME
// SET SCHEMA

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action           = NULL;
    $this->name             = NULL;
    $this->rename_column_to = NULL;
    $this->rename_to        = NULL;

    $this->if_exists           = NULL;
    $this->include_descendents = NULL;
    $this->only                = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->action);
    unset($this->name);
    unset($this->rename_column_to);
    unset($this->rename_to);

    unset($this->if_exists);
    unset($this->include_descendents);
    unset($this->only);
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

    $action = NULL;
    if ($this->if_exists) {
      $action = ' ' . c_database_string::IF_EXISTS;
    }

    if ($this->only) {
      $action = ' ' . c_database_string::ONLY;
    }

    // @todo

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;

    if ($this->include_descendents) {
      $this->value .' *';
    }

    $this->value .= $action;
    unset($action);

    return new c_base_return_true();
  }
}
