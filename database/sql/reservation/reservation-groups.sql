/** Standardized SQL Structure - Groups */
/** This depends on: reservation-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;


/* Note about composite groups (is_composite)
  Instead of having an array of multiple groups assigned to one entity or having a table of group and entity associates, use a group that logically represents multiple groups.
  For example, if I want to add both group_1 and group_2 to entity_a, then I can create group_3 and put all users from group_1 and group_2 in it.
  @todo: I will likely need to create a composite groups table to manage the relations between a composite group and its non-composites for management purposes.

  @todo: with this composite groups design, I can get rid of the user to groups table and need users only to be assigned to a single (composite) group.
         the current flaw with this design may be with access control in creating or auto-creating composite groups.
*/

/** Groups **/
create table s_tables.t_groups (
  id bigint not null,
  id_external bigint,
  id_manager bigint,

  id_sort smallint default 0,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,
  is_composite boolean default false not null,
  is_user boolean default false not null,

  can_manage_paths boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_synced timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  settings json,

  constraint cp_groups primary key (id),

  constraint cc_groups_id check (id > 0),
  constraint cc_groups_id_external check (id_external >= -1),
  constraint cc_groups_name_machine check (name_machine ~ '\w+'),

  constraint cu_groups_id_external unique (id_external),
  constraint cu_groups_name_machine unique (name_machine),

  constraint cf_groups_manager foreign key (id_manager) references s_tables.t_users (id) on delete restrict on update cascade
);

create sequence s_tables.se_groups_id owned by s_tables.t_groups.id;
alter table s_tables.t_groups alter column id set default nextval('s_tables.se_groups_id'::regclass);

grant select,insert,update on s_tables.t_groups to r_reservation_manager, u_reservation_groups_handler;
grant select on s_tables.t_groups to r_reservation_auditor;
grant select,usage on s_tables.se_groups_id to r_reservation_manager;
grant usage on s_tables.se_groups_id to r_reservation, r_reservation_system, u_reservation_groups_handler;

/* Note: id_sort is only needed when directly validating against id or name_machine because both of those are already an index. */
create index i_groups_id_sort_a on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 97;
create index i_groups_id_sort_b on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 98;
create index i_groups_id_sort_c on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 99;
create index i_groups_id_sort_d on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 100;
create index i_groups_id_sort_e on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 101;
create index i_groups_id_sort_f on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 102;
create index i_groups_id_sort_g on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 103;
create index i_groups_id_sort_h on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 104;
create index i_groups_id_sort_i on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 105;
create index i_groups_id_sort_j on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 106;
create index i_groups_id_sort_k on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 107;
create index i_groups_id_sort_l on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 108;
create index i_groups_id_sort_m on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 109;
create index i_groups_id_sort_n on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 110;
create index i_groups_id_sort_o on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 111;
create index i_groups_id_sort_p on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 112;
create index i_groups_id_sort_q on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 113;
create index i_groups_id_sort_r on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 114;
create index i_groups_id_sort_s on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 115;
create index i_groups_id_sort_t on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 116;
create index i_groups_id_sort_u on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 117;
create index i_groups_id_sort_v on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 118;
create index i_groups_id_sort_w on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 119;
create index i_groups_id_sort_x on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 120;
create index i_groups_id_sort_y on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 121;
create index i_groups_id_sort_z on s_tables.t_groups (id_sort) with (fillfactor = 100) where id_sort = 122;

/* only allow one user group per user. */
create unique index i_groups_one_user_group on s_tables.t_groups (id_manager) where is_user;


/*** provide group managers access to manage their groups ***/
create view s_users.v_groups_manage_self with (security_barrier=true) as
  with this_user as (select id from s_users.v_users_locked_not_self)
  select id, id_external, name_machine, name_human, is_locked, is_composite, is_user, can_manage_paths, settings from s_tables.t_groups
    where not is_deleted and id_manager in (select * from this_user);

grant select on s_users.v_groups_manage_self to r_reservation, r_reservation_system;

create view s_users.v_groups_manage_update with (security_barrier=true) as
  select id, id_external, name_machine, name_human, is_locked, is_composite, is_user, can_manage_paths, settings from s_tables.t_groups
    where not is_deleted and id_manager in (select id from s_users.v_users_locked_not_self)
    with check option;

grant update on s_users.v_groups_manage_update to r_reservation, r_reservation_system;


/** each user shall have their own group.
    Note: this violates the naming standard where group name should be first, such as 'group_kday' instead of 'kday_group'.
    This is done specifically because of the id_sort index optimization.
    name_machine is used instad of name_human for the group human name because name_machine is guaranteed to exist.
**/
create function s_administers.f_groups_group_user_insert() returns trigger security definer as $$
  begin
    insert into s_tables.t_groups (id_manager, name_machine, name_human, is_user) values (new.id, new.name_machine || '_user', 'User: ' || new.name_machine, true);

    return null;
  end;
$$ language plpgsql;

alter function s_administers.f_groups_group_user_insert () owner to u_reservation_groups_handler;

create function s_administers.f_groups_group_user_update() returns trigger security definer as $$
  begin
    if (old.name_machine <> new.name_machine) then
      update s_tables.t_groups set name_machine = new.name_machine || '_user', name_human = 'User: ' || new.name_machine, is_locked = new.is_locked, is_deleted = new.is_deleted where id_manager = new.id and is_user;
    elseif (old.is_deleted <> new.is_deleted) then
      update s_tables.t_groups set is_locked = new.is_locked, is_deleted = new.is_deleted where id_manager = new.id and is_user;
    elseif (old.is_locked <> new.is_locked) then
      update s_tables.t_groups set is_locked = new.is_locked where id_manager = new.id and is_user;
    end if;

    return null;
  end;
$$ language plpgsql;

alter function s_administers.f_groups_group_user_update () owner to u_reservation_groups_handler;


create trigger tr_groups_group_user_insert
  after insert on s_tables.t_users
    for each row execute procedure s_administers.f_groups_group_user_insert();

create trigger tr_groups_group_user_update
  after update on s_tables.t_users
    for each row execute procedure s_administers.f_groups_group_user_update();


/** Groups to Users Association **/
create table s_tables.t_group_users (
  id_user bigint not null,
  id_group bigint not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  constraint cp_group_users unique (id_user, id_group),

  constraint cf_group_users_user foreign key (id_user) references s_tables.t_users (id) on delete restrict on update cascade,

  constraint cf_group_users_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade
);

grant select,insert,update on s_tables.t_groups to r_reservation_manager;
grant select on s_tables.t_groups to r_reservation_auditor;


/*** provide current user access to their own information ***/
create view s_users.v_groups_self with (security_barrier=true) as
  with allowed_groups as (select id_group from s_tables.t_group_users where not is_deleted and not is_locked and id_user in (select id from s_users.v_users_locked_not_self))
  select id, id_external, id_manager, name_machine, name_human, is_locked, is_composite, date_created, date_changed, date_synced, can_manage_paths, settings from s_tables.t_groups
    where not is_deleted and id in (select * from allowed_groups);

grant select on s_users.v_groups_self to r_reservation, r_reservation_system;

/*** provide group managers access to manage users assigned to their groups (any user id less than 1000 is reserved/special case, prohibit those). ***/
create view s_users.v_group_users_manage with (security_barrier=true) as
  with managed_groups as (select id from s_tables.t_groups where not is_deleted and id_manager in (select id from s_users.v_users_locked_not_self)),
    available_users as (select id from s_tables.t_users where not is_deleted and not is_locked and not is_system and not is_public)
  select id_user, id_group, is_locked from s_tables.t_group_users
    where not is_deleted and id_group in (select * from managed_groups) and id_user in (select * from available_users);

grant select on s_users.v_group_users_manage to r_reservation, r_reservation_system;

create view s_users.v_group_users_manage_insert with (security_barrier=true) as
  select id_user, id_group from s_tables.t_group_users
    where not is_deleted and id_group in (select id from s_users.v_groups_manage_self) and id_group in (select id_group from s_tables.t_group_users where not is_deleted and not is_locked and id_user in (select id from s_users.v_users_locked_not_self)) and id_user in (select id from s_tables.t_users where not is_deleted and not is_locked and not is_system and not is_public)
    with check option;

grant insert on s_users.v_group_users_manage_insert to r_reservation, r_reservation_system;

create view s_users.v_group_users_manage_update with (security_barrier=true) as
  select id_user, id_group from s_tables.t_group_users
    where not is_deleted and id_group in (select id from s_users.v_groups_manage_self) and id_group in (select id_group from s_tables.t_group_users where not is_deleted and not is_locked and id_user in (select id from s_users.v_users_locked_not_self)) and id_user in (select id from s_tables.t_users where not is_deleted and not is_locked and not is_system and not is_public)
    with check option;

grant update on s_users.v_group_users_manage_update to r_reservation, r_reservation_system;


create trigger tr_groups_users_date_changed_deleted_or_locked
  before update on s_tables.t_group_users
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/** Groups Composites **/
create table s_tables.t_group_composites (
  id_composite bigint not null,
  id_group bigint not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  constraint cu_group_composites_id unique (id_composite, id_group),

  constraint cf_group_composites_composite foreign key (id_composite) references s_tables.t_groups (id) on delete restrict on update cascade,
  constraint cf_group_composites_group foreign key (id_group) references s_tables.t_groups (id) on delete restrict on update cascade
);

grant select,insert,update,delete on s_tables.t_groups to r_reservation_manager;
grant select on s_tables.t_groups to r_reservation_auditor;


/*** provide group managers access to manage composite groups. ***/
create view s_users.v_group_composites with (security_barrier=true) as
  with allowed_groups as (select id from s_users.v_groups_self where not is_locked),
  managed_groups as (select id from s_users.v_groups_manage_self where not is_locked)
  select id_composite, id_group, is_locked from s_tables.t_group_composites
    where not is_deleted and id_group in (select * from managed_groups) or id_group in (select * from allowed_groups);

grant select on s_users.v_group_composites to r_reservation, r_reservation_system;

create view s_users.v_group_composites_manage_insert with (security_barrier=true) as
  select id_user, id_group from s_tables.t_group_users
    where not is_deleted and id_group in (select id_group from s_users.v_group_users_manage where not is_locked)
    with check option;

grant insert on s_users.v_group_composites_manage_insert to r_reservation, r_reservation_system;

create view s_users.v_group_composites_manage_update with (security_barrier=true) as
  select id_user, id_group from s_tables.t_group_users
    where not is_deleted and id_group in (select id_group from s_users.v_group_users_manage where not is_locked)
    with check option;

grant update on s_users.v_group_composites_manage_update to r_reservation, r_reservation_system;


create trigger tr_groups_date_changed_deleted_or_locked
  before update on s_tables.t_group_composites
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



commit transaction;
