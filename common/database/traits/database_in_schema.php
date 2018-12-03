<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql IN SCHEMA functionality.
 */
trait t_database_in_schema {
  protected $in_schema;

  /**
   * Set the in schema, schema names.
   *
   * @param string|null $schema_name
   *   The schema name to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all schema names regardless of the $append parameter.
   * @param bool $append
   *   (optional) When TRUE, the schema name will be appended.
   *   When FALSE, any existing schema names will be cleared before appending the schema name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_in_schema($schema_name, $append = TRUE) {
    if (is_null($schema_name)) {
      $this->in_schema = NULL;
      return new c_base_return_true();
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_string($schema_name)) {
      if ($append) {
        if (!is_array($this->in_schema)) {
          $this->in_schema = [];
        }

        $this->in_schema[] = $schema_name;
      }
      else {
        $this->in_schema = [$schema_name];
      }

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'schema_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the in schema, schema names.
   *
   * @param int|null $index
   *   (optional) Get the schema name at the specified index.
   *   When NULL, all schema names are returned.
   *
   * @return c_base_return_string|c_base_return_array|c_base_return_null
   *   An array of schema names or NULL if not defined.
   *   A single schema name is returned if $index is an integer.
   *   NULL with the error bit set is returned on error.
   */
  public function get_in_schema($index = NULL) {
    if (is_null($this->in_schema)) {
      return new c_base_return_null();
    }

    if (is_null($index)) {
      if (is_array($this->in_schema)) {
        return c_base_return_array::s_new($this->in_schema);
      }
    }
    else {
      if (is_int($index) && array_key_exists($index, $this->in_schema) && is_string($this->in_schema[$index])) {
        return c_base_return_string::s_new($this->in_schema[$index]);
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'in_schema[index]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_null($error);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'in_schema', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned on success.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_in_schema() {
    return c_database_string::IN . ' ' . c_database_string::SCHEMA . ' ' . implode(', ', $this->in_schema);
  }
}
