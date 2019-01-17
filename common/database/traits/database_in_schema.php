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
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_in_schema($schema_name) {
    if (is_null($schema_name)) {
      $this->in_schema = NULL;
      return new c_base_return_true();
    }

    if (is_string($schema_name)) {
      if (!is_array($this->in_schema)) {
        $this->in_schema = [];
      }

      $placeholder = $this->add_placeholder($schema_name);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->in_schema[] = $placeholder;
      unset($placeholder);

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
   * @return i_database_query_placeholder|c_base_return_array|c_base_return_null
   *   An array of schema names or NULL if not defined.
   *   A single schema name query placeholder is returned if $index is an integer.
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
      if (is_int($index) && array_key_exists($index, $this->in_schema) && isset($this->in_schema[$index])) {
        return clone($this->in_schema[$index]);
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
   *   A string is returned.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_in_schema() {
    return c_database_string::IN_SCHEMA . ' ' . implode(', ', $this->in_schema);
  }
}
