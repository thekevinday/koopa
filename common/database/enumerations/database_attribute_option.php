<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database attribute option.
 */
class e_database_attribute_option {
  public const NONE                 = 0;
  public const N_DISTINCT           = 1;
  public const N_DISTINCT_INHERITED = 2;
}
