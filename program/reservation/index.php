<?php
  // assign custom include path.
  set_include_path('/var/www/git/koopa');

  $root_path = 'common/base/classes/';
  require_once($root_path . 'base_http.php');
  require_once($root_path . 'base_cookie.php');
  require_once($root_path . 'base_ldap.php');
  require_once($root_path . 'base_markup.php');
  require_once($root_path . 'base_html.php');
  require_once($root_path . 'base_charset.php');
  require_once($root_path . 'base_database.php');

  $root_path = 'common/theme/classes/';
  require_once($root_path . 'theme_html.php');

  $root_path = 'program/reservation/';
  require_once($root_path . 'reservation_database.php');
  require_once($root_path . 'reservation_session.php');
  require_once($root_path . 'reservation_paths.php');

  unset($root_path);

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
    $settings['database_user'] = 'public_user';
    $settings['database_password'] = NULL;
    $settings['database_timeout'] = 4;
    $settings['database_ssl_mode'] = 'require';
    $settings['database_create_account_host'] = '127.0.0.1';
    $settings['database_create_account_port'] = 5433;

    // cookie/session information
    $settings['cookie_name'] = 'reservation-session';
    $settings['cookie_path'] = '/';
    $settings['cookie_domain'] = '.localhost';
    $settings['session_system'] = 'reservation';
    $settings['session_expire'] = 600; // 10 minutes
    $settings['session_max'] = 1800; // 30 minutes

    // ldap information
    $settings['ldap_server'] = 'ldaps://127.0.0.1:1636/';
    $settings['ldap_base_dn'] = 'ou=users,ou=People';
    $settings['ldap_fields'] = array('mail', 'gecos', 'givenname', 'cn', 'sn', 'employeenumber');

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

    return $http;
  }

  /**
   * Send HTTP response.
   *
   * @param c_base_http $http
   *   Http object.
   */
  function reservation_send_response($http) {
    $old_output_buffering = ini_get('output_buffering');
    ini_set('output_buffering', 'off');

    // finalize the content prior to sending headers to ensure header accuracy.
    $http->set_response_content_length();
    $http->encode_response_content();

    // when the headers are sent, checksums are created, so at this point all error output should be stored and not sent.
    $http->send_response_headers();
    flush();

    // once the header are sent, send the content.
    $http->send_response_content();
    flush();

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
   * @param c_base_cookie &$cookie_login
   *   Session login cookie.
   */
  function reservation_process_request(&$http, &$database, &$settings, &$session, &$cookie_login) {
    $html = new c_base_html();


    // assign class attribute
    $class = array(
      'reservation',
      'no-script',
      'is-html5',
    );

    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $class);
    unset($class);


    // assign id attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_ID, 'reservation-system');


    // assign language attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, i_base_language::ENGLISH_US);


    // assign direction attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION, 'ltr');


    // assign title header tag (setting title tag at delta 0 so that it can easily be overriden as needed).
    $tag = new c_base_markup_tag();
    $tag->set_type(c_base_markup_tag::TYPE_TITLE);
    $tag->set_text('Reservation System');
    $html->set_header($tag, 0);
    unset($tag);


    // assign base header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BASE)->get_value_exact();
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, 'http://localhost/');
    #$html->set_header($tag);
    #unset($tag);


    // assign http-equiv header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'Content-Type');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'text/html; charset=utf-8');
    $html->set_header($tag);
    unset($tag);


    // assign charset header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET, c_base_charset::UTF_8);
    $html->set_header($tag);
    unset($tag);


    // assign canonical header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, 'http://localhost/');
    #$html->set_header($tag);
    #unset($tag);


    // assign shortlink header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'shortlink');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, '/');
    #$html->set_header($tag);
    #unset($tag);


    // assign description header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'description');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'A reservation/scheduling system.');
    $html->set_header($tag);
    unset($tag);


    // assign distribution header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'distribution');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'web');
    $html->set_header($tag);
    unset($tag);


    // assign robots header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'robots');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'INDEX,FOLLOW');
    $html->set_header($tag);
    unset($tag);


    // assign expires header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'expires');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, date('r', strtotime('+30 minutes')));
    #$html->set_header($tag);
    #unset($tag);


    // assign viewport header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META)->get_value_exact();
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'viewport');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'width=device-width, initial-scale=1');
    $html->set_header($tag);
    unset($tag);


    // define any global css/javascript here as appropriate link/script c_base_markup_tag tag types.


    // finish building pages.
    if (!isset($_SERVER["HTTPS"])) {
      reservation_build_page_require_https($html);
    }
    elseif ($session === FALSE) {
      // check to see if user has filled out the login form.
      if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_id']) && $_POST['form_id'] == 'login_form') {
        $problems = reservation_attempt_login($database, $settings, $session, $cookie_login);

        if ($problems instanceof c_base_return_false) {
          // @todo: render default page.
          reservation_build_page_dashboard($html);
          // @todo: process and handle different paths here and load page as requested.
        }
        else {

          // @todo: render login failure.
          // $logged_in should be an array of error messages.
          reservation_build_login_page($html, $problems->get_value());
        }
      }
      else {
        reservation_build_login_page($html);
      }
    }
    else {
      reservation_build_page_dashboard($html);
      // @todo: process and handle different paths here and load page as requested.
    }

    return c_base_html_return::s_new($html);
  }

  /**
   * Render the theme.
   *
   * @param c_base_http $http
   *   Http object.
   * @param c_base_html $html
   *   The HTML object.
   */
  function reservation_render_theme($http, $html) {
    $theme = new c_theme_html();
    $theme->set_html($html);
    $theme->set_http($http);
    $theme->render_markup();

    return $theme->get_markup();
  }

  /**
   * Main Program Function
   */
  function reservation_main() {
    // 1: local settings:
    $settings = reservation_load_settings();
    gc_collect_cycles();


    // 2: receive request information.
    $http = reservation_receive_request();
    gc_collect_cycles();


    // 3: process session information
    $cookie_login = new c_base_cookie();
    $session = reservation_process_sessions($settings, $cookie_login)->get_value_exact();
    gc_collect_cycles();


    // 4: perform actions, process work.
    $database = new c_base_database();
    $html = reservation_process_request($http, $database, $settings, $session, $cookie_login)->get_value();
    if (!($html instanceof c_base_html)) {
      $html = new c_base_html();
    }

    if ($database->is_connected() instanceof c_base_return_true) {
      $database->do_disconnect();
    }
    unset($database);
    gc_collect_cycles();


    // 5: build or finalize theme.
    $markup = reservation_render_theme($http, $html)->get_value_exact();
    unset($html);
    gc_collect_cycles();


    // 6: build response information.
    $http->set_response_content($markup);
    $http->set_response_set_cookie($cookie_login);
    unset($markup);
    unset($cookie_login);
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
