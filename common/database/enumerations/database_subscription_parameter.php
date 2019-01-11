<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with subscription parameter and related queries.
 */
class e_database_subscription_parameter {
  public const NONE               = 0;
  public const CONNECT            = 1;
  public const COPY_DATA          = 2;
  public const CREATE_SLOT        = 3;
  public const ENABLED            = 4;
  public const SLOT_NAME          = 5;
  public const SYNCHRONOUS_COMMIT = 6;
}
