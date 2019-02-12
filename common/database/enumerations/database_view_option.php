<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql view option information.
 */
class e_database_view_option {
  public const NONE             = 0;
  public const CHECK_OPTION     = 1;
  public const SECURITY_BARRIER = 2;
}
