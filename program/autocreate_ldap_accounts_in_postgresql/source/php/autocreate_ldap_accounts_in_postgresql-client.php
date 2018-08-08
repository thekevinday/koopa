<?php
  // This is an example client script for talking to the service.

  error_reporting(E_ALL);

  // command for executing this via a socket file.
  #$socket_path = "/var/www/sockets/autocreate_ldap_accounts_in_postgresql/example/example_users.socket";
  #$socket_family = AF_UNIX;
  #$socket_port = 0;
  #$socket_protocol = 0;

  // command for executing via a network socket.
  $socket_path = "example.com";
  $socket_family = AF_INET;
  $socket_port = 1234;
  $socket_protocol = SOL_TCP;

  $socket_type = SOCK_STREAM;

  $packet_size_target = 63;
  $packet_size_client = 1;


  // open a client socket.
  $socket = socket_create($socket_family, $socket_type, $socket_protocol);

  if ($socket === FALSE) {
    print("Something went wrong with socket_create().\n");
    socket_close($socket);
    return;
  }

  // connect to the socket.
  $connected = socket_connect($socket, $socket_path, $socket_port);

  if ($connected === FALSE) {
    print("Something went wrong with socket_connect().\n");
    socket_close($socket);
    return;
  }


  // build packet for requesting that the user 'example' should be created.
  $test_name = 'example';
  $test_name_length = strlen($test_name);
  $test_name_difference = $packet_size_target - $test_name_length;

  if ($test_name_difference > 0) {
    // the packet expects a packet to be NULL terminated or at most $packet_size_target.
    $test_packet = pack('a' . $test_name_length . 'x' . $test_name_difference, $test_name);
  }
  else {
    $test_packet = pack('a' . $test_name_length, $test_name);
  }

  print("Packet looks like: '$test_packet'\n");
  $written = socket_write($socket, $test_packet, $packet_size_target);

  if ($written === FALSE) {
    print("Something went wrong with socket_write().\n");
    socket_close($socket);
    return;
  }
  else if ($written == 0) {
    print("Nothing was written to the socket using socket_write().\n");
    socket_close($socket);
    return;
  }


  // read the return result from the target socket.
  $response = socket_read($socket, $packet_size_client);

  if (!is_string($response) || strlen($response) == 0) {
    print("Something went wrong with socket_read() and did not get a valid return from the socket.\n");
    socket_close($socket);
    return;
  }

  // an integer is expected to be returned by the socket.
  $response_packet = unpack('C', $response);
  $response_value = (int) $response_packet[1];

  print("Target Socket Replied with = " . print_r($response_value, TRUE) . "\n");

  // response codes as defined in the c source file:
  //    0 = no problems detected.
  //    1 = invalid user name, bad characters, or name too long.
  //    2 = failed to connect to the ldap server and could not query the ldap name.
  //    3 = user name not found in ldap database.
  //    4 = failed to connect to the database.
  //    5 = error returned while executing the SQL command.
  //    6 = error occured while reading input from the user (such as via recv()).
  //    7 = error occured while writing input from the user (such as via send()).
  //    8 = the received packet is invalid, such as wrong length.
  //   10 = connection timed out when reading or writing.
  //   11 = the connection is being forced closed.
  //   12 = the connection is closing because the service is quitting.


  socket_close($socket);
