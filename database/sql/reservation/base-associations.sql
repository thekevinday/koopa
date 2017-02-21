/** Standardized SQL Structure - Content **/
/** This depends on: base-fields.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/*** Associations ***/
create table managers.t_associations (
  id bigint not null,
  id_owner bigint not null,
  id_group bigint,
  id_coordinator bigint not null,

  name_machine varchar(128) not null,
  name_machine_owner varchar(128) not null,
  name_machine_coordinator varchar(128) not null,
  name_human varchar(256) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  field_affiliation bigint not null,
  field_classification bigint not null,

  constraint cu_associations_id unique (id),
  constraint cu_associations_name_machine unique (name_machine),

  constraint cc_associations_id check (id > 0),

  constraint cf_associations_id_owner foreign key (id_owner, name_machine_owner) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_associations_id_coordinator foreign key (id_coordinator, name_machine_coordinator) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_associations_group foreign key (id_group) references managers.t_groups (id) on delete restrict on update cascade,
  constraint cf_associations_field_affiliation foreign key (field_affiliation) references managers.t_field_affiliations (id) on delete restrict on update cascade,
  constraint cf_associations_field_classification foreign key (field_classification) references managers.t_field_classifications (id) on delete restrict on update cascade
);

create sequence managers.s_associations_id owned by managers.t_associations.id;
alter table managers.t_associations alter column id set default nextval('managers.s_associations_id'::regclass);

grant select,insert,update on managers.t_associations to reservation_users_administer;
grant select,insert,update on managers.t_associations to reservation_users_manager;
grant select on managers.t_associations to reservation_users_auditor;
grant select,usage on managers.s_associations_id to reservation_users_administer;
grant select,usage on managers.s_associations_id to reservation_users_manager;
grant usage on managers.s_associations_id to reservation_users;


/*** provide current user access to their own information ***/
create view users.v_associations_self with (security_barrier=true) as
  with allowed_groups as (select id from users.v_groups_self)
  select id, id_owner, id_group, id_coordinator, name_machine, name_machine_owner, name_machine_cooordinator, name_human, date_created, date_changed, field_affiliation, field_classification from managers.t_associations
    where is_deleted is not true and ((group_id is null and (name_machine_owner)::text = (current_user)::text) or group_id in (select * from allowed_groups));

grant select on users.v_associations_self to reservation_users;


/*** provide current user access to associations who they are assigned as the coordinator of ***/
create view users.v_associations_coordinator_self with (security_barrier=true) as
  with allowed_groups as (select id from users.v_groups_self)
  select id, id_owner, id_group, id_coordinator, name_machine, name_machine_owner, name_machine_cooordinator, name_human, date_created, date_changed, field_affiliation, field_classification from managers.t_associations
    where is_deleted is not true and (name_machine_coordinator)::text = (current_user)::text);

grant select on users.v_associations_coordinator_self to reservation_users;


/** provide current user access to insert their own associations **/
create view users.v_associations_self_insert with (security_barrier=true) as
  with allowed_groups as (select id from users.v_groups_self)
  select id, id_owner, id_group, id_coordinator, name_machine, name_machine_owner, name_machine_cooordinator, name_human, field_affiliation, field_classification from managers.t_associations
    where is_deleted is not true and (group_id is null or group_id in (select * from allowed_groups)) and (name_machine_owner)::text = (current_user)::text
    with check option;

grant insert on users.v_associations_self_insert to reservation_users;


/** provide current user access to update their own associations **/
create view public.v_associations_self_update with (security_barrier=true) as
  with allowed_groups as (select id from users.v_groups_self)
  select id, id_group, id_coordinator, name_machine, name_machine_cooordinator, name_human, date_changed, field_affiliation, field_classification from managers.t_associations
    where is_deleted is not true and (group_id is null or group_id in (select * from allowed_groups)) and date_changed = localtimestamp and (name_machine_owner)::text = (current_user)::text
    with check option;

grant insert on users.v_associations_self_update to reservation_users;

commit transaction;
