/** Standardized SQL Structure - Logs */
/** This depends on: standard-users.sql, standard-types.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/*** provide user activity logging ***/
create table s_tables.t_log_users (
  id bigint not null,
  id_user bigint not null,
  id_user_session bigint not null,

  log_title varchar(512) not null,
  log_type bigint not null,
  log_type_sub bigint not null,
  log_severity bigint not null,
  log_facility bigint not null,
  log_details json,
  log_date timestamp with time zone default current_timestamp not null,

  request_client public.ct_client not null,
  response_code smallint not null default 0,

  constraint cp_log_users primary key (id),

  constraint cc_log_users_id check (id > 0),
  constraint cc_log_users_log_severity check (log_severity > 0),

  constraint cf_log_users_id_user foreign key (id_user) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_users_id_user_session foreign key (id_user_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_users_log_type foreign key (log_type) references s_tables.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_users_log_type_sub foreign key (log_type_sub) references s_tables.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_users_log_severity foreign key (log_severity) references s_tables.t_log_type_severitys (id) on delete restrict on update cascade,
  constraint cf_log_users_log_facility foreign key (log_facility) references s_tables.t_log_type_facilitys (id) on delete restrict on update cascade,
  constraint cf_log_users_response_code foreign key (response_code) references s_tables.t_type_http_status_codes (id) on delete restrict on update cascade
);

create sequence s_tables.se_log_users_id owned by s_tables.t_log_users.id;
alter table s_tables.t_log_users alter column id set default nextval('s_tables.se_log_users_id'::regclass);


create index i_log_users_response_code_200 on s_tables.t_log_users (id)
  where response_code = 200;

create index i_log_users_response_code_400 on s_tables.t_log_users (id)
  where response_code = 400;

create index i_log_users_response_code_403 on s_tables.t_log_users (id)
  where response_code = 403;

create index i_log_users_response_code_404 on s_tables.t_log_users (id)
  where response_code = 404;

create index i_log_users_response_code_410 on s_tables.t_log_users (id)
  where response_code = 410;

create index i_log_users_response_code_451 on s_tables.t_log_users (id)
  where response_code = 451;

create index i_log_users_response_code_500 on s_tables.t_log_users (id)
  where response_code = 500;

create index i_log_users_response_code_503 on s_tables.t_log_users (id)
  where response_code = 503;

create index i_log_users_response_code_normal on s_tables.t_log_users (id)
  where response_code in (200, 201, 202, 304);

create index i_log_users_response_code_redirects on s_tables.t_log_users (id)
  where response_code in (301, 302, 303, 307, 308);

create index i_log_users_response_code_notable on s_tables.t_log_users (id)
  where response_code in (400, 403, 404, 410, 451, 500, 503);


/** only allow select and insert for users when user id is current user **/
create view s_users.v_log_users_self with (security_barrier=true) as
  with this_user as (select id from v_users_self_locked_not)
  select id, id_user, log_title, log_type, log_type_sub, log_severity, log_facility, log_details, log_date, request_client, response_code from s_tables.t_log_users
    where id_user in (select * from this_user);

create view s_users.v_log_users_self_insert with (security_barrier=true) as
  select log_title, log_type, log_type_sub, log_severity, log_facility, log_details, request_client, response_code from s_tables.t_log_users
    where id_user in (select id from v_users_self_locked_not)
    with check option;


/** public users should be able to insert, but should never be able to view the logs that they insert. **/
create view public.v_log_users_self_insert with (security_barrier=true) as
  select log_title, log_type, log_type_sub, log_severity, log_facility, log_details, request_client, response_code from s_tables.t_log_users
    where 'r_standard_public' in (select pr.rolname from pg_auth_members pam inner join pg_roles pr on (pam.roleid = pr.oid) inner join pg_roles pr_u on (pam.member = pr_u.oid) where pr_u.rolname = current_user and pr.rolname = 'r_standard_public')
    with check option;


create trigger tr_log_users_enforce_user_and_session_ids
  before insert on s_tables.t_log_users
    for each row execute procedure s_administers.f_common_enforce_user_and_session_ids();



/*** provide access activity logging ***/
create table s_tables.t_log_user_activity (
  id bigint not null,
  id_user bigint not null,
  id_user_session bigint not null,

  request_path varchar(512) not null,
  request_arguments varchar(512) not null,
  request_date timestamp with time zone default current_timestamp not null,
  request_client public.ct_client not null,
  request_headers json,

  response_headers json,
  response_code smallint not null default 0,

  constraint cp_log_user_activity primary key (id),

  constraint cc_log_user_activity_id check (id > 0),

  constraint cf_log_user_activity_id_user foreign key (id_user) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_user_activity_id_user_session foreign key (id_user_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_user_activity_response_code foreign key (response_code) references s_tables.t_type_http_status_codes (id) on delete restrict on update cascade
);

create sequence s_tables.se_log_user_activity_id owned by s_tables.t_log_user_activity.id;
alter table s_tables.t_log_user_activity alter column id set default nextval('s_tables.se_log_user_activity_id'::regclass);


create index i_log_user_activity_response_code_4xx on s_tables.t_log_user_activity (id)
  where response_code >= 400 and response_code < 500;

create index i_log_user_activity_response_code_403 on s_tables.t_log_user_activity (id)
  where response_code = 403;

create index i_log_user_activity_response_code_404 on s_tables.t_log_user_activity (id)
  where response_code = 404;

create index i_log_user_activity_response_code_410 on s_tables.t_log_user_activity (id)
  where response_code = 410;

create index i_log_user_activity_response_code_451 on s_tables.t_log_user_activity (id)
  where response_code = 451;

create index i_log_user_activity_response_code_5xx on s_tables.t_log_user_activity (id)
  where response_code >= 500 and response_code < 600;

create index i_log_user_activity_response_code_500 on s_tables.t_log_user_activity (id)
  where response_code = 500;

create index i_log_user_activity_response_code_503 on s_tables.t_log_user_activity (id)
  where response_code = 503;

create index i_log_user_activity_response_code_notable on s_tables.t_log_user_activity (id)
  where response_code in (403, 404, 410, 451, 500, 503);



/** only allow select and insert for users when user id is current user **/
create view s_users.v_log_user_activity_self with (security_barrier=true) as
  with this_user as (select id from v_users_self_locked_not)
  select id, id_user, request_path, request_arguments, request_date, request_client, request_headers, response_headers, response_code from s_tables.t_log_user_activity
    where id_user in (select * from this_user);

create view s_users.v_log_user_activity_self_insert with (security_barrier=true) as
  select request_path, request_arguments, request_client, request_headers, response_headers, response_code from s_tables.t_log_user_activity
    where id_user in (select id from v_users_self_locked_not)
    with check option;


/** public users should be able to insert, but should never be able to view the logs that they insert. **/
create view public.v_log_user_activity_self_insert with (security_barrier=true) as
  select request_path, request_arguments, request_client, request_headers, response_headers, response_code from s_tables.t_log_user_activity
    where id_user in (select id from v_users_self_locked_not)
    with check option;


create trigger tr_log_user_activity_enforce_user_and_session_ids
  before insert on s_tables.t_log_user_activity
    for each row execute procedure s_administers.f_common_enforce_user_and_session_ids();



commit transaction;
