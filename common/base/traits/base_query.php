<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * The particular design of traits as provided here are intended to be addd to the a base query class for assisting in the generation of an SQL query, statement, or operation.
 * During the generation, it is expected that when there are combined traits, the class building the query will decide which property to use and which not to use.
 * For example, if there is a class that includes SET SCHEMA and RENAME TO, both with properties set by the caller, only one of those may be used if the built SQL will only support one of those at a time.
 * If the SQL query allows both, then both will be used.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_query_string.php');
require_once('common/base/classes/c_base_query_code_set.php');

/**
 * Provide sql OWNER TO functionality.
 */
trait t_base_query_owner_to {
  protected $query_owner_to;
  protected $query_owner_to_user_name;

  /**
   * Set the OWNER TO settings.
   *
   * @param int|null $owner_to
   *   The owner type to assign.
   *   Should be one of: c_base_query_code_user.
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

    if ($owner_type == c_base_query_code_user::USER_NAME) {
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

    if ($owner_type == c_base_query_code_user::USER_CURRENT) {
      $this->query_owner_to_user_name = c_base_query_string::USER_CURRENT;
    }
    elseif ($owner_type == c_base_query_code_user::USER_SESSION) {
      $this->query_owner_to_user_name = c_base_query_string::USER_SESSION;
    }

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned sql owner to.
   *
   * @return c_base_return_int|c_base_return_null
   *   A (c_base_query_code_user) code representing the owner on success.
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

/**
 * Provide the sql RENAME TO functionality.
 */
trait t_base_query_rename_to {
  protected $query_rename_to;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $rename_to
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_rename_to($rename_to) {
    if (!is_null($rename_to) && !is_string($rename_to)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'rename_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->query_rename_to = $rename_to;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_rename_to() {
    if (is_null($this->query_rename_to)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_rename_to)) {
      return c_base_return_string::s_new($this->query_rename_to);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_rename_to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}

/**
 * Provide the sql SET SCHEMA functionality.
 */
trait t_base_query_set_schema {
  protected $query_set_schema;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $set_schema
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_set_schema($set_schema) {
    if (!is_null($set_schema) && !is_string($set_schema)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->query_set_schema = $set_schema;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned schema name to set to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A schema name on success.
   *   NULL is returned if not set (set schema is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set_schema() {
    if (is_null($this->query_set_schema)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_set_schema)) {
      return c_base_return_string::s_new($this->query_set_schema);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}

/**
 * Provide the sql ORDER BY functionality.
 */
trait t_base_query_order_by {
  protected $query_order_by;

  /**
   * Set the ORDER BY settings.
   *
   * @param string|null $order_by
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_order_by($order_by) {
    if (is_null($order_by)) {
      $this->query_order_by = NULL;
      return new c_base_return_true();
    }

    if (is_string($order_by)) {
      $this->query_order_by = $order_by;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'order_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_order_by() {
    if (is_null($this->query_order_by)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_order_by)) {
      return c_base_return_string::s_new($this->query_order_by);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_order_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}

/**
 * Provide the sql GROUP BY functionality.
 */
trait t_base_query_group_by {
  protected $query_group_by;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $group_by
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_group_by($group_by) {
    if (is_null($group_by)) {
      $this->query_group_by = NULL;
      return new c_base_return_true();
    }

    if (is_string($group_by)) {
      $this->query_group_by = $group_by;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'group_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned name to rename to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_group_by() {
    if (is_null($this->query_group_by)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_group_by)) {
      return c_base_return_string::s_new($this->query_group_by);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_group_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}


/**
 * Provide the sql NAME functionality.
 */
trait t_base_query_name {
  protected $query_name;

  /**
   * Set the NAME settings.
   *
   * @param string|null $name
   *   The name to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_name($name) {
    if (is_null($name)) {
      $this->query_name = NULL;
      return new c_base_return_true();
    }

    if (is_string($name)) {
      $this->query_name = $name;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned name.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_name() {
    if (is_null($this->query_name)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_name)) {
      return c_base_return_string::s_new($this->query_name);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}

/**
 * Provide the sql SET TABLESPACE functionality.
 */
trait t_base_query_set_tablespace {
  protected $query_set_tablespace;

  /**
   * Set the SET TABLESPACE settings.
   *
   * @param string|null $set_tablespace
   *   The tablespace name to set to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_set_tablespace($set_tablespace) {
    if (!is_null($set_tablespace) && !is_string($set_tablespace)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_tablespace', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->query_set_tablespace = $set_tablespace;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned tablespace name to set to.
   *
   * @return c_base_return_string|c_base_return_null
   *   A tablespace name on success.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set_tablespace() {
    if (is_null($this->query_set_tablespace)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_set_tablespace)) {
      return c_base_return_string::s_new($this->query_set_tablespace);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set_tablespace', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}

/**
 * Provide the sql SET functionality.
 */
trait t_base_query_set {
  protected $query_set;
  protected $query_set_parameter;
  protected $query_set_value;

  /**
   * Set the SET settings.
   *
   * @param int|null $set
   *   The SET code to assign.
   *   Should be one of: c_base_query_code_set.
   *   Set to NULL to disable.
   * @param string|null $parameter
   *   (optional) When non-NULL this is the configuration parameter.
   *   When NULL, DEFAULT is used if applicablem otherwise this is ignored.
   * @param string|null $value
   *   (optional) When non-NULL this is the value.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_set($set, $parameter = NULL, $value = NULL) {
    if (is_null($set)) {
      $this->query_set = NULL;
      $this->query_set_parameter = NULL;
      $this->query_set_value = NULL;
      return new c_base_return_true();
    }

    if (!is_int($set)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($set == c_base_query_code_set::TO || $set == c_base_query_code_set::EQUAL) {
      if (!is_null($parameter) || !is_string($parameter)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($value) || !is_string($value)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->query_set = $set;
      $this->query_set_parameter = $parameter;
      $this->query_set_value = $value;
      return new c_base_return_true();
    }
    elseif ($set == c_base_query_code_set::FROM_CURRENT) {
      $this->query_set = $set;
      $this->query_set_parameter = NULL;
      $this->query_set_value = NULL;
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned sql set.
   *
   * @return c_base_return_int|c_base_return_null
   *   A (c_base_query_set) code representing the set on success.
   *   NULL is returned if not set (set tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set() {
    if (is_null($this->query_set)) {
      return new c_base_return_null();
    }

    if (is_int($this->query_set)) {
      return c_base_return_int::s_new($this->query_set);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned sql set parameter value.
   *
   * @return c_base_return_string|c_base_return_null
   *   A set parameter value on success.
   *   NULL without error bit set is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set_parameter() {
    if (is_null($this->query_set_parameter)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_set_parameter)) {
      return c_base_return_string::s_new($this->query_set_parameter);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned sql set value value.
   *
   * @return c_base_return_string|c_base_return_null
   *   A set value value on success.
   *   NULL without error bit set is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_set_value() {
    if (is_null($this->query_set_value)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_set_value)) {
      return c_base_return_string::s_new($this->query_set_value);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_set_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}

/**
 * Provide the sql RESET functionality.
 */
trait t_base_query_reset {
  protected $query_reset;
  protected $query_reset_parameter;

  /**
   * Set the RESET settings.
   *
   * @param int|null $reset
   *   The reset code to assign.
   *   Should be one of: c_base_query_code_reset.
   *   Set to NULL to disable.
   * @param string|null $parameter
   *   (optional) When non-NULL this is the configuration parameter.
   *   When NULL, DEFAULT is used if applicablem otherwise this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_query_reset($reset, $parameter = NULL) {
    if (is_null($reset)) {
      $this->query_reset = NULL;
      $this->query_reset_parameter = NULL;
      return new c_base_return_true();
    }

    if (!is_int($reset)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($reset == c_base_query_code_reset::PARAMETER) {
      if (!is_null($parameter) || !is_string($parameter)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->query_reset = $reset;
      $this->query_reset_parameter = $parameter;
      return new c_base_return_true();
    }
    elseif ($reset == c_base_query_code_reset::ALL) {
      $this->query_reset = $reset;
      $this->query_reset_parameter = NULL;
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned sql reset.
   *
   * @return c_base_return_int|c_base_return_null
   *   A (c_base_query_reset) code representing the reset on success.
   *   NULL is returned if not set (reset tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_query_reset() {
    if (is_null($this->query_reset)) {
      return new c_base_return_null();
    }

    if (is_int($this->query_reset)) {
      return c_base_return_int::s_new($this->query_reset);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_reset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned sql reset parameter value.
   *
   * @return c_base_return_string|c_base_return_null
   *   A reset parameter value on success.
   *   NULL without error bit reset is returned if not assigned.
   *   NULL with the error bit reset is returned on error.
   */
  public function get_query_reset_parameter() {
    if (is_null($this->query_reset_parameter)) {
      return new c_base_return_null();
    }

    if (is_string($this->query_reset_parameter)) {
      return c_base_return_string::s_new($this->query_reset_parameter);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'query_reset_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }
}
