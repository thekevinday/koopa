/** Standardized SQL Structure - Logs */
/** This depends on: base-users.sql **/
/** Logs will contain a uuid type so that different logs may be associated with each other as to being related. Each request will have a new uuid. Requests maintained by ajax will likely have new uuids on each request. PHP should generate the uuid. */
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/*** provide log type id and names ***/
create table managers.t_log_types (
  id bigint not null,
  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  date_created timestamp default localtimestamp not null,

  constraint cp_log_types_id primary key (id),

  constraint cu_log_types_user unique (name_machine),

  constraint cc_log_types_id check (id > 0)
);

create sequence managers.s_log_types_id owned by managers.t_log_types.id;
alter table managers.t_log_types alter column id set default nextval('managers.s_log_types_id'::regclass);

grant select,insert,update on managers.t_log_types to reservation_users_administer;
grant select on managers.t_log_types to reservation_users_manager;
grant select,usage on managers.s_log_types_id to reservation_users_administer;

create view public.v_log_types with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_log_types;

grant select on public.v_log_types to reservation_users;
grant select on public.v_log_types to public_users;


/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence managers.s_log_types_id start 1000;
alter sequence managers.s_log_types_id restart;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
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
insert into managers.t_log_types (id, name_machine, name_human) values (14, 'update', 'Update');
insert into managers.t_log_types (id, name_machine, name_human) values (15, 'delete', 'Delete');
insert into managers.t_log_types (id, name_machine, name_human) values (16, 'redirect', 'Redirect');
insert into managers.t_log_types (id, name_machine, name_human) values (17, 'login', 'Login');
insert into managers.t_log_types (id, name_machine, name_human) values (18, 'logout', 'Logout');
insert into managers.t_log_types (id, name_machine, name_human) values (19, 'session', 'Session');
insert into managers.t_log_types (id, name_machine, name_human) values (20, 'database', 'Database');
insert into managers.t_log_types (id, name_machine, name_human) values (21, 'not_found', 'Not Found');
insert into managers.t_log_types (id, name_machine, name_human) values (22, 'access_denied', 'Access Denied');
insert into managers.t_log_types (id, name_machine, name_human) values (23, 'removed', 'Removed');
insert into managers.t_log_types (id, name_machine, name_human) values (24, 'locked', 'Locked');
insert into managers.t_log_types (id, name_machine, name_human) values (25, 'timeout', 'Timeout');
insert into managers.t_log_types (id, name_machine, name_human) values (26, 'expire', 'Expiration');
insert into managers.t_log_types (id, name_machine, name_human) values (27, 'user', 'User');
insert into managers.t_log_types (id, name_machine, name_human) values (28, 'error', 'Error');
insert into managers.t_log_types (id, name_machine, name_human) values (29, 'content', 'Content');
insert into managers.t_log_types (id, name_machine, name_human) values (30, 'workflow', 'Workflow');
insert into managers.t_log_types (id, name_machine, name_human) values (31, 'draft', 'Draft');
insert into managers.t_log_types (id, name_machine, name_human) values (32, 'clone', 'Clone');
insert into managers.t_log_types (id, name_machine, name_human) values (33, 'publish', 'Publish');
insert into managers.t_log_types (id, name_machine, name_human) values (34, 'revert', 'Revert');
insert into managers.t_log_types (id, name_machine, name_human) values (35, 'validate', 'Validate');
insert into managers.t_log_types (id, name_machine, name_human) values (36, 'approve', 'Approve');
insert into managers.t_log_types (id, name_machine, name_human) values (37, 'password', 'Password');
insert into managers.t_log_types (id, name_machine, name_human) values (38, 'revision', 'Revision');
insert into managers.t_log_types (id, name_machine, name_human) values (39, 'search', 'Search');
insert into managers.t_log_types (id, name_machine, name_human) values (40, 'access', 'Access');
insert into managers.t_log_types (id, name_machine, name_human) values (41, 'unknown', 'Unknown');



/*** provide HTTP status codes ***/
create table managers.t_log_http_status_codes (
  id smallint not null,
  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  date_created timestamp default localtimestamp not null,

  constraint cp_log_http_status_codes_id primary key (id),

  constraint cu_log_http_status_codes_user unique (name_machine),

  constraint cc_log_http_status_codes_id check (id >= 0 and id < 600)
);

create sequence managers.s_log_http_status_codes_id owned by managers.t_log_http_status_codes.id;
alter table managers.t_log_http_status_codes alter column id set default nextval('managers.s_log_http_status_codes_id'::regclass);

grant select,insert,update on managers.t_log_http_status_codes to reservation_users_administer;
grant select on managers.t_log_http_status_codes to reservation_users_manager;
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

  date_created timestamp default localtimestamp not null,

  constraint cp_log_severity_levels_id primary key (id),

  constraint cu_log_severity_levels_user unique (name_machine),

  constraint cc_log_severity_levels_id check (id > 0)
);

create sequence managers.s_log_severity_levels_id owned by managers.t_log_severity_levels.id;
alter table managers.t_log_severity_levels alter column id set default nextval('managers.s_log_severity_levels_id'::regclass);

grant select,insert,update on managers.t_log_severity_levels to reservation_users_administer;
grant select on managers.t_log_severity_levels to reservation_users_manager;
grant select,usage on managers.s_log_severity_levels_id to reservation_users_administer;

create view public.v_log_severity_levels with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_log_severity_levels;

grant select on public.v_log_severity_levels to reservation_users;
grant select on public.v_log_severity_levels to public_users;


/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence managers.s_log_severity_levels_id start 1000;
alter sequence managers.s_log_severity_levels_id restart;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (1, 'information', 'Information');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (2, 'notice', 'Notice');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (3, 'debug', 'Debug');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (4, 'warning', 'Warning');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (5, 'error', 'Error');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (6, 'alert', 'Alert');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (7, 'critical', 'Critical');
insert into managers.t_log_severity_levels (id, name_machine, name_human) values (8, 'emergency', 'Emergency');



/*** provide user activity logging ***/
create table managers.t_log_users (
  id bigint not null,
  id_user bigint default 1 not null,
  id_uuid uuid not null,
  name_machine varchar(128) not null,
  log_title varchar(512) not null,
  log_type bigint not null,
  log_severity bigint not null,
  log_details json,
  log_date timestamp default localtimestamp not null,
  request_client public.ct_client not null,
  response_code smallint not null default 0,

  constraint cp_log_users_id primary key (id),

  constraint cc_log_users_id check (id > 0),
  constraint cc_log_users_log_severity check (log_severity > 0),

  constraint cf_log_users_id_user foreign key (id_user, name_machine) references administers.t_users (id, name_machine) on delete set null on update cascade,
  constraint cf_log_users_log_type foreign key (log_type) references managers.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_users_log_severity foreign key (log_severity) references managers.t_log_severity_levels (id) on delete restrict on update cascade,
  constraint cf_log_users_response_code foreign key (response_code) references managers.t_log_http_status_codes (id) on delete restrict on update cascade
);

create sequence managers.s_log_users_id owned by managers.t_log_users.id;
alter table managers.t_log_users alter column id set default nextval('managers.s_log_users_id'::regclass);

grant select on managers.t_log_users to reservation_users_administer;
grant select on managers.t_log_users to reservation_users_manager;
grant select,usage on managers.s_log_users_id to reservation_users_administer;
grant usage on managers.s_log_users_id to reservation_users;
grant usage on managers.s_log_users_id to public_users;

create index ci_log_users_type_php on managers.t_log_users (id)
  where log_type = 1;

create index ci_log_users_type_theme on managers.t_log_users (id)
  where log_type = 2;

create index ci_log_users_type_cache on managers.t_log_users (id)
  where log_type = 3;

create index ci_log_users_type_javascript on managers.t_log_users (id)
  where log_type = 4;

create index ci_log_users_type_ajax on managers.t_log_users (id)
  where log_type = 5;

create index ci_log_users_type_sql on managers.t_log_users (id)
  where log_type = 6;

create index ci_log_users_type_redirect on managers.t_log_users (id)
  where log_type = 16;

create index ci_log_users_type_login on managers.t_log_users (id)
  where log_type = 17;

create index ci_log_users_type_logout on managers.t_log_users (id)
  where log_type = 18;

create index ci_log_users_type_user on managers.t_log_users (id)
  where log_type = 27;

create index ci_log_users_type_error on managers.t_log_users (id)
  where log_type = 28;

create index ci_log_users_type_content on managers.t_log_users (id)
  where log_type = 29;

create index ci_log_users_type_workflow on managers.t_log_users (id)
  where log_type = 30;

create index ci_log_users_type_search on managers.t_log_users (id)
  where log_type = 39;

create index ci_log_users_response_code_200 on managers.t_log_users (id)
  where response_code = 200;

create index ci_log_users_response_code_403 on managers.t_log_users (id)
  where response_code = 403;

create index ci_log_users_response_code_404 on managers.t_log_users (id)
  where response_code = 404;

create index ci_log_users_response_code_410 on managers.t_log_users (id)
  where response_code = 410;

create index ci_log_users_response_code_500 on managers.t_log_users (id)
  where response_code = 500;

create index ci_log_users_response_code_503 on managers.t_log_users (id)
  where response_code = 503;

create index ci_log_users_response_code_normal on managers.t_log_users (id)
  where response_code in (200, 201, 202, 304);

create index ci_log_users_response_code_redirects on managers.t_log_users (id)
  where response_code in (301, 302, 303, 307, 308);

create index ci_log_users_response_code_notable on managers.t_log_users (id)
  where response_code in (400, 403, 404, 410, 500, 503);

/** when using current_user reserved function/word the index gets ignored. To prevent this, create a manual/custom index and alter the behavior of the views to be more explicit. **/
create unique index ci_log_users_current_user on managers.t_log_users (name_machine) with (fillfactor = 100);


/** only allow select and insert for users when user id is current user **/
create view users.v_log_users_self with (security_barrier=true) as
  select id, id_user, id_uuid, log_title, log_type, log_severity, log_details, log_date, request_client from managers.t_log_users
    where (name_machine)::text = (current_user)::text;

grant select on users.v_log_users_self to reservation_users;

create view users.v_log_users_self_insert with (security_barrier=true) as
  select id_user, id_uuid, name_machine, log_title, log_type, log_severity, log_details, request_client from managers.t_log_users
    where (name_machine)::text = (current_user)::text
    with check option;

grant insert on users.v_log_users_self_insert to reservation_users;


/** only allow insert for the public user **/
create view public.v_log_users_self_insert with (security_barrier=true) as
  select id_user, id_uuid, name_machine, log_title, log_type, log_severity, log_details, request_client from managers.t_log_users
    where id_user = 1 and name_machine = 'unknown'
    with check option;

grant insert on public.v_log_users_self_insert to public_users;


/** only allow insert for the system user **/
create view system.v_log_users_self_insert with (security_barrier=true) as
  select id_user, id_uuid, name_machine, log_title, log_type, log_severity, log_details, request_client from managers.t_log_users
    where id_user = 2 and name_machine = 'system'
    with check option;

grant insert on system.v_log_users_self_insert to reservation_user;



/*** provide access activity logging ***/
create table managers.t_log_activity (
  id bigint not null,
  id_user bigint default 1 not null,
  id_uuid uuid not null,
  name_machine varchar(128) not null,
  request_path varchar(512) not null,
  request_arguments varchar(512) not null,
  request_date timestamp default localtimestamp not null,
  request_client public.ct_client not null,
  request_headers json,
  response_headers json,
  response_code smallint not null default 0,

  constraint cp_log_activity_id primary key (id),

  constraint cc_log_activity_id check (id > 0),

  constraint cf_log_activity_id_user foreign key (id_user, name_machine) references administers.t_users (id, name_machine) on delete set null on update cascade,
  constraint cf_log_activity_response_code foreign key (response_code) references managers.t_log_http_status_codes (id) on delete restrict on update cascade
);

create sequence managers.s_log_activity_id owned by managers.t_log_activity.id;
alter table managers.t_log_activity alter column id set default nextval('managers.s_log_activity_id'::regclass);

grant select on managers.t_log_activity to reservation_users_administer;
grant select on managers.t_log_activity to reservation_users_manager;
grant select,usage on managers.s_log_activity_id to reservation_users_administer;
grant usage on managers.s_log_activity_id to reservation_users;
grant usage on managers.s_log_activity_id to public_users;

create index ci_log_activity_response_code_4xx on managers.t_log_activity (id)
  where response_code >= 400 and response_code < 500;

create index ci_log_activity_response_code_403 on managers.t_log_activity (id)
  where response_code = 403;

create index ci_log_activity_response_code_404 on managers.t_log_activity (id)
  where response_code = 404;

create index ci_log_activity_response_code_410 on managers.t_log_activity (id)
  where response_code = 410;

create index ci_log_activity_response_code_5xx on managers.t_log_activity (id)
  where response_code >= 500 and response_code < 600;

create index ci_log_activity_response_code_500 on managers.t_log_activity (id)
  where response_code = 500;

create index ci_log_activity_response_code_503 on managers.t_log_activity (id)
  where response_code = 503;

create index ci_log_activity_response_code_notable on managers.t_log_activity (id)
  where response_code in (403, 404, 410, 500, 503);

/** when using current_user reserved function/word the index gets ignored. To prevent this, create a manual/custom index and alter the behavior of the views to be more explicit. **/
create unique index ci_log_activity_current_user on managers.t_log_activity (name_machine) with (fillfactor = 100);


/** only allow select and insert for users when user id is current user **/
create view users.v_log_activity_self with (security_barrier=true) as
  select id, id_user, id_uuid, request_path, request_arguments, request_date, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where (name_machine)::text = (current_user)::text;

grant select on users.v_log_activity_self to reservation_users;

create view users.v_log_activity_self_insert with (security_barrier=true) as
  select id_user, id_uuid, name_machine, request_path, request_arguments, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where (name_machine)::text = (current_user)::text
    with check option;

grant insert on users.v_log_activity_self_insert to reservation_users;


/** only allow insert for the public user **/
create view public.v_log_activity_self_insert with (security_barrier=true) as
  select id_user, id_uuid, name_machine, request_path, request_arguments, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where id_user = 1 and name_machine = 'unknown'
    with check option;

grant insert on public.v_log_activity_self_insert to public_users;


/** only allow insert for the system user **/
create view system.v_log_activity_self_insert with (security_barrier=true) as
  select id_user, id_uuid, name_machine, request_path, request_arguments, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where id_user = 2 and name_machine = 'system'
    with check option;

grant insert on system.v_log_activity_self_insert to reservation_user;



/** Provide a log of problems, which are defined by the software. **/
create table managers.t_log_problems (
  id bigint not null,
  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  date_created timestamp default localtimestamp not null,

  constraint cp_log_problems_id primary key (id),

  constraint cc_log_problems_id check (id > 0),

  constraint cu_log_problems_name_machine unique (name_machine)
);

grant select on managers.t_log_activity to reservation_users_administer;
grant select on managers.t_log_activity to reservation_users_manager;



/** Provide a log of problems, associated with a given user. **/
create table managers.t_log_problems_users (
  id_problem bigint not null,
  id_user bigint not null,

  name_machine varchar(128) not null,

  date_created timestamp default localtimestamp not null,

  information_additional json,

  constraint cp_log_problems_users_id primary key (id_problem, id_user),

  constraint cu_log_problems_users_name_machine unique (name_machine),

  constraint cf_log_problems_users_id_problem foreign key (id_problem) references managers.t_log_problems (id) on delete restrict on update cascade,
  constraint cf_log_problems_users_id_user foreign key (id_user, name_machine) references administers.t_users (id, name_machine) on delete restrict on update cascade
);

grant select,delete on managers.t_log_problems_users to reservation_users_administer;
grant select,delete on managers.t_log_problems_users to reservation_users_manager;


/** only allow select, insert, and delete for users when user id is current user **/
create view users.v_log_problems_users_self with (security_barrier=true) as
  select id_problem, id_user, date_created, information_additional from managers.t_log_problems_users
    where (name_machine)::text = (current_user)::text;

grant select on users.v_log_activity_self to reservation_users;

create view users.v_log_problems_users_self_insert with (security_barrier=true) as
  select id_problem, id_user, name_machine, information_additional from managers.t_log_problems_users
    where (name_machine)::text = (current_user)::text
    with check option;

grant insert on users.v_log_problems_users_self_insert to reservation_users;

create view users.v_log_problems_users_self_delete with (security_barrier=true) as
  select id_problem, id_user from managers.t_log_problems_users
    where (name_machine)::text = (current_user)::text
    with check option;

grant delete on users.v_log_problems_users_self_delete to reservation_users;


commit transaction;
