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
  const URI_HOME                 = '';
  const URI_DASHBOARD_MANAGEMENT = 'm/dashboard';
  const URI_DASHBOARD_ADMINISTER = 'a/dashboard';
  const URI_USER_CREATE          = 'u/create';
  const URI_USER_DASHBOARD       = 'u/dashboard';
  const URI_USER_VIEW            = 'u/view';
  const URI_USER_EDIT            = 'u/edit';
  const URI_USER_LOCK            = 'u/lock';
  const URI_USER_LOGIN           = 'u/login';
  const URI_USER_LOGOUT          = 'u/logout';
  const URI_USER_UNLOCK          = 'u/unlock';
  const URI_USER_DELETE          = 'u/create';
  const URI_USER_CHECK           = 'u/check';
  const URI_USER_REFRESH         = 'u/refresh';
  const URI_USER_PRINT           = 'u/print';
  const URI_USER_PDF             = 'u/pdf';
  const URI_USER_PS              = 'u/ps';

  protected const PATH_INTERNAL   = 'common/standard/internal/';
  protected const PATH_USER       = 'common/standard/paths/u/';
  protected const PATH_MANAGEMENT = 'common/standard/paths/m/';
  protected const PATH_ADMINISTER = 'common/standard/paths/a/';

  protected const NAME_ACCESS_DENIED        = 'access_denied';
  protected const NAME_NOT_FOUND            = 'not_found';
  protected const NAME_BAD_METHOD           = 'bad_method';
  protected const NAME_SERVER_ERROR         = 'server_error';
  protected const NAME_OPTIONS_METHOD       = 'options';
  protected const NAME_DASHBOARD_MANAGEMENT = 'dashboard';
  protected const NAME_DASHBOARD_ADMINISTER = 'dashboard';
  protected const NAME_INDEX                = 'index';
  protected const NAME_USER_CREATE          = 'user_create';
  protected const NAME_USER_DASHBOARD       = 'user_dashboard';
  protected const NAME_USER_VIEW            = 'user_view';
  protected const NAME_USER_EDIT            = 'user_edit';
  protected const NAME_USER_LOCK            = 'user_lock';
  protected const NAME_USER_LOGIN           = 'user_login';
  protected const NAME_USER_LOGOUT          = 'user_logout';
  protected const NAME_USER_UNLOCK          = 'user_unlock';
  protected const NAME_USER_DELETE          = 'user_delete';
  protected const NAME_USER_PRINT           = 'user_print';
  protected const NAME_USER_PDF             = 'user_pdf';
  protected const NAME_USER_PS              = 'user_ps';

  protected const HANDLER_NOT_FOUND            = 'c_standard_path_not_found';
  protected const HANDLER_ACCESS_DENIED        = 'c_standard_path_access_denied';
  protected const HANDLER_BAD_METHOD           = 'c_standard_path_bad_method';
  protected const HANDLER_SERVER_ERROR         = 'c_standard_path_server_error';
  protected const HANDLER_OPTIONS_METHOD       = 'c_standard_path_options_method';
  protected const HANDLER_MANAGEMENT_DASHBOARD = 'c_standard_path_management_dashboard';
  protected const HANDLER_ADMINISTER_DASHBOARD = 'c_standard_path_administer_dashboard';
  protected const HANDLER_INDEX                = 'c_standard_path_index';
  protected const HANDLER_USER_CREATE          = 'c_standard_path_user_create';
  protected const HANDLER_USER_DASHBOARD       = 'c_standard_path_user_dashboard';
  protected const HANDLER_USER_VIEW            = 'c_standard_path_user_view';
  protected const HANDLER_USER_EDIT            = 'c_standard_path_user_edit';
  protected const HANDLER_USER_LOCK            = 'c_standard_path_user_lock';
  protected const HANDLER_USER_LOGIN           = 'c_standard_path_user_login';
  protected const HANDLER_USER_LOGOUT          = 'c_standard_path_user_logout';
  protected const HANDLER_USER_UNLOCK          = 'c_standard_path_user_unlock';
  protected const HANDLER_USER_DELETE          = 'c_standard_path_user_delete';
  protected const HANDLER_USER_PRINT           = 'c_standard_path_user_print';
  protected const HANDLER_USER_PDF             = 'c_standard_path_user_pdf';
  protected const HANDLER_USER_PS              = 'c_standard_path_user_ps';

  protected const SCRIPT_EXTENSION = '.php';
  protected const WILDCARD_PATH    = '/%';

  protected $handler;
  protected $paths;

  protected $http;
  protected $database;
  protected $session;
  protected $settings;

  protected $language_alias;

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

    $this->language_alias = NULL;

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

    unset($this->language_alias);

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
    return $this->pr_include_path(self::PATH_USER, self::NAME_USER_LOGIN, self::HANDLER_USER_LOGIN);
  }

  /**
   * Load and return the logout handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_logout() {
    return $this->pr_include_path(self::PATH_USER, self::NAME_USER_LOGOUT, self::HANDLER_USER_LOGOUT);
  }

  /**
   * Load and return the not found handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_not_found() {
    return $this->pr_include_path(self::PATH_INTERNAL, self::NAME_NOT_FOUND, self::HANDLER_NOT_FOUND);
  }

  /**
   * Load and return the access denied handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_access_denied() {
    return $this->pr_include_path(self::PATH_INTERNAL, self::NAME_ACCESS_DENIED, self::HANDLER_ACCESS_DENIED);
  }

  /**
   * Load and return the not found handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_bad_method() {
    return $this->pr_include_path(self::PATH_INTERNAL, self::NAME_BAD_METHOD, self::HANDLER_BAD_METHOD);
  }

  /**
   * Load and return the internal server error handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_server_error() {
    return $this->pr_include_path(self::PATH_INTERNAL, self::NAME_SERVER_ERROR, self::HANDLER_SERVER_ERROR);
  }

  /**
   * Load and return the options method handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_options_method() {
    return $this->pr_include_path(self::PATH_INTERNAL, self::NAME_OPTIONS_METHOD, self::HANDLER_OPTIONS_METHOD);
  }

  /**
   * Load and return the index handler.
   *
   * @return c_base_path
   *   A path object.
   */
  public function get_handler_index() {
    return $this->pr_include_path(self::PATH_INTERNAL, self::NAME_INDEX, self::HANDLER_INDEX);
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
      return c_base_return_error::s_return('c_base_path_executed', $error);
    }

    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_return('c_base_path_executed', $error);
    }

    if (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_return('c_base_path_executed', $error);
    }

    if (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'settings', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_return('c_base_path_executed', $error);
    }

    if (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_return('c_base_path_executed', $error);
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
      $path_not_found = $this->get_handler_not_found();

      $path_tree = new c_base_path_tree();
      $path_tree->set_id_group(0);
      $path_tree->set_items(array());

      $path_not_found->set_path_tree($path_tree);
      unset($path_tree);

      return $path_not_found->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    // load always available paths.
    $this->pr_paths_create();


    // load the remaining paths based on the relative path to avoid generating and processing unnecessary paths.
    $path = $this->http->get_request_uri_relative($settings['base_path'])->get_value_exact();

    $id_group = 0;
    $path_object = new c_base_path();
    if ($path_object->set_value($path)) {
      $sanitized = $path_object->get_value_exact();
      unset($path_object);

      $path_parts = explode('/', $sanitized);
      unset($sanitized);

      if (mb_strlen($path_parts[0]) == 1) {
        $ordinal = ord($path_parts[0]);
        if (in_array($ordinal, c_base_defaults_global::RESERVED_PATH_GROUP)) {
          $id_group = $ordinal;
        }
        unset($ordinal);
      }
      unset($path_parts);
    }

    if ($id_group === c_base_ascii::LOWER_A) {
      $this->pr_paths_create_administer();
    }
    elseif ($id_group === c_base_ascii::LOWER_C) {
      $this->pr_paths_create_cache();
    }
    elseif ($id_group === c_base_ascii::LOWER_D) {
      $this->pr_paths_create_data();
    }
    elseif ($id_group === c_base_ascii::LOWER_F) {
      $this->pr_paths_create_file();
    }
    elseif ($id_group === c_base_ascii::LOWER_M) {
      $this->pr_paths_create_management();
    }
    elseif ($id_group === c_base_ascii::LOWER_S) {
      $this->pr_paths_create_submit();
    }
    elseif ($id_group === c_base_ascii::LOWER_T) {
      $this->pr_paths_create_theme();
    }
    elseif ($id_group === c_base_ascii::LOWER_U) {
      $this->pr_paths_create_user();
    }
    elseif ($id_group === c_base_ascii::LOWER_X) {
      $this->pr_paths_create_ajax();
    }
    else {
      $this->pr_paths_create_ungrouped();
    }
    unset($id_group);


    // load the http method.
    $method = $this->http->get_request(c_base_http::REQUEST_METHOD)->get_value_exact();
    if (isset($method['data']) && is_int($method['data'])) {
      $method = $method['data'];
    }
    else {
      $method = c_base_http::HTTP_METHOD_NONE;
    }


    // find the path
    $handler_settings = $this->paths->find_path($path)->get_value();
    unset($path);

    if (!isset($handler_settings['handler'])) {
      if ($method !== c_base_http::HTTP_METHOD_GET && $method !== c_base_http::HTTP_METHOD_POST) {
        // for all invalid pages, report bad method if not HTTP GET or HTTP POST.
        $path_failsafe = $this->get_handler_bad_method();
      }
      else {
        $path_failsafe = $this->get_handler_not_found();
      }
      unset($method);

      $path_tree = new c_base_path_tree();
      $path_tree->set_id_group(0);
      if (isset($handler_settings['path_tree'])) {
        $path_tree->set_items($handler_settings['path_tree']);

        if (isset($handler_settings['id_group'])) {
          $path_tree->set_id_group($handler_settings['id_group']);
        }
      }
      else {
        $path_tree->set_items(array());

        $handler_settings_index = $this->paths->find_path('')->get_value();
        if (isset($handler_settings_index['handler'])) {
          $path_tree->set_item_append($handler_settings_index);
        }
        unset($handler_settings_index);
      }

      $path_failsafe->set_path_tree($path_tree);
      unset($path_tree);

      return $path_failsafe->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    // prepare the path tree object.
    $path_tree = new c_base_path_tree();
    if (isset($handler_settings['path_tree'])) {
      $path_tree->set_items($handler_settings['path_tree']);
    }
    else {
      $path_tree->set_items(array());
    }

    if (isset($handler_settings['id_group'])) {
      $path_tree->set_id_group($handler_settings['id_group']);
    }
    else {
      $path_tree->set_id_group(0);
    }


    // validate allowed methods.
    if (isset($handler_settings['methods']) && is_array($handler_settings['methods'])) {
      if (!array_key_exists($method, $handler_settings['methods'])) {
        unset($method);

        $path_bad_method = $this->get_handler_bad_method();
        $path_bad_method->set_path_tree($path_tree);

        return $path_bad_method->do_execute($this->http, $this->database, $this->session, $this->settings);
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

      $options_method_path->set_path_tree($path_tree);

      unset($handler_settings);
      unset($path_tree);

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


      $redirect_path = $handler_settings['redirect'];
      if (isset($handler_settings['redirect_partial']) && $handler_settings['redirect_partial']) {
        if (isset($handler_settings['extra_slashes']) && $handler_settings['extra_slashes']) {
          $path_original = $this->http->get_request_uri_relative('')->get_value_exact();
          $path_modified = $this->http->get_request_uri_relative($settings['base_path'])->get_value_exact();

          // if path orignal and modified are the same, then the provided base url has extra '/' in it.
          if ($path_original == $path_modified) {
            $redirect_path = preg_replace('@^' . preg_quote($this->settings['base_path'], '@') . '@i', '', '/' . $redirect_path);
            $redirect_path = preg_replace('@/$@', '', $redirect_path);
          }
          unset($path_original);
          unset($path_modified);
        }

        $redirect_path = $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . $redirect_path;
      }

      $redirect = c_standard_path::s_create_redirect($redirect_path, $handler_settings['code'], FALSE);
      unset($redirect_path);

      return $redirect->do_execute($this->http, $this->database, $this->session, $this->settings);
    }
    else {
      if (!empty($handler_settings['include_name']) && is_string($handler_settings['include_name'])) {
        require_once($handler_settings['include_directory'] . $handler_settings['include_name'] . self::SCRIPT_EXTENSION);
      }

      // execute path handler, using custom-language if defined.
      if (empty($handler_settings['handler'])) {
        $handler_server_error = $this->get_handler_server_error();
        $handler_server_error->set_path_tree($path_tree);
        $path_server_error = $handler_server_error->do_execute($this->http, $this->database, $this->session, $this->settings);

        unset($handler_server_error);
        unset($handler_settings);
        unset($path_tree);

        return $path_server_error;
      }
      elseif (is_string($this->language_alias)) {
        @include_once($handler_settings['include_directory'] . $this->language_alias . '/' . $handler_settings['include_name'] . self::SCRIPT_EXTENSION);

        $handler_class = $handler_settings['handler'] . '_' . $this->language_alias;
        if (class_exists($handler_class)) {
          $this->handler = new $handler_class();

          unset($handler_class);
        }
        else {
          unset($handler_class);

          // attempt to fallback to default handler if the language-specific handler class is not found.
          if (!class_exists($handler_settings['handler'])) {
            $handler_server_error = $this->get_handler_server_error();
            $handler_server_error->set_path_tree($path_tree);
            $path_server_error = $handler_server_error->do_execute($this->http, $this->database, $this->session, $this->settings);

            unset($handler_server_error);
            unset($handler_settings);
            unset($path_tree);

            return $path_server_error;
          }
          else {
            $this->handler = new $handler_settings['handler']();
            $this->handler->set_path_tree($path_tree);
          }
        }
      }
      else {
        if (class_exists($handler_settings['handler'])) {
          $this->handler = new $handler_settings['handler']();
          $this->handler->set_path_tree($path_tree);
        }
        else {
          $handler_server_error = $this->get_handler_server_error();
          $handler_server_error->set_path_tree($path_tree);
          $path_server_error = $handler_server_error->do_execute($this->http, $this->database, $this->session, $this->settings);

          unset($handler_server_error);
          unset($handler_settings);
          unset($path_tree);

          return $path_server_error;
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
    unset($path_tree);


    return $this->pr_paths_normal($method);
  }

  /**
   * Creates a list of always available paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create() {
    $this->paths = new c_base_paths();

    // set root path.
    $this->paths->add_path(self::URI_HOME, self::HANDLER_INDEX, self::PATH_INTERNAL, self::NAME_INDEX);

    // create login/logout paths
    $this->paths->add_path(self::URI_USER_LOGIN, self::HANDLER_USER_LOGIN, self::PATH_USER, self::NAME_USER_LOGIN);
    $this->paths->add_path(self::URI_USER_LOGOUT, self::HANDLER_USER_LOGOUT, self::PATH_USER, self::NAME_USER_LOGOUT);
  }

  /**
   * Creates a list of available administer paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_administer() {
    // dashboards
    $this->paths->add_path(self::URI_DASHBOARD_ADMINISTER, self::HANDLER_ADMINISTER_DASHBOARD, self::PATH_ADMINISTER, self::NAME_DASHBOARD_ADMINISTER);
  }

  /**
   * Creates a list of available cache paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_cache() {
  }

  /**
   * Creates a list of available data paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_data() {
  }

  /**
   * Creates a list of available file paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_file() {
  }

  /**
   * Creates a list of available submit paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_submit() {
  }

  /**
   * Creates a list of available management paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_management() {
    // dashboards
    $this->paths->add_path(self::URI_DASHBOARD_MANAGEMENT, self::HANDLER_MANAGEMENT_DASHBOARD, self::PATH_MANAGEMENT, self::NAME_DASHBOARD_MANAGEMENT);
  }

  /**
   * Creates a list of available theme paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_theme() {
  }

  /**
   * Creates a list of available ajax paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_ajax() {
  }

  /**
   * Creates a list of available user paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_user() {
    // dashboards
    $this->paths->add_path(self::URI_USER_DASHBOARD, self::HANDLER_USER_DASHBOARD, self::PATH_USER, self::NAME_USER_DASHBOARD);

    // user paths
    $this->paths->add_path(self::URI_USER_CREATE, self::HANDLER_USER_CREATE, self::PATH_USER, self::NAME_USER_CREATE);
    $this->paths->add_path(self::URI_USER_CREATE . self::WILDCARD_PATH, self::HANDLER_USER_CREATE, self::PATH_USER, self::NAME_USER_CREATE);
    $this->paths->add_path(self::URI_USER_VIEW, self::HANDLER_USER_VIEW, self::PATH_USER, self::NAME_USER_VIEW);
    $this->paths->add_path(self::URI_USER_VIEW . self::WILDCARD_PATH, self::HANDLER_USER_VIEW, self::PATH_USER, self::NAME_USER_VIEW);
    $this->paths->add_path(self::URI_USER_EDIT, self::HANDLER_USER_EDIT, self::PATH_USER, self::NAME_USER_EDIT);
    $this->paths->add_path(self::URI_USER_EDIT . self::WILDCARD_PATH, self::HANDLER_USER_EDIT, self::PATH_USER, self::NAME_USER_EDIT);
    $this->paths->add_path(self::URI_USER_LOCK, self::HANDLER_USER_LOCK, self::PATH_USER, self::NAME_USER_LOCK);
    $this->paths->add_path(self::URI_USER_LOCK . self::WILDCARD_PATH, self::HANDLER_USER_LOCK, self::PATH_USER, self::NAME_USER_LOCK);
    $this->paths->add_path(self::URI_USER_UNLOCK, self::HANDLER_USER_UNLOCK, self::PATH_USER, self::NAME_USER_UNLOCK);
    $this->paths->add_path(self::URI_USER_UNLOCK . self::WILDCARD_PATH, self::HANDLER_USER_UNLOCK, self::PATH_USER, self::NAME_USER_UNLOCK);
    $this->paths->add_path(self::URI_USER_DELETE, self::HANDLER_USER_DELETE, self::PATH_USER, self::NAME_USER_DELETE);
    $this->paths->add_path(self::URI_USER_DELETE . self::WILDCARD_PATH, self::HANDLER_USER_DELETE, self::PATH_USER, self::NAME_USER_DELETE);
    $this->paths->add_path(self::URI_USER_PRINT, self::HANDLER_USER_PRINT, self::PATH_USER, self::NAME_USER_PRINT);
    $this->paths->add_path(self::URI_USER_PRINT . self::WILDCARD_PATH, self::HANDLER_USER_PRINT, self::PATH_USER, self::NAME_USER_PRINT);
    $this->paths->add_path(self::URI_USER_PDF, self::HANDLER_USER_PDF, self::PATH_USER, self::NAME_USER_PDF);
    $this->paths->add_path(self::URI_USER_PDF . self::WILDCARD_PATH, self::HANDLER_USER_PDF, self::PATH_USER, self::NAME_USER_PDF);
    $this->paths->add_path(self::URI_USER_PS, self::HANDLER_USER_PS, self::PATH_USER, self::NAME_USER_PS);
    $this->paths->add_path(self::URI_USER_PS . self::WILDCARD_PATH, self::HANDLER_USER_PS, self::PATH_USER, self::NAME_USER_PS);
  }

  /**
   * Creates a list of available paths that are not assigned to any groups.
   *
   * These are generally user-defined paths.
   *
   * Add/modify paths here as desired.
   */
  protected function pr_paths_create_ungrouped() {
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


    // if the request is private, make sure the user is logged in.
    if ($this->handler->is_private()->get_value_exact()) {
      if ($this->session->is_logged_in()->get_value_exact()) {
        unset($id_group);
        return $this->p_handle_execution_errors($this->handler->do_execute($this->http, $this->database, $this->session, $this->settings));
      }
      elseif ($this->handler->is_root()->get_value_exact()) {
        unset($id_group);

        $this->http->set_response_status(c_base_http_status::FORBIDDEN);

        $path_login = $this->get_handler_login();
        if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
          $path_login->set_path_tree($this->handler->get_path_tree());
        }

        return $this->p_handle_execution_errors($path_login->do_execute($this->http, $this->database, $this->session, $this->settings));
      }
      else {
        if ($id_group === c_base_ascii::LOWER_U) {
          unset($id_group);

          // PHP's instanceof does not support strings, so is_subclass_of() and is_a() must instead be used.
          if (class_exists(self::HANDLER_USER_LOGOUT) && (is_subclass_of($this->handler, self::HANDLER_USER_LOGOUT) || is_a($this->handler, self::HANDLER_USER_LOGOUT, TRUE))) {
            // if the user is not logged in. then provide a page not found for logout path.
            if (!$this->session->is_logged_in()->get_value_exact()) {
              $path_not_found = $this->get_handler_not_found();
              if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
                $path_not_found->set_path_tree($this->handler->get_path_tree());
              }

              return $path_not_found->do_execute($this->http, $this->database, $this->session, $this->settings);;
            }
          }

          $this->http->set_response_status(c_base_http_status::FORBIDDEN);

          $path_login = $this->get_handler_login();
          if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
            $path_login->set_path_tree($this->handler->get_path_tree());
          }

          return $this->p_handle_execution_errors($path_login->do_execute($this->http, $this->database, $this->session, $this->settings));
        }

        // some special case paths always provide login prompt along with access denied.
        if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_M) {
          unset($id_group);

          $this->http->set_response_status(c_base_http_status::FORBIDDEN);

          $path_login = $this->get_handler_login();
          if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
            $path_login->set_path_tree($this->handler->get_path_tree());
          }

          return $this->p_handle_execution_errors($path_login->do_execute($this->http, $this->database, $this->session, $this->settings));
        }
      }
    }
    else {
      unset($id_group);
      return $this->p_handle_execution_errors($this->handler->do_execute($this->http, $this->database, $this->session, $this->settings));
    }

    // return access denied or page not found depending on path and privacy settings.
    if ($id_group === c_base_ascii::LOWER_C || $id_group === c_base_ascii::LOWER_D || $id_group === c_base_ascii::LOWER_T || $id_group === c_base_ascii::LOWER_X || $id_group === c_base_ascii::LOWER_F) {
      // these always return not found for these paths.
      $path_failsafe = $this->get_handler_not_found();
    }
    elseif ($this->handler->is_private()->get_value_exact() && $id_group !== c_base_ascii::NULL) {
      // non private, and non-special case paths should return access denied as per normal behavior.
      $path_failsafe = $this->get_handler_access_denied();
    }
    else {
      // all other case, return not found.
      $path_failsafe = $this->get_handler_not_found();
    }
    unset($id_group);

    if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
      $path_failsafe->set_path_tree($this->handler->get_path_tree());
    }

    return $path_failsafe->do_execute($this->http, $this->database, $this->session, $this->settings);
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
    $path_failsafe = $this->get_handler_not_found();

    if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
      $path_failsafe->set_path_tree($this->handler->get_path_tree());
    }

    return $path_failsafe->do_execute($this->http, $this->database, $this->session, $this->settings);
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
    $path_failsafe = $this->get_handler_not_found();

    if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
      $path_failsafe->set_path_tree($this->handler->get_path_tree());
    }

    return $path_failsafe->do_execute($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Load and save the current preferred language alias.
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

        $this->language_alias = NULL;
        return;
      }

      $aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id($language)->get_value_exact();
    }
    unset($language);

    // use default if no aliases are found.
    if (empty($aliases)) {
      unset($aliases);
      unset($languages);

      $this->language_alias = NULL;
      return;
    }

    $this->language_alias = end($aliases);
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
    if (is_null($this->language_alias)) {
      return new $class();
    }

    // use include_once instead of require_require to allow for failsafe behavior.
    @include_once($path . $this->language_alias . '/' . $name . self::SCRIPT_EXTENSION);

    $language_class = $class . '_' . $this->language_alias;
    if (class_exists($language_class)) {
      return new $language_class();
    }
    unset($language_class);

    // if unable to find, fallback to original class
    return new $class();
  }

  /**
   * Check to see if errors occured and change the return execution object accordingly.
   *
   * This will usually either return path not found, access denied, or server error, on such errors.
   * Do not call this on the error handling paths.
   *
   * @param c_base_path_executed $executed
   *   The already executed path handler result.
   *
   * @return c_base_path_executed
   *   The already executed path handler result.
   */
  private function p_handle_execution_errors($executed) {
    if (!c_base_return::s_has_error($executed)) {
      return $executed;
    }

    // handle errors here.
    $error = $executed->get_error(0);
    $error_code = $error->get_code();
    unset($error);

    if ($error_code === i_base_error_messages::NOT_FOUND_PATH || $error_code === i_base_error_messages::INVALID_ARGUMENT) {
      $handler_error = $this->get_handler_not_found();
    }
    elseif ($error_code === i_base_error_messages::ACCESS_DENIED) {
      $handler_error = $this->get_handler_access_denied();
    }
    else {
      $handler_error = $this->get_handler_server_error();
    }
    unset($error_code);

    if ($this->handler->get_path_tree() instanceof c_base_path_tree) {
      $handler_error->set_path_tree($this->handler->get_path_tree());
    }

    $executed_error = $handler_error->do_execute($this->http, $this->database, $this->session, $this->settings);
    unset($handler_error);

    return $executed_error;
  }
}
