/** Reservation SQL Structure - Legal */
/** This depends on: reservation-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/*** Legal Types ***/
create table s_tables.t_legal_types (
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

  constraint cu_legal_types_id unique (id),
  constraint cu_legal_types_name_machine unique (name_machine),

  constraint cc_legal_types_id check (id > -1),
  constraint cc_legal_name_machine check (name_machine ~ '[A-Za-z]\w*')
);

create sequence s_tables.se_legal_types_id owned by s_tables.t_legal_types.id;
alter table s_tables.t_legal_types alter column id set default nextval('s_tables.se_legal_types_id'::regclass);


create index i_legal_types_deleted_not on s_tables.t_legal_types (id)
  where not is_deleted;

create index i_legal_type_locked_not on s_tables.t_legal_types (id)
  where not is_deleted and not is_locked;


create view s_users.v_legal_types with (security_barrier=true) as
  select id, id_external, name_machine, name_human, is_locked from s_tables.t_legal_types
  where not is_deleted and not is_locked;


create trigger tr_legal_types_date_changed_deleted_or_locked
  before update on s_tables.t_legal_types
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/*** Legal / Digital Signatures ***/
create table s_tables.t_signatures (
  id bigint not null,
  id_creator bigint not null,
  id_creator_session bigint not null,
  id_type bigint not null,
  id_request bigint,

  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_deleted timestamp with time zone,

  /* @todo: PGP/GPG based signatures are planned but not currently implemented, the columns (fingerprint and signature) are created but are subject to major change. */
  field_fingerprint varchar(64),
  field_signature text,

  constraint cp_signatures unique (id),

  constraint cc_signatures_id check (id > 0),

  constraint cf_signatures_user foreign key (id_creator) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_signatures_user_session foreign key (id_creator_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_signatures_id_type foreign key (id_type) references s_tables.t_legal_types (id) on delete restrict on update cascade,
  constraint cf_signatures_id_request foreign key (id_request) references s_tables.t_requests (id) on delete restrict on update cascade
);

create sequence s_tables.se_signatures_id owned by s_tables.t_signatures.id;
alter table s_tables.t_signatures alter column id set default nextval('s_tables.se_signatures_id'::regclass);


create index i_signatures_deleted_not on s_tables.t_signatures (id)
  where not is_deleted;


/*** provide current user access to their own information ***/
create view s_users.v_signatures_self with (security_barrier=true) as
  with this_user as (select id from v_users_self_locked_not)
  select id, id_type, id_request, date_created, field_fingerprint, field_signature from s_tables.t_signatures
    where not is_deleted and id_creator in (select * from this_user);


/** provide current user access to insert their own associations **/
create view s_users.v_signatures_self_insert with (security_barrier=true) as
  select id, id_type, id_creator, id_request, field_fingerprint, field_signature from s_tables.t_signatures
    where not is_deleted and id_creator in (select id from v_users_self_locked_not)
    with check option;


create trigger tr_signatures_date_deleted
  before update on s_tables.t_signatures
    for each row execute procedure s_administers.f_common_update_date_deleted();



commit;
