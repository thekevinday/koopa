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

require_once('common/database/enumerations/database_index_storage_parameter.php');

/**
 * Provide the sql RESET (storage_parameter ...) functionality.
 */
trait t_database_reset_storage_parameter {
  protected $reset_storage_parameter;

  /**
   * Set the RESET (storage_parameter ...) settings.
   *
   * @param int|null $storage_parameter
   *   The index storage_parameter code to assign.
   *   Should be one of: e_database_storage_parameter.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_reset_storage_parameter($storage_parameter) {
    if (is_null($storage_parameter)) {
      $this->reset_storage_parameter = NULL;
      return new c_base_return_true();
    }

    switch ($storage_parameter) {
      case e_database_storage_parameter::AUTOSUMMARIZE:
      case e_database_storage_parameter::BUFFERING:
      case e_database_storage_parameter::FASTUPDATE:
      case e_database_storage_parameter::FILLFACTOR:
      case e_database_storage_parameter::GIN_PENDING_LIST_LIMIT:
      case e_database_storage_parameter::PAGES_PER_RANGE:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'storage_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_array($this->reset_storage_parameter)) {
      $this->reset_storage_parameter = [];
    }

    $this->reset_storage_parameter[] = $storage_parameter;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned RESET storage parameter at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the index storage parameter type at the specified index.
   *   When NULL, all index storage parameter types are returned.
   *
   * @return c_base_return_array|c_base_return_int|c_base_return_null
   *   A code or an array of codes representing the argument_type on success.
   *   NULL is returned if not set (reset_storage_parameter tablespace is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_reset_storage_parameter($index = NULL) {
    if (is_null($this->reset_storage_parameter)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->reset_storage_parameter)) {
        return c_base_return_array::s_new($this->reset_storage_parameter);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->reset_storage_parameter) && is_int($this->reset_storage_parameter[$index])) {
        return c_base_return_int::s_new($this->reset_storage_parameter[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'reset_storage_parameter[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'reset_storage_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_reset_storage_parameter() {
    $values = [];
    foreach ($this->reset_storage_parameter as $storage_parameter) {
      if ($storage_parameter === e_database_storage_parameter::AUTOSUMMARIZE) {
        $values[] = c_database_string::AUTOSUMMARIZE;
      }
      else if ($storage_parameter === e_database_storage_parameter::BUFFERING) {
        $values[] = c_database_string::BUFFERING;
      }
      else if ($storage_parameter === e_database_storage_parameter::FASTUPDATE) {
        $values[] = c_database_string::FASTUPDATE;
      }
      else if ($storage_parameter === e_database_storage_parameter::FILLFACTOR) {
        $values[] = c_database_string::FILLFACTOR;
      }
      else if ($storage_parameter === e_database_storage_parameter::GIN_PENDING_LIST_LIMIT) {
        $values[] = c_database_string::GIN_PENDING_LIST_LIMIT;
      }
      else if ($storage_parameter === e_database_storage_parameter::PAGES_PER_RANGE) {
        $values[] = c_database_string::PAGES_PER_RANGE;
      }
    }
    unset($storage_parameter);

    return c_database_string::RESET . ' (' . implode(', ', $values) . ')';
  }
}
