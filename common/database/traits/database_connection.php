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
 * Provide the sql CONNECTION functionality.
 */
trait t_database_connection {
  protected $connection;

  /**
   * Set the CONNECTION settings.
   *
   * @param string|null $value
   *   The connection information to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_connection($value) {
    if (is_null($value)) {
      $this->connection = NULL;
      return new c_base_return_true();
    }

    if (is_string($value)) {
      $placeholder = $this->add_placeholder($value);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->connection = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned on connection information settings.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A connection information query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_connection() {
    if (is_null($this->connection)) {
      return new c_base_return_null();
    }

    if (isset($this->connection)) {
      return clone($this->connection);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'connection', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_connection() {
    return c_database_string::CONNECTION . ' ' . strval($this->connection);
  }
}
