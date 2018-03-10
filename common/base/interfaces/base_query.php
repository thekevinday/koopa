<?php
/**
 * @file
 * Provides interfaces for specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * An interface for building SQL queries.
 */
interface i_base_query {

  /**
   * Build the Postgresql query string.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_build();

  /**
   * Reset all values in this class.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_reset();
}

/**
 * An interface for passing arguments to a query that may or may not have SQL functions.
 */
interface i_base_query_argument {

  /**
   * Return a processed argument.
   *
   * @return c_base_return_string|c_base_return_false
   *   An SQL string representing the built argument.
   *   FALSE without error bit set is returned if there is nothing to build.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_build_argument();
}
