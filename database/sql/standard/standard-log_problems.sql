/** Standardized SQL Structure - Logs - Problems */
/** This depends on: standard-users.sql **/
/* The problem logs are intended for temporary reporting of problems and are meant to allow permanent deletion. */
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;



/** Provide a log of problems, which are defined by the software. **/
create table s_tables.t_log_problems (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,

  constraint cp_log_problems primary key (id),

  constraint cc_log_problems_id check (id > 0),

  constraint cu_log_problems_name_machine unique (name_machine)
);

create sequence s_tables.se_log_problems_id owned by s_tables.t_log_problems.id;
alter table s_tables.t_log_problems alter column id set default nextval('s_tables.se_log_problems_id'::regclass);



/** Provide a log of problems, associated with a given user. **/
create table s_tables.t_log_problems_users (
  id_problem bigint not null,
  id_user bigint not null,
  id_user_session bigint not null,

  date_created timestamp with time zone default current_timestamp not null,
  date_changed timestamp with time zone default current_timestamp not null,

  log_details json,

  constraint cp_log_problems_users primary key (id_problem, id_user),

  constraint cf_log_problems_users_id_problem foreign key (id_problem) references s_tables.t_log_problems (id) on delete restrict on update cascade,
  constraint cf_log_problems_users_id_user foreign key (id_user) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_log_problems_users_id_user_session foreign key (id_user_session) references s_tables.t_users (id) on delete restrict on update cascade
);


/** only allow select, insert, and delete for users when user id is current user **/
create view s_users.v_log_problems_users_self with (security_barrier=true) as
  with this_user as (select id from v_users_self_locked_not)
  select id_problem, date_created, date_changed, log_details from s_tables.t_log_problems_users
    where id_user in (select * from this_user);

create view s_users.v_log_problems_users_self_insert with (security_barrier=true) as
  select id_problem, date_changed, log_details from s_tables.t_log_problems_users
    where id_user in (select id from v_users_self_locked_not)
    with check option;

create view s_users.v_log_problems_users_self_delete with (security_barrier=true) as
  select id_problem from s_tables.t_log_problems_users
    where id_user in (select id from v_users_self_locked_not)
    with check option;


/** automatically delete problems deleted from the table s_tables.t_log_problems_users **/
create function s_tables.f_log_problems_users_delete() returns trigger security definer as $$
  begin
    if (tg_op = 'DELETE') then
      delete from s_tables.t_log_problems where id = old.id_problem;
      return old;
    end if;

    return null;
  end;
$$ language plpgsql;


create trigger tr_log_problems_users_delete
  after delete on s_tables.t_log_problems_users
    for each row execute procedure s_tables.f_log_problems_users_delete();

create trigger tr_log_problems_enforce_user_and_session_ids
  before insert on s_tables.t_log_problems
    for each row execute procedure s_administers.f_common_enforce_user_and_session_ids();



commit transaction;
