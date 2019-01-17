<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database actions.
 */
class e_database_alter_column {
  public const NONE           = 0;
  public const DROP           = 1;
  public const DROP_DEFAULT   = 2;
  public const SET            = 3;
  public const SET_DATA       = 4;
  public const SET_DEFAULT    = 5;
  public const SET_STATISTICS = 6;
  public const SET_STORAGE    = 7;
}
