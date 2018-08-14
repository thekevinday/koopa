<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database property.
 */
class e_database_property {
  public const NONE      = 0;
  public const ALWAYS    = 1;
  public const IF_EXISTS = 2;
  public const NOT_VALID = 3;
  public const REPLICA   = 4;
}
