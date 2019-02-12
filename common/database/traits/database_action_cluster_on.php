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
 * Provide the sql action CLUSTER ON functionality.
 */
trait t_database_action_cluster_on {
  protected $action_cluster_on;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The index name to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_cluster_on($name) {
    if (is_null($name)) {
      $this->action_cluster_on = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->action_cluster_on = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A name query placeholder.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_cluster_on() {
    if (is_null($this->action_cluster_on)) {
      return new c_base_return_null();
    }

    if (isset($this->action_cluster_on)) {
      return clone($this->action_cluster_on);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_cluster_on', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_cluster_on() {
    return c_database_string::CLUSTER . ' ' . c_database_string::ON . ' ' . $this->action_cluster_on;
  }
}
