<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DOMAIN.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_action.php');
require_once('common/database/enumerations/database_option.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_action.php');
require_once('common/database/traits/database_option.php');

/**
 * The class for building and returning a Postgresql ALTER DOMAIN query string.
 *
 * When no argument mode is specified, then a wildcard * is auto-provided for the aggregate_signature parameter.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdomain.html
 */
class c_database_alter_coalation extends c_database_query {
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_set_schema;
  use t_database_action;
  use t_database_action_property;
  use t_database_option;

  protected const pr_QUERY_COMMAND = 'alter domain';

  protected $expression;
  protected $contraint;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name            = NULL;
    $this->owner_to        = NULL;
    $this->rename_to       = NULL;
    $this->set_schema      = NULL;
    $this->action          = NULL;
    $this->action_property = NULL;
    $this->option          = NULL;

    $this->expression    = NULL;
    $this->contraint     = NULL;
    $this->contraint_new = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->set_schema);
    unset($this->action);
    unset($this->action_property);
    unset($this->option);

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
    if (is_null($this->name)) {
      return new c_base_return_false();
    }

    $action = NULL;
    switch ($this->action) {
        case e_database_action::ADD:
          if (!is_string($this->constraint)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::ADD;
          $action .= ' ' . $this->constraint;

          if ($this->property === e_database_property::NOT_VALID) {
            $action .= ' ' . c_database_string::NOT_VALID;
          }
          break;

        case e_database_action::DROP:
          $action = c_database_string::DROP;
          if ($this->property === e_database_property::NOT_NULL) {
            $action .= ' ' . c_database_string::NOT_NULL;
          }
          break;

        case e_database_action::DROP_CONSTRAINT:
          if (!is_string($this->constraint)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::DROP_CONSTRAINT;
          if ($this->property === e_database_property::IF_EXISTS) {
            $action .= ' ' . c_database_string::IF_EXISTS;
          }

          $action .= ' ' . $this->constraint;

          $option = $this->p_do_build_option();
          if (is_string($option)) {
            $action .= ' ' . $option;
          }
          unset($option);
          break;

        case e_database_action::DROP_DEFAULT:
          $action = c_database_string::DROP_DEFAULT;
          break;

        case e_database_action::OWNER_TO:
          if (!is_string($this->owner_to)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = $this->p_do_build_owner_to();
          break;

        case e_database_action::RENAME_CONSTRAINT:
          if (!is_string($this->constraint) || !is_string($this->constraint_new)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::RENAME_CONSTRAINT . ' ' . $this->constraint . ' ' . c_database_string::TO . ' ' . $this->constraint_new;
          break;

        case e_database_action::RENAME_TO:
          if (!is_string($this->rename_to)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = $this->p_do_build_rename_to();
          break;

        case e_database_action::SET:
          $action = c_database_string::SET;
          if ($this->property === e_database_property::NOT_NULL) {
            $action .= ' ' . c_database_string::NOT_NULL;
          }
          break;

        case e_database_action::SET_DEFAULT:
          if (!is_string($this->expression)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = c_database_string::SET_DEFAULT . ' ' . $this->expression;
          break;

        case e_database_action::SET_SCHEMA:
          if (!is_string($this->set_schema)) {
            unset($action);
            return new c_base_return_false();
          }

          $action = $this->p_do_build_set_schema();
          break;

        case e_database_action::VALIDATE_CONSTRAINT:
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
    $this->value .= ' ' . $this->name;
    $this->value .= ' ' . $action;

    return new c_base_return_true();
  }
}
