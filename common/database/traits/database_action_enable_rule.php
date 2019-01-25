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

require_once('common/database/enumerations/database_enable_rule.php');

/**
 * Provide the sql action ENABLE RULE functionality.
 */
trait t_database_action_enable_rule {
  protected $action_enable_rule;

  /**
   * Set the action ENABLE RULE value.
   *
   * @param int|null $type
   *   An integer representing the type of the rule.
   *   Set to NULL to disable.
   * @param string|null $name
   *   A string representing the table name depending on the $type.
   *   Use type NAME to explicitly require this.
   *   Some types require this value, others ignore it.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_enable_rule($type, $name = NULL) {
    if (is_null($type)) {
      $this->action_enable_rule = NULL;
      return new c_base_return_true();
    }

    $use_name = NULL;
    switch ($type) {
      case e_database_rule::ALWAYS:
      case e_database_rule::NAME:
      case e_database_rule::REPLICA:
        if (!is_string($name)) {
          unset($use_name);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }

        $use_name = $this->add_placeholder($name);
        if ($use_name->has_error()) {
          return c_base_return_error::s_false($placeholder->get_error());
        }
        break;
      case e_database_rule::ALL:
        $use_name = c_database_string::ALL;
        break;
      case e_database_rule::USER:
        $use_name = c_database_string::USER;
        break;
      default:
        unset($use_name);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->action_enable_rule = [
      'type' => $type,
      'name' => $use_name,
    ];
    unset($use_name);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned enable rule settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the enable rule settings.
   *   NULL is returned if not set (not to be confused with DISABLE RULE).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_enable_rule() {
    if (is_null($this->action_enable_rule)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_enable_rule)) {
      return c_base_return_array::s_new($this->action_enable_rule);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_enable_rule', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_enable_rule() {
    $value = NULL;

    if ($this->action_enable_rule['type'] === e_database_rule::ALWAYS || $this->action_enable_rule['type'] === e_database_rule::REPLICA) {
      if (is_string($this->action_enable_rule['name'])) {
        if ($this->action_enable_rule['type'] === e_database_rule::ALWAYS) {
          $value = c_database_string::ENABLE_ALWAYS_RULE . ' ' . $this->action_enable_rule['name'];
        }
        else if ($this->action_enable_rule['type'] === e_database_rule::REPLICA) {
          $value = c_database_string::ENABLE_REPLICA_RULE . ' ' . $this->action_enable_rule['name'];
        }
      }
    }
    else if ($this->action_enable_rule['type'] === e_database_rule::NAME) {
      if (is_string($this->action_enable_rule['name'])) {
          $value = c_database_string::ENABLE_RULE . ' ' . $this->action_enable_rule['name'];
      }
    }
    else if ($this->action_enable_rule['type'] === e_database_rule::ALL) {
      $value = c_database_string::ENABLE_RULE . ' ' . c_database_string::ALL;
    }
    else if ($this->action_enable_rule['type'] === e_database_rule::USER) {
      $value = c_database_string::ENABLE_RULE . ' ' . c_database_string::USER;
    }

    return $value;
  }
}
