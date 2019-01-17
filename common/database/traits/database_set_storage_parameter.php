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

require_once('common/database/enumerations/database_index_storage_parameter.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql SET storage parameter functionality.
 */
trait t_database_set_storage_parameter {
  protected $set_storage_parameter;

  /**
   * Set the SET index (storage_parameter ...) settings.
   *
   * @param int|null $parameter
   *   The storage parameter code to assign.
   *   Should be one of: e_database_storage_parameter.
   *   Set to NULL to disable.
   * @param string|null $value
   *   The value associated with the parameter.
   *   This must not be NULL when $storage_parameter is not NULL.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_storage_parameter($parameter, $value = NULL) {
    if (is_null($parameter)) {
      $this->set_storage_parameter = NULL;
      return new c_base_return_true();
    }

    switch ($parameter) {
      case e_database_storage_parameter::AUTOSUMMARIZE:
      case e_database_storage_parameter::BUFFERING:
      case e_database_storage_parameter::FASTUPDATE:
      case e_database_storage_parameter::FILLFACTOR:
      case e_database_storage_parameter::GIN_PENDING_LIST_LIMIT:
      case e_database_storage_parameter::PAGES_PER_RANGE:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_string($value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->set_storage_parameter)) {
      $this->set_storage_parameter = [];
    }

    $placeholder = $this->add_placeholder($value);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->set_storage_parameter[] = [
      'type' => $parameter,
      'value' => $placeholder,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned SET index storage parameter at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the index storage parameter type at the specified index.
   *   When NULL, all index storage parameter types are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the set index storage parameter settings.
   *   NULL is returned if not set (set index storage parameter not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_storage_parameter($index = NULL) {
    if (is_null($this->set_storage_parameter)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->set_storage_parameter)) {
        return c_base_return_array::s_new($this->set_storage_parameter);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->set_storage_parameter)) {
        return c_base_return_array::s_new($this->set_storage_parameter[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_storage_parameter[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_storage_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_storage_parameter() {
    $values = [];
    foreach ($this->set_storage_parameter as $parameter => $value) {
      if ($parameter === e_database_storage_parameter::AUTOSUMMARIZE) {
        $values[] = c_database_string::AUTOSUMMARIZE . ' = ' . $value;
      }
      else if ($parameter === e_database_storage_parameter::BUFFERING) {
        $values[] = c_database_string::BUFFERING . ' = ' . $value;
      }
      else if ($parameter === e_database_storage_parameter::FASTUPDATE) {
        $values[] = c_database_string::FASTUPDATE . ' = ' . $value;
      }
      else if ($parameter === e_database_storage_parameter::FILLFACTOR) {
        $values[] = c_database_string::FILLFACTOR . ' = ' . $value;
      }
      else if ($parameter === e_database_storage_parameter::GIN_PENDING_LIST_LIMIT) {
        $values[] = c_database_string::GIN_PENDING_LIST_LIMIT . ' = ' . $value;
      }
      else if ($parameter === e_database_storage_parameter::PAGES_PER_RANGE) {
        $values[] = c_database_string::PAGES_PER_RANGE . ' = ' . $value ;
      }
    }
    unset($parameter);
    unset($value);

    return c_database_string::SET . ' (' . implode(', ', $values) . ')';
  }
}
