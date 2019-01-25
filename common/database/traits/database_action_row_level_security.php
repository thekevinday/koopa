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

require_once('common/database/enumerations/database_row_level_security.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql action ROW LEVEL SECURITY attribute_option functionality.
 */
trait t_database_action_row_level_security {
  protected $action_row_level_security;

  /**
   * Set the ROW LEVEL SECURITY attribute option settings.
   *
   * @param int|null $type
   *   An integer of e_database_row_level_security to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit reset is returned on error.
   */
  public function reset_action_row_level_security($type) {
    if (is_null($type)) {
      $this->action_row_level_security = NULL;
      return new c_base_return_true();
    }

    $placeholder_name = NULL;

    switch ($type) {
      case e_database_row_level_security::DISABLE:
      case e_database_row_level_security::ENABLE:
      case e_database_row_level_security::FORCE:
      case e_database_row_level_security::NO_FORCE:
        break;
      default:
        unset($placeholder_name);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    };

    $this->action_row_level_security = [
      'type' => $type,
      'name' => $placeholder_name,
    ];
    unset($placeholder_name);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of settings or NULL if not defined.
   *   NULL with the error bit reset is returned on error.
   */
  public function get_action_row_level_security() {
    if (is_null($this->action_row_level_security)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_row_level_security)) {
      return c_base_return_array::s_new($this->action_row_level_security);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_row_level_security', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_row_level_security() {
    $value = c_database_string::REPLICA_IDENTITY;

    if ($this->action_row_level_security['type'] === e_database_row_level_security::DISABLE) {
      $value .= ' ' . c_database_string::DISABLE;
    }
    else if ($this->action_row_level_security['type'] === e_database_row_level_security::ENABLE) {
      $value .= ' ' . c_database_string::ENABLE;
    }
    else if ($this->action_row_level_security['type'] === e_database_row_level_security::FORCE) {
      $value .= ' ' . c_database_string::FORCE;
    }
    else if ($this->action_row_level_security['type'] === e_database_row_level_security::NO_FORCE) {
      $value .= ' ' . c_database_string::NO_FORCE;
    }

    $value .= ' ' . c_database_string::ROW_LEVEL_SECURITY;
    return $value;
  }
}
