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
  public const ADD       = 1;
  public const ALWAYS    = 2;
  public const DROP      = 3;
  public const IF_EXISTS = 4;
  public const NOT_VALID = 5;
  public const REPLICA   = 6;
  public const SET       = 7;
}
