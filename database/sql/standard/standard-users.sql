/** Standardized SQL Structure - Users */
/** This depends on: standard-main.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/** Users **/
/* Note: is_public and is_private have two different contexts, is_public refers to the r_standard_public role and is_private refers to making certain user fields public/private within the system to a certain degree. */
create table s_tables.t_users (
  id bigint not null,
  id_external bigint,

  id_sort smallint default 0,

  name_machine varchar(128) not null,
  name_human public.ct_name_person default (null, null, null, null, null, null) not null,

  address_email public.ct_email default (null, null, true) not null,

  is_administer boolean default false not null,
  is_manager boolean default false not null,
  is_auditor boolean default false not null,
  is_publisher boolean default false not null,
  is_insurer boolean default false not null,
  is_financer boolean default false not null,
  is_reviewer boolean default false not null,
  is_editor boolean default false not null,
  is_drafter boolean default false not null,
  is_requester boolean default false not null,
  is_roler boolean default false not null,
  is_system boolean default false not null,
  is_public boolean default false not null,
  is_locked boolean default false not null,
  is_private boolean default true not null,
  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_synced timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  image_original bytea,
  image_cropped bytea,
  image_icon bytea,

  settings json,

  constraint cp_users primary key (id),

  constraint cc_users_id check (id > 0),
  constraint cc_users_id_external check (id_external >= -1),

  constraint cu_users_id_external unique (id_external),
  constraint cu_users_name_machine unique (name_machine)
);

create sequence s_tables.se_users_id owned by s_tables.t_users.id;
alter table s_tables.t_users alter column id set default nextval('s_tables.se_users_id'::regclass);


create index i_users_deleted_not on s_tables.t_users (id)
  where not is_deleted;

create index i_users_private_not on s_tables.t_users (id)
  where not is_deleted and not is_private;

create index i_users_locked_not on s_tables.t_users (id)
  where not is_deleted and not is_locked;

create index i_users_private_email_not on s_tables.t_users (id)
  where not is_deleted and not is_private and not (address_email).private;

/* Note: id_sort is not needed when directly validating against id or name_machine because both of those are already an index. */
create index i_users_id_sort_a on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 97;
create index i_users_id_sort_b on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 98;
create index i_users_id_sort_c on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 99;
create index i_users_id_sort_d on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 100;
create index i_users_id_sort_e on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 101;
create index i_users_id_sort_f on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 102;
create index i_users_id_sort_g on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 103;
create index i_users_id_sort_h on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 104;
create index i_users_id_sort_i on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 105;
create index i_users_id_sort_j on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 106;
create index i_users_id_sort_k on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 107;
create index i_users_id_sort_l on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 108;
create index i_users_id_sort_m on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 109;
create index i_users_id_sort_n on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 110;
create index i_users_id_sort_o on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 111;
create index i_users_id_sort_p on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 112;
create index i_users_id_sort_q on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 113;
create index i_users_id_sort_r on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 114;
create index i_users_id_sort_s on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 115;
create index i_users_id_sort_t on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 116;
create index i_users_id_sort_u on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 117;
create index i_users_id_sort_v on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 118;
create index i_users_id_sort_w on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 119;
create index i_users_id_sort_x on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 120;
create index i_users_id_sort_y on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 121;
create index i_users_id_sort_z on s_tables.t_users (id_sort) with (fillfactor = 100) where id_sort = 122;

/*** provide current user access to their own information (system users are not allowed to update their account) ***/
create view s_users.v_users_self with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, is_roler, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted and (name_machine)::text = (current_user)::text;

create view public.v_users_self_session with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, is_roler, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted and (name_machine)::text = (session_user)::text;

create view public.v_users_self_locked_not with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, is_roler, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted and not is_locked and (name_machine)::text = (current_user)::text;

create view public.v_users_self_exists with (security_barrier=true) as
  select id, name_machine, is_system, is_public, is_locked, is_deleted, image_original, image_cropped, image_icon from s_tables.t_users
    where (name_machine)::text = (current_user)::text;

create view s_users.v_users_self_insert with (security_barrier=true) as
  select id_external, name_human, address_email, is_private, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted and not is_locked and not is_system and not is_public and (name_machine)::text = (current_user)::text
    with check option;

create view s_users.v_users_self_update with (security_barrier=true) as
  select address_email, is_private, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted and not is_locked and not is_system and not is_public and (name_machine)::text = (current_user)::text
    with check option;


/**** anonymous user has uid = 1 ****/
create view public.v_users_self with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, is_roler, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted and id = 1;


/*** provide public user information ***/
create view public.v_users with (security_barrier=true) as
  select id, null::bigint as id_external, id_sort, name_machine, name_human, null::public.ct_email as address_email, null::bool as is_administer, null::bool as is_manager, null::bool as is_auditor, null::bool as is_publisher, null::bool as is_insurer, null::bool as is_financer, null::bool as is_reviewer, null::bool as is_editor, null::bool as is_drafter, null::bool as is_requester, is_system, is_public, null::bool as is_locked, is_private, is_deleted, null::bool as is_roler, null::timestamp as date_created, null::timestamp as date_changed, null::timestamp as date_synced, null::timestamp as date_locked, null::timestamp as date_deleted, image_original, image_cropped, image_icon, null::json as settings from s_tables.t_users
    where (not is_deleted and not is_private) or (not is_deleted and (name_machine)::text = (current_user)::text);


/*** provide e-mail address as public information only if it is explicitly allowed ***/
create view public.v_users_email with (security_barrier=true) as
  select id, null::bigint as id_external, id_sort, name_machine, name_human, address_email, null::bool as is_administer, null::bool as is_manager, null::bool as is_auditor, null::bool as is_publisher, null::bool as is_insurer, null::bool as is_financer, null::bool as is_reviewer, null::bool as is_editor, null::bool as is_drafter, null::bool as is_requester, is_system, is_public, null::bool as is_locked, is_private, is_deleted, null::bool as is_roler, null::timestamp as date_created, null::timestamp as date_changed, null::timestamp as date_synced, null::timestamp as date_locked, null::timestamp as date_deleted, image_original, image_cropped, image_icon, null::json as settings from s_tables.t_users
    where (not is_deleted and not is_private and not (address_email).private) or (not is_deleted and (name_machine)::text = (current_user)::text);


/*** provide managers with the ability to modify accounts ***/
create view s_managers.v_users with (security_barrier=true) as
  select * from s_tables.t_users
    where not is_deleted;

create view s_managers.v_users_insert with (security_barrier=true) as
  select id, id_external, name_machine, name_human, address_email, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_locked, is_private, is_roler, image_original, image_cropped, image_icon, settings from s_tables.t_users
    with check option;

create view s_managers.v_users_update with (security_barrier=true) as
  select id, id_external, name_machine, name_human, address_email, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_locked, is_private, is_roler, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where not is_deleted
    with check option;

create view s_managers.v_users_deleted with (security_barrier=true) as
  select id, id_external, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_locked, is_private, is_roler, date_created, date_changed, date_synced, date_locked, image_original, image_cropped, image_icon, settings from s_tables.t_users
    where is_deleted;


/** Create Materialized views for table based on history (maybe current day should be a view and previous days should be a materialized view that is updated by cron?) **/
create materialized view s_administers.m_users_date_created_this_day as select * from s_tables.t_users where date_trunc('day', date_created) = date_trunc('day', current_timestamp);
create materialized view s_administers.m_users_date_created_previous_day as select * from s_tables.t_users where date_trunc('day', date_created) = date_trunc('day', current_timestamp) - interval '1 day';
create materialized view s_administers.m_users_date_created_previous_month as select * from s_tables.t_users where date_trunc('month', date_created) = date_trunc('month', current_timestamp) - interval '1 month';
create materialized view s_administers.m_users_date_created_previous_year as select * from s_tables.t_users where date_trunc('year', date_created) = date_trunc('year', current_timestamp) - interval '1 year';

create materialized view s_administers.m_users_date_changed_this_day as select * from s_tables.t_users where date_trunc('day', date_changed) = date_trunc('day', current_timestamp);
create materialized view s_administers.m_users_date_changed_previous_day as select * from s_tables.t_users where date_trunc('day', date_changed) = date_trunc('day', current_timestamp) - interval '1 day';
create materialized view s_administers.m_users_date_changed_previous_month as select * from s_tables.t_users where date_trunc('month', date_changed) = date_trunc('month', current_timestamp) - interval '1 month';
create materialized view s_administers.m_users_date_changed_previous_year as select * from s_tables.t_users where date_trunc('year', date_changed) = date_trunc('year', current_timestamp) - interval '1 year';

create materialized view s_administers.m_users_date_synced_this_day as select * from s_tables.t_users where date_trunc('day', date_synced) = date_trunc('day', current_timestamp);
create materialized view s_administers.m_users_date_synced_previous_day as select * from s_tables.t_users where date_trunc('day', date_synced) = date_trunc('day', current_timestamp) - interval '1 day';
create materialized view s_administers.m_users_date_synced_previous_month as select * from s_tables.t_users where date_trunc('month', date_synced) = date_trunc('month', current_timestamp) - interval '1 month';
create materialized view s_administers.m_users_date_synced_previous_year as select * from s_tables.t_users where date_trunc('year', date_synced) = date_trunc('year', current_timestamp) - interval '1 year';


create function s_administers.f_users_insert_actions() returns trigger as $$
  begin
    if (new.name_machine is null) then
      new.name_machine = current_user;
    end if;

    new.id_sort = ascii(new.name_machine);

    return new;
  end;
$$ language plpgsql;

create function s_administers.f_users_update_actions() returns trigger as $$
  begin
    new.date_changed = localtimestamp;

    if (old.is_deleted = false and new.is_deleted = true) then
      new.date_deleted = localtimestamp;
    elseif (old.is_deleted = true and new.is_deleted = false) then
      new.date_deleted = localtimestamp;
    end if;

    if (old.is_locked = false and new.is_locked = true) then
      new.date_locked = localtimestamp;
    elseif (old.is_locked = true and new.is_locked = false) then
      new.date_locked = localtimestamp;
    end if;

    if (new.id_sort <> ascii(new.name_machine)) then
      new.id_sort = ascii(new.name_machine);
    end if;

    return new;
  end;
$$ language plpgsql;


/* This is a stub function intended to be replaced with site-specific content in standard-permissions.sql (and therefore the site-specific one, such as reservation-permission.sql).. */
create function s_administers.f_users_insert_as_administer() returns trigger security definer as $$
  begin

    reset client_min_messages;

    return null;
  end;
$$ language plpgsql;

/* This is a stub function intended to be replaced with site-specific content in standard-permissions.sql (and therefore the site-specific one, such as reservation-permission.sql).. */
create function s_administers.f_users_update_as_administer() returns trigger security definer as $$
  begin

    reset client_min_messages;

    return null;
  end;
$$ language plpgsql;

create function s_administers.f_users_update_materialized_views() returns trigger security definer as $$
  begin

    refresh materialized view s_administers.m_users_date_created_this_day;
    refresh materialized view s_administers.m_users_date_changed_this_day;
    refresh materialized view s_administers.m_users_date_synced_this_day;

    return null;
  end;
$$ language plpgsql;


create trigger tr_users_insert_actions
  before insert on s_tables.t_users
    for each row execute procedure s_administers.f_users_insert_actions();

create trigger tr_users_update_actions
  before update on s_tables.t_users
    for each row execute procedure s_administers.f_users_update_actions();

create trigger tr_users_insert_as_administer
  after insert on s_tables.t_users
    for each row execute procedure s_administers.f_users_insert_as_administer();

create trigger tr_users_update_as_administer
  after update on s_tables.t_users
    for each row execute procedure s_administers.f_users_update_as_administer();

create trigger tr_users_update_materialized_views
  after insert or update on s_tables.t_users
    for each statement execute procedure s_administers.f_users_update_materialized_views();



commit transaction;
