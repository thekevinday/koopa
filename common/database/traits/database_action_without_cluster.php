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
 * Provide the sql WITHOUT CLUSTER functionality.
 */
trait t_database_action_without_cluster {
  protected $action_without_cluster;

  /**
   * Assign the settings.
   *
   * @param bool|null $without_cluster
   *   Set to TRUE for WITHOUT CLUSTER.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_without_cluster($without_cluster) {
    if (is_null($without_cluster)) {
      $this->action_without_cluster = NULL;
      return new c_base_return_true();
    }

    if (is_bool($without_cluster)) {
      $this->action_without_cluster = $without_cluster;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'without_cluster', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned without cluster value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for WITHOUT CLUSTER on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_without_cluster() {
    if (is_null($this->action_without_cluster)) {
      return new c_base_return_null();
    }

    if (is_bool($this->action_without_cluster)) {
      return c_base_return_bool::s_new($this->action_without_cluster);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_without_cluster', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_without_cluster() {
    return $this->action_without_cluster ? c_database_string::WITHOUT . ' ' . c_database_string::CLUSTER : NULL;
  }
}
