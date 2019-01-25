<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with REPLICA IDENTITY, and related queries.
 */
class e_database_row_level_security {
  public const NONE     = 0;
  public const DISABLE  = 1;
  public const ENABLE   = 2;
  public const FORCE    = 3;
  public const NO_FORCE = 4;
}
