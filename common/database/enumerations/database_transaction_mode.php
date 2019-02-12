<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql transaction mode information.
 */
class e_database_transaction_mode {
  public const NONE                             = 0;
  public const DEFERRABLE                       = 1;
  public const ISOLATION_LEVEL_REPEATABLE_READ  = 2;
  public const ISOLATION_LEVEL_READ_COMMITTED   = 3;
  public const ISOLATION_LEVEL_READ_UNCOMMITTED = 4;
  public const ISOLATION_LEVEL_SERIALIZABLE     = 5;
  public const NOT_DEFERRABLE                   = 6;
  public const READ_WRITE                       = 7;
  public const READ_ONLY                        = 8;
}
