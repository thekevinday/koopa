/** Standardized SQL Structure - Logs - Types */
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/*** provide log type id and names ***/
create table managers.t_log_types (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cp_log_types_id primary key (id),

  constraint cu_log_types_user unique (name_machine),

  constraint cc_log_types_id check (id >= 0)
);

create sequence managers.s_log_types_id owned by managers.t_log_types.id;
alter table managers.t_log_types alter column id set default nextval('managers.s_log_types_id'::regclass);

grant select,insert,update on managers.t_log_types to reservation_users_administer;
grant select on managers.t_log_types to reservation_users_manager;
grant select on managers.t_log_types to reservation_users_auditor;
grant select,usage on managers.s_log_types_id to reservation_users_administer;

create index ci_log_types_deleted_not on managers.t_log_types (id)
  where is_deleted is not true;

create index ci_log_types_public on managers.t_log_types (id)
  where is_deleted is not true and is_locked is not true;

create view public.v_log_types with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_log_types
  where is_deleted is not true and is_locked is not true;

grant select on public.v_log_types to reservation_users;
grant select on public.v_log_types to public_users;


/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence managers.s_log_types_id start 1000;
alter sequence managers.s_log_types_id restart;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into managers.t_log_types (id, name_machine, name_human) values (0, 'none', 'None');
insert into managers.t_log_types (id, name_machine, name_human) values (1, 'php', 'PHP');
insert into managers.t_log_types (id, name_machine, name_human) values (2, 'theme', 'Theme');
insert into managers.t_log_types (id, name_machine, name_human) values (3, 'cache', 'Cache');
insert into managers.t_log_types (id, name_machine, name_human) values (4, 'javascript', 'Javascript');
insert into managers.t_log_types (id, name_machine, name_human) values (5, 'ajax', 'AJAX');
insert into managers.t_log_types (id, name_machine, name_human) values (6, 'sql', 'SQL');
insert into managers.t_log_types (id, name_machine, name_human) values (7, 'form', 'Form');
insert into managers.t_log_types (id, name_machine, name_human) values (8, 'form_validate', 'Form Validation');
insert into managers.t_log_types (id, name_machine, name_human) values (9, 'form_submit', 'Form Submit');
insert into managers.t_log_types (id, name_machine, name_human) values (10, 'load', 'Load');
insert into managers.t_log_types (id, name_machine, name_human) values (11, 'save', 'Save');
insert into managers.t_log_types (id, name_machine, name_human) values (12, 'render', 'Render');
insert into managers.t_log_types (id, name_machine, name_human) values (13, 'client', 'Client');
insert into managers.t_log_types (id, name_machine, name_human) values (14, 'add', 'Add');
insert into managers.t_log_types (id, name_machine, name_human) values (15, 'create', 'Create');
insert into managers.t_log_types (id, name_machine, name_human) values (16, 'change', 'Change');
insert into managers.t_log_types (id, name_machine, name_human) values (17, 'delete', 'Delete');
insert into managers.t_log_types (id, name_machine, name_human) values (18, 'redirect', 'Redirect');
insert into managers.t_log_types (id, name_machine, name_human) values (19, 'login', 'Login');
insert into managers.t_log_types (id, name_machine, name_human) values (20, 'logout', 'Logout');
insert into managers.t_log_types (id, name_machine, name_human) values (21, 'session', 'Session');
insert into managers.t_log_types (id, name_machine, name_human) values (22, 'database', 'Database');
insert into managers.t_log_types (id, name_machine, name_human) values (23, 'not_found', 'Not Found');
insert into managers.t_log_types (id, name_machine, name_human) values (24, 'access_denied', 'Access Denied');
insert into managers.t_log_types (id, name_machine, name_human) values (25, 'removed', 'Removed');
insert into managers.t_log_types (id, name_machine, name_human) values (26, 'locked', 'Locked');
insert into managers.t_log_types (id, name_machine, name_human) values (27, 'timeout', 'Timeout');
insert into managers.t_log_types (id, name_machine, name_human) values (28, 'expire', 'Expiration');
insert into managers.t_log_types (id, name_machine, name_human) values (29, 'user', 'User');
insert into managers.t_log_types (id, name_machine, name_human) values (30, 'error', 'Error');
insert into managers.t_log_types (id, name_machine, name_human) values (31, 'content', 'Content');
insert into managers.t_log_types (id, name_machine, name_human) values (32, 'workflow', 'Workflow');
insert into managers.t_log_types (id, name_machine, name_human) values (33, 'draft', 'Draft');
insert into managers.t_log_types (id, name_machine, name_human) values (34, 'clone', 'Clone');
insert into managers.t_log_types (id, name_machine, name_human) values (35, 'publish', 'Publish');
insert into managers.t_log_types (id, name_machine, name_human) values (36, 'revert', 'Revert');
insert into managers.t_log_types (id, name_machine, name_human) values (37, 'validate', 'Validate');
insert into managers.t_log_types (id, name_machine, name_human) values (38, 'approve', 'Approve');
insert into managers.t_log_types (id, name_machine, name_human) values (39, 'password', 'Password');
insert into managers.t_log_types (id, name_machine, name_human) values (40, 'revision', 'Revision');
insert into managers.t_log_types (id, name_machine, name_human) values (41, 'search', 'Search');
insert into managers.t_log_types (id, name_machine, name_human) values (42, 'access', 'Access');
insert into managers.t_log_types (id, name_machine, name_human) values (43, 'unknown', 'Unknown');



/*** provide HTTP status codes ***/
create table managers.t_log_http_status_codes (
  id smallint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cp_log_http_status_codes_id primary key (id),

  constraint cu_log_http_status_codes_user unique (name_machine),

  constraint cc_log_http_status_codes_id check (id >= 0 and id < 600)
);

create sequence managers.s_log_http_status_codes_id owned by managers.t_log_http_status_codes.id;
alter table managers.t_log_http_status_codes alter column id set default nextval('managers.s_log_http_status_codes_id'::regclass);

grant select,insert,update on managers.t_log_http_status_codes to reservation_users_administer;
grant select on managers.t_log_http_status_codes to reservation_users_manager;
grant select on managers.t_log_http_status_codes to reservation_users_auditor;
grant select,usage on managers.s_log_http_status_codes_id to reservation_users_administer;

create view public.v_log_http_status_codes with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_log_http_status_codes;

grant select on public.v_log_http_status_codes to reservation_users;
grant select on public.v_log_http_status_codes to public_users;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (0, '0', 'Undefined');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (1, '1', 'Invalid');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (2, '2', 'Unknown');

insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (100, '100', 'Continue');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (101, '101', 'Switching Protocols');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (102, '102', 'Processing');

insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (200, '200', 'OK');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (201, '201', 'Created');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (202, '202', 'Accepted');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (203, '203', 'Non-Authoritative Information');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (204, '204', 'No Content');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (205, '205', 'Reset Content');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (206, '206', 'Partial Content');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (207, '207', 'Multi-Status');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (208, '208', 'Already Reported');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (226, '226', 'IM used');

insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (300, '300', 'Multiple Choices');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (301, '301', 'Moved Permanently');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (302, '302', 'Found');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (303, '303', 'See Other');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (304, '304', 'Not Modified');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (305, '305', 'Use Proxy');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (306, '306', 'Switch Proxy');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (307, '307', 'Temporary Redirect');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (308, '308', 'Permanent Redirect');

insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (400, '400', 'Bad Request');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (401, '401', 'Unauthorized');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (402, '402', 'Payment Required');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (403, '403', 'Forbidden');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (404, '404', 'Not Found');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (405, '405', 'Method Not Allowed');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (406, '406', 'Not Acceptable');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (407, '407', 'Proxy Authentication Required');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (408, '408', 'Request Timeout');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (409, '409', 'Conflict');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (410, '410', 'Gone');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (411, '411', 'Length Required');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (412, '412', 'Precondition Failed');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (413, '413', 'Payload Too Large');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (414, '414', 'Request-URI Too Long');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (415, '415', 'Unsupported Media Type');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (416, '416', 'Requested Range Not Satisfiable');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (417, '417', 'Expectation Failed');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (422, '422', 'Misdirected Request');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (423, '423', 'Locked');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (424, '424', 'Failed Dependency');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (426, '426', 'Upgrade Required');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (428, '428', 'Precondition Required');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (429, '429', 'Too Many Requests');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (431, '431', 'Request Header Fields Too Large');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (451, '451', 'Unavailable For Legal Reasons');

insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (500, '500', 'Internal Server Error');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (501, '501', 'Not Implemented');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (502, '502', 'Bad Gateway');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (503, '503', 'Service Unavailable');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (504, '504', 'Gateway Timeout');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (505, '505', 'HTTP Version Not Supported');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (506, '506', 'Variant Also Negotiates');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (507, '507', 'Insufficient Storage');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (508, '508', 'Loop Detected');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (510, '510', 'Not Extended');
insert into managers.t_log_http_status_codes (id, name_machine, name_human) values (511, '511', 'Network Authentication Required');



/*** provide log severity level id and names ***/
create table managers.t_log_severity_levels (
  id bigint not null,
  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cp_log_severity_levels_id primary key (id),

  constraint cu_log_severity_levels_user unique (name_machine),

  constraint cc_log_severity_levels_id check (id >= 0)
);

create sequence managers.s_log_severity_levels_id owned by managers.t_log_severity_levels.id;
alter table managers.t_log_severity_levels alter column id set default nextval('managers.s_log_severity_levels_id'::regclass);

grant select,insert,update on managers.t_log_severity_levels to reservation_users_administer;
grant select on managers.t_log_severity_levels to reservation_users_manager;
grant select on managers.t_log_severity_levels to reservation_users_auditor;
grant select,usage on managers.s_log_severity_levels_id to reservation_users_administer;

create view users.v_log_severity_levels with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_log_severity_levels
  where is_deleted is false;

grant select on users.v_log_severity_levels to reservation_users;
grant select on users.v_log_severity_levels to public_users;


/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence managers.s_log_severity_levels_id start 1000;
alter sequence managers.s_log_severity_levels_id restart;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (0, 'none', 'None');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (1, 'information', 'Information');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (2, 'notice', 'Notice');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (3, 'debug', 'Debug');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (4, 'warning', 'Warning');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (5, 'error', 'Error');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (6, 'alert', 'Alert');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (7, 'critical', 'Critical');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (8, 'emergency', 'Emergency');



commit transaction;
