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

/**
 * Provide the sql SET TABLESPACE functionality.
 */
trait t_database_set_tablespace {
  protected $query_set_tablespace;

  /**
   * Set the SET TABLESPACE settings.
   *
   * @param string|null $set_tablespace
   *   The tablespace name to set to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_set_tablespace($set_tablespace) {
    if (!is_null($set_tablespace) && !is_string($set_tablespace)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_tablespace', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->query_set_tablespace = $set_tablespace;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned tablespace name to set to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A tablespace name on success.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set_tablespace() {
    if (is_null($this->query_set_tablespace)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_set_tablespace)) {
      return c_base_return_string::s_new($this->query_set_tablespace);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set_tablespace', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}
