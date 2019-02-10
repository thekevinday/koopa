<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with server option and related queries.
 */
class e_database_server_option {
  public const NONE = 0;
  public const ADD  = 1;
  public const DROP = 2;
  public const SET  = 3;
}
