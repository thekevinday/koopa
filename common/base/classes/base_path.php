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

/**
 * A generic class for managing paths information.
 *
 * It should store all data provided by the paths table in the database.
 * This is intended to be extended by a sub-class for adding project-specific path settings.
 *
 * This is meant to represent a string return type such that the string is the path.
 * All other properties represent the path settings or functions to load the path settings (such as from a database).
 *
 * The settings is_content, is_alias, and is_redirect are mutually exclusive.
 * - A path either provides content, is an alias to existing content, or redirects to another path.
 *
 * The settings is_coded and is_dynamic are not mutually exlusive.
 * - is_coded means that the path is manually defined, usually via code.
 * - is_dynamic means that the path is defined via a database.
 * - when both is_coded and is_dynamic are set, then the path is manually defined, usually via code, but may incorporate dynamic parts that may be stored in the database.
 *
 * The setting is_user defines whether or not the path is defined by the system (FALSE) or the user (TRUE).
 *
 * Coded paths should have an id of 0 (aka: none).
 *
 * The path value is limited to 256 characters maximum.
 *
 * The variables provided by this object are based on v_paths and not t_paths.
 */
class c_base_path extends c_base_return_string {
  private $id       = NULL;
  private $id_group = NULL;

  private $name_machine = NULL;
  private $name_human   = NULL;

  private $is_content  = NULL;
  private $is_alias    = NULL;
  private $is_redirect = NULL;
  private $is_coded    = NULL;
  private $is_dynamic  = NULL;
  private $is_user     = NULL;
  private $is_private  = NULL;
  private $is_locked   = NULL;

  //private $field_path  = NULL; // stored as $this->value (this is temporary notation and will be removed).
  private $field_destination   = NULL;
  private $field_response_code = NULL;

  private $date_created = NULL;
  private $date_changed = NULL;
  private $date_locked  = NULL;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id       = NULL;
    $this->id_group = NULL;

    $this->name_machine = NULL;
    $this->name_human   = NULL;

    $this->is_content  = TRUE;
    $this->is_alias    = FALSE;
    $this->is_redirect = FALSE;
    $this->is_coded    = FALSE;
    $this->is_dynamic  = TRUE;
    $this->is_user     = FALSE;
    $this->is_private  = TRUE;
    $this->is_locked   = FALSE;

    $this->field_destination   = NULL;
    $this->field_response_code = NULL;

    $this->date_created = NULL;
    $this->date_changed = NULL;
    $this->date_locked  = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->id_group);

    unset($this->name_machine);
    unset($this->name_human);

    unset($this->is_content);
    unset($this->is_alias);
    unset($this->is_redirect);
    unset($this->is_coded);
    unset($this->is_dynamic);
    unset($this->is_user);
    unset($this->is_private);
    unset($this->is_locked);

    unset($this->field_destination);
    unset($this->field_response_code);

    unset($this->date_created);
    unset($this->date_changed);
    unset($this->date_locked);

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
   * @param string $field_path
   *   The URL path assigned to this field.
   *   An empty string is assigned on parameter error.
   * @param string $name_machine
   *   The machine name of the path.
   *   This is not assigned on parameter error.
   * @param string $name_human
   *   The human-friendly name of the path.
   *   This is not assigned on parameter error.
   * @param bool $is_dynamic
   *   (optional) When TRUE this designates that the path includes dynamic parts.
   *   When FALSE, there is no interpretation on the url and path is treated exactly as is.
   *   Default setting is assigned on parameter error.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag
   *   Always returns the newly created tag.
   *   Error bit is set on error.
   */
  public static function s_create_content($field_path, $name_machine, $name_human, $is_dynamic = TRUE, $is_private = TRUE) {
    $path = new __CLASS__();

    // store all errors on return.
    $errors = array();

    if (is_string($field_path)) {
      $path->set_value($field_path);
    }
    else {
      $path->set_value('');
    }

    if (is_string($name_machine)) {
      $path->set_name_machine($name_machine);
    }

    if (is_string($name_human)) {
      $path->set_name_human($name_human);
    }

    if (is_bool($is_dynamic)) {
      $path->set_is_dynamic($is_dynamic);
    }
    else {
      $path->set_is_dynamic(TRUE);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    if (isset($_SERVER['REQUEST_TIME_FLOAT']) && is_float($_SERVER['REQUEST_TIME_FLOAT'])) {
      $path->set_date_created($_SERVER['REQUEST_TIME_FLOAT']);
      $path->set_date_changed($_SERVER['REQUEST_TIME_FLOAT']);
    }
    elseif (isset($_SERVER['REQUEST_TIME']) && is_float($_SERVER['REQUEST_TIME'])) {
      $path->set_date_created($_SERVER['REQUEST_TIME']);
      $path->set_date_changed($_SERVER['REQUEST_TIME']);
    }
    else {
      $time = (int) microtime(TRUE);
      $path->set_date_created($time);
      $path->set_date_changed($time);
      unset($time);
    }

    return $path;
  }

  /**
   * Create an alias path.
   *
   * Defaults are silently forced on invalid parameters.
   *
   * @param string $field_path
   *   The URL path assigned to this field.
   *   An empty string is assigned on parameter error.
   * @param string $name_machine
   *   The machine name of the path.
   *   This is not assigned on parameter error.
   * @param string $name_human
   *   The human-friendly name of the path.
   *   This is not assigned on parameter error.
   * @param bool $is_dynamic
   *   (optional) When TRUE this designates that the path includes dynamic parts.
   *   When FALSE, there is no interpretation on the url and path is treated exactly as is.
   *   Default setting is assigned on parameter error.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_alias($field_path, $name_machine, $name_human, $field_destination, $is_dynamic = TRUE, $is_private = TRUE) {
    $path = new __CLASS__();

    // store all errors on return.
    $errors = array();

    if (is_string($field_path)) {
      $path->set_value($field_path);
    }
    else {
      $path->set_value('');
    }

    if (is_string($name_machine)) {
      $path->set_name_machine($name_machine);
    }

    if (is_string($name_human)) {
      $path->set_name_human($name_human);
    }

    if (is_string($field_destination)) {
      $path->set_field_destination($field_destination);
    }

    if (is_bool($is_dynamic)) {
      $path->set_is_dynamic($is_dynamic);
    }
    else {
      $path->set_is_dynamic(TRUE);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    if (isset($_SERVER['REQUEST_TIME_FLOAT']) && is_float($_SERVER['REQUEST_TIME_FLOAT'])) {
      $path->set_date_created($_SERVER['REQUEST_TIME_FLOAT']);
      $path->set_date_changed($_SERVER['REQUEST_TIME_FLOAT']);
    }
    elseif (isset($_SERVER['REQUEST_TIME']) && is_float($_SERVER['REQUEST_TIME'])) {
      $path->set_date_created($_SERVER['REQUEST_TIME']);
      $path->set_date_changed($_SERVER['REQUEST_TIME']);
    }
    else {
      $time = microtime(TRUE);
      $path->set_date_created($time);
      $path->set_date_changed($time);
      unset($time);
    }

    return $path;
  }

  /**
   * Create a redirect path.
   *
   * Defaults are silently forced on invalid parameters.
   *
   * @param string $field_path
   *   The URL path assigned to this field.
   *   An empty string is assigned on parameter error.
   * @param string $name_machine
   *   The machine name of the path.
   *   This is not assigned on parameter error.
   * @param string $name_human
   *   The human-friendly name of the path.
   *   This is not assigned on parameter error.
   * @param string field_destination
   *
   * @param int $field_response_code
   *   The redirect response code.
   *   Should be a 3xx url code.
   *   Usually one of:
   *   - 300 (Multiple Choices):
   *   - 301 (Moved Permanently):
   *   - 303 (See Other):
   *   This is not assigned on parameter error.
   * @param bool $is_dynamic
   *   (optional) When TRUE this designates that the path includes dynamic parts.
   *   When FALSE, there is no interpretation on the url and path is treated exactly as is.
   *   Default setting is assigned on parameter error.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *   Default setting is assigned on parameter error.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_redirect($field_path, $name_machine, $name_human, $field_destination, $field_response_code, $is_dynamic = TRUE, $is_private = TRUE) {
    $path = new __CLASS__();

    // store all errors on return.
    $errors = array();

    if (is_string($field_path)) {
      $path->set_value($field_path);
    }
    else {
      $path->set_value('');
    }

    if (is_string($name_machine)) {
      $path->set_name_machine($name_machine);
    }

    if (is_string($name_human)) {
      $path->set_name_human($name_human);
    }

    if (is_string($field_destination)) {
      $path->set_field_destination($field_destination);
    }

    if (is_int($field_response_code)) {
      $path->set_response_code($field_response_code);
    }

    if (is_bool($is_dynamic)) {
      $path->set_is_dynamic($is_dynamic);
    }
    else {
      $path->set_is_dynamic(TRUE);
    }

    if (is_bool($is_private)) {
      $path->set_is_private($is_private);
    }
    else {
      $path->set_is_private(TRUE);
    }

    if (isset($_SERVER['REQUEST_TIME_FLOAT']) && is_float($_SERVER['REQUEST_TIME_FLOAT'])) {
      $path->set_date_created($_SERVER['REQUEST_TIME_FLOAT']);
      $path->set_date_changed($_SERVER['REQUEST_TIME_FLOAT']);
    }
    else {
      $time = (int) microtime(TRUE);
      $path->set_date_created($time);
      $path->set_date_changed($time);
      unset($time);
    }

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
   * @param int $id_group
   *   The machine name associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_id_group($id_group) {
    if (!is_int($id_group)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id_group', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_group = $id_group;
    return new c_base_return_true();
  }

  /**
   * Assigns the machine name setting.
   *
   * @param string $name_machine
   *   The machine name associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_name_machine($name_machine) {
    if (!is_string($name_machine)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name_machine', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name_machine = $name_machine;
    return new c_base_return_true();
  }

  /**
   * Assigns the human name setting.
   *
   * @param string $name_human
   *   The human name associated with the path.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_name_human($name_human) {
    if (!is_string($name_human)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name_human', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name_human = $name_human;
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
   * @return c_base_return_int|c_base_return_null
   *   ID on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->id);
  }

  /**
   * Gets the ID group setting.
   *
   * @return c_base_return_int|c_base_return_null
   *   ID group on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_id_group() {
    if (!is_string($this->id_group)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->id_group);
  }

  /**
   * Gets the machine name setting.
   *
   * @return c_base_return_string|c_base_return_null
   *   Machine name on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_name_machine() {
    if (!is_string($this->name_machine)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->name_machine);
  }

  /**
   * Gets the human name setting.
   *
   * @return c_base_return_string|c_base_return_null
   *   Human name boolean on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_name_human() {
    if (!is_string($this->name_human)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->name_human);
  }

  /**
   * Gets the is content boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is content on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_content() {
    if (!is_bool($this->is_content)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_content);
  }

  /**
   * Gets the is alias boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is alias on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_alias() {
    if (!is_bool($this->is_alias)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_alias);
  }

  /**
   * Gets the is redirect boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is redirect on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_redirect() {
    if (!is_bool($this->is_redirect)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_redirect);
  }

  /**
   * Gets the is coded boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is coded on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_coded() {
    if (!is_bool($this->is_coded)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_coded);
  }

  /**
   * Gets the is dynamic boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is dynamic on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_dynamic() {
    if (!is_bool($this->is_dynamic)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_dynamic);
  }

  /**
   * Gets the is user boolean name setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is user on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_user() {
    if (!is_bool($this->is_user)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_user);
  }

  /**
   * Gets the is private boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is private on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_private() {
    if (!is_bool($this->is_private)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_private);
  }

  /**
   * Gets the is locked boolean setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   Is locked on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_is_locked() {
    if (!is_bool($this->is_locked)) {
      return new c_base_return_null();
    }

    return c_base_return_bool::s_new($this->is_locked);
  }

  /**
   * Gets the destination field setting.
   *
   * @return c_base_return_string|c_base_return_null
   *   Destination field on success.
   *   NULL is returned if the value is not assigned.
   *   Error bit is set on error.
   */
  public function get_field_destination() {
    if (!is_string($this->field_destination)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->field_destination);
  }

  /**
   * Gets the response code field setting.
   *
   * @return c_base_return_int|c_base_return_null
   *   Response code on success.
   *   NULL is returned if date is not assigned.
   *   Error bit is set on error.
   */
  public function get_field_response_code() {
    if (!is_int($this->field_response_code)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->field_response_code);
  }

  /**
   * Gets the date created setting.
   *
   * @return c_base_return_float|c_base_return_null
   *   Date created on success.
   *   NULL is returned if date is not assigned.
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
   *   NULL is returned if date is not assigned.
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
   *   NULL is returned if date is not assigned.
   *   Error bit is set on error.
   */
  public function get_date_locked() {
    if (!is_float($this->date_locked)) {
      return new c_base_return_null();
    }

    return c_base_return_float::s_new($this->date_locked);
  }
}
