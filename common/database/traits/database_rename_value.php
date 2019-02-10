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
 * Provide the sql RENAME VALUE functionality.
 */
trait t_database_rename_value {
  protected $rename_value;

  /**
   * Set the RENAME VALUE settings.
   *
   * @param string|null $from
   *   The name to rename from.
   *   Set to NULL to disable.
   * @param string|null $to
   *   (optional) The name to rename to.
   *   Required when $from is not NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_rename_value($from, $to = NULL) {
    if (is_null($from)) {
      $this->rename_value = NULL;
      return new c_base_return_true();
    }

    if (!is_string($from)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'from', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($to)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'to', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder_from = $this->add_placeholder($from);
    if ($placeholder_from->has_error()) {
      return c_base_return_error::s_false($placeholder_from->get_error());
    }

    $placeholder_to = $this->add_placeholder($to);
    if ($placeholder_to->has_error()) {
      unset($placeholder_from);
      return c_base_return_error::s_false($placeholder_to->get_error());
    }

    $this->rename_value = [
      'from' => $placeholder_from,
      'to' => $placeholder_to,
    ];
    unset($placeholder_from);
    unset($placeholder_to);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned nsettings.
   *
   * @return c_database_return_array|c_base_return_null
   *   An array containing the settings.
   *   NULL is returned if not set (rename to is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_rename_value() {
    if (is_null($this->rename_value)) {
      return new c_base_return_null();
    }

    if (is_array($this->rename_value)) {
      return c_database_return_array::s_new($this->rename_value);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'rename_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_rename_value() {
    $value = c_database_string::RENAME_ATTRIBUTE;
    $value .= ' ' . $this->rename_value['from'];
    $value .= ' ' . c_database_string::TO;
    $value .= ' ' . $this->rename_value['to'];
    return $value;
  }
}
