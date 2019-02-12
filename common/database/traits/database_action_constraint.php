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

require_once('common/database/enumerations/database_cascade.php');
require_once('common/database/enumerations/database_constraint.php');
require_once('common/database/enumerations/database_constraint_mode.php');

/**
 * Provide the sql ADD/ALTER/VALIDATE/DROP action CONSTAINT functionality.
 */
trait t_database_action_constraint {
  protected $action_constraint;

  /**
   * Assign the settings.
   *
   * @param string|null $action_constraint_name
   *   The name to use.
   *   Set to NULL to disable.
   * @param int|null $type
   *   The type code representing the constaint operation.
   *   This can only be NULL when $action_constraint_name is NULL.
   * @param bool|int|null $value
   *   When $type is ADD, then this is a boolean such that NOT VALID is added when TRUE.
   *   When $type is ALTER, then this is an integer of e_database_constraint_mode.
   *   When $type is DROP, then this is a boolean such that IS EXISTS is added when TRUE.
   *   Otherwise this should be NULL.
   * @param int|null $cascade
   *   When $type is DROP, this must be an integer representing CASCADE or RESTRICT.
   *   Otherwise this should be NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_constraint($action_constraint_name, $type = NULL, $value = NULL, $cascade = NULL) {
    if (is_null($action_constraint)) {
      $this->action_constraint = NULL;
      return new c_base_return_true();
    }

    if (!is_string($action_constraint_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action_constraint', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $action_constraint = [
      'name' => $placeholder,
      'type' => $type,
      'value' => NULL,
      'cascade' => NULL,
    ];
    unset($placeholder);

    if ($type === e_database_constraint::ADD) {
      if (!is_bool($value)) {
        unset($action_constraint);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $action_constraint['value'] = $value;
    }
    else if ($type === e_database_constraint::ALTER) {
      switch($value) {
        case e_database_constraint_mode::DEFERRABLE:
        case e_database_constraint_mode::INITIALLY_DEFERRED:
        case e_database_constraint_mode::INITIALLY_IMMEDIATE:
        case e_database_constraint_mode::NOT_DEFERRABLE:
          break;

        default:
          unset($action_constraint);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
      }

      $action_constraint['value'] = $value;
    }
    else if ($type === e_database_constraint::DROP) {
      if (!is_bool($value)) {
        unset($action_constraint);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $action_constraint['value'] = $value;
    }
    else if ($type === e_database_constraint::VALIDATE) {
      switch ($cascade) {
        case e_database_cascade::CASCADE:
        case e_database_cascade::RESTRICT:
          $action_constraint['cascade'] = $cascade;
          break;
        default:
          unset($action_constraint);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
      }
    }
    else {
      unset($action_constraint);
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->action_constraint = $action_constraint;
    unset($action_constraint);
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned action_constraint settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of action_constraint settings.
   *   NULL is returned if not set (action_constraint is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_constraint() {
    if (is_null($this->action_constraint)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_constraint)) {
      return c_base_return_array::s_new($this->action_constraint);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_constraint', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_constraint() {
    $value = NULL;
    if ($this->action_constraint['type'] === e_database_constraint::ADD) {
      $value = c_database_string::ADD . ' ' . $this->action_constraint['name'];

      if ($this->action_constraint['value']) {
        $value .= ' ' . c_database_string::NOT . ' ' . c_database_string::VALID;
      }
    }
    else if ($this->action_constraint['type'] === e_database_constraint::ALTER) {
      $value = c_database_string::ALTER . ' ' . c_database_string::CONSTRAINT . ' ' . $this->action_constraint['name'];

      if ($this->action_constraint['value'] === e_database_constraint_mode::DEFERRABLE) {
        $value .= ' ' . c_database_string::DEFERRABLE;
      }
      else if ($this->action_constraint['value'] === e_database_constraint_mode::INITIALLY_DEFERRED) {
        $value .= ' ' . c_database_string::INITIALLY . ' ' . c_database_string::DEFERRED;
      }
      else if ($this->action_constraint['value'] === e_database_constraint_mode::INITIALLY_IMMEDIATE) {
        $value .= ' ' . c_database_string::INITIALLY . ' ' . c_database_string::IMMEDIATE;
      }
      else if ($this->action_constraint['value'] === e_database_constraint_mode::NOT_DEFERRABLE) {
        $value .= ' ' . c_database_string::NOT . ' ' . c_database_string::DEFERRABLE;
      }
    }
    else if ($this->action_constraint['type'] === e_database_constraint::DROP) {
      $value = c_database_string::DROP . ' ' . c_database_string::CONSTRAINT . ' ' . $this->action_constraint['name'];
    }
    else if ($this->action_constraint['type'] === e_database_constraint::VALIDATE) {
      $value = c_database_string::VALIDATE_CONSTAINT;

      if ($this->action_constraint['value']) {
        $value .= ' ' . c_database_string::NOT . ' ' . c_database_string::VALID;
      }

      $value .=' ' . $this->action_constraint['name'];

      if ($this->action_constraint['cascade'] === e_database_cascade::CASCADE) {
        $value .= ' ' . c_database_string::CASCADE;
      }
      else if ($this->action_constraint['cascade'] === e_database_cascade::RESTRICT) {
        $value .= ' ' . c_database_string::RESTRICT;
      }
    }

    return $value;
  }
}
