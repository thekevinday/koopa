<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with postgresql function action information.
 */
class e_database_function_action {
  public const NONE                       = 0;
  public const CALLED_ON_NULL_INPUT       = 1;
  public const IMMUTABLE                  = 2;
  public const LEAKPROOF                  = 3;
  public const NOT_LEAKPROOF              = 4;
  public const PARALLEL_RESTRICTED        = 5;
  public const PARALLEL_SAFE              = 6;
  public const PARALLEL_UNSAFE            = 7;
  public const RESET                      = 8;
  public const RESET_ALL                  = 9;
  public const RETURNS_NULL_ON_NULL_INPUT = 10;
  public const SECURITY_DEFINER           = 11;
  public const SECURITY_INVOKER           = 12;
  public const SET_EQUAL                  = 13;
  public const SET_FROM                   = 14;
  public const SET_TO                     = 15;
  public const STABLE                     = 16;
  public const STRICT                     = 17;
  public const VOLATILE                   = 18;
}
