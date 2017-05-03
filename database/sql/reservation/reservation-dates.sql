/** Reservation SQL Structure - Dates **/
/** This depends on: reservation-main.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/*** Dates: Contexts ***/
create table s_tables.t_date_contexts (
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

  constraint cu_date_contexts_id unique (id),
  constraint cu_date_contexts_name_machine unique (name_machine),

  constraint cc_date_contexts_id check (id >= 0),
  constraint cc_date_contexts_name_machine check (name_machine ~ '[A-Za-z]\w*')
);

create sequence s_tables.se_date_contexts_id owned by s_tables.t_date_contexts.id;
alter table s_tables.t_date_contexts alter column id set default nextval('s_tables.se_date_contexts_id'::regclass);


create index i_date_contexts_deleted_not on s_tables.t_date_contexts (id)
  where not is_deleted;

create index i_date_contexts_locked_not on s_tables.t_date_contexts (id)
  where not is_deleted and not is_locked;


create view s_requesters.v_date_contexts with (security_barrier=true) as
  select id, id_external, name_machine, name_human, is_locked from s_tables.t_date_contexts
  where not is_deleted;


create trigger tr_date_contexts_update_date_changed_deleted_or_locked
  before update on s_tables.t_date_contexts
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



commit transaction;
