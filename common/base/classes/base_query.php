<?php
/**
 * @file
 * Provides classes for specific Postgesql Queries.
 *
 * The objects should return query strings to be processed by the database class.
 *
 * The following query parameter placeholders are reserved for use for auto-generation reasons:
 *   - :qp_# such that # is any valid whole number >= 0.
 *
 * @todo: a base class is needed for handling all query parameters because SQL functions may be used anywhere!
 *        This will make argument handling more complex than using simple integers, strings, etc..
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/base/interfaces/base_query.php');

/**
 * The base class for building and returning a Postgresql query string.
 *
 * The built query is stored in value.
 * Strings should not be allowed to be assigned to this class.
 * Instead, only other c_base_query objects may be assigned to the value.
 *
 * @todo: redesign and move all parameter settings to traits.
 */
abstract class c_base_query extends c_base_return_string implements i_base_query {
  public const PARAMETER_NONE = 0;

  protected const pr_QUERY_COMMAND = '';

  protected $placeholders;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->placeholders = [];
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->placeholders);

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
   * Assign the value.
   *
   * This changes the behavior of the extended class from accepting only strings to accepting only c_base_query.
   *
   * @param c_base_query $value
   *   Any value so long as it is a c_base_query.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!($value instanceof c_base_query)) {
      return FALSE;
    }

    $this->value = clone($value);
    return TRUE;
  }

  /**
   * Add a query parameter placeholder.
   *
   * This does not sanitize the placeholder.
   *
   * @param string $placeholder
   *   The placeholder string, without the leading ':'.
   * @param $value
   *   The placeholder value the placeholder represents.
   *   @todo: add placeholder types and validation?
   *
   * @return c_base_return_status
   *   TRUE if added, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function add_placeholder($placeholder, $value) {
    if (!is_string($placeholder)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'placeholder', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->placeholders)) {
      $this->placeholders = [];
    }

    $this->placeholders[$placeholder] = $value;
    return new c_base_return_true();
  }

  /**
   * Get a query parameter placeholder value.
   *
   * @param string $placeholder
   *   Name of the placeholder to return the value of, without the leading ':'.
   *
   * @return c_base_return_value|c_base_return_status
   *   The value assigned to the placeholder.
   *   FALSE without the error bit set is returned if placeholder does not exist.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_placeholder($placeholder) {
    if (!is_string($placeholder)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'placeholder', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_array($this->placeholders) && array_key_exists($placeholder, $this->placeholders)) {
      return c_base_return_value::s_new($this->placeholders[$placeholder]);
    }

    return new c_base_return_false();
  }

  /**
   * Get all query parameter placeholders assigned.
   *
   * @return c_base_return_array
   *   An array of all assigned placeholders.
   *   An empty array with the error bit set is returned on error.
   */
  public function get_placeholders() {
    if (is_array($this->placeholders)) {
      return c_base_return_array::s_new($this->placeholders);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_vale([], 'c_base_return_array', $error);
  }

  /**
   * Remove a query parameter placeholder and its value.
   *
   * @param string $placeholder
   *   Name of the placeholder to remove, without the leading ':'.
   *
   * @return c_base_return_status
   *   TRUE if placeholder exists and is removed, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function remove_placeholder($placeholder) {
    if (is_array($this->placeholders) && array_key_exists($placeholder, $this->placeholders)) {
      unset($this->placeholders[$placeholder]);
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Remove all query parameter placeholders assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function remove_placeholders() {
    $this->placeholders = [];
    return new c_base_return_true();
  }

  /**
   * Get total query parameter placeholders assigned to this class.
   *
   * @return c_base_return_int
   *   The total number of placeholders assigned.
   *   0 with the error bit set is returned on error.
   */
  public function count_placeholders() {
    if (is_array($this->placeholders)) {
      return c_base_return_int::s_new(count($this->placeholders));
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_value(0, 'c_bae_return_int', $error);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    $this->value = static::pr_QUERY_COMMAND;
    return new c_base_return_true();
  }

  /**
   * Implements do_reset().
   */
  public function do_reset() {
    $this->value = '';
    $this->placeholders = [];
    return new c_base_return_true();
  }
}

/**
 * Provide an SQL query argument.
 *
 * SQL queries may be passed to other SQL queries, marking them as sub-queries.
 * This will most commonly be added to other expressions.
 */
class c_base_query_argument_query implements i_base_query_argument {
  protected $query;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->query = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->query);
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
   * Set the SQL query.
   *
   * @param c_base_query|null $query
   *   A fully configured query.
   *   Set to NULL to remove the assigned query.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_query($query) {
    if (is_null($this->query)) {
      $this->query = NULL;
      return new c_base_return_true();
    }

    if ($query instanceof c_base_query) {
      $this->query = clone($query);
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'query', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the assigned SQL query.
   *
   * @return c_base_query|c_base_return_null
   *   A valid c_base_query.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_query() {
    if (is_null($this->query)) {
      return new c_base_return_null();
    }

    if ($this->query instanceof c_base_query) {
      return clone($this->query);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build_argument().
   */
  public function do_build_argument() {
    if (is_null($this->query)) {
      return new c_base_return_false();
    }

    $built = $this->query->do_build();
    if ($built instanceof c_base_return_false) {
      if (c_base_return::s_has_error($built)) {
        $error = $built->get_error();
        return c_base_return_error::s_false($error);
      }

      return new c_base_return_false();
    }
    unset($built);

    return c_base_return_string::s_new($this->query->get_value_exact());
  }
}

/**
 * Provide an SQL expression argument.
 */
class c_base_query_argument_expression implements i_base_query_argument {
  protected const pr_QUERY_AS = 'as';

  protected $expression;
  protected $alias;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->expression = NULL;
    $this->alias      = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->expression);
    unset($this->alias);
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
   * Set the SQL expression.
   *
   * Expression are usually fields but also be other expressions.
   * Expressions may even be SQL queries.
   *
   * @param i_base_query_argument|null $expression
   *   The expression to assign.
   *   Set to NULL to remove the assigned expression.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_expression($expression) {
    if (is_null($expression)) {
      $this->expression = NULL;
      return new c_base_return_true();
    }

    if ($expression instanceof i_base_query_argument) {
      $this->expression = $expression;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the SQL expression alias.
   *
   * @param string|null $alias
   *   An optional alias to assign.
   *   May be required, depending on the generated query.
   *   Set to NULL to disable using during build.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_expression_alias($alias) {
    if (is_null($alias)) {
      $this->alias = NULL;
      return new c_base_return_true();
    }

    if (is_string($alias)) {
      $this->alias = $alias;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the assigned SQL expression.
   *
   * @return i_base_query_argument|null
   *   A valid query argument.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_expression() {
    if (is_null($this->expression)) {
      return new c_base_return_null();
    }

    if ($this->expression instanceof i_base_query_argument) {
      return clone($this->expression);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the assigned SQL expression alias.
   *
   * @return string|null
   *   A valid expression alias.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_expression_alias() {
    if (is_null($this->alias)) {
      return new c_base_return_null();
    }

    if (is_string($this->alias)) {
      return c_base_return_string::s_new($this->alias);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build_argument().
   */
  public function do_build_argument() {
    if (!is_string($this->expression)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $built = $this->expression;
    if (!is_string($this->alias)) {
      $built .= ' ' . static::pr_QUERY_AS . ' ' . $this->alias;
    }

    return c_base_return_string::s_new($built);
  }
}

/**
 * Provide an SQL expression argument, specific to column types.
 */
class c_base_query_argument_expression_column extends c_base_query_argument_expression {
  protected $column;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->column = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->column);

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
   * Set the SQL expression column.
   *
   * @param string|null $column
   *   Set to NULL to remove the assigned expression column.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_expression_column($column) {
    if (is_null($column)) {
      $this->column = NULL;
      return new c_base_return_true();
    }

    if (is_string($column)) {
      $this->column = $column;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the assigned SQL expression column.
   *
   * @return string|null
   *   A valid expression column.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_expression_column() {
    if (is_null($this->column)) {
      return new c_base_return_null();
    }

    if (is_string($this->column)) {
      return c_base_return_string::s_new($this->column);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build_argument().
   */
  public function do_build_argument() {
    $built = parent::do_build_argument();

    if ($built instanceof c_base_return_false) {
      if (c_base_return::s_has_error($built)) {
        unset($built);
        $error = $built->get_error();
        return c_base_return_error::s_false($error);
      }
      unset($built);

      return new c_base_return_false();
    }

    if (is_string($this->column) && $built instanceof c_base_return_string) {
      $new_built = $this->column . '.' . $built->get_value_exact();
      unset($built);

      return c_base_return_string::s_new($new_built);
    }

    return $built;
  }
}

/**
 * Provide an SQL argument aggregate signature base structure.
 *
 * This is essentially an aggregate_signature without the wildcard and ORDER BY support.
 * The ORDER BY support can then utilize this base without having its own wildcard and ORDER BY.
 */
class c_base_query_argument_aggregate_signature_base implements i_base_query_argument {
  public const QUERY_ARGUMENT_MODE_NONE     = 0;
  public const QUERY_ARGUMENT_MODE_IN       = 1;
  public const QUERY_ARGUMENT_MODE_VARIADIC = 2;

  protected $argument_mode;
  protected $argument_name;
  protected $argument_type;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->argument_mode = NULL;
    $this->argument_name = NULL;
    $this->argument_type = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->argument_mode);
    unset($this->argument_name);
    unset($this->argument_type);
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
   * Set the SQL argument mode.
   *
   * @param int|null $argument_mode
   *   The argument mode to assign.
   *   Set to NULL to remove the assigned mode.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_mode($argument_mode) {
    if (is_null($argument_mode)) {
      $this->argument_mode = NULL;
      return new c_base_return_true();
    }

    if (is_int($argument_mode)) {
      $this->argument_mode = $argument_mode;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_mode', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the SQL argument name.
   *
   * @param string|null $argument_name
   *   The argument name to assign.
   *   Set to NULL to remove the assigned name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_name($argument_name) {
    if (is_null($argument_name)) {
      $this->argument_name = NULL;
      return new c_base_return_true();
    }

    if (is_string($argument_name)) {
      $this->argument_name = $argument_name;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the SQL argument type.
   *
   * @param string|null $argument_type
   *   The argument type to assign.
   *   Set to NULL to remove the assigned type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_type($argument_type) {
    if (is_null($argument_type)) {
      $this->argument_type = NULL;
      return new c_base_return_true();
    }

    if (is_string($argument_type)) {
      $this->argument_type = $argument_type;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_type}' => 'argument_type', ':{function_type}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the assigned SQL argument mode.
   *
   * @return int|null
   *   A valid query argument mode.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_mode() {
    if (is_null($this->argument_mode)) {
      return new c_base_return_null();
    }

    if (is_int($this->argument_mode)) {
      return $this->argument_mode;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_mode', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the assigned SQL argument name.
   *
   * @return string|null
   *   A valid query argument name.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_name() {
    if (is_null($this->argument_name)) {
      return new c_base_return_null();
    }

    if (is_string($this->argument_name)) {
      return $this->argument_name;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the assigned SQL argument type.
   *
   * @return string|null
   *   A valid query argument type.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_type() {
    if (is_null($this->argument_type)) {
      return new c_base_return_null();
    }

    if (is_string($this->argument_type)) {
      return $this->argument_type;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_type}' => 'argument_type', ':{function_type}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build_argument().
   */
  public function do_build_argument() {
    if (!is_string($this->argument_type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_typr', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $built = '';
    if ($this->argument_mode === static::QUERY_MODE_IN) {
      $built = static::pr_QUERY_MODE_IN;
    }
    elseif ($this->argument_mode === static::QUERY_MODE_VARIADIC) {
      $built = static::pr_QUERY_MODE_VARIADIC;
    }

    if (is_string($this->argument_name)) {
      $built .= ' ' . $this->argument_name;
    }

    if (is_string($this->argument_type)) {
      $built .= ' ' . $this->argument_type;
    }

    return c_base_return_string::s_new($built);
  }
}

/**
 * Provide an SQL argument aggregate signature.
 */
class c_base_query_argument_aggregate_signature extends c_base_query_argument_aggregate_signature_base {
  protected $argument_all;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->argument_all = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->argument_all);

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
   * Set the SQL argument all.
   *
   * This represents whether or not a wildcard is used to represent all fields.
   *
   * @param bool|null $argument_all
   *   Set to TRUE to enable argument all.
   *   Set to FALSE disable argument all.
   *   Set to NULL to remove the assigned value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_all($argument_all) {
    if (is_null($argument_all)) {
      $this->argument_all = NULL;
      return new c_base_return_true();
    }

    if (is_bool($argument_all)) {
      $this->argument_all = $argument_all;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'argument_all', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the assigned SQL argument all.
   *
   * This represents whether or not a wildcard is used to represent all fields.
   *
   * @return bool|null
   *   TRUE if enabled, FALSE otherwise.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_all() {
    if (is_null($this->argument_all)) {
      return new c_base_return_null();
    }

    if (is_int($this->argument_all)) {
      return c_base_return_bool::s_new($this->argument_all);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_all', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build_argument().
   */
  public function do_build_argument() {
    if ($this->argument_all === TRUE) {
      $built = '*';
    }
    else {
      if (!is_string($this->argument_type)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
        return c_base_return_error::s_null($error);
      }

      $built = '';
      if ($this->argument_mode === static::QUERY_MODE_IN) {
        $built .= c_base_query_string::IN;
      }
      elseif ($this->argument_mode === static::QUERY_MODE_VARIADIC) {
        $built .= c_base_query_string::VARIADIC;
      }

      if (is_string($this->argument_name)) {
        $built .= ' ' . $this->argument_name;
      }

      if (is_string($this->argument_type)) {
        $built .= ' ' . $this->argument_type;
      }
    }

    return c_base_return_string::s_new($built);
  }
}

/**
 * Provide an SQL argument database option.
 */
class c_base_query_argument_database_option extends i_base_query_argument {
  protected $argument_allow_connection;
  protected $argument_connection_limit;
  protected $argument_is_template;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->argument_allow_connection = NULL;
    $this->argument_connection_limit = NULL;
    $this->argument_is_template      = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->argument_allow_connection);
    unset($this->argument_connection_limit);
    unset($this->argument_is_template);

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
   * Set the SQL argument allow connection.
   *
   * @param bool|null $allow_connection
   *   Set to TRUE to enable allow connection.
   *   Set to FALSE disable allow connection.
   *   Set to NULL to remove the assigned value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_allow_connection($allow_connection) {
    if (is_null($allow_connection)) {
      $this->argument_allow_connection = NULL;
      return new c_base_return_true();
    }

    if (is_bool($allow_connection)) {
      $this->argument_allow_connection = $allow_connection;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'allow_connection', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the SQL argument connection limit.
   *
   * @param int|null $connection_limit
   *   A connection limit integer.
   *   -1 means no limit.
   *   Set to NULL to remove the assigned value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_connection_limit($connection_limit) {
    if (is_null($connection_limit)) {
      $this->argument_connection_limit = NULL;
      return new c_base_return_true();
    }

    if (is_int($connection_limit) && $connection_limit > -2) {
      $this->argument_connection_limit = $connection_limit;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'connection_limit', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Set the SQL argument is template.
   *
   * @param bool|null $is_template
   *   Set to TRUE to enable is template.
   *   Set to FALSE disable is template.
   *   Set to NULL to remove the assigned value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_argument_is_template($is_template) {
    if (is_null($is_template)) {
      $this->argument_is_template = NULL;
      return new c_base_return_true();
    }

    if (is_bool($is_template)) {
      $this->argument_is_template = $is_template;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_template', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the assigned SQL argument allow connection.
   *
   * @return bool|null
   *   TRUE if enabled, FALSE otherwise.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_allow_connection() {
    if (is_null($this->argument_allow_connection)) {
      return new c_base_return_null();
    }

    if (is_bool($this->argument_allow_connection)) {
      return c_base_return_bool::s_new($this->argument_allow_connection);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_allow_connection', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the assigned SQL argument connection limit.
   *
   * @return int|null
   *   An integer representing the connection limit.
   *   -1 means no limit.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_connection_limit() {
    if (is_null($this->argument_connection_limit)) {
      return new c_base_return_null();
    }

    if (is_int($this->argument_connection_limit) && $this->argument_connection_limit > -2) {
      return c_base_return_int::s_new($this->argument_connection_limit);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_connection_limit', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the assigned SQL argument allow connection.
   *
   * @return bool|null
   *   TRUE if enabled, FALSE otherwise.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_argument_allow_connection() {
    if (is_null($this->argument_allow_connection)) {
      return new c_base_return_null();
    }

    if (is_bool($this->argument_allow_connection)) {
      return c_base_return_bool::s_new($this->argument_allow_connection);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'argument_allow_connection', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build_argument().
   */
  public function do_build_argument() {
    $built = NULL;
    if (is_bool($this->argument_allow_connection)) {
      $built .= ' ' c_base_query_string::ALLOW_CONNECTION;
      if ($this->argument_connection_limit) {
        $built .= ' ' . c_base_query_string::TRUE;
      }
      else {
        $built .= ' ' . c_base_query_string::FALSE;
      }
    }

    if (is_int($this->argument_connection_limit)) {
      $built .= ' ' . c_base_query_string::CONNECTION_LIMIT . ' ' . $this->argument_connection_limit;
    }

    if (is_bool($this->argument_is_template)) {
      $built .= ' ' . c_base_query_string::IS_TEMPLATE;
      if ($this->argument_is_template) {
        $built .=  ' ' . c_base_query_string::TRUE;
      }
      else {
        $built .= ' ' . c_base_query_string::FALSE;
      }
    }

    if (is_null($built)) {
      $built = '';
    }
    else {
      $built = c_base_query_string::WITH . ' ' . $built;
    }

    return c_base_return_string::s_new($built);
  }
}
