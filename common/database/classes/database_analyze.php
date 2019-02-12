<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ANALYZE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_analyze_options.php');
require_once('common/database/traits/database_column_names.php');
require_once('common/database/traits/database_table_name.php');

/**
 * The class for building and returning a Postgresql ANALYZE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-analyze.html
 */
class c_database_analyze extends c_database_query {
  use t_database_analyze_options;
  use t_database_column_names;
  use t_database_table_name;

  protected const p_QUERY_COMMAND = 'analyze';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    unset($this->analyze_options);
    unset($this->column_names);
    unset($this->table_name);
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    $this->analyze_options = NULL;
    $this->column_names    = NULL;
    $this->table_name      = NULL;

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
    if (is_null($this->table_name)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_table_name();

    if (!is_null($this->analyze_options)) {
      $value = '(' . $this->p_do_build_analyze_options() . ') ' . $value;
    }

    if (!is_null($this->column_names)) {
      $value .= ' (' . $this->p_do_build_column_names() . ')';
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
