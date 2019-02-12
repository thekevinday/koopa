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

require_once('common/database/enumerations/database_transaction_action.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql transaction action functionality.
 */
trait t_database_transaction_action {
  protected $transaction_action;

  /**
   * Assign the settings.
   *
   * @param int|null $action
   *   The action, one of e_database_transaction_action.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_transaction_action($action) {
    if (is_null($action)) {
      $this->transaction_action = NULL;
      return new c_base_return_true();
    }

    switch ($action) {
      case e_database_transaction_action::TRANSACTION:
      case e_database_transaction_action::WORK:
        break;

      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->transaction_action = $action;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_int|c_base_return_null
   *   A code representing the setting.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_transaction_action() {
    if (is_null($this->transaction_action)) {
      return new c_base_return_null();
    }

    if (is_int($this->transaction_action)) {
      return c_base_return_int::s_new($this->transaction_action);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'transaction_action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_transaction_action() {
    $value = NULL;

    if ($this->transaction_action === e_database_transaction_action::TRANSACTION) {
      $value = c_database_string::TRANSACTION;
    }
    else if ($this->transaction_action === e_database_transaction_action::WORK) {
      $value = c_database_string::WORK;
    }

    return $value;
  }
}
