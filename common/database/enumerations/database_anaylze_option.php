<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database analyze options.
 */
class e_database_analyze_option {
  public const NONE    = 0;
  public const VERBOSE = 1;
}
