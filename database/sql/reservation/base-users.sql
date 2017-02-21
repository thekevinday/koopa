/** Standardized SQL Structure - Users */
/** This depends on: base-main.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;




/** Users **/
create table administers.t_users (
  id bigint not null,
  id_sort smallint not null default 0,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human public.ct_name_person default (null, null, null, null, null, null) not null,

  address_email public.ct_email default (null, null, true) not null,

  is_coordinator boolean default false not null,
  is_deleted boolean default false not null,
  is_locked boolean default false not null,
  is_private boolean default true not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_synced timestamp default localtimestamp not null,
  date_deleted timestamp,

  settings json,

  constraint cp_users_id primary key (id),

  constraint cc_users_id check (id > 0),
  constraint cc_users_id_external check (id_external > 0),

  constraint cu_users_id_external unique (id_external),
  constraint cu_users_name_machine unique (name_machine),
  constraint cu_users_user unique (id, name_machine)
);

create sequence administers.s_users_id owned by administers.t_users.id;
alter table administers.t_users alter column id set default nextval('administers.s_users_id'::regclass);

grant select,insert,update on administers.t_users to reservation_users_administer;
grant select on administers.t_users to reservation_users_auditor;
grant select,usage on administers.s_users_id to reservation_users_administer;
grant usage on administers.s_users_id to reservation_users;

create index ci_users_deleted_not on administers.t_users (id)
  where is_deleted is not true;

create index ci_users_private_not on administers.t_users (id)
  where is_deleted is not true and is_private is not true;

create index ci_users_private_email_not on administers.t_users (id)
  where is_deleted is not true and is_private is not true and (address_email).private is not true;

/** when using current_user reserved function/word the index gets ignored. To prevent this, create a manual/custom index and alter the behavior of the views to be more explicit. **/
create unique index ci_users_current_user on administers.t_users (name_machine) with (fillfactor = 100);

create index ci_users_id_sort_a on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 97;
create index ci_users_id_sort_b on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 98;
create index ci_users_id_sort_c on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 99;
create index ci_users_id_sort_d on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 100;
create index ci_users_id_sort_e on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 101;
create index ci_users_id_sort_f on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 102;
create index ci_users_id_sort_g on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 103;
create index ci_users_id_sort_h on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 104;
create index ci_users_id_sort_i on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 105;
create index ci_users_id_sort_j on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 106;
create index ci_users_id_sort_k on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 107;
create index ci_users_id_sort_l on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 108;
create index ci_users_id_sort_m on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 109;
create index ci_users_id_sort_n on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 110;
create index ci_users_id_sort_o on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 111;
create index ci_users_id_sort_p on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 112;
create index ci_users_id_sort_q on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 113;
create index ci_users_id_sort_r on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 114;
create index ci_users_id_sort_s on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 115;
create index ci_users_id_sort_t on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 116;
create index ci_users_id_sort_u on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 117;
create index ci_users_id_sort_v on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 118;
create index ci_users_id_sort_w on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 119;
create index ci_users_id_sort_x on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 120;
create index ci_users_id_sort_y on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 121;
create index ci_users_id_sort_z on administers.t_users (id_sort) with (fillfactor = 100) where id_sort = 122;


/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence administers.s_users_id start 1000;
alter sequence administers.s_users_id restart;


/*** create hard-coded user ids ***/
insert into administers.t_users (id, name_machine, name_human, is_private) values (1, 'unknown', (null, 'Unknown', null, null, null, 'Unknown'), false);
insert into administers.t_users (id, name_machine, name_human, is_private) values (2, 'system', (null, 'System', null, null, null, 'System'), false);
insert into administers.t_users (id, name_machine, name_human, is_private) values (3, 'console', (null, 'Console', null, null, null, 'Console'), false);
insert into administers.t_users (id, name_machine, name_human, is_private) values (4, 'cron', (null, 'Cron', null, null, null, 'Cron'), false);


/*** provide current user access to their own information ***/
create view users.v_users_self with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, is_locked, is_coordinator, date_created, date_changed, date_synced, settings from administers.t_users
    where is_deleted is not true and (name_machine)::text = (current_user)::text;

grant select on users.v_users_self to reservation_users;

create view users.v_users_self_insert with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, settings from administers.t_users
    where (name_machine)::text = (current_user)::text
    with check option;

grant insert on users.v_users_self_insert to reservation_users;

create view users.v_users_self_update with (security_barrier=true) as
  select address_email, is_private, date_changed, date_synced, settings from administers.t_users
    where is_deleted is not true and date_changed = localtimestamp and (date_synced is null or date_synced = localtimestamp) and (name_machine)::text = (current_user)::text
    with check option;

grant update on users.v_users_self_update to reservation_users;


/**** anonymous user has uid = 1 ****/
create view public.v_users_self with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, is_locked, is_coordinator, date_created, date_changed, date_synced, settings from administers.t_users
    where id = 1 and is_deleted is not true and id_sort = 0;

grant select on public.v_users_self to public_users;


/**** system user has uid = 2 ****/
create view system.v_users_self with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, is_locked, is_coordinator, date_created, date_changed, date_synced, settings from administers.t_users
    where id = 2 and is_deleted is not true and id_sort = 0;

grant select on system.v_users_self to reservation_user;


/*** provide public user information ***/
create view public.v_users with (security_barrier=true) as
  select id, id_sort, name_machine, name_human from administers.t_users
    where (is_deleted is not true and is_private is not true) or (is_deleted is not true and (name_machine)::text = (current_user)::text);

grant select on public.v_users to reservation_users;
grant select on public.v_users to public_users;


/*** provide e-mail address as public information only if it is explicitly allowed ***/
create view public.v_users_email with (security_barrier=true) as
  select id, id_sort, name_machine, name_human, address_email from administers.t_users
    where (is_deleted is not true and is_private is not true and (address_email).private is not true) or (is_deleted is not true and (name_machine)::text = (current_user)::text);

grant select on public.v_users_email to reservation_users;
grant select on public.v_users_email to public_users;


/*** provide managers with the ability to modify accounts ***/
create view managers.v_users with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, is_locked, is_coordinator, date_created, date_changed, date_synced from administers.t_users
    where is_deleted is not true;

grant select on managers.v_users to reservation_users_manager;

create view managers.v_users_insert with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, is_locked, is_coordinator from administers.t_users
    where is_deleted is not true
    with check option;

grant insert on managers.v_users_insert to reservation_users_manager;

create view managers.v_users_update with (security_barrier=true) as
  select id, id_sort, id_external, name_machine, name_human, address_email, is_private, is_locked, is_coordinator, date_changed, date_synced from administers.t_users
    where is_deleted is not true and date_changed = localtimestamp and (date_synced is null or date_synced = localtimestamp)
    with check option;

grant update on managers.v_users_update to reservation_users_manager;


/** Create Materialized views for table based on history **/
create materialized view administers.vm_users_date_created_this_day as select * from administers.t_users where date_trunc('day', date_created) = date_trunc('day', current_timestamp);
create materialized view administers.vm_users_date_created_previous_day as select * from administers.t_users where date_trunc('day', date_created) = date_trunc('day', current_timestamp) - interval '1 day';
create materialized view administers.vm_users_date_created_previous_month as select * from administers.t_users where date_trunc('month', date_created) = date_trunc('month', current_timestamp) - interval '1 month';
create materialized view administers.vm_users_date_created_previous_year as select * from administers.t_users where date_trunc('year', date_created) = date_trunc('year', current_timestamp) - interval '1 year';

create materialized view administers.vm_users_date_changed_this_day as select * from administers.t_users where date_trunc('day', date_changed) = date_trunc('day', current_timestamp);
create materialized view administers.vm_users_date_changed_previous_day as select * from administers.t_users where date_trunc('day', date_changed) = date_trunc('day', current_timestamp) - interval '1 day';
create materialized view administers.vm_users_date_changed_previous_month as select * from administers.t_users where date_trunc('month', date_changed) = date_trunc('month', current_timestamp) - interval '1 month';
create materialized view administers.vm_users_date_changed_previous_year as select * from administers.t_users where date_trunc('year', date_changed) = date_trunc('year', current_timestamp) - interval '1 year';

create materialized view administers.vm_users_date_synced_this_day as select * from administers.t_users where date_trunc('day', date_synced) = date_trunc('day', current_timestamp);
create materialized view administers.vm_users_date_synced_previous_day as select * from administers.t_users where date_trunc('day', date_synced) = date_trunc('day', current_timestamp) - interval '1 day';
create materialized view administers.vm_users_date_synced_previous_month as select * from administers.t_users where date_trunc('month', date_synced) = date_trunc('month', current_timestamp) - interval '1 month';
create materialized view administers.vm_users_date_synced_previous_year as select * from administers.t_users where date_trunc('year', date_synced) = date_trunc('year', current_timestamp) - interval '1 year';

grant select on administers.vm_users_date_created_this_day to reservation_users_administer;
grant select on administers.vm_users_date_created_this_day to reservation_users_manager;
grant select on administers.vm_users_date_created_previous_day to reservation_users_administer;
grant select on administers.vm_users_date_created_previous_day to reservation_users_manager;
grant select on administers.vm_users_date_created_previous_month to reservation_users_administer;
grant select on administers.vm_users_date_created_previous_month to reservation_users_manager;
grant select on administers.vm_users_date_created_previous_year to reservation_users_administer;
grant select on administers.vm_users_date_created_previous_year to reservation_users_manager;

grant select on administers.vm_users_date_changed_this_day to reservation_users_administer;
grant select on administers.vm_users_date_changed_this_day to reservation_users_manager;
grant select on administers.vm_users_date_changed_previous_day to reservation_users_administer;
grant select on administers.vm_users_date_changed_previous_day to reservation_users_manager;
grant select on administers.vm_users_date_changed_previous_month to reservation_users_administer;
grant select on administers.vm_users_date_changed_previous_month to reservation_users_manager;
grant select on administers.vm_users_date_changed_previous_year to reservation_users_administer;
grant select on administers.vm_users_date_changed_previous_year to reservation_users_manager;

grant select on administers.vm_users_date_synced_this_day to reservation_users_administer;
grant select on administers.vm_users_date_synced_this_day to reservation_users_manager;
grant select on administers.vm_users_date_synced_previous_day to reservation_users_administer;
grant select on administers.vm_users_date_synced_previous_day to reservation_users_manager;
grant select on administers.vm_users_date_synced_previous_month to reservation_users_administer;
grant select on administers.vm_users_date_synced_previous_month to reservation_users_manager;
grant select on administers.vm_users_date_synced_previous_year to reservation_users_administer;
grant select on administers.vm_users_date_synced_previous_year to reservation_users_manager;


/*** provide sequence id preservation table ***/
create table administers.t_users_sequences (
  id bigint not null,
  id_user bigint not null,
  name_machine varchar(128) not null,
  is_locked boolean default true not null,
  date_expire timestamp not null,

  constraint cu_users_sequences_id unique (id),
  constraint cu_users_sequences_name_machine unique (name_machine),

  constraint cc_users_sequences_id check (id > 0),

  constraint cf_users_sequences_user foreign key (id_user, name_machine) references administers.t_users (id, name_machine) on delete cascade on update cascade
);

grant select,insert,update,delete on administers.t_users_sequences to reservation_users_administer;
grant select on administers.t_users_sequences to reservation_users_auditor;

/** when using current_user reserved function/word the index gets ignored. To prevent this, create a manual/custom index and alter the behavior of the views to be more explicit. **/
create unique index ci_users_sequences_current_user on administers.t_users_sequences (name_machine) with (fillfactor = 40);

create view public.v_users_sequences_locked with (security_barrier=true) as
  select id, id_user, name_machine, is_locked, date_expire from administers.t_users_sequences
    where is_locked is true and date_expire >= current_timestamp and (name_machine)::text = (current_user)::text
    with check option;

grant select,insert,update,delete on v_users_sequences_locked to reservation_users;

create view public.v_users_sequences_unlocked with (security_barrier=true) as
  select id, id_user, name_machine, is_locked, date_expire from administers.t_users_sequences
    where (is_locked is not true or date_expire < current_timestamp) and (name_machine)::text = (current_user)::text
    with check option;

grant select,update,delete on v_users_sequences_unlocked to reservation_users;



commit transaction;
