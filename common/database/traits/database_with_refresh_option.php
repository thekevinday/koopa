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

require_once('common/database/enumerations/database_refresh_option.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql WITH refresh option functionality.
 */
trait t_database_with_refresh_option {
  protected $with_refresh_option;

  /**
   * Assign the settings.
   *
   * @param int|null $option
   *   The refresh option code to assign.
   *   Should be one of: e_database_refresh_option.
   *   Set to NULL to disable.
   * @param bool|null $value
   *   Set the option value to either TRUE or FALSE.
   *   This is ignored when $option is NULL.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_with_refresh_option($option, $value = NULL) {
    if (is_null($option)) {
      $this->with_refresh_option = NULL;
      return new c_base_return_true();
    }

    switch ($option) {
      case e_database_refresh_option::REFRESH:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_bool($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->with_refresh_option)) {
      $this->with_refresh_option = [];
    }

    $this->with_refresh_option[] = [
      'option' => $option,
      'value' => $value,
    ];

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned WITH refresh option.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the with refresh option settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_with_refresh_option() {
    if (is_null($this->with_refresh_option)) {
      return new c_base_return_null();
    }

    if (is_array($this->with_refresh_option)) {
      return c_base_return_array::s_new($this->with_refresh_option);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'with_refresh_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_with_refresh_option() {
    $values = [];
    foreach ($this->with_refresh_option as $option) {
      if ($parameter === e_database_refresh_option::COPY_DATA) {
        $value = c_database_string::COPY_DATA . ' = ';

        if ($option) {
          $value .= c_database_string::TRUE;
        }
        else {
          $value .= c_database_string::FALSE;
        }

        $values[] = $value;
      }
    }
    unset($option);
    unset($value);

    return c_database_string::WITH . ' (' . implode(', ', $values) . ')';
  }
}
