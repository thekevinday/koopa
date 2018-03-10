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
static class c_base_query_string {
  public const OWNER_TO     = 'owner to';
  public const USER_CURRENT = 'current_user';
  public const USER_SESSION = 'session_user';

  public const REFRESH_VERSION = 'refresh version';

  public const SET_SCHEMA = 'set schema';
  public const RENAME_TO  = 'rename to';

  public const ORDER_BY = 'order by';
  public const GROUP_BY = 'group by';

  public const IN   = 'in';
  public const WITH = 'with';
  public const TO   = 'to';

  public const VARIADIC = 'variadic';

  public const SET       = 'set';
  public const RESET     = 'reset';
  public const RESET_ALL = 'reset all';

  public const ASCEND  = 'asc';
  public const DESCEND = 'desc';

  public const TRUE  = 'true';
  public const FALSE = 'false';

  public const ALLOW_CONNECTIONS = 'allow_connections';
  public const CONNECTION_LIMIT  = 'connection limit';
  public const IS_TEMPLATE       = 'is_template';

  public const SET_TABLESPACE = 'set tablespace';
  public const FROM_CURRENT   = 'from current';
}
