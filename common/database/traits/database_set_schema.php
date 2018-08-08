<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
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
  protected $query_set_schema;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $set_schema
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_set_schema($set_schema) {
    if (!is_null($set_schema) && !is_string($set_schema)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->query_set_schema = $set_schema;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned schema name to set to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A schema name on success.
   *   NULL is returned if not set (set schema is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set_schema() {
    if (is_null($this->query_set_schema)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_set_schema)) {
      return c_base_return_string::s_new($this->query_set_schema);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}
