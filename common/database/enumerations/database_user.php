<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql user/session information.
 */
class e_database_user {
  public const NONE    = 0;
  public const ALL     = 1;
  public const CURRENT = 2;
  public const NAME    = 3;
  public const PUBLIC  = 4;
  public const SESSION = 5;
}
