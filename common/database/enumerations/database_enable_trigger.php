<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql enable/disable trigger information.
 */
class e_database_enable_trigger {
  public const NONE    = 0;
  public const ALL     = 1;
  public const ALWAYS  = 2;
  public const NAME    = 3;
  public const REPLICA = 4;
  public const USER    = 5;
}
