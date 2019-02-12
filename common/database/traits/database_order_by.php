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
 * Provide the sql ORDER BY functionality.
 */
trait t_database_order_by {
  protected $order_by;

  /**
   * Assign the settings.
   *
   * @param string|null $order_by
   *   The name to group by.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_order_by($order_by) {
    if (is_null($order_by)) {
      $this->order_by = NULL;
      return new c_base_return_true();
    }

    if (!is_string($order_by)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'order_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->order_by)) {
      $this->order_by = [];
    }

    $placeholder = $this->add_placeholder($order_by);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->order_by[] = $placeholder;
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned for group by value.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of group by values or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_order_by() {
    if (is_null($this->order_by)) {
      return new c_base_return_null();
    }

    if (is_array($this->order_by)) {
      return c_base_return_array::s_new($this->order_by);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'order_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_order_by() {
    return c_database_string::ORDER . ' ' . c_database_string::BY . ' ' . implode(', ', $this->order_by);
  }
}
