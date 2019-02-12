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
 * Provide the sql CLUSTER ON functionality.
 */
trait t_database_cluster_on {
  protected $cluster_on;

  /**
   * Assign the settings.
   *
   * @param string|null $index_name
   *   The index name to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_cluster_on($index_name) {
    if (is_null($index_name)) {
      $this->cluster_on = NULL;
      return new c_base_return_true();
    }

    if (is_string($index_name)) {
      $placeholder = $this->add_placeholder($index_name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->cluster_on = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'index_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned cluster on setting.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A index name query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_cluster_on() {
    if (is_null($this->cluster_on)) {
      return new c_base_return_null();
    }

    if (isset($this->cluster_on)) {
      return clone($this->cluster_on);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'cluster_on', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_cluster_on() {
    return c_database_string::CLUSTER . ' ' . c_database_string::ON . ' ' . $this->cluster_on;
  }
}
