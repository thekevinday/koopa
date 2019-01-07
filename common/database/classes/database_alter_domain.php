<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DOMAIN.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_action.php');
require_once('common/database/enumerations/database_cascade.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_action.php');

/**
 * The class for building and returning a Postgresql ALTER DOMAIN query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdomain.html
 */
class c_database_alter_coalation extends c_database_query {
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_set_schema;
  use t_database_action;

  protected const p_QUERY_COMMAND = 'alter domain';

  protected $expression;
  protected $constraint;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();
;
    $this->action     = NULL;
    $this->name       = NULL;
    $this->owner_to   = NULL;
    $this->rename_to  = NULL;
    $this->set_schema = NULL;

    $this->expression = NULL;
    $this->constraint = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->action);
    unset($this->name);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->set_schema);

    unset($this->expression);
    unset($this->constraint);

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
   * @param string|null $new_name
   *   The new constraint name, when applicable.
   *   Set to NULL to disable.
   * @param int|null $cascade
   *   Set eithe CASCADE/RESTRICT, when applicable.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_constraint($constraint, $new_name = NULL, $cascade = NULL) {
    if (is_null($constraint)) {
      $this->constraint = NULL;
      return new c_base_return_true();
    }

    if (!is_string($constraint)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'constraint', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($new_name) && !is_string($new_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'new_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($cascade) && $cascade !== e_database_cascade::CASCADE && $cascade !== e_database_cascade::RESTRICT) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->constraint = [
      'name' => $constraint,
      'name_new' => $new_name,
      'cascade' => $cascade,
    ];
    return new c_base_return_true();
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

    return c_base_return_string::s_new($this->constraint['name']);
  }

  /**
   * Get constraint cascade option.
   *
   * @return c_base_return_int|c_base_return_null
   *   An integer representing the constraint cascade option.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_constraint_cascade() {
    if (is_null($this->constraint) || is_null($this->constraint['cascade'])) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->constraint['cascade']);
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
  public function get_constraint_new_name() {
    if (is_null($this->constraint) || is_null($this->constraint['name_new'])) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->constraint['name_new']);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->name)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name() . ' ';
    switch ($this->action) {
        case e_database_action::ADD:
          if (!is_array($this->constraint)) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= c_database_string::ADD;
          $value .= ' ' . $this->constraint['name'];

          if ($this->property === e_database_property::NOT_VALID) {
            $value .= ' ' . c_database_string::NOT_VALID;
          }
          break;

        case e_database_action::DROP:
          $value .= c_database_string::DROP;
          if ($this->property === e_database_property::NOT_NULL) {
            $value .= ' ' . c_database_string::NOT_NULL;
          }
          break;

        case e_database_action::DROP_CONSTRAINT:
          if (!is_array($this->constraint['name'])) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= c_database_string::DROP_CONSTRAINT;
          if ($this->property === e_database_property::IF_EXISTS) {
            $value .= ' ' . c_database_string::IF_EXISTS;
          }

          $value .= ' ' . $this->constraint['name'];

          if ($this->constraint['cascade'] === e_database_cascade::CASCADE) {
            $value .= ' ' . c_database_string::CASCADE;
          }
          else if ($this->constraint['cascade'] === e_database_cascade::RESTRICT) {
            $value .= ' ' . c_database_string::RESTRICT;
          }
          break;

        case e_database_action::DROP_DEFAULT:
          $value .= c_database_string::DROP_DEFAULT;
          break;

        case e_database_action::OWNER_TO:
          if (!isset($this->owner_to)) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= $this->p_do_build_owner_to();
          break;

        case e_database_action::RENAME_CONSTRAINT:
          if (!isset($this->constraint['name']) || !isset($this->constraint['name_new'])) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= c_database_string::RENAME_CONSTRAINT . ' ' . $this->constraint['name'] . ' ' . c_database_string::TO . ' ' . $this->constraint['name_new'];
          break;

        case e_database_action::RENAME_TO:
          if (!isset($this->rename_to)) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= $this->p_do_build_rename_to();
          break;

        case e_database_action::SET:
          $value = c_database_string::SET;
          if ($this->property === e_database_property::NOT_NULL) {
            $value .= ' ' . c_database_string::NOT_NULL;
          }
          break;

        case e_database_action::SET_DEFAULT:
          if (!isset($this->expression)) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= c_database_string::SET_DEFAULT . ' ' . $this->expression;
          break;

        case e_database_action::SET_SCHEMA:
          if (!isset($this->set_schema)) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= $this->p_do_build_set_schema();
          break;

        case e_database_action::VALIDATE_CONSTRAINT:
          if (!is_array($this->constraint)) {
            unset($value);
            return new c_base_return_false();
          }

          $value .= c_database_string::VALIDATE_CONSTRAINT . ' ' . $this->constraint['name'];
          break;

        default:
          unset($value);
          return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
