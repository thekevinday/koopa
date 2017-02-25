/** Standardized SQL Structure - Logs */
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/*** provide user activity logging ***/
create table managers.t_log_users (
  id bigint not null,
  id_user bigint default 1 not null,

  name_machine_user varchar(128) not null,

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

  constraint cf_log_users_id_user foreign key (id_user, name_machine_user) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_log_users_log_type foreign key (log_type) references managers.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_users_log_severity foreign key (log_severity) references managers.t_log_severity_levels (id) on delete restrict on update cascade,
  constraint cf_log_users_response_code foreign key (response_code) references managers.t_log_http_status_codes (id) on delete restrict on update cascade
);

create sequence managers.s_log_users_id owned by managers.t_log_users.id;
alter table managers.t_log_users alter column id set default nextval('managers.s_log_users_id'::regclass);

grant select on managers.t_log_users to reservation_users_administer;
grant select on managers.t_log_users to reservation_users_manager;
grant select on managers.t_log_users to reservation_users_auditor;
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


/** only allow select and insert for users when user id is current user **/
create view users.v_log_users_self with (security_barrier=true) as
  select id, id_user, log_title, log_type, log_severity, log_details, log_date, request_client, response_code from managers.t_log_users
    where (name_machine_user)::text = (current_user)::text;

grant select on users.v_log_users_self to reservation_users;

create view users.v_log_users_self_insert with (security_barrier=true) as
  select id_user, name_machine_user, log_title, log_type, log_severity, log_details, request_client, response_code from managers.t_log_users
    where (name_machine_user)::text = (current_user)::text
    with check option;

grant insert on users.v_log_users_self_insert to reservation_users;


/** only allow insert for the public user **/
create view public.v_log_users_self_insert with (security_barrier=true) as
  select id_user, name_machine_user, log_title, log_type, log_severity, log_details, request_client, response_code from managers.t_log_users
    where id_user = 1 and name_machine_user = 'unknown'
    with check option;

grant insert on public.v_log_users_self_insert to public_users;


/** only allow insert for the system user **/
create view system.v_log_users_self_insert with (security_barrier=true) as
  select id_user, name_machine_user, log_title, log_type, log_severity, log_details, request_client, response_code from managers.t_log_users
    where id_user = 2 and name_machine_user = 'system'
    with check option;

grant insert on system.v_log_users_self_insert to reservation_users;



/*** provide access activity logging ***/
create table managers.t_log_activity (
  id bigint not null,
  id_user bigint default 1 not null,

  name_machine_user varchar(128) not null,

  request_path varchar(512) not null,
  request_arguments varchar(512) not null,
  request_date timestamp default localtimestamp not null,
  request_client public.ct_client not null,
  request_headers json,

  response_headers json,
  response_code smallint not null default 0,

  constraint cp_log_activity_id primary key (id),

  constraint cc_log_activity_id check (id > 0),

  constraint cf_log_activity_id_user foreign key (id_user, name_machine_user) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_log_activity_response_code foreign key (response_code) references managers.t_log_http_status_codes (id) on delete restrict on update cascade
);

create sequence managers.s_log_activity_id owned by managers.t_log_activity.id;
alter table managers.t_log_activity alter column id set default nextval('managers.s_log_activity_id'::regclass);

grant select on managers.t_log_activity to reservation_users_administer;
grant select on managers.t_log_activity to reservation_users_manager;
grant select on managers.t_log_activity to reservation_users_auditor;
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



/** only allow select and insert for users when user id is current user **/
create view users.v_log_activity_self with (security_barrier=true) as
  select id, id_user, request_path, request_arguments, request_date, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where (name_machine_user)::text = (current_user)::text;

grant select on users.v_log_activity_self to reservation_users;

create view users.v_log_activity_self_insert with (security_barrier=true) as
  select id_user, name_machine_user, request_path, request_arguments, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where (name_machine_user)::text = (current_user)::text
    with check option;

grant insert on users.v_log_activity_self_insert to reservation_users;


/** only allow insert for the public user **/
create view public.v_log_activity_self_insert with (security_barrier=true) as
  select id_user, name_machine_user, request_path, request_arguments, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where id_user = 1 and name_machine_user = 'unknown'
    with check option;

grant insert on public.v_log_activity_self_insert to public_users;


/** only allow insert for the system user **/
create view system.v_log_activity_self_insert with (security_barrier=true) as
  select id_user, name_machine_user, request_path, request_arguments, request_client, request_headers, response_headers, response_code from managers.t_log_activity
    where id_user = 2 and name_machine_user = 'system'
    with check option;

grant insert on system.v_log_activity_self_insert to reservation_users;



commit transaction;
