<?php
/**
 * @file
 * Provides enumeration classes for managing codes used for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * Codes associated with index storage_parameter and related queries.
 */
class e_database_storage_parameter {
  public const NONE                                  = 0;
  public const AUTOSUMMARIZE                         = 1;
  public const AUTOVACUUM_ANALYZE_SCALE_FACTOR       = 2;
  public const AUTOVACUUM_ANALYZE_THRESHOLD          = 3;
  public const AUTOVACUUM_COST_DELAY                 = 4;
  public const AUTOVACUUM_COST_LIMIT                 = 5;
  public const AUTOVACUUM_ENABLED                    = 6;
  public const AUTOVACUUM_FREEZE_MIN_AGE             = 7;
  public const AUTOVACUUM_FREEZE_MAX_AGE             = 8;
  public const AUTOVACUUM_FREEZE_TABLE_AGE           = 9;
  public const AUTOVACUUM_MULTIXACT_FREEZE_MIN_AGE   = 10;
  public const AUTOVACUUM_MULTIXACT_FREEZE_TABLE_AGE = 11;
  public const AUTOVACUUM_SCALE_FACTOR               = 12;
  public const AUTOVACUUM_VACUUM_THRESHOLD           = 13;
  public const BUFFERING                             = 14;
  public const FAST_UPDATE                           = 15;
  public const FILL_FACTOR                           = 16;
  public const GIN_PENDING_LIST_LIMIT                = 17;
  public const LOG_AUTOVACUUM_MIN_DURATION           = 18;
  public const PAGES_PER_RANGE                       = 19;
  public const PARALLEL_WORKERS                      = 20;
  public const USER_CATALOG_TABLE                    = 21;
}
