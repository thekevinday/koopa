<?php
/**
 * @file
 * Provides reservation session functions.
 */
  require_once('common/base/classes/base_error.php');
  require_once('common/base/classes/base_return.php');
  require_once('common/base/classes/base_markup.php');
  require_once('common/base/classes/base_html.php');
  require_once('common/base/classes/base_charset.php');
  require_once('common/base/classes/base_ascii.php');
  require_once('common/base/classes/base_form.php');
  require_once('common/base/classes/base_path.php');

  require_once('program/reservation/reservation_database.php');
  require_once('program/reservation/reservation_session.php');

class c_reservation_paths {
  // paths to common files (not url paths).
  private const PATH_LOGIN         = 'program/reservation/paths/u/login.php';
  private const PATH_LOGOUT        = 'program/reservation/paths/u/logout.php';
  private const PATH_ACCESS_DENIED = 'program/reservation/internal/access_denied.php';
  private const PATH_NOT_FOUND     = 'program/reservation/internal/not_found.php';

  private $http      = NULL;
  private $database  = NULL;
  private $settings  = NULL;
  private $session   = NULL;
  private $markup    = NULL;
  private $logged_in = NULL;
  private $paths     = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->uri       = NULL;
    $this->http      = NULL;
    $this->database  = NULL;
    $this->settings  = NULL;
    $this->session   = NULL;
    $this->markup    = NULL;
    $this->logged_in = NULL;
    $this->paths     = NULL;
    $this->path      = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->uri);
    unset($this->http);
    unset($this->settings);
    unset($this->session);
    unset($this->markup);
    unset($this->logged_in);
    unset($this->paths);
    unset($this->path);
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
  public function reservation_process_path(&$http, &$database, &$session, &$html, $settings, $logged_in = TRUE) {
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

    if (!($html instanceof c_base_html)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'html', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
    $this->markup = &$html;
    $this->logged_in = $logged_in;

    // require HTTPS for access to any part of this website.
    if (!isset($_SERVER["HTTPS"])) {
      // @todo: redirect to https version of requested uri.
      $failure_path = $this->p_get_path_not_found();

      return $failure_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
    }


    $request_uri = $http->get_request(c_base_http::REQUEST_URI)->get_value_exact();

    $this->uri = array(
      'scheme' => '',
      'authority' => '',
      'path' => '',
      'query' => array(),
      'fragment' => '',
      'url' => TRUE,
    );

    if (isset($request_uri['data'])) {
      $this->uri = $request_uri['data'];
    }
    unset($request_uri);

    // strip the base path from the requested uri.
    if (!empty($settings['base_path'])) {
      $this->uri['path'] = preg_replace('@^' . preg_quote($settings['base_path'], '@') . '@i', '', $this->uri['path']);
      $this->uri['path'] = preg_replace('@/$@', '', $this->uri['path']);
    }


    // load all available paths.
    $this->p_paths_create();


    // find the path
    $handler_settings = $this->paths->find_path($this->uri['path']);

    if (!is_array($handler_settings)) {
      unset($handler_settings);

      // @todo: handle error case and failsafe (404)?.
      return new c_base_return_false();
    }

    if (array_key_exists('redirect', $handler_settings)) {
      // @todo: handle redirect.
    }
    else {
      if (!empty($handler_settings['include']) && is_string($handler_settings['include'])) {
        require_once($handler_settings['include']);
      }

      if (empty($handler_settings['handler']) || !class_exists($handler_settings['handler'])) {
        // @todo: handle error case.
      }
      else {
        $this->path = new $handler_settings['handler']();
      }
    }
    unset($handler_settings);

    // handle request method validation.
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
      // @todo: considering limiting _POST to different path groups here.
    }
    elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
      $id_group = $this->path->get_id_group()->get_value_exact();

      // all paths except /s/ and /x/ may use GET.
      if ($id_group === c_base_ascii::LOWER_S || $id_group === c_base_ascii::LOWER_X) {
        $failure_path = $this->p_get_path_bad_method();

        return $failure_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
      }
      unset($id_group);
    }
    else {
      $failure_path = $this->p_get_path_bad_method();

      return $failure_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
    }


    return $this->p_paths_normal();
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
    $this->paths->set_path('', 'c_reservation_path_user_dashboard', 'program/reservation/paths/u/dashboard.php');


    // create login/logout paths
    $path = c_base_path::s_create_content(c_base_ascii::LOWER_U, 'login', FALSE);
    $this->paths->set_path($path, 'c_reservation_path_user_login', 'program/reservation/login.php');
    unset($path);

    $path = c_base_path::s_create_content(c_base_ascii::LOWER_U, 'logout', FALSE);
    $this->paths->set_path($path, 'c_reservation_path_user_logout', 'program/reservation/logout.php');
    unset($path);


    // user dashboard
    $path = c_base_path::s_create_content(c_base_ascii::LOWER_U, 'dashboard', FALSE);
    $this->paths->set_path($path, 'c_reservation_path_user_dashboard', 'program/reservation/paths/u/dashboard.php');
    unset($path);
  }

  /**
   * Process request paths and determine what to do.
   *
   * @return c_base_path_executed
   *   The execution results.
   *   The execution results with the error bit set on error.
   */
  private function p_paths_normal() {
    $id_group = $this->path->get_id_group()->get_value_exact();

    // regardless of path-specific settings, the following paths always require login credentials to access.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_U) {
      $this->path->set_is_private(TRUE);
    }

    if ($this->path instanceof c_reservation_path_user_login) {
      unset($id_group);
      return $this->path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
    }


    // if the request is private, make sure the user is logged in.
    if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_U || $this->path->get_is_private()->get_value_exact()) {
      if ($this->logged_in) {
        unset($id_group);
        return $this->path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
      }
      elseif ($this->path->get_is_root()->get_value_exact()) {
        unset($id_group);

        $this->http->set_response_status(c_base_http_status::FORBIDDEN);

        $login_path = $this->p_get_path_login();
        return $login_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
      }
      else {
        // some special case paths always provide login prompt along with access denied.
        if ($id_group === c_base_ascii::LOWER_A || $id_group === c_base_ascii::LOWER_U) {
          unset($id_group);

          $this->http->set_response_status(c_base_http_status::FORBIDDEN);

          $login_path = $this->p_get_path_login();
          return $login_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
        }
      }
    }
    else {
      unset($id_group);
      return $this->path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
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

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
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

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
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

    return $failsafe_path->do_execute($this->http, $this->database, $this->session, $this->markup, $this->settings);
  }

  /**
   * Load and return the login path.
   */
  private function p_get_path_login() {
    require_once(self::PATH_LOGIN);
    return new c_reservation_path_user_login();
  }

  /**
   * Load and return the logout path.
   */
  private function p_get_path_logout() {
    require_once(self::PATH_LOGOUT);
    return new c_reservation_path_user_logout();
  }

  /**
   * Load and return the access denied path.
   */
  private function p_get_path_access_denied() {
    require_once(self::PATH_ACCESS_DENIED);
    return new c_reservation_path_access_denied();
  }

  /**
   * Load and return the not found path.
   */
  private function p_get_path_not_found() {
    require_once(self::PATH_NOT_FOUND);
    return new c_reservation_path_not_found();
  }

  /**
   * Load and return the not found path.
   */
  private function p_get_path_bad_method() {
    require_once(self::PATH_BAD_METHOD);
    return new c_reservation_path_bad_method();
  }
}
