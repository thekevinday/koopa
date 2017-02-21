/** Standardized SQL Structure - Logs - Groups */
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/*** provide group activity logging ***/
create table managers.t_log_groups (
  id bigint not null,
  id_user bigint default 1 not null,
  id_group bigint not null,

  name_machine_user varchar(128) not null,

  log_type bigint not null,
  log_details json,
  log_date timestamp default localtimestamp not null,

  constraint cp_log_groups_id primary key (id),

  constraint cc_log_groups_id check (id > 0),

  constraint cf_log_groups_id_user foreign key (id_user, name_machine_user) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_log_groups_id_group foreign key (id_group) references managers.t_groups (id) on delete restrict on update cascade,
  constraint cf_log_groups_log_type foreign key (log_type) references managers.t_log_types (id) on delete restrict on update cascade,
);

create sequence managers.s_log_groups_id owned by managers.t_log_groups.id;
alter table managers.t_log_groups alter column id set default nextval('managers.s_log_groups_id'::regclass);

grant select on managers.t_log_groups to reservation_users_administer;
grant select on managers.t_log_groups to reservation_users_manager;
grant select on managers.t_log_groups to reservation_users_auditor;
grant select,usage on managers.s_log_groups_id to reservation_users_administer;
grant usage on managers.s_log_groups_id to reservation_users;


/** only allow select and insert for users when user id is current user **/
create view users.v_log_groups_self with (security_barrier=true) as
  gselect id, id_user, id_group, log_type, log_details, log_date from managers.t_log_groups
    where (name_machine_user)::text = (current_user)::text;

grant select on users.v_log_groups_self to reservation_users;

create view users.v_log_groups_self_insert with (security_barrier=true) as
  select id_user, id_group, name_machine_user, log_type, log_details from managers.t_log_groups
    where (name_machine_user)::text = (current_user)::text
    with check option;

grant insert on users.v_log_groups_self_insert to reservation_users;



/*** provide group user activity logging ***/
create table managers.t_log_groups_users (
  id bigint not null,
  id_user bigint default 1 not null,
  id_group bigint not null,

  name_machine_user varchar(128) not null,

  log_type bigint not null,
  log_date timestamp default localtimestamp not null,

  constraint cp_log_groups_id primary key (id),

  constraint cc_log_groups_id check (id > 0),

  constraint cf_log_groups_id_user foreign key (id_user, name_machine_user) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_log_groups_id_group foreign key (id_group) references managers.t_groups (id) on delete restrict on update cascade,
  constraint cf_log_groups_log_type foreign key (log_type) references managers.t_log_types (id) on delete restrict on update cascade,
);

create sequence managers.s_log_groups_users_id owned by managers.t_log_groups_users.id;
alter table managers.t_log_groups_users alter column id set default nextval('managers.s_log_groups_users_id'::regclass);

grant select on managers.t_log_groups_users to reservation_users_administer;
grant select on managers.t_log_groups_users to reservation_users_manager;
grant select on managers.t_log_groups_users to reservation_users_auditor;
grant select,usage on managers.s_log_groups_users_id to reservation_users_administer;
grant usage on managers.s_log_groups_users_id to reservation_users;


/** only allow select and insert for users when user id is current user **/
create view users.v_log_groups_users_self with (security_barrier=true) as
  select id, id_user, id_group, log_type, log_date from managers.t_log_groups_users
    where (name_machine_user)::text = (current_user)::text;

grant select on users.v_log_groups_users_self to reservation_users;

create view users.v_log_groups_users_self_insert with (security_barrier=true) as
  select id_user, id_group, name_machine_user, log_type from managers.t_log_groups_users
    where (name_machine_user)::text = (current_user)::text
    with check option;

grant insert on users.v_log_groups_users_self_insert to reservation_users;



commit transaction;
