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

require_once('common/database/enumerations/database_transaction_mode.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql transaction action functionality.
 */
trait t_database_transaction_mode {
  protected $transaction_mode;

  /**
   * Assign the settings.
   *
   * @param int|null $mode
   *   The mode, one of e_database_transaction_mode.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_transaction_mode($mode) {
    if (is_null($action)) {
      $this->transaction_mode = NULL;
      return new c_base_return_true();
    }

    switch ($action) {
      case e_database_transaction_mode::DEFERRABLE:
      case e_database_transaction_mode::ISOLATION_LEVEL_REPEATABLE_READ:
      case e_database_transaction_mode::ISOLATION_LEVEL_READ_COMMITTED:
      case e_database_transaction_mode::ISOLATION_LEVEL_READ_UNCOMMITTED:
      case e_database_transaction_mode::ISOLATION_LEVEL_SERIALIZABLE:
      case e_database_transaction_mode::NOT_DEFERRABLE:
      case e_database_transaction_mode::READ_WRITE:
      case e_database_transaction_mode::READ_ONLY:
        break;

      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->transaction_mode = $action;
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
  public function get_transaction_mode() {
    if (is_null($this->transaction_mode)) {
      return new c_base_return_null();
    }

    if (is_int($this->transaction_mode)) {
      return c_base_return_int::s_new($this->transaction_mode);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'transaction_mode', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_transaction_mode() {
    $value = NULL;

    if ($this->transaction_mode === e_database_transaction_mode::DEFERRABLE) {
      $value = c_database_string::DEFERRABLE;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::ISOLATION_LEVEL_REPEATABLE_READ) {
      $value = c_database_string::ISOLATION . ' ' . c_database_string::LEVEL . ' ' . c_database_string::REPEATABLE . ' ' . c_database_string::READ;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::ISOLATION_LEVEL_READ_COMMITTED) {
      $value = c_database_string::ISOLATION . ' ' . c_database_string::LEVEL . ' ' . c_database_string::READ . ' ' . c_database_string::COMMITTED;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::ISOLATION_LEVEL_READ_UNCOMMITTED) {
      $value = c_database_string::ISOLATION . ' ' . c_database_string::LEVEL . ' ' . c_database_string::READ . ' ' . c_database_string::UNCOMMITTED;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::ISOLATION_LEVEL_SERIALIZABLE) {
      $value = c_database_string::ISOLATION . ' ' . c_database_string::LEVEL . ' ' . c_database_string::SERIALIZABLE;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::NOT_DEFERRABLE) {
      $value = c_database_string::NOT . ' ' . c_database_string::DEFERRABLE;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::READ_WRITE) {
      $value = c_database_string::READ . ' ' . c_database_string::WRITE;
    }
    else if ($this->transaction_mode === e_database_transaction_mode::READ_ONLY) {
      $value = c_database_string::READ . ' ' . c_database_string::ONLY;
    }
    return $value;
  }
}
