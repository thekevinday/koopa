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

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql REFRESH PUBLICATION functionality.
 */
trait t_database_refresh_publication {
  protected $refresh_publication;

  /**
   * Set the REFRESH PUBLICATION settings.
   *
   * @param bool|null $refresh
   *   Set to TRUE to use REFRESH PUBLICATION.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_refresh_publication($refresh) {
    if (is_null($option)) {
      $this->refresh_publication = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($refresh)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => '$refresh', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->refresh_publication = $refresh;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned REFRESH PUBLICATION setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean representing whether or not refresh publication is to be used.
   *   NULL is returned if not set (refresh publication not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_refresh_publication($index = NULL) {
    if (is_null($this->refresh_publication)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->refresh_publication);
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
  protected function p_do_build_refresh_publication() {
    if ($this->refresh_publication) {
      return c_database_string::REFRESH_PUBLICATION;
    }

    return NULL;
  }
}
