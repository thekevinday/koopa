/** Standardized SQL Structure - Groups */
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;


/* Note about composite groups (is_composite)
  Instead of having an array of multiple groups assigned to one entity or having a table of group and entity associates, use a group that logically represents multiple groups.
  For example, if I want to add both group_1 and group_2 to entity_a, then I can create group_3 and put all users from group_1 and group_2 in it.
  @todo: I will likely need to create a composite groups table to manage the relations between a composite group and its non-composites for management purposes.

  @todo: with this composite groups design, I can get rid of the user to groups table and need users only to be assigned to a single (composite) group.
         the current flaw with this design may be with access control in creating or auto-creating composite groups.
*/

/** Groups **/
create table managers.t_groups (
  id bigint not null,
  id_sort smallint not null default 0,
  id_external bigint,
  id_manager bigint,

  name_machine varchar(128) not null,
  name_machine_creator varchar(128) not null,
  name_machine_manager varchar(128) not null,
  name_human varchar(256) not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_synced timestamp default localtimestamp not null,
  date_deleted timestamp,

  is_deleted boolean default false not null,
  is_locked boolean default false not null,
  is_composite boolean default false not null,

  can_manage_paths boolean default false not null,

  settings json,

  constraint cp_groups_id primary key (id),

  constraint cc_groups_id check (id > 0),
  constraint cc_groups_id_external check (id_external > 0),

  constraint cu_groups_id_external unique (id_external),
  constraint cu_groups_name_machine unique (name_machine),
  constraint cu_groups_user unique (id, name_machine),

  constraint cf_t_groups_manager foreign key (id_manager, name_machine_manager) references administers.t_users (id, name_machine) on delete restrict on update cascade
);

create sequence managers.s_groups_id owned by managers.t_groups.id;
alter table managers.t_groups alter column id set default nextval('managers.s_groups_id'::regclass);

grant select,insert,update on managers.t_groups to reservation_users_administer;
grant select,insert,update on managers.t_groups to reservation_users_manager;
grant select on managers.t_groups to reservation_users_auditor;
grant select,usage on managers.s_groups_id to reservation_users_administer;
grant select,usage on managers.s_groups_id to reservation_users_manager;
grant usage on managers.s_groups_id to reservation_users;

create index ci_groups_id_sort_a on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 97;
create index ci_groups_id_sort_b on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 98;
create index ci_groups_id_sort_c on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 99;
create index ci_groups_id_sort_d on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 100;
create index ci_groups_id_sort_e on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 101;
create index ci_groups_id_sort_f on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 102;
create index ci_groups_id_sort_g on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 103;
create index ci_groups_id_sort_h on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 104;
create index ci_groups_id_sort_i on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 105;
create index ci_groups_id_sort_j on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 106;
create index ci_groups_id_sort_k on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 107;
create index ci_groups_id_sort_l on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 108;
create index ci_groups_id_sort_m on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 109;
create index ci_groups_id_sort_n on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 110;
create index ci_groups_id_sort_o on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 111;
create index ci_groups_id_sort_p on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 112;
create index ci_groups_id_sort_q on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 113;
create index ci_groups_id_sort_r on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 114;
create index ci_groups_id_sort_s on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 115;
create index ci_groups_id_sort_t on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 116;
create index ci_groups_id_sort_u on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 117;
create index ci_groups_id_sort_v on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 118;
create index ci_groups_id_sort_w on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 119;
create index ci_groups_id_sort_x on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 120;
create index ci_groups_id_sort_y on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 121;
create index ci_groups_id_sort_z on managers.t_groups (id_sort) with (fillfactor = 100) where id_sort = 122;




/** Groups to Users Association **/
create table managers.t_group_users (
  id_user bigint not null,
  id_group bigint not null,

  name_machine_user varchar(128) not null,

  constraint cu_groups_id unique (id_user, id_group),

  constraint cf_t_group_users_user foreign key (id_user, name_machine_user) references administers.t_users (id, name_machine) on delete cascade on update cascade,
  constraint cf_t_group_users_group foreign key (id_group) references managers.t_groups (id) on delete cascade on update cascade
);

grant select,insert,update on managers.t_groups to reservation_users_administer;
grant select,insert,update on managers.t_groups to reservation_users_manager;
grant select on managers.t_groups to reservation_users_auditor;


/*** provide group manages access to manage users assigned to their groups ***/
create view users.v_group_users_manage with (security_barrier=true) as
  select gu.id_user, gu.id_group from managers.t_group_users gu
    inner join managers.t_groups g on g.id = gu.id_group
    where g.is_deleted is not true and (g.name_machine_manager)::text = (current_user)::text;

grant select, insert, update, delete on users.v_group_users_manage to reservation_users;


/*** provide current user access to their own information ***/
create view users.v_groups_self with (security_barrier=true) as
  select g.id, g.id_sort, g.id_external, g.id_manager, g.name_machine, g.name_human, g.is_locked, g.is_composite, g.date_created, g.date_changed, g.date_synced, g.can_manage_paths, g.settings from managers.t_groups g
    inner join managers.t_group_users gu on g.id = gu.id_group
    where g.is_deleted is not true and (gu.name_machine_user)::text = (current_user)::text;

grant select on users.v_groups_self to reservation_users;


/*** provide group manages access to manage users their groups ***/
create view users.v_groups_manage with (security_barrier=true) as
  select g.id, g.id_sort, g.id_external, g.name_machine, g.name_human, g.is_locked, g.is_composite, g.date_changed, g.date_synced, g.can_manage_paths, g.settings from managers.t_groups g
    inner join managers.t_group_users gu on g.id = gu.id_group
    where g.is_deleted is not true and (date_changed is null or date_changed = localtimestamp) and (date_synced is null or date_synced = localtimestamp) and (gu.name_machine_user)::text = (current_user)::text;

grant select, insert, update on users.v_groups_manage to reservation_users;




/** Groups Composites **/
create table managers.t_group_composites (
  id_composite bigint not null,
  id_group bigint not null,

  constraint cu_group_composites_id unique (id_composite, id_group),

  constraint cf_t_group_composites_composite foreign key (id_composite) references managers.t_groups (id) on delete cascade on update cascade,
  constraint cf_t_group_composites_group foreign key (id_group) references managers.t_groups (id) on delete cascade on update cascade
);

grant select,insert,update on managers.t_groups to reservation_users_administer;
grant select,insert,update on managers.t_groups to reservation_users_manager;
grant select on managers.t_groups to reservation_users_auditor;


/*** provide group manages access to composite groups containing groups they belong to. ***/
create view users.v_group_composites with (security_barrier=true) as
  with allowed_groups as (select id from users.v_groups_self)
  select gc.id_composite, gc.id_group from managers.t_group_composites gc
    inner join managers.t_groups g on g.id = gc.id_group
    where g.is_deleted is not true and (g.is_locked is not true or (g.name_machine_manager)::text = (current_user)::text) and ((g.name_machine_manager)::text = (current_user)::text or gc.id_group in (select * from allowed_groups));

grant select on users.v_group_composites to reservation_users;


commit transaction;
