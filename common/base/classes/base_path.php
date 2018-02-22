<?php
/**
 * @file
 * Provides a class for managing hard coded and dynamic paths.
 *
 * For performance reasons, hard-coded paths are preferred and should not be stored in the database.
 * For dynamic reasons, dynamic paths are stored in the database and should not be hardcoded.
 *
 * This is open-source, it is far too easy to modify source code and add explicit paths.
 * The gains in performance and resources are generally worth it.
 *
 * The preferred design is to provide dynamic hard-coded paths whose variable parts reference known data (such as request ids).
 * The specific paths themselves do not need to be explicitly declared.
 * A good way to look at dynamic hard-coded paths is by seeing such paths as a function.
 * Then, for very specific cases, users should then be granted the ability to create path aliases.
 * However, aliases should not be able to override pre-define paths or the performance gains are lost by requiring loading of the aliases before each static path.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_utf8.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_array.php');

/**
 * A generic class for managing paths information.
 *
 * @todo: update this class documentation.
 *
 * Note: There are still a.lot of changes going on in this and this comments may become out of date.
 *       At this time, I am considering having system defined paths be 'group-paths', existing at: '/a/, '/b/', '/c/', .., '/0/', '/1/', .., '/9'.
 *       All other non-'group-paths' are dynamic paths used by the database.
 *
 *       This should make it easy to determine whether or not the database should be accessed.
 *       This function was originally designed to be both static and dynamic (aka: database), but I am currently leaning towards using this for static only.
 *
 *       Expect things to be out of date and need updating.
 *       There may be major refactoring as I develop this (at least, more so than whatever is normal).
 *
 *       With this implementation, path aliases and redirects might be meaningless and may be removed in the future.
 *
 * This provides a way to both create static paths not in the database and to store a database path for use
 *
 * This is meant to represent a string return type such that the string is the path.
 * All other properties represent the path settings or functions to load the path settings (such as from a database).
 *
 * The settings is_content, is_alias, and is_redirect are mutually exclusive.
 * - A path either provides content, is an alias to existing content, or redirects to another path.
 *
 * The setting is_user defines whether or not the path is defined by the system (FALSE) or the user (TRUE).
 *
 * The path value is limited to 256 characters maximum.
 *
 * The variables provided by this object are based on v_paths and not t_paths.
 *
 * $id_group is used to represent the 'group-path' using an ordinal of one of the following 'a-z', 'A-Z', or '0-9'.
 * These paths are case-sensitive.
 *
 * @todo: add support to conditionally handle HTTP Request Methods and handle errors as desired by implementing classes.
 *
 * // c_base_utf8::s_substring($path_string, 0, 1);
 */
class c_base_path extends c_base_rfc_string {
  use t_base_return_value_exact;

  private const p_DEFAULT_ALLOWED_METHODS = [
    c_base_http::HTTP_METHOD_GET => c_base_http::HTTP_METHOD_GET,
    c_base_http::HTTP_METHOD_POST => c_base_http::HTTP_METHOD_POST,
    c_base_http::HTTP_METHOD_HEAD => c_base_http::HTTP_METHOD_HEAD,
    c_base_http::HTTP_METHOD_OPTIONS => c_base_http::HTTP_METHOD_OPTIONS,
  ];

  private const p_DEFAULT_SANITIZE_HTML = [
    'flags' => ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE,
    'encoding' => 'UTF-8',
  ];

  protected $id_group;

  protected $is_content;
  protected $is_alias;
  protected $is_redirect;
  protected $is_private;
  protected $is_locked;
  protected $is_root;

  protected $field_destination;
  protected $field_response_code;

  protected $date_created;
  protected $date_changed;
  protected $date_synced;
  protected $date_locked;
  protected $date_deleted;

  protected $include_directory;
  protected $include_name;

  protected $allowed_methods;
  protected $sanitize_html;

  protected $path_tree;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id_group = NULL;

    $this->is_content  = TRUE;
    $this->is_alias    = FALSE;
    $this->is_redirect = FALSE;
    $this->is_private  = TRUE;
    $this->is_locked   = FALSE;
    $this->is_root     = FALSE;

    $this->field_destination   = NULL;
    $this->field_response_code = NULL;

    $this->date_created = NULL;
    $this->date_changed = NULL;
    $this->date_synced  = NULL;
    $this->date_locked  = NULL;
    $this->date_deleted = NULL;

    $this->include_directory = NULL;
    $this->include_name      = NULL;

    $this->allowed_methods = self::p_DEFAULT_ALLOWED_METHODS;
    $this->sanitize_html   = self::p_DEFAULT_SANITIZE_HTML;

    $this->path_tree = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id_group);

    unset($this->is_content);
    unset($this->is_alias);
    unset($this->is_redirect);
    unset($this->is_private);
    unset($this->is_locked);
    unset($this->is_root);

    unset($this->field_destination);
    unset($this->field_response_code);

    unset($this->date_created);
    unset($this->date_changed);
    unset($this->date_synced);
    unset($this->date_locked);
    unset($this->date_deleted);

    unset($this->include_directory);
    unset($this->include_name);

    unset($this->allowed_methods);
    unset($this->sanitize_html);

    unset($this->path_tree);

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
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Create a content path.
   *
   * @param int $id_group
   *   An ordinal of one of the characters a-z, A-Z, or 0-9.
   *   0 may be assigned to represent no group.
   * @param string $field_path
   *   The URL path assigned to this field.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag
   *   Always returns the newly created tag.
   *   Error bit is set on error.
   */
  public static function s_create_content($id_group, $field_path, $is_private = TRUE) {
    $class = __CLASS__;
    $path = new $class();
    unset($class);

    // @todo: store all errors on return.
    $errors = [];

    $path->set_id_group($id_group);
    $path->set_value($field_path);

    if (is_bool($is_private)) {
      $path->is_private($is_private);
    }
    else {
      $path->is_private(TRUE);
    }

    $path->is_content(TRUE);

    $timestamp_session = c_base_defaults_global::s_get_timestamp_session();
    $path->set_date_created($timestamp_session);
    $path->set_date_changed($timestamp_session);
    unset($timestamp_session);

    return $path;
  }

  /**
   * Create an alias path.
   *
   * Defaults are silently forced on invalid parameters.
   *
   * @param int $id_group
   *   An ordinal of one of the characters a-z, A-Z, or 0-9.
   *   0 may be assigned to represent no group.
   * @param string $field_path
   *   The URL path assigned to this field.
   * @param string|array $field_destination
   *   When a string, a destination URL to redirect to.
   *   When an array, an array of the destination url parts.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_alias($id_group, $field_path, $field_destination, $is_private = TRUE) {
    $class = __CLASS__;
    $path = new $class();
    unset($class);

    // @todo: store all errors on return.
    $errors = [];

    $path->set_id_group($id_group);
    $path->set_value($field_path);
    $path->set_field_destination($field_destination);

    if (is_bool($is_private)) {
      $path->is_private($is_private);
    }
    else {
      $path->is_private(TRUE);
    }

    $path->is_alias(TRUE);

    $timestamp_session = c_base_defaults_global::s_get_timestamp_session();
    $path->set_date_created($timestamp_session);
    $path->set_date_changed($timestamp_session);
    unset($timestamp_session);

    return $path;
  }

  /**
   * Create a redirect path.
   *
   * Defaults are silently forced on invalid parameters.
   *
   * @param string|array $field_destination
   *   When a string, a destination URL to redirect to.
   *   When an array, an array of the destination url parts.
   * @param int $response_code
   *   The HTTP code to use when performing the redirect.
   *   Should be one of 3xx error code integers.
   * @param int $field_response_code
   *   The redirect response code.
   *   Should be a 3xx url code.
   *   Usually one of:
   *   - 300 (Multiple Choices):
   *   - 301 (Moved Permanently):
   *   - 303 (See Other):
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_redirect($field_destination, $field_response_code, $is_private = TRUE) {
    $class = __CLASS__;
    $path = new $class();
    unset($class);

    // @todo: store all errors on return.
    $errors = [];

    $path->set_field_destination($field_destination);
    $path->set_field_response_code($field_response_code);

    if (is_bool($is_private)) {
      $path->is_private($is_private);
    }
    else {
      $path->is_private(TRUE);
    }

    $path->is_redirect(TRUE);

    $timestamp_session = c_base_defaults_global::s_get_timestamp_session();
    $path->set_date_created($timestamp_session);
    $path->set_date_changed($timestamp_session);
    unset($timestamp_session);

    return $path;
  }

  /**
   * Assign the value.
   *
   * The string must be a valid URL path.
   *
   * This removes multiple consecutive '/'.
   * This removes any '/' prefix.
   * This removes any '/' suffix.
   * This limits the string size to 256 characters.
   *
   * @param string $value
   *   Any value so long as it is a string.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_string($value)) {
      return FALSE;
    }

    $sanitized = self::pr_sanitize_path($value);

    // the path wildcard is intentionally non-standard.
    // remove it so that it does not cause the validator to fail.
    $without_wildcard = preg_replace('@(^%/|^%$|(/%)+|/%/$|/%$)@', '', $sanitized);
    if (!is_string($without_wildcard)) {
      return FALSE;
    }

    // check to see if sanitized value is allowed.
    $without_wilcard_parts = $this->pr_rfc_string_prepare($without_wildcard);
    unset($without_wildcard);

    if ($without_wilcard_parts['invalid']) {
      unset($without_wilcard_parts);
      unset($sanitized);
      return FALSE;
    }

    $validated = $this->pr_rfc_string_is_path($without_wilcard_parts['ordinals'], $without_wilcard_parts['characters']);
    if ($validated['invalid']) {
      unset($without_wilcard_parts);
      unset($validated);
      unset($sanitized);
      return FALSE;
    }
    unset($without_wilcard_parts);
    unset($validated);

    $this->value = $sanitized;
    unset($sanitized);

    return TRUE;
  }

  /**
   * Assigns sort id.
   *
   * @param int $id_group
   *   A id used for grouping and sorting the path.
   *   Must be >= 0.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_group($id_group) {
    if (!is_int($id_group) || $id_group < 0) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_group', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_group = $id_group;
    return new c_base_return_true();
  }

  /**
   * Assigns the destination field setting.
   *
   * @param string|array $field_destination
   *   When a string, a destination URL to redirect to.
   *   When an array, an array of the destination url parts.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_field_destination($field_destination) {
    if (!is_string($field_destination) && !is_array($field_destination)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'field_destination', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->field_destination = $field_destination;
    return new c_base_return_true();
  }

  /**
   * Assigns the response code field setting.
   *
   * @param int $field_response_code
   *   The response code field associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_field_response_code($field_response_code) {
    if (!is_int($field_response_code)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'field_response_code', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->field_response_code = $field_response_code;
    return new c_base_return_true();
  }

  /**
   * Assigns the date created setting.
   *
   * @param float $date_created
   *   The date created associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_created($date_created) {
    if (!is_float($date_created) && !is_int($date_created)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_created', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_created = (float) $date_created;
    return new c_base_return_true();
  }

  /**
   * Assigns the date changed setting.
   *
   * @param float $date_changed
   *   The date changed associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_changed($date_changed) {
    if (!is_float($date_changed) && !is_int($date_changed)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_changed', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_changed = (float) $date_changed;
    return new c_base_return_true();
  }

  /**
   * Assigns the date synced setting.
   *
   * @param float $date_synced
   *   The date synced associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_synced($date_synced) {
    if (!is_float($date_synced) && !is_int($date_synced)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_synced', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_synced = (float) $date_synced;
    return new c_base_return_true();
  }

  /**
   * Assigns the date locked setting.
   *
   * @param float $date_locked
   *   The date locked associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_locked($date_locked) {
    if (!is_float($date_locked) && !is_int($date_locked)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_locked', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_locked = (float) $date_locked;
    return new c_base_return_true();
  }

  /**
   * Assigns the date deleted setting.
   *
   * @param float $date_deleted
   *   The date deleted associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_deleted($date_deleted) {
    if (!is_float($date_deleted) && !is_int($date_deleted)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_deleted', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_deleted = (float) $date_deleted;
    return new c_base_return_true();
  }

  /**
   * Assign an include path directory needed to process this path.
   *
   * This is the prefix part of the path.
   *
   * @param string|null $directory
   *   A path to a file that may be found via the PHP search path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_include_directory($directory) {
    if (!is_string($directory) && !is_null($directory)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'directory', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->include_directory = $directory;
    return new c_base_return_true();
  }

  /**
   * Assign an include path name needed to process this path.
   *
   * This is the suffix part of the path.
   *
   * @param string|null $path
   *   A path to a file that may be found via the PHP search path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_include_name($name) {
    if (!is_string($name) && !is_null($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->include_name = $name;
    return new c_base_return_true();
  }

  /**
   * Assign an allowed http method.
   *
   * @param int $method
   *   The id of the method to allow.
   * @param bool $append
   *   (optional) When TRUE, the method id is appended.
   *   When FALSE, the array is re-created with $method as the only array value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_allowed_method($method, $append = TRUE) {
    if (!is_int($method)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'method', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!$append) {
      $this->allowed_methods = [];
    }

    $this->allowed_methods[$method] = $method;
    return new c_base_return_true();
  }

  /**
   * Assign all allowed http methods.
   *
   * @param array $method
   *   An array of method ids of the method to allow.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_allowed_methods($methods) {
    if (!is_array($methods)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'methods', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->allowed_methods = [];
    foreach ($methods as $method) {
      if (is_int($method)) {
        $this->allowed_methods[$method] = $method;
      }
    }
    unset($method);

    return new c_base_return_true();
  }

  /**
   * Assign html sanitization settings.
   *
   * @param int|null $flags
   *   (optional) An integer representing the flags to be directly passed to htmlspecialchars().
   * @param string|null $encoding
   *   (optional) A string representing the encodong to be directly passed to htmlspecialchars().
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: htmlspecialchars()
   */
  public function set_sanitize_html($flags = NULL, $encoding = NULL) {
    if (!is_null($flags) && !is_int($flags)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'flags', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($encoding) && !is_string($encoding)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'encoding', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->sanitize_html)) {
      $this->sanitize_html = static::p_DEFAULT_SANITIZE_HTML;
    }

    if (!is_null($flags)) {
      $this->sanitize_html['flags'] = $flags;
    }

    if (!is_null($encoding)) {
      $this->sanitize_html['encoding'] = $encoding;
    }

    return new c_base_return_true();
  }

  /**
   * Assign an path tree associated with the path.
   *
   * This should include the current path.
   * This can be used to generate the breadcrumb.
   *
   * @param c_base_path_tree|null $path_tree
   *   A path tree to the current path that this object represents.
   *   Set to NULL to remove the currently assigned path tree value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_path_tree($path_tree) {
    if (!is_null($path_tree) && !($path_tree instanceof c_base_path_tree)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'path_tree', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($path_tree)) {
      $this->path_tree = NULL;
    }
    else {
      $this->path_tree = clone($path_tree);
    }

    return new c_base_return_true();
  }

  /**
   * Return the value.
   *
   * @return string|null
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_string($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return DOMNode
   *   The value DOMNode stored within this class.
   */
  public function get_value_exact() {
    if (!is_string($this->value)) {
      return '';
    }

    return $this->value;
  }

  /**
   * Gets the ID sort setting.
   *
   * @return c_base_return_int
   *   ID group on success.
   *   A path ID of 0 means that there is no valid ID specified.
   *   Error bit is set on error.
   */
  public function get_id_group() {
    if (!is_int($this->id_group)) {
      return c_base_return_int::s_new(0);
    }

    return c_base_return_int::s_new($this->id_group);
  }

  /**
   * Gets the destination field setting.
   *
   * @return c_base_return_string|c_base_return_array
   *   Destination field on success.
   *   An empty string is returned if not defined.
   *   Error bit is set on error.
   */
  public function get_field_destination() {
    if (!is_string($this->field_destination) && !is_array($this->field_destination)) {
      return c_base_return_string::s_new('');
    }

    if (is_string($this->field_destination)) {
      return c_base_return_string::s_new($this->field_destination);
    }

    return c_base_return_array::s_new($this->field_destination);
  }

  /**
   * Gets the response code field setting.
   *
   * @return c_base_return_int
   *   Response code on success.
   *   An empty string is returned if not defined.
   *   Error bit is set on error.
   */
  public function get_field_response_code() {
    if (!is_int($this->field_response_code)) {
      return c_base_return_int::s_new('');
    }

    return c_base_return_int::s_new($this->field_response_code);
  }

  /**
   * Gets the date created setting.
   *
   * @return c_base_return_float|c_base_return_null
   *   Date created on success.
   *   FALSE is returned if the date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_created() {
    if (!is_float($this->date_created)) {
      return new c_base_return_false();
    }

    return c_base_return_float::s_new($this->date_created);
  }

  /**
   * Gets the date changed setting.
   *
   * @return c_base_return_float|c_base_return_null
   *   Date changed on success.
   *   FALSE is returned if the date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_changed() {
    if (!is_float($this->date_changed)) {
      return new c_base_return_null();
    }

    return c_base_return_float::s_new($this->date_changed);
  }

  /**
   * Gets the date synced setting.
   *
   * @return c_base_return_float|c_base_return_null
   *   Date synced on success.
   *   FALSE is returned if the date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_synced() {
    if (!is_float($this->date_synced)) {
      return new c_base_return_false();
    }

    return c_base_return_float::s_new($this->date_synced);
  }

  /**
   * Gets the date locked setting.
   *
   * @return c_base_return_float|c_base_return_null
   *   Date locked on success.
   *   NULL is returned if the date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_locked() {
    if (!is_float($this->date_locked)) {
      return new c_base_return_null();
    }

    return c_base_return_float::s_new($this->date_locked);
  }

  /**
   * Gets the date deleted setting.
   *
   * @return c_base_return_float|c_base_return_null
   *   Date deleted on success.
   *   FALSE is returned if the date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_deleted() {
    if (!is_float($this->date_deleted)) {
      return new c_base_return_false();
    }

    return c_base_return_float::s_new($this->date_deleted);
  }

  /**
   * Get the assigned include path directory.
   *
   * This is the prefix part of the path.
   *
   * @return c_base_return_string|c_base_return_null
   *   Include path string on success.
   *   NULL is returned if the include path is not assigned.
   *   Error bit is set on error.
   */
  public function get_include_directory() {
    if (!is_string($this->include_directory)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->include_directory);
  }

  /**
   * Get the assigned include path name.
   *
   * This is the suffix part of the path.
   *
   * @return c_base_return_string|c_base_return_null
   *   Include path string on success.
   *   NULL is returned if the include path is not assigned.
   *   Error bit is set on error.
   */
  public function get_include_name() {
    if (!is_string($this->include_name)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->include_name);
  }

  /**
   * Get the assigned include path name.
   *
   * This is the suffix part of the path.
   *
   * @return c_base_return_array
   *   An array of allowed methods is returned.
   *   An empty array with the error bit set is returned on error.
   */
  public function get_allowed_methods() {
    if (!is_array($this->allowed_methods)) {
      $this->allowed_methods = static::p_DEFAULT_ALLOWED_METHODS;
    }

    return c_base_return_array::s_new($this->allowed_methods);
  }

  /**
   * Get the currently assigned HTML sanitization settings.
   *
   * @return c_base_return_array
   *   An array of html sanitization settings.
   *   An empty array with the error bit set is returned on error.
   *
   * @see: htmlspecialchars()
   */
  public function get_sanitize_html() {
    if (!is_array($this->sanitize_html)) {
      $this->sanitize_html = static::p_DEFAULT_SANITIZE_HTML;
    }

    return c_base_return_array::s_new($this->sanitize_html);
  }

  /**
   * Get the assigned path tree.
   *
   * This should include the current path.
   * This can be used to generate the breadcrumb.
   *
   * @return c_base_path_tree|c_base_return_null
   *   An array of path strings
   *   NULL is returned if the path tree is not assigned.
   *   Error bit is set on error.
   */
  public function get_path_tree() {
    if (!($this->path_tree instanceof c_base_path_tree)) {
      return new c_base_return_null();
    }

    return clone($this->path_tree);
  }

  /**
   * Get or Assign the is content boolean setting.
   *
   * @param bool|null $is_content
   *   When a boolean, this is assigned as the current is content setting.
   *   When NULL, the current setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_content is NULL, is content boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_content($is_content = NULL) {
    if (!is_null($is_content) && !is_bool($is_content)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_content', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_content)) {
      if (!is_bool($this->is_content)) {
        $this->is_content = FALSE;
      }

      if ($this->is_content) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    $this->is_content = $is_content;
    return new c_base_return_true();
  }

  /**
   * Get or Assign the is alias boolean setting.
   *
   * @param bool|null $is_alias
   *   When a boolean, this is assigned as the current is alias setting.
   *   When NULL, the current setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_alias is NULL, is alias boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_alias($is_alias = NULL) {
    if (!is_null($is_alias) && !is_bool($is_alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_alias)) {
      if (!is_bool($this->is_alias)) {
        $this->is_alias = FALSE;
      }

      if ($this->is_alias) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    $this->is_alias = $is_alias;
    return new c_base_return_true();
  }

  /**
   * Get or Assign the is redirect boolean setting.
   *
   * @param bool|null $is_redirect
   *   When a boolean, this is assigned as the current is redirect setting.
   *   When NULL, the current setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_redirect is NULL, is redirect boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_redirect($is_redirect = NULL) {
    if (!is_null($is_redirect) && !is_bool($is_redirect)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_redirect', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_redirect)) {
      if (!is_bool($this->is_redirect)) {
        $this->is_redirect = FALSE;
      }

      if ($this->is_redirect) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    $this->is_redirect = $is_redirect;
    return new c_base_return_true();
  }

  /**
   * Get or Assign the is private boolean setting.
   *
   * @param bool|null $is_private
   *   When a boolean, this is assigned as the current is private setting.
   *   When NULL, the current setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_private is NULL, is private boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_private($is_private = NULL) {
    if (!is_null($is_private) && !is_bool($is_private)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_private', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_private)) {
      if (!is_bool($this->is_private)) {
        $this->is_private = FALSE;
      }

      if ($this->is_private) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    $this->is_private = $is_private;
    return new c_base_return_true();
  }

  /**
   * Get or Assign the is locked boolean setting.
   *
   * @param bool|null $is_locked
   *   When a boolean, this is assigned as the current is locked setting.
   *   When NULL, the current setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_locked is NULL, is locked boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_locked($is_locked = NULL) {
    if (!is_null($is_locked) && !is_bool($is_locked)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_locked', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_locked)) {
      if (!is_bool($this->is_locked)) {
        $this->is_locked = FALSE;
      }

      if ($this->is_locked) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    $this->is_locked = $is_locked;
    return new c_base_return_true();
  }

  /**
   * Get or Assign the is root boolean setting.
   *
   * @param bool|null $is_root
   *   When a boolean, this is assigned as the current is root setting.
   *   When NULL, the current setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_root is NULL, is root boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_root($is_root = NULL) {
    if (!is_null($is_root) && !is_bool($is_root)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_root', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_root)) {
      if (!is_bool($this->is_root)) {
        $this->is_root = FALSE;
      }

      if ($this->is_root) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    $this->is_root = $is_root;
    return new c_base_return_true();
  }

  /**
   * Execute using the specified path, rendering the page.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   (optional) An array of additional settings that are usually site-specific.
   *
   * @return c_base_path_executed|int
   *   An executed array object is returned on success.
   *   An executed array object with error bit set is returned on error.
   */
  public function do_execute(&$http, &$database, &$session, $settings = []) {
    $executed = new c_base_path_executed();

    if ($this->is_redirect) {
      $http->set_response_location($this->field_destination);
      $http->set_response_status($this->field_response_code);
    }

    $result = $this->set_parameters($http, $database, $session, $settings);
    if (c_base_return::s_has_error($result)) {
      $executed->set_error($result->get_errors());
    }
    unset($result);

    return $executed;
  }

  /**
   * Assign default variables used by this class.
   *
   * This is normally done automatically, but in certain cases, this may need to be explicitly called.
   *
   * Calling this will trigger default settings to be regenerated, including the breadcrumbs.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   (optional) An array of additional settings that are usually site-specific.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::do_execute()
   */
  public function set_parameters(&$http, &$database, &$session, $settings) {
    if (!($http instanceof c_base_http)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'http', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'settings', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->http = $http;
    $this->database = $database;
    $this->session = $session;
    $this->settings = $settings;

    $request_uri = $this->http->get_request(c_base_http::REQUEST_URI)->get_value_exact();
    if (isset($request_uri['data']) && is_string($request_uri['data'])) {
      $request_uri = $request_uri['data'];
      unset($request_uri['current']);
      unset($request_uri['invalid']);

      $this->request_uri = $request_uri;
    }
    else {
      $this->request_uri = [
        'scheme' => $this->settings['base_scheme'],
        'authority' => $this->settings['base_host'],
        'path' => $this->settings['base_path'],
        'query' => NULL,
        'fragment' => NULL,
        'url' => TRUE,
      ];
    }
    unset($request_uri);

    return new c_base_return_true();
  }

  /**
   * Returns a sanitized string for use as the url path string.
   *
   * This removes any '../' in the path.
   * This removes multiple consecutive '/'.
   * This removes any '/' prefix.
   * This removes any '/' suffix.
   * This limits the string size to 256 characters.
   *
   * @param string $path
   *   The path string to sanitize.
   *
   * @return string
   *   The sanitized string.
   */
  protected static function pr_sanitize_path($path) {
    // do not support '../' in the url paths (primarily for security reasons).
    $sanitized = preg_replace('@^\.\./(\.\./)*@', '', $path);
    $sanitized = preg_replace('@/(\.\./)+@', '/', $sanitized);

    // remove redundant path parts, such as replacing '//////' with '/'.
    $sanitized = preg_replace('@/(/)+@', '/', $sanitized);

    // remove leading and trailing slashes.
    $sanitized = preg_replace('@(^/|/$)@', '', $sanitized);

    // enforce a max path size of 256 characters.
    $sanitized = c_base_utf8::s_substring($sanitized, 0, 256);

    return $sanitized->get_value_exact();
  }

  /**
   * @todo: write type sanitization code by implementing protected functions that can be overwritten by the calling class as necessary.
   *
   * It may be useful to provide the variables, such as $http, $database, $settings, etc.., to this class to allow for more advanced handling of types.
   *
   * @param string $id
   *   The unique id representing the value.
   * @param int $type
   *   The data type to sanitize the value as.
   * @param string|int|null $type_sub
   *   (optional) a sub-type to provide additional customization on each type.
   *   This is intended to be used in site-specific ways and is not implemented by the base project.
   *   Instead, extend this class and implement this function to utilize $type_sub.
   *   If this is not a string, int, or null, it is forced to be NULL without an error bit being assigned.
   *
   * @return c_base_return_value|c_base_return_null
   *   The sanitized value, in whatever form it needs to be.
   *   NULL is returned if unable to process or value is supposed to be NULL.
   *   NULL is returned with error bit set on error.
   */
  protected function pr_sanitize($id, $type, $type_sub = NULL) {
    if (!is_string($id) && mb_strlen($id) > 0) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_null($error);
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_null($error);
    }

    if (!is_array($_POST) || !array_key_exists($id, $_POST)) {
      return new c_base_return_null();
    }

    if (!is_null($type_sub) && !is_int($type_sub) && !is_string($type_sub)) {
      $type_sub = NULL;
    }

    // @fixme: $value is returned unsanitized until this code is implemented.
    return c_base_return_value::s_new($_POST[$id]);
  }

  /**
   * Obtains the current HTTP request method.
   *
   * @param c_base_http &$http
   *   The HTTP information object.
   *
   * @return int
   *   An HTTP request method is always returned.
   */
  protected function pr_get_method(&$http) {
    $method = $http->get_request(c_base_http::REQUEST_METHOD)->get_value_exact();
      if (isset($method['data']) && is_int($method['data'])) {
      $method = $method['data'];
    }
    else {
      $method = c_base_http::HTTP_METHOD_NONE;
    }

    return $method;
  }

  /**
   * Replace all occurences of arguments within string.
   *
   * Perform sanitization based on the first character.
   * If first character is ':', do not perform sanitization.
   * If first character is '@', santize as HTML text.
   *
   * I recommend wrapping placeholders in '{' and '}' to help enforce uniqueness.
   * - For example the string ':words' could be confused with two different placeholders: ':word' and ':words'.
   * - By using ':{words}' and ':{word}', there there should be fewer chances of mixups.
   *
   * @param string &$string
   *   The string to perform replacements on.
   * @param array $arguments
   *   An array of replacement arguments.
   *
   * @see: htmlspecialchars()
   * @see: str_replace()
   */
  protected function pr_process_replacements(&$string, $arguments) {
    foreach ($arguments as $place_holder => $replacement) {
      $type = mb_substr($place_holder, 0, 1);

      if ($type == ':') {
        $sanitized = $replacement;
      }
      elseif ($type == '@') {
        $sanitized = htmlspecialchars($replacement, $this->sanitize_html['flags'], $this->sanitize_html['encoding']);
      }
      else {
        unset($type);

        // do not perform replacements on unknown placeholders.
        continue;
      }
      unset($type);

      $string = str_replace($place_holder, $sanitized, $string);
    }
    unset($place_holder);
    unset($replacement);
  }
}

/**
 * A generic class for provided returns values of the do_execute() functions from c_base_path.
 *
 * The array value of this class is intended to be used for any additional return values (such as form errors).
 * The $cookies is meant to hold any HTTP cookies to be processed after the execution.
 * The $output is meant to hold the output for any non-HTML content in the event that HTML is not to be renderred..
 *
 * @see: c_base_path()
 */
class c_base_path_executed extends c_base_array {
  private $cookies = NULL;
  private $output  = NULL;
  private $form    = NUll;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->cookies = NULL;
    $this->output  = NULL;
    $this->form    = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->cookies);
    unset($this->output);
    unset($this->form);

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
   * Assign cookies.
   *
   * @param c_base_cookie $cookie
   *   The cookie to assign.
   * @param bool $append
   *   (optional) When TRUE the $cookie is appended.
   *   When FALSE, the array is reset with $cookie as the only value in the array.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_cookies($cookie, $append = TRUE) {
    if (!($cookie instanceof c_base_cookie)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cookie', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!$append || !is_array($this->cookies)) {
      $this->cookies = [];
    }

    $this->cookies[] = $cookie;
    return new c_base_return_true();
  }

  /**
   * Assign output.
   *
   * @param c_base_return|null $output
   *   The output to assign.
   *   NULL may be specified to remove any output.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_output($output) {
    if (!is_null($output) && !($output instanceof c_base_return)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'output', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->output = $output;
    return new c_base_return_true();
  }

  /**
   * Gets the assigned output
   *
   * @return c_base_return|c_base_return_null
   *   The assigned output is returned.
   *   If there is no assigned output (generally when execution is not performed) NULL is returned.
   */
  public function get_output() {
    if (is_null($this->output)) {
      return new c_base_return_null();
    }

    return $this->output;
  }
}

/**
 * A generic class for providing path trees (like a breadcrumb).
 *
 * @see: c_base_path()
 */
class c_base_path_tree extends c_base_array {
  private $id_group = NULL;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id_group = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id_group);

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
   * Assign group id.
   *
   * @param int $id_group
   *   The group id to assign.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_group($id_group) {
    if (!is_int($id_group)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_group', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_group = $id_group;
    return new c_base_return_true();
  }

  /**
   * Gets the assigned output
   *
   * @return c_base_return|c_base_return_null
   *   The assigned output is returned.
   *   If there is no assigned output (generally when execution is not performed) NULL is returned.
   */
  public function get_id_group() {
    if (!is_int($this->id_group)) {
      return c_base_return_int::s_new(0);
    }

    return c_base_return_int::s_new($this->id_group);
  }
}
