/** Standardized SQL Structure - Files **/
/** This depends on: standard-users.sql, standard-types.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;


create table s_tables.t_files (
  id bigint not null,
  id_creator bigint not null,
  id_creator_session bigint not null,
  id_type bigint not null,
  id_group bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,
  name_extension varchar(64) not null,

  is_private boolean default true not null,
  is_locked boolean default false not null,
  is_deleted boolean default false not null,
  is_system boolean default false not null,
  is_user boolean default false not null,

  field_size bigint not null,
  field_width bigint,
  field_height bigint,
  field_data bytea not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  constraint cp_files primary key (id),

  constraint cu_files_name_machine unique (name_machine),

  constraint cc_files_id check (id > 0),
  constraint cc_files_name_machine check (name_machine ~ '[A-Za-z]\w*'),
  constraint cc_files_name_extension check (name_extension ~ '[A-Za-z]\w*'),
  constraint cc_field_size check (field_size >= 0),
  constraint cc_field_width check (field_width >= 0),
  constraint cc_field_height check (field_height >= 0),

  constraint cf_files_id_creator foreign key (id_creator) references s_tables.t_users (id) on delete cascade on update cascade,
  constraint cf_files_id_creator_session foreign key (id_creator_session) references s_tables.t_users (id) on delete cascade on update cascade,
  constraint cf_files_id_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade
);

create sequence s_tables.se_files_id owned by s_tables.t_files.id;
alter table s_tables.t_files alter column id set default nextval('s_tables.se_files_id'::regclass);

alter table s_tables.t_users add constraint cf_users_image_original foreign key (image_original) references s_tables.t_files (id) on delete cascade on update cascade;
alter table s_tables.t_users add constraint cf_users_image_cropped foreign key (image_cropped) references s_tables.t_files (id) on delete cascade on update cascade;
alter table s_tables.t_users add constraint cf_users_image_icon foreign key (image_icon) references s_tables.t_files (id) on delete cascade on update cascade;


create index i_files_deleted_not on s_tables.t_files (id)
  where not is_deleted;

create index i_files_private_not on s_tables.t_files (id)
  where not is_deleted and not is_private;

create index i_files_locked_not on s_tables.t_files (id)
  where not is_deleted and not is_locked;

create index i_files_public on s_tables.t_files (id)
  where not is_deleted and not is_locked and not is_private;


create view s_users.v_files with (security_barrier=true) as
  with allowed_groups as (select id from s_users.v_groups_self)
  select id, id_type, id_group, name_machine, name_human, name_extension, is_private, field_size, field_width, field_height, field_data, date_created, date_changed from s_tables.t_files
  where not is_deleted and (not is_locked or id_group in (select * from allowed_groups)) and (not is_private or (is_private and id_group in (select * from allowed_groups)));

create view public.v_files with (security_barrier=true) as
  select id, id_type, NULL::bigint as id_group, name_machine, name_human, name_extension, NULL::bool as is_private, field_size, field_width, field_height, field_data, NULL::bool as date_created, NULL::bool as date_changed from s_tables.t_files
  where not is_deleted and not is_locked and not is_private;


create trigger tr_files_date_changed_deleted_or_locked
  before update on s_tables.t_files
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_files_enforce_creator_and_session_ids
  before insert on s_tables.t_files
    for each row execute procedure s_administers.f_common_enforce_creator_and_session_ids();



commit transaction;
