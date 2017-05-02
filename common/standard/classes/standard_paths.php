<?php
/**
 * @file
 * Provides the standard site index class.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_paths.php');

require_once('common/standard/classes/standard_path.php');

/**
 * The standard class for use in index.php or equivalent.
 */
class c_standard_paths extends c_base_return {
  protected const PATH_LOGIN                = 'common/standard/paths/u/';
  protected const PATH_LOGOUT               = 'common/standard/paths/u/';
  protected const PATH_ACCESS_DENIED        = 'common/standard/internal/';
  protected const PATH_NOT_FOUND            = 'common/standard/internal/';
  protected const PATH_BAD_METHOD           = 'common/standard/internal/';
  protected const PATH_SERVER_ERROR         = 'common/standard/internal/';
  protected const PATH_OPTIONS_METHOD       = 'common/standard/internal/';
  protected const PATH_DASHBOARD_USER       = 'common/standard/paths/u/';
  protected const PATH_DASHBOARD_MANAGEMENT = 'common/standard/paths/m/';
  protected const PATH_DASHBOARD_ADMINISTER = 'common/standard/paths/a/';
  protected const PATH_INDEX                = 'common/standard/internal/';

  protected const NAME_LOGIN                = 'login';
  protected const NAME_LOGOUT               = 'logout';
  protected const NAME_ACCESS_DENIED        = 'access_denied';
  protected const NAME_NOT_FOUND            = 'not_found';
  protected const NAME_BAD_METHOD           = 'bad_method';
  protected const NAME_SERVER_ERROR         = 'server_error';
  protected const NAME_OPTIONS_METHOD       = 'options';
  protected const NAME_DASHBOARD_USER       = 'dashboard';
  protected const NAME_DASHBOARD_MANAGEMENT = 'dashboard';
  protected const NAME_DASHBOARD_ADMINISTER = 'dashboard';
  protected const NAME_INDEX                = 'index';

  protected const HANDLER_LOGIN                = 'c_standard_path_user_login';
  protected const HANDLER_LOGOUT               = 'c_standard_path_user_logout';
  protected const HANDLER_NOT_FOUND            = 'c_standard_path_not_found';
  protected const HANDLER_ACCESS_DENIED        = 'c_standard_path_access_denied';
  protected const HANDLER_BAD_METHOD           = 'c_standard_path_bad_method';
  protected const HANDLER_SERVER_ERROR         = 'c_standard_path_server_error';
  protected const HANDLER_OPTIONS_METHOD       = 'c_standard_path_options_method';
  protected const HANDLER_USER_DASHBOARD       = 'c_standard_path_user_dashboard';
  protected const HANDLER_MANAGEMENT_DASHBOARD = 'c_standard_path_management_dashboard';
  protected const HANDLER_ADMINISTER_DASHBOARD = 'c_standard_path_administer_dashboard';
  protected const HANDLER_INDEX                = 'c_standard_path_index';

  protected const URI_LOGIN                = '/u/login';
  protected const URI_LOGOUT               = '/u/logout';
  protected const URI_DASHBOARD_USER       = '/u/dashboard';
  protected const URI_DASHBOARD_MANAGEMENT = '/m/dashboard';
  protected const URI_DASHBOARD_ADMINISTER = '/a/dashboard';

  protected const SCRIPT_EXTENSION = '.php';

  // a class name to prepend to css classes or id attributes.
  protected const CSS_BASE = 'standard-';

  protected $handler;
  protected $paths;

  protected $http;
  protected $database;
  protected $session;
  protected $settings;

  protected $alias;

  protected $output;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->handler = NULL;
    $this->paths   = array();

    $this->http     = NULL;
    $this->database = NULL;
    $this->session  = NULL;
    $this->settings = NULL;

    $this->alias = NULL;

    $this->output = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->handler);
    unset($this->paths);

    unset($this->http);
    unset($this->database);
    unset($this->session);
    unset($this->settings);

    unset($this->alias);

    unset($this->output);

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
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Load and return the login handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_login() {
    return $this->pr_include_path(self::PATH_LOGIN, self::NAME_LOGIN, self::HANDLER_LOGIN);
  }

  /**
   * Load and return the logout handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_logout() {
    return $this->pr_include_path(self::PATH_LOGOUT, self::NAME_LOGOUT, self::HANDLER_LOGOUT);
  }

  /**
   * Load and return the not found handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_not_found() {
    return $this->pr_include_path(self::PATH_NOT_FOUND, self::NAME_NOT_FOUND, self::HANDLER_NOT_FOUND);
  }

  /**
   * Load and return the access denied handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_access_denied() {
    return $this->pr_include_path(self::PATH_ACCESS_DENIED, self::NAME_ACCESS_DENIED, self::HANDLER_ACCESS_DENIED);
  }

  /**
   * Load and return the not found handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_bad_method() {
    return $this->pr_include_path(self::PATH_BAD_METHOD, self::NAME_BAD_METHOD, self::HANDLER_BAD_METHOD);
  }

  /**
   * Load and return the internal server error handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_server_error() {
    return $this->pr_include_path(self::PATH_SERVER_ERROR, self::NAME_SERVER_ERROR, self::HANDLER_SERVER_ERROR);
  }

  /**
   * Load and return the options method handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_options_method() {
    return $this->pr_include_path(self::PATH_OPTIONS_METHOD, self::NAME_OPTIONS_METHOD, self::HANDLER_OPTIONS_METHOD);
  }

  /**
   * Load and return the index handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_index() {
    return $this->pr_include_path(self::PATH_INDEX, self::NAME_INDEX, self::HANDLER_INDEX);
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
  public function do_process_path(&$http, &$database, &$session, $settings) {
    // @todo: these parameter errors might need a custom service unavailable and system log support.
    if (!($http instanceof c_base_http)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'http', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'settings', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    if (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    $this->http = &$http;
    $this->database = &$database;
    $this->session = &$session;
    $this->settings = $settings;

    $this->output = NULL;

    $this->pr_get_language_alias();

    // require HTTPS for access to any part of this website.
    if (!isset($_SERVER["HTTPS"])) {
      // @todo: redirect to https version of requested uri.
      $failure_path = $this->get_handler_not_found();

      return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    // load all available paths.
    $this->pr_paths_create();


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

        $failure_path = $this->get_handler_bad_method();

        return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      unset($method);

      $not_found = $this->get_handler_not_found();
      return $not_found->do_execute($this->http, $this->database, $this->session, $this->settings);
    }

    // validate allowed methods.
    if (isset($handler_settings['methods']) && is_array($handler_settings['methods'])) {
      if (!array_key_exists($method, $handler_settings['methods'])) {
        unset($method);

        $failure_path = $this->get_handler_bad_method();

        return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
    }


    // HTTP OPTIONS method does not process the page, only returns available methods.
    if ($method === c_base_http::HTTP_METHOD_OPTIONS) {
      unset($method);

      $options_method_path = $this->get_handler_options_method();

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
      if (!is_string($handler_settings['redirect'])) {
        $handler_settings['redirect'] = '';
      }

      if (!isset($handler_settings['code']) || !is_int($handler_settings['code'])) {
        $handler_settings['code'] = c_base_http_status::MOVED_PERMANENTLY;
      }

      $redirect = c_standard_path::s_create_redirect($handler_settings['redirect'], $handler_settings['code'], FALSE);
      return $redirect->do_execute($this->http, $this->database, $this->session, $this->settings);
    }
    else {
      if (!empty($handler_settings['include_name']) && is_string($handler_settings['include_name'])) {
        require_once($handler_settings['include_directory'] . $handler_settings['include_name'] . self::SCRIPT_EXTENSION);
      }

      // execute path handler, using custom-language if defined.
      if (empty($handler_settings['handler'])) {
        return $this->get_handler_server_error()->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      elseif (is_string($this->alias)) {
        @include_once($handler_settings['include_directory'] . $this->alias . '/' . $handler_settings['include_name']);

        $handler_class = $handler_settings['handler'] . '_' . $this->alias;
        if (class_exists($handler_class)) {
          $this->handler = new $handler_class();

          unset($handler_class);
        }
        else {
          unset($handler_class);

          // attempt to fallback to default handler if the language-specific handler class is not found.
          if (!class_exists($handler_settings['handler'])) {
            return $this->get_handler_server_error()->do_execute($this->http, $this->database, $this->session, $this->settings);
          }
          else {
            $this->handler = new $handler_settings['handler']();
          }
        }
      }
      else {
        if (class_exists($handler_settings['handler'])) {
          $this->handler = new $handler_settings['handler']();
        }
        else {
          return $this->get_handler_server_error()->do_execute($this->http, $this->database, $this->session, $this->settings);
        }
      }

      if (isset($handler_settings['is_root']) && $handler_settings['is_root']) {
        $this->handler->is_root(TRUE);
      }

      if (isset($handler_settings['id_group'])) {
        $this->handler->set_id_group($handler_settings['id_group']);
      }
    }
    unset($handler_settings);


    return $this->pr_paths_normal($method);
  }

  /**
   * Creates and returns a list of all available paths.
   *
   * Add/modify paths here as desired.
   *
   * @return c_base_paths
   *   The generated paths object.
   */
  protected function pr_paths_create() {
    $this->paths = new c_base_paths();

    // set root path to be the user dashboard.
    $this->paths->add_path('', self::HANDLER_INDEX, self::PATH_INDEX, self::NAME_INDEX);

    // create login/logout paths
    $this->paths->add_path(self::URI_LOGIN, self::HANDLER_LOGIN, self::PATH_LOGIN, self::NAME_LOGIN);
    $this->paths->add_path(self::URI_LOGOUT, self::HANDLER_LOGOUT, self::PATH_LOGOUT, self::NAME_LOGOUT);

    // dashboards
    $this->paths->add_path(self::URI_DASHBOARD_USER, self::HANDLER_USER_DASHBOARD, self::PATH_DASHBOARD_USER, self::NAME_DASHBOARD_USER);
    $this->paths->add_path(self::URI_DASHBOARD_MANAGEMENT, self::HANDLER_MANAGEMENT_DASHBOARD, self::PATH_DASHBOARD_MANAGEMENT, self::NAME_DASHBOARD_MANAGEMENT);
    $this->paths->add_path(self::URI_DASHBOARD_ADMINISTER, self::HANDLER_ADMINISTER_DASHBOARD, self::PATH_DASHBOARD_ADMINISTER, self::NAME_DASHBOARD_ADMINISTER);
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
  protected function pr_paths_normal($method) {
    $id_group = $this->handler->get_id_group()->get_value_exact();

    // regardless of path-specific settings, the following paths always require login credentials to access.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M || $id_group === c_base_ascii::LOWER_U) {
      $this->handler->is_private(TRUE);
    }


    if (class_exists('c_standard_path_user_login') && $this->handler instanceof c_standard_path_user_login) {
      unset($id_group);
      return $this->handler->do_execute($this->http, $this->database, $this->session, $this->settings);
    }
    elseif (class_exists('c_standard_path_user_logout') && $this->handler instanceof c_standard_path_user_logout) {
      // if the user is not logged in. then provide a page not found for logout path.
      if (!$this->session->is_logged_in()->get_value_exact()) {
        unset($id_group);
        return $this->get_handler_not_found()->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
    }


    // if the request is private, make sure the user is logged in.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M || $id_group === c_base_ascii::LOWER_U || $this->handler->is_private()->get_value_exact()) {
      if ($this->session->is_logged_in()->get_value_exact()) {
        unset($id_group);
        return $this->handler->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      elseif ($this->handler->is_root()->get_value_exact()) {
        unset($id_group);

        $this->http->set_response_status(c_base_http_status::FORBIDDEN);

        $login_path = $this->get_handler_login();
        return $login_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      else {
        // some special case paths always provide login prompt along with access denied.
        if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M || $id_group === c_base_ascii::LOWER_U) {
          unset($id_group);

          $this->http->set_response_status(c_base_http_status::FORBIDDEN);

          $login_path = $this->get_handler_login();
          return $login_path->do_execute($this->http, $this->database, $this->session, $this->settings);
        }
      }
    }
    else {
      unset($id_group);
      return $this->handler->do_execute($this->http, $this->database, $this->session, $this->settings);
    }

    // return access denied or page not found depending on path and privacy settings.
    if ($id_group === c_base_ascii::LOWER_C || $id_group === c_base_ascii::LOWER_D || $id_group === c_base_ascii::LOWER_T || $id_group === c_base_ascii::LOWER_X || $id_group === c_base_ascii::LOWER_F) {
      // these always return not found for these paths.
      $failsafe_path = $this->get_handler_not_found();
    }
    elseif ($this->handler->is_private()->get_value_exact() && $id_group !== c_base_ascii::NULL) {
      // non private, and non-special case paths should return access denied as per normal behavior.
      $failsafe_path = $this->get_handler_access_denied();
    }
    else {
      // all other case, return not found.
      $failsafe_path = $this->get_handler_not_found();
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
  protected function pr_paths_forms() {
    // @todo

    // always return not found, do not inform user if the access is denied.
    $failsafe_path = $this->get_handler_not_found();

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Process request path ajax paths and determine what to do.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  protected function pr_paths_ajax() {
    // @todo

    // always return not found, do not inform user if the access is denied.
    $failsafe_path = $this->get_handler_not_found();

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Load and save the current preferred language alias.
   *
   * This will be stored in $this->alias.
   */
  protected function pr_get_language_alias() {
    $aliases = array();
    $languages = $this->http->get_response_content_language()->get_value_exact();
    if (is_array($languages) && !empty($languages)) {
      $language = reset($languages);

      // us-english is the default, so do not attempt to include any external files.
      if ($language === i_base_languages::ENGLISH_US || $language === i_base_languages::ENGLISH) {
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
  protected function pr_include_path($path, $name, $class) {
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