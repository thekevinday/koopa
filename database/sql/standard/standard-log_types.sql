/** Standardized SQL Structure - Logs - Types */
/** This depends on: reservation-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;



/*** provide log type id and names ***/
create table s_tables.t_log_types (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cp_log_types primary key (id),

  constraint cu_log_types_user unique (name_machine),

  constraint cc_log_types_id check (id >= 0)
);

create sequence s_tables.se_log_types_id owned by s_tables.t_log_types.id;
alter table s_tables.t_log_types alter column id set default nextval('s_tables.se_log_types_id'::regclass);

grant select,insert,update on s_tables.t_log_types to r_standard_administer;
grant select on s_tables.t_log_types to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_types_id to r_standard_administer;

create index i_log_types_deleted_not on s_tables.t_log_types (id)
  where not is_deleted;

create index i_log_types_public on s_tables.t_log_types (id)
  where not is_deleted and not is_locked;

create view public.v_log_types with (security_barrier=true) as
  select id, name_machine, name_human from s_tables.t_log_types
  where not is_deleted and not is_locked;

grant select on public.v_log_types to r_standard, r_standard_public, r_standard_system;


create trigger tr_log_types_date_changed_deleted_or_locked
  before update on s_tables.t_log_types
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/*** provide log severity level id and names ***/
create table s_tables.t_log_type_severity_levels (
  id bigint not null,
  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  constraint cp_log_type_severity_levels primary key (id),

  constraint cu_log_type_severity_levels_user unique (name_machine),

  constraint cc_log_type_severity_levels_id check (id >= 0)
);

create sequence s_tables.se_log_type_severity_levels_id owned by s_tables.t_log_type_severity_levels.id;
alter table s_tables.t_log_type_severity_levels alter column id set default nextval('s_tables.se_log_type_severity_levels_id'::regclass);

grant select,insert,update on s_tables.t_log_type_severity_levels to r_standard_administer;
grant select on s_tables.t_log_type_severity_levels to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_type_severity_levels_id to r_standard_administer;

create view s_users.v_log_type_severity_levels with (security_barrier=true) as
  select id, name_machine, name_human from s_tables.t_log_type_severity_levels
  where not is_deleted;

grant select on s_users.v_log_type_severity_levels to r_standard, r_standard_public, r_standard_system;


create trigger tr_log_type_severity_levels_date_changed_deleted_or_locked
  before update on s_tables.t_log_type_severity_levels
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



commit transaction;
