<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with index publication parameter and related queries.
 */
class e_database_publication_parameter {
  public const NONE    = 0;
  public const PUBLISH = 1;
}
