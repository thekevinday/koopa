Installation
============
This assumes that the /programs/ paths are being used.

Add and enable the init script:
  Usng common sysvinit tools:
    cp -v source/bash/autocreate_ldap_accounts_in_postgresql.sh /etc/init.d/autocreate_ldap_accounts_in_postgresql
    chkconfig --add autocreate_ldap_accounts_in_postgresql
    chkconfig autocreate_ldap_accounts_in_postgresql on

  Using common systemd tools:
    Using sysvinit inside of systemd:
      cp -v source/bash/autocreate_ldap_accounts_in_postgresql.sh /etc/init.d/autocreate_ldap_accounts_in_postgresql
      systemctl enable autocreate_ldap_accounts_in_postgresql

    Using systemd native tools:
      @todo

Configure the settings (assuming system called "example"):
  mkdir -vp /programs/settings/autocreate_ldap_accounts_in_postgresql/
  cp -v settings/{example,systems}.settings /programs/settings/autocreate_ldap_accounts_in_postgresql/

Note: rename 'example.settings' to the name of the system as defined in 'systems.settings'.

Postgresql needs to have the database created and setup (see appropriate project).
Once the database is setup, an account needs to exist in the database with account creation access.
This is the account that needs to be defined in the example.settings file.
Using the example.settings file provided, this user would be '@todo'.
  @todo: describe process for creating and setting up postgresql account information.

The example.settings file has an alap_connect_user and an alap_connect_password for assigning user name and password for connection.
This user and password is for connecting to postgresql and should be assigned the appropriate create_ldap_users role as described below in postgresql.


Create the role in the postgresql (replace example_database with appropriate database name).
The "create_ldap_users" role can be assigned to the account that will login and create the roles (or used directly).
Example commands (will likely need to be run as postgresql admin user):
  psql example_database -c "create role create_ldap_users createrole"
  psql example_database -c "alter role create_ldap_users login"

The source code has a hard-coded port of 5433, be sure to open up appropriate firewall access and/or change that port number.
The source code has a hardcoded ldap server and search dn, be sure to update that as well where appropriate.
Compile the source code:
  gcc -g -lldap -lpq source/c/autocreate_ldap_accounts_in_postgresql.c -o /programs/bin/autocreate_ldap_accounts_in_postgresql

Start the service
  service autocreate_ldap_accounts_in_postgresql start

For most users, the /programs/ path needs to be changed to a custom path for your system.
The source code and bash scripts will need to be updated with these hardcoded paths.
