/** First time or one time execution stuff **/
/** Things here must be run first and cannot be run a second time due to their nature. **/
/** For example, tablespaces need only be created 1x and then any database on the system can use them **/
/** Be sure to replace reservation_ and reservation- with the prefix that is specific to your database. **/
/** This script creates the database (at or near the end of the script). **/



/** Create the roles**/
create role r_reservation inherit nologin;
create role r_reservation_administer inherit nologin;
create role r_reservation_manager inherit nologin;
create role r_reservation_auditor inherit nologin;
create role r_reservation_publisher inherit nologin;
create role r_reservation_insurer inherit nologin;
create role r_reservation_financer inherit nologin;
create role r_reservation_reviewer inherit nologin;
create role r_reservation_editor inherit nologin;
create role r_reservation_drafter inherit nologin;
create role r_reservation_requester inherit nologin;
create role r_reservation_system inherit nologin;
create role r_reservation_public inherit nologin;

/* special use roles neither to be assigned directly nor are they added to the t_users table. */
create role u_reservation_revision_requests inherit nologin;
create role u_reservation_statistics_update inherit nologin;
create role u_reservation_logger inherit nologin;
create role u_reservation_groups_handler inherit nologin;
create role u_reservation_grant_roles inherit nologin;


grant r_reservation to u_reservation_grant_roles with admin option;
grant r_reservation_administer to u_reservation_grant_roles with admin option;
grant r_reservation_manager to u_reservation_grant_roles with admin option;
grant r_reservation_auditor to u_reservation_grant_roles with admin option;
grant r_reservation_publisher to u_reservation_grant_roles with admin option;
grant r_reservation_insurer to u_reservation_grant_roles with admin option;
grant r_reservation_financer to u_reservation_grant_roles with admin option;
grant r_reservation_reviewer to u_reservation_grant_roles with admin option;
grant r_reservation_editor to u_reservation_grant_roles with admin option;
grant r_reservation_drafter to u_reservation_grant_roles with admin option;
grant r_reservation_requester to u_reservation_grant_roles with admin option;
grant r_reservation_system to u_reservation_grant_roles with admin option;
grant r_reservation_public to u_reservation_grant_roles with admin option;


/** This is the role and role group the database should use for anonymous traffic. I further suggest setting up pg_hba.conf to allow non-ssl connections (increase performance as public information is still public). **/
/** If the data is meant to be private, then have the public account use the system user with ssl connections **/
create role u_reservation_public inherit login;

grant r_reservation_public to u_reservation_public;


/** These are the roles and role group the database should use for system activity (such as executing cron-jobs). **/
create role u_reservation_system inherit login;
create role u_reservation_system_administer inherit login;
create role u_reservation_system_manager inherit login;
create role u_reservation_system_auditor inherit login;
create role u_reservation_system_publisher inherit login;
create role u_reservation_system_insurer inherit login;
create role u_reservation_system_financer inherit login;
create role u_reservation_system_reviewer inherit login;
create role u_reservation_system_editor inherit login;
create role u_reservation_system_drafter inherit login;
create role u_reservation_system_requester inherit login;
create role u_reservation_system_public inherit login;



/** Create and connect to the database **/
create database reservation;

\c reservation ;


/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;
set timezone to UTC;


/* Make sure public is never allowed to create tables! */
revoke create on schema public from public;
