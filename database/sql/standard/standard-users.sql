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
  is_system boolean default false not null,
  is_public boolean default false not null,
  is_locked boolean default false not null,
  is_private boolean default true not null,
  is_deleted boolean default false not null,

  can_manage_roles boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_synced timestamp with time zone default current_timestamp not null,
  date_locked timestamp with time zone,
  date_deleted timestamp with time zone,

  settings json,

  constraint cp_users primary key (id),

  constraint cc_users_id check (id > 0),
  constraint cc_users_id_external check (id_external >= -1),

  constraint cu_users_id_external unique (id_external),
  constraint cu_users_name_machine unique (name_machine)
);

create sequence s_tables.se_users_id owned by s_tables.t_users.id;
alter table s_tables.t_users alter column id set default nextval('s_tables.se_users_id'::regclass);

grant select,insert,update on s_tables.t_users to r_standard_administer;
grant select on s_tables.t_users to r_standard_auditor;
grant select,usage on s_tables.se_users_id to r_standard_administer;
grant usage on s_tables.se_users_id to r_standard, r_standard_system;

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
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, can_manage_roles, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, settings from s_tables.t_users
    where not is_deleted and (name_machine)::text = (current_user)::text;

grant select on s_users.v_users_self to r_standard, r_standard_system;

create view public.v_users_self_session with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, can_manage_roles, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, settings from s_tables.t_users
    where not is_deleted and (name_machine)::text = (session_user)::text;

grant select on public.v_users_self_session to r_standard, r_standard_system, r_standard_public;

create view public.v_users_self_locked_not with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, can_manage_roles, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, settings from s_tables.t_users
    where not is_deleted and not is_locked and (name_machine)::text = (current_user)::text;

grant select on public.v_users_self_locked_not to r_standard, r_standard_system, r_standard_public;

create view public.v_users_self_exists with (security_barrier=true) as
  select id, name_machine, is_system, is_public, is_locked, is_deleted from s_tables.t_users
    where (name_machine)::text = (current_user)::text;

grant select on public.v_users_self_exists to r_standard, r_standard_system, r_standard_public;

create view s_users.v_users_self_insert with (security_barrier=true) as
  select id_external, name_human, address_email, is_private, settings from s_tables.t_users
    where not is_deleted and not is_locked and not is_system and not is_public and (name_machine)::text = (current_user)::text
    with check option;

grant insert on s_users.v_users_self_insert to r_standard, r_standard_system;

create view s_users.v_users_self_update with (security_barrier=true) as
  select address_email, is_private, settings from s_tables.t_users
    where not is_deleted and not is_locked and not is_system and not is_public and (name_machine)::text = (current_user)::text
    with check option;

grant update on s_users.v_users_self_update to r_standard, r_standard_system;


/**** anonymous user has uid = 1 ****/
create view public.v_users_self with (security_barrier=true) as
  select id, id_external, id_sort, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, is_deleted, date_created, date_changed, date_synced, date_locked, null::timestamp as date_deleted, settings from s_tables.t_users
    where not is_deleted and id = 1;

grant select on public.v_users_self to r_standard_public, r_standard, r_standard_system;


/*** provide public user information ***/
create view public.v_users with (security_barrier=true) as
  select id, null::bigint as id_external, id_sort, name_machine, name_human, null::public.ct_email as address_email, null::bool as is_administer, null::bool as is_manager, null::bool as is_auditor, null::bool as is_publisher, null::bool as is_insurer, null::bool as is_financer, null::bool as is_reviewer, null::bool as is_editor, null::bool as is_drafter, null::bool as is_requester, is_system, is_public, null::bool as is_locked, is_private, is_deleted, null::bool as can_manage_roles, null::timestamp as date_created, null::timestamp as date_changed, null::timestamp as date_synced, null::timestamp as date_locked, null::timestamp as date_deleted, null::json as settings from s_tables.t_users
    where (not is_deleted and not is_private) or (not is_deleted and (name_machine)::text = (current_user)::text);

grant select on public.v_users to r_standard, r_standard_public, r_standard_system;


/*** provide e-mail address as public information only if it is explicitly allowed ***/
create view public.v_users_email with (security_barrier=true) as
  select id, null::bigint as id_external, id_sort, name_machine, name_human, address_email, null::bool as is_administer, null::bool as is_manager, null::bool as is_auditor, null::bool as is_publisher, null::bool as is_insurer, null::bool as is_financer, null::bool as is_reviewer, null::bool as is_editor, null::bool as is_drafter, null::bool as is_requester, is_system, is_public, null::bool as is_locked, is_private, is_deleted, null::bool as can_manage_roles, null::timestamp as date_created, null::timestamp as date_changed, null::timestamp as date_synced, null::timestamp as date_locked, null::timestamp as date_deleted, null::json as settings from s_tables.t_users
    where (not is_deleted and not is_private and not (address_email).private) or (not is_deleted and (name_machine)::text = (current_user)::text);

grant select on public.v_users_email to r_standard, r_standard_public, r_standard_system;


/*** provide managers with the ability to modify accounts ***/
create view s_managers.v_users with (security_barrier=true) as
  select * from s_tables.t_users
    where not is_deleted;

grant select on s_managers.v_users to r_standard_manager;

create view s_managers.v_users_insert with (security_barrier=true) as
  select id, id_external, name_machine, name_human, address_email, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_locked, is_private, can_manage_roles, settings from s_tables.t_users
    with check option;

grant insert on s_managers.v_users_insert to r_standard_manager;

create view s_managers.v_users_update with (security_barrier=true) as
  select id, id_external, name_machine, name_human, address_email, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_locked, is_private, can_manage_roles, settings from s_tables.t_users
    where not is_deleted
    with check option;

grant update on s_managers.v_users_update to r_standard_manager;

create view s_managers.v_users_deleted with (security_barrier=true) as
  select id, id_external, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_insurer, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_locked, is_private, can_manage_roles, date_created, date_changed, date_synced, date_locked, settings from s_tables.t_users
    where is_deleted;

grant select on s_managers.v_users to r_standard_manager;


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

alter materialized view s_administers.m_users_date_created_this_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_created_previous_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_created_previous_month owner to r_standard_administer;
alter materialized view s_administers.m_users_date_created_previous_year owner to r_standard_administer;

alter materialized view s_administers.m_users_date_changed_this_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_changed_previous_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_changed_previous_month owner to r_standard_administer;
alter materialized view s_administers.m_users_date_changed_previous_year owner to r_standard_administer;

alter materialized view s_administers.m_users_date_synced_this_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_synced_previous_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_synced_previous_month owner to r_standard_administer;
alter materialized view s_administers.m_users_date_synced_previous_year owner to r_standard_administer;

grant select on s_administers.m_users_date_created_this_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_year to r_standard_manager;

grant select on s_administers.m_users_date_changed_this_day to r_standard_manager;
grant select on s_administers.m_users_date_changed_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_changed_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_changed_previous_year to r_standard_manager;

grant select on s_administers.m_users_date_synced_this_day to r_standard_manager;
grant select on s_administers.m_users_date_synced_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_synced_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_synced_previous_year to r_standard_manager;

grant select on s_administers.m_users_date_created_this_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_year to r_standard_manager;


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


/* attempt to auto-manage postgresql standard roles with the standard database user roles. */
/* user ids 1 and 2 are explicitly reserved for anonymous/public and the database postgresql accounts. */
/* postgresql does not seem to support variables for the user with grant and revoke, therefore the execute statement is used to perform the query. */
/* @fixme: the name_machine must be forcibly sanitized to be alphanumeric, -, or _ in all cases. */
create function s_administers.f_users_insert_as_administer() returns trigger security definer as $$
  declare
    name_machine constant text default quote_ident(new.name_machine);
  begin
    if (new.id = 1 or new.id = 2) then
      return null;
    end if;

    set client_min_messages to error;

    if (new.is_locked or new.is_deleted) then
      if (new.is_deleted) then
        execute 'revoke r_standard from ' || name_machine;
        execute 'revoke r_standard_system from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      elseif (new.is_public) then
        execute 'grant r_standard_public to ' || name_machine;
      elseif (new.is_system) then
        execute 'grant r_standard_system to ' || name_machine;
      elseif (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
        execute 'grant r_standard to ' || name_machine;
      end if;

      execute 'revoke r_standard_administer from ' || name_machine;
      execute 'revoke r_standard_manager from ' || name_machine;
      execute 'revoke r_standard_auditor from ' || name_machine;
      execute 'revoke r_standard_publisher from ' || name_machine;
      execute 'revoke r_standard_financer from ' || name_machine;
      execute 'revoke r_standard_insurer from ' || name_machine;
      execute 'revoke r_standard_reviewer from ' || name_machine;
      execute 'revoke r_standard_editor from ' || name_machine;
      execute 'revoke r_standard_drafter from ' || name_machine;
      execute 'revoke r_standard_requester from ' || name_machine;
    elseif (new.is_public) then
      execute 'grant r_standard_public to ' || name_machine;
      execute 'revoke r_standard from ' || name_machine;

      if (new.is_system) then
        execute 'grant r_standard_system to ' || name_machine;
      else
        execute 'revoke r_standard_system from ' || name_machine;
      end if;

      execute 'revoke r_standard_administer from ' || name_machine;
      execute 'revoke r_standard_manager from ' || name_machine;
      execute 'revoke r_standard_auditor from ' || name_machine;
      execute 'revoke r_standard_publisher from ' || name_machine;
      execute 'revoke r_standard_financer from ' || name_machine;
      execute 'revoke r_standard_insurer from ' || name_machine;
      execute 'revoke r_standard_reviewer from ' || name_machine;
      execute 'revoke r_standard_editor from ' || name_machine;
      execute 'revoke r_standard_drafter from ' || name_machine;
      execute 'revoke r_standard_requester from ' || name_machine;
    else
      if (new.is_system) then
        execute 'grant r_standard_system to ' || name_machine;

        execute 'revoke r_standard from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      elseif (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
        execute 'grant r_standard to ' || name_machine;

        execute 'revoke r_standard_system from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      end if;

      if (new.is_administer) then
        execute 'grant r_standard_administer to ' || name_machine;
      end if;

      if (new.is_manager) then
        execute 'grant r_standard_manager to ' || name_machine;
      end if;

      if (new.is_auditor) then
        execute 'grant r_standard_auditor to ' || name_machine;
      end if;

      if (new.is_publisher) then
        execute 'grant r_standard_publisher to ' || name_machine;
      end if;

      if (new.is_insurer) then
        execute 'grant r_standard_insurer to ' || name_machine;
      end if;

      if (new.is_financer) then
        execute 'grant r_standard_financer to ' || name_machine;
      end if;

      if (new.is_reviewer) then
        execute 'grant r_standard_reviewer to ' || name_machine;
      end if;

      if (new.is_editor) then
        execute 'grant r_standard_editor to ' || name_machine;
      end if;

      if (new.is_drafter) then
        execute 'grant r_standard_drafter to ' || name_machine;
      end if;

      if (new.is_requester) then
        execute 'grant r_standard_requester to ' || name_machine;
      end if;
    end if;

    reset client_min_messages;

    return null;
  end;
$$ language plpgsql;

alter function s_administers.f_users_insert_as_administer() owner to u_standard_grant_roles;


create function s_administers.f_users_update_as_administer() returns trigger security definer as $$
  declare
    name_machine constant text default quote_ident(new.name_machine);
  begin
    if (new.id = 1 or new.id = 2) then
      return null;
    end if;

    set client_min_messages to error;

    if (old.is_locked <> new.is_locked or old.is_deleted <> new.is_deleted) then
      if (old.is_deleted <> new.is_deleted) then
        if (new.is_deleted) then
          execute 'revoke r_standard from ' || name_machine;
          execute 'revoke r_standard_system from ' || name_machine;
          execute 'revoke r_standard_public from ' || name_machine;
        else
          if (new.is_public) then
            execute 'grant r_standard_public to ' || name_machine;
          elseif (new.is_system) then
            execute 'grant r_standard_system to ' || name_machine;
          elseif (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
            execute 'grant r_standard to ' || name_machine;
          end if;
        end if;
      end if;

      if (new.is_locked or new.is_deleted) then
        execute 'revoke r_standard_administer from ' || name_machine;
        execute 'revoke r_standard_manager from ' || name_machine;
        execute 'revoke r_standard_auditor from ' || name_machine;
        execute 'revoke r_standard_publisher from ' || name_machine;
        execute 'revoke r_standard_financer from ' || name_machine;
        execute 'revoke r_standard_insurer from ' || name_machine;
        execute 'revoke r_standard_reviewer from ' || name_machine;
        execute 'revoke r_standard_editor from ' || name_machine;
        execute 'revoke r_standard_drafter from ' || name_machine;
        execute 'revoke r_standard_requester from ' || name_machine;
        execute 'revoke r_standard_system from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      elseif (new.is_public) then
        execute 'grant r_standard_public to ' || name_machine;

        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        end if;
      else
        if (new.is_administer) then
          execute 'grant r_standard_administer to ' || name_machine;
        end if;

        if (new.is_manager) then
          execute 'grant r_standard_manager to ' || name_machine;
        end if;

        if (new.is_auditor) then
          execute 'grant r_standard_auditor to ' || name_machine;
        end if;

        if (new.is_publisher) then
          execute 'grant r_standard_publisher to ' || name_machine;
        end if;

        if (new.is_financer) then
          execute 'grant r_standard_financer to ' || name_machine;
        end if;

        if (new.is_insurer) then
          execute 'grant r_standard_insurer to ' || name_machine;
        end if;

        if (new.is_reviewer) then
          execute 'grant r_standard_reviewer to ' || name_machine;
        end if;

        if (new.is_editor) then
          execute 'grant r_standard_editor to ' || name_machine;
        end if;

        if (new.is_drafter) then
          execute 'grant r_standard_drafter to ' || name_machine;
        end if;

        if (new.is_requester) then
          execute 'grant r_standard_requester to ' || name_machine;
        end if;

        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        end if;
      end if;
    elseif (old.is_public <> new.is_public and new.is_public) then
      execute 'grant r_standard_public to ' || name_machine;

      execute 'revoke r_standard_administer from ' || name_machine;
      execute 'revoke r_standard_manager from ' || name_machine;
      execute 'revoke r_standard_auditor from ' || name_machine;
      execute 'revoke r_standard_publisher from ' || name_machine;
      execute 'revoke r_standard_financer from ' || name_machine;
      execute 'revoke r_standard_insurer from ' || name_machine;
      execute 'revoke r_standard_reviewer from ' || name_machine;
      execute 'revoke r_standard_editor from ' || name_machine;
      execute 'revoke r_standard_drafter from ' || name_machine;
      execute 'revoke r_standard_requester from ' || name_machine;

      if (old.is_system <> new.is_system) then
        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        else
          execute 'revoke r_standard_system from ' || name_machine;
        end if;
      end if;
    else
      if (old.is_public <> new.is_public) then
        execute 'revoke r_standard_public from ' || name_machine;
      end if;

      if (old.is_system <> new.is_system) then
        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        else
          execute 'revoke r_standard_system from ' || name_machine;
        end if;
      elseif (not new.is_system) then
        if (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
          execute 'grant r_standard to ' || name_machine;
        end if;
      end if;

      if (old.is_administer <> new.is_administer) then
        if (new.is_administer) then
          execute 'grant r_standard_administer to ' || name_machine;
        else
          execute 'revoke r_standard_administer from ' || name_machine;
        end if;
      end if;

      if (old.is_manager <> new.is_manager) then
        if (new.is_manager) then
          execute 'grant r_standard_manager to ' || name_machine;
        else
          execute 'revoke r_standard_manager from ' || name_machine;
        end if;
      end if;

      if (old.is_auditor <> new.is_auditor) then
        if (new.is_auditor) then
          execute 'grant r_standard_auditor to ' || name_machine;
        else
          execute 'revoke r_standard_auditor from ' || name_machine;
        end if;
      end if;

      if (old.is_publisher <> new.is_publisher) then
        if (new.is_publisher) then
          execute 'grant r_standard_publisher to ' || name_machine;
        else
          execute 'revoke r_standard_publisher from ' || name_machine;
        end if;
      end if;

      if (old.is_insurer <> new.is_insurer) then
        if (new.is_insurer) then
          execute 'grant r_standard_insurer to ' || name_machine;
        else
          execute 'revoke r_standard_insurer from ' || name_machine;
        end if;
      end if;

      if (old.is_financer <> new.is_financer) then
        if (new.is_financer) then
          execute 'grant r_standard_financer to ' || name_machine;
        else
          execute 'revoke r_standard_financer from ' || name_machine;
        end if;
      end if;

      if (old.is_reviewer <> new.is_reviewer) then
        if (new.is_reviewer) then
          execute 'grant r_standard_reviewer to ' || name_machine;
        else
          execute 'revoke r_standard_reviewer from ' || name_machine;
        end if;
      end if;

      if (old.is_editor <> new.is_editor) then
        if (new.is_editor) then
          execute 'grant r_standard_editor to ' || name_machine;
        else
          execute 'revoke r_standard_editor from ' || name_machine;
        end if;
      end if;

      if (old.is_drafter <> new.is_drafter) then
        if (new.is_drafter) then
          execute 'grant r_standard_drafter to ' || name_machine;
        else
          execute 'revoke r_standard_drafter from ' || name_machine;
        end if;
      end if;

      if (old.is_requester <> new.is_requester) then
        if (new.is_requester) then
          execute 'grant r_standard_requester to ' || name_machine;
        else
          execute 'revoke r_standard_requester from ' || name_machine;
        end if;
      end if;
    end if;

    reset client_min_messages;

    return null;
  end;
$$ language plpgsql;

alter function s_administers.f_users_update_as_administer() owner to u_standard_grant_roles;

create function s_administers.f_users_update_materialized_views() returns trigger security definer as $$
  begin

    refresh materialized view s_administers.m_users_date_created_this_day;
    refresh materialized view s_administers.m_users_date_changed_this_day;
    refresh materialized view s_administers.m_users_date_synced_this_day;

    return null;
  end;
$$ language plpgsql;

alter function s_administers.f_users_update_materialized_views() owner to r_standard_administer;

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

/** Special Cases: manually add the postgresql and public users first before any logging triggers are defined (because some of them depend on this table recursively! **/
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_public) values (1, 'u_standard_public', (null, 'Unknown', null, null, null, 'Unknown'), false, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system) values (2, 'postgres', (null, 'Database', null, 'Administer', null, 'Database (Administer)'), true, true);



commit transaction;
