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
class e_database_validator {
  public const NONE         = 0;
  public const NO_VALIDATOR = 1;
  public const VALIDATOR    = 2;
}
