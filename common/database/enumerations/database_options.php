<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database options.
 */
class e_database_options {
  public const NONE = 0;
  public const ADD  = 1;
  public const DROP = 2;
  public const SET  = 3;
}
