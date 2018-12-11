<?php
/**
 * @file
 * Provides classes for storing constant strings for generating specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * A collection of strings used for generating SQL.
 */
class c_database_string {
  public const ACCESS_METHOD                         = 'access method';
  public const ADD                                   = 'add';
  public const AGGREGATE                             = 'aggregate';
  public const ALL                                   = 'all';
  public const ALLOW_CONNECTIONS                     = 'allow_connections';
  public const AS                                    = 'as';
  public const ASCEND                                = 'asc';
  public const AUTOSUMMARIZE                         = 'autosummarize';
  public const AUTOVACUUM_ANALYZE_SCALE_FACTOR       = 'autovacuum_analyze_scale_factor';
  public const AUTOVACUUM_ANALYZE_THRESHOLD          = 'autovacuum_analyze_threshold';
  public const AUTOVACUUM_COST_DELAY                 = 'autovacuum_cost_delay';
  public const AUTOVACUUM_COST_LIMIT                 = 'autovacuum_cost_limit';
  public const AUTOVACUUM_ENABLED                    = 'autovacuum_enabled';
  public const AUTOVACUUM_FREEZE_MIN_AGE             = 'autovacuum_freeze_min_age';
  public const AUTOVACUUM_FREEZE_MAX_AGE             = 'autovacuum_freeze_max_age';
  public const AUTOVACUUM_FREEZE_TABLE_AGE           = 'autovacuum_freeze_table_age';
  public const AUTOVACUUM_MULTIXACT_FREEZE_MIN_AGE   = 'autovacuum_multixact_freeze_min_age';
  public const AUTOVACUUM_MULTIXACT_FREEZE_TABLE_AGE = 'autovacuum_multixact_freeze_table_age';
  public const AUTOVACUUM_SCALE_FACTOR               = 'autovacuum_scale_factor';
  public const AUTOVACUUM_VACUUM_THRESHOLD           = 'autovacuum_vacuum_threshold';
  public const BUFFERING                             = 'buffering';
  public const CALLED_ON_NULL_INPUT                  = 'called on null input';
  public const CASCADE                               = 'cascade';
  public const CAST                                  = 'cast';
  public const COLLATION                             = 'collation';
  public const CONNECTION_LIMIT                      = 'connection limit';
  public const CONVERSION                            = 'conversion';
  public const COST                                  = 'cost';
  public const CREATE                                = 'create';
  public const DEFAULT                               = 'default';
  public const DELETE                                = 'delete';
  public const DEPENDS_ON_EXTENSION                  = 'depends on extension';
  public const DESCEND                               = 'desc';
  public const DISABLE_TRIGGER                       = 'disable trigger';
  public const DOMAIN                                = 'domain';
  public const DROP                                  = 'drop';
  public const DROP_CONSTRAINT                       = 'drop constraint';
  public const DROP_DEFAULT                          = 'drop default';
  public const ENABLE_ALWAYS_TRIGGER                 = 'enable always trigger';
  public const ENABLE_REPLICA_TRIGGER                = 'enable replica trigger';
  public const ENABLE_TRIGGER                        = 'enable trigger';
  public const EVENT_TRIGGER                         = 'event trigger';
  public const EXECUTE                               = 'execute';
  public const EXTERNAL                              = 'external';
  public const FALSE                                 = 'false';
  public const FAST_UPDATE                           = 'fastupdate';
  public const FILL_FACTOR                           = 'fillfactor';
  public const FOREIGN_DATA_WRAPPER                  = 'foreign data wrapper';
  public const FOREIGN_TABLE                         = 'foreign table';
  public const FOR                                   = 'for';
  public const FOR_ROLE                              = 'for role';
  public const FROM_CURRENT                          = 'from current';
  public const FUNCTION                              = 'function';
  public const GRANT                                 = 'grant';
  public const GRANT_OPTION_FOR                      = 'grant option for';
  public const GROUP                                 = 'group';
  public const GROUP_BY                              = 'group by';
  public const GIN_PENDING_LIST_LIMIT                = 'gin_pending_list_limit';
  public const HANDLER                               = 'handler';
  public const IF_EXISTS                             = 'if exists';
  public const IMMUTABLE                             = 'immutable';
  public const IN                                    = 'in';
  public const INOUT                                 = 'inout';
  public const IN_SCHEMA                             = 'in schema';
  public const INHERIT                               = 'inherit';
  public const INSERT                                = 'insert';
  public const IS_TEMPLATE                           = 'is_template';
  public const LANGUAGE                              = 'language';
  public const LEAKPROOF                             = 'leakproof';
  public const LOG_AUTOVACUUM_MIN_DURATION           = 'log_autovacuum_min_duration';
  public const MATERIALIZED_VIEW                     = 'materialized view';
  public const NO_HANDLER                            = 'no handler';
  public const NO_INHERIT                            = 'no inherit';
  public const NO_VALIDATOR                          = 'no validator';
  public const NO_WAIT                               = 'no wait';
  public const NOT_LEAKPROOF                         = 'not leakproof';
  public const NOT_NULL                              = 'not null';
  public const NOT_VALID                             = 'not valid';
  public const ON_FUNCTIONS                          = 'on functons';
  public const ON_SCHEMAS                            = 'on schemas';
  public const ON_SEQUENCES                          = 'on sequences';
  public const ON_TABLES_TO                          = 'on tables to';
  public const ON_TYPES                              = 'on types';
  public const ONLY                                  = 'only';
  public const OPERATOR_CLASS                        = 'operator class';
  public const OPERATOR_FAMILY                       = 'operator family';
  public const OPTIONS                               = 'options';
  public const ORDER_BY                              = 'order by';
  public const OUT                                   = 'out';
  public const OWNED_BY                              = 'owned by';
  public const OWNER_TO                              = 'owner to';
  public const PAGES_PER_RANGE                       = 'pages_per_range';
  public const PARALLEL                              = 'parallel';
  public const PARALLEL_WORKERS                      = 'parallel_workers';
  public const PROCEDURAL                            = 'procedural';
  public const PUBLIC                                = 'public';
  public const REFERENCES                            = 'references';
  public const REFRESH_VERSION                       = 'refresh version';
  public const RENAME_TO                             = 'rename to';
  public const RENAME_COLUMN                         = 'rename column';
  public const RENAME_CONSTRAINT                     = 'rename constraint';
  public const RESET                                 = 'reset';
  public const RESET_ALL                             = 'reset all';
  public const RESTRICT                              = 'restrict';
  public const RESTRICTED                            = 'restricted';
  public const RETURNS_NULL_ON_NULL_INPUT            = 'returns null on null input';
  public const REVOKE                                = 'revoke';
  public const ROLE                                  = 'role';
  public const ROWS                                  = 'rows';
  public const SAFE                                  = 'safe';
  public const SCHEMA                                = 'schema';
  public const SECURITY_DEFINER                      = 'security definer';
  public const SECURITY_INVOKER                      = 'security invoker';
  public const SELECT                                = 'select';
  public const SEQUENCE                              = 'sequence';
  public const SERVER                                = 'server';
  public const SET                                   = 'set';
  public const SET_DEFAULT                           = 'set default';
  public const SET_SCHEMA                            = 'set schema';
  public const SET_TABLESPACE                        = 'set tablespace';
  public const SET_WITH_OIDS                         = 'set with oids';
  public const SET_WITHOUT_OIDS                      = 'set without oids';
  public const STABLE                                = 'stable';
  public const STRICT                                = 'strict';
  public const TABLE                                 = 'table';
  public const TEXT_SEARCH_CONFIGURATION             = 'text search configuration';
  public const TEXT_SEARCH_DICTIONARY                = 'text search dictionary';
  public const TEXT_SEARCH_PARSER                    = 'text search parser';
  public const TEXT_SEARCH_TEMPLATE                  = 'text search template';
  public const TRANSFORM_FOR                         = 'transform for';
  public const TRIGGER                               = 'trigger';
  public const TRUE                                  = 'true';
  public const TRUNCATE                              = 'truncate';
  public const TYPE                                  = 'type';
  public const TO                                    = 'to';
  public const UNSAFE                                = 'unsafe';
  public const UPDATE                                = 'update';
  public const USAGE                                 = 'usage';
  public const USER                                  = 'user';
  public const USER_CATALOG_TABLE                    = 'user_catalog_table';
  public const USER_CURRENT                          = 'current_user';
  public const USER_SESSION                          = 'session_user';
  public const USING                                 = 'using';
  public const VALIDATOR                             = 'validator';
  public const VALIDATE_CONSTRAINT                   = 'validate constraint';
  public const VARIADIC                              = 'variadic';
  public const VOLATILE                              = 'volatile';
  public const VIEW                                  = 'view';
  public const WITH                                  = 'with';
  public const WITH_GRANT_OPTION                     = 'with grant option';
}
