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

require_once('common/database/enumerations/database_server_option.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql server OPTIONS functionality.
 */
trait t_database_server_options {
  protected $server_options;

  /**
   * Assign the settings.
   *
   * @param int|null $option
   *   The server option code to assign.
   *   Should be one of: e_database_server_option.
   *   Set to NULL to disable.
   * @param string|null $name
   *   (optional) The option name.
   *   This is ignored when $option is NULL.
   *   When NULL, this is ignored.
   * @param string|null $value
   *   (optional) The value associated with the option name.
   *   This is ignored when $option is NULL.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_server_options($option, $name = NULL, $value = NULL) {
    if (is_null($option)) {
      $this->server_options = NULL;
      return new c_base_return_true();
    }

    switch ($option) {
      case server_option::ADD:
      case server_option::DROP:
      case server_option::SET:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder_name = $this->add_placeholder($value);
    if ($placeholder_name->has_error()) {
      return c_base_return_error::s_false($placeholder_name->get_error());
    }

    $placeholder_value = NULL;
    if (!is_null($value)) {
      if (!is_string($value)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $placeholder_value = $this->add_placeholder($value);
      if ($placeholder_value->has_error()) {
        return c_base_return_error::s_false($placeholder_value->get_error());
      }
    }

    if (!is_array($this->server_options)) {
      $this->server_options = [];
    }

    $this->server_options[] = [
      'option' => $option,
      'name' => $placeholder_name,
      'value' => $placeholder_value,
    ];
    unset($placeholder_name);
    unset($placeholder_value);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned WITH refresh option.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the with server option settings.
   *   NULL is returned if not set (with refresh option not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_server_options() {
    if (is_null($this->server_options)) {
      return new c_base_return_null();
    }

    if (is_array($this->server_options)) {
      return c_base_return_array::s_new($this->server_options);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'server_options', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_server_options() {
    $values = [];
    foreach ($this->server_options as $option) {
      if ($parameter === server_option::ADD) {
        $value = c_database_string::ADD . ' =';
        $value .= ' ' . $option['name'];

        if (isset($option['value'])) {
          $value .= ' ' . $option['value'];
        }

        $values[] = $value;
      }
      else if ($parameter === server_option::DROP) {
        $value = c_database_string::DROP;
        $value .= ' ' . $option['name'];
        $values[] = $value;
      }
      else if ($parameter === server_option::SET) {
        $value = c_database_string::SET . ' =';
        $value .= ' ' . $option['name'];

        if (isset($option['value'])) {
          $value .= ' ' . $option['value'];
        }

        $values[] = $value;
      }
    }
    unset($option);
    unset($value);

    return c_database_string::OPTIONS . ' (' . implode(', ', $values) . ')';
  }
}
