#!/bin/bash
#
# sessionize_accounts      Helper service for handling account sessions, namely password storage/retrieval.
#
# chkconfig: 345 40 60
# description: Provide a per-user, per-ip_address, per-session storage of passwords for users.

### BEGIN INIT INFO
# Provides: sessionize_accounts
# Required-Start: $local_fs $network
# Required-Stop: $local_fs $network
# Default-Start: 3 4 5
# Default-Stop: 0 1 2 6
# Short-Description: Provides session storage of usernames and passwords on a per ip-address basis.
# Description: Provides session storage of usernames and passwords on a per ip-address basis.
### END INIT INFO

# Source function library.
if [[ -f /etc/rc.d/init.d/functions ]] ; then
  . /etc/rc.d/init.d/functions
fi

main() {
  local process_owner=
  local process_group="apache"
  local path_programs="/programs/"
  local path_service="/usr/local/bin/php ${path_programs}bin/sessionize_accounts-server.php"
  local path_settings="${path_programs}settings/sessionize_accounts/"
  local path_systems="${path_settings}systems.settings"
  local path_pids="/programs/run/sessionize_accounts/"
  local path_socket_directory="/programs/sockets/sessionize_accounts/"
  local path_socket_name="sessions.socket"
  local path_socket_directory_mask="u+rwx,g+rx,o-rwx"
  local path_socket_name_mask="ugo+rw-x"
  local parameter_system=$2
  local sa_systems=
  local i=
  local j=

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

  sa_systems=$(grep -o '^sa_systems[[:space:]][[:space:]]*.*$' $path_systems | sed -e 's|^sa_systems[[:space:]][[:space:]]*||')

  if [[ $sa_systems == "" ]] ; then
    echo "No valid systems defined by setting 'sa_systems' in file: $path_systems"
    exit -1
  fi

  if [[ $parameter_system != "" ]] ; then
    j=$sa_systems
    sa_systems=

    for i in $j ; do
      if [[ $i == $parameter_system ]] ; then
        sa_systems=$i
        break;
      fi
    done

    i=
    j=

    if [[ $sa_systems == "" ]] ; then
      echo "System '$parameter_system' is not a valid system defined by setting 'sa_systems' in file: $path_systems"
      exit -1
    fi
  fi

  # system-specific settings are not needed by this script.
  #j=$sa_systems
  #sa_systems=
  #for i in $j ; do
  #  if [[ -f $path_settings${i}.settings ]] ; then
  #    sa_systems="$sa_systems$i "
  #  else
  #    echo "Skipping system '$i' because it does not have a settings file defined here: '$path_settings${i}.settings'"
  #  fi
  #done

  i=
  j=

  case "$1" in
    start)
      start
      ;;
    stop)
      stop
      ;;
    restart)
      restart
      ;;
    status)
      status
      ;;
    *)
      echo "Usage: sessionize_accounts {start|stop|restart|status}"
      return 2
  esac

  return $?
}

start() {
  local sa_system=
  local result=
  local any_success=0
  local any_failure=0

  for sa_system in $sa_systems ; do
    load_system_settings
    check_pid

    if [[ $result -eq -1 ]] ; then
      continue
    elif [[ $result -gt 0 ]] ; then
      echo "Not starting process for $sa_system, it is already running with pid=$pid."
      continue
    fi

    start_command

    if [[ $result -eq 0 ]] ; then
      wait_pid

      if [[ $result -eq 0 ]] ; then
        get_pid
      fi

      if [[ $pid == "" ]] ; then
        echo "Started process for $sa_system but was unable to determine pid, command: $path_service $sa_system."
      else
        echo "Successfully started process for $sa_system, pid=$pid, command: $path_service $sa_system."
      fi
    fi
  done

  if [[ $any_success -ne 0 || $any_failure -eq 1 ]] ; then
    exit -1
  fi

  return 0
}

stop() {
  local sa_system=
  local result=
  local any_success=0
  local any_failure=0
  local original_pid=

  for sa_system in $sa_systems ; do
    load_system_settings
    get_pid

    if [[ $pid == "" ]] ; then
      continue
    fi

    stop_command

    if [[ $result -eq 0 ]] ; then
      echo "Successfully stopped process for $sa_system, pid=$pid."
    fi
  done

  if [[ $any_success -ne 0 || $any_failure -eq 1 ]] ; then
    exit -1
  fi

  return 0
}

restart() {
  local sa_system=
  local result=
  local any_success=0
  local any_failure=0
  local original_pid=

  for sa_system in $sa_systems ; do
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
        echo "Successfully stopped process for $sa_system, pid=$original_pid."
      else
        echo "Sent stop command for $sa_system, pid=$original_pid, but pid file ($pid_file) still exists (cannot start process, skipping)."
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
        echo "Started process for $sa_system but was unable to determine pid, command: $path_service $sa_system."
      else
        echo "Successfully started process for $sa_system, pid=$pid, command: $path_service $sa_system."
      fi
    fi
  done

  if [[ $any_success -ne 0 ]] ; then
    exit -1
  fi

  return 0
}

status() {
  local sa_system=
  local pid_file=
  local pid=
  local result=

  for sa_system in $sa_systems ; do
    load_system_settings
    get_pid

    if [[ $pid == "" ]] ; then
      continue
    fi

    echo "The system '$sa_system' appears to be running as process $pid."
  done

  return 0
}

load_system_settings() {
  local path_system=$path_settings${sa_system}.settings

  # nothing to load
}

start_command() {
  # guarantee that all directories in the socket file's path exist.
  if [[ ! -d $path_socket_directory/$sa_system/ ]] ; then
    mkdir -p $path_socket_directory/$sa_system/
    chown $process_owner $path_socket_directory/$sa_system/
  fi

  # guarantee that the '$process_group' has read and execute only access to the directory, deny world access.
  chgrp $process_group $path_socket_directory/$sa_system/
  chmod $path_socket_directory_mask $path_socket_directory/$sa_system/

  # make sure no session socket already exists before starting.
  # this assumes that the pid file has already been checked and therefore no existing process is using the socket file (aka: assume this is a stale socket file).
  if [[ -e $path_socket_directory/$sa_system/$path_socket_name ]] ; then
    rm -f $path_socket_directory/$sa_system/$path_socket_name
  fi

  if [[ $process_owner == "" ]] ; then
    $path_service "$sa_system"
    result=$?
  else
    su $process_owner -l -c "$path_service \"$sa_system\""
    result=$?
  fi

  # make sure the socket has the desired permissions.
  if [[ -e $path_socket_directory/$sa_system/$path_socket_name ]] ; then
    chmod $path_socket_name_mask $path_socket_directory/$sa_system/$path_socket_name
  fi

  if [[ $result -ne 0 ]] ; then
    echo "Failed to start process, command: $path_service \"$sa_system\"."
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

    # cleanup the session socket ad pid file.
    rm -f $path_socket_directory/$sa_system/$path_socket_name
    rm -f $pid_file
  fi
}

wait_pid() {
  local k=
  local max=32

  pid=
  pid_file=$path_pids$sa_system.pid
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
  pid_file=$path_pids$sa_system.pid

  if [[ ! -f $pid_file ]] ; then
    echo "No pid file ($pid_file) found for system '$sa_system', it must not be running."
    return 0
  fi

  pid=$(cat $pid_file)
  result=$?

  if [[ $result -ne 0 ]] ; then
    echo "Failed to read the pid file ($pid_file) for system '$sa_system', command: cat $pid_file."
    pid=
    return 0
  fi

  if [[ $pid == "" ]] ; then
    echo "The pid file ($pid_file) for system '$sa_system' is empty."
    pid=
    return 0
  fi

  result=$(ps --no-headers -o pid -p $pid)
  if [[ $? -lt 0 ]] ; then
    echo "An error occured while searching for the process for system '$sa_system', command: ps --no-headers -o pid -p $pid."
    pid=
    return 0
  fi

  if [[ $result == "" ]] ; then
    echo "No process $pid was found for the system '$sa_system', the pid file might be stale or inaccurate."
    pid=
    return 0
  fi
}

check_pid() {
  pid=
  pid_file=$path_pids$sa_system.pid

  if [[ ! -f $pid_file ]] ; then
    result=-2
    return 0
  fi

  pid=$(cat $pid_file)
  result=$?

  if [[ $result -ne 0 ]] ; then
    echo "Failed to read the pid file ($pid_file) for system '$sa_system', command: cat $pid_file."
    result=-1
    return 0
  fi

  if [[ $pid == "" ]] ; then
    result=0
    return 0
  fi

  result=$(ps --no-headers -o pid -p $pid)
  if [[ $? -lt 0 ]] ; then
    echo "An error occured while searching for the process for system '$sa_system', command: ps --no-headers -o pid -p $pid."
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

main "$1" "$2"
