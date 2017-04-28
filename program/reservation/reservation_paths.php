<?php
/**
 * @file
 * Provides reservation session functions.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_markup.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_ascii.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_mime.php');

require_once('common/theme/classes/theme_html.php');

require_once('program/reservation/reservation_database.php');
require_once('program/reservation/reservation_session.php');

class c_reservation_paths {
  // paths to common files (not url paths).
  private const PATH_LOGIN                = 'program/reservation/paths/u/';
  private const PATH_LOGOUT               = 'program/reservation/paths/u/';
  private const PATH_ACCESS_DENIED        = 'program/reservation/internal/';
  private const PATH_NOT_FOUND            = 'program/reservation/internal/';
  private const PATH_BAD_METHOD           = 'program/reservation/internal/';
  private const PATH_SERVER_ERROR         = 'program/reservation/internal/';
  private const PATH_OPTIONS_METHOD       = 'program/reservation/internal/';
  private const PATH_REDIRECTS            = 'program/reservation/';
  private const PATH_DASHBOARD_USER       = 'program/reservation/paths/u/';
  private const PATH_DASHBOARD_MANAGEMENT = 'program/reservation/paths/m/';
  private const PATH_DASHBOARD_ADMINISTER = 'program/reservation/paths/a/';

  private const NAME_LOGIN                = 'login';
  private const NAME_LOGOUT               = 'logout';
  private const NAME_ACCESS_DENIED        = 'access_denied';
  private const NAME_NOT_FOUND            = 'not_found';
  private const NAME_BAD_METHOD           = 'bad_method';
  private const NAME_SERVER_ERROR         = 'server_error';
  private const NAME_OPTIONS_METHOD       = 'options';
  private const NAME_REDIRECTS            = 'reservation_redirects';
  private const NAME_DASHBOARD_USER       = 'dashboard';
  private const NAME_DASHBOARD_MANAGEMENT = 'dashboard';
  private const NAME_DASHBOARD_ADMINISTER = 'dashboard';

  private const SCRIPT_EXTENSION = '.php';

  // a class name to prepend to css classes or id attributes.
  const CSS_BASE = 'reservation-';

  private $http     = NULL;
  private $database = NULL;
  private $settings = NULL;
  private $session  = NULL;
  private $output   = NULL;

  private $paths = NULL;
  private $path  = NULL;

  private $alias = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->http     = NULL;
    $this->database = NULL;
    $this->settings = NULL;
    $this->session  = NULL;
    $this->output   = NULL;

    $this->paths = NULL;
    $this->path  = NULL;

    $this->alias = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->http);
    unset($this->settings);
    unset($this->session);
    unset($this->output);

    unset($this->paths);
    unset($this->path);

    unset($this->alias);
  }

  /**
   * Process request path and determine what to do.
   *
   * @param c_base_http &$http
   *   The HTTP settings.
   * @param c_base_database &database
   *   The current database.
   * @param c_base_session &$session
   *   The current session.
   * @param c_base_html &$html
   *   The html page object.
   * @param array $settings
   *   The system settings array.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  public function reservation_process_path(&$http, &$database, &$session, $settings) {
    // @todo: these parameter errors might need a custom service unavailable and system log support.
    if (!($http instanceof c_base_http)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'http', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'session', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    $this->http = &$http;
    $this->database = &$database;
    $this->settings = $settings;
    $this->session = &$session;
    $this->output = NULL;

    $this->p_get_language_alias();

    // require HTTPS for access to any part of this website.
    if (!isset($_SERVER["HTTPS"])) {
      // @todo: redirect to https version of requested uri.
      $failure_path = $this->p_get_path_not_found();

      return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    // load all available paths.
    $this->p_paths_create();


    // load the http method.
    $method = $this->http->get_request(c_base_http::REQUEST_METHOD)->get_value_exact();
      if (isset($method['data']) && is_int($method['data'])) {
      $method = $method['data'];
    }
    else {
      $method = c_base_http::HTTP_METHOD_NONE;
    }


    // find the path
    $path = $this->http->get_request_uri_relative($settings['base_path'])->get_value_exact();
    $handler_settings = $this->paths->find_path($path)->get_value();
    unset($path);

    if (!is_array($handler_settings)) {
      // for all invalid pages, report bad method if not HTTP GET or HTTP POST.
      if ($method !== c_base_http::HTTP_METHOD_GET && $method !== c_base_http::HTTP_METHOD_POST) {
        unset($method);

        $failure_path = $this->p_get_path_bad_method();

        return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      unset($method);

      $not_found = $this->p_get_path_not_found();
      return $not_found->do_execute($this->http, $this->database, $this->session, $this->settings);
    }

    // validate allowed methods.
    if (isset($handler_settings['methods']) && is_array($handler_settings['methods'])) {
      if (!array_key_exists($method, $handler_settings['methods'])) {
        unset($method);

        $failure_path = $this->p_get_path_bad_method();

        return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
    }


    // HTTP OPTIONS method does not process the page, only returns available methods.
    if ($method === c_base_http::HTTP_METHOD_OPTIONS) {
      unset($method);

      $options_method_path = $this->p_get_path_options_method();

      if (isset($handler_settings['methods']) && is_array($handler_settings['methods'])) {
        $options_method_path->set_allowed_methods($handler_settings['methods']);
      }
      else {
        $options_method_path->set_allowed_methods(array());
      }

      return $options_method_path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    if (array_key_exists('redirect', $handler_settings)) {
      unset($method);

      // successfully logged in.
      require_once(self::PATH_REDIRECTS . self::NAME_REDIRECTS . self::SCRIPT_EXTENSION);

      if (!is_string($handler_settings['redirect'])) {
        $handler_settings['redirect'] = '';
      }

      if (!isset($handler_settings['code']) || !is_int($handler_settings['code'])) {
        $handler_settings['code'] = c_base_http_status::MOVED_PERMANENTLY;
      }

      $redirect = c_reservation_path_redirect::s_create_redirect($handler_settings['redirect'], $handler_settings['code'], FALSE);
      return $redirect->do_execute($this->http, $this->database, $this->session, $this->settings);
    }
    else {
      if (!empty($handler_settings['include_name']) && is_string($handler_settings['include_name'])) {
        require_once($handler_settings['include_directory'] . $handler_settings['include_name'] . self::SCRIPT_EXTENSION);
      }

      // execute path handler, using custom-language if defined.
      if (empty($handler_settings['handler'])) {
        return $this->p_get_path_server_error()->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      elseif (is_string($this->alias)) {
        @include_once($handler_settings['include_directory'] . $this->alias . '/' . $handler_settings['include_name']);

        $handler_class = $handler_settings['handler'] . '_' . $this->alias;
        if (class_exists($handler_class)) {
          $this->path = new $handler_class();

          unset($handler_class);
        }
        else {
          unset($handler_class);

          // attempt to fallback to default handler if the language-specific handler class is not found.
          if (!class_exists($handler_settings['handler'])) {
            return $this->p_get_path_server_error()->do_execute($this->http, $this->database, $this->session, $this->settings);
          }
          else {
            $this->path = new $handler_settings['handler']();
          }
        }
      }
      else {
        if (class_exists($handler_settings['handler'])) {
          $this->path = new $handler_settings['handler']();
        }
        else {
          return $this->p_get_path_server_error()->do_execute($this->http, $this->database, $this->session, $this->settings);
        }
      }

      if (isset($handler_settings['is_root']) && $handler_settings['is_root']) {
        $this->path->is_root(TRUE);
      }
    }
    unset($handler_settings);

    return $this->p_paths_normal($method);
  }

  /**
   * Creates and returns a list of all available paths.
   *
   * Add/modify paths here as desired.
   *
   * @return c_base_paths
   *   The generated paths object.
   */
  private function p_paths_create() {
    $this->paths = new c_base_paths();

    // set root path to be the user dashboard.
    $this->paths->set_path('', 'c_reservation_path_user_dashboard', self::PATH_DASHBOARD_USER, self::NAME_DASHBOARD_USER);

    // create login/logout paths
    $this->paths->set_path('/u/login', 'c_reservation_path_user_login', self::PATH_LOGIN, self::NAME_LOGIN);
    $this->paths->set_path('/u/logout', 'c_reservation_path_user_logout', self::PATH_LOGOUT, self::NAME_LOGOUT);

    // dashboards
    $this->paths->set_path('/u/dashboard', 'c_reservation_path_user_dashboard', self::PATH_DASHBOARD_USER, self::NAME_DASHBOARD_USER);
    #$this->paths->set_path('/m/dashboard', 'c_reservation_path_management_dashboard', self::PATH_DASHBOARD_MANAGEMENT, self::NAME_DASHBOARD_MANAGEMENT);
    #$this->paths->set_path('/a/dashboard', 'c_reservation_path_administer_dashboard', self::PATH_DASHBOARD_ADMINISTER, self::NAME_DASHBOARD_ADMINISTER);
  }

  /**
   * Process request paths and determine what to do.
   *
   * @param int $method
   *   The id of the HTTP request method.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  private function p_paths_normal($method) {
    $id_group = $this->path->get_id_group()->get_value_exact();

    // regardless of path-specific settings, the following paths always require login credentials to access.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M || $id_group === c_base_ascii::LOWER_U) {
      $this->path->is_private(TRUE);
    }

    if ($this->path instanceof c_reservation_path_user_login) {
      unset($id_group);
      return $this->path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }
    elseif ($this->path instanceof c_reservation_path_user_logout) {
      // if the user is not logged in. then provide a page not found for logout path.
      if (!$this->session->get_logged_in()->get_value_exact()) {
        return $this->p_get_path_not_found()->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
    }
    elseif ($this->path instanceof c_reservation_path_user_dashboard && $id_group === 0) {
      // the user dashboard is setup as the site root.
      // when path is root, there is no id_group, so explicitly assign the group.
      $id_group = c_base_ascii::LOWER_U;

      // @todo: do this for other dashboards as well (manager, and administer).
    }


    // if the request is private, make sure the user is logged in.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M || $id_group === c_base_ascii::LOWER_U || $this->path->is_private()->get_value_exact()) {
      if ($this->session->get_logged_in()->get_value_exact()) {
        unset($id_group);
        return $this->path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      elseif ($this->path->is_root()->get_value_exact()) {
        unset($id_group);

        $this->http->set_response_status(c_base_http_status::FORBIDDEN);

        $login_path = $this->p_get_path_login();
        return $login_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      else {
        // some special case paths always provide login prompt along with access denied.
        if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M || $id_group === c_base_ascii::LOWER_U) {
          unset($id_group);

          $this->http->set_response_status(c_base_http_status::FORBIDDEN);

          $login_path = $this->p_get_path_login();
          return $login_path->do_execute($this->http, $this->database, $this->session, $this->settings);
        }
      }
    }
    else {
      unset($id_group);
      return $this->path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }

    // return access denied or page not found depending on path and privacy settings.
    if ($id_group === c_base_ascii::LOWER_C || $id_group === c_base_ascii::LOWER_D || $id_group === c_base_ascii::LOWER_T || $id_group === c_base_ascii::LOWER_X || $id_group === c_base_ascii::LOWER_F) {
      // these always return not found for these paths.
      $failsafe_path = $this->p_get_path_not_found();
    }
    elseif ($this->path->is_private()->get_value_exact() && $id_group !== c_base_ascii::NULL) {
      // non private, and non-special case paths should return access denied as per normal behavior.
      $failsafe_path = $this->p_get_path_access_denied();
    }
    else {
      // all other case, return not found.
      $failsafe_path = $this->p_get_path_not_found();
    }
    unset($id_group);

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Process request path form paths and determine what to do.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  private function p_paths_forms() {
    // @todo

    // always return not found, do not inform user if the access is denied.
    $failsafe_path = $this->p_get_path_not_found();

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Process request path ajax paths and determine what to do.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  private function p_paths_ajax() {
    // @todo

    // always return not found, do not inform user if the access is denied.
    $failsafe_path = $this->p_get_path_not_found();

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Load and return the login path.
   */
  private function p_get_path_login() {
    return $this->p_include_path(self::PATH_LOGIN, self::NAME_LOGIN, 'c_reservation_path_user_login');
  }

  /**
   * Load and return the logout path.
   */
  private function p_get_path_logout() {
    return $this->p_include_path(self::PATH_LOGOUT, self::NAME_LOGOUT, 'c_reservation_path_user_logout');
  }

  /**
   * Load and return the access denied path.
   */
  private function p_get_path_access_denied() {
    return $this->p_include_path(self::PATH_ACCESS_DENIED, self::NAME_ACCESS_DENIED, 'c_reservation_path_access_denied');
  }

  /**
   * Load and return the not found path.
   */
  private function p_get_path_not_found() {
    return $this->p_include_path(self::PATH_NOT_FOUND, self::NAME_NOT_FOUND, 'c_reservation_path_not_found');
  }

  /**
   * Load and return the not found path.
   */
  private function p_get_path_bad_method() {
    return $this->p_include_path(self::PATH_BAD_METHOD, self::NAME_BAD_METHOD, 'c_reservation_path_bad_method');
  }

  /**
   * Load and return the internal server error path.
   */
  private function p_get_path_server_error() {
    return $this->p_include_path(self::PATH_SERVER_ERROR, self::NAME_SERVER_ERROR, 'c_reservation_path_server_error');
  }

  /**
   * Load and return the internal server error path.
   */
  private function p_get_path_options_method() {
    return $this->p_include_path(self::PATH_OPTIONS_METHOD, self::NAME_OPTIONS_METHOD, 'c_reservation_path_options_method');
  }

  /**
   * Load and save the current preferred language alias.
   *
   * This will be stored in $this->alias.
   */
  private function p_get_language_alias() {
    $aliases = array();
    $languages = $this->http->get_response_content_language()->get_value_exact();
    if (is_array($languages) && !empty($languages)) {
      $language = reset($languages);

      // us-english is the default, so do not attempt to include any external files.
      if ($language == i_base_language::ENGLISH_US || $language == i_base_language::ENGLISH) {
        unset($language);
        unset($aliases);
        unset($languages);

        $this->alias = NULL;
        return;
      }

      $aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id($language)->get_value_exact();
    }
    unset($language);

    // use default if no aliases are found.
    if (empty($aliases)) {
      unset($aliases);
      unset($languages);

      $this->alias = NULL;
      return;
    }

    $this->alias = end($aliases);
  }

  /**
   * Will include a custom language path if one exists.
   *
   * The default language files ends in "${path}${name}.php".
   * All other language files end in "${path}${language_alias}/${name}.php".
   *
   * The default class is the provided class name.
   * All other languages use the provided class name with '_${language_alias}' appended.
   *
   * For example (using path='my_file'), us english is the default, so that would load the file 'my_file.php'.
   *                                     japanese language load the file 'my_file-ja.php'.
   *
   * @param string $path
   *   The path to the include file, without the file name.
   * @param string $name
   *   The file name of the PHP file, without the '.php' extension.
   * @param string $class
   *   The name of the class, that is an instance of c_base_path, to execute.
   *
   * @return c_base_path
   *   The created c_base_path object.
   */
  private function p_include_path($path, $name, $class) {
    require_once($path . $name . self::SCRIPT_EXTENSION);

    // use default if no aliases are found.
    if (is_null($this->alias)) {
      return new $class();
    }

    // use include_once instead of require_require to allow for failsafe behavior.
    @include_once($path . $this->alias . '/' . $name . self::SCRIPT_EXTENSION);

    $language_class = $class . '_' . $this->alias;
    if (class_exists($language_class)) {
      return new $language_class();
    }
    unset($language_class);

    // if unable to find, fallback to original class
    return new $class();
  }
}

/**
 * Provides reservation-specific path functionality.
 */
class c_reservation_path extends c_base_path {
  protected $use_p_tags = NULL;
  protected $base_path  = NULL;
  protected $user_name  = NULL;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->use_p_tags = FALSE;
    $this->base_path  = '';
    $this->user_name  = '';
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->use_p_tags);
    unset($this->base_path);
    unset($this->user_name);

    parent::__destruct();
  }

  /**
   * Load any default settings.
   *
   * Very validation is performed.
   *
   * @param array $settings
   *   The array containing all of the settings to parse.
   */
  protected function pr_assign_defaults($settings) {
    if (isset($settings['standard_issue-use_p_tags']) && is_bool($settings['standard_issue-use_p_tags'])) {
      $this->use_p_tags = $settings['standard_issue-use_p_tags'];
    }

    if (isset($settings['base_path']) && is_string($settings['base_path'])) {
      $this->base_path = $settings['base_path'];
    }

    if (isset($settings['database_user']) && is_string($settings['database_user'])) {
      $this->user_name = $settings['database_user'];
    }
  }

  /**
   * Creates the standard wrapper.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_wrapper() {
    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, c_reservation_paths::CSS_BASE . c_reservation_paths::CSS_BASE . 'content-wrapper', array(c_reservation_paths::CSS_BASE . 'content-wrapper', 'content-wrapper'));
  }

  /**
   * Creates the standard break tag.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_break() {
    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BREAK);
  }

  /**
   * Creates the standard title.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_title($text, $arguments = array()) {
    if (is_int($text)) {
      return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1, NULL, array('as-title'), $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1, NULL, array('as-title'), $text);
  }

  /**
   * Creates the standard text.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text($text, $arguments = array()) {
    $type = c_base_markup_tag::TYPE_SPAN;
    if ($this->use_p_tags) {
      $type = c_base_markup_tag::TYPE_PARAGRAPH;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($type, NULL, array('as-text'), $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($type, NULL, array('as-text'), $text);
  }

  /**
   * Creates the standard paragraph.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph($text, $arguments = array()) {
    $type = c_base_markup_tag::TYPE_SPAN;
    if ($this->use_p_tags) {
      $type = c_base_markup_tag::TYPE_PARAGRAPH;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($type, NULL, array('as-paragraph'), $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($type, NULL, array('as-paragraph'), $text);
  }

  /**
   * Creates the standard text, wrapped in a block.
   *
   * @param int|string|null $text
   *   The text or the text code to use.
   *   If NULL, only the block is created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text_block($text, $arguments = array()) {
    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, array('as-text-block'));

    if (!is_null($text)) {
      $type = c_base_markup_tag::TYPE_SPAN;
      if ($this->use_p_tags) {
        $type = c_base_markup_tag::TYPE_PARAGRAPH;
      }

      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($type, NULL, array('as-text'), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($type, NULL, array('as-text'), $text);
      }
      unset($type);

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Creates the standard text, wrapped in a block.
   *
   * @param int|string|null $text
   *   The text or the text code to use.
   *   If NULL, only the block is created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph_block($text, $arguments = array()) {
    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, array('as-paragraph-block'));

    if (!is_null($text)) {
      $type = c_base_markup_tag::TYPE_SPAN;
      if ($this->use_p_tags) {
        $type = c_base_markup_tag::TYPE_PARAGRAPH;
      }

      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($type, NULL, array('as-paragraph'), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($type, NULL, array('as-paragraph'), $text);
      }
      unset($type);

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Load the title text associated with this page.
   *
   * This is provided here as a means for a language class to override with a custom language for the title.
   *
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return string|null
   *   A string is returned as the custom title.
   *   NULL is returned to enforce default title.
   */
  protected function pr_get_title($arguments = array()) {
    return NULL;
  }

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }

  /**
   * Create a new HTML markup class with default settings populated.
   *
   * @param c_base_http $http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database $database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   *
   * @return c_base_html
   *   The generated html is returned on success.
   *   The generated html with error bit set is returned on error.
   */
  protected function pr_create_html(&$http, &$database, &$session, $settings) {
    $title = $this->pr_get_title();

    $html = new c_base_html();


    // assign class attributes
    $class = array(
      'reservation',
      'javascript-disabled',
      'is-html5',
    );

    // add date/time classes.
    $instance = c_base_defaults_global::s_get_timestamp_session()->get_value_exact();
    $class[] = 'date-year-' . $html->sanitize_css(date('Y', $instance))->get_value_exact();
    $class[] = 'date-month-' . $html->sanitize_css(strtolower(date('F', $instance)))->get_value_exact();
    $class[] = 'date-week_day-' . $html->sanitize_css(strtolower(date('l', $instance)))->get_value_exact();
    $class[] = 'date-day-' . $html->sanitize_css(date('d', $instance))->get_value_exact();
    $class[] = 'time-hour-' . $html->sanitize_css(date('H', $instance))->get_value_exact();
    $class[] = 'time-minute-' . $html->sanitize_css(date('m', $instance))->get_value_exact();
    $class[] = 'time-second-' . $html->sanitize_css(date('s', $instance))->get_value_exact();
    unset($instance);

    // add path classes
    $path = $http->get_request_uri_relative($settings['base_path'])->get_value_exact();
    $path_parts = explode('/', $path);

    if (is_array($path_parts)) {
      $sanitized = NULL;
      $delta = 0;
      foreach ($path_parts as $path_part) {
        $sanitized_part = $html->sanitize_css($path_part, TRUE)->get_value_exact();
        $sanitized .= '-' . $sanitized_part;

        $class[] = 'path-part-' . $delta . '-' . $html->sanitize_css($sanitized_part)->get_value_exact();
        $delta++;
      }
      unset($path_part);
      unset($sanitized_part);

      $class[] = 'path-full-' . $html->sanitize_css(substr($sanitized, 1))->get_value_exact();
      unset($sanitized);
    }
    unset($path_parts);

    $html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_CLASS, $class);
    unset($class);


    // assign id attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_ID, 'reservation-system');
    $html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ID, 'reservation-system-body');


    // assign language attribute.
    $language = i_base_language::ENGLISH_US;
    $languages = $http->get_response_content_language()->get_value_exact();
    if (is_array($languages) && !empty($languages)) {
      $language = reset($languages);
    }

    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, $language);
    unset($language);


    // assign default direction attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION, 'ltr');


    // assign title header tag (setting title tag at delta 0 so that it can easily be overriden as needed).
    $tag = new c_base_markup_tag();
    $tag->set_type(c_base_markup_tag::TYPE_TITLE);

    if (is_string($title)) {
      $tag->set_text($title);
    }
    else {
      $tag->set_text('Reservation System');
    }

    $html->set_header($tag, 0);
    unset($tag);


    // assign base header tag
    if (isset($this->settings['base_path']) && is_string($this->settings['base_path']) && mb_strlen($this->settings['base_scheme']) > 0) {
      $href = '';
      if (isset($this->settings['base_scheme']) && is_string($this->settings['base_scheme']) && mb_strlen($this->settings['base_scheme']) > 0) {
        if (isset($this->settings['base_host']) && is_string($this->settings['base_host']) && mb_strlen($this->settings['base_host']) > 0) {
          $href .= $this->settings['base_scheme'] . '://' . $this->settings['base_host'];
        }
      }

      $href .= $this->settings['base_path'];

      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BASE);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $href);
      $html->set_header($tag);
      unset($tag);
      unset($href);
    }


    // assign http-equiv header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'Content-Type');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'text/html; charset=utf-8');
    $html->set_header($tag);
    unset($tag);


    // assign charset header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET, c_base_charset::UTF_8);
    $html->set_header($tag);
    unset($tag);


    // assign canonical header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, 'http://localhost/');
    #$html->set_header($tag);
    #unset($tag);


    // assign shortlink header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'shortlink');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, '/');
    #$html->set_header($tag);
    #unset($tag);


    // assign description header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'description');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'A reservation/scheduling system.');
    $html->set_header($tag);
    unset($tag);


    // assign distribution header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'distribution');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'web');
    $html->set_header($tag);
    unset($tag);


    // assign robots header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'robots');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'INDEX,FOLLOW');
    $html->set_header($tag);
    unset($tag);


    // assign expires header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'expires');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, c_base_defaults_global::s_get_date('r', strtotime('+30 minutes'))->get_value_exact());
    #$html->set_header($tag);
    #unset($tag);


    // assign viewport header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'viewport');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'width=device-width, initial-scale=1');
    $html->set_header($tag);
    unset($tag);


    // assign content http-equiv header tag
    $aliases = array();
    if (is_array($languages) && !empty($languages)) {
      // assign the primary language.
      $language_aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id(reset($languages))->get_value_exact();
      if (is_array($language_aliases) && !empty($language_aliases)) {
        $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, reset($language_aliases));
      }
      unset($language_aliases);

      foreach ($languages as $language) {
        $language_aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id($language)->get_value_exact();
        if (is_array($language_aliases) && !empty($language_aliases)) {
          $aliases[] = array_pop($language_aliases);
        }
        unset($language_aliases);
      }
      unset($language);
    }
    unset($languages);

    if (!empty($aliases)) {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'content-language');
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, implode(', ', $aliases));
      $html->set_header($tag);
      unset($tag);
    }
    unset($aliases);


    // provide a custom javascript for detecting if javascript is enabled and storing in a css class name.
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SCRIPT);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE, c_base_mime::TYPE_TEXT_JS);

    $javascript = 'function f_reservation_hmtl_javascript_detection() {';
    $javascript .= 'document.body.removeAttribute(\'onLoad\');';
    $javascript .= 'document.body.className = document.body.className.replace(/\bjavascript-disabled\b/i, \'javascript-enabled\');';
    $javascript .= '}';
    $tag->set_text($javascript);
    unset($javascript);

    $html->set_header($tag);
    $html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ON_LOAD, 'f_reservation_hmtl_javascript_detection();');
    unset($tag);

    return $html;
  }

}
