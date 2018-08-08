<?php
  // This is an example client script for talking to the service.
  define('PACKET_MAX_LENGTH', 4096);

  error_reporting(E_ALL);

  // command for executing this via a socket file.
  $system_name = 'example';
  $socket_path = '/var/www/sockets/sessionize_accounts/' . $system_name . '/sessions.socket';
  $socket_family = AF_UNIX;
  $socket_port = 0;
  $socket_protocol = 0;

  $socket_type = SOCK_STREAM;


  // open a client socket.
  $socket = socket_create($socket_family, $socket_type, $socket_protocol);

  if ($socket === FALSE) {
    print("Session Request: Something went wrong with socket_create().\n");
    socket_close($socket);
    return;
  }

  // connect to the socket.
  $connected = socket_connect($socket, $socket_path, $socket_port);

  if ($connected === FALSE) {
    print("Session Request: Something went wrong with socket_connect().\n");
    socket_close($socket);
    return;
  }


  // build session request packet
  $request_array = array(
    'name' => 'some_user',
    'ip' => '127.0.0.1',
    'password' => 'This is weak\' password.',
  );

  $request_json = json_encode($request_array);
  $written = socket_write($socket, $request_json);

  if ($written === FALSE) {
    print("Session Request: Something went wrong with socket_write().\n");
    socket_close($socket);
    return;
  }
  else if ($written == 0) {
    print("Session Request: Nothing was written to the socket using socket_write().\n");
    socket_close($socket);
    return;
  }

  // read the return result from the target socket.
  $response = socket_read($socket, PACKET_MAX_LENGTH);

  if (!is_string($response) || strlen($response) == 0) {
    print("Session Request: Something went wrong with socket_read() and did not get a valid return from the socket.\n");
    socket_close($socket);
    return;
  }

  // an array is expected to be returned by the socket.
  $response_array = json_decode($response, TRUE);
  if (!is_array($response_array['result']) || empty($response_array['result']['session_id']) || !is_string($response_array['result']['session_id'])) {
    print("Session Request: no valid session id was returned.\n");
    socket_close($socket);
    return;
  }

  socket_close($socket);

  $session_id = $response_array['result']['session_id'];
  $expire = $response_array['result']['expire'];
  $max = $response_array['result']['max'];



  // build password request packet using the returned session id.
  $socket = socket_create($socket_family, $socket_type, $socket_protocol);

  if ($socket === FALSE) {
    print("Password Request: Something went wrong with socket_create().\n");
    socket_close($socket);
    return;
  }

  $connected = socket_connect($socket, $socket_path, $socket_port);

  if ($connected === FALSE) {
    print("Password Request: Something went wrong with socket_connect().\n");
    socket_close($socket);
    return;
  }

  $request_array = array(
    'ip' => '127.0.0.1',
    'session_id' => $session_id,
  );

  $request_json = json_encode($request_array);
  $written = socket_write($socket, $request_json);

  if ($written === FALSE) {
    print("Password Request: Something went wrong with socket_write().\n");
    socket_close($socket);
    return;
  }
  else if ($written == 0) {
    print("Password Request: Nothing was written to the socket using socket_write().\n");
    socket_close($socket);
    return;
  }

  // read the return result from the target socket.
  $response = socket_read($socket, PACKET_MAX_LENGTH);

  if (!is_string($response) || strlen($response) == 0) {
    print("Password Request: Something went wrong with socket_read() and did not get a valid return from the socket.\n");
    socket_close($socket);
    return;
  }

  // an integer is expected to be returned by the socket.
  $response_array = json_decode($response, TRUE);

  $password = $response_array['result'];
  if (is_bool($password)) {
    print("Password Request: no password was returned.\n");
    socket_close($socket);
    return;
  }


  socket_close($socket);
