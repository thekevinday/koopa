/** Standardized SQL Structure - Dates **/
/** This depends on: base-main.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/*** Dates: Contexts ***/
create table managers.t_date_contexts (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cu_date_contexts_id unique (id),
  constraint cu_date_contexts_name_machine unique (name_machine),

  constraint cc_date_contexts_id check (id >= 0)
);

create sequence managers.s_date_contexts_id owned by managers.t_date_contexts.id;
alter table managers.t_date_contexts alter column id set default nextval('managers.s_date_contexts_id'::regclass);

grant select,insert,update on managers.t_date_contexts to reservation_users_administer;
grant select,insert,update on managers.t_date_contexts to reservation_users_manager;
grant select on managers.t_date_contexts to reservation_users_auditor;
grant select,usage on managers.s_date_contexts_id to reservation_users_administer;
grant select,usage on managers.s_date_contexts_id to reservation_users_manager;

create index ci_date_contexts_deleted_not on managers.t_date_contexts (id)
  where is_deleted is not true;

create index ci_date_contexts_public on managers.t_date_contexts (id)
  where is_deleted is not true and is_locked is not true;


create view requesters.v_date_contexts with (security_barrier=true) as
  select id, name_machine, name_human from managers.t_date_contexts
  where is_deleted is not true and is_locked is not true;

grant select on requesters.v_date_contexts to reservation_users_requester;

insert into managers.t_date_contexts (id, name_machine, name_human) values (0, 'none', 'None');
insert into managers.t_date_contexts (name_machine, name_human) values ('rehearsal', 'Rehearsal / Setup');
insert into managers.t_date_contexts (name_machine, name_human) values ('event', 'Event / Meeting');
insert into managers.t_date_contexts (name_machine, name_human) values ('cleanup', 'Cleanup / Breakdown');
