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
 * Provide the sql WITH GANT OPTION functionality.
 */
trait t_database_with_grant_option {
  protected $with_grant_option;

  /**
   * Set the WITH GRANT OPTION value.
   *
   * @param bool|null $with_grant_option
   *   Set to TRUE for WITH GANT OPTION.
   *   Set to FALSE for nothing.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_with_grant_option($with_grant_option) {
    if (is_null($with_grant_option)) {
      $this->with_grant_option = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($with_grant_option)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'with_grant_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->with_grant_option = $with_grant_option;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned with grant option value.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for WITH GANT OPTION on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_with_grant_option() {
    if (is_null($this->with_grant_option)) {
      return new c_base_return_null();
    }

    if (is_bool($this->with_grant_option)) {
      return c_base_return_bool::s_new($this->with_grant_option);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'with_grant_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_with_grant_option() {
    if (is_null($this->with_grant_option)) {
      return NULL;
    }

    return $this->with_grant_option ? c_database_string::WITH_GRANT_OPTION : NULL;
  }
}
