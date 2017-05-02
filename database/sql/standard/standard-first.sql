/** First time or one time execution stuff **/
/** Things here must be run first and cannot be run a second time due to their nature. **/
/** For example, tablespaces need only be created 1x and then any database on the system can use them **/
/** Be sure to replace reservation_ and reservation- with the prefix that is specific to your database. **/
/** This script creates the database (at or near the end of the script). **/



/** Create the roles**/
create role r_standard inherit nologin;
create role r_standard_administer inherit nologin;
create role r_standard_manager inherit nologin;
create role r_standard_auditor inherit nologin;
create role r_standard_publisher inherit nologin;
create role r_standard_insurer inherit nologin;
create role r_standard_financer inherit nologin;
create role r_standard_reviewer inherit nologin;
create role r_standard_editor inherit nologin;
create role r_standard_drafter inherit nologin;
create role r_standard_requester inherit nologin;
create role r_standard_system inherit nologin;
create role r_standard_public inherit nologin;

/* special use roles neither to be assigned directly nor are they added to the t_users table. */
create role u_standard_revision_requests inherit nologin;
create role u_standard_statistics_update inherit nologin;
create role u_standard_logger inherit nologin;
create role u_standard_groups_handler inherit nologin;
create role u_standard_grant_roles inherit nologin;


grant r_standard to u_standard_grant_roles with admin option;
grant r_standard_administer to u_standard_grant_roles with admin option;
grant r_standard_manager to u_standard_grant_roles with admin option;
grant r_standard_auditor to u_standard_grant_roles with admin option;
grant r_standard_publisher to u_standard_grant_roles with admin option;
grant r_standard_insurer to u_standard_grant_roles with admin option;
grant r_standard_financer to u_standard_grant_roles with admin option;
grant r_standard_reviewer to u_standard_grant_roles with admin option;
grant r_standard_editor to u_standard_grant_roles with admin option;
grant r_standard_drafter to u_standard_grant_roles with admin option;
grant r_standard_requester to u_standard_grant_roles with admin option;
grant r_standard_system to u_standard_grant_roles with admin option;
grant r_standard_public to u_standard_grant_roles with admin option;


/** This is the role and role group the database should use for anonymous traffic. I further suggest setting up pg_hba.conf to allow non-ssl connections (increase performance as public information is still public). **/
/** If the data is meant to be private, then have the public account use the system user with ssl connections **/
create role u_standard_public inherit login;

grant r_standard_public to u_standard_public;


/** These are the roles and role group the database should use for system activity (such as executing cron-jobs). **/
create role u_standard_system inherit login;
create role u_standard_system_administer inherit login;
create role u_standard_system_manager inherit login;
create role u_standard_system_auditor inherit login;
create role u_standard_system_publisher inherit login;
create role u_standard_system_insurer inherit login;
create role u_standard_system_financer inherit login;
create role u_standard_system_reviewer inherit login;
create role u_standard_system_editor inherit login;
create role u_standard_system_drafter inherit login;
create role u_standard_system_requester inherit login;
create role u_standard_system_public inherit login;



/** Create and connect to the database **/
create database standard;

\c standard ;


/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;


/* Make sure public is never allowed to create tables! */
revoke create on schema public from public;
