<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql role information.
 */
class e_database_role {
  public const NONE   = 0;
  public const GROUP  = 1;
  public const NAME   = 2;
  public const PUBLIC = 3;
}
