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

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_ascii.php');
require_once('common/base/classes/base_utf8.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_markup.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_cookie.php');

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

  protected $id_group = NULL;

  protected $is_content  = NULL;
  protected $is_alias    = NULL;
  protected $is_redirect = NULL;
  protected $is_private  = NULL;
  protected $is_locked   = NULL;
  protected $is_root     = NULL;

  protected $field_destination   = NULL;
  protected $field_response_code = NULL;

  protected $date_created = NULL;
  protected $date_changed = NULL;
  protected $date_locked  = NULL;

  protected $include_directory = NULL;
  protected $include_name      = NULL;

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
    $this->date_locked  = NULL;

    $this->include_directory = NULL;
    $this->include_name      = NULL;
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
    unset($this->date_locked);

    unset($this->include_directory);
    unset($this->include_name);

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
    $without_wildcard = preg_replace('@(^%/|^%$|/%/|/%$)@', '', $sanitized);
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
   * Return the value.
   *
   * @return string|null $value
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
   * @return DOMNode $value
   *   The value DOMNode stored within this class.
   */
  public function get_value_exact() {
    if (!is_string($this->value)) {
      return '';
    }

    return $this->value;
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
    $errors = array();

    $path->set_id_group($id_group);
    $path->set_value($field_path);

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    $path->set_is_content(TRUE);

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
    $errors = array();

    $path->set_id_group($id_group);
    $path->set_value($field_path);
    $path->set_field_destination($field_destination);

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    $path->set_is_alias(TRUE);

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
    $errors = array();

    $path->set_field_destination($field_destination);
    $path->set_field_response_code($field_response_code);

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    $path->set_is_redirect(TRUE);

    $timestamp_session = c_base_defaults_global::s_get_timestamp_session();
    $path->set_date_created($timestamp_session);
    $path->set_date_changed($timestamp_session);
    unset($timestamp_session);

    return $path;
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
   */
  public function set_id_group($id_group) {
    if (!is_int($id_group) || $id_group < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id_group', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_group = $id_group;
    return new c_base_return_true();
  }

  /**
   * Assigns the is content boolean setting.
   *
   * @param bool $is_content
   *   The is content boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_content($is_content) {
    if (!is_bool($is_content)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_content', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_content = $is_content;
    return new c_base_return_true();
  }

  /**
   * Assigns the is alias boolean setting.
   *
   * @param bool $is_alias
   *   The is alias boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_alias($is_alias) {
    if (!is_bool($is_alias)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_alias', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_alias = $is_alias;
    return new c_base_return_true();
  }

  /**
   * Assigns the is redirect boolean setting.
   *
   * @param bool $is_redirect
   *   The is redirect boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_redirect($is_redirect) {
    if (!is_bool($is_redirect)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_redirect', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_redirect = $is_redirect;
    return new c_base_return_true();
  }

  /**
   * Assigns the is coded boolean setting.
   *
   * @param bool $is_coded
   *   The is coded boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_coded($is_coded) {
    if (!is_bool($is_coded)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_coded', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_coded = $is_coded;
    return new c_base_return_true();
  }

  /**
   * Assigns the is dynamic boolean setting.
   *
   * @param bool $is_dynamic
   *   The is dynamic boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_dynamic($is_dynamic) {
    if (!is_bool($is_dynamic)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_dynamic', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_dynamic = $is_dynamic;
    return new c_base_return_true();
  }

  /**
   * Assigns the is user boolean name setting.
   *
   * @param bool $is_user
   *   The is user boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_user($is_user) {
    if (!is_bool($is_user)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_user', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_user = $is_user;
    return new c_base_return_true();
  }

  /**
   * Assigns the is private boolean setting.
   *
   * @param bool $is_private
   *   The is private boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_private($is_private) {
    if (!is_bool($is_private)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_private', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_private = $is_private;
    return new c_base_return_true();
  }

  /**
   * Assigns the is locked boolean setting.
   *
   * @param bool $is_locked
   *   The is locked boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_locked($is_locked) {
    if (!is_bool($is_locked)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_locked', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_locked = $is_locked;
    return new c_base_return_true();
  }

  /**
   * Assigns the is root boolean setting.
   *
   * @param bool $is_root
   *   The is root boolean associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_is_root($is_root) {
    if (!is_bool($is_root)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'is_root', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->is_root = $is_root;
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
   */
  public function set_field_destination($field_destination) {
    if (!is_string($field_destination) && !is_array($field_destination)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'field_destination', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
   */
  public function set_field_response_code($field_response_code) {
    if (!is_int($field_response_code)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'field_response_code', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
   */
  public function set_date_created($date_created) {
    if (!is_float($date_created) && !is_int($date_created)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'date_created', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
   */
  public function set_date_changed($date_changed) {
    if (!is_float($date_changed) && !is_int($date_changed)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'date_changed', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_changed = (float) $date_changed;
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
   */
  public function set_date_locked($date_locked) {
    if (!is_float($date_locked) && !is_int($date_locked)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'date_locked', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_locked = (float) $date_locked;
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
   */
  public function set_include_directory($directory) {
    if (!is_string($directory) && !is_null($directory)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_directory' => 'directory', ':function_directory' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
   */
  public function set_include_name($name) {
    if (!is_string($name) && !is_null($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->include_name = $name;
    return new c_base_return_true();
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
   * Gets the is content boolean setting.
   *
   * @return c_base_return_bool
   *   Is content on success.
   *   Error bit is set on error.
   */
  public function get_is_content() {
    if (!is_bool($this->is_content)) {
      $this->is_content = FALSE;
    }

    return c_base_return_bool::s_new($this->is_content);
  }

  /**
   * Gets the is alias boolean setting.
   *
   * @return c_base_return_bool
   *   Is alias on success.
   *   Error bit is set on error.
   */
  public function get_is_alias() {
    if (!is_bool($this->is_alias)) {
      $this->is_alias = FALSE;
    }

    return c_base_return_bool::s_new($this->is_alias);
  }

  /**
   * Gets the is redirect boolean setting.
   *
   * @return c_base_return_bool
   *   Is redirect on success.
   *   Error bit is set on error.
   */
  public function get_is_redirect() {
    if (!is_bool($this->is_redirect)) {
      $this->is_redirect = FALSE;
    }

    return c_base_return_bool::s_new($this->is_redirect);
  }

  /**
   * Gets the is private boolean setting.
   *
   * @return c_base_return_bool
   *   Is private on success.
   *   Error bit is set on error.
   */
  public function get_is_private() {
    if (!is_bool($this->is_private)) {
      $this->is_private = FALSE;
    }

    return c_base_return_bool::s_new($this->is_private);
  }

  /**
   * Gets the is locked boolean setting.
   *
   * @return c_base_return_bool
   *   Is locked on success.
   *   Error bit is set on error.
   */
  public function get_is_locked() {
    if (!is_bool($this->is_locked)) {
      $this->is_locked = FALSE;
    }

    return c_base_return_bool::s_new($this->is_locked);
  }

  /**
   * Gets the is root boolean setting.
   *
   * @return c_base_return_bool
   *   Is root on success.
   *   Error bit is set on error.
   */
  public function get_is_root() {
    if (!is_bool($this->is_root)) {
      $this->is_root = FALSE;
    }

    return c_base_return_bool::s_new($this->is_root);
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
   * Execute using the specified path, rendering the page.
   *
   * @param c_base_http $http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database $database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   (optional) An array of additional settings that are usually site-specific.
   *
   * @return c_base_path_executed
   *   An executed array object is returned on success.
   *   An executed array object with error bit set is returned on error.
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
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

    return new c_base_path_executed();
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(NULL, 'c_base_return_null', $error);
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'type', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(NULL, 'c_base_return_null', $error);
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
}

/**
 * A generic class for provided returns values of the do_execute() functions from c_base_path.
 *
 * The array value of this class is intended to be used for any additional return values (such as form errors).
 * The $cookies is meant to hold any HTTP cookies to be processed after the execution.
 * The $output is meant to hold the output for any non-HTML content in the event that HTML is not to be renderred..
 *
 * @see: c_base_path
 */
class c_base_path_executed extends c_base_return_array {
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
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Assign cookies.
   *
   * @param c_base_cookie
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'cookie', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'append', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!$append || !is_array($this->cookies)) {
      $this->cookies = array();
    }

    $this->cookies[] = $cookie;
    return new c_base_return_true();
  }

  /**
   * Assign output.
   *
   * @param c_base_return
   *   The output to assign.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_output($output) {
    if (!($output instanceof c_base_return)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'output', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
 * Provides a collection of possible paths for selection and execution.
 *
 * This utilizes some very basic path based optimizations.
 * First, the path group is optimized (an ordinal representing one of: NULL, a-z, A-Z, or 0-9).
 * Second, the first character of the path string (expects utf-8).
 * Third, the paths are exploded and searched based on all their sub-parts.
 */
class c_base_paths extends c_base_return {
  private $paths = NULL;
  private $root  = NULL;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->paths = array();
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
    return self::p_s_value_exact($return, __CLASS__, array());
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
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_path($path, $handler, $include_directory = NULL, $include_name = NULL) {
    if (!is_string($path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($handler)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'handler', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ((!is_null($include_directory) || (is_null($include_directory) && !is_null($include_name))) && !is_string($include_directory)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'include_directory', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ((!is_null($include_name) || (is_null($include_name) && !is_null($include_directory))) && !is_string($include_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'include_name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (mb_strlen($path) == 0) {
      $this->root = array('handler' => $handler, 'include_directory' => $include_directory, 'include_name' => $include_name, 'is_root' => TRUE);
      return new c_base_return_true();
    }

    $path_object = new c_base_path();
    $valid_path = $path_object->set_value($path);

    if (!$valid_path) {
      unset($path_object);
      unset($valid_path);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
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
      $this->paths = array();
    }

    if (!array_key_exists($id_group, $this->paths)) {
      $this->paths[$id_group] = array();
    }

    $path_tree = &$this->paths[$id_group];

    $depth_current = 1;
    $depth_total = count($path_parts);
    foreach ($path_parts as $path_part) {
      if ($depth_current == $depth_total) {
        $path_tree['include_directory'] = $include_directory;
        $path_tree['include_name'] = $include_name;
        $path_tree['handler'] = $handler;
        break;
      }

      if (!isset($path_tree['paths'][$path_part])) {
        $path_tree['paths'][$path_part] = array(
          'paths' => array(),
          'include_directory' => NULL,
          'include_name' => NULL,
          'handler' => NULL,
        );
      }

      $path_tree = &$path_tree['paths'][$path_part];
      $depth_current++;
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
   *   - 'handler': the name of the handler class.
   *   - 'redirect': if specified, then a redirect path (instead of include/handler).
   *   - 'code': if redirect is specified, then the http response code associated with the redirect.
   *   Wildcards are matched after all non-wildcards.
   *   NULL is returned if not found.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_login()
   * @see: self::process_path()
   */
  public function find_path($path_string) {
    if (!is_null($path_string) && !is_string($path_string)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'path_string', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($path_string) || mb_strlen($path_string) == 0) {
      if (is_array($this->root)) {
        return c_base_return_array::s_new($this->root);
      }

      return new c_base_return_null();
    }


    // sanitize the url path.
    $path = new c_base_path();
    if (!$path->set_value($path_string)) {
      unset($path);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':format_name' => 'path_string', ':expected_format' => 'Valid URL path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    $sanitized = $path->get_value_exact();
    unset($path);

    // if the sanitized path is different from the original, then send a url redirect.
    if (strcmp($path_string, $sanitized) != 0 && $path_string != '/' . $sanitized) {
      return c_base_return_array::s_new(array('redirect' => $sanitized, 'code' => c_base_http_status::MOVED_PERMANENTLY));
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
    foreach ($path_parts as $path_part) {
      if ($depth_current == $depth_total) {
        if (isset($path_tree['handler'])) {
          $found = array('include_directory' => $path_tree['include_directory'], 'include_name' => $path_tree['include_name'], 'handler' => $path_tree['handler']);
          break;
        }
      }

      if (!isset($path_tree['paths'][$path_part])) {
        if ($depth_current == $depth_total) {
          if (isset($path_tree['handler'])) {
            $found = array('include_directory' => $path_tree['include_directory'], 'include_name' => $path_tree['include_name'], 'handler' => $path_tree['handler']);
            break;
          }
        }

        if (isset($path_tree['paths']['%'])) {
          $path_tree = &$path_tree['paths']['%'];
          $depth_current++;
          continue;
        }

        break;
      }

      $path_tree = &$path_tree['paths'][$path_part];
      $depth_current++;
    }
    unset($path_part);
    unset($path_parts);
    unset($depth_current);
    unset($depth_total);
    unset($path_tree);

    if (is_array($found)) {
      return c_base_return_array::s_new($found);
    }
    unset($found);

    return new c_base_return_null();
  }
}
