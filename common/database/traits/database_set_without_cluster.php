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
 * Provide the sql SET WITHOUT CLUSTER functionality.
 */
trait t_database_set_without_cluster {
  protected $set_without_cluster;

  /**
   * Set the SET WITHOUT CLUSTER value.
   *
   * @param bool|null $set_without_cluster
   *   Set to TRUE for SET WITHOUT CLUSTER.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_without_cluster($set_without_cluster) {
    if (is_null($set_without_cluster)) {
      $this->set_without_cluster = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($set_without_cluster)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_without_cluster', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->set_without_cluster = $set_without_cluster;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned with grant option value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for SET WITHOUT CLUSTER on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_without_cluster() {
    if (is_null($this->set_without_cluster)) {
      return new c_base_return_null();
    }

    if (is_bool($this->set_without_cluster)) {
      return c_base_return_bool::s_new($this->set_without_cluster);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_without_cluster', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_without_cluster() {
    return $this->set_without_cluster ? c_database_string::SET_WITHOUT_CLUSTER : NULL;
  }
}
