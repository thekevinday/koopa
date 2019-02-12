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

require_once('common/database/enumerations/database_rule.php');

/**
 * Provide the sql action DISABLE RULE functionality.
 */
trait t_database_action_disable_rule {
  protected $action_disable_rule;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   A string representing the rule name.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_disable_rule($name) {
    if (is_null($name)) {
      $this->action_disable_rule = NULL;
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

    $this->action_disable_rule = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned enable rule settings.
   *
   * @return c_base_return_string|c_base_return_null
   *   A string containing the disable rule name.
   *   NULL is returned if not set (not to be confused with DISABLE RULE).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_disable_rule() {
    if (is_null($this->action_disable_rule)) {
      return new c_base_return_null();
    }

    if (is_string($this->action_disable_rule)) {
      return c_base_return_string::s_new($this->action_disable_rule);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_disable_rule', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_disable_rule() {
    return c_database_string::DISABLE . ' ' . c_database_string::RULE . ' ' . $this->action_disable_rule;
  }
}
