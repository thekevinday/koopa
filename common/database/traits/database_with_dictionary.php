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
 * Provide the sql WITH DICTIONARY functionality.
 */
trait t_database_with_dictionary {
  protected $with_dictionary;

  /**
   * Set the WITH DICTIONARY settings.
   *
   * @param string|null $name
   *   The dictionary name.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_with_dictionary($name) {
    if (is_null($name)) {
      $this->with_dictionary = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->with_dictionary = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned dictionary name to set to.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A dictionary name query placeholder on success.
   *   NULL is returned if not set (set schema is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_with_dictionary() {
    if (is_null($this->with_dictionary)) {
      return new c_base_return_null();
    }

    if (isset($this->with_dictionary)) {
      return clone($this->with_dictionary);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'with_dictionary', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_with_dictionary() {
    return c_database_string::WITH_DICTIONARY . ' ' . $this->with_dictionary;
  }
}
