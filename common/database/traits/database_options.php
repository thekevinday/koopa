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
   * Assign the settings.
   *
   * @param string|null $type
   *   The option type to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   * @param string|null $option
   *   (optional) The option name to use.
   *   When type is NULL, this is ignored.
   *   Required when $type is not NULL.
   * @param string|null $value
   *   (optional) The value to associate with the type.
   *   When type is NULL, this is ignored.
   *   Required when $type is not NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_options($type, $option = NULL, $value = NULL) {
    if (is_null($type)) {
      $this->options = NULL;
      return new c_base_return_true();
    }

    if (!is_string($option)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($type) {
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

    $placeholder_option = $this->add_placeholder($option);
    if ($placeholder_option->has_error()) {
      return c_base_return_error::s_false($placeholder_option->get_error());
    }

    $placeholder_value = $this->add_placeholder($value);
    if ($placeholder_value->has_error()) {
      unset($placeholder_option);
      return c_base_return_error::s_false($placeholder_value->get_error());
    }

    $this->options[] = [
      'type' => $type,
      'option' => $placeholder_option,
      'value' => $placeholder_value,
    ];
    unset($placeholder_option);
    unset($placeholder_value);

    return new c_base_return_true();
  }

  /**
   * Get the options.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of options arrays or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_options() {
    if (is_null($this->options)) {
      return new c_base_return_null();
    }

    if (is_array($this->options)) {
      return c_base_return_array::s_new($this->options);
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
   *   A string is returned.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_options() {
    $value = c_database_string::OPTIONS;

    $values = [];
    foreach ($this->options['values'] as $set) {
      if ($set['type'] === e_database_options::ADD) {
        $set_value = c_database_string::ADD;
      }
      else if ($set['type'] === e_database_options::DROP) {
        $set_value = c_database_string::DROP;
      }
      else if ($set['type'] === e_database_options::SET) {
        $set_value = c_database_string::SET;
      }
      else {
        continue;
      }

      $set_value .= ' ' . $set['option'] . ' ' . $set['value'];
      $values[] = $set_value;
    }
    unset($set);
    unset($set_value);

    return $value . ' ' . implode(', ', $values);
  }
}
