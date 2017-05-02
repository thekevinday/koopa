<?php
/**
 * @file
 * Provides a class for managing system roles.
 */
require_once('common/base/classes/base_return.php');

/**
 * A class for managing user accounts.
 *
 * This extends c_base_return_array such that the user 'settings' field are stored in $this->value.
 */
class c_base_users_user extends c_base_return_array {
  protected $id;
  protected $id_external;
  protected $id_sort;

  protected $name_machine;
  protected $name_human;

  protected $address_email;

  protected $roles;

  protected $is_private;
  protected $is_locked;
  protected $is_deleted;

  protected $can_manage_roles;

  protected $date_created;
  protected $date_changed;
  protected $date_synced;
  protected $date_locked;
  protected $date_deleted;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id          = NULL;
    $this->id_external = NULL;
    $this->id_sort     = NULL;

    $this->name_machine = NULL;
    $this->name_human   = NULL;

    $this->address_email = NULL;

    $this->roles = new c_base_roles();

    $this->is_private = NULL;
    $this->is_locked  = NULL;
    $this->is_deleted = NULL;

    $this->can_manage_roles = NULL;

    $this->date_created = NULL;
    $this->date_changed = NULL;
    $this->date_synced  = NULL;
    $this->date_locked  = NULL;
    $this->date_deleted = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->id_external);
    unset($this->id_sort);

    unset($this->name_machine);
    unset($this->name_human);

    unset($this->address_email);

    unset($this->roles);

    unset($this->is_private);
    unset($this->is_locked);
    unset($this->is_deleted);

    unset($this->can_manage_roles);

    unset($this->date_created);
    unset($this->date_changed);
    unset($this->date_synced);
    unset($this->date_locked);
    unset($this->date_deleted);

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
   * Wrapper to c_base_roles::set_role()
   *
   * @see: c_base_roles::set_role()
   */
  public function set_role($role, $value) {
    return $this->roles->set_role($role, $value);
  }

  /**
   * Wrapper to c_base_roles::get_role()
   *
   * @see: c_base_roles::get_role()
   */
  public function get_role($role) {
    return $this->roles->get_role($role);
  }

  /**
   * Wrapper to c_base_roles::get_roles()
   *
   * @see: c_base_roles::get_roles()
   */
  public function get_roles() {
    return $this->roles->get_roles();
  }

  /**
   * Load user account from the specified database.
   *
   * @param c_base_database &$databae
   *   The already processed and ready to use database object.
   * @param int|string|null|TRUE $user_name_or_id
   *   (optional) If an integer, represents the user id.
   *   If a string, represents the machine name of the user.
   *   If NULL, loads the roles for the current user.
   *   If TRUE, then load the current session user.
   * @param bool $administrative
   *   (optional) When TRUE, loads as an administrative account, which may have access more accounts.
   *   When FALSE, load using normal methods.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE if unable to find roles.
   *   FALSE with error bit set on error.
   */
  public function do_load(&$database, $user_name_or_id = NULL, $administrative = FALSE) {
    return new c_base_return_false();
  }
}
