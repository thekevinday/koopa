<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with (operator) SET and related queries.
 */
class e_database_set_operator {
  public const NONE     = 0;
  public const JOIN     = 1;
  public const RESTRICT = 2;
}
