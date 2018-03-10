<?php
/**
 * @file
 * Provides classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql user/session information.
 */
static class c_base_query_code_user {
  public const NONE    = 0;
  public const CURRENT = 1;
  public const SESSION = 2;
  public const NAME    = 3;
}

/**
 * Codes associated with GROUP BY, ORDER BY, and related queries.
 */
static class c_base_query_code_direction {
  public const NONE    = 0;
  public const ASCEND  = 1;
  public const DESCEND = 2;
}

/**
 * Codes associated with SET and related queries.
 */
static class c_base_query_code_set {
  public const NONE         = 0;
  public const TO           = 1;
  public const EQUAL        = 2;
  public const FROM_CURRENT = 3;
}

/**
 * Codes associated with RESET and related queries.
 */
static class c_base_query_code_reset {
  public const NONE      = 0;
  public const ALL       = 1;
  public const PARAMETER = 2;
}
