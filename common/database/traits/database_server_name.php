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
 * Provide the sql SERVER name functionality.
 */
trait t_database_server_name {
  protected $server_name;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The server name to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_server_name($name) {
    if (is_null($name)) {
      $this->server_name = NULL;
      return new c_base_return_true();
    }

    if (is_string($name)) {
      $placeholder = $this->add_placeholder($name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->server_name = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned server name.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A server name query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_server_name() {
    if (is_null($this->server_name)) {
      return new c_base_return_null();
    }

    if (isset($this->server_name)) {
      return clone($this->server_name);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'server_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_server_name() {
    return c_database_string::SERVER . ' ' . $this->server_name;
  }
}
