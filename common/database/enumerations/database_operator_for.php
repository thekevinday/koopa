<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with operator add/drop for and related queries.
 */
class e_database_operator_for {
  public const NONE         = 0;
  public const FOR_ORDER_BY = 1;
  public const FOR_SEARCH   = 2;
}
