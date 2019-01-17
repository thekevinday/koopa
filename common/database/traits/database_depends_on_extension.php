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
 * Provide the sql DEPENDS ON EXTENSION functionality.
 */
trait t_database_depends_on_extension {
  protected $depends_on_extension;

  /**
   * Set the RENAME TO settings.
   *
   * @param string|null $depends_on_extension
   *   The name to rename to.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_depends_on_extension($depends_on_extension) {
    if (!is_null($depends_on_extension) && !is_string($depends_on_extension)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'depends_on_extension', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($depends_on_extension);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->depends_on_extension = $placeholder;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned schema name to set to.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   An extension name on success.
   *   NULL is returned if not set (depends on extension is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_depends_on_extension() {
    if (is_null($this->depends_on_extension)) {
      return new c_base_return_null();
    }

    if (isset($this->depends_on_extension)) {
      return clone($this->depends_on_extension);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'depends_on_extension', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_depends_on_extension() {
    return c_database_string::DEPENDS_ON_EXTENSION . ' ' . $this->depends_on_extension;
  }
}
