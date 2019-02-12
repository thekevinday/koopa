<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with SET/DROP DEFAULT for columns and related queries.
 */
class e_database_set_column_default {
  public const NONE = 0;
  public const DROP = 1;
  public const SET  = 2;
}
