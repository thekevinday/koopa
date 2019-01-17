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
 * Provide the sql AS data type functionality.
 */
trait t_database_as_data_type {
  protected $as_data_type;

  /**
   * Set the AS data type settings.
   *
   * @param string|null $type
   *   The data type to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_as_data_type($type) {
    if (is_null($type)) {
      $this->as_data_type = NULL;
      return new c_base_return_true();
    }

    if (is_string($type)) {
      $placeholder = $this->add_placeholder($type);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->as_data_type = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned data type.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A data type query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_as_data_type() {
    if (is_null($this->as_data_type)) {
      return new c_base_return_null();
    }

    if (isset($this->as_data_type)) {
      return clone($this->as_data_type);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'as_data_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_as_data_type() {
    return c_database_string::AS . ' ' . $this->as_data_type;
  }
}
