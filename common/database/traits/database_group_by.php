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
 * Provide the sql GROUP BY functionality.
 * @fixme: doesn't group by support multiple arguments?
 */
trait t_database_group_by {
  protected $group_by;

  /**
   * Set the GROUP BY settings.
   *
   * @param string|null $group_by
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_group_by($group_by) {
    if (is_null($group_by)) {
      $this->group_by = NULL;
      return new c_base_return_true();
    }

    if (is_string($group_by)) {
      $this->group_by = $group_by;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'group_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned name to group by.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set (group by is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_group_by() {
    if (is_null($this->group_by)) {
      return new c_base_return_null();
    }

    if (is_string($this->group_by)) {
      return c_base_return_string::s_new($this->group_by);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'group_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_group_by() {
    return c_database_string::GROUP_BY . $this->group_by;
  }
}
