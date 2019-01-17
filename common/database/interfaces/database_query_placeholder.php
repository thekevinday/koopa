<?php
/**
 * @file
 * Provides interfaces for specific Postgesql query parameter placeholders.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * An interface for passing query parameter placeholders to a query for use by PDO or similar.
 *
 * The implementing class is expected and assumed to be of type c_base_return_value.
 * The get_value(), set_value(), and has_value() method should represent this.
 */
interface i_database_query_placeholder {

  /**
   * Reset all query values in this class.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_reset();

  /**
   * Return the value.
   *
   * @return $value
   *   Always returns value represented by this class.
   */
  public function get_value();

  /**
   * Gets the id used for generating the placeholder name.
   *
   * @return c_base_return_id|c_base_return_null
   *   A value is returned.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_id();

  /**
   * Gets a placeholder name, generated from the prefix and id.
   *
   * @return c_base_return_string|c_base_return_null
   *   A value is returned.
   *   NULL is returned if not assigned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_name();

  /**
   * Gets the prefix used for generating the placeholder name.
   *
   * @return c_base_return_string|c_base_return_null
   *   A value is returned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_placeholder();

   /**
   * Assign the placeholder value.
   *
   * @param $value
   *   The data to assign.
   *   The allowed data type is specific to implementing classes.
   *   Set to NULL to assign a value of NULL to the data.
   *   (NULL does not unassign this and is considered data.)
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value);

  /**
   * Assign the placeholder name id.
   *
   * @param int $id
   *   The id to use for generating the placeholder name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_id($id);

  /**
   * Assign the placeholder name prefix.
   *
   * @param string $prefix
   *   The prefix to use for generating the placeholder name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_prefix($prefix);
}
