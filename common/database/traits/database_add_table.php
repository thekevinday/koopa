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
 * Provide the sql ADD TABLE functionality.
 */
trait t_database_add_table {
  protected $add_table;

  /**
   * Set the ADD TABLE settings.
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
  public function set_add_table($name, $only = NULL, $descendents = NULL) {
    if (is_null($name)) {
      $this->add_table = NULL;
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

    if (!is_array($this->add_table)) {
      $this->add_table = [
        'only' => NULL,
        'values' => [],
      ];
    }

    $this->add_table['values'][] = [
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
   *   An array containing the add table settings on success.
   *   NULL is returned if not set (add table not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_add_table() {
    if (is_null($this->add_table)) {
      return new c_base_return_null();
    }

    if (isset($this->add_table)) {
      return c_base_return_array::s_new($this->add_table);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'add_table', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_add_table() {
    $value = c_database_string::ADD_TABLE;

    if ($this->add_table['only']) {
      $value .= ' ' . c_database_string::ONLY;
    }

    $values = [];
    foreach ($this->add_table['values'] as $add_value) {
      $name = $add_value['name'];
      if ($add_value['descendents']) {
        $name .= ' *';
      }
      $values[] =  $name;
    }
    unset($add_value);
    unset($name);

    return $value . ' ' . implode(', ', $values);
  }
}
