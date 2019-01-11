<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with index publication value and related queries.
 */
class e_database_publication_value {
  public const NONE   = 0;
  public const DELETE = 1;
  public const INSERT = 2;
  public const UPDATE = 3;
}
