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

require_once('common/database/enumerations/database_analyze_option.php');

/**
 * Provide the sql ANALYZE options functionality.
 */
trait t_database_analyze_options {
  protected $analyze_options;

  /**
   * Assign the settings.
   *
   * @param int|null $attribute_option
   *   The attribute option code to assign.
   *   Should be one of: e_database_analyze_option.
   *   When both this and $name are NULL, then column reset is disabled.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_analyze_options($option) {
    if (is_null($option)) {
      $this->analyze_options = NULL;
      return new c_base_return_true();
    }

    switch ($option) {
      case e_database_analyze_option::VERBOSE:
        break;
      case NULL:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_array($this->analyze_options)) {
      $this->analyze_options = [];
    }

    $this->analyze_options[] = $option;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of codes representing the argument_type on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_analyze_options() {
    if (is_null($this->analyze_options)) {
      return new c_base_return_null();
    }

    if (is_array($this->analyze_options)) {
      return c_base_return_array::s_new($this->analyze_options);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'analyze_options', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_analyze_options() {
    $values = [];
    foreach ($this->analyze_options as $analyze_option) {
      if ($analyze_option === e_database_attribute_option::VERBOSE) {
        $values[] = c_database_string::VERBOSE;
      }
    }
    unset($analyze_option);

    return implode(', ', $values);
  }
}
