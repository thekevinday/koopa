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
class c_base_query_string {
  public const OWNER_TO     = 'owner to';
  public const USER_CURRENT = 'current_user';
  public const USER_SESSION = 'session_user';

  public const PUBLIC = 'public';
  public const ROLE   = 'role';
  public const GROUP  = 'group';

  public const REFRESH_VERSION = 'refresh version';

  public const SET_SCHEMA = 'set schema';
  public const RENAME_TO  = 'rename to';

  public const ORDER_BY = 'order by';
  public const GROUP_BY = 'group by';

  public const IN   = 'in';
  public const WITH = 'with';
  public const TO   = 'to';
  public const FOR  = 'for';
  public const ALL  = 'all';

  public const VARIADIC = 'variadic';

  public const SET       = 'set';
  public const RESET     = 'reset';
  public const RESET_ALL = 'reset all';

  public const ASCEND  = 'asc';
  public const DESCEND = 'desc';

  public const GRANT  = 'grant';
  public const REVOKE = 'revoke';

  public const WITH_GRANT_OPTION = 'with grant option';
  public const GRANT_OPTION_FOR  = 'grant option for';

  public const ON_TABLES_TO = 'on tables to';
  public const ON_SEQUENCES = 'on sequences';
  public const ON_FUNCTIONS = 'on functons';
  public const ON_TYPES     = 'on types';
  public const ON_SCHEMAS   = 'on schemas';

  public const SELECT     = 'select';
  public const INSERT     = 'insert';
  public const UPDATE     = 'update';
  public const DELETE     = 'delete';
  public const TRUNCATE   = 'truncate';
  public const REFERENCES = 'references';
  public const TRIGGER    = 'trigger';
  public const USAGE      = 'usage';
  public const EXECUTE    = 'execute';
  public const CREATE     = 'create';

  public const TRUE  = 'true';
  public const FALSE = 'false';

  public const ALLOW_CONNECTIONS = 'allow_connections';
  public const CONNECTION_LIMIT  = 'connection limit';
  public const IS_TEMPLATE       = 'is_template';

  public const SET_TABLESPACE = 'set tablespace';
  public const FROM_CURRENT   = 'from current';

  public const CASCADE  = 'cascade';
  public const RESTRICT = 'restrict';
}
