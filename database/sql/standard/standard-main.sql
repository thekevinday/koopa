/** Standardized SQL Structure - Main */
/** This depends on: standard-first.sql **/
/* @todo: add restrictions to even managers and administers so that ALL users access via views to allow for disabling any account (even an admin).
          only the postgresql/root account may access tables directly.
          This requires changing permissions and adding the appropriate s_administers and s_managers tables.
*/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/** Schemas **/
create schema s_administers authorization postgres;
create schema s_managers authorization postgres;
create schema s_auditors authorization postgres;
create schema s_publishers authorization postgres;
create schema s_insurers authorization postgres;
create schema s_financers authorization postgres;
create schema s_reviewers authorization postgres;
create schema s_editors authorization postgres;
create schema s_drafters authorization postgres;
create schema s_requesters authorization postgres;
create schema s_users authorization postgres;
create schema s_tables authorization postgres;

grant usage on schema s_administers to r_standard_administer;
grant usage on schema s_managers to r_standard_manager;
grant usage on schema s_auditors to r_standard_auditor;
grant usage on schema s_publishers to r_standard_publisher;
grant usage on schema s_insurers to r_standard_insurer;
grant usage on schema s_financers to r_standard_financer;
grant usage on schema s_reviewers to r_standard_reviewer;
grant usage on schema s_editors to r_standard_editor;
grant usage on schema s_drafters to r_standard_drafter;
grant usage on schema s_requesters to r_standard_requester;
grant usage on schema s_users to r_standard;

grant usage on schema s_tables to u_standard_revision_requests, u_standard_statistics_update, u_standard_logger, u_standard_groups_handler;


/** Composite Types **/
create type public.ct_name_person as (
  prefix varchar(32),
  first varchar(64),
  middle varchar(64),
  last varchar(64),
  suffix varchar(32),
  complete varchar(256)
);

create type public.ct_client as (
  ip inet,
  port int,
  agent varchar(256)
);

create type public.ct_email as (
  name varchar(128),
  domain varchar(128),
  private boolean
);

create type public.ct_text as (
  content text,
  context bigint
);

create type public.ct_location as (
  building bigint,
  room bigint[]
);

create type public.ct_date as (
  date timestamp with time zone,
  time_start timestamp with time zone,
  time_stop timestamp with time zone
);

create type public.ct_date_context as (
  date timestamp with time zone,
  time_start timestamp with time zone,
  time_stop timestamp with time zone,
  context bigint
);

create type public.ct_phone_number as (
  country smallint,
  area smallint,
  number smallint,
  extension smallint
);

create type public.ct_phone_number_context as (
  country smallint,
  area smallint,
  number smallint,
  extension smallint,
  context bigint
);

create type public.ct_money_context as (
  money money,
  context bigint
);

create type public.ct_field_fees as (
  needed bool,
  quantity bigint,
  days bigint,
  hours bigint,
  amount money
);

create type public.ct_field_used_with_contact as (
  used bool,
  email text,
  name text,
  phone public.ct_phone_number
);

create type public.ct_field_needed_with_total as (
  needed bool,
  total bigint
);

create type public.ct_field_needed_with_details as (
  needed bool,
  details text
);

create type public.ct_field_used_with_details as (
  used bool,
  details text
);

create type public.ct_field_used_with_designer as (
  used bool,
  designer text
);

create type public.ct_field_served_with_caterer as (
  served bool,
  caterer text
);

create type public.ct_field_generated_with_types as (
  generated bool,
  types bigint[]
);

create type public.ct_field_needed_with_types as (
  needed bool,
  types bigint[]
);

create type public.ct_field_needed_with_types_and_microphone as (
  needed bool,
  types bigint[],
  microphone bigint
);

create type public.ct_field_insurance as (
  needed bool,
  provided bool
);



/** Common Functions **/
/* User ID and Session User ID Functions */
create function s_administers.f_common_enforce_user_and_session_ids() returns trigger as $$
  begin
    new.id_user = (select id from v_users_self);
    new.id_user_session = (select id from v_users_self_session);
    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_enforce_creator_and_session_ids() returns trigger as $$
  begin
    new.id_creator = (select id from v_users_self);
    new.id_creator_session = (select id from v_users_self_session);
    return new;
  end;
$$ language plpgsql;


/* Date Change Functions */
create function s_administers.f_common_update_date_deleted() returns trigger as $$
  begin
    if (old.is_deleted = false and new.is_deleted = true) then
      new.date_deleted = localtimestamp;
    elseif (old.is_deleted = true and new.is_deleted = false) then
      new.date_deleted = localtimestamp;
    end if;

    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_update_date_changed_or_deleted() returns trigger as $$
  begin
    new.date_changed = localtimestamp;

    if (old.is_deleted = false and new.is_deleted = true) then
      new.date_deleted = localtimestamp;
    elseif (old.is_deleted = true and new.is_deleted = false) then
      new.date_deleted = localtimestamp;
    end if;

    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_update_date_changed_or_locked() returns trigger as $$
  begin
    new.date_changed = localtimestamp;

    if (old.is_locked = false and new.is_locked = true) then
      new.date_locked = localtimestamp;
    elseif (old.is_locked = true and new.is_locked = false) then
      new.date_locked = localtimestamp;
    end if;

    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_update_date_changed_deleted_or_locked() returns trigger as $$
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

    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_update_date_changed_deleted_cancelled_or_locked() returns trigger as $$
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

    if (old.is_cancelled = false and new.is_cancelled = true) then
      new.date_cancelled = localtimestamp;
    elseif (old.is_cancelled = true and new.is_cancelled = false) then
      new.date_cancelled = localtimestamp;
    end if;

    return new;
  end;
$$ language plpgsql;


/* Revision Increment Functions */
create function s_administers.f_common_increment_revision() returns trigger as $$
  begin
    new.id_revision = old.id_revision + 1;
    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_increment_revision_update_date_changed_or_deleted() returns trigger as $$
  begin
    new.date_changed = localtimestamp;

    if (old.is_deleted = false and new.is_deleted = true) then
      new.date_deleted = localtimestamp;
    elseif (old.is_deleted = true and new.is_deleted = false) then
      new.date_deleted = localtimestamp;
    end if;

    new.id_revision = old.id_revision + 1;

    return new;
  end;
$$ language plpgsql;

create function s_administers.f_common_increment_revision_update_date_changed_deleted_or_locked() returns trigger as $$
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

    new.id_revision = old.id_revision + 1;

    return new;
  end;
$$ language plpgsql;

commit transaction;
