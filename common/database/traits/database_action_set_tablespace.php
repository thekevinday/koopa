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
 * Provide the sql action SET TABLESPACE functionality.
 */
trait t_database_action_set_tablespace {
  protected $action_set_tablespace;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The tablespace name to set to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_set_tablespace($name) {
    if (is_null($name)) {
      $this->action_set_tablespace = NULL;
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

    $this->action_set_tablespace = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned tablespace name to set to.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A tablespace name on success.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_set_tablespace() {
    if (is_null($this->action_set_tablespace)) {
      return new c_base_return_null();
    }

    if (isset($this->action_set_tablespace)) {
      return clone($this->action_set_tablespace);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_set_tablespace', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_set_tablespace() {
    return c_database_string::SET . ' ' . c_database_string::TABLESPACE . ' ' . $this->action_set_tablespace;
  }
}
