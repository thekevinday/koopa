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

require_once('common/database/enumerations/database_options.php');

/**
 * Provide options support for an SQL query.
 */
trait t_database_options {
  protected $options;

  /**
   * Set the in options.
   *
   * @param string|null $options_type
   *   The option type to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   * @param string $value
   *   The value to associate with the options_type.
   *   When options_type is NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_options($options_type, $value) {
    if (is_null($options_type)) {
      $this->options = NULL;
      return new c_base_return_true();
    }

    if (!is_string($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($options_type) {
      case e_database_options::ADD:
      case e_database_options::DROP:
      case e_database_options::SET:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'schema_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    };

    if (!is_array($this->options)) {
      $this->options = [];
    }

    $placeholder = $this->add_placeholder($value);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->options[] = [
      'type' => $options_type,
      'value' => $placeholder,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the options.
   *
   * @param int|null $index
   *   (optional) Get the options at the specified index.
   *   When NULL, all options are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of options arrays or NULL if not defined.
   *   A single options array is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_options($index = NULL) {
    if (is_null($this->options)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->options)) {
        return c_base_return_array::s_new($this->options);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->options) && is_array($this->options[$index])) {
        return c_base_return_array::s_new($this->options[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'options[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'options', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_options() {
    $options = [];
    foreach ($this->options as $options_value) {
      if ($options_value['type'] == e_database_options::ADD) {
        $options[] = c_database_string::ADD . ' ' . $options_value['value'];
      }
      else if ($options_value['type'] == e_database_options::DROP) {
        $options[] = c_database_string::DROP . ' ' . $options_value['value'];
      }
      else if ($options_value['type'] == e_database_options::SET) {
        $options[] = c_database_string::SET . ' ' . $options_value['value'];
      }
    }
    unset($options_value);

    return empty($options) ? NULL : c_database_string::OPTIONS . ' ' . implode(', ', $options);
  }
}
