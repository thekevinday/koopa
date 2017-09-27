<?php
  namespace n_koopa;

  // make sure the class files can be loaded (be sure to customize this as necessary).
  set_include_path('/var/www/koopa');

  // load the global defaults file (this file is not included by default but is required by all).
  // replace this with your own as you see fit.
  require_once('common/base/classes/base_defaults_global.php');

  require_once('common/base/classes/base_error.php');
  require_once('common/base/classes/base_return.php');
  require_once('common/base/classes/base_session.php');
  require_once('common/base/classes/base_cookie.php');
  require_once('common/base/classes/base_database.php');
  require_once('common/base/classes/base_ldap.php');
  require_once('common/base/classes/base_http.php');
  require_once('common/base/classes/base_languages.php');


  // create an alias for the default language for error messages.
  #class_alias('c_base_error_messages_english', 'c_base_error_messages');


  function process_received_headers(&$stuff) {
    // default supported languages.
    c_base_defaults_global::s_set_languages(new c_base_languages_limited());

    $stuff['http'] = new c_base_http();
    $stuff['http']->do_load_request();
    $stuff['http']->set_response_content_language();

    // test error message handling using english or japanese.
    $supported_languages = [
      i_base_languages::ENGLISH_US => '\n_koopa\c_base_error_messages_english',
      i_base_languages::ENGLISH => '\n_koopa\c_base_error_messages_english',
      i_base_languages::JAPANESE => '\n_koopa\c_base_error_messages_japanese',
    ];

    $stuff['http']->set_response_content_language(i_base_languages::ENGLISH_US);
    $stuff['http']->set_response_content_language(i_base_languages::ENGLISH);

    $language_chosen = i_base_languages::ENGLISH_US;
    $languages_accepted = $stuff['http']->get_request(c_base_http::REQUEST_ACCEPT_LANGUAGE)->get_value();
    if (isset($languages_accepted['data']['weight']) && is_array($languages_accepted['data']['weight'])) {
      foreach ($languages_accepted['data']['weight'] as $weight => $language) {
        $language_keys = array_keys($language);
        $language_code = array_pop($language_keys);
        unset($language_keys);

        if (array_key_exists($language_code, $supported_languages)) {
          $language_chosen = $language_code;
          break;
        }
      }
      unset($weight);
      unset($language);
      unset($language_code);
    }
    unset($languages_accepted);

    if ($language_chosen === i_base_languages::ENGLISH || $language_chosen === i_base_languages::ENGLISH_US) {
      require_once('common/base/classes/base_error_messages_english.php');
    }
    elseif ($language_chosen === i_base_languages::JAPANESE) {
      require_once('common/base/classes/base_error_messages_japanese.php');
      $stuff['http']->set_response_content_language(i_base_languages::JAPANESE);
    }

    $stuff['error_messages'] = new $supported_languages[$language_chosen];

    unset($supported_languages);
    unset($language_chosen);
  }

  $stuff = [
    'resources' => [
      'time' => microtime(TRUE),
     ],
  ];

  function send_prepared_headers($stuff) {
    // add headers
    $stuff['http']->set_response_date();
    $stuff['http']->set_response_content_type('text/html');
    #$stuff['http']->set_response_etag();
    #$stuff['http']->set_response_last_modified(strtotime('now'));
    #$stuff['http']->set_response_expires(strtotime('+30 minutes'));
    $stuff['http']->set_response_pragma('no-cache');
    $stuff['http']->set_response_vary('Host');
    $stuff['http']->set_response_vary('User-Agent');
    $stuff['http']->set_response_vary('Accept');
    $stuff['http']->set_response_vary('Accept-Language');
    #$stuff['http']->set_response_warning('1234 This site is under active development.');


    // manually disable output buffering (if enabled) when transfer headers and content.
    $old_output_buffering = ini_get('output_buffering');
    ini_set('output_buffering', 'off');


    // when the headers are sent, checksums are created, so at this point all error output should be stored and not sent.
    $stuff['http']->send_response_headers(TRUE);

    if (isset($stuff['cookie_existence']['cookie'])) {
      $stuff['cookie_existence']['cookie']->do_push();
    }

    if (isset($stuff['cookie_login']['cookie'])) {
      $stuff['cookie_login']['cookie']->do_push();
    }

    // additional cookies to expire.
    if (isset($stuff['cookie_expire']['cookies']) && is_array($stuff['cookie_expire']['cookies'])) {
      foreach ($stuff['cookie_expire']['cookies'] as $cookie) {
        if (!($cookie instanceof c_base_cookie)) {
          continue;
        }

        $cookie->set_expires(-1);
        $cookie->set_max_age(-1);
        $cookie->do_push();
      }
      unset($cookie);
    }
  }

  function theme($stuff) {
    // @todo: call the appropriate http send function from $stuff['http']. this requires rewriting this entire theme function.
    // note: not changing language here because most of the page is in english, regardless of the http accept-language setting.
    print('<html lang="en-US">');
    print('<head>');
    print('<title>Testing Koopa</title>');
    print('<meta content="A simple and rough test of various functionality provided by the koopa project." name="description">');
    print('<meta content="text/html; charset=utf-8" http-equiv="Content-Type">');
    print('<meta charset="UTF-8">');
    print('<meta content="web" name="distribution">');
    print('<meta content="INDEX,FOLLOW" name="robots">');
    print('<meta content="width=device-width, initial-scale=1" name="viewport">');
    print('<style type="text/css" rel="stylesheet" media="all">');
    print('.error_message > .error_message-argument { display: inline-block; vertical-align: baseline; }');
    print('</style>');
    print('</head>');

    print('<body>');
    print("<h1>Testing</h1>\n");
    print("The following is a test of the database design and base database and cookied functionality.<br>");
    print("<br>");

    if (isset($stuff['errors'])) {
      print("0) The following errors have been detected:<br>");
      print('<ol>' . $stuff['errors'] . '</ol>');
    }

    if (isset($_SERVER["HTTPS"])) {
      print("1) You are using HTTPS.<br>");
      print("<br>");
    }
    else {
      print("1) You are not using HTTPS.<br>");
      print("<br>");
    }

    print("2) _SERVER: <br>");
    #foreach ($_SERVER as $key => $value) {
    #  print(" - $key = " . print_r($value, TRUE) . "<br>");
    #}
    print(" - output disabled.<br>");
    unset($key);
    unset($value);
    print("<br>");

    print("3) Language Test: <br>");

    // disclaimer: I used translate.google.com to generate the languages and provided only the default translation (expect translation errors).
    $test_strings = [
      i_base_languages::ENGLISH_US => 'This is a test using your browser default language. Currently english (default), spanish, japanese, and russian are tested.',
      i_base_languages::ENGLISH => 'This is a test using your browser default language. Currently english (default), spanish, japanese, and russian are tested.',
      i_base_languages::JAPANESE => 'これは、ブラウザのデフォルト言語を使用したテストです。 現在、英語（デフォルト）、スペイン語、日本語、ロシア語がテストされています。',
      i_base_languages::RUSSIAN => 'Это тест с помощью браузера по умолчанию язык. В настоящее время английский (по умолчанию), испанский, японский и русский тестируются.',
      i_base_languages::SPANISH => 'Se trata de una prueba que utiliza el idioma predeterminado de su navegador. Actualmente se ponen a prueba el inglés (predeterminado), el español, el japonés y el ruso.',
    ];

    $language_chosen = i_base_languages::ENGLISH;
    $languages_accepted = $stuff['http']->get_request(c_base_http::REQUEST_ACCEPT_LANGUAGE)->get_value();
    if (isset($languages_accepted['data']['weight']) && is_array($languages_accepted['data']['weight'])) {
      foreach ($languages_accepted['data']['weight'] as $weight => $language) {
        $language_keys = array_keys($language);
        $language_code = array_pop($language_keys);
        unset($language_keys);

        if (array_key_exists($language_code, $test_strings)) {
          $language_chosen = $language_code;
          break;
        }
      }
      unset($weight);
      unset($language);
      unset($language_code);
    }

    print(' - ' . $test_strings[$language_chosen] . "<br>");
    print("<br>");

    unset($language_chosen);
    unset($languages_accepted);
    unset($test_strings);

    // Useful _SERVER Variables:
    //   REQUEST_TIME, REQUEST_TIME_FLOAT
    //   HTTPS, HTTP_HOST
    //   HTTP_USER_AGENT
    //   HTTP_ACCEPT, HTTP_ACCEPT_LANGUAGE, HTTP_ACCEPT_ENCODING
    //   HTTP_DNT
    //   HTTP_CONNECTION, HTTP_CACHE_CONTRO
    //   HTTP_AUTHORIZATION
    //   protossl (which = 's' for https, and = '' for http)
    //   SERVER_NAME, SERVER_ADDR, SERVER_PORT
    //   REMOTE_ADDR, REMOTE_PORT
    //   DOCUMENT_ROOT
    //   REQUEST_SCHEME
    //   CONTEXT_PREFIX, CONTEXT_DOCUMENT_ROOT
    //   SERVER_PROTOCOL
    //   REQUEST_METHOD, REQUEST_URI, SCRIPT_NAME
    //   QUERY_STRING
    //   QUERY_STRING

    // running php from command line exposes user space environmental variables, such as:
    // TERM, SHELL, USER, HOME, _

    print("<h2>Cookie Test</h2>\n");
    if (isset($stuff['cookie_existence']['new'])) {
      print("4) A new existence cookie has been created.<br>");
      print($stuff['cookie_existence']['new']);
    }
    elseif (isset($stuff['cookie_existence']['exists'])) {
      print("4) The existence cookie has been loaded.<br>");
      print($stuff['cookie_existence']['exists']);
    }
    else {
      print("4) Disabled<br>");
    }


    print("<h2>Login, Session, and Database Connection Test</h2>\n");
    if (isset($stuff['login']) && isset($_SERVER["HTTPS"])) {
      print("5) Login<br>");
      print($stuff['login']);
    }
    else {
      print("5) Disabled<br>");
    }


    print("<h2>LDAP Test</h2>\n");
    if (isset($stuff['ldap']) && isset($_SERVER["HTTPS"])) {
      print("6) LDAP<br>");
      if (isset($stuff['ldap']['markup'])) {
        print($stuff['ldap']['markup']);
      }
    }
    else {
      print("6) Disabled<br>");
    }


    if (isset($stuff['resources'])) {
      $difference_seconds = microtime(TRUE) - $stuff['resources']['time'];
      $difference_milli = $difference_seconds * 1000;
      $mu_1 = memory_get_usage(TRUE);
      $mu_2 = memory_get_usage();
      $mp_1 = memory_get_peak_usage(TRUE);
      $mp_2 = memory_get_peak_usage();

      print("<h2>Resources</h2>\n");
      print("7) Time Taken: " . sprintf('%.10g', $difference_milli) . " milliseconds (" . sprintf('%.06g', $difference_seconds)  . " seconds).<br>");
      print("8) Memory Usage (Real): " . $mu_1 . " bytes (" . sprintf('%.06g', $mu_1 / 1024 / 1024) . " megabytes).<br>");
      print("9) Memory Usage (emalloc): " . $mu_2 . " bytes (" . sprintf('%.06g', $mu_2 / 1024 / 1024) . " megabytes)<br>");
      print("10) Peak Memory Usage (Real): " . $mp_1 . " bytes (" . sprintf('%.06g', $mp_1 / 1024 / 1024) . " megabytes).<br>");
      print("11) Peak Memory Usage (emalloc): " . $mp_2 . " bytes (" . sprintf('%.06g', $mp_2 / 1024 / 1024) . " megabytes).<br>");
    }

    print('</body>');
    print('</html>');
  }

  function session(&$stuff) {
    $database = new c_base_database();

    $remote_address = '127.0.0.1';
    if (!empty($_SERVER['REMOTE_ADDR'])) {
      $remote_address = $_SERVER['REMOTE_ADDR'];
    }

    // cookie is used to determine whether or not the user is logged in.
    $cookie = new c_base_cookie();
    $cookie->set_name("test-logged_in-localhost");
    $cookie->set_path('/');
    $cookie->set_domain('.localhost');
    $cookie->set_secure(TRUE);
    $cookie->set_http_only(TRUE);
    $cookie->set_first_only(TRUE);
    $cookie->set_same_site(c_base_cookie::SAME_SITE_STRICT);

    $logged_in = FALSE;
    $failure = FALSE;

    if ($cookie->do_pull() instanceof c_base_return_true) {
      $data = $cookie->get_value_exact();

      if ($cookie->validate() instanceof c_base_return_true && !empty($data['session_id'])) {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_id'])) {
          if ($_POST['form_id'] == 'logout_form') {
            // delete the session.
            $session = new c_base_session();
            $session->set_socket_directory('/programs/sockets/sessionize_accounts/');
            $session->set_system_name('reservation');
            $session->set_host($remote_address);
            $session->set_session_id($data['session_id']);

            $result = $session->do_connect();
            if ($result instanceof c_base_return_true) {
              unset($connected);
              $session->do_pull();

              $name = $session->get_name()->get_value();
              $password = $session->get_password()->get_value();
              assign_database_string($database, $name, $password);
              unset($name);
              unset($password);

              $connected = connect_database($database);
              if ($connected) {
                set_log_user($database, 'logout');
                $database->do_disconnect();
              }
              unset($connected);

              $result = $session->do_terminate();
              $session->do_disconnect();
            }
            unset($session);
            unset($result);

            // delete the cookie.
            $cookie->set_expires(-1);
            $cookie->set_max_age(-1);
            $stuff['cookie_login']['cookie'] = $cookie;
          }
        }
        else {
          $session = new c_base_session();
          $session->set_socket_directory('/programs/sockets/sessionize_accounts/');
          $session->set_system_name('reservation');
          $session->set_host($remote_address);
          $session->set_session_id($data['session_id']);

          $result = $session->do_connect();
          $failure = c_base_return::s_has_error($result);
          if (!$failure) {
            $result = $session->do_pull();
            $session->do_disconnect();

            $connected = FALSE;
            if ($result instanceof c_base_return_true) {
              $name = $session->get_name()->get_value();
              $password = $session->get_password()->get_value();
              assign_database_string($database, $name, $password);
              unset($name);
              unset($password);

              $connected = connect_database($database);

              if (!isset($stuff['login'])) {
                $stuff['login'] = '';
              }

              if ($connected) {
                $stuff['login'] .= 'Connected: success<br>' . "\n";
              }
              else {
                $stuff['login'] .= 'Connected: failure<br>' . "\n";
              }
            }
            unset($result);

            if ($connected) {
              // check to see if the session timeout has been extended and if so, then update the cookie.
              $session_expire = $session->get_timeout_expire()->get_value_exact();
              $session_seconds = $session_expire - time();
              if ($session_seconds == 0) {
                $session_seconds = -1;
              }
              if ($session_expire > $data['expire']) {
                $data['expire'] = gmdate("D, d-M-Y H:i:s T", $session_expire);
                $cookie->set_value($data);
                $cookie->set_expires($session_expire);
                $stuff['cookie_login']['cookie'] = $cookie;
              }

              if (!isset($stuff['login'])) {
                $stuff['login'] = '';
              }

              $user_data = get_user_data($database, $stuff, $session->get_name()->get_value_exact());

              $stuff['login'] .= ' - You are logged in as: ' . $user_data['name_machine'] . '<br>' . "\n";
              $stuff['login'] .= ' - Your session user is: ' . $session->get_name()->get_value_exact() . '<br>' . "\n";
              #$stuff['login'] .= ' - Your password is: ' . $session->get_password()->get_value_exact() . '<br>' . "\n";
              $stuff['login'] .= ' - You will be auto-logged out at: ' . $data['expire'] . '<br>' . "\n";
              $stuff['login'] .= '<br>' . "\n";
              $stuff['login'] .= 'Your session user data is: <br>' . "\n";
              $stuff['login'] .= print_r($user_data, TRUE) . "<br>";
              $stuff['login'] .= '<br>' . "\n";
              $logged_in = TRUE;

              if (!isset($user_data['name_machine']) || $user_data['name_machine'] != $session->get_name()->get_value_exact()) {
                $stuff['login'] .= 'Your session name does not match the logged in name.' . '<br>' . "\n";
                $stuff['login'] .= 'This can happen when the user name has been deleted from the database for an existing session.' . '<br>' . "\n";
                $stuff['login'] .= 'Your session cookie is being deleted.' . '<br>' . "\n";
                $stuff['login'] .= '<br>' . "\n";

                $cookie->set_expires(-1);
                $cookie->set_max_age(-1);
                $stuff['cookie_login']['cookie'] = $cookie;
              }

              ldap_get_user($stuff, $session->get_name()->get_value_exact());
              set_log_activity($database);

              if (!empty($session->get_name()->get_value_exact()) && $session->get_name()->get_value_exact() != 'u_reservation_public') {
                get_database_data($database, $stuff);

                $log = get_log_activity($database);
                $table = build_log_activity_table($log);
                $stuff['login'] .= "<br>" . $table . "<br>";

                unset($log);
                unset($table);

                $log = get_log_users($database);
                $table = build_log_users_table($log);
                $stuff['login'] .= "<br>" . $table . "<br>";

                unset($log);
                unset($table);
              }
            }
            else {
              if (!isset($stuff['login'])) {
                $stuff['login'] = '';
              }

              $error = $session->get_error();
              $stuff['login'] .= ' - Failed to load requested session, error: ' . print_r($error, TRUE) . "<br><br>";
              unset($error);
            }
          }

          unset($session);
        }
      }
      unset($data);
    }
    else {
      if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_id'])) {
        if ($_POST['form_id'] == 'login_form' && !empty($_POST['login_name']) && isset($_POST['login_password'])) {
          $connected = FALSE;
          $session = new c_base_session();
          $session->set_socket_directory('/programs/sockets/sessionize_accounts/');
          $session->set_system_name('reservation');
          $session->set_name($_POST['login_name']);
          $session->set_host($remote_address);
          $session->set_password($_POST['login_password']);

          $is_public = FALSE;
          $user_data = [];

          // allow direct login as u_reservation_public and assume/require that the u_reservation_public account already exists.
          if ($_POST['login_name'] == 'u_reservation_public') {
            $is_public = TRUE;
            $account_exists = FALSE;
          }
          else {
            $account_exists = check_login_access($stuff, $database, $_POST['login_name'], $_POST['login_password'], $session);
          }

          if (!$account_exists) {
            $session->set_name('u_reservation_public');
            $session->set_password(NULL);

            if ($is_public === FALSE) {
              if (!isset($stuff['login'])) {
                $stuff['login'] = '';
              }
              $stuff['login'] .= "DEBUG: user " . htmlspecialchars($_POST['login_name'], ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . " does not exist and user does not exist in ldap (falling back to the public account to access the database).<br>";
            }

            #$database->set_persistent(TRUE);
            assign_database_string($database, 'u_reservation_public', NULL);
            $connected = connect_database($database);
            if ($connected) {
              if ($is_public === FALSE) {
                set_log_user($database, 'login_failure', $_POST['login_name'], NULL, 401);
              }
              else {
                set_log_user($database, 'login');
              }

              $user_data = get_user_data($database, $stuff, 'u_reservation_public');

              $stuff['login'] .= ' - Accessing database as: u_reservation_public' . '<br>' . "\n";
              $stuff['login'] .= ' - Your user id is: ' . (isset($user_data['id']) ? $user_data['id'] : 'does not exist') . '<br>' . "\n";
              $stuff['login'] .= '<br>' . "\n";
              $logged_in = TRUE;
              $is_public = TRUE;
            }
          }
          else {
            if (!isset($stuff['login'])) {
              $stuff['login'] = '';
            }

            $ldap_data = NULL;
            ldap_get_user($stuff, $session->get_name()->get_value_exact());
            if (!empty($stuff['ldap']['data'])) {
              $ldap_data = $stuff['ldap']['data'];
            }

            $connected = TRUE;
            $stuff['login'] .= "DEBUG: account already exists.<br>";
            $user_data = get_user_data($database, $stuff, $_POST['login_name'], $ldap_data);
            unset($ldap_data);
          }
          unset($account_exists);

          $session->set_settings($user_data);

          if (!$connected) {
            $failure = TRUE;
          }
          else {
            $result = $session->do_connect();
            $failure = c_base_return::s_has_error($result);
          }

          if (!$failure) {
            if (!$is_public) {
              $result = $session->do_push(600, 1800); // (10 minutes, 30 minutes)
              $session_expire = $session->get_timeout_expire()->get_value_exact();
              $session_max = $session->get_timeout_max()->get_value_exact();
              $expire_string = c_base_defaults_global::s_get_date("D, d M Y H:i:s T", $session_expire)->get_value_exact();

              $cookie->set_expires($session_expire);
              $cookie->set_max_age(NULL);

              set_log_user($database, 'login', NULL, $session_expire);

              if ($result instanceof c_base_return_true) {
                $data = [
                  'session_id' => $session->get_session_id()->get_value_exact(),
                  'expire' => gmdate("D, d-M-Y H:i:s T", $session_expire), // unnecessary, but provided for debug purposes.
                ];
                $cookie->set_value($data);
                $stuff['cookie_login']['cookie'] = $cookie;
              }
            }
            else {
              $session_expire = NULL;
              $session_max = NULL;
              $expire_string = 'indefinite';

              $cookie->set_expires(-1);
              $cookie->set_max_age(-1);

              $result = new c_base_return_true();
            }
            $session->do_disconnect();

            if ($result instanceof c_base_return_true) {
              if (!isset($stuff['login'])) {
                $stuff['login'] = '';
              }

              $stuff['login'] .= ' - You are logged in as: ' . $user_data['name_machine'] . '<br>' . "\n";
              $stuff['login'] .= ' - Your session user is: ' . $session->get_name()->get_value_exact() . '<br>' . "\n";
              #$stuff['login'] .= ' - Your password is: ' . $session->get_password()->get_value_exact() . '<br>' . "\n";
              $stuff['login'] .= ' - You will be auto-logged out at: ' . $expire_string . ' (' . $session_expire . ')' . '<br>' . "\n";
              $stuff['login'] .= '<br>' . "\n";
              $stuff['login'] .= 'Your session user data is: <br>' . "\n";
              $stuff['login'] .= print_r($user_data, TRUE) . "<br>";
              $stuff['login'] .= '<br>' . "\n";
              $logged_in = TRUE;

              if (!isset($stuff['ldap']['markup'])) {
                ldap_get_user($stuff, $session->get_name()->get_value_exact());
              }

              set_log_activity($database);

              if (!empty($session->get_name()->get_value_exact()) && $session->get_name()->get_value_exact() != 'u_reservation_public') {
                get_database_data($database, $stuff);

                $log = get_log_activity($database);
                $table = build_log_activity_table($log);
                $stuff['login'] .= "<br>" . $table . "<br>";

                unset($log);
                unset($table);

                $log = get_log_users($database);
                $table = build_log_users_table($log);
                $stuff['login'] .= "<br>" . $table . "<br>";

                unset($log);
                unset($table);
              }

              unset($user_data);
            }
            else {
              if (!isset($stuff['login'])) {
                $stuff['login'] = '';
              }

              $error_messages = '';
              $errors = $result->get_error();
              if (is_array($errors)) {
                foreach ($errors as $error) {
                  if (!($error instanceof c_base_error)) {
                    continue;
                  }

                  $error_message = $error->get_message();
                  if (!is_string($error_message)) {
                    $error_message = NULL;
                  }

                  $error_messages .= $stuff['error_messages']::s_render_error_message($error, TRUE, FALSE, $error_message)->get_value_exact();
                  unset($error_message);
                }
                unset($error);
              }
              unset($errors);

              $stuff['login'] .= ' - failed to save requested session, error: ' . $error_messages . "<br>";
              unset($error_messages);
            }
          }

          unset($session);
        }
      }
    }
    unset($cookie);

    login_form($stuff, $logged_in);

    $database->do_disconnect();
    unset($database);
  }

  function login_form(&$stuff, $logged_in = FALSE) {
    if (!isset($stuff['login'])) {
      $stuff['login'] = '';
    }

    $stuff['login'] .= '<form id="login_form" name="login_form" method="post">' . "\n";

    if ($logged_in) {
      $stuff['login'] .= '  <input type="hidden" name="form_id" value="logout_form">' . "\n";
      $stuff['login'] .= '  <input type="submit" value="Logout">' . "\n";
    }
    else {
      $stuff['login'] .= '  <input type="hidden" name="form_id" value="login_form">' . "\n";
      $stuff['login'] .= '  <input type="text" name="login_name">' . "\n";
      $stuff['login'] .= '  <input type="password" name="login_password">' . "\n";
      $stuff['login'] .= '  <input type="submit" value="Login">' . "\n";
    }
    $stuff['login'] .= '</form>' . "\n";
    $stuff['login'] .= '<br>' . "\n";

    #$stuff['login'] .= '_SERVER[REQUEST_METHOD]= ' . print_r($_SERVER["REQUEST_METHOD"], TRUE) . "<br>";
    #$stuff['login'] .= '_GET = ' . print_r($_GET, TRUE) . "<br>";
    #$stuff['login'] .= '_POST = ' . print_r($_POST, TRUE) . "<br>";
    #$stuff['login'] .= '<br>' . "\n";
  }

  function get_database_data(&$database, &$stuff) {
    $stuff['login'] .= 'query: "select * from v_users where not is_system and not is_public limit 20;"<br>' . "\n";
    $query_result = $database->do_query(' where not is_system and not is_public limit 20');
    if ($query_result instanceof c_base_database_result) {
      $all = $query_result->fetch_all();
      $stuff['login'] .= "<ol>";
      foreach ($all->get_value_exact() as $row) {
        $stuff['login'] .= "<li>";
        foreach ($row as $column => $value) {
          $stuff['login'] .= " '$column' = '$value'";
        }
        $stuff['login'] .= "</li>\n";
        unset($column);
        unset($value);
      }
      unset($all);
      unset($row);
    }
    else {
      if (!isset($stuff['errors'])) {
        $stuff['errors'] = '';
      }

      $stuff['errors'] .= '<li>' . $database->get_last_error()->get_value_exact() . '</li>';
    }
    unset($query_result);

    $stuff['login'] .= '</ol><br>' . "\n";
    $stuff['login'] .= '<br>' . "\n";
  }

  function check_login_access(&$stuff, &$database, $username, $password, $session) {
    if ($username == 'u_reservation_public') return FALSE;

    if ($database->is_connected() instanceof c_base_return_true) {
      return TRUE;
    }

    // directly check to see if the current user exists in ldap (this is technically not necessary because postgresql will also do this).
    $stuff_stub = [];
    $found_username = ldap_get_user($stuff_stub, $username);
    unset($stuff_stub);

    if (!$found_username) {
      unset($found_username);
      return FALSE;
    }

    assign_database_string($database, $username, $password);

    $connected = connect_database($database);
    if ($connected) {
      return TRUE;
    }

    // it is possible the user name might not exist, so try to create it.
    $ensure_result = ensure_user_account($_POST['login_name']);
    if (c_base_return::s_has_error($ensure_result)) {
      $errors = $ensure_result->get_error();
      if (is_array($errors)) {
        if (!isset($stuff['errors'])) {
          $stuff['errors'] = '';
        }

        foreach ($errors as $error) {
          if ($error instanceof c_base_error) {
            $stuff['errors'] .= '<li>' . $stuff['error_messages']::s_render_error_message($error)->get_value_exact() . '</li>';
          }
        }
        unset($error);
      }
      unset($errors);
    }
    else {
      // try again now that the system has attempted to ensure the user account exists.
      $connected = connect_database($database);
      if ($connected) {
        set_log_user($database, 'create_user');
        return TRUE;
      }
    }
    unset($ensure_result);

    return FALSE;
  }

  function assign_database_string(&$database, $username, $password) {
    $connection_string = new c_base_database_connection_string();
    $connection_string->set_host('127.0.0.1');
    $connection_string->set_port(5432);
    $connection_string->set_database('reservation');
    $connection_string->set_user($username);
    $connection_string->set_password($password);
    #$connection_string->set_ssl_mode('require');
    $connection_string->set_ssl_mode('disable');
    $connection_string->set_connect_timeout(4);
    $database->set_connection_string($connection_string);
    unset($connection_string);
  }

  function connect_database(&$database) {
    if (!($database->do_connect() instanceof c_base_return_true)) {
      return FALSE;
    }

    $database->do_query('set bytea_output to hex;');
    $database->do_query('set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;');
    $database->do_query('set datestyle to us;');

    return TRUE;
  }

  function set_log_user(&$database, $type, $user_name = NULL, $expires = NULL, $response_code = 200) {
    $extra_parameters = '';
    $extra_values = '';

    $query_string = '';
    $query_string .= 'insert into v_log_users_self_insert (log_title, log_type, log_severity, request_client, response_code, log_details)';
    $query_string .= ' values ($1, $2, $3, ($4, $5, $6), $7, $8); ';

    $query_parameters = [];
    $query_parameters[3] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $query_parameters[4] = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : 0;
    $query_parameters[5] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ;
    $query_parameters[6] = $response_code;

    if ($type == 'login') {
      $query_parameters[0] = "Logging in to the system.";
      $query_parameters[1] = 17;
      $query_parameters[2] = 1;
      $query_parameters[7] = json_encode(array('expires' => $expires));
    }
    elseif ($type == 'logout') {
      $query_parameters[0] = "Logging out of the system.";
      $query_parameters[1] = 18;
      $query_parameters[2] = 1;
      $query_parameters[7] = NULL;
    }
    elseif ($type == 'create') {
      $query_parameters[0] = "Created the user account.";
      $query_parameters[1] = 27;
      $query_parameters[2] = 1;
      $query_parameters[7] = NULL;
    }
    elseif ($type == 'login_failure') {
      $query_parameters[0] = "Failed to login as the user '" . $user_name . "'.";
      $query_parameters[1] = 17;
      $query_parameters[2] = 2;
      $query_parameters[7] = json_encode(array('user_name' => $user_name));
    }
    else {
      return FALSE;
    }

    ksort($query_parameters);

    $query_result = $database->do_query($query_string, $query_parameters);
    unset($query_string);
    unset($query_parameters);

    if ($query_result instanceof c_base_return_false) {
      if (!isset($stuff['errors'])) {
        $stuff['errors'] = '';
      }

      $stuff['errors'] .= '<li>' . $database->get_last_error()->get_value_exact() . '</li>';
    }
    unset($query_result);

    return TRUE;
  }

  function set_log_activity(&$database, $response_code = 200) {
    $connected = connect_database($database);

    if (!isset($stuff['login'])) {
      $stuff['login'] = '';
    }

    if ($connected) {
      $stuff['login'] .= 'Connected: success<br>' . "\n";
    }
    else {
      $stuff['login'] .= 'Connected: failure<br>' . "\n";
    }

    if ($connected) {
      $query_string = '';
      $query_string .= 'insert into v_log_user_activity_self_insert (request_path, request_arguments, request_client, response_code) values ($1, $2, ($3, $4, $5), $6); ';

      $query_parameters = [];
      $query_parameters[] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
      $query_parameters[] = is_array($_GET) && !empty($_GET) ? print_r(array_keys($_GET), TRUE) : '';
      $query_parameters[] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
      $query_parameters[] = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : 0;
      $query_parameters[] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
      $query_parameters[] = $response_code;
      unset($response_headers);
      unset($request_headers);
      unset($response_code);

      // for debugging.
      #print($query_string . '<br>');
      #print_r($query_parameters);
      #print('<br>');

      $query_result = $database->do_query($query_string, $query_parameters);
      unset($query_string);
      unset($query_parameters);

      if ($query_result instanceof c_base_return_false) {
        if (!isset($stuff['errors'])) {
          $stuff['errors'] = '';
        }

        $stuff['errors'] .= '<li>' . $database->get_last_error()->get_value_exact() . '</li>';
      }
      unset($query_result);
    }
    else {
      return FALSE;
    }

    return TRUE;
  }

  function get_user_data(&$database, &$stuff, $user_name, $ldap_data = NULL) {
    $user_data = [];
    $query_result = $database->do_query('select id, id_external, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, date_created, date_changed, date_synced, date_locked, settings from v_users_self');


    if (c_base_return::s_has_error($query_result)) {
      $errors = $query_result->get_error();
      if (is_array($errors)) {
        if (!isset($stuff['errors'])) {
          $stuff['errors'] = '';
        }

        foreach ($errors as $error) {
          if ($error instanceof c_base_error) {
            $stuff['errors'] .= '<li>' . $stuff['error_messages']::s_render_error_message($error)->get_value_exact() . '</li>';
          }
        }
        unset($error);
      }
      unset($errors);
    }

    if ($query_result instanceof c_base_database_result) {
      if ($query_result->number_of_rows()->get_value_exact() > 0) {
        $result = $query_result->fetch_row();

        if (c_base_return::s_has_error($result)) {
          $errors = $result->get_error();
          if (is_array($errors)) {
            if (!isset($stuff['errors'])) {
              $stuff['errors'] = '';
            }

            foreach ($errors as $error) {
              if ($error instanceof c_base_error) {
                $stuff['errors'] .= '<li>' . $stuff['error_messages']::s_render_error_message($error)->get_value_exact() . '</li>';
              }
            }
            unset($error);
          }
          unset($errors);
        }

        if (!($result instanceof c_base_return_false)) {
          $result_array = $result->get_value();
          if (!empty($result_array)) {
            $user_data['id'] = $result_array[0];
            $user_data['id_external'] = $result_array[1];
            $user_data['name_machine'] = $result_array[2];
            $user_data['name_human'] = $result_array[3];
            $user_data['address_email'] = $result_array[4];
            $user_data['is_administer'] = $result_array[5];
            $user_data['is_manager'] = $result_array[6];
            $user_data['is_auditor'] = $result_array[7];
            $user_data['is_publisher'] = $result_array[8];
            $user_data['is_financer'] = $result_array[9];
            $user_data['is_reviewer'] = $result_array[10];
            $user_data['is_editor'] = $result_array[11];
            $user_data['is_drafter'] = $result_array[12];
            $user_data['is_requester'] = $result_array[13];
            $user_data['is_system'] = $result_array[14];
            $user_data['is_public'] = $result_array[15];
            $user_data['is_locked'] = $result_array[16];
            $user_data['is_private'] = $result_array[17];
            $user_data['date_created'] = $result_array[18];
            $user_data['date_changed'] = $result_array[19];
            $user_data['date_synced'] = $result_array[20];
            $user_data['date_locked'] = $result_array[21];
            $user_data['settings'] = json_decode($result_array[22], TRUE);
          }
        }
      }

      if (!isset($user_data['id'])) {
        // note: the error here is not recored because the user account may not exist yet and needs to be auto-created.
        #if (!isset($stuff['errors'])) {
        #  $stuff['errors'] = '';
        #}

        #$stuff['errors'] .= '<li>No valid data found for the user: ' . $user_name . '</li>';
      }
    }
    else {
      if (!isset($stuff['errors'])) {
        $stuff['errors'] = '';
      }

      $stuff['errors'] .= '<li>' . htmlspecialchars($database->get_last_error()->get_value_exact(), ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</li>';

      return $user_data;
    }
    unset($query_result);

    // if the user does not yet exist in the database (and is not the public user), then create it.
    if (!isset($user_data['id']) && $user_name != 'u_reservation_public') {
      if (is_null($ldap_data)) {
        $query_result = $database->do_query('insert into v_users_self_insert (name_human.first, name_human.last, name_human.complete, address_email, id_external) values (null, null, null, null, null)');
        if ($query_result instanceof c_base_return_false) {
          if (!isset($stuff['errors'])) {
            $stuff['errors'] = '';
          }

          $stuff['errors'] .= '<li>' . htmlspecialchars($database->get_last_error()->get_value_exact(), ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</li>';

          // don't attempt to perform select again because the insert query has failed and so there should be nothing to select.
          unset($query_result);
          return $user_data;
        }
        unset($query_result);
      }
      else {
        $email = explode('@', $ldap_data['mail']);
        $parameters = [
          $ldap_data['givenname'],
          $ldap_data['sn'],
          $ldap_data['cn'],
          $email[0],
          $email[1],
          $ldap_data['employeenumber'],
        ];

        $query_result = $database->do_query('insert into v_users_self_insert (name_human.first, name_human.last, name_human.complete, address_email, id_external) values ($1, $2, $3, ($4, $5, TRUE), $6)', $parameters);
        if ($query_result instanceof c_base_return_false) {
          if (!isset($stuff['errors'])) {
            $stuff['errors'] = '';
          }

          $stuff['errors'] .= '<li>' . htmlspecialchars($database->get_last_error()->get_value_exact(), ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</li>';

          // don't attempt to perform select again because the insert query has failed and so there should be nothing to select.
          unset($query_result);
          return $user_data;
        }
        unset($query_result);
      }

      $user_data['id'] = NULL;
      $query_result = $database->do_query('select id, id_external, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, date_created, date_changed, date_synced, date_locked, settings from v_users_self');
      if ($query_result instanceof c_base_database_result) {
        if ($query_result->number_of_rows()->get_value_exact() > 0) {
          $result = $query_result->fetch_row();
          if (!($result instanceof c_base_return_false)) {
            $result_array = $result->get_value();
            $user_data['id'] = $result_array[0];
            $user_data['id_external'] = $result_array[1];
            $user_data['name_machine'] = $result_array[2];
            $user_data['name_human'] = $result_array[3];
            $user_data['address_email'] = $result_array[4];
            $user_data['is_administer'] = $result_array[5];
            $user_data['is_manager'] = $result_array[6];
            $user_data['is_auditor'] = $result_array[7];
            $user_data['is_publisher'] = $result_array[8];
            $user_data['is_financer'] = $result_array[9];
            $user_data['is_reviewer'] = $result_array[10];
            $user_data['is_editor'] = $result_array[11];
            $user_data['is_drafter'] = $result_array[12];
            $user_data['is_requester'] = $result_array[13];
            $user_data['is_system'] = $result_array[14];
            $user_data['is_public'] = $result_array[15];
            $user_data['is_locked'] = $result_array[16];
            $user_data['is_private'] = $result_array[17];
            $user_data['date_created'] = $result_array[18];
            $user_data['date_changed'] = $result_array[19];
            $user_data['date_synced'] = $result_array[20];
            $user_data['date_locked'] = $result_array[21];
            $user_data['settings'] = json_decode($result_array[22], TRUE);
          }
        }

        if (!isset($user_data['id'])) {
          if (!isset($stuff['errors'])) {
            $stuff['errors'] = '';
          }

          $stuff['errors'] .= '<li>No valid data found for the user: ' . $user_name . '</li>';
        }
      }
      else {
        if (!isset($stuff['errors'])) {
          $stuff['errors'] = '';
        }

        $stuff['errors'] .= '<li>' . htmlspecialchars($database->get_last_error()->get_value_exact(), ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</li>';
      }
      unset($query_result);
    }

    return $user_data;
  }

  function get_log_activity(&$database) {
    $values = [];

    $query_result = $database->do_query('select id, request_path, request_date, request_client, response_code from v_log_user_activity_self order by request_date desc limit 20;');
    if ($query_result instanceof c_base_database_result) {
     $total_rows = $query_result->number_of_rows()->get_value_exact();

      if ($total_rows > 0) {
        for ($i = 0; $i < $total_rows; $i++) {
          $result = $query_result->fetch_row();
          if (!($result instanceof c_base_return_false)) {
            $values[$i] = $result->get_value();
          }
        }
      }
    }
    else {
      if (!isset($stuff['errors'])) {
        $stuff['errors'] = '';
      }

      $stuff['errors'] .= '<li>' . htmlspecialchars($database->get_last_error()->get_value_exact(), ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</li>';
    }
    unset($query_result);

    return $values;
  }

  function get_log_users(&$database) {
    $values = [];

    $query_result = $database->do_query('select id, log_title, log_type, log_date, request_client, response_code from v_log_users_self order by log_date desc limit 10;');
    if ($query_result instanceof c_base_database_result) {
     $total_rows = $query_result->number_of_rows()->get_value_exact();

      if ($total_rows > 0) {
        for ($i = 0; $i < $total_rows; $i++) {
          $result = $query_result->fetch_row();
          if (!($result instanceof c_base_return_false)) {
            $values[$i] = $result->get_value();
          }
        }
      }
    }
    else {
      if (!isset($stuff['errors'])) {
        $stuff['errors'] = '';
      }

      $stuff['errors'] .= '<li>' . htmlspecialchars($database->get_last_error()->get_value_exact(), ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</li>';
    }
    unset($query_result);

    return $values;
  }

  function build_log_activity_table($activity) {
    $table = '<table id="log_activity-history" border="1" width="100%" style="min-width: 600px; max-width: 1500px; margin-left: auto; margin-right: auto;">';
    $table .= '<caption>Activity History (Latest 20 Entries)</caption>';
    $table .= '<thead>';
    $table .= '<tr>';
    $table .= '<th>ID</th>';
    $table .= '<th>Path</th>';
    $table .= '<th>Date</th>';
    $table .= '<th>Client</th>';
    $table .= '<th>HTTP Code</th>';
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody>';

    $total = count($activity);
    if ($total > 0) {
      for ($i = 0; $i < $total; $i++) {
        $table .= '<tr>';
        $table .= '<td>' . $activity[$i][0] . '</td>';
        $table .= '<td>' . $activity[$i][1] . '</td>';
        $table .= '<td>' . $activity[$i][2] . '</td>';
        $table .= '<td>' . $activity[$i][3] . '</td>';
        $table .= '<td>' . $activity[$i][4] . '</td>';
        $table .= '</tr>';
      }
      unset($i);
    }
    else {
      $table .= '<tr><td colspan="5">No Entries Found</td></tr>';
    }
    unset($total);

    $table .= '</tbody>';
    $table .= '</table>';

    return $table;
  }

  function build_log_users_table($activity) {
    $table = '<table id="log_user-history" border="1" width="100%" style="min-width: 600px; max-width: 1500px; margin-left: auto; margin-right: auto;">';
    $table .= '<caption>User History (Latest 10 Entries)</caption>';
    $table .= '<thead>';
    $table .= '<tr>';
    $table .= '<th>ID</th>';
    $table .= '<th>Title</th>';
    $table .= '<th>Type</th>';
    $table .= '<th>Date</th>';
    $table .= '<th>Client</th>';
    $table .= '<th>HTTP Code</th>';
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody>';

    $total = count($activity);
    if ($total > 0) {
      for ($i = 0; $i < $total; $i++) {
        $table .= '<tr>';
        $table .= '<td>' . $activity[$i][0] . '</td>';
        $table .= '<td>' . $activity[$i][1] . '</td>';
        $table .= '<td>' . $activity[$i][2] . '</td>';
        $table .= '<td>' . $activity[$i][3] . '</td>';
        $table .= '<td>' . $activity[$i][4] . '</td>';
        $table .= '<td>' . $activity[$i][5] . '</td>';
        $table .= '</tr>';
      }
      unset($i);
    }
    else {
      $table .= '<tr><td colspan="6">No Entries Found</td></tr>';
    }
    unset($total);

    $table .= '</tbody>';
    $table .= '</table>';

    return $table;
  }

  function ensure_user_account($user_name) {
    $socket_path = "127.0.0.1";
    $socket_family = AF_INET;
    $socket_port = 5433;
    $socket_protocol = SOL_TCP;

    $socket_type = SOCK_STREAM;

    $packet_size_target = 63;
    $packet_size_client = 1;

    $socket = @socket_create($socket_family, $socket_type, $socket_protocol);
    if (!is_resource($socket)) {
      unset($socket);

      $socket_error = @socket_last_error();

      @socket_clear_error();

      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'socket_create', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::SOCKET_FAILURE);
      unset($socket_error);

      return c_base_return_error::s_false($error);
    }

    $connected = @socket_connect($socket, $socket_path, $socket_port);
    if ($connected === FALSE) {
      unset($connected);

      $socket_error = @socket_last_error($socket);
      @socket_clear_error($socket);

      @socket_close($socket);
      unset($socket);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'socket_connect', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::SOCKET_FAILURE);
      unset($socket_error);

      return c_base_return_error::s_false($error);
    }
    unset($connected);

    $name_length = strlen(trim($user_name));
    $difference = $packet_size_target - $name_length;

    if ($difference > 0) {
      // the packet expects a packet to be NULL terminated or at most $packet_size_target.
      $packet = pack('a' . $name_length . 'x' . $difference, trim($user_name));
    }
    else {
      $packet = pack('a' . $name_length, $user_name);
    }

    $written = @socket_write($socket, $packet, $packet_size_target);
    if ($written === FALSE) {
      unset($written);

      $socket_error = @socket_last_error($socket);
      @socket_clear_error($socket);

      @socket_close($socket);
      unset($socket);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'socket_write', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::SOCKET_FAILURE);
      unset($socket_error);

      return c_base_return_error::s_false($error);
    }
    unset($written);

    $response = @socket_read($socket, $packet_size_client);
    if ($response === FALSE) {
      unset($response);

      $socket_error = @socket_last_error($socket);
      @socket_clear_error($socket);

      @socket_close($socket);
      unset($socket);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'socket_read', ':{socket_error}' => $socket_error, ':{socket_error_message}' => @socket_strerror($socket_error), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::SOCKET_FAILURE);
      unset($socket_error);

      return c_base_return_error::s_false($error);
    }
    @socket_close($socket);
    unset($socket);

    if (!is_string($response) || strlen($response) == 0) {
      unset($response);
      return FALSE;
    }

    // an integer is expected to be returned by the socket.
    $response_packet = unpack('C', $response);
    $response_value = (int) $response_packet[1];
    unset($response);

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
    return c_base_return_int::s_new($response_value);
  }

  function ldap_get_user(&$stuff, $username) {
    $stuff['ldap']['markup'] = '';
    $stuff['ldap']['data'] = [];

    $ldap = new c_base_ldap_get_user();
    $ldap->set_name('ldaps://127.0.0.1:1636/');
    #$ldap->set_bind_name('');
    #$ldap->set_bind_password('');
    $connected = $ldap->do_connect();
    if (c_base_return::s_has_error($connected)) {
      $message = $ldap->get_error_message();
      if ($message instanceof c_base_return_string) {
        $message = $message->get_value();
      }
      else {
        $message = NULL;
      }

      $stuff['ldap']['markup'] .= 'Connection Failed' . "<br>";
      $stuff['ldap']['markup'] .= 'ERROR: ' . $message . "<br>";
      unset($message);
      return;
    }

    $base_dn = 'ou=users,ou=People';
    $filter = '(uid=' . $username . ')';

    $read = $ldap->do_search($base_dn, $filter, ['mail', 'gecos', 'givenname', 'cn', 'sn', 'employeenumber']);
    if (c_base_return::s_has_error($read)) {
      $message = $ldap->get_error_message();
      if ($message instanceof c_base_return_string) {
        $message = $message->get_value();
      }
      else {
        $message = NULL;
      }

      $stuff['ldap']['markup'] .= 'ERROR: ' . $message . "<br>";
      unset($message);

      $ldap->do_disconnect();
      return;
    }

    $entries = $read->get_entry_all();
    if ($entries instanceof c_base_return_array) {
      $entries = $entries->get_value();
    }
    else {
      $entries = [];
    }

    if ($entries['count'] > 0) {
      $entry = [
        'uid' => $username,
        'mail' => $entries[0]['mail'][0],
        'gecos' => $entries[0]['gecos'][0],
        'givenname' => $entries[0]['givenname'][0],
        'cn' => $entries[0]['cn'][0],
        'sn' => $entries[0]['sn'][0],
        'employeenumber' => $entries[0]['employeenumber'][0],
      ];
      $stuff['ldap']['data'] = $entry;

      $stuff['ldap']['markup'] .= "<ul>\n";
      $stuff['ldap']['markup'] .= '<li>name: ' . $entry['uid'] . "</li>\n";
      $stuff['ldap']['markup'] .= '<li>e-mail: ' . $entry['mail'] . "</li>\n";
      $stuff['ldap']['markup'] .= '<li>gecos: ' . $entry['gecos'] . "</li>\n";
      $stuff['ldap']['markup'] .= '<li>givenname: ' . $entry['givenname'] . "</li>\n";
      $stuff['ldap']['markup'] .= '<li>cn: ' . $entry['cn'] . "</li>\n";
      $stuff['ldap']['markup'] .= '<li>sn: ' . $entry['sn'] . "</li>\n";
      $stuff['ldap']['markup'] .= '<li>employeenumber: ' . $entry['employeenumber'] . "</li>\n";
      $stuff['ldap']['markup'] .= "</ul>\n";
    }
    else {
      $stuff['ldap']['data'] = NULL;

      $stuff['ldap']['markup'] .= "No LDAP entry found.\n";
    }
  }

  function existence_cookie(&$stuff) {
    $agent_string = 'unknown';
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent_string = $_SERVER['HTTP_USER_AGENT'];
    }


    // create an existence cookie
    $cookie = new c_base_cookie();
    $cookie->set_name("test-cookie_existence-" . (isset($_SERVER["HTTPS"]) ? 'ssl' : 'no_ssl'));
    $cookie->set_path('/');
    $cookie->set_domain('.localhost');
    $cookie->set_secure(isset($_SERVER["HTTPS"]));
    $cookie->set_max_age(600);
    $cookie->set_http_only(TRUE);
    $cookie->set_first_only(TRUE);
    $cookie->set_same_site(c_base_cookie::SAME_SITE_STRICT);

    $result = $cookie->do_pull();
    if ($result instanceof c_base_return_true) {
      $data = $cookie->get_value_exact();

      $same_site = '';
      switch ($cookie->get_same_site()->get_value_exact()) {
        case c_base_cookie::SAME_SITE_NONE:
          $same_site = 'no restriction';
          break;
        case c_base_cookie::SAME_SITE_RELAXED:
          $same_site = 'relaxed';
          break;
        case c_base_cookie::SAME_SITE_STRICT:
          $same_site = 'strict';
          break;
      }

      #$expire_string = c_base_defaults_global::s_get_date("D, d M Y H:i:s T", $cookie->get_expires()->get_value_exact())->get_value_exact();
      #$max_age_string = c_base_defaults_global::s_get_date("D, d M Y H:i:s T", $cookie->get_max_age()->get_value_exact())->get_value_exact();
      $max_age_string = $cookie->get_max_age()->get_value_exact();

      $validated = "Invalid";
      if ($cookie->validate() instanceof c_base_return_true) {
        $validated = "Valid";
      }

      $stuff['cookie_existence'] = ['exists' => []];
      $stuff['cookie_existence']['exists'] = " - The cookie settings:<br>";
      $stuff['cookie_existence']['exists'] .= " -- Name: '" . $cookie->get_name()->get_value_exact() . "'<br>";
      $stuff['cookie_existence']['exists'] .= " -- Domain: '" . $cookie->get_domain()->get_value_exact() . "'<br>";
      $stuff['cookie_existence']['exists'] .= " -- Path: '" . $cookie->get_path()->get_value_exact() . "'<br>";
      $stuff['cookie_existence']['exists'] .= " -- Value:<br>";
      $stuff['cookie_existence']['exists'] .= " ---- Message: " . $data['message'] . "<br>";
      $stuff['cookie_existence']['exists'] .= " ---- Expire: " . $data['expire'] . "<br>";
      $stuff['cookie_existence']['exists'] .= " ---- Checksum: " . $data['checksum'] . "<br>";
      $stuff['cookie_existence']['exists'] .= " ---- Checksum Validity: " . $validated . "<br>";
      $stuff['cookie_existence']['exists'] .= " -- Max-Age: '" . $max_age_string . "'<br>";
      #$stuff['cookie_existence']['exists'] .= " -- Expires: '" . $expire_string . "'<br>";
      $stuff['cookie_existence']['exists'] .= " -- HTTP Only: '" . ($cookie->get_http_only()->get_value_exact() ? 'True' : 'False') . "'<br>";
      $stuff['cookie_existence']['exists'] .= " -- First Only: '" . ($cookie->get_first_only()->get_value_exact() ? 'True' : 'False') . "'<br>";
      $stuff['cookie_existence']['exists'] .= " -- Same Site: '" . $same_site . "'<br>";
      $stuff['cookie_existence']['exists'] .= "<br>";

      unset($data);
      unset($validated);
      unset($expire_string);
      unset($max_age_string);
      unset($same_site);
    }
    else {
      $expire = '+10 minutes';
      $expire_stamp = strtotime($expire);
      $expire_string = c_base_defaults_global::s_get_date("D, d M Y H:i:s T", $expire_stamp)->get_value_exact();
      $same_site = '';
      switch ($cookie->get_same_site()->get_value_exact()) {
        case c_base_cookie::SAME_SITE_NONE:
          $same_site = 'no restriction';
          break;
        case c_base_cookie::SAME_SITE_RELAXED:
          $same_site = 'relaxed';
          break;
        case c_base_cookie::SAME_SITE_STRICT:
          $same_site = 'strict';
          break;
      }

      #$max_age_string = c_base_defaults_global::s_get_date("D, d M Y H:i:s T", $cookie->get_max_age()->get_value_exact())->get_value_exact();
      $max_age_string = $cookie->get_max_age()->get_value_exact();

      $data = [
        'message' => "Your agent string is: " . $agent_string,
        'expire' => gmdate("D, d-M-Y H:i:s T", $expire_stamp),
      ];
      $cookie->set_value($data);
      $cookie->set_expires($expire_stamp);
      $checksum = $cookie->build_checksum()->get_value_exact();

      $stuff['cookie_existence'] = ['new' => []];
      $stuff['cookie_existence']['new'] = " - The following cookie should be generated:<br>";
      $stuff['cookie_existence']['new'] .= " -- Name: '" . $cookie->get_name()->get_value_exact() . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- Domain: '" . $cookie->get_domain()->get_value_exact() . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- Path: '" . $cookie->get_path()->get_value_exact() . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- Value:<br>";
      $stuff['cookie_existence']['new'] .= " ---- Message: " . $data['message'] . "<br>";
      $stuff['cookie_existence']['new'] .= " ---- Expire: " . $data['expire'] . "<br>";
      $stuff['cookie_existence']['new'] .= " ---- Checksum (expected): " . $checksum . "<br>";
      $stuff['cookie_existence']['new'] .= " -- Max-Age: '" . $max_age_string . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- Expires: '" . $expire . "', or: '" . $expire_string . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- HTTP Only: '" . ($cookie->get_http_only()->get_value_exact() ? 'True' : 'False') . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- First Only: '" . ($cookie->get_first_only()->get_value_exact() ? 'True' : 'False') . "'<br>";
      $stuff['cookie_existence']['new'] .= " -- Same Site: '" . $same_site . "'<br>";
      $stuff['cookie_existence']['new'] .= "<br>";
      $stuff['cookie_existence']['cookie'] = $cookie;

      unset($data);
      unset($checksum);
      unset($expire);
      unset($expire_string);
      unset($expire_stamp);
      unset($max_age_string);
      unset($same_site);
    }
    unset($result);
    unset($agent_string);
  }

  process_received_headers($stuff);
  existence_cookie($stuff);
  session($stuff);
  send_prepared_headers($stuff);
  theme($stuff);
