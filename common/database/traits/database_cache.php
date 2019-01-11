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

/**
 * Provide the sql CACHE functionality.
 */
trait t_database_cache {
  protected $cache;

  /**
   * Set the CACHE settings.
   *
   * @param int|null $sequence
   *   The cache sequence number to use.
   *   Postgesql only supports 1 or greater.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_cache($sequence) {
    if (is_null($sequence)) {
      $this->cache = NULL;
      return new c_base_return_true();
    }

    if (is_int($sequence) && $sequence > 0) {
      $this->cache = $sequence;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'sequence', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned cache sequence number.
   *
   * @return c_base_return_int|c_base_return_null
   *   A cache sequence number.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_cache() {
    if (is_null($this->cache)) {
      return new c_base_return_null();
    }

    if (isset($this->cache)) {
      return c_base_return_int::s_new($this->cache);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'cache', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_cache() {
    return c_database_string::CACHE . ' ' . $this->cache;
  }
}
