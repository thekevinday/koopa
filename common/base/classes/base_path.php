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

/**
 * A generic class for managing paths information.
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
 *
 * $id_path is used to represent the 'group-path' using an ordinal of one of the following 'a-z', 'A-Z', or '0-9'.
 * Interpretation of case-sensitivity is implimentation specific.
 *
 * // c_base_utf8::s_substring($path_string, 0, 1);
 */
abstract class c_base_path extends c_base_return_string {
  private $id       = NULL;
  private $id_path  = NULL;
  private $id_sort  = NULL;

  private $is_content  = NULL;
  private $is_alias    = NULL;
  private $is_redirect = NULL;
  private $is_private  = NULL;
  private $is_locked   = NULL;

  //private $field_path  = NULL; // stored as $this->value (this is temporary notation and will be removed).
  private $field_destination   = NULL;
  private $field_response_code = NULL;

  private $date_created = NULL;
  private $date_changed = NULL;
  private $date_locked  = NULL;

  private $processed = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id       = NULL;
    $this->id_path  = NULL;
    $this->id_sort  = NULL;

    $this->is_content  = TRUE;
    $this->is_alias    = FALSE;
    $this->is_redirect = FALSE;
    $this->is_private  = TRUE;
    $this->is_locked   = FALSE;

    $this->field_destination   = NULL;
    $this->field_response_code = NULL;

    $this->date_created = NULL;
    $this->date_changed = NULL;
    $this->date_locked  = NULL;

    $this->processed = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->id_path);
    unset($this->id_sort);

    unset($this->is_content);
    unset($this->is_alias);
    unset($this->is_redirect);
    unset($this->is_private);
    unset($this->is_locked);

    unset($this->field_destination);
    unset($this->field_response_code);

    unset($this->date_created);
    unset($this->date_changed);
    unset($this->date_locked);

    unset($this->processed);

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
   * Create a content path.
   *
   * @param int $id_path
   *   An ordinal of one of the characters a-z, A-Z, or 0-9.
   *   Used for grouping paths.
   * @param string $field_path
   *   The URL path assigned to this field.
   *   This is not assigned on parameter error.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag
   *   Always returns the newly created tag.
   *   Error bit is set on error.
   */
  public static function s_create_content($id_path, $field_path, $is_private = TRUE) {
    $path = new __CLASS__();

    // store all errors on return.
    $errors = array();

    if (is_int($id_path)) {
      $path->set_id_path($id_path);
    }

    if (is_string($field_path)) {
      $path->set_value($field_path);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

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
   * @param int $id_path
   *   An ordinal of one of the characters a-z, A-Z, or 0-9.
   * @param string $field_path
   *   The URL path assigned to this field.
   *   This is not assigned on parameter error.
   * @param string $field_destination
   *   A destination URL to in which this is an alias of.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_alias($id_path, $field_path, $field_destination, $is_private = TRUE) {
    $path = new __CLASS__();

    // store all errors on return.
    $errors = array();

    if (is_int($id_path)) {
      $path->set_id_path($id_path);
    }

    if (is_string($field_path)) {
      $path->set_value($field_path);
    }

    if (is_string($field_destination)) {
      $path->set_field_destination($field_destination);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

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
   * @param int $id_path
   *   An ordinal of one of the characters a-z, A-Z, or 0-9.
   * @param string $field_path
   *   The URL path assigned to this field.
   *   This is not assigned on parameter error.
   * @param string $field_destination
   *   A destination URL to redirect to.
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
   *   This is not assigned on parameter error.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_redirect($id_path, $field_path, $field_destination, $field_response_code, $is_private = TRUE) {
    $path = new __CLASS__();

    // store all errors on return.
    $errors = array();

    if (is_int($id_path)) {
      $path->set_id_path($id_path);
    }

    if (is_string($field_path)) {
      $path->set_value($field_path);
    }

    if (is_string($field_destination)) {
      $path->set_field_destination($field_destination);
    }

    if (is_int($field_response_code)) {
      $path->set_response_code($field_response_code);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    $timestamp_session = c_base_defaults_global::s_get_timestamp_session();
    $path->set_date_created($timestamp_session);
    $path->set_date_changed($timestamp_session);
    unset($timestamp_session);

    return $path;
  }

  /**
   * Assigns the machine name setting.
   *
   * @param int $id
   *   The machine name associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_id($id) {
    if (!is_int($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Assigns the machine name setting.
   *
   * @param int $id_path
   *   The machine name associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_id_path($id_path) {
    if (!is_int($id_path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id_path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_path = $id_path;
    return new c_base_return_true();
  }

  /**
   * Assign the value.
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

    $this->value = $this->p_sanitize_value($value);

    if (mb_strleng($this->value) == 0) {
      $this->id_sort = 0;
    }
    else {
      $this->id_sort = ord(c_base_utf8::s_substring($this->value, 0, 1)->get_value_exact());
    }

    return TRUE;
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
   * Assigns the destination field setting.
   *
   * @param string $field_destination
   *   The destination field associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_field_destination($field_destination) {
    if (!is_string($field_destination)) {
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
   * Gets the id setting.
   *
   * @return c_base_return_int
   *   ID on success.
   *   An ID of 0 means that there is no valid ID specified.
   *   Error bit is set on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      return c_base_return_int::s_new(0);
    }

    return c_base_return_int::s_new($this->id);
  }

  /**
   * Gets the ID path setting.
   *
   * @return c_base_return_int
   *   ID group on success.
   *   A path ID of 0 means that there is no valid ID specified.
   *   Error bit is set on error.
   */
  public function get_id_path() {
    if (!is_int($this->id_path)) {
      return c_base_return_int::s_new(0);
    }

    return c_base_return_int::s_new($this->id_path);
  }

  /**
   * Gets the ID sort value.
   *
   * The ID sort value is the ordinal of the first character of the path.
   * This is used for minor optimization.
   *
   * @return c_base_return_int
   *   ID group on success.
   *   A path ID of 0 means that there is no valid ID specified.
   *   Error bit is set on error.
   */
  public function get_id_sort() {
    if (!is_int($this->id_sort)) {
      return c_base_return_int::s_new(0);
    }

    return c_base_return_int::s_new($this->id_sort);
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
   * Gets the destination field setting.
   *
   * @return c_base_return_string
   *   Destination field on success.
   *   An empty string is returned if not defined.
   *   Error bit is set on error.
   */
  public function get_field_destination() {
    if (!is_string($this->field_destination)) {
      return c_base_return_string::s_new('');
    }

    return c_base_return_string::s_new($this->field_destination);
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
   *   FALSE is returned if the date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_locked() {
    if (!is_float($this->date_locked)) {
      return new c_base_return_null();
    }

    return c_base_return_float::s_new($this->date_locked);
  }

  /**
   * Get the results of when this given path is executed.
   *
   * @return c_base_return_null|c_base_return_bool|c_base_return_int|c_base_return_float|c_base_return_array
   *   This can be any of NULL, bool, int, float, or array as defined by this class.
   *   The class c_base_return can be used as a catch all.
   *   NULL is intended to represent that execution has not happend or do_execute was called and no operations were performed.
   *   This does set the error bit.
   *
   * @see: $this->do_execute()
   */
  final public function get_processed() {
    if (is_bool($this->processed)) {
      if ($this->processed) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }

    if (is_int($this->processed)) {
      return c_base_return_int::s_new($this->processed);
    }

    if (is_float($this->processed)) {
      return c_base_return_float::s_new($this->processed);
    }

    if (is_array($this->processed)) {
      return c_base_return_array::s_new($this->processed);
    }

    return new c_base_return_null();
  }

  /**
   * Execute using the specified path.
   *
   * The results of this function are stored in a 'processed' string.
   *
   * @param c_base_http $http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   *
   * @return c_base_return_null|c_base_return_bool
   *   NULL is returned if no operation was performed.
   *   TRUE is returned if function executed.
   *   FALSE is returned if function was not execution, but should have.
   *   NULL is returned with error bit set if no operation was performed because $http is an invalid parameter.
   *   TRUE is returned with error bit set if function executed but an error occured.
   *   FALSE is returned with error bit set if function did not execute, but should have, and an error occured.
   *
   * @see: $this->get_processed();
   */
  public function do_execute($http) {
    if (!($http instanceof c_base_http)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'c_base_http', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_null', $error);
    }

    if (!is_null($this->processed)) {
      $this->processed = NULL;
    }

    return new c_base_return_null();
  }

  /**
   * Returns a sanitized string for use as the url path string.
   *
   * This removes multiple consecutive '/'.
   * This removes any '/' prefix.
   * This removes any '/' suffix.
   * This limits the string size to 256 characters.
   *
   * @return string
   *   The sanitized string.
   */
  private function p_sanitize_value($value) {
    $value = preg_replace('@/+@i', '/', $value);
    $value = preg_replace('@(^/|/$)@', '', $value);
    $value = c_base_utf8::s_substring($value, 0, 256);

    return $value->get_value_exact();
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
  private $root  = NULL;
  private $paths = NULL;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->root  = NULL;
    $this->paths = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->root);
    unset($this->paths);

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
   * Assign the path object string to this class.
   *
   * Duplicate paths overwrite previous paths.
   *
   * @param c_base_path $path
   *   An implentation of c_base_path to be executed when the path is requested.
   *   If path value is an empty string, then this is treated as the root path.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_path($path) {
    if (!($path instanceof c_base_path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $path_string = $path->get_value();

    // No default will be specified, so return error if the value is not properly defined.
    if (!is_string($path_string)) {
      return new c_base_return_false();
    }

    if (mb_strlen($path_string) == 0) {
      $this->root = $path;
      return new c_base_return_true();
    }

    // array is optimized based on the path group id and then the first character of a given path.
    $ordinal = $this->get_id_path()->get_value_exact();
    $sort = $this->get_id_sort()->get_value_exact();

    if (!is_array($this->paths)) {
      $this->paths = array();
    }

    if (!array_key_exists($ordinal, $this->paths)) {
      $this->paths[$ordinal] = array();
    }

    if (!array_key_exists($sort, $this->paths[$ordinal])) {
      $this->paths[$ordinal][$sort] = array();
    }

    $path_parts = explode('/', $path);

    // @todo: explode this into parts and place them into array.
    //        the line below is incorrect, but is used as a notation until I finish writing this.
    //$this->paths[$ordinal][$sort][$path_string] = $path;

    return new c_base_return_true();
  }

  /**
   * Gets a path object for the specified path.
   *
   * @param string $path_string
   *   The URL path without any path arguments.
   *   This does not accept wildcards.
   *
   * @return c_base_path|c_base_status
   *   A path object is returned if the path matches, with wildcards.
   *   Wildcards are matched after all non-wildcards.
   *   FALSE without error bit set is return if path was not found.
   *   FALSE with error bit set is returned on error.
   */
  public function get_path($path_string) {
    // @todo
  }
}
