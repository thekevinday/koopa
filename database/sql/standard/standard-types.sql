/** Standardized SQL Structure - Logs - Types */
/** This depends on: standard-main.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/*** provide HTTP status codes ***/
create table s_tables.t_type_http_status_codes (
  id smallint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  constraint cp_log_type_http_status_codes primary key (id),

  constraint cu_log_type_http_status_codes_user unique (name_machine),

  constraint cc_log_type_http_status_codes_id check (id >= 0 and id < 600)
);

create sequence s_tables.se_log_type_http_status_codes_id owned by s_tables.t_type_http_status_codes.id;
alter table s_tables.t_type_http_status_codes alter column id set default nextval('s_tables.se_log_type_http_status_codes_id'::regclass);

grant select,insert,update on s_tables.t_type_http_status_codes to r_standard_administer;
grant select on s_tables.t_type_http_status_codes to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_type_http_status_codes_id to r_standard_administer;

create view public.v_log_type_http_status_codes with (security_barrier=true) as
  select id, name_machine, name_human from s_tables.t_type_http_status_codes;

grant select on public.v_log_type_http_status_codes to r_standard, r_standard_public, r_standard_system;


create trigger tr_log_type_http_status_codes_date_changed_deleted_or_locked
  before update on s_tables.t_type_http_status_codes
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/*** provide mime type category id and names ***/
create table s_tables.t_type_mime_categorys (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  field_category varchar(64),

  constraint cp_types_mime_categorys primary key (id),

  constraint cu_types_mime_categorys_name_machine unique (name_machine),

  constraint cc_types_mime_categorys_id check (id > -1),
  constraint cc_types_mime_categorys_name_machine check (name_machine ~ '[A-Za-z]\w*')
);

grant select,insert,update on s_tables.t_type_mime_categorys to r_standard_administer;

create view public.v_types_mime_categorys with (security_barrier=true) as
  select id, name_machine, name_human, is_locked from s_tables.t_type_mime_categorys
  where not is_deleted;

grant select on public.v_types_mime_categorys to r_standard, r_standard_public, r_standard_system;

grant select,insert,update on s_tables.t_type_mime_categorys to r_standard_administer;


create view public.v_types_mime_categorys_locked_not with (security_barrier=true) as
  select id, name_machine, name_human, field_category from s_tables.t_type_mime_categorys
  where not is_deleted and not is_locked;

grant select on public.v_types_mime_categorys_locked_not to r_standard, r_standard_public, r_standard_system;


create trigger tr_types_mime_categorys_date_changed_deleted_or_locked
  before update on s_tables.t_type_mime_categorys
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/*** provide mime type ids and names ***/
create table s_tables.t_type_mime_types (
  id bigint not null,
  id_category bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  field_extension varchar(64),
  field_mime varchar(128),

  constraint cc_types_mime_types_id check (id > -1),

  constraint cu_types_mime_types_mime_type unique (id, id_category, field_extension, field_mime),

  constraint cf_types_mime_types_id foreign key (id_category) references s_tables.t_type_mime_categorys (id) on delete restrict on update cascade
);

grant select,insert,update on s_tables.t_type_mime_types to r_standard_administer;

create view public.v_types_mime_types with (security_barrier=true) as
  select id, id_category, name_machine, name_human, field_extension, field_mime, is_locked from s_tables.t_type_mime_types
  where not is_deleted;

grant select on public.v_types_mime_types to r_standard, r_standard_public, r_standard_system;

create view public.v_types_mime_types_locked_not with (security_barrier=true) as
  select id, id_category, name_machine, name_human, field_extension, field_mime, is_locked from s_tables.t_type_mime_types
  where not is_deleted and not is_locked;

grant select on public.v_types_mime_types to r_standard, r_standard_public, r_standard_system;


create trigger tr_types_mime_types_date_changed_deleted_or_locked
  before update on s_tables.t_type_mime_types
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



commit transaction;
