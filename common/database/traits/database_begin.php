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

require_once('common/database/enumerations/database_transaction_action.php');

/**
 * Provide the sql BEGIN functionality.
 */
trait t_database_begin {
  protected $begin;

  /**
   * Assign the settings.
   *
   * @param int|null $begin
   *   The begin code from e_database_transaction_action.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_begin($begin) {
    if (is_null($begin)) {
      $this->begin = NULL;
      return new c_base_return_true();
    }

    switch ($begin) {
      case e_database_transaction_action::TRANSACTION:
      case e_database_transaction_action::WORK:
        break;

      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'begin', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->begin = $begin;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_int|c_base_return_null
   *   A e_database_transaction_action on success.
   *   NULL is returned if not set (begin is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_begin() {
    if (is_null($this->begin)) {
      return new c_base_return_null();
    }

    if (is_int($this->begin)) {
      return c_base_return_int::s_new($this->begin);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'begin', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_begin() {
    $value = NULL;
    if ($this->begin === e_database_transaction_action::TRANSACTION) {
      $value = c_database_string::TRANSACTION;
    }
    else if ($this->begin === e_database_transaction_action::WORK) {
      $value = c_database_string::WORK;
    }

    return $value;
  }
}
