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
 * Provide the sql DROP TABLE functionality.
 */
trait t_database_drop_value {
  protected $drop_value;

  /**
   * Set the DROP TABLE settings.
   *
   * @param string|null $name
   *   The table name.
   *   Set to NULL to disable.
   * @param bool|null $only
   *   (optional) whether or not to specify ONLY.
   * @param bool|null $descendents
   *   (optional) whether or not to specify a wildcard after the table name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_drop_value($name, $only = NULL, $descendents = NULL) {
    if (is_null($name)) {
      $this->drop_value = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($only) && !is_bool($only)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'only', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($descendents) && !is_bool($descendents)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'descendents', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    if (!is_array($this->drop_value)) {
      $this->drop_value = [
        'only' => NULL,
        'values' => [],
      ];
    }

    $this->drop_value['values'][] = [
      'name' => $placeholder,
      'descendents' => $descendents,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned add table settings.
   *
   * @param int|null $index
   *   (optional) Get the add table settings at the specified index.
   *   When NULL, all add table settings are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the add table settings.
   *   NULL is returned if not set (add table not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_drop_value() {
    if (is_null($this->drop_value)) {
      return new c_base_return_null();
    }

    if (isset($this->drop_value)) {
      return c_base_return_array::s_new($this->drop_value);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'drop_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_drop_value() {
    $value = c_database_string::DROP_TABLE;

    if ($this->drop_value['only']) {
      $value .= ' ' . c_database_string::ONLY;
    }

    $values = [];
    foreach ($this->drop_value['values'] as $drop_value) {
      $name = $drop_value['name'];
      if ($drop_value['descendents']) {
        $name .= ' *';
      }
      $values[] =  $name;
    }
    unset($drop_value);
    unset($name);

    return $value . ' ' . implode(', ', $values);
  }
}
