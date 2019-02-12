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

require_once('common/database/enumerations/database_set_column_default.php');

/**
 * Provide the sql SET DEFAULT / DROP DEFAULT functionality.
 */
trait t_database_alter_column {
  protected $alter_column;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The column name to set to.
   *   Set to NULL to disable.
   * @param int|null $type
   *   (optional) The e_database_set_column_default to assign.
   *   Required when $name is not NULL.
   * @param string|null $expression
   *   (optional) The expression to use.
   *   This may be required or ignored depending on $type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_alter_column($name, $type = NULL, $expression = NULL) {
    if (is_null($name)) {
      $this->alter_column = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($type) {
      case e_database_set_column_default::DROP:
        $placeholder_expression = NULL;
        break;

      case e_database_set_column_default::SET:
        if (!is_string($expression)) {
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'expression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }

        $placeholder_expression = $this->add_placeholder($expression);
        if ($placeholder_expression->has_error()) {
          return c_base_return_error::s_false($placeholder_expression->get_error());
        }

        break;

      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $placeholder_name = $this->add_placeholder($name);
    if ($placeholder_name->has_error()) {
      unset($placeholder_expression);
      return c_base_return_error::s_false($placeholder_name->get_error());
    }

    $this->alter_column = [
      'name' => $placeholder_name,
      'type' => $type,
      'expression' => $placeholder_expression,
    ];
    unset($placeholder_name);
    unset($placeholder_expression);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned tablespace name to set to.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_alter_column() {
    if (is_null($this->alter_column)) {
      return new c_base_return_null();
    }

    if (is_array($this->alter_column)) {
      return c_base_return_array::s_new($this->alter_column);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'alter_column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_alter_column() {
    $value = c_database_string::ALTER . ' ' . c_database_string::COLUMN . ' ' . $this->alter_column['name'];

    if ($this->alter_column['type'] === e_database_set_column_default::DROP) {
      $value .= ' ' . c_database_string::DROP . ' ' . c_database_string::DEFAULT;
    }
    else if ($this->alter_column['type'] === e_database_set_column_default::SET) {
      $value .= ' ' . c_database_string::SET_DEFAULT;
      $value .= ' ' . $this->alter_column['expression'];
    }

    return $value;
  }
}
