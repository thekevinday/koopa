<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database privileges.
 */
class e_database_privilege {
  public const NONE       = 0;
  public const ALL        = 1;
  public const CREATE     = 2;
  public const DELETE     = 3;
  public const EXECUTE    = 4;
  public const INSERT     = 5;
  public const REFERENCES = 6;
  public const SELECT     = 7;
  public const TRIGGER    = 8;
  public const TRUNCATE   = 9;
  public const UPDATE     = 10;
  public const USAGE      = 11;
}
