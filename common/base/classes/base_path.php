<?php
/**
 * @file
 * Provides a class for managing hard coded and dynamic paths.
 *
 * For performance reasons, hard-coded paths are preferred and should not be stored in the database.
 * For dynamic reasons, dynamic paths are stored in the database and should not be hardcoded.
 *
 * This is open-source, it is far too easy to modify source codde and add explicit paths.
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
   * @param string $name_machine
   *   The machine name of the path.
   * @param string $name_human
   *   The human-friendly name of the path.
   * @param bool $is_dynamic
   *   (optional) When TRUE this designates that the path includes dynamic parts.
   *   When FALSE, there is no interpretation on the url and path is treated exactly as is.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_content($field_path, $name_machine, $name_human, $is_dynamic = TRUE, $is_private = TRUE) {
    $path = new __CLASS__();

    // @todo: write functions and handle return values.
    $path->set_value($field_path);
    $path->set_name_machine($name_machine);
    $path->set_name_human($name_human);

    // @todo: ..

    return $path;
  }

  /**
   * Create an alias path.
   *
   * @param string $field_path
   *   The URL path assigned to this field.
   * @param string $name_machine
   *   The machine name of the path.
   * @param string $name_human
   *   The human-friendly name of the path.
   * @param string $field_destination
   *   The url this content pretends to be.
   * @param bool $is_dynamic
   *   (optional) When TRUE this designates that the path includes dynamic parts.
   *   When FALSE, there is no interpretation on the url and path is treated exactly as is.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_alias($field_path, $name_machine, $name_human, $field_destination, $is_dynamic = TRUE, $is_private = TRUE) {
    $path = new __CLASS__();

    // @todo: write functions and handle return values.
    $path->set_value($field_path);
    $path->set_name_machine($name_machine);
    $path->set_name_human($name_human);
    $path->set_field_destination($field_destination);

    // @todo: ..

    return $path;
  }

  /**
   * Create a redirect path.
   *
   * @param string $field_path
   *   The URL path assigned to this field.
   * @param string $name_machine
   *   The machine name of the path.
   * @param string $name_human
   *   The human-friendly name of the path.
   * @param string $field_destination
   *   The url to redirect to.
   * @param string $field_destination
   *   The redirect response code.
   *   Should be a 3xx url code.
   *   Usually one of:
   *   - 300 (Multiple Choices):
   *   - 301 (Moved Permanently):
   *   - 303 (See Other):
   * @param bool $is_dynamic
   *   (optional) When TRUE this designates that the path includes dynamic parts.
   *   When FALSE, there is no interpretation on the url and path is treated exactly as is.
   * @param bool $is_private
   *   (optional) When TRUE, path is considered private and requires specific access privileges.
   *   When FALSE, the path is accessible without any access privileges.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_content($field_path, $name_machine, $name_human, $field_destination, $field_response_code, $is_dynamic = TRUE, $is_private = TRUE) {
    $path = new __CLASS__();

    // @todo: write functions and handle return values.
    $path->set_value($field_path);
    $path->set_name_machine($name_machine);
    $path->set_name_human($name_human);
    $path->set_field_destination($field_destination);
    $path->set_field_response_code($field_response_code);

    // @todo: ..

    return $path;
  }
}
