<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_user.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide sql OWNER TO functionality.
 */
trait t_database_owner_to {
  protected $owner_to;

  /**
   * Set the OWNER TO settings.
   *
   * @param int|null $owner_to
   *   The owner type to assign.
   *   Should be one of: e_database_user.
   *   Set to NULL to disable.
   * @param string|null $user_name
   *   (optional) When non-NULL this is the database user name.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_owner_to($owner_to, $user_name = NULL) {
    if (is_null($owner_to)) {
      $this->owner_to = NULL;
      return new c_base_return_true();
    }

    if (!is_int($owner_to)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'owner_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($owner_type == e_database_user::NAME) {
      if (!is_null($user_name) && !is_string($user_name)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'user_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->owner_to = [
        'type' => $owner_to,
        'value' => $user_name,
      ];

      return new c_base_return_true();
    }

    if (!is_null($user_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'user_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->owner_to = [
      'type' => $owner_to,
      'value' => NULL,
    ];

    if ($owner_type == e_database_user::CURRENT) {
      $this->owner_to['value'] = c_database_string::USER_CURRENT;
    }
    else if ($owner_type == e_database_user::SESSION) {
      $this->owner_to['value'] = c_database_string::USER_SESSION;
    }

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned sql owner to.
   *
   * @return c_base_return_int|c_base_return_null
   *   A (e_database_user) code representing the owner on success.
   *   NULL without error bit set is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_owner_to() {
    if (is_null($this->owner_to)) {
      return new c_base_return_null();
    }

    if (is_int($this->owner_to['type'])) {
      return c_base_return_int::s_new($this->owner_to['type']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'owner_to[type]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned sql owner to specified name.
   *
   * @return c_base_return_string|c_base_return_null
   *   An owner to name on success.
   *   NULL without error bit set is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_owner_to_user_name() {
    if (is_null($this->owner_to)) {
      return new c_base_return_null();
    }

    if (is_string($this->owner_to['value'])) {
      return c_base_return_string::s_new($this->owner_to['value']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'owner_to[value]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_owner_to() {
    if (is_null($this->owner_to)) {
      return NULL;
    }

    return c_database_string::OWNER_TO . ' ' . $this->owner_to['value'];
  }
}
