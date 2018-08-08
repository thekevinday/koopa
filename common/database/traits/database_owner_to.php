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

require_once('common/database/classes/database_string.php');

/**
 * Provide sql OWNER TO functionality.
 */
trait t_database_owner_to {
  protected $query_owner_to;
  protected $query_owner_to_user_name;

  /**
   * Set the OWNER TO settings.
   *
   * @param int|null $owner_to
   *   The owner type to assign.
   *   Should be one of: c_database_code_user.
   *   Set to NULL to disable.
   * @param string|null $user_name
   *   (optional) When non-NULL this is the database user name.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_owner_to($owner_to, $user_name = NULL) {
    if (is_null($owner_to)) {
      $this->query_owner_to = NULL;
      $this->query_owner_to_user_name = NULL;
      return new c_base_return_true();
    }

    if (!is_int($owner_to)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'owner_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($owner_type == c_database_code_user::USER_NAME) {
      if (!is_null($user_name) && !is_string($user_name)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'user_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->query_owner_to = $owner_to;
      $this->query_owner_to_user_name = $user_name;
      return new c_base_return_true();
    }

    if (!is_null($user_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'user_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->query_owner_to = $owner_to;
    $this->query_owner_to_user_name = NULL;

    if ($owner_type == c_database_code_user::USER_CURRENT) {
      $this->query_owner_to_user_name = c_database_string::USER_CURRENT;
    }
    else if ($owner_type == c_database_code_user::USER_SESSION) {
      $this->query_owner_to_user_name = c_database_string::USER_SESSION;
    }

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned sql owner to.
   *
   * @return c_base_return_int|c_base_return_null
   *   A (c_database_code_user) code representing the owner on success.
   *   NULL without error bit set is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_owner_to() {
    if (is_null($this->query_owner_to)) {
      return new c_base_return_null();
    }

    if (is_int($this->query_owner_to)) {
      return c_base_return_int::s_new($this->query_owner_to);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_owner_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  public function get_query_owner_to_user_name() {
    if (is_null($this->query_owner_to_user_name)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_owner_to_user_name)) {
      return c_base_return_string::s_new($this->query_owner_to_user_name);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_owner_to_user_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}
