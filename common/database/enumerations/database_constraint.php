<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database constaint.
 */
class e_database_constaint {
  public const NONE     = 0;
  public const ADD      = 1;
  public const DROP     = 2;
  public const VALIDATE = 3;
}
