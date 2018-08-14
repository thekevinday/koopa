<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with SET and related queries.
 */
class e_database_set {
  public const NONE         = 0;
  public const TO           = 1;
  public const EQUAL        = 2;
  public const FROM_CURRENT = 3;
}
