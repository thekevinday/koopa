/** Standardized SQL Structure - Content **/
/** This depends on: reservation-groups.sql, reservation-types.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;



/* @todo: come up with a design for dynamic path management via users/managers (as opposed to hardcoded paths in source). */
/*** provide paths table (@todo: this is added as a stub and needs to be finished) ***/
create table s_tables.t_paths (
  id bigint not null,
  id_creator bigint not null,
  id_creator_session bigint not null,
  id_group bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_content boolean default true not null,
  is_alias boolean default false not null,
  is_redirect boolean default false not null,
  is_coded boolean default false not null,
  is_dynamic boolean default true not null,
  is_user boolean default false not null,
  is_private boolean default true not null,
  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  field_path varchar(256) not null,
  field_destination varchar(256),
  field_response_code smallint,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  constraint cp_paths primary key (id),

  constraint cu_paths_name_machine unique (name_machine),
  constraint cu_paths_field_path unique (field_path),

  constraint cc_paths_id check (id > 0),
  constraint cc_paths_name_machine check (name_machine ~ '\w+'),
  constraint cc_paths_one_of_content_alias_redirect check ((is_content and not (is_alias or is_redirect)) or (is_alias and not (is_content or is_redirect))),

  constraint cf_paths_id_creator foreign key (id_creator) references s_tables.t_users (id) on delete cascade on update cascade,
  constraint cf_paths_id_creator_session foreign key (id_creator_session) references s_tables.t_users (id) on delete cascade on update cascade,
  constraint cf_paths_id_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade,
  constraint cf_paths_field_response_code foreign key (field_response_code) references s_tables.t_type_http_status_codes (id) on delete restrict on update cascade
);

create sequence s_tables.se_paths_id owned by s_tables.t_paths.id;
alter table s_tables.t_paths alter column id set default nextval('s_tables.se_paths_id'::regclass);

grant select,insert,update on s_tables.t_paths to r_reservation_administer;
grant select on s_tables.t_paths to r_reservation_manager, r_reservation_auditor;
grant select,usage on s_tables.se_paths_id to r_reservation_administer;
grant usage on s_tables.se_paths_id to r_reservation, r_reservation_system;

create index i_paths_deleted_not on s_tables.t_paths (id)
  where not is_deleted;

create index i_paths_private_not on s_tables.t_paths (id)
  where not is_deleted and not is_private;

create index i_paths_locked_not on s_tables.t_paths (id)
  where not is_deleted and not is_locked;

create index i_paths_public on s_tables.t_paths (id)
  where not is_deleted and not is_locked and not is_private;

create index i_paths_content on s_tables.t_paths (id)
  where not is_deleted and is_content;

create index i_paths_alias on s_tables.t_paths (id)
  where not is_deleted and is_alias;

create index i_paths_redirect on s_tables.t_paths (id)
  where not is_deleted and is_redirect;


/* @todo: provide management functionality for managers (for all user content) and users (for groups they belong to with appropriate can_manage role). */
create view s_users.v_paths with (security_barrier=true) as
  with allowed_groups as (select id from s_users.v_groups_self)
  select id, id_group, name_machine, name_human, is_content, is_alias, is_redirect, is_coded, is_dynamic, is_locked, is_private, field_path, field_destination, field_response_code, date_created, date_changed, date_locked from s_tables.t_paths
  where not is_deleted and (not is_locked or not is_private or id_group in (select * from allowed_groups));

grant select on s_users.v_paths to r_reservation, r_reservation_system;

create view public.v_paths with (security_barrier=true) as
  select id, NULL::bigint as id_group, name_machine, name_human, is_content, is_alias, is_redirect, is_coded, is_dynamic, FALSE as is_locked, FALSE as is_private, field_path, field_destination, field_response_code, NULL::bool as date_created, NULL::bool as date_changed, NULL::bool as date_locked from s_tables.t_paths
  where not is_deleted and not is_locked and not is_private;

grant select on public.v_paths to r_reservation_public;


create trigger tr_paths_date_changed_deleted_or_locked
  before update on s_tables.t_paths
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_paths_enforce_creator_and_session_ids
  before insert on s_tables.t_paths
    for each row execute procedure s_administers.f_common_enforce_creator_and_session_ids();



commit transaction;
