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
 * Provide the sql SET WITH OIDS functionality.
 */
trait t_database_set_with_oids {
  protected $set_with_oids;

  /**
   * Set the SET WITH OIDS value.
   *
   * @param bool|null $set_with_oids
   *   Set to TRUE for SET WITH OIDS.
   *   Set to FALSE for SET WITHOUT OIDS.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_with_oids($set_with_oids) {
    if (is_null($set_with_oids)) {
      $this->set_with_oids = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($set_with_oids)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'set_with_oids', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->set_with_oids = $set_with_oids;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned set with oids status.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for SET WITH OIDS or FALSE for SET WITHOUT OIDS on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_with_oids() {
    if (is_null($this->set_with_oids)) {
      return new c_base_return_null();
    }

    if (is_bool($this->set_with_oids)) {
      return c_base_return_bool::s_new($this->set_with_oids);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_with_oids', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_with_oids() {
    return $this->set_with_oids ? c_database_string::SET_WITH_OIDS : c_database_string::SET_WITHOUT_OIDS;
  }
}
