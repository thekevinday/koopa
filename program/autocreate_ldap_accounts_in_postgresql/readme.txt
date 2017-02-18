Installation
============
This assumes that the /programs/ paths are being used.

Compile the source code:
  gcc -g -lldap -lpq source/c/autocreate_ldap_accounts_in_postgresql.c -o /programs/bin/autocreate_ldap_accounts_in_postgresql

Add and enable the init script:
  cp -v source/bash/autocreate_ldap_accounts_in_postgresql.sh /etc/init.d/autocreate_ldap_accounts_in_postgresql
  chkconfig --add autocreate_ldap_accounts_in_postgresql
  chkconfig autocreate_ldap_accounts_in_postgresql on

Configure the settings (assuming system called "example"):
  mkdir -vp /programs/settings/autocreate_ldap_accounts_in_postgresql/
  cp -v settings/{example,systems}.settings /programs/settings/autocreate_ldap_accounts_in_postgresql/

Note: rename 'example.settings' to the name of the system as defined in 'systems.settings'.

Start the service
  service autocreate_ldap_accounts_in_postgresql start

For most users, the /programs/ path needs to be changed to a custom path for your system.
The source code and bash scripts will need to be updated with these hardcoded paths.
