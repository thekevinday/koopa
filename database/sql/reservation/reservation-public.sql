/** Reservation SQL Structure - Public **/
/** This depends on: reservation-main.sql **/

/* Replaces references to r_standard_public with r_reservation_public. */
start transaction;

/** public users should be able to insert, but should never be able to view the logs that they insert. **/
drop view public.v_log_users_self_insert;

create view public.v_log_users_self_insert with (security_barrier=true) as
  select log_title, log_type, log_type_sub, log_severity, log_facility, log_details, request_client, response_code from s_tables.t_log_users
    where 'r_reservation_public' in (select pr.rolname from pg_auth_members pam inner join pg_roles pr on (pam.roleid = pr.oid) inner join pg_roles pr_u on (pam.member = pr_u.oid) where pr_u.rolname = current_user and pr.rolname = 'r_reservation_public')
    with check option;


commit transaction;
