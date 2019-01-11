<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql role option information.
 */
class e_database_role_option {
  public const NONE               = 0;
  public const BYPASSRLS          = 1;
  public const CONNECTION_LIMIT   = 2;
  public const CREATEDB           = 3;
  public const CREATEROLE         = 4;
  public const INHERIT            = 5;
  public const LOGIN              = 6;
  public const NOBYPASSRLS        = 7;
  public const NOCREATEDB         = 8;
  public const NOCREATEROLE       = 9;
  public const NOINHERIT          = 10;
  public const NOLOGIN            = 11;
  public const NOREPLICATION      = 12;
  public const NOSUPERUSER        = 13;
  public const PASSWORD           = 14;
  public const PASSWORD_ENCRYPTED = 15;
  public const REPLICATION        = 16;
  public const SUPERUSER          = 17;
  public const VALIDUNTIL         = 18;
}
