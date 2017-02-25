/** Standardized SQL Structure - Content **/
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/*** Field: Affiliations ***/
create table managers.t_field_affiliations (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cu_field_affiliations_id unique (id),
  constraint cu_field_affiliations_name_machine unique (name_machine),

  constraint cc_field_affiliations_id check (id > 0)
);

create sequence managers.s_field_affiliations_id owned by managers.t_field_affiliations.id;
alter table managers.t_field_affiliations alter column id set default nextval('managers.s_field_affiliations_id'::regclass);

grant select,insert,update on managers.t_field_affiliations to reservation_users_administer;
grant select,insert,update on managers.t_field_affiliations to reservation_users_manager;
grant select on managers.t_field_affiliations to reservation_users_auditor;
grant select,usage on managers.s_field_affiliations_id to reservation_users_administer;
grant select,usage on managers.s_field_affiliations_id to reservation_users_manager;

create view users.v_field_affiliations with (security_barrier=true) as
  select id, id_external, name_machine, name_human from managers.t_field_affiliations
  where is_deleted is false;

grant select on users.v_field_affiliations to reservation_users;



/*** Field: Classifications ***/
create table managers.t_field_classifications (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cu_field_classifications_id unique (id),
  constraint cu_field_classifications_name_machine unique (name_machine),

  constraint cc_field_classifications_id check (id > 0)
);

create sequence managers.s_field_classifications_id owned by managers.t_field_classifications.id;
alter table managers.t_field_classifications alter column id set default nextval('managers.s_field_classifications_id'::regclass);

grant select,insert,update on managers.t_field_classifications to reservation_users_administer;
grant select,insert,update on managers.t_field_classifications to reservation_users_manager;
grant select on managers.t_field_classifications to reservation_users_auditor;
grant select,usage on managers.s_field_classifications_id to reservation_users_administer;
grant select,usage on managers.s_field_classifications_id to reservation_users_manager;

create view users.v_field_classifications with (security_barrier=true) as
  select id, id_external, name_machine, name_human from managers.t_field_classifications
  where is_deleted is false;

grant select on users.v_field_classifications to reservation_users;

/** @todo: create all field types needed for f_requests fields **/

commit transaction;
