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

require_once('common/database/enumerations/database_cascade.php');

/**
 * Provide the sql action DROP COLUMN functionality.
 */
trait t_database_action_drop_column {
  protected $action_drop_column;

  /**
   * Assign the settings.
   *
   * @param string|null $column_name
   *   The column name to use.
   *   Set to NULL to disable.
   * @param bool|null $if_exists
   *   (optional) TRUE for IF EXISTS, FALSE does nothing.
   *   Required when $column_name is not NULL.
   *   Ignored when $column_name is NULL.
   * @param int|null $cascade
   *   (optional) The cascade type from e_database_cascade.
   *   Ignored when $column_name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_action_drop_column($column_name, $if_exists = NULL, $cascade = NULL) {
    if (is_null($column_name)) {
      $this->action_drop_column = NULL;
      return new c_base_return_true();
    }

    if (!is_string($column_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'column_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($if_exists) && !is_bool($if_exists)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'if_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($cascade) {
      case e_database_cascade::CASCADE:
      case e_database_cascade::RESTRICT:
        break;

      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $placeholder_column_name = $this->add_placeholder($column_name);
    if ($placeholder_column_name->has_error()) {
      return c_base_return_error::s_false($placeholder_column_name->get_error());
    }

    $this->action_drop_column = [
      'column_name' => $placeholder_column_name,
      'if_exists' => $if_exists,
      'cascade' => $cascade,
    ];
    unset($placeholder_column_name);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned action drop column settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_action_drop_column() {
    if (is_null($this->action_drop_column)) {
      return new c_base_return_null();
    }

    if (is_array($this->action_drop_column)) {
      return c_base_return_array::s_new($this->action_drop_column);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'action_drop_column', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_action_drop_column() {
    $value = c_database_string::DROP_COLUMN . ' ' . $this->action_drop_column['column_name'];
    if ($this->action_drop_column['if_exists']) {
      $value = c_database_string::IF_EXISTS . ' ' . $value;
    }

    switch ($this->action_drop_column['cascade']) {
      case e_database_cascade::CASCADE:
        $value .= ' ' . c_database_string::CASCADE;
        break;
      case e_database_cascade::RESTRICT:
        $value .= ' ' . c_database_string::RESTRICT;
        break;
    }

    return $value;
  }
}
