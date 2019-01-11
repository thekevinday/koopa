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

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql SET SCHEMA functionality.
 */
trait t_database_set_schema {
  protected $set_schema;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $set_schema
   *   The schema name.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_schema($set_schema) {
    if (!is_null($set_schema) && !is_string($set_schema)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($set_schema);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->set_schema = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned schema name to set to.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A schema name query placeholder on success.
   *   NULL is returned if not set (set schema is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_schema() {
    if (is_null($this->set_schema)) {
      return new c_base_return_null();
    }

    if (isset($this->set_schema)) {
      return clone($this->set_schema);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_schema() {
    return c_database_string::SET_SCHEMA . ' ' . $this->set_schema;
  }
}
