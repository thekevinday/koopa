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
 * Provide the sql USING functionality.
 */
trait t_database_using_index_method {
  protected $using_index_method;

  /**
   * Set the USING settings.
   *
   * @param string|null $using_index_method
   *   The using_index_method to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_using_index_method($using_index_method) {
    if (is_null($using_index_method)) {
      $this->using_index_method = NULL;
      return new c_base_return_true();
    }

    if (is_string($using_index_method)) {
      $placeholder = $this->add_placeholder($using_index_method);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->using_index_method = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'using_index_method', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned using_index_method.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A using_index_method query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_using_index_method() {
    if (is_null($this->using_index_method)) {
      return new c_base_return_null();
    }

    if (isset($this->using_index_method)) {
      return clone($this->using_index_method);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'using_index_method', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned on success.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_using_index_method() {
    return strval($this->using_index_method);
  }
}
