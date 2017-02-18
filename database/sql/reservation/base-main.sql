/** Standardized SQL Structure - Main */
/** This depends on: base-first.sql **/
/** users will be called 'reservation_user_X' where X is the name, so that regex can be used on this script at a later time and mass rename these as necessary.
  @todo: prepare for Row Level Security Policies as defined here: https://wiki.postgresql.org/wiki/What%27s_new_in_PostgreSQL_9.5 and here: http://www.depesz.com/2014/10/02/waiting-for-9-5-row-level-security-policies-rls/
  All roles associated with a user can be determined with: "select pr.rolname from pg_auth_members pam inner join pg_roles pr on (pam.roleid = pr.oid) inner join pg_roles pr_u on (pam.member = pr_u.oid) where pr_u.rolname = user;"
  For performance reasons, name_machine is not directly assigned as a foreign key, only the id_user is. This can result in out of sync issues if the name_machine is ever changed. To avoid this, prediodically run a cron job to analyze all the entries and fix conflicts. (name_human may also fall into this situation in certain circumstances.)
  @todo: investigate and compare use of btree and gist for indexes used. This will require populating the data and comparing 'explain analyze' performance results. btree is the default and gist can be added by inserting 'using gist' for the index after the 'on name' part.
  Example of date selection format: select to_char('now'::timestamp, 'YYYY/MM/DD HH12:MI:SS am '); (see: http://www.postgresql.org/docs/9.4/static/functions-formatting.html)
  @todo: use http://www.postgresql.org/docs/9.4/static/functions-formatting.html and daterange for calendar dates and times (GiST is good for indexing ranges) (consider 'overlaps') (see: http://www.postgresql.org/docs/9.4/interactive/rangetypes.html#RANGETYPES-GIST).
  @todo: use BRIN (pg 9.5) indexing for indexes used to optimize specific table views or for tables expected to get very large (content, logs, files, etc..) (see: https://wiki.postgresql.org/wiki/What%27s_new_in_PostgreSQL_9.5)
*/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/** Schemas **/
create schema system authorization reservation_user;
create schema administers authorization reservation_users_administer;
create schema managers authorization reservation_users_manager;
create schema publishers authorization reservation_users_publisher;
create schema insurer authorization reservation_users_insurer;
create schema financer authorization reservation_users_financer;
create schema reviewers authorization reservation_users_reviewer;
create schema drafters authorization reservation_users_drafter;
create schema users authorization reservation_users;

revoke create on schema system from reservation_user;
revoke create on schema administers from reservation_users_administer;
revoke create on schema managers from reservation_users_manager;
revoke create on schema publishers from reservation_users_publisher;
revoke create on schema insurer from reservation_users_insurer;
revoke create on schema financer from reservation_users_financer;
revoke create on schema reviewers from reservation_users_reviewer;
revoke create on schema drafters from reservation_users_drafter;
revoke create on schema users from reservation_users;

grant usage on schema system to reservation_user;
grant usage on schema administers to reservation_users_administer;
grant usage on schema managers to reservation_users_manager;
grant usage on schema publishers to reservation_users_publisher;
grant usage on schema insurer to reservation_users_insurer;
grant usage on schema financer to reservation_users_financer;
grant usage on schema reviewers to reservation_users_reviewer;
grant usage on schema drafters to reservation_users_drafter;
grant usage on schema users to reservation_users;

grant create,usage on schema system to postgres;
grant create,usage on schema administers to postgres;
grant create,usage on schema managers to postgres;
grant create,usage on schema publishers to postgres;
grant create,usage on schema insurer to postgres;
grant create,usage on schema financer to postgres;
grant create,usage on schema reviewers to postgres;
grant create,usage on schema drafters to postgres;
grant create,usage on schema users to postgres;


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



commit transaction;