<?php
/**
 * @file
 * Provides a class for managing hard coded and dynamic paths.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_path.php');

/**
 * Provides a collection of possible paths for selection and execution.
 *
 * This utilizes some very basic path based optimizations.
 * First, the path group is optimized (an ordinal representing one of: NULL, a-z, A-Z, or 0-9).
 * Second, the first character of the path string (expects utf-8).
 * Third, the paths are exploded and searched based on all their sub-parts.
 */
class c_base_paths extends c_base_return {
  protected const SCRIPT_EXTENSION = '.php';

  private $paths = NULL;
  private $root  = NULL;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->paths = [];
    $this->root  = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->paths);
    unset($this->root);

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
    return self::p_s_value_exact($return, __CLASS__, NULL);
  }

  /**
   * Assign a specific path handler.
   *
   * Duplicate paths overwrite previous paths.
   *
   * @todo: should redirect and alias booleans be added as parameters?
   *
   * @pram string $directory
   *   The first part of the file path
   * @pram string $path
   *   The url path in which the handler applies to.
   * @param string $handler
   *   The name of an implementation of c_base_path.
   * @param string|null $directory
   *   (optional) The prefix path (relative to the PHP includes) to include that contains the requested path.
   *   When not NULL, both $directory and $name must not be NULL.
   * @param string|null $name
   *   (optional) The suffix path (relative to the PHP includes) to include that contains the requested path.
   *   When not NULL, both $directory and $name must not be NULL.
   * @param array|null $allowed_methods
   *   (optional) An array of ids of allowed methods.
   *   When NULL, this value is ignored.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function add_path($path, $handler, $include_directory = NULL, $include_name = NULL, $allowed_methods = NULL) {
    if (!is_string($path)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'path', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($handler)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'handler', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ((!is_null($include_directory) || (is_null($include_directory) && !is_null($include_name))) && !is_string($include_directory)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'include_directory', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ((!is_null($include_name) || (is_null($include_name) && !is_null($include_directory))) && !is_string($include_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'include_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    // get allowed methods
    $path_object = new c_base_path();
    if (is_null($allowed_methods)) {
      $methods = $path_object->get_allowed_methods()->get_value_exact();
      if (!is_array($methods)) {
        $methods = [];
      }
    }
    else {
      $methods = $allowed_methods;
    }

    if (mb_strlen($path) == 0) {
      unset($path_object);
      $this->root = ['handler' => $handler, 'include_directory' => $include_directory, 'include_name' => $include_name, 'is_root' => TRUE, 'methods' => $methods];
      return new c_base_return_true();
    }

    if (!is_null($allowed_methods) && !is_array($allowed_methods)) {
      unset($path_object);
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'allowed_methods', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $valid_path = $path_object->set_value('/' . $path);
    if (!$valid_path) {
      unset($path_object);
      unset($valid_path);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'path', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    unset($valid_path);

    $path_string = $path_object->get_value_exact();
    unset($path_object);

    // assign each path part to the path
    $path_parts = explode('/', $path_string);
    unset($path_string);


    // load the path group, if available.
    $id_group = 0;
    if (mb_strlen($path_parts[0]) == 1) {
      $ordinal = ord($path_parts[0]);
      if (in_array($ordinal, c_base_defaults_global::RESERVED_PATH_GROUP)) {
        $id_group = $ordinal;
      }
      unset($ordinal);
      unset($path_parts[0]);
    }

    if (!is_array($this->paths)) {
      $this->paths = [];
    }

    if (!array_key_exists($id_group, $this->paths)) {
      $this->paths[$id_group] = [];
    }

    $path_tree = &$this->paths[$id_group];

    $depth_current = 1;
    $depth_total = count($path_parts);

    // make sure the first path exists.
    // note that 'paths' is not populated here, but is later used when being processed by self::find_path().
    $path_part = array_shift($path_parts);
    if (!array_key_exists($path_part, $path_tree)) {
      $path_tree[$path_part] = [
        'paths' => [],
        'include_directory' => NULL,
        'include_name' => NULL,
        'handler' => NULL,
        'methods' => [],
      ];
    }

    $path_tree = &$path_tree[$path_part];
    if ($depth_current == $depth_total) {
      $path_tree['include_directory'] = $include_directory;
      $path_tree['include_name'] = $include_name;
      $path_tree['handler'] = $handler;
      $path_tree['methods'] = $methods;
    }
    else {
      foreach ($path_parts as $path_part) {
        if (!isset($path_tree['paths'][$path_part])) {
          $path_tree['paths'][$path_part] = [
            'paths' => [],
            'include_directory' => NULL,
            'include_name' => NULL,
            'handler' => NULL,
            'methods' => [],
          ];
        }

        $path_tree = &$path_tree['paths'][$path_part];
        $depth_current++;

        if ($depth_current == $depth_total) {
          $path_tree['include_directory'] = $include_directory;
          $path_tree['include_name'] = $include_name;
          $path_tree['handler'] = $handler;
          $path_tree['methods'] = $methods;
          break;
        }
      }
    }
    unset($path_part);
    unset($path_parts);
    unset($depth_current);
    unset($depth_total);

    return new c_base_return_true();
  }

  /**
   * Gets a path object for the specified path.
   *
   * @param string|null $path_string
   *   The URL path without any path arguments.
   *   This does not accept wildcards.
   *   Set to NULL or an empty string for the root path.
   *
   * @return c_base_return_array|c_base_return_int|c_base_return_null
   *   An array containing:
   *   - 'include_directory': the prefix path of the file to include that contains the handler class implementation.
   *   - 'include_name': the suffix path of the file to include that contains the handler class implementation.
   *   - 'handler': the name of the handler class (set to the boolean TRUE, when redirects are used).
   *   - 'methods': An array of HTTP request codes that are allowed at this path.
   *   - 'path_tree': An array of the path tree.
   *   - 'id_group': The group id code for the specified path.
   *   - 'redirect': if specified, then a redirect path (instead of include/handler).
   *   - 'redirect_partial': boolean designating if the redirect url is only a partial url.
   *   - 'code': if redirect is specified, then the http response code associated with the redirect.
   *   - 'extra_slashes': boolean designating that there are multiple extra slashes found (a reason for a url redirect).
   *
   *   Wildcards are matched after all non-wildcards.
   *   If not found, the 'handler' in the array will be set to NULL.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_login()
   * @see: self::process_path()
   */
  public function find_path($path_string) {
    if (!is_null($path_string) && !is_string($path_string)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'path_string', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($path_string) || mb_strlen($path_string) == 0) {
      if (is_array($this->root)) {
        $root_array = $this->root;
        $root_array['id_group'] = 0;
        $root_array['path_tree'] = [];

        return c_base_return_array::s_new($root_array);
      }

      return c_base_return_array::s_new(array(
        'include_directory' => NULL,
        'include_name' => NULL,
        'handler' => NULL,
        'methods' => NULL,
        'id_group' => 0,
        'path_tree' => [],
      ));
    }


    // sanitize the url path.
    $path = new c_base_path();
    if (!$path->set_value($path_string)) {
      unset($path);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{format_name}' => 'path_string', ':{expected_format}' => 'Valid URL path', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    $sanitized = $path->get_value_exact();
    unset($path);

    // if the sanitized path is different from the original, then send a url redirect.
    if (strcmp($path_string, $sanitized) != 0 && $path_string != '/' . $sanitized) {
      return c_base_return_array::s_new(array(
        'handler' => TRUE,
        'redirect' => $sanitized,
        'code' => c_base_http_status::MOVED_PERMANENTLY,
        'redirect_partial' => TRUE,
        'extra_slashes' => TRUE,
      ));
    }

    $path_parts = explode('/', $sanitized);
    unset($sanitized);


    // load the path group, if available.
    $id_group = 0;
    if (mb_strlen($path_parts[0]) == 1) {
      $ordinal = ord($path_parts[0]);
      if (in_array($ordinal, c_base_defaults_global::RESERVED_PATH_GROUP)) {
        $id_group = $ordinal;
      }
      unset($ordinal);
      unset($path_parts[0]);
    }


    $depth_current = 1;
    $depth_total = count($path_parts);
    $found = NULL;
    $path_tree = &$this->paths[$id_group];
    $path_tree_history = [];

    if (is_array($this->root)) {
      $path_tree_history[] = $this->root;
    }

    // @fixme: the current design needs to handle multiple possible wildcard paths when searching (such as '/a/b/c/%', '/a/%/c', where '/a/b/c/%' would prevent '/a/%/c' from ever matching).
    $path_part = array_shift($path_parts);
    if (is_array($path_tree) && (array_key_exists($path_part, $path_tree) || array_key_exists('%', $path_tree))) {
      if (array_key_exists($path_part, $path_tree)) {
        $path_tree = &$path_tree[$path_part];
      }
      else {
        $path_tree = &$path_tree['%'];
      }

      $path_tree_history[] = [
        'path' => $path_part,
        'include_directory' => isset($path_tree['include_directory']) ? $path_tree['include_directory'] : NULL,
        'include_name' => isset($path_tree['include_name']) ? $path_tree['include_name'] : NULL,
        'handler' => isset($path_tree['handler']) ? $path_tree['handler'] : NULL,
        'methods' => isset($path_tree['methods']) ? $path_tree['methods'] : NULL,
      ];

      if ($depth_current == $depth_total) {
        $found = [
          'include_directory' => $path_tree['include_directory'],
          'include_name' => $path_tree['include_name'],
          'handler' => $path_tree['handler'],
          'methods' => $path_tree['methods'],
          'id_group' => $id_group,
          'path_tree' => $path_tree_history,
        ];
      }
      else {
        foreach ($path_parts as $path_part) {
          if (array_key_exists($path_part, $path_tree['paths'])) {
            $path_tree = &$path_tree['paths'][$path_part];
            $depth_current++;
          }
          else if (array_key_exists('%', $path_tree['paths'])) {
            $path_tree = &$path_tree['paths']['%'];
            $depth_current++;
          }
          else {
            break;
          }

          $path_tree_history[] = [
            'path' => $path_part,
            'include_directory' => isset($path_tree['include_directory']) ? $path_tree['include_directory'] : NULL,
            'include_name' => isset($path_tree['include_name']) ? $path_tree['include_name'] : NULL,
            'handler' => isset($path_tree['handler']) ? $path_tree['handler'] : NULL,
            'methods' => isset($path_tree['methods']) ? $path_tree['methods'] : NULL,
          ];

          if ($depth_current == $depth_total) {
            $found = [
              'include_directory' => $path_tree['include_directory'],
              'include_name' => $path_tree['include_name'],
              'handler' => $path_tree['handler'],
              'methods' => $path_tree['methods'],
              'id_group' => $id_group,
              'path_tree' => $path_tree_history,
            ];
            break;
          }
        }
      }
    }
    unset($path_part);
    unset($path_parts);
    unset($depth_current);
    unset($depth_total);
    unset($path_tree);

    if (is_array($found) && !is_null($found['handler'])) {
      unset($id_group);
      unset($path_tree_history);
      return c_base_return_array::s_new($found);
    }
    unset($found);

    return c_base_return_array::s_new(array(
      'include_directory' => NULL,
      'include_name' => NULL,
      'handler' => NULL,
      'methods' => NULL,
      'id_group' => $id_group,
      'path_tree' => $path_tree_history,
    ));
  }
}
