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
interface i_database_query {

  /**
   * Build the Postgresql query string.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE without error bit set is returned if there is nothing to build.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_build();

  /**
   * Reset all query values in this class.
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
interface i_database_argument {

  /**
   * Build the Postgresql query argument.
   *
   * @return c_base_return_bool
   *   TRUE is returned on success.
   *   FALSE without error bit set is returned if there is nothing to build.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_build_argument();

  /**
   * Reset all argument values in this class.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function do_reset_argument();
}
