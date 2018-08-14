<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with RESET and related queries.
 */
class e_database_reset {
  public const NONE      = 0;
  public const ALL       = 1;
  public const PARAMETER = 2;
}
