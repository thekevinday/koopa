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

require_once('common/database/enumerations/database_cascade.php');

/**
 * Provide the sql HANDLER/NO HANDLER functionality.
 */
trait t_database_cascade {
  protected $cascade;

  /**
   * Set the HANDLER settings.
   *
   * @param int|null $cascade
   *   The integer representing cascade/no-cascade.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_cascade($cascade) {
    if (is_null($cascade)) {
      $this->cascade = NULL;
      return new c_base_return_true();
    }

    if ($cascade === e_database_cascade::CASCADE || $cascade === e_database_cascade::RESTRICT) {
      $this->cascade = $cascade;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned cascade.
   *
   * @return c_base_return_int|c_base_return_null
   *   An integer containing the cascade setting on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_cascade() {
    if (is_null($this->cascade)) {
      return new c_base_return_null();
    }

    if (is_array($this->cascade)) {
      return c_base_return_array::s_new($this->cascade);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_cascade() {
    $value = NULL;
    if ($this->cascade['type'] === e_database_cascade::CASCADE) {
      if (isset($this->cascade['name'])) {
        $value = c_database_string::CASCADE;
      }
    }
    else if ($this->cascade['type'] === e_database_cascade::RESTRICT) {
      $value .= c_database_string::RESTRICT;
    }

    return $value;
  }
}
