<?php
/**
 * @file
 * Provides a class for managing system roles.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_address.php');

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
  protected $is_roler;

  protected $date_created;
  protected $date_changed;
  protected $date_synced;
  protected $date_locked;
  protected $date_deleted;

  protected $image_original;
  protected $image_cropped;
  protected $image_icon;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id          = 0;
    $this->id_external = 0;
    $this->id_sort     = 0;

    $this->name_machine = '';
    $this->name_human   = new c_base_users_user_name();

    $this->address_email = new c_base_address_email();

    $this->roles = new c_base_roles();

    $this->is_private = TRUE;
    $this->is_locked  = FALSE;
    $this->is_deleted = FALSE;
    $this->is_roler   = FALSE;

    $this->date_created = NULL;
    $this->date_changed = NULL;
    $this->date_synced  = NULL;
    $this->date_locked  = NULL;
    $this->date_deleted = NULL;

    $this->image_original = NULL;
    $this->image_cropped  = NULL;
    $this->image_icon     = NULL;
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
    unset($this->is_roler);

    unset($this->date_created);
    unset($this->date_changed);
    unset($this->date_synced);
    unset($this->date_locked);
    unset($this->date_deleted);

    unset($this->image_original);
    unset($this->image_cropped);
    unset($this->image_icon);

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
   * Set the user id.
   *
   * @param int $id
   *   The user id.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id($id) {
    if (!is_int($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Set the external user id.
   *
   * @param int $id_external
   *   The external user id.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_external($id_external) {
    if (!is_int($id_external)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_external', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_external = $id_external;
    return new c_base_return_true();
  }

  /**
   * Set the sort id.
   *
   * @param int $id_sort
   *   The sort id.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_sort($id_sort) {
    if (!is_int($id_sort)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_sort', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_sort = $id_sort;
    return new c_base_return_true();
  }

  /**
   * Set the user machine name.
   *
   * @param int $name_machine
   *   The user machine name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_name_machine($name_machine) {
    if (!is_string($name_machine)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name_machine', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name_machine = $name_machine;
    return new c_base_return_true();
  }

  /**
   * Set the user human name.
   *
   * @param c_base_users_user_name $name_machine
   *   The user human name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_name_human($name_human) {
    if (!($name_human instanceof c_base_users_user_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name_human', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name_human = clone($name_human);
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned email address.
   *
   * @param c_base_address_email $address_email
   *   The e-mail address associated with this account.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_address_email($address_email) {
    if (!($address_email instanceof c_base_address_email)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'address_email', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->address_email = clone($address_email);
    return new c_base_return_true();
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
   * Set the created date.
   *
   * @param int|float $date_created
   *   The created date.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_created($date_created) {
    if (!is_int($date_created) && !is_float($date_created)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_created', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_created = $date_created;
    return new c_base_return_true();
  }

  /**
   * Set the changed date.
   *
   * @param int|float $date_changed
   *   The changed date.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_changed($date_changed) {
    if (!is_int($date_changed) && !is_float($date_changed)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_changed', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_changed = $date_changed;
    return new c_base_return_true();
  }

  /**
   * Set the synced date.
   *
   * @param int|float $date_synced
   *   The synced date.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_synced($date_synced) {
    if (!is_int($date_synced) && !is_float($date_synced)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_synced', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_synced = $date_synced;
    return new c_base_return_true();
  }

  /**
   * Set the locked date.
   *
   * @param int|float $date_locked
   *   The locked date.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_locked($date_locked) {
    if (!is_int($date_locked) && !is_float($date_locked)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_locked', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_locked = $date_locked;
    return new c_base_return_true();
  }

  /**
   * Set the deleted date.
   *
   * @param int|float $date_deleted
   *   The deleted date.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date_deleted($date_deleted) {
    if (!is_int($date_deleted) && !is_float($date_deleted)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'date_deleted', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date_deleted = $date_deleted;
    return new c_base_return_true();
  }

  /**
   * Set the original image.
   *
   * @param int|null $image_original
   *   The numeric id representing the image in the database.
   *   Set to NULL to remove the image.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_image_original($image_original) {
    if (!is_int($image_original) && !is_null($image_original)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'image_original', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->image_original = $image_original;
    return new c_base_return_true();
  }

  /**
   * Set the cropped image.
   *
   * @param int|null $image_cropped
   *   The numeric id representing the image in the database.
   *   Set to NULL to remove the image.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_image_cropped($image_cropped) {
    if (!is_int($image_cropped) && !is_null($image_cropped)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'image_cropped', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->image_cropped = $image_cropped;
    return new c_base_return_true();
  }

  /**
   * Set the icon image.
   *
   * @param int|null $image_icon
   *   The numeric id representing the image in the database.
   *   Set to NULL to remove the image.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_image_icon($image_icon) {
    if (!is_int($image_icon) && !is_null($image_icon)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'image_icon', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->image_icon = $image_icon;
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned user id.
   *
   * @return c_base_return_int
   *   User ID integer on success.
   *   0 with error bit set is returned on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      $this->id = 0;
    }

    return c_base_return_int::s_new($this->id);
  }

  /**
   * Get the currently assigned external user id.
   *
   * @return c_base_return_int
   *   External User ID integer on success.
   *   0 with error bit set is returned on error.
   */
  public function get_id_external() {
    if (!is_int($this->id_external)) {
      $this->id_external = 0;
    }

    return c_base_return_int::s_new($this->id_external);
  }

  /**
   * Get the currently assigned sort id.
   *
   * @return c_base_return_int
   *   Sort integer on success.
   *   0 with error bit set is returned on error.
   */
  public function get_id_sort() {
    if (!is_int($this->id_sort)) {
      $this->id_sort = 0;
    }

    return c_base_return_int::s_new($this->id_sort);
  }

  /**
   * Get the currently assigned machine name.
   *
   * @return c_base_return_string
   *   Machine name on success.
   *   An empty string with error bit set is returned on error.
   */
  public function get_name_machine() {
    if (!is_string($this->name_machine)) {
      $this->name_machine = '';
    }

    return c_base_return_string::s_new($this->name_machine);
  }

  /**
   * Get the currently assigned human name.
   *
   * @return c_base_users_user_name
   *   Human name on success.
   *   Error bit is set is on error.
   */
  public function get_name_human() {
    if (!($this->name_human instanceof c_base_users_user_name)) {
      $this->name_human = new c_base_users_user_name();
    }

    return clone($this->name_human);
  }

  /**
   * Get the currently assigned email address.
   *
   * @return c_base_return_null|c_base_return_array
   *   E-mail address array on success.
   *   Error bit is set is on error.
   */
  public function get_address_email() {
    if (is_null($this->address_email)) {
      $this->address_email = new c_base_address_email();
    }

    return clone($this->address_email);
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
   * Get the currently assigned created date.
   *
   * @return c_base_return_null|c_base_return_int|c_base_return_float
   *   Date created on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_date_created() {
    if (is_null($this->date_created)) {
      return new c_base_return_null();
    }

    if (is_float($this->date_created)) {
      return c_base_return_float::s_new($this->date_created);
    }

    return c_base_return_int::s_new($this->date_created);
  }

  /**
   * Get the currently assigned changed date.
   *
   * @return c_base_return_null|c_base_return_int|c_base_return_float
   *   Date changed on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_date_changed() {
    if (is_null($this->date_changed)) {
      return new c_base_return_null();
    }

    if (is_float($this->date_changed)) {
      return c_base_return_float::s_new($this->date_changed);
    }

    return c_base_return_int::s_new($this->date_changed);
  }

  /**
   * Get the currently assigned synced date.
   *
   * @return c_base_return_null|c_base_return_int|c_base_return_float
   *   Date synced on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_date_synced() {
    if (is_null($this->date_synced)) {
      return new c_base_return_null();
    }

    if (is_float($this->date_synced)) {
      return c_base_return_float::s_new($this->date_synced);
    }

    return c_base_return_int::s_new($this->date_synced);
  }

  /**
   * Get the currently assigned locked date.
   *
   * @return c_base_return_null|c_base_return_int|c_base_return_float
   *   Date locked on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_date_locked() {
    if (is_null($this->date_locked)) {
      return new c_base_return_null();
    }

    if (is_float($this->date_locked)) {
      return c_base_return_float::s_new($this->date_locked);
    }

    return c_base_return_int::s_new($this->date_locked);
  }

  /**
   * Get the currently assigned deleted date.
   *
   * @return c_base_return_null|c_base_return_int|c_base_return_float
   *   Date deleted on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_date_deleted() {
    if (is_null($this->date_deleted)) {
      return new c_base_return_null();
    }

    if (is_float($this->date_deleted)) {
      return c_base_return_float::s_new($this->date_deleted);
    }

    return c_base_return_int::s_new($this->date_deleted);
  }

  /**
   * Get the currently assigned original image.
   *
   * @return c_base_return_null|c_base_return_int
   *   Image ID on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_image_original() {
    if (is_null($this->image_original)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->image_original);
  }

  /**
   * Get the currently assigned cropped image.
   *
   * @return c_base_return_null|c_base_return_int
   *   Image ID on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_image_cropped() {
    if (is_null($this->image_cropped)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->image_cropped);
  }

  /**
   * Get the currently assigned icon image.
   *
   * @return c_base_return_null|c_base_return_int
   *   Image ID on success.
   *   NULL without error bit is returned if not defined.
   *   NULL with error bit set is returned on error.
   */
  public function get_image_icon() {
    if (is_null($this->image_icon)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->image_icon);
  }

  /**
   * Get the is private setting.
   *
   * @param bool|null $is_private
   *   When a boolean, this is assigned as the current is private setting.
   *   When NULL, the private setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_private is NULL, is content boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_private($is_private = NULL) {
    if (!is_null($is_private) && !is_bool($is_private)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_private', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_private)) {
      if (!is_bool($this->is_private)) {
        $this->is_private = TRUE;
      }

      return c_base_return_bool::s_new($this->is_private);
    }

    $this->is_private = $is_private;
    return new c_base_return_true();
  }

  /**
   * Get the is locked setting.
   *
   * @param bool|null $is_locked
   *   When a boolean, this is assigned as the current is locked setting.
   *   When NULL, the locked setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_locked is NULL, is content boolean setting on success.
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

      return c_base_return_bool::s_new($this->is_locked);
    }

    $this->is_locked = $is_locked;
    return new c_base_return_true();
  }

  /**
   * Get the is deleted setting.
   *
   * @param bool|null $is_deleted
   *   When a boolean, this is assigned as the current is deleted setting.
   *   When NULL, the deleted setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_deleted is NULL, is content boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_deleted($is_deleted = NULL) {
    if (!is_null($is_deleted) && !is_bool($is_deleted)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_deleted', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_deleted)) {
      if (!is_bool($this->is_deleted)) {
        $this->is_deleted = FALSE;
      }

      return c_base_return_bool::s_new($this->is_deleted);
    }

    $this->is_deleted = $is_deleted;
    return new c_base_return_true();
  }


  /**
   * Get the is roler setting.
   *
   * A "roler" refers to a user who is allowed to manage roles.
   *
   * @param bool|null $is_roler
   *   When a boolean, this is assigned as the current can manage roles setting.
   *   When NULL, the can manage roles setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_roler is NULL, is content boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_roler($is_roler = NULL) {
    if (!is_null($is_roler) && !is_bool($is_roler)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'is_roler', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_roler)) {
      if (!is_bool($this->is_roler)) {
        $this->is_roler = FALSE;
      }

      return c_base_return_bool::s_new($this->is_roler);
    }

    $this->is_roler = $is_roler;
    return new c_base_return_true();
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
   *   FALSE with error bit set is returned on error.
   */
  public function do_load(&$database, $user_name_or_id = NULL, $administrative = FALSE) {
    return new c_base_return_false();
  }
}

/**
 * A class for managing human name of a user.
 */
class c_base_users_user_name extends c_base_return {
  private $prefix;
  private $first;
  private $middle;
  private $last;
  private $suffix;
  private $complete;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->prefix   = NULL;
    $this->first    = NULL;
    $this->middle   = NULL;
    $this->last     = NULL;
    $this->suffix   = NULL;
    $this->complete = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->prefix);
    unset($this->first);
    unset($this->middle);
    unset($this->last);
    unset($this->suffix);
    unset($this->complete);

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
   * Set the prefix name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_prefix($prefix) {
    if (!is_string($prefix)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'prefix', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->prefix = $prefix;
    return new c_base_return_true();
  }

  /**
   * Set the first name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_first($first) {
    if (!is_string($first)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'first', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->first = $first;
    return new c_base_return_true();
  }

  /**
   * Set the middle name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_middle($middle) {
    if (!is_string($middle)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'middle', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->middle = $middle;
    return new c_base_return_true();
  }

  /**
   * Set the last name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_last($last) {
    if (!is_string($last)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'last', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->last = $last;
    return new c_base_return_true();
  }

  /**
   * Set the suffix name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_suffix($suffix) {
    if (!is_string($suffix)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'suffix', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->suffix = $suffix;
    return new c_base_return_true();
  }

  /**
   * Set the complete name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_complete($complete) {
    if (!is_string($complete)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'complete', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->complete = $complete;
    return new c_base_return_true();
  }

  /**
   * Set the prefix name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_prefix() {
    if (!is_string($this->prefix)) {
      $this->prefix = '';
    }

    return c_base_return_string::s_new($this->prefix);
  }

  /**
   * Set the first name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_first() {
    if (!is_string($this->first)) {
      $this->first = '';
    }

    return c_base_return_string::s_new($this->first);
  }

  /**
   * Set the middle name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_middle() {
    if (!is_string($this->middle)) {
      $this->middle = '';
    }

    return c_base_return_string::s_new($this->middle);
  }

  /**
   * Set the last name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_last() {
    if (!is_string($this->last)) {
      $this->last = '';
    }

    return c_base_return_string::s_new($this->last);
  }

  /**
   * Set the suffix name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_suffix() {
    if (!is_string($this->suffix)) {
      $this->suffix = '';
    }

    return c_base_return_string::s_new($this->suffix);
  }

  /**
   * Set the complete name.
   *
   * @return c_base_return_string
   *   String on success.
   *   An empty string with error bit set is returned on error.
   */
  public function get_complete() {
    if (!is_string($this->complete)) {
      $this->complete = '';
    }

    return c_base_return_string::s_new($this->complete);
  }
}
