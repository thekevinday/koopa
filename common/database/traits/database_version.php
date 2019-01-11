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
 * Provide the sql VERSION functionality.
 */
trait t_database_version {
  protected $version;

  /**
   * Set the VERSION settings.
   *
   * @param string|null $version
   *   The version to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_version($version) {
    if (is_null($version)) {
      $this->version = NULL;
      return new c_base_return_true();
    }

    if (is_string($version)) {
      $placeholder = $this->add_placeholder($version);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->version = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'version', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned on version settings.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A version query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_version() {
    if (is_null($this->version)) {
      return new c_base_return_null();
    }

    if (isset($this->version)) {
      return clone($this->version);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'version', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_version() {
    return strval($this->version);
  }
}
