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
 * Provide the sql WITH CHECK expression functionality.
 */
trait t_database_with_check_expression {
  protected $with_check_expression;

  /**
   * Assign the settings.
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
  public function set_with_check_expression($sql_expression) {
    if (is_null($sql_expression)) {
      $this->with_check_expression = NULL;
      return new c_base_return_true();
    }

    if (is_string($sql_expression)) {
      $this->with_check_expression = $sql_expression;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'sql_expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned WITH CHECK expression settings.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_with_check_expression() {
    if (is_null($this->with_check_expression)) {
      return new c_base_return_null();
    }

    if (isset($this->with_check_expression)) {
      return c_base_return_string::s_new($this->with_check_expression);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'with_check_expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_with_check_expression() {
    return c_database_string::WITH . ' ' . c_database_string::CHECK . ' ' . $this->with_check_expression;
  }
}
