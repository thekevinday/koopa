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
 * Provide the sql action OF functionality.
 */
trait t_database_action_of {
  protected $action_of;

  /**
   * Assign the settings.
   *
   * @param string|null $type_name
   *   The of type name to set to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_of($type_name) {
    if (is_null($type_name)) {
      $this->action_of = NULL;
      return new c_base_return_true();
    }

    if (!is_string($type_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($type_name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->action_of = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned  action OF setting.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A tablespace name on success.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_of() {
    if (is_null($this->action_of)) {
      return new c_base_return_null();
    }

    if (isset($this->action_of)) {
      return clone($this->action_of);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_of', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_of() {
    return c_database_string::OF . ' ' . $this->action_of;
  }
}
