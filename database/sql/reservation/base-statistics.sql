/** Standardized SQL Structure - Statistics **/
/** This depends on: base-logs.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/** Provide status code statistics **/
create table managers.t_statistics_http_status_codes (
  code smallint not null,
  count bigint not null default 0,

  date_created timestamp default localtimestamp not null,

  constraint cp_statistics_http_status_codes_code primary key (code),

  constraint cc_statistics_http_status_codes_count check (count >= 0),

  constraint cf_statistics_http_status_codes_code foreign key (code) references managers.t_log_http_status_codes (id) on delete restrict on update cascade
);

grant select,insert,update on managers.t_statistics_http_status_codes to reservation_users_administer;
grant select,insert,update on managers.t_statistics_http_status_codes to reservation_users_manager;


/** create an auto-update trigger. set the role to reservation_users_manager so that the function runs as that role when using "SECURITY DEFINER". The reservation_users_manager must also have the appropriate create privileges. **/
grant create on schema managers to reservation_users_manager;
set role reservation_users_manager;

create function managers.f_statistics_http_status_codes_insert() returns trigger security definer as $$
  begin
    if (tg_op = 'INSERT') then
      update managers.t_statistics_http_status_codes set count = (select count + 1 as count from managers.t_statistics_http_status_codes where code = new.response_code) where code = new.response_code;
      if not found then
        insert into managers.t_statistics_http_status_codes (code, count) values (new.response_code, 1);
        if not found then return null; end if;
      end if;

      return new;
    end if;

    return null;
  end;
$$ language plpgsql;
reset role;
revoke create on schema managers from reservation_users_manager;

create trigger tr_statistics_http_status_codes_insert
  after insert on managers.t_log_activity
    for each row execute procedure managers.f_statistics_http_status_codes_insert();


/** create all of the known codes, initializing them to 0. **/
insert into managers.t_statistics_http_status_codes (code) values (0);
insert into managers.t_statistics_http_status_codes (code) values (1);
insert into managers.t_statistics_http_status_codes (code) values (2);

insert into managers.t_statistics_http_status_codes (code) values (100);
insert into managers.t_statistics_http_status_codes (code) values (101);
insert into managers.t_statistics_http_status_codes (code) values (102);

insert into managers.t_statistics_http_status_codes (code) values (200);
insert into managers.t_statistics_http_status_codes (code) values (201);
insert into managers.t_statistics_http_status_codes (code) values (202);
insert into managers.t_statistics_http_status_codes (code) values (203);
insert into managers.t_statistics_http_status_codes (code) values (204);
insert into managers.t_statistics_http_status_codes (code) values (205);
insert into managers.t_statistics_http_status_codes (code) values (206);
insert into managers.t_statistics_http_status_codes (code) values (207);
insert into managers.t_statistics_http_status_codes (code) values (208);
insert into managers.t_statistics_http_status_codes (code) values (226);

insert into managers.t_statistics_http_status_codes (code) values (300);
insert into managers.t_statistics_http_status_codes (code) values (301);
insert into managers.t_statistics_http_status_codes (code) values (302);
insert into managers.t_statistics_http_status_codes (code) values (303);
insert into managers.t_statistics_http_status_codes (code) values (304);
insert into managers.t_statistics_http_status_codes (code) values (305);
insert into managers.t_statistics_http_status_codes (code) values (306);
insert into managers.t_statistics_http_status_codes (code) values (307);
insert into managers.t_statistics_http_status_codes (code) values (308);

insert into managers.t_statistics_http_status_codes (code) values (400);
insert into managers.t_statistics_http_status_codes (code) values (401);
insert into managers.t_statistics_http_status_codes (code) values (402);
insert into managers.t_statistics_http_status_codes (code) values (403);
insert into managers.t_statistics_http_status_codes (code) values (404);
insert into managers.t_statistics_http_status_codes (code) values (405);
insert into managers.t_statistics_http_status_codes (code) values (406);
insert into managers.t_statistics_http_status_codes (code) values (407);
insert into managers.t_statistics_http_status_codes (code) values (408);
insert into managers.t_statistics_http_status_codes (code) values (409);
insert into managers.t_statistics_http_status_codes (code) values (410);
insert into managers.t_statistics_http_status_codes (code) values (411);
insert into managers.t_statistics_http_status_codes (code) values (412);
insert into managers.t_statistics_http_status_codes (code) values (413);
insert into managers.t_statistics_http_status_codes (code) values (414);
insert into managers.t_statistics_http_status_codes (code) values (415);
insert into managers.t_statistics_http_status_codes (code) values (416);
insert into managers.t_statistics_http_status_codes (code) values (417);
insert into managers.t_statistics_http_status_codes (code) values (422);
insert into managers.t_statistics_http_status_codes (code) values (423);
insert into managers.t_statistics_http_status_codes (code) values (424);
insert into managers.t_statistics_http_status_codes (code) values (426);
insert into managers.t_statistics_http_status_codes (code) values (428);
insert into managers.t_statistics_http_status_codes (code) values (429);
insert into managers.t_statistics_http_status_codes (code) values (431);
insert into managers.t_statistics_http_status_codes (code) values (451);

insert into managers.t_statistics_http_status_codes (code) values (500);
insert into managers.t_statistics_http_status_codes (code) values (501);
insert into managers.t_statistics_http_status_codes (code) values (502);
insert into managers.t_statistics_http_status_codes (code) values (503);
insert into managers.t_statistics_http_status_codes (code) values (504);
insert into managers.t_statistics_http_status_codes (code) values (505);
insert into managers.t_statistics_http_status_codes (code) values (506);
insert into managers.t_statistics_http_status_codes (code) values (507);
insert into managers.t_statistics_http_status_codes (code) values (508);
insert into managers.t_statistics_http_status_codes (code) values (510);
insert into managers.t_statistics_http_status_codes (code) values (511);



/** Provide request path statistics **/
create table managers.t_statistics_request_path (
  path varchar(512) not null,
  count bigint not null default 0,

  date_created timestamp default localtimestamp not null,

  constraint cp_statistics_request_path_code primary key (path),

  constraint cc_statistics_request_path_count check (count >= 0)
);

grant select on managers.t_statistics_request_path to reservation_users_administer;
grant select on managers.t_statistics_request_path to reservation_users_manager;

/** permissions prevent this from working as desired, so for now open up these stats to the following users (via a view) **/
create view users.v_statistics_request_path with (security_barrier=true) as
  select path, count from managers.t_statistics_request_path
    with check option;

grant select,insert,update on users.v_statistics_request_path to reservation_users;

create view public.v_statistics_request_path with (security_barrier=true) as
  select path, count from managers.t_statistics_request_path
    with check option;

grant select,insert,update on public.v_statistics_request_path to public_users;

/** create an auto-update trigger **/
create function managers.f_statistics_request_path_insert() returns trigger as $$
  begin
    if (tg_op = 'INSERT') then
      update v_statistics_request_path set count = (select count + 1 as count from v_statistics_request_path where path = new.request_path) where path = new.request_path;
      if not found then
        insert into v_statistics_request_path (path, count) values (new.request_path, 1);
        if not found then return null; end if;
      end if;

      return new;
    end if;

    return null;
  end;
$$ language plpgsql;

create trigger tr_statistics_request_path_insert
  after insert on managers.t_log_activity
    for each row execute procedure managers.f_statistics_request_path_insert();



commit transaction;
