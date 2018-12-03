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
  public const ACCESS_METHOD              = 'access method';
  public const ADD                        = 'add';
  public const AGGREGATE                  = 'aggregate';
  public const ALL                        = 'all';
  public const ALLOW_CONNECTIONS          = 'allow_connections';
  public const AS                         = 'as';
  public const ASCEND                     = 'asc';
  public const CASCADE                    = 'cascade';
  public const CAST                       = 'cast';
  public const COLLATION                  = 'collation';
  public const CONNECTION_LIMIT           = 'connection limit';
  public const CONVERSION                 = 'conversion';
  public const CREATE                     = 'create';
  public const DELETE                     = 'delete';
  public const DESCEND                    = 'desc';
  public const DOMAIN                     = 'domain';
  public const DROP                       = 'drop';
  public const DROP_CONSTRAINT            = 'drop constraint';
  public const DROP_DEFAULT               = 'drop default';
  public const EVENT_TRIGGER              = 'event trigger';
  public const EXECUTE                    = 'execute';
  public const FALSE                      = 'false';
  public const FOREIGN_DATA_WRAPPER       = 'foreign data wrapper';
  public const FOREIGN_TABLE              = 'foreign table';
  public const FOR                        = 'for';
  public const FROM_CURRENT               = 'from current';
  public const FUNCTION                   = 'function';
  public const GRANT                      = 'grant';
  public const GRANT_OPTION_FOR           = 'grant option for';
  public const GROUP                      = 'group';
  public const GROUP_BY                   = 'group by';
  public const HANDLER                    = 'handler';
  public const IF_EXISTS                  = 'if exists';
  public const IN                         = 'in';
  public const INSERT                     = 'insert';
  public const IS_TEMPLATE                = 'is_template';
  public const LANGUAGE                   = 'language';
  public const MATERIALIZED_VIEW          = 'materialized view';
  public const NO_HANDLER                 = 'no handler';
  public const NO_VALIDATOR               = 'no validator';
  public const NOT_NULL                   = 'not null';
  public const NOT_VALID                  = 'not valid';
  public const ON_FUNCTIONS               = 'on functons';
  public const ON_SCHEMAS                 = 'on schemas';
  public const ON_SEQUENCES               = 'on sequences';
  public const ON_TABLES_TO               = 'on tables to';
  public const ON_TYPES                   = 'on types';
  public const ONLY                       = 'only';
  public const OPERATOR_CLASS             = 'operator class';
  public const OPERATOR_FAMILY            = 'operator family';
  public const OPTIONS                    = 'options';
  public const ORDER_BY                   = 'order by';
  public const OWNER_TO                   = 'owner to';
  public const PROCEDURAL                 = 'procedural';
  public const PUBLIC                     = 'public';
  public const REFERENCES                 = 'references';
  public const REFRESH_VERSION            = 'refresh version';
  public const RENAME_TO                  = 'rename to';
  public const RENAME_CONSTRAINT          = 'rename constraint';
  public const RESET                      = 'reset';
  public const RESET_ALL                  = 'reset all';
  public const RESTRICT                   = 'restrict';
  public const REVOKE                     = 'revoke';
  public const ROLE                       = 'role';
  public const SCHEMA                     = 'schema';
  public const SELECT                     = 'select';
  public const SEQUENCE                   = 'sequence';
  public const SERVER                     = 'server';
  public const SET                        = 'set';
  public const SET_DEFAULT                = 'set default';
  public const SET_SCHEMA                 = 'set schema';
  public const SET_TABLESPACE             = 'set tablespace';
  public const TABLE                      = 'table';
  public const TEXT_SEARCH_CONFIGURATION  = 'text search configuration';
  public const TEXT_SEARCH_DICTIONARY     = 'text search dictionary';
  public const TEXT_SEARCH_PARSER         = 'text search parser';
  public const TEXT_SEARCH_TEMPLATE       = 'text search template';
  public const TRANSFORM_FOR              = 'transform for';
  public const TRIGGER                    = 'trigger';
  public const TRUE                       = 'true';
  public const TRUNCATE                   = 'truncate';
  public const TYPE                       = 'type';
  public const TO                         = 'to';
  public const UPDATE                     = 'update';
  public const USAGE                      = 'usage';
  public const USER_CURRENT               = 'current_user';
  public const USER_SESSION               = 'session_user';
  public const USING                      = 'using';
  public const VALIDATOR                  = 'validator';
  public const VALIDATE_CONSTRAINT        = 'validate constraint';
  public const VARIADIC                   = 'variadic';
  public const VIEW                       = 'view';
  public const WITH                       = 'with';
  public const WITH_GRANT_OPTION          = 'with grant option';
}
