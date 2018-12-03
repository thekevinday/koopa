<?php
/**
 * @file
 * Provides enumeration classes for member object methods used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with database actions.
 */
class e_database_member_object {
  public const NONE                      = 0;
  public const ACCESS_METHOD             = 1;
  public const AGGREGATE                 = 2;
  public const CAST                      = 3;
  public const COLLATION                 = 4;
  public const CONVERSION                = 5;
  public const DOMAIN                    = 6;
  public const EVENT_TRIGGER             = 7;
  public const FOREIGN_DATA_WRAPPER      = 8;
  public const FOREIGN_TABLE             = 9;
  public const FUNCTION                  = 10;
  public const MATERIALIZED_VIEW         = 11;
  public const OPERATOR_CLASS            = 12;
  public const OPERATOR_FAMILY           = 13;
  public const SCHEMA                    = 14;
  public const SEQUENCE                  = 14;
  public const SERVER                    = 14;
  public const TABLE                     = 14;
  public const TEXT_SEARCH_CONFIGURATION = 14;
  public const TEXT_SEARCH_DICTIONARY    = 14;
  public const TEXT_SEARCH_PARSER        = 14;
  public const TEXT_SEARCH_TEMPLATE      = 14;
  public const TRANSFORM_FOR             = 14;
  public const TYPE                      = 14;
  public const VIEW                      = 14;
}
