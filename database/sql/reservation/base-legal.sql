/** Standardized SQL Structure - Legal */
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/*** Legal Types ***/
create table managers.t_legal_types (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cu_legal_types_id unique (id),
  constraint cu_legal_types_name_machine unique (name_machine),

  constraint cc_legal_types_id check (id >= 0)
);

create sequence managers.s_legal_types_id owned by managers.t_legal_types.id;
alter table managers.t_legal_types alter column id set default nextval('managers.s_legal_types_id'::regclass);

grant select,insert,update on managers.t_legal_types to reservation_users_administer;
grant select,insert,update on managers.t_legal_types to reservation_users_manager;
grant select on managers.t_legal_types to reservation_users_auditor;
grant select,usage on managers.s_legal_types_id to reservation_users_administer;
grant select,usage on managers.s_legal_types_id to reservation_users_manager;

create index ci_legal_types_deleted_not on managers.t_legal_types (id)
  where is_deleted is not true;

create index ci_legal_types_public on managers.t_legal_types (id)
  where is_deleted is not true and is_locked is not true;


create view users.v_legal_types with (security_barrier=true) as
  select id, id_external, name_machine, name_human from managers.t_legal_types
  where is_deleted is not true and is_locked is not true;

grant select on users.v_legal_types to reservation_users_requester;


insert into managers.t_legal_types (id, name_machine, name_human) values (0, 'none', 'None');



/*** Legal / Digital Signatures ***/
create table managers.t_signatures (
  id bigint not null,
  id_type bigint not null,
  id_signer bigint not null,
  id_request bigint,

  name_machine_signer varchar(128) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_deleted timestamp,

  /* @todo: PGP/GPG based signatures are planned but not currently implemented, the columns (fingerprint and signature) are created but are subject to major change. */
  fingerprint varchar(64),
  signature text,

  constraint cu_signatures_id unique (id),

  constraint cc_signatures_id check (id > 0),

  constraint cf_signatures_id_creator foreign key (id_signer, name_machine_signer) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_signatures_id_type foreign key (id_type) references managers.t_legal_types (id) on delete restrict on update cascade,
  constraint cf_signatures_id_request foreign key (id_request) references managers.t_requests (id) on delete restrict on update cascade
);

create sequence managers.s_signatures_id owned by managers.t_signatures.id;
alter table managers.t_signatures alter column id set default nextval('managers.s_signatures_id'::regclass);

grant select,insert,update on managers.t_signatures to reservation_users_administer;
grant select,insert,update on managers.t_signatures to reservation_users_manager;
grant select on managers.t_signatures to reservation_users_auditor;
grant select,usage on managers.s_signatures_id to reservation_users_administer;
grant select,usage on managers.s_signatures_id to reservation_users_manager;
grant usage on managers.s_signatures_id to reservation_users;

create index ci_signatures_deleted_not on managers.t_signatures (id)
  where is_deleted is not true;

create index ci_signatures_approved on managers.t_signatures (id)
  where is_deleted is not true and is_cancelled is not true and is_approved is true;

create index ci_signatures_approved_cancelled on managers.t_signatures (id)
  where is_deleted is not true and is_cancelled is true and is_approved is true;


/*** provide current user access to their own information ***/
create view users.v_signatures_self with (security_barrier=true) as
  select id, id_type, id_request, date_created, fingerprint, signature from managers.t_signatures
    where is_deleted is not true and (name_machine_signer)::text = (current_user)::text;

grant select on users.v_signatures_self to reservation_users;


/** provide current user access to insert their own associations **/
create view users.v_signatures_self_insert with (security_barrier=true) as
  select id, id_type, id_signer, name_machine_signer, id_request, fingerprint, signature from managers.t_signatures
    where is_deleted is not true and (name_machine_signer)::text = (current_user)::text
    with check option;

grant insert on users.v_signatures_self_insert to reservation_users;
