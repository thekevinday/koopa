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
require_once('common/database/traits/database_options.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_column.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_set_with_oids.php');

/**
 * The class for building and returning a Postgresql ALTER FOREIGN TABLE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterforeigntable.html
 */
class c_database_alter_foreign_table extends c_database_query {
  use t_database_action;
  use t_database_name;
  use t_database_options;
  use t_database_owner_to;
  use t_database_rename_column;
  use t_database_rename_to;
  use t_database_set_schema;
  use t_database_set_with_oids;

  protected const pr_QUERY_COMMAND = 'alter foreign table';

  protected $if_exists;
  protected $include_descendents; // The '*' following 'name'
  protected $only;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action        = NULL;
    $this->name          = NULL;
    $this->options       = NULL;
    $this->owner_to      = NULL;
    $this->rename_column = NULL;
    $this->rename_to     = NULL;
    $this->set_schema    = NULL;
    $this->set_with_oids = NULL;

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
    unset($this->options);
    unset($this->owner_to);
    unset($this->rename_column);
    unset($this->rename_to);
    unset($this->set_schema);
    unset($this->set_with_oids);

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
   * Assigns IF EXISTS.
   *
   * @param bool|null $if_exists
   *   Set to TRUE to enable IF EXISTS, FALSE to disable.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_if_exists($if_exists) {
    if (is_null($if_exists)) {
      $this->if_exists = NULL;
      return new c_base_return_true();
    }

    if (is_bool($if_exists)) {
      $this->if_exists = $if_exists;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'if_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assigns wildcard '*' after the table name.
   *
   * @param bool|null $include_decendents
   *   Set to TRUE to enable wildcard '*', FALSE to disable.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_include_decendents($include_decendents) {
    if (is_null($include_decendents)) {
      $this->include_decendents = NULL;
      return new c_base_return_true();
    }

    if (is_bool($include_decendents)) {
      $this->include_decendents = $include_decendents;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'include_decendents', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assigns ONLY.
   *
   * @param bool|null $only
   *   Set to TRUE to enable ONLY, FALSE to disable.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_only($only) {
    if (is_null($only)) {
      $this->only = NULL;
      return new c_base_return_true();
    }

    if (is_bool($only)) {
      $this->only = $only;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'only', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
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

    if (is_array($this->rename_column)) {
      if ($this->only) {
        $action .= is_null($action) ? '' : ' ';
        $action .= c_database_string::ONLY;
      }

      $action .= ' ' . $this->p_do_build_rename_column();
    }
    else if (is_array($this->rename_to)) {
      $action .= is_null($action) ? '' : ' ';
      $action .= $this->p_do_build_rename_to();
    }
    else if (is_array($this->set_schema)) {
      $action .= is_null($action) ? '' : ' ';
      $action .= $this->p_do_build_set_schema();
    }
    else {
      if ($this->only) {
        $action .= is_null($action) ? '' : ' ';
        $action .= c_database_string::ONLY;
      }

      $action .= is_null($action) ? '' : ' ';
      // @todo

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
      else if (is_bool($this->set_with_oids)) {
        $action .= $this->p_do_build_set_with_oids();
      }
      else if (is_array($this->inherit)) {
        $action .= $this->p_do_build_inherit();
      }
      else if (is_array($this->owner_to)) {
        $action .= $this->p_do_build_owner_to();
      }
      else if (is_array($this->options)) {
        $action .= $this->p_do_build_options();
      }
      else {
        unset($action);
        return new c_base_return_false();
      }
    }

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;

    if ($this->include_descendents) {
      $this->value .' *';
    }

    $this->value .= ' ' . $action;
    unset($action);

    return new c_base_return_true();
  }
}
