<?php
// assign custom include path.
set_include_path('/var/git/koopa');

// load the global defaults file (this file is not included by default but is required by all).
// replace this with your own as you see fit.
require_once('common/base/classes/base_defaults_global.php');

require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_ldap.php');
require_once('common/base/classes/base_markup.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_charset.php');
require_once('common/base/classes/base_database.php');
require_once('common/base/classes/base_languages.php');

require_once('program/reservation/reservation_database.php');
require_once('program/reservation/reservation_session.php');
require_once('program/reservation/reservation_paths.php');
require_once('program/reservation/reservation_build.php');

/**
 * Load all custom settings.
 *
 * @return array
 *   Custom settings.
 */
function reservation_load_settings() {
  ini_set('opcache.enable', FALSE);
  ini_set('opcache.enable_cli', FALSE);

  // only enable output buffering during the output stage, keep it disabled until then.
  ini_set('output_buffering', FALSE);

  $settings = array();

  // database information
  $settings['database_host'] = '127.0.0.1';
  $settings['database_port'] = 5432;
  $settings['database_name'] = 'reservation';
  $settings['database_user'] = 'u_reservation_public';
  $settings['database_password'] = NULL;
  $settings['database_timeout'] = 4;
  #$settings['database_ssl_mode'] = 'require';
  $settings['database_ssl_mode'] = 'disable';
  $settings['database_create_account_host'] = '127.0.0.1';
  $settings['database_create_account_port'] = 5433;

  // cookie/session information
  $settings['cookie_name'] = 'reservation-session';
  $settings['cookie_path'] = '/';
  $settings['cookie_domain'] = '.localhost';
  $settings['cookie_http_only'] = FALSE; // setting this to false will allow javascript to access this cookie, such as for ajax.
  $settings['cookie_host_only'] = TRUE;
  $settings['cookie_same_site'] = c_base_cookie::SAME_SITE_STRICT;
  $settings['session_socket'] = '/program/sockets/sessionize_accounts/';
  $settings['session_system'] = 'reservation';
  $settings['session_expire'] = 600; // 10 minutes
  $settings['session_max'] = 1800; // 30 minutes

  // ldap information
  $settings['ldap_server'] = 'ldaps://127.0.0.1:1636/';
  $settings['ldap_base_dn'] = 'ou=users,ou=People';
  $settings['ldap_fields'] = array('mail', 'gecos', 'givenname', 'cn', 'sn', 'employeenumber');

  // base settings
  $settings['base_scheme'] = 'https';
  $settings['base_host'] = 'localhost';
  $settings['base_path'] = $settings['cookie_path'];

  if (!isset($_SERVER["HTTPS"])) {
    $settings['base_scheme'] = 'http';
  }

  // default supported languages.
  c_base_defaults_global::s_set_languages(new c_base_language_limited());

  return $settings;
}

/**
 * Process HTTP request.
 *
 * @return c_base_http
 *   Processed and loaded request.
 */
function reservation_receive_request() {
  $http = new c_base_http();
  $http->do_load_request();

  // Assign a default response protocol.
  $http->set_response_protocol('HTTP/1.1');

  // Assign a default response status (expected to be overridden by path handlers).
  $http->set_response_status(c_base_http_status::OK);

  // get the current language and assign the default.
  $languages = c_base_defaults_global::s_get_languages()::s_get_ids()->get_value_exact();
  if (!is_array($languages) || empty($languages)) {
    $languages = array(i_base_language::ENGLISH_US => i_base_language::ENGLISH_US, i_base_language::ENGLISH => i_base_language::ENGLISH);
  }

  $selected = $http->select_language($languages)->get_value_exact();

  // select the primary language.
  $http->set_response_content_language($selected, FALSE);

  // this website is primary us-english (and therefore english), also set this as an additional language because multi-lingual support is not 100% guaranteed.
  if ($selected != i_base_language::ENGLISH_US) {
    $http->set_response_content_language(i_base_language::ENGLISH_US);
  }

  if ($selected != i_base_language::ENGLISH) {
    $http->set_response_content_language(i_base_language::ENGLISH);
  }
  unset($selected);

  return $http;
}

/**
 * Send HTTP response.
 *
 * @param c_base_http $http
 *   Http object.
 */
function reservation_send_response($http) {
  // add headers
  $http->set_response_date();
  $http->set_response_content_type('text/html');
  #$http->set_response_etag();
  #$http->set_response_last_modified(strtotime('now'));
  #$http->set_response_expires(strtotime('+30 minutes'));
  $http->set_response_pragma('no-cache');
  $http->set_response_vary('Host');
  $http->set_response_vary('User-Agent');
  $http->set_response_vary('Accept');
  $http->set_response_vary('Accept-Language');
  #$http->set_response_warning('1234 This site is under active development.');

  // finalize the content prior to sending headers to ensure header accuracy.
  $http->encode_response_content();


  // manually disable output buffering (if enabled) when transfer headers and content.
  $old_output_buffering = ini_get('output_buffering');
  ini_set('output_buffering', 'off');


  // when the headers are sent, checksums are created, so at this point all error output should be stored and not sent.
  $http->send_response_headers(TRUE);


  // once the header are sent, send the content.
  $http->send_response_content();


  ini_set('output_buffering', $old_output_buffering);
  unset($old_output_buffering);
}

/**
 * Process page request.
 *
 * @param c_base_http &$http
 *   Http object.
 * @param c_base_database &$databbase
 *   The database object.
 * @param array &$settings
 *   System settings
 * @param c_base_session &$session
 *   Session information.
 *
 * @return c_base_return
 *   The generated html is returned on success.
 *   The generated text is returned on success.
 *   This does not set the error bit on error.
 */
function reservation_process_request(&$http, &$database, &$session, &$settings) {
  $session_user = $session->get_name()->get_value_exact();
  if (is_null($session_user)) {
    $logged_in = FALSE;

    // @todo: delete old cookies, if they expire.
    $cookie_login = $session->get_cookie();

    // @fixme: shouldn't this check be in the session management code?
    // the session should already be logged into at this point.
    // if the session id exists, but no user id is defined, then the session is no longer valid, so delete the invalid cookie.
    if (!empty($session->get_session_id()->get_value_exact())) {
      $cookie_login->set_expires(-1);
      $cookie_login->set_max_age(-1);
      $session->set_cookie($cookie_login);
      unset($cookie_login);

      $session->set_session_id(NULL);
    }
  }
  else {
    $user_name = $session->get_name()->get_value();
    $password = $session->get_password()->get_value();

    if (is_null($user_name) || is_null($password)) {
      unset($user_name);
      unset($password);

      $logged_in = FALSE;
    }
    else {
      $logged_in = TRUE;
      reservation_database_string($database, $settings, $user_name, $password);

      unset($user_name);
      unset($password);
    }
  }

  $paths = new c_reservation_paths();
  $executed = $paths->reservation_process_path($http, $database, $session, $settings, $logged_in);
  unset($logged_in);
  unset($paths);

  return $executed->get_output();
}

/**
 * Render the theme.
 *
 * @param c_base_http $http
 *   Http object.
 * @param c_base_return $output
 *   The HTML object.
 */
function reservation_render_theme($http, $output) {
  $theme = new c_theme_html();
  $theme->set_html($output); // @fixme: this needs to be changed from set_html() to set_output() to handle more types of content.
  $theme->set_http($http);
  $theme->render_markup();

  return $theme->get_markup();
}

/**
 * Build the HTTP response.
 *
 * @param c_base_http &$http
 *   Http object.
 * @param c_base_session &$session
 *   Session information.
 * @param string $markup
 *   The HTML markup.
 */
function reservation_build_response(&$http, &$session, $markup) {
  $http->set_response_checksum_header(c_base_http::CHECKSUM_ACTION_AUTO);
  $http->set_response_content($markup);


  // send the session cookie if a session id is specified.
  $session_id = $session->get_session_id()->get_value_exact();
  if (!empty($session_id)) {
    $cookie_login = $session->get_cookie();

    if ($cookie_login instanceof c_base_cookie) {
      $http->set_response_set_cookie($cookie_login);
    }
    unset($cookie_login);
  }
  unset($session_id);

  return new c_base_return_true();
}

/**
 * Main Program Function
 *
 * note: Future designs will likely include content caching.
 *       There are different designs based on the type of content that can be used for caching.
 *       The following are some common generic areas to cache:
 *       - design 1 (public content): 3, 4. 5 (cache handling happens between 2 and 3).
 *       - design 2 (database bypass): 4 (4 is to be replaced with cache handling).
 *       - design 3 (theme bypass): 4. 5 (4 is to be replaced with cache handling).
 *       - design 4 (full private cache): 4. 5, 6* (should still handling login access, only (vary) headers are to be changed in 6).
 *       - design 5 (full public cache): 3, 4. 5, 6* (should still handling login access, only (vary) headers are to be changed in 6).
 *
 *       It is also recommended that some placeholders are added to the css body to provide dynamic css class names, even on cached content.
 */
function reservation_main() {
  // 1: local settings:
  $settings = reservation_load_settings();
  gc_collect_cycles();


  // 2: receive request information.
  $http = reservation_receive_request();
  gc_collect_cycles();


  // 3: process session information
  $session = reservation_process_sessions($http, $settings);
  gc_collect_cycles();


  // 4: perform actions, process work.
  $database = new c_base_database();
  $output = reservation_process_request($http, $database, $session, $settings);

  if ($database->is_connected() instanceof c_base_return_true) {
    $database->do_disconnect();
  }
  unset($database);
  gc_collect_cycles();


  // 5: build or finalize theme.
  $markup = reservation_render_theme($http, $output)->get_value_exact();
  unset($output);
  gc_collect_cycles();


  // 6: build response information.
  reservation_build_response($http, $session, $markup);
  unset($markup);
  gc_collect_cycles();


  // 7: send HTTP response.
  reservation_send_response($http);
  gc_collect_cycles();

  unset($settings);
  unset($http);
  unset($session);
  gc_collect_cycles();
}

reservation_main();
