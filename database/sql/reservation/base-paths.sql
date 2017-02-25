/** Standardized SQL Structure - Content **/
/** This depends on: base-groups.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/*** provide path type id and names ***/
create table managers.t_path_types (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cp_path_types_id primary key (id),

  constraint cu_path_types_user unique (name_machine),

  constraint cc_path_types_id check (id > 0)
);

create sequence managers.s_path_types_id owned by managers.t_path_types.id;
alter table managers.t_path_types alter column id set default nextval('managers.s_path_types_id'::regclass);

grant select,insert,update on managers.t_path_types to reservation_users_administer;
grant select on managers.t_path_types to reservation_users_manager;
grant select on managers.t_path_types to reservation_users_auditor;
grant select,usage on managers.s_path_types_id to reservation_users_administer;

create index ci_path_types_deleted_not on managers.t_path_types (id)
  where is_deleted is not true;

create index ci_path_types_public on managers.t_path_types (id)
  where is_deleted is not true and is_locked is not true;

create view public.v_path_types with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_path_types
  where is_deleted is not true and is_locked is not true;

grant select on public.v_path_types to reservation_users;
grant select on public.v_path_types to public_users;


/* @todo: pre-populate the path types. */


/* @todo: with its current design, the id_access field needs a trigger to maintain its up to date status if/when group ids change. */
/* @todo: come up with a design for dynamic path management via users/managers (as opposed to hardcoded paths in source). */
/*** provide paths table (@todo: this is added as a stub and needs to be finished) ***/
create table managers.t_paths (
  id bigint not null,
  id_type bigint not null,
  id_group bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_private boolean default true not null,
  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cp_paths_id primary key (id),

  constraint cu_paths_name_machine unique (name_machine),

  constraint cc_paths_id check (id > 0),

  constraint cf_paths_id_type foreign key (id_type) references managers.t_path_types (id) on delete restrict on update cascade,
  constraint cf_paths_id_group foreign key (id_group) references managers.t_groups (id) on delete restrict on update cascade
);

create sequence managers.s_paths_id owned by managers.t_paths.id;
alter table managers.t_paths alter column id set default nextval('managers.s_paths_id'::regclass);

grant select on managers.t_paths to reservation_users_administer;
grant select on managers.t_paths to reservation_users_manager;
grant select on managers.t_paths to reservation_users_auditor;
grant select,usage on managers.s_paths_id to reservation_users_administer;
grant usage on managers.s_paths_id to reservation_users;

create index ci_paths_deleted_not on managers.t_paths (id)
  where is_deleted is not true;

create index ci_paths_private_not on managers.t_paths (id)
  where is_deleted is not true and is_private is not true;

create index ci_paths_locked_not on managers.t_paths (id)
  where is_deleted is not true and is_locked is not true;

create index ci_paths_public on managers.t_paths (id)
  where is_deleted is not true and is_locked is not true and is_private is not true;

create view users.v_paths with (security_barrier=true) as
  with allowed_groups as (select id from users.v_groups_self)
  select id, id_type, id_group, name_machine, name_human, is_private from managers.t_paths
  where is_deleted is not true and (is_locked is not true or id_group in (select * from allowed_groups)) and (is_private is not true or (is_private is true and id_group in (select * from allowed_groups)));

grant select on users.v_paths to reservation_users;

create view public.v_paths with (security_barrier=true) as
  select id, id_type, name_machine, name_human from managers.t_paths
  where is_deleted is not true and is_locked is not true and is_private is not true;

grant select on public.v_path_types to public_users;

commit transaction;
