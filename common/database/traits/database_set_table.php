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
 * Provide the sql SET TABLE functionality.
 */
trait t_database_set_table {
  protected $set_table;

  /**
   * Assign the settings.
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
  public function set_set_table($name, $only = NULL, $descendents = NULL) {
    if (is_null($name)) {
      $this->set_table = NULL;
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

    if (!is_array($this->set_table)) {
      $this->set_table = [
        'only' => NULL,
        'values' => [],
      ];
    }

    $this->set_table['values'][] = [
      'name' => $placeholder,
      'descendents' => $descendents,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned add table settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the add table settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_table() {
    if (is_null($this->set_table)) {
      return new c_base_return_null();
    }

    if (is_array($this->set_table)) {
      return c_base_return_array::s_new($this->set_table);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_table', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_table() {
    $value = c_database_string::SET . ' ' . c_database_string::TABLE;

    if ($this->set_table['only']) {
      $value .= ' ' . c_database_string::ONLY;
    }

    $values = [];
    foreach ($this->set_table['values'] as $set_value) {
      $name = $set_value['name'];
      if ($set_value['descendents']) {
        $name .= ' *';
      }
      $values[] =  $name;
    }
    unset($set_value);
    unset($name);

    return $value . ' ' . implode(', ', $values);
  }
}
