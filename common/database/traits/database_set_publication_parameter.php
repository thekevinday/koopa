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

require_once('common/database/enumerations/database_publication_parameter.php');
require_once('common/database/enumerations/database_publication_value.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql SET publication parameter functionality.
 */
trait t_database_set_publication_parameter {
  protected $set_publication_parameter;

  /**
   * Set the SET (publication_parameter ...) settings.
   *
   * @param int|null $parameter
   *   The publication parameter code to assign.
   *   Should be one of: e_database_publication_parameter.
   *   Set to NULL to disable.
   * @param int|null $value
   *   The type code to assign the parameter from e_database_publication_value.
   *   This must not be NULL when $parameter is not NULL.
   *   When NULL, this is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_set_publication_parameter($parameter, $value = NULL) {
    if (is_null($parameter)) {
      $this->set_publication_parameter = NULL;
      return new c_base_return_true();
    }

    switch ($parameter) {
      case e_database_publication_parameter::PUBLISH:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    switch ($value) {
      case e_database_publication_value::DELETE:
      case e_database_publication_value::INSERT:
      case e_database_publication_value::UPDATE:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_array($this->set_publication_parameter)) {
      $this->set_publication_parameter = [];
    }

    $this->set_publication_parameter[] = [
      'type' => $parameter,
      'value' => $value,
    ];

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned SET publication parameter at the specified index.
   *
   * @param int|null $index
   *   (optional) Get the publication parameter type at the specified index.
   *   When NULL, all publication parameter types are returned.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the set publication parameter settings.
   *   NULL is returned if not set (publication parameter not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_set_publication_parameter($index = NULL) {
    if (is_null($this->set_publication_parameter)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->set_publication_parameter)) {
        return c_base_return_array::s_new($this->set_publication_parameter);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->set_publication_parameter)) {
        return c_base_return_array::s_new($this->set_publication_parameter[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_publication_parameter[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'set_publication_parameter', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_set_publication_parameter() {
    $values = [];
    foreach ($this->set_publication_parameter as $parameter => $value) {
      if ($parameter === e_database_publication_parameter::PUBLISH) {
        $parameter_value = c_database_string::PUBLISH . ' = ';

        if ($value === e_database_publication_value::DELETE) {
          $parameter_value .= c_database_string::DELETE;
        }
        else if ($value === e_database_publication_value::INSERT) {
          $parameter_value .= c_database_string::INSERT;
        }
        else if ($value === e_database_publication_value::UPDATE) {
          $parameter_value .= c_database_string::UPDATE;
        }
        else {
          continue;
        }

        $values[] = $parameter_value;
      }
    }
    unset($parameter_value);
    unset($parameter);
    unset($value);

    return c_database_string::SET . ' (' . implode(', ', $values) . ')';
  }
}
