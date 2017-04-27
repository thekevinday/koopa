<?php
/**
 * @file
 * Provides reservation session functions.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_markup.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_charset.php');
require_once('common/base/classes/base_ascii.php');
require_once('common/base/classes/base_form.php');
require_once('common/base/classes/base_path.php');

require_once('program/reservation/reservation_database.php');
require_once('program/reservation/reservation_session.php');

class c_reservation_paths {
  // paths to common files (not url paths).
  private const PATH_LOGIN         = 'program/reservation/paths/u/';
  private const PATH_LOGOUT        = 'program/reservation/paths/u/';
  private const PATH_ACCESS_DENIED = 'program/reservation/internal/';
  private const PATH_NOT_FOUND     = 'program/reservation/internal/';
  private const PATH_BAD_METHOD    = 'program/reservation/internal/';
  private const PATH_SERVER_ERROR  = 'program/reservation/internal/';
  private const PATH_REDIRECTS     = 'program/reservation/';

  private const NAME_LOGIN         = 'login';
  private const NAME_LOGOUT        = 'logout';
  private const NAME_ACCESS_DENIED = 'access_denied';
  private const NAME_NOT_FOUND     = 'not_found';
  private const NAME_BAD_METHOD    = 'bad_method';
  private const NAME_SERVER_ERROR  = 'server_error';
  private const NAME_REDIRECTS     = 'reservation_redirects';

  private $http      = NULL;
  private $database  = NULL;
  private $settings  = NULL;
  private $session   = NULL;
  private $output    = NULL;
  private $logged_in = NULL;

  private $paths = NULL;
  private $path  = NULL;

  private $alias = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->http      = NULL;
    $this->database  = NULL;
    $this->settings  = NULL;
    $this->session   = NULL;
    $this->output    = NULL;
    $this->logged_in = NULL;

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
    unset($this->logged_in);

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
   * @param bool $logged_in
   *   (optional) TRUE of logged in, FALSE otherwise.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  public function reservation_process_path(&$http, &$database, &$session, $settings, $logged_in = TRUE) {
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

    if (!is_bool($logged_in)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'logged_in', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(array(), 'c_base_path_executed', $error);
    }

    $this->http = &$http;
    $this->database = &$database;
    $this->settings = $settings;
    $this->session = &$session;
    $this->output = NULL;
    $this->logged_in = $logged_in;

    $this->p_get_language_alias();

    // require HTTPS for access to any part of this website.
    if (!isset($_SERVER["HTTPS"])) {
      // @todo: redirect to https version of requested uri.
      $failure_path = $this->p_get_path_not_found();

      return $failure_path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    $request_uri = $http->get_request(c_base_http::REQUEST_URI)->get_value_exact();

    $this->settings['uri'] = array(
      'scheme' => '',
      'authority' => '',
      'path' => '',
      'query' => array(),
      'fragment' => '',
      'url' => TRUE,
    );

    if (isset($request_uri['data'])) {
      $this->settings['uri'] = $request_uri['data'];
    }
    unset($request_uri);

    // strip the base path from the requested uri.
    if (!empty($settings['base_path'])) {
      $this->settings['uri']['path'] = preg_replace('@^' . preg_quote($settings['base_path'], '@') . '@i', '', $this->settings['uri']['path']);
      $this->settings['uri']['path'] = preg_replace('@/$@', '', $this->settings['uri']['path']);
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
    $handler_settings = $this->paths->find_path($this->settings['uri']['path'])->get_value();
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
      require_once(self::PATH_REDIRECTS . self::NAME_REDIRECTS . '.php');

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
        require_once($handler_settings['include_directory'] . $handler_settings['include_name']);
      }

      // execute path handler, using custom-language if defined.
      if (empty($handler_settings['handler'])) {
        $server_error = $this->p_get_path_server_error();
        return $server_error->do_execute($this->http, $this->database, $this->session, $this->settings);
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
            $server_error = $this->p_get_path_server_error();
            return $server_error->do_execute($this->http, $this->database, $this->session, $this->settings);
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
          $server_error = $this->p_get_path_server_error();
          return $server_error->do_execute($this->http, $this->database, $this->session, $this->settings);
        }
      }

      if (isset($handler_settings['is_root']) && $handler_settings['is_root']) {
        $this->path->set_is_root(TRUE);
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
    $this->paths->set_path('', 'c_reservation_path_user_dashboard', 'program/reservation/paths/u/', 'dashboard.php');

    // create login/logout paths
    $this->paths->set_path('/u/login', 'c_reservation_path_user_login', 'program/reservation/paths/u/', 'login.php');
    $this->paths->set_path('/u/logout', 'c_reservation_path_user_logout', 'program/reservation/paths/u/', 'logout.php');

    // user dashboard
    $this->paths->set_path('/u/dashboard', 'c_reservation_path_user_dashboard', 'program/reservation/paths/u/', 'dashboard.php');
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
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_U) {
      $this->path->set_is_private(TRUE);
    }

    if ($this->path instanceof c_reservation_path_user_login) {
      unset($id_group);
      return $this->path->do_execute($this->http, $this->database, $this->session, $this->settings);
    }


    // if the request is private, make sure the user is logged in.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_U || $this->path->get_is_private()->get_value_exact()) {
      if ($this->logged_in) {
        unset($id_group);
        return $this->path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      elseif ($this->path->get_is_root()->get_value_exact()) {
        unset($id_group);

        $this->http->set_response_status(c_base_http_status::FORBIDDEN);

        $login_path = $this->p_get_path_login();
        return $login_path->do_execute($this->http, $this->database, $this->session, $this->settings);
      }
      else {
        // some special case paths always provide login prompt along with access denied.
        if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_U) {
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
    elseif ($this->path->get_is_private()->get_value_exact() && $id_group !== c_base_ascii::NULL) {
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
    return new c_reservation_path_options_method();
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
    require_once($path . $name . '.php');

    // use default if no aliases are found.
    if (is_null($this->alias)) {
      return new $class();
    }

    // use include_once instead of require_require to allow for failsafe behavior.
    @include_once($path . $this->alias . '/' . $name . '.php');

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
 * Provide the HTTP options response.
 *
 * This does not provide any content body.
 */
final class c_reservation_path_options_method extends c_base_path {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }


    // assign HTTP response status.
    $allowed_methods = $this->allowed_methods;
    $allowed_method = array_shift($allowed_methods);
    $http->set_response_allow($allowed_method, TRUE);

    if (!empty($allowed_methods)) {
      foreach ($allowed_methods as $allowed_method) {
        $http->set_response_allow($allowed_method);
      }
    }
    unset($allowed_method);
    unset($allowed_methods);

    return $executed;
  }

  /**
   * Load the title text associated with this page.
   *
   * This is provided here as a means for a language class to override with a custom language for the title.
   *
   * @return string|null
   *   A string is returned as the custom title.
   *   NULL is returned to enforce default title.
   */
  protected function pr_get_title() {
    return NULL;
  }

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   */
  protected function pr_get_text($code) {
    switch ($code) {
      case 0:
        return 'Server Error';
      case 1:
        return 'Something went wrong while processing your request, please try again later.';
    }

    return '';
  }
}

