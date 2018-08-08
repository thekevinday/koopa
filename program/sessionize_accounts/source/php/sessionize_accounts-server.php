<?php
/**
 * Helper program for maintaining account sessions between PHP instances.
 *
 * This is necessary for accessing postgresql via ldap without requiring the user to enter in a new password every time a page is refreshed.
 * For security reasons, passwords are only stored in memory and that memory is freed as soon as the session expires.
 *
 * The timezone values used and returned will always be in UTC.
 *
 * @todo: provide the ability to save the current session information to the disk via a json array and encrypt it.
 *        On load, this file can be decrypted to load on startup.
 *        This allows for restarting the service without losing session information.
 *        This must be manually done by some user so that the password is never written to the disk.
 *
 * The program expects the following parameters: [system_name]
 *
 * This packet uses json to transmit data to and from the program.
 *
 *   A response packet will contain a json string that stores an array with the following keys:
 *     error:  FALSE on no error, an array containing error details on error.
 *     result: An array is returned for valid save requests.
 *             An array is returned for valid load requests.
 *             TRUE is returned for valid flush requests.
 *             TRUE is returned for valid close requests.
 *             FALSE is returned in all other cases.
 *
 *     Valid save response array keys:
 *     - session_id: The id of the session.
 *     - expire: The session expiration timeout (potentially reduced by hard-limits).
 *     - max: The max session expiration timeout (potentially reduced by hard-limits).
 *     - interval: the idle timeout interval, in seconds.
 *
 *     Valid load response array keys:
 *     - name: The user name associated with the session.
 *     - password: The password associated with the session.
 *     - expire: The session expiration timeout.
 *     - max: The max session expiration timeout.
 *     - interval: the idle timeout interval, in seconds.
 *     - settings: an array containing any additional settings associated with the session.
 *
 * A save request packet has the following keys:
 *   - name: The username to associate with the session.
 *   - ip: The ip address to associate with the session. These sessions are ip-address specific.
 *   - password: The password to associated with the session (must be defined, but may be NULL).
 *   - expire: Request idle timeout interval, in seconds (this is a soft limit and cannot exceed the hard limit defined by this server).
 *   - max: The maximum amount of time a session is allowed to exist, in seconds (this is a soft limit and cannot exceed the hard limit defined by this server).
 *   - settings: (optional) an array containing additional information that may be pertenent to save with the session information.
 *
 * A load request packet has the following keys:
 *   - ip: The ip address associated with the session.
 *   - session_id: The session id to load the password from.
 *
 * A close request packet has the following keys:
 *   - ip: The ip address associated with the session.
 *   - session_id: The session id to load the password from.
 *   - close: A boolean must be set to TRUE.
 *
 * A flush request packet (for manual flushing of expired data) has the following keys:
 *   flush: Must be set to TRUE.
 *
 *   The response will be TRUE on success, FALSE otherwise.
 *
 * Copyright Kevin Day, lgpl v2.1 or later.
 */

// This program needs to run indefinetely.
ini_set('max_execution_time', 0);
ini_set('memory_limit', '192M');

error_reporting(E_ALL);

define('SESSION_RANDOM_BYTES', 512);
//define('SESSION_ID_MAX', 1536);
define('SOCKET_BACKLOG', 1024);
define('SOCKET_TIMEOUT_SECONDS', 0);
define('SOCKET_TIMEOUT_MICROSECONDS', 40000); // 0.04 seconds.
define('PACKET_MAX_LENGTH', 4096);

define('INTERVAL_TIMEOUT_HARD_EXPIRE', 172800); // 48 hours.
define('INTERVAL_TIMEOUT_HARD_MAX', 1382400); // 16 days.

/**
 * Main function is self-contain to prevent leakage.
 *
 * @param int $argc
 *   The number of arguments passed through the command line.
 * @param array $argv
 *   An array of command line arguments.
 *
 * @return bool
 *   TRUE on success, FALSE otherwise.
 */
function main($argc, $argv) {
  if (!isset($argv[1]) || !is_string($argv[1]) || empty($argv[1])) {
    print("ERROR: This program requires exactly 1 argument: 'system_name'.\n");
    return FALSE;
  }

  // long running processes should have PHP's garbage collection enabled, otherwise no cleanup ever happens.
  gc_enable();

  $pid_path_directory = '/programs/run/sessionize_accounts/';
  $pid_path = $pid_path_directory . $argv[1] . '.pid';
  $socket_path = '/programs/sockets/sessionize_accounts/' . $argv[1] . '/sessions.socket';
  $socket_family = AF_UNIX;
  $socket_port = 0;
  $socket_protocol = 0;
  $socket_type = SOCK_STREAM;

  if (file_exists($socket_path)) {
    print("ERROR: The socket file '$socket_path' already exists.\n");
    return FALSE;
  }

  // Used as an unproven attempt to clear passwords from memory before delete in hopes to avoid security issues inherit in a garbage collector.
  $cleartext = str_repeat(' ', 2048);

  // open a client socket.
  $socket = socket_create($socket_family, $socket_type, $socket_protocol);

  if ($socket === FALSE) {
    print("ERROR: Something went wrong with socket_create().\n");
    return FALSE;
  }

  $bound = socket_bind($socket, $socket_path);

  if (socket_listen($socket, SOCKET_BACKLOG) === FALSE) {
    print("ERROR: Something went wrong with socket_listen().\n");
    socket_close($socket);
    unlink($socket_path);
    return FALSE;
  }

  if (file_exists($pid_path)) {
    print("ERROR: The pid file '$pid_path' already exists.\n");
    socket_close($socket);
    unlink($socket_path);
    return FALSE;
  }

  if (!is_dir($pid_path_directory)) {
    print("ERROR: The pid path directory '$pid_path_directory' does not exist or is not a directory.\n");
    socket_close($socket);
    unlink($socket_path);
    return FALSE;
  }

  // check to see if the pid file can be written to by writing the current pid.
  if (file_put_contents($pid_path, getmypid()) === FALSE) {
    print("ERROR: Cannot write to the pid file '$pid_path'.\n");
    socket_close($socket);
    unlink($socket_path);
    return FALSE;
  }

  unlink($pid_path);


  // background the process by forking and making the parent quit after spawning a child process.
  $forked = pcntl_fork();
  if ($forked == -1) {
    print("ERROR: failed to fork process, could not go into background.\n");
    return FALSE;
  }

  if ($forked) {
    return TRUE;
  }

  // force the timezone to always be in UTC.
  date_default_timezone_set('UTC');


  // immediately cleanup any unused memory to keep the footprint as small as possible before the program really does anything.
  gc_collect_cycles();


  // save the pid file at the pid path.
  file_put_contents($pid_path, getmypid());

  $database = array();
  $timeouts = array();


  // listening for connections indefinetely.
  do {
    $client_socket = socket_accept($socket);
    if ($client_socket === FALSE) {
      print("socket_accept() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n");

      unset($client_socket);
      continue;
    }

    socket_set_option($client_socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => SOCKET_TIMEOUT_SECONDS, 'usec' => SOCKET_TIMEOUT_MICROSECONDS));

    $response = array(
      'error' => FALSE,
      'result' => FALSE,
    );

    $encoded_packet = socket_read($client_socket, PACKET_MAX_LENGTH);

    if ($encoded_packet === FALSE) {
      print("socket_read() failed: reason: " . socket_strerror(socket_last_error($client_socket)) . "\n");

      socket_close($client_socket);
      unset($client_socket);
      unset($encoded_packet);
      continue;
    }

    if (!is_string($encoded_packet)) {
      $response['error'] = array(
        'target' => 'encoded_packet',
        'message' => "No valid encoded packet was specified. It must be a valid json string.",
      );

      socket_write($client_socket, json_encode($response));
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }

    $decoded_packet = json_decode($encoded_packet, TRUE);

    if (!is_array($decoded_packet) || empty($decoded_packet)) {
      $response['error'] = array(
        'target' => 'decoded_packet',
        'message' => "No valid decoded packet was specified. It must be a valid, non-empty, array.",
      );

      socket_write($client_socket, json_encode($response));
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }

    // support manually sending custom packets to periodically flush expired sessions (usefull for cron jobs).
    if (array_key_exists('flush', $decoded_packet)) {
      if (isset($decoded_packet['flush']) !== TRUE) {
        $not_found_error = array(
          'target' => 'decoded_packet[flush]',
          'message' => "Invalid flush value provided.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      if (count($decoded_packet) > 1) {
        $not_found_error = array(
          'target' => 'decoded_packet[*]',
          'message' => "Too many values provided, only the flush parameter is allowed.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      $response['result'] = process_expired_sessions($database, $timeouts, $cleartext);
      if ($response['result'] === FALSE) {
        $response['error'] = array(
          'target' => 'failure',
          'message' => "Failed to flush the expired sessions.",
        );
      }

      socket_write($client_socket, json_encode($response));
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }

    if (!isset($decoded_packet['ip']) || !is_string($decoded_packet['ip']) || strlen($decoded_packet['ip']) == 0 || ip2long($decoded_packet['ip']) === FALSE) {
      $response['error'] = array(
        'target' => 'decoded_packet[ip]',
        'message' => "No valid ip address was specified. A valid, non-empty, ip address string must be provided.",
      );

      socket_write($client_socket, json_encode($response));
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }


    // support closing sessions before they expire.
    if (array_key_exists('close', $decoded_packet)) {
      if (isset($decoded_packet['close']) !== TRUE) {
        $not_found_error = array(
          'target' => 'decoded_packet[close]',
          'message' => "Invalid close value provided.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      if (isset($decoded_packet['session_id']) && strlen($decoded_packet['session_id']) > 0) {
        $not_found_error = array(
          'target' => 'decoded_packet[session_id]',
          'message' => "No valid session was provided.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      if (count($decoded_packet) > 3) {
        $not_found_error = array(
          'target' => 'decoded_packet[*]',
          'message' => "Too many values provided, only the session_id, ip, and close parameters are allowed.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      // provide an error, but specifically do not give details about which field is invalid for security reasons.
      if (!isset($database['sessions'][$decoded_packet['ip']][$decoded_packet['session_id']])) {
        $response['error'] = array(
          'target' => 'not_found',
          'message' => "No valid session was found associated with the specified user name and ip address.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      $response['result'] = expire_session($database, $timeouts, $decoded_packet['session_id'], $decoded_packet['ip']);
      if ($response['result'] === FALSE) {
        $response['error'] = array(
          'target' => 'failure',
          'message' => "Failed to close the session by the given session id and ip address.",
        );
      }

      socket_write($client_socket, json_encode($response));
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }


    // expire sessions now so that expired session do not get included in the retrieval process.
    process_expired_sessions($database, $timeouts, $cleartext);


    // retrieve password.
    if (isset($decoded_packet['session_id']) && strlen($decoded_packet['session_id']) > 0) {
      if (!isset($database['sessions'][$decoded_packet['ip']][$decoded_packet['session_id']])) {
        $response['error'] = array(
          'target' => 'not_found',
          'message' => "No valid session was found associated with the specified user name and ip address.",
        );
        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }


      // a password request shows that this connection is still active, so extend the timestamp.
      $db_session = &$database['sessions'][$decoded_packet['ip']][$decoded_packet['session_id']];
      $unique_id = $db_session['timeouts']['id'];

      if (isset($db_session['timeouts']['expire']) && isset($db_session['timeouts']['max']) && $db_session['timeouts']['expire'] < $db_session['timeouts']['max']) {
        $stamp_old = $db_session['timeouts']['expire'];
        $stamp_new = strtotime('+' . $db_session['timeouts']['interval'] . ' seconds');
        if ($stamp_new > $db_session['timeouts']['max']) {
          $stamp_new = $db_session['timeouts']['max'];
        }

        if (isset($timeouts[$stamp_old]) && $stamp_old != $stamp_new) {
          $db_session['timeouts']['expire'] = $stamp_new;
          $new_key = !array_key_exists($stamp_new, $timeouts);

          $timeouts[$stamp_new]['expire'][$unique_id] = $timeouts[$stamp_old]['expire'][$unique_id];
          unset($timeouts[$stamp_old]['expire'][$unique_id]);

          if (empty($timeouts[$stamp_old]['expire'])) {
            unset($timeouts[$stamp_old]['expire']);
          }

          if (empty($timeouts[$stamp_old])) {
            unset($timeouts[$stamp_old]);
          }

          // only perform expensive sort if its a new key, which might be out of order.
          if ($new_key) {
            ksort($timeouts);
          }

          unset($new_key);
        }

        unset($stamp_new);
        unset($stamp_old);
      }

      $response['result'] = array(
        'name' => $db_session['name'],
        'password' => $db_session['password'],
        'expire' => $db_session['timeouts']['expire'],
        'max' => $db_session['timeouts']['max'],
        'interval' => $db_session['timeouts']['interval'],
        'settings' => $db_session['settings'],
      );

      socket_write($client_socket, json_encode($response));
      unset($db_timeout);
      unset($unique_id);
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }
    // store password.
    else if (array_key_exists('password', $decoded_packet) && (is_null($decoded_packet['password']) || is_string($decoded_packet['password']))) {
      if (!isset($decoded_packet['name']) || strlen($decoded_packet['name']) == 0 || preg_match('/^(\w|-)+$/i', $decoded_packet['name']) != 1) {
        $response['error'] = array(
          'target' => 'decoded_packet[name]',
          'message' => "No valid user name was specified. A valid, non-empty, user name string must be provided.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      if (!isset($decoded_packet['name']) || !is_string($decoded_packet['name']) || empty($decoded_packet['name'])) {
        $response['error'] = array(
          'target' => 'decoded_packet[name]',
          'message' => "No valid name was specified. A valid user name string, must be provided.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      if (isset($decoded_packet['settings']) && !is_array($decoded_packet['settings'])) {
        $response['error'] = array(
          'target' => 'decoded_packet[settings]',
          'message' => "If specified, settings must be a valid array.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      $session_id = build_session_id();
      if (isset($database['sessions'][$decoded_packet['ip']]) && is_array($database['sessions'][$decoded_packet['ip']]) && array_key_exists($session_id, $database['sessions'][$decoded_packet['ip']])) {
        $response['error'] = array(
          'target' => 'conflict',
          'message' => "Failed to generate a unique session id due to a conflict. Please try again.",
        );

        socket_write($client_socket, json_encode($response));
        unset($response);
        unset($encoded_packet);
        unset($session_id);

        socket_close($client_socket);
        unset($client_socket);
        continue;
      }

      $unique_id = $decoded_packet['name'] . '-' . uniqid();
      $database['sessions'][$decoded_packet['ip']][$session_id] = array(
        'name' => $decoded_packet['name'],
        'password' => $decoded_packet['password'],
        'timeouts' => array(
          'id' => $unique_id,
          'expire' => NULL,
          'max' => NULL,
        ),
        'settings' => isset($decoded_packet['settings']) ? $decoded_packet['settings'] : array(),
      );


      // the timeout only needs to contain what is necessary to obtain the session data.
      $timeout = array(
        'ip' => $decoded_packet['ip'],
        'session_id' => $session_id,
      );


      // grab optional soft timeouts and enforce hard timeouts.
      $timeout_expire = INTERVAL_TIMEOUT_HARD_EXPIRE;
      $timeout_max = INTERVAL_TIMEOUT_HARD_MAX;

      if (isset($decoded_packet['max']) && is_int($decoded_packet['max']) && $decoded_packet['max'] > 0) {
        if ($decoded_packet['max'] < INTERVAL_TIMEOUT_HARD_MAX) {
          $timeout_max = $decoded_packet['max'];

          if ($timeout_expire > $timeout_max) {
            $timeout_expire = $decoded_packet['max'];
          }
        }
      }

      if (isset($decoded_packet['expire']) && is_int($decoded_packet['expire']) && $decoded_packet['expire'] > 0) {
        if ($decoded_packet['expire'] < INTERVAL_TIMEOUT_HARD_EXPIRE && $decoded_packet['expire'] < $timeout_max) {
          $timeout_expire = $decoded_packet['expire'];
        }
      }


      // save basic timeout.
      $stamp = strtotime('+' . $timeout_expire . ' seconds');
      $database['sessions'][$decoded_packet['ip']][$session_id]['timeouts']['expire'] = $stamp;
      $database['sessions'][$decoded_packet['ip']][$session_id]['timeouts']['interval'] = $timeout_expire;
      $timeouts[$stamp]['expire'][$unique_id] = $timeout;


      // save maxiumum timeout.
      $stamp = strtotime('+' . $timeout_max . ' seconds');
      $database['sessions'][$decoded_packet['ip']][$session_id]['timeouts']['max'] = $stamp;
      $timeouts[$stamp]['max'][$unique_id] = $timeout;


      // sort timeouts array for faster cleanups (this action is required).
      ksort($timeouts);


      // return the session key.
      $response['result'] = array(
        'session_id' => $session_id,
        'expire' => $database['sessions'][$decoded_packet['ip']][$session_id]['timeouts']['expire'],
        'max' => $database['sessions'][$decoded_packet['ip']][$session_id]['timeouts']['max'],
        'interval' => $timeout_expire,
      );

      socket_write($client_socket, json_encode($response));
      unset($session_id);
      unset($stamp);
      unset($unique_id);
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }
    else {
      $response['error'] = array(
        'target' => 'no_session',
        'message' => "Either a new session_id must be requested by provided the password string or a password must be requested by providing the session id string.",
      );

      socket_write($client_socket, json_encode($response));
      unset($response);
      unset($encoded_packet);

      socket_close($client_socket);
      unset($client_socket);
      continue;
    }

    unset($response);
    unset($encoded_packet);

    socket_close($client_socket);
    unset($client_socket);
  } while (TRUE);

  socket_close($socket);
  unlink($socket_path);
  unlink($pid_path);
  return TRUE;
}

/**
 * Builds and returns a session id string.
 *
 * Based on drupal's drupal_random_bytes()
 *
 * @return string|false
 *   A base64 string.
 *   FALSE is returned on error.
 *
 * @see: https://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_random_bytes/7
 */
function build_session_id() {
  $fh = @fopen('/dev/urandom', 'rb');
  if (!$fh) {
    return FALSE;
  }

  $bytes = fread($fh, SESSION_RANDOM_BYTES);
  fclose($fh);

  return base64_encode($bytes);
}

/**
 * Searches through the database and removes expired keys.
 *
 * This uses a simple search design where the keys are expected to be in order.
 * When the first key that represents a future time is encountered, the function will exit.
 *
 * @param array $database
 *   An array of all usernames, ips, passwords, and sessions.
 * @param array $timeouts
 *   An array of all timeouts keyed in numeric order.
 * @param string $cleartext
 *  Used as an unproven attempt to clear passwords from memory before delete in hopes to avoid security issues inherit in a garbage collector.
 *
 * @param bool
 *   TRUE on success, FALSE otherwise.
 */
function process_expired_sessions(&$database, &$timeouts, $cleartext) {
  if (!is_array($database)) {
    return FALSE;
  }

  if (!is_array($timeouts)) {
    return FALSE;
  }

  // this is not a problem, it simply means that there is nothing to do.
  if (empty($database) || empty($timeouts)) {
    return TRUE;
  }

  $now = strtotime('now');
  $to_delete = array();
  foreach ($timeouts as $time => &$timeout) {
    if ($time > $now) {
      break;
    }

    // process expire timeouts.
    if (!empty($timeout['expire'])) {
      foreach ($timeout['expire'] as $unique_key => $setting) {
        $to_delete[$unique_key] = array(
          'ip' => $setting['ip'],
          'session_id' => $setting['session_id'],
        );
      }
      unset($unique_key);
      unset($setting);
    }

    // process max timeouts.
    if (!empty($timeout['max'])) {
      foreach ($timeout['max'] as $unique_key => $setting) {
        $to_delete[$unique_key] = array(
          'ip' => $setting['ip'],
          'session_id' => $setting['session_id'],
        );
      }
      unset($unique_key);
      unset($setting);
    }
  }
  unset($time);
  unset($timeout);


  // remove expired keys.
  if (!empty($to_delete)) {
    foreach ($to_delete as $unique_key => $setting) {
      if (isset($database['session'][$setting['ip']][$setting['session_id']])) {
        expire_session($database, $timeouts, $setting['session_id'], $setting['ip']);
      }
    }
    unset($unique_key);
    unset($setting);


    // force garbage collection cleanup.
    gc_collect_cycles();
  }

  return TRUE;
}

/**
 * pre-maturely expire a specific session.
 *
 * Use this to 'logout' of a session and remove the username and password information from this process before the session expires.
 *
 * @param array $database
 *   The database array.
 * @param array $timeouts
 *   The timeouts array.
 * @param string $session_id
 *   The session id string.
 * @param string $ip
 *   The ip address string.
 *
 * @param bool
 *   TRUE on success, FALSE otherwise.
 */
function expire_session(&$database, &$timeouts, $session_id, $ip) {
  if (!is_array($database) || !is_array($timeouts)) {
    return FALSE;
  }

  if (!is_string($session_id) || !is_string($ip)) {
    return FALSE;
  }

  if (!isset($database['sessions'][$ip][$session_id])) {
    // if it does not exist, then consider the close successful.
    return TRUE;
  }

  $session = $database['session'][$ip][$session_id];
  foreach (array('expire', 'max') as $key) {
    if (isset($timeouts[$session['timeouts'][$key]][$session['timeouts']['id']])) {
      unset($timeouts[$session['timeouts'][$key]][$session['timeouts']['id']]);

      if (empty($timeouts[$session['timeouts'][$key]])) {
        unset($timeouts[$session['timeouts'][$key]]);
      }
    }
  }
  unset($session);
  unset($key);

  $database['sessions'][$ip][$session_id]['password'] = $cleartext;
  unset($database['sessions'][$ip][$session_id]);

  if (empty($database['sessions'][$ip])) {
    unset($database['sessions'][$ip]);
  }

  return TRUE;
}


main($argc, $argv);
