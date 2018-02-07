Installation
============
This assumes that the /program/ paths are being used.

Compile the source code:
  gcc -g source/c/sessionize_ldap_accounts_in_postgresql.c -o /programs/bin/sessionize_ldap_accounts_in_postgresql

Add and enable the init script:
  Usng common sysvinit tools:
    cp -v source/bash/sessionize_accounts.sh /etc/init.d/sessionize_accounts
    chkconfig --add sessionize_accounts
    chkconfig sessionize_accounts on

  Using common systemd tools:
    Using sysvinit inside of systemd:
      cp -v source/bash/sessionize_accounts.sh /etc/init.d/sessionize_accounts
      systemctl enable sessionize_accounts

    Using systemd native tools:
      @todo

Configure the settings (assuming system called "example"):
  mkdir -vp /programs/settings/sessionize_ldap_accounts_in_postgresql/
  cp -v settings/{example,systems}.settings /programs/settings/sessionize_ldap_accounts_in_postgresql/

Note: rename 'example.settings' to the name of the system as defined in 'systems.settings'.

Start the service
  service sessionize_ldap_accounts_in_postgresql start

For most users, the /programs/ path needs to be changed to a custom path for your system.
The source code and bash scripts will need to be updated with these hardcoded paths.

