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

require_once('common/database/enumerations/database_replica_identity.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql action REPLICA IDENTITY attribute_option functionality.
 */
trait t_database_action_replica_identity {
  protected $action_replica_identity;

  /**
   * Assign the settings.
   *
   * @param int|null $type
   *   An integer of e_database_replica_identity to use.
   *   Set to NULL to disable.
   * @param string|null $name
   *   (optional) The index name when $type is USING_INDEX.
   *   Required when $type is USING_INDEX.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit reset is returned on error.
   */
  public function reset_action_replica_identity($type, $name = NULL) {
    if (is_null($type)) {
      $this->action_replica_identity = NULL;
      return new c_base_return_true();
    }

    $placeholder_name = NULL;

    switch ($type) {
      case e_database_replica_identity::DEFAULT:
      case e_database_replica_identity::FULL:
      case e_database_replica_identity::NOTHING:
        break;
      case e_database_replica_identity::USING_INDEX:
        if (!is_string($name)) {
          unset($placeholder_name);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }

        $placeholder_name = $this->add_placeholder($name);
        if ($placeholder_name->has_error()) {
          return c_base_return_error::s_false($placeholder_name->get_error());
        }
        break;
      default:
        unset($placeholder_name);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    };

    $this->action_replica_identity = [
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
  public function get_action_replica_identity() {
    if (is_null($this->action_replica_identity)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_replica_identity)) {
      return c_base_return_array::s_new($this->action_replica_identity);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_replica_identity', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_replica_identity() {
    $value = c_database_string::REPLICA . ' ' . c_database_string::IDENTITY;

    if ($this->action_replica_identity['type'] === e_database_replica_identity::DEFAULT) {
      $value .= ' ' . c_database_string::DEFAULT;
    }
    else if ($this->action_replica_identity['type'] === e_database_replica_identity::FULL) {
      $value .= ' ' . c_database_string::FULL;
    }
    else if ($this->action_replica_identity['type'] === e_database_replica_identity::NOTHING) {
      $value .= ' ' . c_database_string::NOTHING;
    }
    else if ($this->action_replica_identity['type'] === e_database_replica_identity::USING_INDEX) {
      $value .= ' ' . c_database_string::USING . ' ' . c_database_string::INDEX;
      $value .= ' ' . $this->action_replica_identity['name'];
    }

    return $value;
  }
}
