<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database set storage.
 */
class e_database_set_storage {
  public const NONE     = 0;
  public const EXTENDED = 1;
  public const EXTERNAL = 2;
  public const MAIN     = 3;
  public const PLAIN    = 4;
}
