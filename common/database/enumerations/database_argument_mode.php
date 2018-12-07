<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database argument type mode.
 */
class e_database_argument_mode {
  public const NONE     = 0;
  public const IN       = 1;
  public const INOUT    = 2;
  public const OUT      = 3;
  public const VARIADIC = 4;
}
