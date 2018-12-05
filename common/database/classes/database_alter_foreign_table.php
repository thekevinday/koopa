<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER FOREIGN TABLE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_alter_foeign_table_action.php');
require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_column.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_schema.php');

/**
 * The class for building and returning a Postgresql ALTER FOREIGN TABLE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterforeigntable.html
 */
class c_database_alter_foreign_table extends c_database_query {
  use t_database_name;
  use t_database_rename_column;
  use t_database_rename_to;
  use t_database_set_schema;

  protected const p_QUERY_COMMAND = 'alter foreign table';

  protected $actions;
  protected $if_exists;
  protected $include_descendents; // The '*' following 'name'
  protected $only;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name          = NULL;
    $this->rename_column = NULL;
    $this->rename_to     = NULL;
    $this->set_schema    = NULL;

    $this->actions             = NULL;
    $this->if_exists           = NULL;
    $this->include_descendents = NULL;
    $this->only                = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->name);
    unset($this->rename_column);
    unset($this->rename_to);
    unset($this->set_schema);

    unset($this->actions);
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
   * Set or append and action.
   *
   * @param c_database_alter_foreign_table_action|null $action
   *   A specific action to this class.
   *   Set to NULL to disable.
   *   When NULL, this will remove all actions.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_action($action) {
    if (is_null($action)) {
      $this->actions = NULL;
      return new c_base_return_true();
    }

    if (is_string($action)) {
      if (!is_array($this->actions)) {
        $this->actions = [];
      }

      $this->actions[] = $action;
    }
    else {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return new c_base_return_true();
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
   * Get an action or all actions.
   *
   * @param int|null $index
   *   (optional) Get the action at the specified index.
   *   When NULL, all actions are returned.
   *
   * @return c_database_alter_foreign_table_action|c_base_return_array|c_base_return_null
   *   An array of actions or NULL if not defined.
   *   A single action is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action($index = NULL) {
    if (is_null($this->actions)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->actions)) {
        return c_base_return_array::s_new($this->actions);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->actions) && $this->actions[$index] instanceof c_database_alter_foreign_table_action) {
        return $this->actions[$index];
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'actions[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'actions', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the if exists setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean representing the IF EXISTS setting.
   *   NULL is returned if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_if_exists($index = NULL) {
    if (is_null($this->if_exists)) {
      return new c_base_return_null();
    }

    if (is_bool($index)) {
      return c_base_return_bool::s_new($this->if_exists);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'if_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the include decendents setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean representing the '*' setting.
   *   NULL is returned if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_include_decendents($index = NULL) {
    if (is_null($this->include_decendents)) {
      return new c_base_return_null();
    }

    if (is_bool($index)) {
      return c_base_return_bool::s_new($this->include_decendents);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'include_decendents', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the set only setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean representing the ONLY setting.
   *   NULL is returned if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_only($index = NULL) {
    if (is_null($this->set_only)) {
      return new c_base_return_null();
    }

    if (is_bool($index)) {
      return c_base_return_bool::s_new($this->set_only);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_only', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    if (!is_string($this->name)) {
      return new c_base_return_false();
    }

    $value = NULL;
    if ($this->if_exists) {
      $value = ' ' . c_database_string::IF_EXISTS;
    }

    if (is_array($this->rename_column)) {
      if ($this->only) {
        $value .= is_null($value) ? '' : ' ';
        $value .= c_database_string::ONLY;
      }

      $value .= ' ' . $this->p_do_build_rename_column();
    }
    else if (is_array($this->rename_to)) {
      $value .= is_null($value) ? '' : ' ';
      $value .= $this->p_do_build_rename_to();
    }
    else if (is_array($this->set_schema)) {
      $value .= is_null($value) ? '' : ' ';
      $value .= $this->p_do_build_set_schema();
    }
    else {
      if ($this->only) {
        $value .= is_null($value) ? '' : ' ';
        $value .= c_database_string::ONLY;
      }

      $value .= is_null($value) ? '' : ' ';
      if (is_array($this->actions) && !empty($this->actions)) {
        $actions = [];
        foreach ($this->actions as $action) {
          if ($action instanceof c_database_alter_foreign_table_action && $action->do_build() instanceof c_base_return_true) {
            $actions[] = $action->get_value_exact();
          }
        }
        unset($action);

        $value .= implode(', ', $actions);
        unset($actions);
      }
      else {
        unset($value);
        return new c_base_return_false();
      }
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;

    if ($this->include_descendents) {
      $this->value .' *';
    }

    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
