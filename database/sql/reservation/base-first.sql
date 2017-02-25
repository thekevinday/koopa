/** First time or one time execution stuff **/
/** Things here must be run first and cannot be run a second time due to their nature. **/
/** For example, tablespaces need only be created 1x and then any database on the system can use them **/
/** Be sure to replace reservation_ and reservation- with the prefix that is specific to your database. **/



/** Create the roles (note: public_user and public_users are expected to have already been created. Users and not roles should be created with noinherit) **/
create role reservation_users inherit nologin;
create role reservation_users_administer inherit nologin;
create role reservation_users_manager inherit nologin;
create role reservation_users_auditor inherit nologin;
create role reservation_users_publisher inherit nologin;
create role reservation_users_insurer inherit nologin;
create role reservation_users_financer inherit nologin;
create role reservation_users_reviewer inherit nologin;
create role reservation_users_editor inherit nologin;
create role reservation_users_drafter inherit nologin;
create role reservation_users_requester inherit nologin;

grant reservation_users to reservation_users_administer with admin option;
grant reservation_users to reservation_users_manager with admin option;
grant reservation_users to reservation_users_auditor;
grant reservation_users to reservation_users_publisher;
grant reservation_users to reservation_users_insurer;
grant reservation_users to reservation_users_financer;
grant reservation_users to reservation_users_reviewer;
grant reservation_users to reservation_users_editor;
grant reservation_users to reservation_users_drafter;
grant reservation_users to reservation_users_requester;

grant reservation_users_manager to reservation_users_administer with admin option;

grant reservation_users_auditor to reservation_users_administer with admin option;
grant reservation_users_auditor to reservation_users_manager with admin option;

grant reservation_users_publisher to reservation_users_administer with admin option;
grant reservation_users_publisher to reservation_users_manager with admin option;

grant reservation_users_insurer to reservation_users_administer with admin option;
grant reservation_users_insurer to reservation_users_manager with admin option;

grant reservation_users_financer to reservation_users_administer with admin option;
grant reservation_users_financer to reservation_users_manager with admin option;

grant reservation_users_reviewer to reservation_users_administer with admin option;
grant reservation_users_reviewer to reservation_users_manager with admin option;

grant reservation_users_editor to reservation_users_administer with admin option;
grant reservation_users_editor to reservation_users_manager with admin option;

grant reservation_users_drafter to reservation_users_administer with admin option;
grant reservation_users_drafter to reservation_users_manager with admin option;

grant reservation_users_requester to reservation_users_administer with admin option;
grant reservation_users_requester to reservation_users_manager with admin option;

/** This is the role the database should use to connect to to perform system activity **/
create role reservation_user;

grant reservation_users to reservation_user;


/** This is the role and role group the database should use for anonymous traffic. I further suggest setting up pg_hba.conf to allow non-ssl connections (increase performance as public information is still public). **/
/** If the data is meant to be private, then have the public account use the system user with ssl connections **/
create role public_users;
create role public_user;

grant public_users to public_user;

