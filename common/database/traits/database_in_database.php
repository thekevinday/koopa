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
 * Provide the sql IN DATABASE functionality.
 */
trait t_database_in_database {
  protected $in_database;

  /**
   * Set the IN DATABASE settings.
   *
   * @param string|null $name
   *   The database name to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_in_database($name) {
    if (is_null($name)) {
      $this->in_database = NULL;
      return new c_base_return_true();
    }

    if (is_string($name)) {
      $placeholder = $this->add_placeholder($name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->in_database = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned database name.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A database name query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_in_database() {
    if (is_null($this->in_database)) {
      return new c_base_return_null();
    }

    if (isset($this->in_database)) {
      return clone($this->in_database);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'in_database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_in_database() {
    return c_database_string::IN_DATABASE . ' ' . $this->in_database;
  }
}
