<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database grant/revoke.
 */
class e_database_grant {
  public const NONE   = 0;
  public const GRANT  = 1;
  public const REVOKE = 2;
}
