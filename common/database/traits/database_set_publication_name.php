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
 * Provide the sql SET PUBLICATION name functionality.
 */
trait t_database_set_publication_name {
  protected $set_publication_name;

  /**
   * Set the SET PUBLICATION (name ...) settings.
   *
   * @param string|null $name
   *   The publication name.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_publication_name($name) {
    if (is_null($option)) {
      $this->set_publication_name = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->set_publication_name)) {
      $this->set_publication_name = [];
    }

    $this->set_publication_name[] = $name;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned SET PUBLICATION name at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the publication name at the specified index.
   *   When NULL, all publication names are returned.
   *
   * @return c_base_return_array|c_base_return_string|c_base_return_null
   *   An array of publication names or a string representing the publication name at $index..
   *   NULL is returned if not set (publication name not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_publication_name($index = NULL) {
    if (is_null($this->set_publication_name)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->set_publication_name)) {
        return c_base_return_array::s_new($this->set_publication_name);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->set_publication_name)) {
        return c_base_return_string::s_new($this->set_publication_name[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_publication_name[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_publication_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_publication_name() {
    return c_database_string::SET_PUBLICATION . ' ' . implode(', ', $this->set_publication_name);
  }
}
