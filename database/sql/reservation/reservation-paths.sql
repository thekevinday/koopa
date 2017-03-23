/** Standardized SQL Structure - Content **/
/** This depends on: base-groups.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;



/*** provide path type id and names ***/
create table s_tables.t_path_types (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,
  date_locked timestamp,

  constraint cp_path_types primary key (id),

  constraint cu_path_types_name_machine unique (name_machine),

  constraint cc_path_types_id check (id > -1)
);

create sequence s_tables.se_path_types_id owned by s_tables.t_path_types.id;
alter table s_tables.t_path_types alter column id set default nextval('s_tables.se_path_types_id'::regclass);

grant select,insert,update on s_tables.t_path_types to r_reservation_administer;
grant select on s_tables.t_path_types to r_reservation_manager, r_reservation_auditor;
grant select,usage on s_tables.se_path_types_id to r_reservation_administer;

create index i_path_types_deleted_not on s_tables.t_path_types (id)
  where is_deleted is not true;

create index i_path_types_public on s_tables.t_path_types (id)
  where is_deleted is not true and is_locked is not true;


create view s_managers.v_path_types with (security_barrier=true) as
  select id, name_machine, name_human, is_locked, date_created, date_changed from s_tables.t_path_types
  where is_deleted is not true and is_locked is not true;

grant select on s_managers.v_path_types to r_reservation_manager;


create view public.v_path_types with (security_barrier=true) as
  select id, name_machine, name_human, FALSE as is_locked, NULL::timestamp as date_created, NULL::timestamp as date_changed from s_tables.t_path_types
  where is_deleted is not true and is_locked is not true;

grant select on public.v_path_types to r_reservation, r_public, r_reservation_system;


create trigger tr_path_types_date_changed_deleted_or_locked
  before update on s_tables.t_path_types
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();


/* @todo: come up with a design for dynamic path management via users/managers (as opposed to hardcoded paths in source). */
/*** provide paths table (@todo: this is added as a stub and needs to be finished) ***/
create table s_tables.t_paths (
  id bigint not null,
  id_creator bigint not null,
  id_creator_session bigint not null,
  id_type bigint not null,
  id_group bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_private boolean default true not null,
  is_locked boolean default false not null,
  is_deleted boolean default false not null,
  is_system boolean default false not null,
  is_user boolean default false not null,

  field_path varchar(256) not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  constraint cp_paths primary key (id),

  constraint cu_paths_name_machine unique (name_machine),
  constraint cu_paths_field_path unique (field_path),

  constraint cc_paths_id check (id > 0),

  constraint cf_paths_id_creator foreign key (id_creator) references s_tables.t_users (id) on delete cascade on update cascade,
  constraint cf_paths_id_creator_session foreign key (id_creator_session) references s_tables.t_users (id) on delete cascade on update cascade,
  constraint cf_paths_id_type foreign key (id_type) references s_tables.t_path_types (id) on delete restrict on update cascade,
  constraint cf_paths_id_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade
);

create sequence s_tables.se_paths_id owned by s_tables.t_paths.id;
alter table s_tables.t_paths alter column id set default nextval('s_tables.se_paths_id'::regclass);

grant select,insert,update on s_tables.t_path_types to r_reservation_administer;
grant select on s_tables.t_paths to r_reservation_manager, r_reservation_auditor;
grant select,usage on s_tables.se_paths_id to r_reservation_administer;
grant usage on s_tables.se_paths_id to r_reservation, r_reservation_system;

create index i_paths_deleted_not on s_tables.t_paths (id)
  where is_deleted is not true;

create index i_paths_private_not on s_tables.t_paths (id)
  where is_deleted is not true and is_private is not true;

create index i_paths_locked_not on s_tables.t_paths (id)
  where is_deleted is not true and is_locked is not true;

create index i_paths_public on s_tables.t_paths (id)
  where is_deleted is not true and is_locked is not true and is_private is not true;


create view s_users.v_paths with (security_barrier=true) as
  with allowed_groups as (select id from s_users.v_groups_self)
  select id, id_type, id_group, name_machine, name_human, is_private, date_created, date_changed from s_tables.t_paths
  where is_deleted is not true and (is_locked is not true or id_group in (select * from allowed_groups)) and (is_private is not true or (is_private is true and id_group in (select * from allowed_groups)));

grant select on s_users.v_paths to r_reservation, r_reservation_system;

create view public.v_paths with (security_barrier=true) as
  select id, id_type, NULL::bigint as id_group, name_machine, name_human, NULL::bool as is_private, NULL::bool as date_created, NULL::bool as date_changed from s_tables.t_paths
  where is_deleted is not true and is_locked is not true and is_private is not true;

grant select on public.v_path_types to r_reservation, r_public, r_reservation_system;


create trigger tr_paths_date_changed_deleted_or_locked
  before update on s_tables.t_paths
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_paths_enforce_creator_and_session_ids
  before insert on s_tables.t_paths
    for each row execute procedure s_administers.f_common_enforce_creator_and_session_ids();



commit transaction;
