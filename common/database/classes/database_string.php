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
  public const ADD                 = 'add';
  public const ALL                 = 'all';
  public const ALLOW_CONNECTIONS   = 'allow_connections';
  public const ASCEND              = 'asc';
  public const CASCADE             = 'cascade';
  public const CONNECTION_LIMIT    = 'connection limit';
  public const CREATE              = 'create';
  public const DELETE              = 'delete';
  public const DESCEND             = 'desc';
  public const DROP                = 'drop';
  public const DROP_CONSTRAINT     = 'drop constraint';
  public const DROP_DEFAULT        = 'drop default';
  public const EXECUTE             = 'execute';
  public const FALSE               = 'false';
  public const FOR                 = 'for';
  public const FROM_CURRENT        = 'from current';
  public const GRANT               = 'grant';
  public const GRANT_OPTION_FOR    = 'grant option for';
  public const GROUP               = 'group';
  public const GROUP_BY            = 'group by';
  public const IF_EXISTS           = 'if exists';
  public const IN                  = 'in';
  public const INSERT              = 'insert';
  public const IS_TEMPLATE         = 'is_template';
  public const NOT_NULL            = 'not null';
  public const NOT_VALID           = 'not valid';
  public const ORDER_BY            = 'order by';
  public const OWNER_TO            = 'owner to';
  public const ON_FUNCTIONS        = 'on functons';
  public const ON_SCHEMAS          = 'on schemas';
  public const ON_SEQUENCES        = 'on sequences';
  public const ON_TABLES_TO        = 'on tables to';
  public const ON_TYPES            = 'on types';
  public const PUBLIC              = 'public';
  public const REFERENCES          = 'references';
  public const REFRESH_VERSION     = 'refresh version';
  public const RENAME_TO           = 'rename to';
  public const RENAME_CONSTRAINT   = 'rename constraint';
  public const RESET               = 'reset';
  public const RESET_ALL           = 'reset all';
  public const RESTRICT            = 'restrict';
  public const REVOKE              = 'revoke';
  public const ROLE                = 'role';
  public const SELECT              = 'select';
  public const SET                 = 'set';
  public const SET_DEFAULT         = 'set default';
  public const SET_SCHEMA          = 'set schema';
  public const SET_TABLESPACE      = 'set tablespace';
  public const TRIGGER             = 'trigger';
  public const TRUE                = 'true';
  public const TRUNCATE            = 'truncate';
  public const TO                  = 'to';
  public const UPDATE              = 'update';
  public const USAGE               = 'usage';
  public const USER_CURRENT        = 'current_user';
  public const USER_SESSION        = 'session_user';
  public const VALIDATE_CONSTRAINT = 'validate constraint';
  public const VARIADIC            = 'variadic';
  public const WITH                = 'with';
  public const WITH_GRANT_OPTION   = 'with grant option';
}
