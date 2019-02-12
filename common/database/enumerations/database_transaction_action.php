<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql transaction action information.
 */
class e_database_transaction_action {
  public const NONE        = 0;
  public const TRANSACTION = 1;
  public const WORK        = 2;
}
