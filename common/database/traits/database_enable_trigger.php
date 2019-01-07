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
 * Provide the sql SET WITH OIDS functionality.
 */
trait t_database_enable_trigger {
  protected $enable_trigger;

  /**
   * Set the ENABLE TRIGGER or DISABLE TRIGGER value.
   *
   * @param bool|null $enable_trigger
   *   Set to TRUE for ENABLE TRIGGER, FALSE for DISABLE TRIGGER.
   *   Set to NULL to disable (as-in: no SQL is generated, do not confuse this with the generated DISABLE TRIGGER state produced by FALSE).
   * @param int|null $type
   *   An integer representing the type of the trigger.
   *   Should only be NULL when $enable_trigger is NULL.
   * @param string|null $name
   *   A string representing the table name depending on the $type.
   *   Use type NAME to explicitly require this.
   *   Some types require this value, others ignore it.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_enable_trigger($enable, $type = NULL, $name = NULL) {
    if (is_null($enable_trigger)) {
      $this->enable_trigger = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($enable_trigger)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'enable_trigger', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $use_name = NULL;
    switch ($type) {
      case e_database_enable_trigger::ALWAYS:
      case e_database_enable_trigger::NAME:
      case e_database_enable_trigger::REPLICA:
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
      case e_database_enable_trigger::ALL:
        $use_name = c_database_string::ALL;
        break;
      case e_database_enable_trigger::USER:
        $use_name = c_database_string::USER;
        break;
      default:
        unset($use_name);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->enable_trigger = [
      'status' => $enable_trigger,
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
   *   An array containing the enable trigger settings on success.
   *   NULL is returned if not set (not to be confused with DISABLE TRIGGER).
   *   NULL with the error bit set is returned on error.
   */
  public function get_enable_trigger() {
    if (is_null($this->enable_trigger)) {
      return new c_base_return_null();
    }

    if (is_array($this->enable_trigger)) {
      return c_base_return_array::s_new($this->enable_trigger);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'enable_trigger', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_enable_trigger() {
    $value = NULL;
    switch ($this->enable_trigger['type']) {
      case e_database_enable_trigger::ALWAYS:
      case e_database_enable_trigger::REPLICA:
        if (is_string($this->enable_trigger['name'])) {
          if ($this->enable_trigger['type'] === e_database_enable_trigger::ALWAYS) {
            $value = c_database_string::ENABLE_ALWAYS_TRIGGER . ' ' . $this->enable_trigger['name'];
          }
          else if ($this->enable_trigger['type'] === e_database_enable_trigger::REPLICA) {
            $value = c_database_string::ENABLE_REPLICA_TRIGGER . ' ' . $this->enable_trigger['name'];
          }
        }
        break;

      case e_database_enable_trigger::NAME:
        if (is_string($this->enable_trigger['name'])) {
          if ($this->enable_trigger['status']) {
            $value = c_database_string::ENABLE_TRIGGER . ' ' . $this->enable_trigger['name'];
          }
          else {
            $value = c_database_string::DISABLE_TRIGGER . ' ' . $this->enable_trigger['name'];
          }
        }
        break;
      case e_database_enable_trigger::ALL:
        if ($this->enable_trigger['status']) {
          $value = c_database_string::ENABLE_TRIGGER . ' ' . c_database_string::ALL;
        }
        else {
          $value = c_database_string::DISABLE_TRIGGER . ' ' . c_database_string::ALL;
        }
        break;
      case e_database_enable_trigger::USER:
        if ($this->enable_trigger['status']) {
          $value = c_database_string::ENABLE_TRIGGER . ' ' . c_database_string::USER;
        }
        else {
          $value = c_database_string::DISABLE_TRIGGER . ' ' . c_database_string::USER;
        }
        break;
    }

    return $value;
  }
}
