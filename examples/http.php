<?php
  // make sure the class files can be loaded.
  set_include_path('.');


  $root_path = 'common/base/classes/';
  require_once($root_path . 'base_http.php');
  require_once($root_path . 'base_database.php');
  require_once($root_path . 'base_cookie.php');

  class_alias('c_base_return', 'c_return');
  class_alias('c_base_return_status', 'c_status');
  class_alias('c_base_return_false', 'c_false');
  class_alias('c_base_return_true', 'c_true');
  class_alias('c_base_return_array', 'c_array');
  class_alias('c_base_return_string', 'c_string');
  class_alias('c_base_return_int', 'c_int');
  unset($root_path);


  function program() {
    $data_program['debug']['time_start'] = microtime(TRUE);
    $data_program['debug']['memory_usage'] = array(0 => memory_get_usage());
    $data_program['debug']['memory_peak'] = array(0 => memory_get_peak_usage());
    $data_program['debug']['data_program'] = array();

    $data_program['timestamp'] = microtime(TRUE);

    // 1: local settings:
    program_load_settings($data_program);

    $data_program['debug']['memory_usage'][1] = memory_get_usage();
    $data_program['debug']['memory_peak'][1] = memory_get_peak_usage();
    gc_collect_cycles();


    // 2: receive request information.
    program_receive_request($data_program);

    $data_program['debug']['memory_usage'][2] = memory_get_usage();
    $data_program['debug']['memory_peak'][2] = memory_get_peak_usage();
    gc_collect_cycles();


    // 3: perform actions, process work.
    program_process_request($data_program);

    $data_program['debug']['memory_usage'][3] = memory_get_usage();
    $data_program['debug']['memory_peak'][3] = memory_get_peak_usage();
    gc_collect_cycles();


    // 4: build or finalize theme.
    program_process_theme($data_program);

    $data_program['debug']['memory_usage'][4] = memory_get_usage();
    $data_program['debug']['memory_peak'][4] = memory_get_peak_usage();
    gc_collect_cycles();


    // 5: send reqsponse information.
    program_build_response($data_program);
    program_send_response($data_program);

    unset($data_program);
    gc_collect_cycles();
  }

  function program_load_settings(&$data_program) {
    ini_set('opcache.enable', FALSE);
    ini_set('opcache.enable_cli', FALSE);

    // only enable output buffering during the output stage, keep it disabled until then.
    ini_set('output_buffering', FALSE);
  }

  function program_load_session(&$data_program) {
    $database = new c_base_database();

    $remote_address = '127.0.0.1';
    if (!empty($_SERVER['REMOTE_ADDR'])) {
      $remote_address = $_SERVER['REMOTE_ADDR'];
    }

    // cookie is used to determine whether or not the user is logged in.
    $cookie = new c_base_cookie();
    $cookie->set_name("localhost");
    $cookie->set_path('/');
    $cookie->set_domain('.localhost');
    $cookie->set_secure(TRUE);

    $logged_in = FALSE;
    $failure = FALSE;

    $result = $cookie->do_pull();
    if ($result instanceof c_base_return_true) {
      $value = $cookie->get_value_exact();

      if ($cookie->validate() instanceof c_base_return_true && !empty($value['session_id'])) {
        $session = new c_base_session();
        $session->set_socket_directory('/programs/sockets/sessionize_accounts/');
        $session->set_system_name('example');
        $session->set_host($remote_address);
        $session->set_session_id($value['session_id']);
        $result = $session->do_connect();
        $failure = c_base_return::s_has_error($result);
        if (!$failure) {
          $result = $session->do_pull();
          $session->do_disconnect();
          $data_program['session'] = $result;
        }
        unset($failure);

        $connected = FALSE;
        if ($result instanceof c_base_return_true) {
          $name = $session->get_name()->get_value();
          $password = $session->get_name()->get_value();
          program_assign_database_string($database, $name, $password, $session);
          unset($name);
          unset($password);

          $connected = program_connect_database($database);
        }
        unset($result);

        if ($connected) {
          // check to see if the session timeout has been extended and if so, then update the cookie.
          $session_expire = cbri::s_value_exact($session->get_timeout_expire());
          $session_seconds = $session_expire - time();
          if ($session_seconds == 0) {
            $session_seconds = -1;
          }
          if ($session_expire > $value['expire']) {
            $value['expire'] = gmdate("D, d-M-Y H:i:s T", $session_expire);
            $cookie->set_value($value);
            $cookie->set_expires($session_expire);
          }
          unset($session_expire);
          unset($session_seconds);
        }
        unset($connected);
        unset($session);
      }
    }
    else {
      program_assign_database_string($database, 'example_user', NULL, NULL);
      $connected = program_connect_database($database);

      if ($connected) {
        $data_program['http']->set_response_content('Connected: success<br>.');
        $database->do_disconnect();
      }
      unset($connected);
    }
    unset($value);
    unset($cookie);
    unset($database);
    unset($remote_address);
    unset($logged_in);
  }

  function program_receive_request(&$data_program) {
    $data_program['http'] = new c_base_http();

    $data_program['http']->do_load_request();
    $data_program['http']->set_response_content("");

    program_load_session($data_program);
  }

  function program_process_request(&$data_program) {
    $timestamp = $data_program['http']->get_request_time();
    if (!($timestamp instanceof c_base_return_false)) {
      $data_program['http']->set_response_content("The request was made on (" . $timestamp->get_value_exact() . "): " . date("Y/m/d h:i:s a", $timestamp->get_value_exact()) . ": " . "<br>\n<br>\n");
    }
    unset($timestamp);

    $data_program['http']->set_response_content("The Complete Request Array: " . "<br>\n");
    $request = $data_program['http']->get_request()->get_value_exact();

    foreach ($request as $request_id => $request_values) {
      if (!$request_values['defined'] || $request_values['invalid']) {
        continue;
      }

      $request_name = "Undefined";

      switch ($request_id) {
        case c_base_http::REQUEST_ACCEPT:
          $request_name = "Accept";
          break;
        case c_base_http::REQUEST_ACCEPT_CHARSET:
          $request_name = "Accept";
          break;
        case c_base_http::REQUEST_ACCEPT_ENCODING:
          $request_name = "Accept-Encoding";
          break;
        case c_base_http::REQUEST_ACCEPT_LANGUAGE:
          $request_name = "Accept-Language";
          break;
        case c_base_http::REQUEST_ACCEPT_DATETIME:
          $request_name = "Accept-Datetime";
          break;
        case c_base_http::REQUEST_AUTHORIZATION:
          $request_name = "Authorization";
          break;
        case c_base_http::REQUEST_CACHE_CONTROL:
          $request_name = "Cache-Control";
          break;
        case c_base_http::REQUEST_CONNECTION:
          $request_name = "Connection";
          break;
        case c_base_http::REQUEST_COOKIE:
          $request_name = "Cookie";
          break;
        case c_base_http::REQUEST_CONTENT_LENGTH:
          $request_name = "Content-Length";
          break;
        case c_base_http::REQUEST_CONTENT_TYPE:
          $request_name = "Content-Type";
          break;
        case c_base_http::REQUEST_DATE:
          $request_name = "Date";
          break;
        case c_base_http::REQUEST_EXPECT:
          $request_name = "Expect";
          break;
        case c_base_http::REQUEST_FROM:
          $request_name = "From";
          break;
        case c_base_http::REQUEST_HOST:
          $request_name = "Host";
          break;
        case c_base_http::REQUEST_IF_MATCH:
          $request_name = "If-Match";
          break;
        case c_base_http::REQUEST_IF_MODIFIED_SINCE:
          $request_name = "If-Modified-Since";
          break;
        case c_base_http::REQUEST_IF_NONE_MATCH:
          $request_name = "If-None-Match";
          break;
        case c_base_http::REQUEST_IF_RANGE:
          $request_name = "If-Range";
          break;
        case c_base_http::REQUEST_IF_UNMODIFIED_SINCE:
          $request_name = "If-Unmodified-Since";
          break;
        case c_base_http::REQUEST_MAX_FORWARDS:
          $request_name = "Max-Forwards";
          break;
        case c_base_http::REQUEST_ORIGIN:
          $request_name = "Origin";
          break;
        case c_base_http::REQUEST_PRAGMA:
          $request_name = "Pragma";
          break;
        case c_base_http::REQUEST_PROXY_AUTHORIZATION:
          $request_name = "Prox-Authorization";
          break;
        case c_base_http::REQUEST_RANGE:
          $request_name = "Range";
          break;
        case c_base_http::REQUEST_REFERER:
          $request_name = "Referer";
          break;
        case c_base_http::REQUEST_TE:
          $request_name = "TE";
          break;
        case c_base_http::REQUEST_USER_AGENT:
          $request_name = "User-Agent";
          break;
        case c_base_http::REQUEST_UPGRADE:
          $request_name = "Upgrade";
          break;
        case c_base_http::REQUEST_VIA:
          $request_name = "Via";
          break;
        case c_base_http::REQUEST_WARNING:
          $request_name = "Warning";
          break;
      }

      $data_program['http']->set_response_content("Request #$request_id: " . $request_name . ": " . "<br>\n");

      $data_program['http']->set_response_content("<ul>\n");
      foreach ($request_values['data'] as $key => $value) {
        $data_program['http']->set_response_content("<li>");
        $data_program['http']->set_response_content("<strong>" . $key . "</strong>" . " = " . print_r($value, TRUE));
        $data_program['http']->set_response_content("</li>");
      }
      $data_program['http']->set_response_content("</ul>\n");
    }

    $data_program['http']->set_response_content("<br>\n");
  }

  function program_process_theme(&$data_program) {
  }

  function program_build_response(&$data_program) {
    $data_program['http']->set_language_class('c_base_language_us_limited');

    $data_program['http']->set_response_protocol('HTTP/1.1');
    $data_program['http']->set_response_allow(c_base_http::HTTP_METHOD_GET);
    $data_program['http']->set_response_allow(c_base_http::HTTP_METHOD_HEAD);
    $data_program['http']->set_response_cache_control(c_base_http::CACHE_CONTROL_NO_CACHE);
    #$data_program['http']->set_response_cache_control(c_base_http::CACHE_CONTROL_PUBLIC);
    #$data_program['http']->set_response_cache_control(c_base_http::CACHE_CONTROL_MAX_AGE, '32');
    #$data_program['http']->set_response_age(2);
    #$data_program['http']->set_response_connection('close');
    #$data_program['http']->set_response_content_disposition();
    $data_program['http']->set_response_content_language();
    $data_program['http']->set_response_content_type('text/html');
    $data_program['http']->set_response_date($data_program['timestamp']);
    $data_program['http']->set_response_expires(strtotime('+5 minutes', $data_program['timestamp']));
    $data_program['http']->set_response_last_modified($data_program['timestamp']);
    #$data_program['http']->set_response_location();
    $data_program['http']->set_response_status(c_base_http_status::OK);
    $data_program['http']->set_response_vary('host');
    $data_program['http']->set_response_vary('user-agent');
    $data_program['http']->set_response_vary('accept-encoding');

    #$data_program['http']->set_response_date_actual(strtotime('+5 days')); // this only needs to be set for apache.
  }

  function program_send_response(&$data_program) {
    $old_output_buffering = ini_get('output_buffering');
    ini_set('output_buffering', 'off');

    $data_program['http']->set_response_checksum_content();

    $response = $data_program['http']->get_response();
    $data_program['http']->set_response_content("The Complete Response Array: " . "<br>\n");
    $data_program['http']->set_response_content(print_r($response->get_value_exact(), TRUE));
    $data_program['http']->set_response_content("<br>\n");
    unset($response);

    #$data_program['http']->set_response_content("<br>\n");
    #$data_program['http']->set_response_content("The Complete _SERVER array: " . "<br>\n");
    #$data_program['http']->set_response_content(print_r($_SERVER, TRUE));
    #$data_program['http']->set_response_content("<br>\n");

    $data_program['http']->set_response_etag();

    // example using a favicon.ico test file.
    #$filename = '/var/www/sites/koopa/favicon.ico';
    #$data_program['http']->set_response_content($filename, FALSE, TRUE);
    #$data_program['http']->set_response_etag();
    #$data_program['http']->set_response_content_type('image/gif');
    #$data_program['http']->set_response_last_modified(filemtime($filename));

    program_debug_information($data_program);

    // finalize the content prior to sending headers to ensure header accuracy.
    $data_program['http']->set_response_content_length();
    $data_program['http']->encode_response_content();

    // when the headers are sent, checksums are created, so at this point all error output should be stored and not sent.
    $data_program['http']->send_response_headers();
    flush();

    // once the header are sent, send the content.
    $data_program['http']->send_response_content();
    flush();

    ini_set('output_buffering', $old_output_buffering);
    unset($old_output_buffering);
  }

  function program_debug_information(&$data_program) {
    $data_program['debug']['memory_usage'][6] = memory_get_usage();
    $data_program['debug']['memory_peak'][6] = memory_get_peak_usage();
    $time_stop = microtime(TRUE);

    $encoding = $data_program['http']->get_response_content_encoding();
    if ($encoding instanceof c_base_return_false || $encoding == c_base_http::ENCODING_CHUNKED) {
      $data_program['http']->set_response_content("<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used (1)</strong>: " . ($data_program['debug']['memory_usage'][1] - $data_program['debug']['memory_usage'][0]) . " bytes (". (floor(($data_program['debug']['memory_usage'][1] - $data_program['debug']['memory_usage'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used Peak (1)</strong>: " . ($data_program['debug']['memory_peak'][1] - $data_program['debug']['memory_peak'][0]) . " bytes (". (floor(($data_program['debug']['memory_peak'][1] - $data_program['debug']['memory_peak'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used (2)</strong>: " . ($data_program['debug']['memory_usage'][2] - $data_program['debug']['memory_usage'][0]) . " bytes (". (floor(($data_program['debug']['memory_usage'][2] - $data_program['debug']['memory_usage'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used Peak (2)</strong>: " . ($data_program['debug']['memory_peak'][2] - $data_program['debug']['memory_peak'][0]) . " bytes (". (floor(($data_program['debug']['memory_peak'][2] - $data_program['debug']['memory_peak'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used (3)</strong>: " . ($data_program['debug']['memory_usage'][3] - $data_program['debug']['memory_usage'][0]) . " bytes (". (floor(($data_program['debug']['memory_usage'][3] - $data_program['debug']['memory_usage'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used Peak (3)</strong>: " . ($data_program['debug']['memory_peak'][3] - $data_program['debug']['memory_peak'][0]) . " bytes (". (floor(($data_program['debug']['memory_peak'][3] - $data_program['debug']['memory_peak'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used (4)</strong>: " . ($data_program['debug']['memory_usage'][4] - $data_program['debug']['memory_usage'][0]) . " bytes (". (floor(($data_program['debug']['memory_usage'][4] - $data_program['debug']['memory_usage'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used Peak (4)</strong>: " . ($data_program['debug']['memory_peak'][4] - $data_program['debug']['memory_peak'][0]) . " bytes (". (floor(($data_program['debug']['memory_peak'][4] - $data_program['debug']['memory_peak'][0]) / 1024)). " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<br>\n");
      #$data_program['http']->set_response_content("<strong>Memory Used (5)</strong>: " . ($data_program['debug']['memory_usage'][5] - $data_program['debug']['memory_usage'][0]) . " bytes (". (floor(($data_program['debug']['memory_usage'][5] - $data_program['debug']['memory_usage'][0]) / 1024)) . " KB)" . "<br>\n");
      #$data_program['http']->set_response_content("<strong>Memory Used Peak (5)</strong>: " . ($data_program['debug']['memory_peak'][5] - $data_program['debug']['memory_peak'][0]) . " bytes (". (floor(($data_program['debug']['memory_peak'][5] - $data_program['debug']['memory_peak'][0]) / 1024)) . " KB)" . "<br>\n");
      #$data_program['http']->set_response_content("<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used (Exit)</strong>: " . ($data_program['debug']['memory_usage'][6] - $data_program['debug']['memory_usage'][0]) . " bytes (". (floor(($data_program['debug']['memory_usage'][6] - $data_program['debug']['memory_usage'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<strong>Memory Used Peak (Exit)</strong>: " . ($data_program['debug']['memory_peak'][6] - $data_program['debug']['memory_peak'][0]) . " bytes (". (floor(($data_program['debug']['memory_peak'][6] - $data_program['debug']['memory_peak'][0]) / 1024)) . " KB)" . "<br>\n");
      $data_program['http']->set_response_content("<br>\n");
      $data_program['http']->set_response_content("<strong>Time Taken</strong>: " . ($time_stop - $data_program['debug']['time_start']) . " microseconds (". (floor(($time_stop - $data_program['debug']['time_start']) * 0.000001)) . " seconds)" . "<br>\n");
    }
    unset($encoding);
    unset($time_stop);
    unset($data_program['debug']);
  }

  function program_assign_database_string(&$database, $username, $password, $session) {
    if (!is_null($session)) {
      $database->set_session($session);
    }

    $connection_string = new c_base_connection_string();
    $connection_string->set_host('127.0.0.1');
    $connection_string->set_port(5432);
    $connection_string->set_database('example');
    $connection_string->set_user($username);
    if (!is_null($password)) {
      $connection_string->set_password($password);
    }
    #$connection_string->set_ssl_mode('require');
    $connection_string->set_ssl_mode('disable');
    $connection_string->set_connect_timeout(4);
    $database->set_connection_string($connection_string);
    unset($connection_string);
  }

  function program_connect_database(&$database) {
    if (!($database->do_connect() instanceof c_base_return_true)) {
      return FALSE;
    }

    $database->do_query('set bytea_output to hex;');
    $database->do_query('set datestyle to us;');

    return TRUE;
  }


  program();
