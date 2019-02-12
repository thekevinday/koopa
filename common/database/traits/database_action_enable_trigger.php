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

require_once('common/database/enumerations/database_enable_trigger.php');

/**
 * Provide the sql action ENABLE TRIGGER functionality.
 */
trait t_database_action_enable_trigger {
  protected $action_enable_trigger;

  /**
   * Assign the settings.
   *
   * @param int|null $type
   *   An integer representing the type of the trigger.
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
  public function set_action_enable_trigger($type, $name = NULL) {
    if (is_null($type)) {
      $this->action_enable_trigger = NULL;
      return new c_base_return_true();
    }

    $use_name = NULL;
    switch ($type) {
      case e_database_trigger::ALWAYS:
      case e_database_trigger::NAME:
      case e_database_trigger::REPLICA:
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
      case e_database_trigger::ALL:
        $use_name = c_database_string::ALL;
        break;
      case e_database_trigger::USER:
        $use_name = c_database_string::USER;
        break;
      default:
        unset($use_name);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->action_enable_trigger = [
      'type' => $type,
      'name' => $use_name,
    ];
    unset($use_name);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned enable trigger settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the enable trigger settings.
   *   NULL is returned if not set (not to be confused with DISABLE TRIGGER).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_enable_trigger() {
    if (is_null($this->action_enable_trigger)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_enable_trigger)) {
      return c_base_return_array::s_new($this->action_enable_trigger);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_enable_trigger', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_enable_trigger() {
    $value = NULL;

    if ($this->action_enable_trigger['type'] === e_database_trigger::ALWAYS || $this->action_enable_trigger['type'] === e_database_trigger::REPLICA) {
      if (is_string($this->action_enable_trigger['name'])) {
        if ($this->action_enable_trigger['type'] === e_database_trigger::ALWAYS) {
          $value = c_database_string::ENABLE . ' ' . c_database_string::ALWAYS . ' ' . c_database_string::TRIGGER . ' ' . $this->action_enable_trigger['name'];
        }
        else if ($this->action_enable_trigger['type'] === e_database_trigger::REPLICA) {
          $value = c_database_string::ENABLE . ' ' . c_database_string::REPLICA . ' ' . c_database_string::TRIGGER . ' ' . $this->action_enable_trigger['name'];
        }
      }
    }
    else if ($this->action_enable_trigger['type'] === e_database_trigger::NAME) {
      if (is_string($this->action_enable_trigger['name'])) {
          $value = c_database_string::ENABLE . ' ' . c_database_string::TRIGGER . ' ' . $this->action_enable_trigger['name'];
      }
    }
    else if ($this->action_enable_trigger['type'] === e_database_trigger::ALL) {
      $value = c_database_string::ENABLE . ' ' . c_database_string::TRIGGER . ' ' . c_database_string::ALL;
    }
    else if ($this->action_enable_trigger['type'] === e_database_trigger::USER) {
      $value = c_database_string::ENABLE . ' ' . c_database_string::TRIGGER . ' ' . c_database_string::USER;
    }

    return $value;
  }
}
