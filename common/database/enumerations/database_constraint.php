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
class e_database_constraint {
  public const NONE     = 0;
  public const ADD      = 1;
  public const ALTER    = 2;
  public const DROP     = 3;
  public const VALIDATE = 4;
}
