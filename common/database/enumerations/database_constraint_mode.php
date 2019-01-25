<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database constaint mode.
 */
class e_database_constraint_mode {
  public const NONE                = 0;
  public const DEFERRABLE          = 1;
  public const INITIALLY_DEFERRED  = 2;
  public const INITIALLY_IMMEDIATE = 3;
  public const NOT_DEFERRABLE      = 4;
}
