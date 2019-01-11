<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with operator family add/dop and related queries.
 */
class e_database_operator_family {
  public const NONE     = 0;
  public const FUNCTION = 1;
  public const OPERATOR = 2;
}
