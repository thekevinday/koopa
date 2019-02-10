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
class e_database_action_deprecated {
  public const NONE                = 0;
  public const ADD                 = 1;
  public const DISABLE             = 2;
  public const DROP                = 3;
  public const DROP_CONSTRAINT     = 4;
  public const DROP_DEFAULT        = 5;
  public const ENABLE              = 6;
  public const GRANT               = 7;
  public const HANDLER             = 8;
  public const NO_HANDLER          = 9;
  public const OPTIONS             = 10;
  public const OWNER_TO            = 11;
  public const RENAME_CONSTRAINT   = 12;
  public const RENAME_TO           = 13;
  public const SET                 = 14;
  public const SET_DEFAULT         = 15;
  public const SET_SCHEMA          = 16;
  public const VALIDATE_CONSTRAINT = 17;
  public const VALIDATOR           = 18;
  public const NO_VALIDATOR        = 19;
}
