<?php
/**
 * @file
 * Provides the standard site index class.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_users.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_session.php');
require_once('common/base/classes/base_paths.php');
require_once('common/base/classes/base_languages.php');

require_once('common/standard/classes/standard_paths.php');
require_once('common/standard/classes/standard_users.php');
require_once('common/standard/classes/standard_database.php');

require_once('common/theme/classes/theme_html.php');

/**
 * The standard class for use in index.php or equivalent.
 */
class c_standard_index extends c_base_return {
  protected const HTTP_RESPONSE_PROTOCOL = 'HTTP/1.1';

  protected const OUTPUT_TYPE_NONE = 0;
  protected const OUTPUT_TYPE_HTML = 1;
  protected const OUTPUT_TYPE_AJAX = 2;
  protected const OUTPUT_TYPE_FILE = 3;

  protected $settings;

  protected $http;
  protected $session;
  protected $database;

  protected $languages_selected; // primary language of the document.
  protected $lanaguages_all;     // all languages used in the document.

  protected $paths;
  protected $processed;   // unrenderred output.
  protected $output;      // renderred output.
  protected $output_type; // index-specific mime types for output handling.

  private $original_output_buffering;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->settings = [];

    // database information
    $this->settings['database_host']                = '127.0.0.1';
    $this->settings['database_port']                = 5432;
    $this->settings['database_name']                = NULL;
    $this->settings['database_user_public']         = NULL;
    $this->settings['database_user_public_default'] = TRUE; // when TRUE, auto-login as public account by default, when FALSE do not attempt anonymous database connection.
    $this->settings['database_timeout']             = 4;
    $this->settings['database_ssl_mode']            = 'disable';
    $this->settings['database_create_account_host'] = '127.0.0.1';
    $this->settings['database_create_account_port'] = 5433;

    // cookie/session information
    $this->settings['cookie_name']      = NULL;
    $this->settings['cookie_path']      = '/';
    $this->settings['cookie_domain']    = 'localhost'; // warning: the standards require '.' in front, but webkit-based browsers reject such domains as '.localhost'.
    $this->settings['cookie_http_only'] = FALSE; // setting this to false will allow javascript to access this cookie, such as for ajax.
    $this->settings['cookie_host_only'] = TRUE;
    $this->settings['cookie_same_site'] = c_base_cookie::SAME_SITE_STRICT;
    $this->settings['cookie_secure']    = TRUE;
    $this->settings['session_socket']   = '/programs/sockets/sessionize_accounts/';
    $this->settings['session_system']   = 'standard';
    $this->settings['session_expire']   = 1200; // 20 minutes
    $this->settings['session_max']      = 7200; // 120 minutes / 2 hours

    // ldap information
    $this->settings['ldap_server']        = NULL; // 'ldaps://127.0.0.1:1636/';
    $this->settings['ldap_bind_name']     = NULL;
    $this->settings['ldap_bind_password'] = NULL;
    $this->settings['ldap_base_dn']       = '';
    $this->settings['ldap_fields']        = [];

    // base settings
    $this->settings['base_scheme']       = 'https';
    $this->settings['base_host']         = 'localhost';
    $this->settings['base_port']         = ''; // @todo: implement support for thus such that base_port is something like: ':8080' (as opposed to '8080').
    $this->settings['base_path']         = '/'; // must end in a trailing slash.
    $this->settings['base_path_prefix']  = ''; // identical to base_path, except there is no trailing slash.
    $this->settings['response_encoding'] = FALSE; // set to FALSE to disable, otherwise set to a valid $compression parameter value for c_base_http::encode_response_content().

    if (!isset($_SERVER["HTTPS"])) {
      $this->settings['base_scheme'] = 'http';
    }

    // theme information
    $this->settings['system_name'] = $this->settings['session_system'];
    $this->settings['base_css'] = 'standard-';

    // The HTML tag <p>, represents a paragraph.
    // However, many sites, services, and developers incorrectly use it to represent text.
    // The definition of the word 'paragraph' contradicts this current usage of the HTML tag <p>.
    // It is also important to note that many browsers will alter the content of the <p> tag to remove blocks of any kind, such as <ul>.
    // The <span> tag does not seem to have this issue.
    // Therefore, the use of HTML <p> tag is consider non-safe and likely to cause problems with formatting (because client browsers alter the HTML).
    // This provides a way to still use <p> tags despite the implementation, usage, and context flaws.
    $this->settings['standards_issue-use_p_tags'] = FALSE;

    // cache/static file paths
    $this->settings['cache_static'] = ''; // example: '/var/www-static';
    $this->settings['cache_error']  = ''; // example: '/var/www-error';

    $this->http     = new c_base_http();
    $this->session  = new c_base_session();
    $this->database = new c_standard_database();

    $this->languages_selected = NULL;
    $this->languages_all      = NULL;

    $this->paths       = NULL;
    $this->processed   = NULL;
    $this->output      = NULL;
    $this->output_type = static::OUTPUT_TYPE_HTML;

    $this->original_output_buffering = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->settings);

    unset($this->user_current);
    unset($this->user_session);

    unset($this->http);
    unset($this->session);
    unset($this->database);

    unset($this->languages_selected);
    unset($this->languages_all);

    unset($this->paths);
    unset($this->processed);
    unset($this->output);
    unset($this->output_type);

    unset($this->original_output_buffering);

    parent::__destruct();
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, []);
  }

  /**
   * Initialize the default database settings.
   *
   * @param c_base_database &$database
   *   The database object.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_do_initialize_database(&$database) {
    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $database->do_query('set bytea_output to hex');
    $database->do_query('set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public');
    $database->do_query('set datestyle to us');
    $database->do_query('set timezone to UTC');

    return new c_base_return_true();
  }

  /**
   * Initialize any globals as expected by this class.
   *
   * This is not intended to be called within this class because global should be global.
   * This, or something like this, should instead be called within the appropriate index.php file.
   *
   * This is also not necessary given that the c_base_defaults_global is intended to be defined and included by the developer instead of being included by default.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function do_initialize_globals() {
    c_base_defaults_global::s_get_timestamp_session(TRUE);

    $class = c_base_defaults_global::LANGUAGE_CLASS_DEFAULT;
    c_base_defaults_global::s_set_languages(new $class());
    unset($class);


    return new c_base_return_true();
  }

  /**
   * Execution the standard index.
   *
   * This should effectively be the 'main' function.
   *
   * @param string $paths_handler
   *   (optional) The name of a class that is an instance of c_base_paths.
   *   You must pre-load the class file before calling this function.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function do_execute($paths_handler = 'c_standard_paths') {
    if (!is_string($paths_handler) || strlen($paths_handler) == 0 || !class_exists($paths_handler) || !($paths_handler instanceof c_base_paths)) {
      $paths_handler = 'c_standard_paths';
    }

    $this->pr_do_set_up();

    $this->pr_do_receive_request();
    gc_collect_cycles();

    $this->pr_do_process_sessions();
    gc_collect_cycles();

    $this->pr_do_process_request($paths_handler);
    gc_collect_cycles();

    $this->pr_do_render_theme();
    gc_collect_cycles();

    $this->pr_do_build_response();
    gc_collect_cycles();

    $this->pr_do_send_response();
    gc_collect_cycles();

    $this->pr_do_break_down();
    gc_collect_cycles();

    return new c_base_return_true();
  }

  /**
   * Perform any initial setup operations.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_set_up() {
    // turn off any op-caching (this is done for testing and will likely be commented out or removed in production).
    ini_set('opcache.enable', FALSE);
    ini_set('opcache.enable_cli', FALSE);

    // enable output buffering to catch any unexpected output.
    $this->original_output_buffering = ini_get('output_buffering');
    ini_set('output_buffering', TRUE);

    ob_start();

    $this->do_initialize_globals();

    return new c_base_return_true();
  }

  /**
   * Load and sanitize the request.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_receive_request() {
    $this->http->do_load_request();

    // Assign a default response protocol.
    $this->http->set_response_protocol(static::HTTP_RESPONSE_PROTOCOL);

    // Assign a default response status (expected to be overridden by path handlers).
    $this->http->set_response_status(c_base_http_status::OK);

    // get the current language and assign the default, using us-english as a failsafe language.
    $this->languages_all = c_base_defaults_global::s_get_languages()::s_get_ids()->get_value_exact();
    if (is_array($this->languages_all) && !empty($this->languages_all)) {
      $this->languages_selected = $this->http->select_language($this->languages_all)->get_value_exact();
    }
    else {
      $this->languages_selected = i_base_languages::ENGLISH_US;
      $this->languages_all = [i_base_languages::ENGLISH_US => i_base_languages::ENGLISH_US, i_base_languages::ENGLISH => i_base_languages::ENGLISH];
    }

    // select the primary language.
    $this->http->set_response_content_language($this->languages_selected, FALSE);

    // this website is primarily us-english, also set this as an additional language because multi-lingual support is not 100% guaranteed.
    if ($this->languages_selected != i_base_languages::ENGLISH_US) {
      $this->http->set_response_content_language(i_base_languages::ENGLISH_US);
    }

    if ($this->languages_selected != i_base_languages::ENGLISH) {
      $this->http->set_response_content_language(i_base_languages::ENGLISH);
    }

    return new c_base_return_true();
  }

  /**
   * Load an process the sessions and session cookies.
   *
   * This processes existing connections, it does not create new ones.
   *
   * The database is generally not connected to here for performance reasons.
   * Static content may not need database connections and so avoid connecting to the database when unnecessary.
   * This means that user account information is not expected to be loaded into the session via this function.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   TRUE with error bit set is returned if no session cookie is defined.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_process_sessions() {
    $cookie_login = $this->http->get_request(c_base_http::REQUEST_COOKIE, $this->settings['cookie_name']);

    $no_session = FALSE;
    if (!($cookie_login instanceof c_base_cookie)) {
      $cookie_login = new c_base_cookie();

      $no_session = TRUE;
    }

    // create a session object regardless of login session cookie.
    $this->session = new c_base_session();
    $this->session->set_socket_directory($this->settings['session_socket']);
    $this->session->set_system_name($this->settings['session_system']);

    // the requester should not have any control over specifying/changing these settings, so overwrite whatever is defined by the request cookie.
    $cookie_login->set_name($this->settings['cookie_name']);
    $cookie_login->set_path($this->settings['cookie_path']);
    $cookie_login->set_domain($this->settings['cookie_domain']);
    $cookie_login->set_http_only($this->settings['cookie_http_only']);
    $cookie_login->set_host_only($this->settings['cookie_host_only']);
    $cookie_login->set_same_site($this->settings['cookie_same_site']);
    $cookie_login->set_secure($this->settings['cookie_secure']);

    if (empty($_SERVER['REMOTE_ADDR'])) {
      $this->session->set_host('0.0.0.0');
    }
    else {
      $this->session->set_host($_SERVER['REMOTE_ADDR']);
    }

    // no session cookie has been defined, so there is no existing session to load.
    if ($no_session) {
      unset($cookie_login);
      unset($no_session);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{session_name}' => $this->settings['cookie_name'], ':{function_name}' => __FUNCTION__]], i_base_error_messages::NO_SESSION);
      return c_base_return_error::s_true($error);
    }
    unset($no_session);

    $cookie_data = $cookie_login->get_value_exact();
    if (!($cookie_login->validate() instanceof c_base_return_true) || empty($cookie_data['session_id'])) {
      $cookie_login->set_expires(0);
      $cookie_login->set_max_age(NULL);
      $this->session->set_cookie($cookie_login);
      unset($cookie_login);

      // cookie_login failed validation or the cookie contains no session id.
      $error = c_base_error::s_log(NULL, ['arguments' => [':{session_name}' => $this->settings['cookie_name'], ':{function_name}' => __FUNCTION__]], i_base_error_messages::SESSION_INVALID);

      // also set the error on the session object.
      $this->session->set_error($error);

      $this->session->set_session_id(NULL);
      $this->session->is_logged_in(FALSE);

      return c_base_return_error::s_false($error);
    }

    $this->session->set_session_id($cookie_data['session_id']);


    // connect to the session using the given session id.
    $session_connection = $this->session->do_connect();
    if (c_base_return::s_has_error($session_connection)) {
      // @todo: process the specific error conditions.
      //        some errors the cookie should be deleted and others it should remain untouched.

      // also set the error on the session object.
      $errror = $session_connection->get_error();
      $this->session->set_error($error);
      unset($session_connection);

      $this->session->set_session_id(NULL);
      $this->session->is_logged_in(FALSE);

      return c_base_return_error::s_false($error);
    }
    unset($session_connection);

    $session_loaded = $this->session->do_pull();
    $this->session->do_disconnect();

    if (c_base_return::s_has_error($session_loaded)) {
      // @todo: process the specific error conditions.
      //        some errors the cookie should be deleted and others it should remain untouched.

      // also set the error on the session object.
      $error = $session_loaded->get_error();
      $this->session->set_error($error);
      unset($session_loaded);

      $this->session->set_session_id(NULL);
      $this->session->is_logged_in(FALSE);

      return c_base_return_error::s_false($error);
    }

    // if either the session name or password is undefined for any reason, then consider the session invalid and expire it.
    if (is_null($this->session->get_name()->get_value()) || is_null($this->session->get_password()->get_value())) {
      $cookie_login->set_expires(0);
      $cookie_login->set_max_age(NULL);
      $this->session->set_cookie($cookie_login);
      unset($cookie_login);

      $this->session->set_session_id(NULL);
      $this->session->is_logged_in(FALSE);

      return new c_base_return_true();
    }

    $this->session->is_logged_in(TRUE);
    unset($session_loaded);

    // check to see if the session timeout has been extended and if so, then update the cookie.
    $session_expire = $this->session->get_timeout_expire()->get_value_exact();

    // if the session is expired, do not extend it and expire it along with the cookie.
    // by setting the is expired flag, special pages and login prompts can be presented to tell the user that their session is expired.
    $session_difference = $session_expire - time();
    if ($session_difference <= 0) {
      unset($cookie_login);

      $this->session->is_logged_in(FALSE);
      $this->session->is_expired(TRUE);

      return new c_base_return_true();
    }

    if ($session_expire > $cookie_data['expire']) {
      $cookie_data['expire'] = gmdate("D, d-M-Y H:i:s T", $session_expire);
      $cookie_login->set_value($cookie_data);
      $cookie_login->set_expires($session_expire);
    }
    unset($session_expire);

    $this->session->set_cookie($cookie_login);
    unset($cookie_login);

    return new c_base_return_true();
  }

  /**
   * Connect to the database, loading any relevant information.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_database_connect() {
    if ($this->session->is_logged_in()->get_value_exact()) {
      $user_name = $this->session->get_name()->get_value();
      $password = $this->session->get_password()->get_value();
    }
    else {
      $user_name = $this->settings['database_user_public'];
      $password = NULL;

      // do not login with public/anoynmous account if default is disabled.
      if (!$this->settings['database_user_public_default']) {
        return new c_base_return_false();
      }
    }

    if (!is_string($user_name) || strlen($user_name) < 0) {
      return new c_base_return_false();
    }


    // prepare the connection string.
    $connection_string = new c_base_database_connection_string();
    $connection_string->set_host($this->settings['database_host']);
    $connection_string->set_port($this->settings['database_port']);
    $connection_string->set_database($this->settings['database_name']);

    $connection_string->set_user($user_name);
    if (!is_null($password)) {
      $connection_string->set_password($password);
    }

    $connection_string->set_ssl_mode($this->settings['database_ssl_mode']);
    $connection_string->set_connect_timeout($this->settings['database_timeout']);

    $this->database->set_connection_string($connection_string);
    unset($connection_string);


    // open a database connection
    $result = $this->database->do_connect();
    if (c_base_return::s_has_error($result)) {
      if ($user_name == $this->settings['database_user_public']) {
        $error_message = $result->get_error(0)->get_message();
        if (preg_match('/fe_sendauth: no password supplied/i', $error_message) > 0) {
          $error = c_base_error::s_log('Unable to connect to database with public account (message = ' . $error_message . ').', ['arguments' => [':{database_account}' => $account_name, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::POSTGRESQL_NO_ACCOUNT);
          return c_base_return_error::s_false($error);
        }
        unset($error_message);
      }
      return c_base_return_error::s_false($result->get_error());
    }
    unset($result);

    self::s_do_initialize_database($this->database);


    // load database session information.
    $user_current = new c_standard_users_user();
    $database_loaded = $user_current->do_load($this->database);
    if ($database_loaded instanceof c_base_return_true) {
      unset($database_loaded);

      $this->session->set_user_current($user_current);
    }
    else {
      if (c_base_return::s_has_error($database_loaded)) {
        $error_message = $database_loaded->get_error(0);
      }
      else {
        $error_message = NULL;
      }
      unset($database_loaded);

      $account_name = $user_current->get_name_machine();
      if ($account_name instanceof c_base_return_string) {
        $account_name = $account_name->get_value_exact();
      }
      else {
        $account_name = '';
      }

      $error = c_base_error::s_log($error_message, ['arguments' => [':{database_account}' => $account_name, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::POSTGRESQL_NO_ACCOUNT);
      unset($account_name);
      unset($error_message);

      return c_base_return_error::s_false($error);
    }
    unset($user_current);

    $user_session = new c_standard_users_user();
    if ($user_session->do_load($this->database, TRUE) instanceof c_base_return_true) {
      $this->session->set_user_current($user_session);
    }
    else {
      // @todo: handle errors.
    }
    unset($user_session);

    return new c_base_return_true();
  }

  /**
   * Disconnect from the database.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_database_disconnect() {
    if ($this->database instanceof c_base_database && $this->database->is_connected()->get_value_exact()) {
      $this->database->do_disconnect();
    }

    return new c_base_return_true();
  }

  /**
   * Process the request.
   *
   * @param string $paths_handler
   *   The name of a class that is an instance of c_standard_paths.
   *   You must pre-load the class file before calling this function.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_process_request($paths_handler) {
    // @todo: handle database connection error and then call $paths_handler() to handle the appropriate errors.
    $connected = $this->pr_do_database_connect();

    // require $paths_handler to be a valid instance of c_base_paths.
    // if it is not, then use the standard so that some sort of error handling can be performed..
    if ($paths_handler instanceof c_standard_paths) {
      $paths = new $paths_handler();
    }
    else {
      $paths = new c_standard_paths();
    }

    if (c_base_return::s_has_error($connected)) {
      $this->session->set_error($connected->get_error());
      unset($connected);
      $error_page = $paths->get_handler_server_error($this->http, $this->database, $this->session, $this->settings);
      $executed = $error_page->do_execute($this->http, $this->database, $this->session, $this->settings);
      unset($error_page);
    }
    else {
      unset($connected);

      $executed = $paths->do_process_path($this->http, $this->database, $this->session, $this->settings);
    }
    unset($paths);

    $this->processed = $executed->get_output();
    unset($executed);

    $this->pr_do_log_user_activity();

    $this->pr_do_database_disconnect();

    return new c_base_return_true();
  }

  /**
   * Render any themes as the output.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_render_theme() {
    // @fixme: this needs to support more output types than just html.
    if ($this->processed instanceof c_base_html) {
      $theme = new c_theme_html();
      $theme->set_html($this->processed);
      $theme->set_http($this->http);
      $theme->render_markup();

      $this->output = $theme->get_markup()->get_value_exact();
      $this->output_type = static::OUTPUT_TYPE_HTML;
    }
    elseif ($this->processed instanceof c_base_file) {
      // @todo: write a class, such as c_theme_file, to handle formatting the file output.
      $this->output = '';
      $this->output_type = static::OUTPUT_TYPE_FILE;
    }
    elseif ($this->processed instanceof c_base_ajax) {
      // all ajax needs to respond with is a jsonized string.
      $this->output = $this->processed->get_items_jsonized()->get_value_exact();
      if (!is_string($this->output)) {
        // this happens on error. @todo: handle error.
        $this->output = '';
      }
      $this->output_type = static::OUTPUT_TYPE_AJAX;
    }
    else {
      // nothing to output.
      $this->output = '';
      return new c_base_return_false();
    }

    // clear the processed variable to save resources.
    $this->processed = NULL;

    return new c_base_return_true();
  }

  /**
   * Build the response for the client.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_build_response() {
    $this->http->set_response_checksum_header(c_base_http::CHECKSUM_ACTION_AUTO);
    $this->http->set_response_content($this->output);


    // send the session cookie if a session id is specified.
    $session_id = $this->session->get_session_id()->get_value_exact();
    if (!empty($session_id)) {
      $cookie_login = $this->session->get_cookie();

      if ($cookie_login instanceof c_base_cookie) {
        $this->http->set_response_set_cookie($cookie_login);
      }
      unset($cookie_login);
    }
    unset($session_id);

    return new c_base_return_true();
  }

  /**
   * Send the response to the client.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_send_response() {
    // get current http header method, which may determine which headers to set or not set.
    $method = $this->http->get_request(c_base_http::REQUEST_METHOD)->get_value_exact();
      if (isset($method['data']) && is_int($method['data'])) {
      $method = $method['data'];
    }
    else {
      $method = c_base_http::HTTP_METHOD_NONE;
    }

    // process any unexpected, but captured, output (if the content is a file, silently ignore output).
    if (ob_get_length() > 0 && $this->http->is_response_content_file()->get_value() === FALSE) {
      $response_content = $this->http->get_response_content()->get_value_exact();
      $this->http->set_response_content(ob_get_contents(), FALSE);
      $this->http->set_response_content($response_content);
      unset($response_content);
    }

    ob_end_clean();


    // build the http response headers.
    if ($this->output_type === static::OUTPUT_TYPE_FILE) {
      $this->http->set_response_date();
      $this->http->set_response_vary('Host');
      $this->http->set_response_vary('User-Agent');
      $this->http->set_response_vary('Accept');
      $this->http->set_response_vary('Accept-Language');

      // @todo: assign file-specific headers
    }
    elseif ($this->output_type === static::OUTPUT_TYPE_AJAX) {
      $this->http->set_response_date();
      $this->http->set_response_pragma('no-cache');
      $this->http->set_response_vary('Host');
      $this->http->set_response_vary('User-Agent');
      $this->http->set_response_vary('Accept');
      $this->http->set_response_vary('Accept-Language');

      // @todo: assign ajax-related headers
    }
    else {
      // use html output type on request and on fail.
      $this->http->set_response_date();
      $this->http->set_response_content_type('text/html');
      #$this->http->set_response_etag();
      #$this->http->set_response_last_modified(strtotime('now'));
      #$this->http->set_response_expires(strtotime('+30 minutes'));
      $this->http->set_response_pragma('no-cache');
      $this->http->set_response_vary('Host');
      $this->http->set_response_vary('User-Agent');
      $this->http->set_response_vary('Accept');
      $this->http->set_response_vary('Accept-Language');
    }

    #$this->http->set_response_warning('1234 This site is under active development.');

    // finalize the content prior to sending headers to ensure header accuracy.
    if ($this->settings['response_encoding'] !== FALSE) {
      $this->http->encode_response_content($this->settings['response_encoding']);
    }

    // http head method responses do not sent content.
    if ($method === c_base_http::HTTP_METHOD_HEAD) {
      $this->http->set_response_content('', FALSE);
    }


    // manually disable output buffering (if enabled) when transfer headers and content.
    #$old_output_buffering = ini_get('output_buffering');
    #ini_set('output_buffering', 'off');


    // when the headers are sent, checksums are created, so at this point all error output should be stored and not sent.
    $this->http->send_response_headers(TRUE);


    // once the header are sent, send the content.
    $this->http->send_response_content();


    #ini_set('output_buffering', $old_output_buffering);
    #unset($old_output_buffering);

    return new c_base_return_true();
  }

  /**
   * Terminate any active database connections.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_unload_database() {
    if ($this->database->is_connected() instanceof c_base_return_true) {
      $this->database->do_disconnect();
    }

    return new c_base_return_true();
  }

  /**
   * Perform any final break-down operations.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_break_down() {
    if (!is_null($this->original_output_buffering)) {
      ini_set('output_buffering', $this->original_output_buffering);
    }

    // make sure the database is disconnected.
    $this->pr_do_database_disconnect();

    return new c_base_return_true();
  }

  /**
   * Add a log entry for the current user in regards to their request.
   *
   * This is expected to include a log of the intended response code and should be called only after the response has been determined.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_do_log_user_activity() {
    if ($this->database->is_connected() instanceof c_base_return_false) {
      // an active database connection is required.
      return new c_base_return_false();
    }

    $query_string = 'insert into v_log_user_activity_self_insert (request_path, request_arguments, request_client, request_headers, response_headers, response_code)';
    $query_string .= ' values ($1, $2, ($3, $4, $5), $6, $7, $8)';

    $query_parameters = [];
    $query_parameters[0] = $this->http->get_request_uri_relative($this->settings['base_path'])->get_value_exact();
    $query_parameters[1] = $this->http->get_request_uri_query($this->settings['base_path'])->get_value_exact();

    $query_parameters[2] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $query_parameters[3] = isset($_SERVER['REMOTE_PORT']) && is_numeric($_SERVER['REMOTE_PORT']) ? (int) $_SERVER['REMOTE_PORT'] : 0;
    $query_parameters[4] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ;

    $query_parameters[5] = json_encode($this->http->get_request_headers()->get_value_exact());
    $query_parameters[6] = json_encode($this->http->get_response()->get_value_exact());
    $query_parameters[7] = $this->http->get_response_status()->get_value_exact();

    $query_result = $this->database->do_query($query_string, $query_parameters);

    if (c_base_return::s_has_error($query_result)) {
      $last_error = $this->database->get_last_error()->get_value_exact();

      $false = c_base_return_error::s_false($query_result->get_error());
      unset($query_result);

      if (!empty($last_error)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{database_error_message}' => $last_error, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::POSTGRESQL_ERROR);
        $false->set_error($error);
      }
      unset($last_error);

      return $false;
    }
    unset($query_result);

    return new c_base_return_true();
  }
}
