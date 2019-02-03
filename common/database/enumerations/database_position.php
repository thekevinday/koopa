<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database position.
 */
class e_database_position {
  public const NONE   = 0;
  public const AFTER  = 1;
  public const BEFORE = 2;
}
