<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * Provide the sql USING expression functionality.
 */
trait t_database_using_expression {
  protected $using_expression;

  /**
   * Set the USING expression settings.
   *
   * @param string|null $sql_expression
   *   An SQL conditional expression.
   *   This is not converted to a placeholder because it is an SQL expression.
   *   The caller must ensure SQL safety.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_using_expression($sql_expression) {
    if (is_null($sql_expression)) {
      $this->using_expression = NULL;
      return new c_base_return_true();
    }

    if (is_string($sql_expression)) {
      $this->using_expression = $sql_expression;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'sql_expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned USING expression settings.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_using_expression() {
    if (is_null($this->using_expression)) {
      return new c_base_return_null();
    }

    if (isset($this->using_expression)) {
      return c_base_return_string::s_new($this->using_expression);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'using_expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_using_expression() {
    return c_database_string::USING . ' ' . $this->using_expression;
  }
}
