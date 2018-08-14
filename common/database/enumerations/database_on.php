<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database options.
 */
class e_database_on {
  public const NONE      = 0;
  public const TABLES_TO = 1;
  public const SEQUENCES = 2;
  public const FUNCTIONS = 3;
  public const TYPES     = 4;
  public const SCHEMAS   = 5;
}
