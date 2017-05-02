/** Standardized SQL Structure - Logs - Groups */
/** This depends on: reservation-groups.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/*** provide group activity logging ***/
create table s_tables.t_log_groups (
  id bigint not null,
  id_user bigint not null,
  id_user_session bigint not null,
  id_group bigint not null,

  log_type bigint not null,
  log_type_sub bigint not null,
  log_severity bigint not null,
  log_facility bigint not null,
  log_details json,
  log_date timestamp with time zone default current_timestamp not null,

  constraint cp_log_groups primary key (id),

  constraint cc_log_groups_id check (id > 0),

  constraint cf_log_groups_id_user foreign key (id_user) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_groups_id_user_session foreign key (id_user_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_groups_id_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade,
  constraint cf_log_groups_log_type foreign key (log_type) references s_tables.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_groups_log_type_sub foreign key (log_type_sub) references s_tables.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_groups_log_severity foreign key (log_severity) references s_tables.t_log_type_severitys (id) on delete restrict on update cascade,
  constraint cf_log_groups_log_facility foreign key (log_facility) references s_tables.t_log_type_facilitys (id) on delete restrict on update cascade
);

create sequence s_tables.se_log_groups_id owned by s_tables.t_log_groups.id;
alter table s_tables.t_log_groups alter column id set default nextval('s_tables.se_log_groups_id'::regclass);

grant select,usage on s_tables.se_log_groups_id to r_reservation_manager, r_reservation_auditor;
grant usage on s_tables.se_log_groups_id to r_reservation, r_reservation_system;


/** only allow select and insert for users when user id is current user **/
create view s_users.v_log_groups_self with (security_barrier=true) as
  with this_user as (select id from public.v_users_self_locked_not)
  select id, id_user, id_group, log_type, log_type_sub, log_severity, log_facility, log_details, log_date from s_tables.t_log_groups
    where id_user in (select * from this_user);

grant select on s_users.v_log_groups_self to r_reservation, r_reservation_system;

create view s_users.v_log_groups_self_insert with (security_barrier=true) as
  select id_group, log_type, log_type_sub, log_severity, log_facility, log_details from s_tables.t_log_groups
    where id_user in (select id from public.v_users_self_locked_not) and id_group in (select id from s_users.v_groups_self where not is_locked)
    with check option;

grant insert on s_users.v_log_groups_self_insert to r_reservation, r_reservation_system;


create trigger tr_log_groups_date_changed_deleted_or_locked
  before update on s_tables.t_log_groups
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_log_groups_enforce_user_and_session_ids
  before insert on s_tables.t_log_groups
    for each row execute procedure s_administers.f_common_enforce_user_and_session_ids();



/*** provide group user activity logging ***/
create table s_tables.t_log_group_users (
  id bigint not null,
  id_user bigint not null,
  id_user_session bigint not null,
  id_group bigint not null,

  log_type bigint not null,
  log_type_sub bigint not null,
  log_severity bigint not null,
  log_facility bigint not null,
  log_date timestamp with time zone default current_timestamp not null,

  constraint cp_log_group_users primary key (id),

  constraint cc_log_group_users_id check (id > 0),

  constraint cf_log_group_users_id_user foreign key (id_user) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_group_users_id_user_session foreign key (id_user_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_group_users_id_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade,
  constraint cf_log_group_users_log_type foreign key (log_type) references s_tables.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_group_users_log_type_sub foreign key (log_type_sub) references s_tables.t_log_types (id) on delete restrict on update cascade,
  constraint cf_log_group_users_log_severity foreign key (log_severity) references s_tables.t_log_type_severitys (id) on delete restrict on update cascade,
  constraint cf_log_group_users_log_facility foreign key (log_facility) references s_tables.t_log_type_facilitys (id) on delete restrict on update cascade
);

create sequence s_tables.se_log_group_users_id owned by s_tables.t_log_group_users.id;
alter table s_tables.t_log_group_users alter column id set default nextval('s_tables.se_log_group_users_id'::regclass);

grant select,usage on s_tables.se_log_group_users_id to r_reservation_manager, r_reservation_auditor;
grant usage on s_tables.se_log_group_users_id to r_reservation, r_reservation_system;


/** only allow select and insert for users when user id is current user **/
create view s_users.v_log_group_users_self with (security_barrier=true) as
  with this_user as (select id from public.v_users_self_locked_not),
    allowed_groups as (select id from s_users.v_groups_self where not is_locked)
  select id, id_user, id_group, log_type, log_type_sub, log_severity, log_facility, log_date from s_tables.t_log_group_users
    where id_user in (select * from this_user) or id_group in (select * from allowed_groups);

grant select on s_users.v_log_group_users_self to r_reservation, r_reservation_system;

create view s_users.v_log_group_users_self_insert with (security_barrier=true) as
  select id_group, log_type, log_type_sub, log_severity, log_facility from s_tables.t_log_group_users
    where id_user in (select id from public.v_users_self_locked_not) and id_group in (select id from s_users.v_groups_self where not is_locked)
    with check option;

grant insert on s_users.v_log_group_users_self_insert to r_reservation, r_reservation_system;


create trigger tr_log_group_users_date_changed_deleted_or_locked
  before update on s_tables.t_log_group_users
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_log_group_users_enforce_user_and_session_ids
  before insert on s_tables.t_log_group_users
    for each row execute procedure s_administers.f_common_enforce_user_and_session_ids();



commit transaction;
