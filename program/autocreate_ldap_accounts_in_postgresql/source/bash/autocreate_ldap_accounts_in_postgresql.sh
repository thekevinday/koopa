#!/bin/bash
#
# autocreate_ldap_accounts_in_postgresql      Helper service for auto-populating ldap accounts in postgresql.
#
# chkconfig: 345 40 60
# description: Provide a per-database/per-role way to auto-create ldap accounts and auto assign a single role.

### BEGIN INIT INFO
# Provides: autocreate_ldap_accounts_in_postgresql
# Required-Start: $local_fs $network
# Required-Stop: $local_fs $network
# Default-Start: 3 4 5
# Default-Stop: 0 1 2 6
# Short-Description: Auto-create ldap-based accounts in postgresql.
# Description: Provide a per-database/per-role way to auto-create ldap accounts and auto assign a single role.
### END INIT INFO

# Source function library, found on some sysvinit systems.
load_sysvinit() {
  if [[ -e /etc/rc.d/init.d/functions ]] ; then
    . /etc/rc.d/init.d/functions
  fi
}

# Source function library, found on some systemd systems.
load_systemd() {
  if [[ -e /lib/lsb/init-functions ]] ; then
    . /lib/lsb/init-functions
  fi
}

main() {
  local process_owner="alap"
  local process_group="alap"
  local path_programs="/programs/"
  local path_service="${path_programs}bin/autocreate_ldap_accounts_in_postgresql"
  local path_settings="${path_programs}settings/autocreate_ldap_accounts_in_postgresql/"
  local path_systems="${path_settings}systems.settings"
  local path_pids="/var/run/autocreate_ldap_accounts_in_postgresql/"
  local parameter_system=$2
  local alap_systems=
  local i=
  local j=

  # when process_owner is defined, make sure that the binary has the following set:
  # setcap cap_net_bind_service=ep /programs/bin/autocreate_ldap_accounts_in_postgresql

  if [[ ! -f $path_systems ]] ; then
    echo "No valid path_systems file defined at: $path_systems"
    exit -1
  fi

  if [[ ! -d $path_pids ]] ; then
    mkdir -p $path_pids
  fi

  if [[ $process_owner != "" ]] ; then
    chown $process_owner $path_pids
  fi

  alap_systems=$(grep -o '^alap_systems[[:space:]][[:space:]]*.*$' $path_systems | sed -e 's|^alap_systems[[:space:]][[:space:]]*||')

  if [[ $alap_systems == "" ]] ; then
    echo "No valid systems defined by setting 'alap_systems' in file: $path_systems"
    exit -1
  fi

  if [[ $parameter_system != "" ]] ; then
    j=$alap_systems
    alap_systems=

    for i in $j ; do
      if [[ $i == $parameter_system ]] ; then
        alap_systems=$i
        break;
      fi
    done

    i=
    j=

    if [[ $alap_systems == "" ]] ; then
      echo "System '$parameter_system' is not a valid system defined by setting 'alap_systems' in file: $path_systems"
      exit -1
    fi
  fi

  j=$alap_systems
  alap_systems=
  for i in $j ; do
    if [[ -f $path_settings${i}.settings ]] ; then
      alap_systems="$alap_systems$i "
    else
      echo "Skipping system '$i' because it does not have a settings file defined here: '$path_settings${i}.settings'"
    fi
  done

  i=
  j=

  case "$1" in
    start)
      do_start
      ;;
    stop)
      do_stop
      ;;
    restart)
      do_restart
      ;;
    status)
      do_status
      ;;
    *)
      echo "Usage: autocreate_ldap_accounts_in_postgresql {start|stop|restart|status}"
      return 2
  esac

  return $?
}

do_start() {
  local alap_name_system=
  local alap_name_group=
  local alap_name_database=
  local alap_connect_user=
  local alap_connect_password=
  local alap_port=
  local alap_system=
  local result=
  local any_success=0
  local any_failure=0

  for alap_system in $alap_systems ; do
    load_system_settings
    check_pid

    if [[ $result -eq -1 ]] ; then
      continue
    elif [[ $result -gt 0 ]] ; then
      echo "Not starting process for $alap_system, it is already running with pid=$pid."
      continue
    fi

    start_command

    if [[ $result -eq 0 ]] ; then
      wait_pid

      if [[ $result -eq 0 ]] ; then
        get_pid
      fi

      if [[ $pid == "" ]] ; then
        echo "Started process for $alap_system but was unable to determine pid, command: $path_service $alap_name_system $alap_name_group $alap_name_database $alap_port."
      else
        echo "Successfully started process for $alap_system, pid=$pid, command: $path_service $alap_name_system $alap_name_group $alap_name_database $alap_port."
      fi
    fi
  done

  if [[ $any_success -ne 0 || $any_failure -eq 1 ]] ; then
    exit -1
  fi

  return 0
}

do_stop() {
  local alap_name_system=
  local alap_name_group=
  local alap_name_database=
  local alap_port=
  local alap_system=
  local result=
  local any_success=0
  local any_failure=0
  local original_pid=

  for alap_system in $alap_systems ; do
    load_system_settings
    get_pid

    if [[ $pid == "" ]] ; then
      continue
    fi

    stop_command

    if [[ $result -eq 0 ]] ; then
      original_pid=$pid
      check_pid

      if [[ $result -eq -2 ]] ; then
        echo "Successfully stopped process for $alap_system, pid=$original_pid."
      else
        echo "Sent stop command for $alap_system, pid=$pid, but pid file ($pid_file) still exists."
      fi
    fi
  done

  if [[ $any_success -ne 0 || $any_failure -eq 1 ]] ; then
    exit -1
  fi

  return 0
}

do_restart() {
  local alap_name_system=
  local alap_name_group=
  local alap_name_database=
  local alap_port=
  local alap_system=
  local result=
  local any_success=0
  local any_failure=0
  local original_pid=

  for alap_system in $alap_systems ; do
    load_system_settings
    check_pid

    if [[ $result -lt 0 ]] ; then
      continue
    elif [[ $result -gt 0 ]] ; then
      stop_command

      if [[ $result -ne 0 ]] ; then
        continue
      fi

      original_pid=$pid
      check_pid

      if [[ $result -eq -2 ]] ; then
        echo "Successfully stopped process for $alap_system, pid=$original_pid."
      else
        echo "Sent stop command for $alap_system, pid=$original_pid, but pid file ($pid_file) still exists (cannot start process, skipping)."
        continue
      fi
    fi

    start_command

    if [[ $result -eq 0 ]] ; then
      wait_pid

      if [[ $result -eq 0 ]] ; then
        get_pid
      fi

      if [[ $pid == "" ]] ; then
        echo "Started process for $alap_system but was unable to determine pid, command: $path_service $alap_name_system $alap_name_group $alap_name_database $alap_port."
      else
        echo "Successfully started process for $alap_system, pid=$pid, command: $path_service $alap_name_system $alap_name_group $alap_name_database $alap_port."
      fi
    fi
  done

  if [[ $any_success -ne 0 ]] ; then
    exit -1
  fi

  return 0
}

do_status() {
  local alap_name_system=
  local alap_name_group=
  local alap_name_database=
  local alap_port=
  local alap_system=
  local pid_file=
  local pid=
  local result=

  for alap_system in $alap_systems ; do
    load_system_settings
    get_pid

    if [[ $pid == "" ]] ; then
      continue
    fi

    echo "The system '$alap_system' appears to be running as process $pid."
  done

  return 0
}

load_system_settings() {
  local path_system=$path_settings${alap_system}.settings
  alap_name_system=
  alap_name_group=
  alap_name_database=
  alap_connect_user=
  alap_connect_password=
  alap_port=

  if [[ $alap_system == "" || ! -f $path_system ]] ; then
    echo "No valid path_systems file defined at: $path_system"
    exit -1
  fi

  alap_name_system=$(grep -o '^alap_name_system[[:space:]][[:space:]]*.*$' $path_system | sed -e 's|^alap_name_system[[:space:]][[:space:]]*||')
  alap_name_group=$(grep -o '^alap_name_group[[:space:]][[:space:]]*.*$' $path_system | sed -e 's|^alap_name_group[[:space:]][[:space:]]*||')
  alap_name_database=$(grep -o '^alap_name_database[[:space:]][[:space:]]*.*$' $path_system | sed -e 's|^alap_name_database[[:space:]][[:space:]]*||')
  alap_connect_user=$(grep -o '^alap_connect_user[[:space:]][[:space:]]*.*$' $path_system | sed -e 's|^alap_connect_user[[:space:]][[:space:]]*||')
  alap_connect_password=$(grep -o '^alap_connect_password[[:space:]][[:space:]]*.*$' $path_system | sed -e 's|^alap_connect_password[[:space:]][[:space:]]*||')
  alap_port=$(grep -o '^alap_port[[:space:]][[:space:]]*.*$' $path_system | sed -e 's|^alap_port[[:space:]][[:space:]]*||')

  if [[ $alap_name_system == "" ]] ; then
    echo "No valid alap_name_system setting defined in file: $path_system"
    exit -1
  fi

  if [[ $alap_name_group == "" ]] ; then
    echo "No valid alap_name_group setting defined in file: $path_system"
    exit -1
  fi

  if [[ $alap_name_database == "" ]] ; then
    echo "No valid alap_name_database setting defined in file: $path_system"
    exit -1
  fi

  if [[ $alap_connect_user == "" ]] ; then
    echo "No valid alap_connect_user setting defined in file: $path_system"
    exit -1
  fi

  if [[ $alap_port == "" ]] ; then
    echo "No valid alap_port setting defined in file: $path_system"
    exit -1
  fi
}

start_command() {
  export alap_connect_user="$alap_connect_user"
  export alap_connect_password="$alap_connect_password"

  if [[ $process_owner == "" ]] ; then
    $path_service $alap_name_system $alap_name_group $alap_name_database $alap_port
    result=$?
  else
    su $process_owner -m -c "$path_service $alap_name_system $alap_name_group $alap_name_database $alap_port"
    result=$?
  fi

  if [[ $result -ne 0 ]] ; then
    echo "Failed to start process, command: $path_service $alap_name_system $alap_name_group $alap_name_database $alap_port."
    any_failure=1
  else
    any_success=1
  fi
}

stop_command() {
  # -3 = SIGQUIT, -15 = SIGTERM, -9 = SIGKILL
  kill -3 $pid
  result=$?

  if [[ $result -ne 0 ]] ; then
    echo "Signal to quit failed, command: kill -3 $pid."
    any_failure=1
  else
    any_success=1

    # pause and give the process time to close down.
    sleep 0.1
  fi
}

wait_pid() {
  local k=
  local max=32

  pid=
  pid_file=$path_pids$alap_system.pid
  result=-1

  # the started process will go into the background, so wait until the pid file is created, but only wait for so long.
  let k=0
  while [[ $k -lt $max ]] ; do
    if [[ -f $pid_file ]] ; then
      result=0
      break
    fi

    sleep 0.05

    let k=$k+1
  done

  return 0
}

get_pid() {
  pid=
  pid_file=$path_pids$alap_system.pid

  if [[ ! -f $pid_file ]] ; then
    echo "No pid file ($pid_file) found for system '$alap_system', it must not be running."
    return 0
  fi

  pid=$(cat $pid_file)
  result=$?

  if [[ $result -ne 0 ]] ; then
    echo "Failed to read the pid file ($pid_file) for system '$alap_system', command: cat $pid_file."
    pid=
    return 0
  fi

  if [[ $pid == "" ]] ; then
    echo "The pid file ($pid_file) for system '$alap_system' is empty."
    pid=
    return 0
  fi

  result=$(ps --no-headers -o pid -p $pid)
  if [[ $? -lt 0 ]] ; then
    echo "An error occured while searching for the process for system '$alap_system', command: ps --no-headers -o pid -p $pid."
    pid=
    return 0
  fi

  if [[ $result == "" ]] ; then
    echo "No process $pid was found for the system '$alap_system', the pid file might be stale or inaccurate."
    pid=
    return 0
  fi
}

check_pid() {
  pid=
  pid_file=$path_pids$alap_system.pid

  if [[ ! -f $pid_file ]] ; then
    result=-2
    return 0
  fi

  pid=$(cat $pid_file)
  result=$?

  if [[ $result -ne 0 ]] ; then
    echo "Failed to read the pid file ($pid_file) for system '$alap_system', command: cat $pid_file."
    result=-1
    return 0
  fi

  if [[ $pid == "" ]] ; then
    result=0
    return 0
  fi

  result=$(ps --no-headers -o pid -p $pid)
  if [[ $? -lt 0 ]] ; then
    echo "An error occured while searching for the process for system '$alap_system', command: ps --no-headers -o pid -p $pid."
    result=-1
    return 0
  fi

  if [[ $result == "" ]] ; then
    result=

    # the pid file is invalid, so remove the pid file.
    rm -f $pid_file

    # return 0 to allow for starting a new process.
    result=0
    return 0
  fi

  result=1
  return 0
}

load_sysvinit
load_systemd
main "$1" "$2"
