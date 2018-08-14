<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with GROUP BY, ORDER BY, and related queries.
 */
class e_database_direction {
  public const NONE    = 0;
  public const ASCEND  = 1;
  public const DESCEND = 2;
}
