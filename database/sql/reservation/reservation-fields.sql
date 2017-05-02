/** Standardized SQL Structure - Fields **/
/** This depends on: reservation-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/*** Field: Affiliations ***/
create table s_tables.t_field_affiliations (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  constraint cu_field_affiliations_id unique (id),
  constraint cu_field_affiliations_name_machine unique (name_machine),

  constraint cc_field_affiliations_id check (id > 0),
  constraint cc_field_affiliations_name_machine check (name_machine ~ '[A-Za-z]\w*')
);

create sequence s_tables.se_field_affiliations_id owned by s_tables.t_field_affiliations.id;
alter table s_tables.t_field_affiliations alter column id set default nextval('s_tables.se_field_affiliations_id'::regclass);

grant select,insert,update on s_tables.t_field_affiliations to r_reservation_manager;
grant select on s_tables.t_field_affiliations to r_reservation_auditor;
grant select,usage on s_tables.se_field_affiliations_id to r_reservation_manager;

create index i_field_affiliations_deleted_not on s_tables.t_field_affiliations (id)
  where not is_deleted;

create index i_field_affiliations_locked_not on s_tables.t_field_affiliations (id)
  where not is_deleted and not is_locked;

create view s_users.v_field_affiliations with (security_barrier=true) as
  select id, id_external, name_machine, name_human, is_locked from s_tables.t_field_affiliations
  where not is_deleted;

grant select on s_users.v_field_affiliations to r_reservation, r_reservation_system;


create trigger tr_field_affiliations_update_date_changed_deleted_or_locked
  before update on s_tables.t_field_affiliations
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/*** Field: Classifications ***/
create table s_tables.t_field_classifications (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  constraint cu_field_classifications_id unique (id),
  constraint cu_field_classifications_name_machine unique (name_machine),

  constraint cc_field_classifications_id check (id > 0)
);

create sequence s_tables.se_field_classifications_id owned by s_tables.t_field_classifications.id;
alter table s_tables.t_field_classifications alter column id set default nextval('s_tables.se_field_classifications_id'::regclass);

grant select,insert,update on s_tables.t_field_classifications to r_reservation_manager;
grant select on s_tables.t_field_classifications to r_reservation_auditor;
grant select,usage on s_tables.se_field_classifications_id to r_reservation_manager;

create index i_field_classifications_deleted_not on s_tables.t_field_classifications (id)
  where not is_deleted;

create index i_field_classifications_locked_not on s_tables.t_field_classifications (id)
  where not is_deleted and not is_locked;


create view s_users.v_field_classifications with (security_barrier=true) as
  select id, id_external, name_machine, name_human, is_locked from s_tables.t_field_classifications
  where not is_deleted;

grant select on s_users.v_field_classifications to r_reservation, r_reservation_system;


create trigger tr_field_classifications_update_date_changed_deleted_or_locked
  before update on s_tables.t_field_classifications
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

/** @todo: create all field types needed for f_requests fields **/

commit transaction;
