<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with REPLICA IDENTITY, and related queries.
 */
class e_database_replica_identity {
  public const NONE        = 0;
  public const DEFAULT     = 1;
  public const FULL        = 2;
  public const NOTHING     = 3;
  public const USING_INDEX = 4;
}
