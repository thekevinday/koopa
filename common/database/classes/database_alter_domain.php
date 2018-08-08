<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DOMAIN.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/base/traits/base_query.php');

/**
 * The class for building and returning a Postgresql ALTER DOMAIN query string.
 *
 * When no argument mode is specified, then a wildcard * is auto-provided for the aggregate_signature parameter.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alteraggregate.html
 */
class c_database_alter_coalation extends c_database_query {
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_set_schema;
  use t_database_action;
  use t_database_property;
  use t_database_option;

  public const ACTION_NONE                = 0;
  public const ACTION_ADD                 = 1;
  public const ACTION_DROP                = 2;
  public const ACTION_DROP_CONSTRAINT     = 3;
  public const ACTION_DROP_DEFAULT        = 4;
  public const ACTION_OWNER_TO            = 5;
  public const ACTION_RENAME_CONSTRAINT   = 6;
  public const ACTION_RENAME_TO           = 7;
  public const ACTION_SET                 = 8;
  public const ACTION_SET_DEFAULT         = 9;
  public const ACTION_SET_SCHEMA          = 10;
  public const ACTION_VALIDATE_CONSTRAINT = 11;

  public const PROPERTY_NONE      = 0;
  public const PROPERTY_NOT_VALID = 1;
  public const PROPERTY_IF_EXISTS = 2;

  public const OPTION_NONE     = 0;
  public const OPTION_CASCADE  = 1;
  public const OPTION_RESTRICT = 2;

  protected const pr_QUERY_COMMAND = 'alter domain';

  protected $expression;
  protected $contraint;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->query_name            = NULL;
    $this->query_owner_to        = NULL;
    $this->query_rename_to       = NULL;
    $this->query_set_schema      = NULL;
    $this->query_action          = NULL;
    $this->query_action_property = NULL;
    $this->query_option          = NULL;

    $this->expression    = NULL;
    $this->contraint     = NULL;
    $this->contraint_new = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->query_name);
    unset($this->query_owner_to);
    unset($this->query_rename_to);
    unset($this->query_set_schema);
    unset($this->query_action);
    unset($this->query_action_property);
    unset($this->query_option);

    unset($this->expression);
    unset($this->contraint);
    unset($this->contraint_new);

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
   * Assigns the expression.
   *
   * The expression is required by the SET_DEFAULT action.
   *
   * @param string|null $expression
   *   The expression string to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_expression($expression) {
    if (is_null($expression)) {
      $this->expression = NULL;
      return new c_base_return_true();
    }

    if (is_string($expression)) {
      $this->expression = $expression;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assigns the constraint name or domain constraint string.
   *
   * @param string|null $constraint
   *   The constraint string to use.
   *   Set to NULL to disable.
   * @param string|null $constraint_new
   *   When the constraint
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_constraint($constraint, $constraint_new = NULL) {
    if (is_null($constraint)) {
      if (is_null($constraint_new)) {
        $this->constraint = NULL;
        $this->constraint_new = NULL;
        return new c_base_return_true();
      }

      if (is_string($constraint_new)) {
        $this->constraint = NULL;
        $this->constraint_new = $constraint_new;
        return new c_base_return_true();
      }
    }
    else if (is_string($constraint)) {
      if (is_null($constraint_new)) {
        $this->constraint = $constraint;
        $this->constraint_new = NULL;
        return new c_base_return_true();
      }
      else if (is_string($constraint_new)) {
        $this->constraint = $constraint;
        $this->constraint_new = $constraint_new;
        return new c_base_return_true();
      }
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get expression.
   *
   * @return c_base_return_string|c_base_return_null
   *   An string representing the expression.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_expression() {
    if (is_null($this->expression)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->expression);
  }

  /**
   * Get constraint.
   *
   * @return c_base_return_string|c_base_return_null
   *   An string representing the constraint.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_constraint() {
    if (is_null($this->constraint)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->constraint);
  }

  /**
   * Get constraint (new name).
   *
   * This is only relevant when action is set to self::ACTION_RENAME_CONSTRAINT.
   *
   * @return c_base_return_string|c_base_return_null
   *   An string representing the new constraint name.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_constraint_new() {
    if (is_null($this->constraint_new)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->constraint_new);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->query_name)) {
      return new c_base_return_false();
    }

    $action = NULL;
    switch ($this->query_action) {
        case static::ACTION_ADD:
          if (!is_string($this->constraint)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::ADD;
          $action .= ' ' . $this->constraint;

          if ($this->property === static::PROPERTY_NOT_VALID) {
            $action .= ' ' . c_database_string::NOT_VALID;
          }
          break;

        case static::ACTION_DROP:
          $action = c_database_string::DROP;
          if ($this->property === static::PROPERTY_NOT_NULL) {
            $action .= ' ' . c_database_string::NOT_NULL;
          }
          break;

        case static::ACTION_DROP_CONSTRAINT:
          if (!is_string($this->constraint)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::DROP_CONSTRAINT;
          if ($this->property === static::PROPERTY_IF_EXISTS) {
            $action .= ' ' . c_database_string::IF_EXISTS;
          }

          $action .= ' ' . $this->constraint;

          if ($this->option === static::OPTION_RESTRICT) {
            $action .= ' ' . c_database_string::RESTRICT;
          }
          else if ($this->option === static::OPTION_CASCADE) {
            $action .= ' ' . c_database_string::CASCADE;
          }
          break;

        case static::ACTION_DROP_DEFAULT:
          $action = c_database_string::DROP_DEFAULT;
          break;

        case static::ACTION_OWNER_TO:
          if (!is_string($this->query_owner_to_user_name)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::OWNER_TO . ' (' . $this->query_owner_to_user_name . ')';
          break;

        case static::ACTION_RENAME_CONSTRAINT:
          if (!is_string($this->constraint) || !is_string($this->constraint_new)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::RENAME_CONSTRAINT . ' ' . $this->constraint . ' ' . c_database_string::TO . ' ' . $this->constraint_new;
          break;

        case static::ACTION_RENAME_TO:
          if (!is_string($this->query_rename_to)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::RENAME_TO . ' (' . $this->query_rename_to . ')';
          break;

        case static::ACTION_SET:
          $action = c_database_string::SET;
          if ($this->property === static::PROPERTY_NOT_NULL) {
            $action .= ' ' . c_database_string::NOT_NULL;
          }
          break;

        case static::ACTION_SET_DEFAULT:
          if (!is_string($this->expression)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::SET_DEFAULT . ' ' . $this->expression;
          break;

        case static::ACTION_SET_SCHEMA:
          if (!is_string($this->query_set_schema)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = ' ' . c_database_string::SET_SCHEMA . ' (' . $this->query_set_schema . ')';
          break;

        case static::ACTION_VALIDATE_CONSTRAINT:
          if (!is_string($this->constraint)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::VALIDATE_CONSTRAINT . ' ' . $this->constraint;
          break;

        default:
          unset($action);
          return new c_base_return_false();
    }

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $this->query_name;
    $this->value .= ' ' . $action;

    return new c_base_return_true();
  }
}
