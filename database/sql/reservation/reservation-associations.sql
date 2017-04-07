/** Standardized SQL Structure - Associations **/
/** This depends on: reservation-fields.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;



/*** Associations ***/
create table s_tables.t_associations (
  id bigint not null,
  id_creator bigint not null,
  id_creator_session bigint not null,
  id_manager bigint not null,
  id_coordinator bigint not null,
  id_group bigint,
  id_sort smallint default 0,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_approved boolean default false not null,
  is_cancelled boolean default false not null,
  is_denied boolean default false not null,
  is_troubled boolean default false not null,
  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_synced timestamp default localtimestamp not null,
  date_approved timestamp,
  date_cancelled timestamp,
  date_denied timestamp,
  date_troubled timestamp,
  date_locked timestamp,
  date_deleted timestamp,

  field_affiliation bigint,
  field_classification bigint,

  constraint cu_associations_id unique (id),
  constraint cu_associations_name_machine unique (name_machine),

  constraint cc_associations_id check (id > 0),
  constraint cc_associations_name_machine check (name_machine ~ '[A-Za-z]\w*'),

  constraint cf_associations_manager foreign key (id_manager) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_associations_creator foreign key (id_creator) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_associations_creator_session foreign key (id_creator_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_associations_coordinator foreign key (id_coordinator) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_associations_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade,
  constraint cf_associations_field_affiliation foreign key (field_affiliation) references s_tables.t_field_affiliations (id) on delete restrict on update cascade,
  constraint cf_associations_field_classification foreign key (field_classification) references s_tables.t_field_classifications (id) on delete restrict on update cascade
);

create sequence s_tables.se_associations_id owned by s_tables.t_associations.id;
alter table s_tables.t_associations alter column id set default nextval('s_tables.se_associations_id'::regclass);

grant select,insert,update on s_tables.t_associations to r_reservation_manager;
grant select on s_tables.t_associations to r_reservation_auditor;
grant select,usage on s_tables.se_associations_id to r_reservation_manager;
grant usage on s_tables.se_associations_id to r_reservation_requester, r_reservation_reviewer;

/* @todo: should associations allow names that begin with numbers?
/* Note: id_sort is not needed when directly validating against id or name_machine because both of those are already an index. */
create index i_associations_id_sort_a on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 97;
create index i_associations_id_sort_b on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 98;
create index i_associations_id_sort_c on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 99;
create index i_associations_id_sort_d on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 100;
create index i_associations_id_sort_e on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 101;
create index i_associations_id_sort_f on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 102;
create index i_associations_id_sort_g on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 103;
create index i_associations_id_sort_h on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 104;
create index i_associations_id_sort_i on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 105;
create index i_associations_id_sort_j on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 106;
create index i_associations_id_sort_k on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 107;
create index i_associations_id_sort_l on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 108;
create index i_associations_id_sort_m on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 109;
create index i_associations_id_sort_n on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 110;
create index i_associations_id_sort_o on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 111;
create index i_associations_id_sort_p on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 112;
create index i_associations_id_sort_q on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 113;
create index i_associations_id_sort_r on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 114;
create index i_associations_id_sort_s on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 115;
create index i_associations_id_sort_t on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 116;
create index i_associations_id_sort_u on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 117;
create index i_associations_id_sort_v on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 118;
create index i_associations_id_sort_w on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 119;
create index i_associations_id_sort_x on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 120;
create index i_associations_id_sort_y on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 121;
create index i_associations_id_sort_z on s_tables.t_associations (id_sort) with (fillfactor = 100) where id_sort = 122;


/*** provide current user access to their own information ***/
create view s_users.v_associations_self with (security_barrier=true) as
  with this_user as (select id from s_users.v_users_locked_not_self),
    allowed_groups as (select id from s_users.v_groups_self)
  select id, id_manager, id_coordinator, id_group, id_sort, name_machine, name_human, is_approved, is_cancelled, is_denied, is_troubled, is_locked, date_created, date_changed, date_synced, date_approved, date_cancelled, date_denied, date_troubled, date_locked, field_affiliation, field_classification from s_tables.t_associations
    where not is_deleted and (id_manager in (select * from this_user) or id_group in (select * from allowed_groups));

grant select on s_users.v_associations_self to r_reservation_requester, r_reservation_reviewer;


/*** provide current user access to associations who they are assigned as the manager of ***/
create view s_users.v_associations_manage with (security_barrier=true) as
  with this_user as (select id from s_users.v_users_locked_not_self)
  select id, id_creator, id_coordinator, id_group, id_sort, name_machine, name_human, is_approved, is_cancelled, is_denied, is_troubled, is_locked, date_created, date_changed, date_synced, date_approved, date_cancelled, date_denied, date_troubled, date_locked, field_affiliation, field_classification from s_tables.t_associations
    where not is_deleted and id_manager in (select * from this_user);

grant select on s_users.v_associations_manage to r_reservation_requester, r_reservation_reviewer;


/*** provide current user access to associations who they are assigned as the coordinator of ***/
create view s_users.v_associations_coordinate with (security_barrier=true) as
  with this_user as (select id from s_users.v_users_locked_not_self)
  select id, id_creator, id_manager, id_group, id_sort, name_machine, name_human, is_approved, is_cancelled, is_denied, is_troubled, is_locked, date_created, date_changed, date_synced, date_approved, date_cancelled, date_denied, date_troubled, date_locked, field_affiliation, field_classification from s_tables.t_associations
    where not is_deleted and id_coordinator in (select * from this_user);

grant select on s_users.v_associations_coordinate to r_reservation_requester, r_reservation_reviewer;


/** provide current user access to insert their own associations (with them as the manager) **/
create view s_users.v_associations_self_insert with (security_barrier=true) as
  select id_manager, id_group, id_coordinator, name_machine, name_human, field_affiliation, field_classification from s_tables.t_associations
    where not is_deleted and id_manager in (select id from s_users.v_users_locked_not_self)
    with check option;

grant insert on s_users.v_associations_self_insert to r_reservation_requester, r_reservation_reviewer;


/** provide current user access to update associations they manager **/
create view s_users.v_associations_self_update with (security_barrier=true) as
  select id_manager, id_group, id_coordinator, name_machine, name_human, date_changed, field_affiliation, field_classification from s_tables.t_associations
    where not is_deleted and id_manager in (select id from s_users.v_users_locked_not_self)
    with check option;

grant update on s_users.v_associations_self_update to r_reservation_requester, r_reservation_reviewer;


create trigger tr_associations_update_date_changed_deleted_or_locked
  before update on s_tables.t_associations
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_associations_enforce_creator_and_session_ids
  before insert on s_tables.t_associations
    for each row execute procedure s_administers.f_common_enforce_creator_and_session_ids();



commit transaction;
