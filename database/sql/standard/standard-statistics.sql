/** Standardized SQL Structure - Statistics **/
/** This depends on: standard-log_users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/** Provide status code statistics **/
create table s_tables.t_statistics_http_status_codes (
  code smallint not null,
  count bigint not null default 0,

  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_deleted timestamp with time zone,

  constraint cp_statistics_http_status_codes primary key (code),

  constraint cc_statistics_http_status_codes_count check (count >= 0),

  constraint cf_statistics_http_status_codes_code foreign key (code) references s_tables.t_type_http_status_codes (id) on delete restrict on update cascade
);


/** create an auto-update trigger. set the role to r_standard_manager so that the function runs as that role when using "SECURITY DEFINER". The r_standard_manager must also have the appropriate create privileges. **/
create function s_tables.f_statistics_http_status_codes_insert() returns trigger security definer as $$
  begin
    if (tg_op = 'INSERT') then
      update s_tables.t_statistics_http_status_codes set count = (select count + 1 as count from s_tables.t_statistics_http_status_codes where code = new.response_code) where code = new.response_code;
      if not found then
        insert into s_tables.t_statistics_http_status_codes (code, count) values (new.response_code, 1);
        if not found then return null; end if;
      end if;

      return new;
    end if;

    return null;
  end;
$$ language plpgsql;


create trigger tr_log_user_activity_insert_statistics_http_status_codes
  after insert on s_tables.t_log_user_activity
    for each row execute procedure s_tables.f_statistics_http_status_codes_insert();

create trigger tr_log_users_insert_statistics_http_status_codes
  after insert on s_tables.t_log_users
    for each row execute procedure s_tables.f_statistics_http_status_codes_insert();

create trigger tr_statistics_http_status_codes_date_deleted
  before update on s_tables.t_statistics_http_status_codes
    for each row execute procedure s_administers.f_common_update_date_deleted();



/** Provide request path statistics **/
create table s_tables.t_statistics_http_request_path (
  path varchar(512) not null,
  count bigint not null default 0,

  is_deleted boolean default false not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,
  date_deleted timestamp with time zone,

  constraint cp_statistics_http_request_path primary key (path),

  constraint cc_statistics_http_request_path_count check (count >= 0)
);


/** permissions prevent this from working as desired, so for now open up these stats to the following users (via a view) **/
/* @todo: review this and try to restrict what accounts can access and set request_path in the same way s_tables.f_statistics_http_status_codes_insert() is handled. */
create view s_users.v_statistics_http_request_path with (security_barrier=true) as
  select path, count from s_tables.t_statistics_http_request_path
    with check option;

create view public.v_statistics_http_request_path with (security_barrier=true) as
  select path, count from s_tables.t_statistics_http_request_path
    with check option;


/** create an auto-update trigger **/
create function s_tables.f_statistics_http_request_path_insert() returns trigger as $$
  begin
    if (tg_op = 'INSERT') then
      update v_statistics_http_request_path set count = (select count + 1 as count from v_statistics_http_request_path where path = new.request_path) where path = new.request_path;
      if not found then
        insert into v_statistics_http_request_path (path, count) values (new.request_path, 1);
        if not found then return null; end if;
      end if;

      return new;
    end if;

    return null;
  end;
$$ language plpgsql;


create trigger tr_statistics_http_request_path_insert
  after insert on s_tables.t_statistics_http_request_path
    for each row execute procedure s_tables.f_statistics_http_request_path_insert();

create trigger tr_statistics_http_request_path_date_deleted
  before update on s_tables.t_statistics_http_request_path
    for each row execute procedure s_administers.f_common_update_date_deleted();



commit transaction;
