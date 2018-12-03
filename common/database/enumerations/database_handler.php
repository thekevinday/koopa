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
class e_database_handler {
  public const NONE       = 0;
  public const HANDLER    = 1;
  public const NO_HANDLER = 2;
}
