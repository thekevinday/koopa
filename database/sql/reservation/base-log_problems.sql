/** Standardized SQL Structure - Logs - Problems */
/** This depends on: base-users.sql **/
/* The problem logs are intended for temporary reporting of problems and are meant to allow permanent deletion. */
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/** Provide a log of problems, which are defined by the software. **/
/* @todo: shouldnt there be a problem type code? */
create table managers.t_log_problems (
  id bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,

  constraint cp_log_problems_id primary key (id),

  constraint cc_log_problems_id check (id > 0),

  constraint cu_log_problems_name_machine unique (name_machine)
);

grant select,insert,update,delete on managers.t_log_problems to reservation_users_administer;
grant select,insert,update,delete on managers.t_log_problems to reservation_users_manager;
grant select on managers.t_log_problems to reservation_users_auditor;

/* @todo: it seems the views for allowing users to insert/delete problems needs to be created. */



/** Provide a log of problems, associated with a given user. **/
create table managers.t_log_problems_users (
  id_problem bigint not null,
  id_user bigint not null,

  name_machine_user varchar(128) not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,

  log_details json,

  constraint cp_log_problems_users_id primary key (id_problem, id_user),

  constraint cu_log_problems_users_name_machine unique (name_machine_user),

  constraint cf_log_problems_users_id_problem foreign key (id_problem) references managers.t_log_problems (id) on delete restrict on update cascade,
  constraint cf_log_problems_users_id_user foreign key (id_user, name_machine_user) references administers.t_users (id, name_machine) on delete restrict on update cascade
);

grant select,delete on managers.t_log_problems_users to reservation_users_administer;
grant select,delete on managers.t_log_problems_users to reservation_users_manager;
grant select on managers.t_log_problems_users to reservation_users_auditor;


/** only allow select, insert, and delete for users when user id is current user **/
create view users.v_log_problems_users_self with (security_barrier=true) as
  select id_problem, id_user, date_created, date_changed, log_details from managers.t_log_problems_users
    where (name_machine_user)::text = (current_user)::text;

grant select on users.v_log_problems_users_self to reservation_users;


create view users.v_log_problems_users_self_insert with (security_barrier=true) as
  select id_problem, id_user, name_machine_user, date_changed, log_details from managers.t_log_problems_users
    where (name_machine_user)::text = (current_user)::text
    with check option;

grant insert on users.v_log_problems_users_self_insert to reservation_users;


create view users.v_log_problems_users_self_delete with (security_barrier=true) as
  select id_problem, id_user from managers.t_log_problems_users
    where (name_machine_user)::text = (current_user)::text
    with check option;

grant delete on users.v_log_problems_users_self_delete to reservation_users;


/** automatically delete problems deleted from the table managers.t_log_problems_users **/
create function managers.f_log_problems_users_delete() returns trigger security definer as $$
  begin
    if (tg_op = 'DELETE') then
      delete from managers.t_log_problems where id = old.id_problem;
      return old;
    end if;

    return null;
  end;
$$ language plpgsql;

create trigger tr_log_problems_users_delete
  after delete on managers.t_log_problems_users
    for each row execute procedure managers.f_log_problems_users_delete();


commit transaction;
