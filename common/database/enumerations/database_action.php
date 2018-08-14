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
class e_database_action {
  public const NONE                = 0;
  public const ADD                 = 1;
  public const DISABLE             = 2;
  public const DROP                = 3;
  public const DROP_CONSTRAINT     = 4;
  public const DROP_DEFAULT        = 5;
  public const ENABLE              = 6;
  public const GRANT               = 7;
  public const OWNER_TO            = 8;
  public const RENAME_CONSTRAINT   = 9;
  public const RENAME_TO           = 10;
  public const SET                 = 11;
  public const SET_DEFAULT         = 12;
  public const SET_SCHEMA          = 13;
  public const VALIDATE_CONSTRAINT = 14;
}
