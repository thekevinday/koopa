<?php
/**
 * @file
 * Provides a class for managing files.
 *
 * This is primarily intended to be used to store file data (of any format) prior to theme output.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_mime.php');

/**
 * A generic container for files.
 */
class c_base_file extends c_base_return {
  protected $id;
  protected $id_creator;
  protected $id_creator_session;
  protected $id_type;
  protected $id_group;

  protected $name_machine;
  protected $name_human;
  protected $name_extension;

  protected $field_size;
  protected $field_width;
  protected $field_height;

  protected $is_private;
  protected $is_locked;
  protected $is_deleted;
  protected $is_system;
  protected $is_user;

  protected $date_created;
  protected $date_changed;
  protected $date_locked;
  protected $date_deleted;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id                 = NULL;
    $this->id_creator         = NULL;
    $this->id_creator_session = NULL;
    $this->id_type            = NULL;
    $this->id_group           = NULL;

    $this->name_machine   = NULL;
    $this->name_human     = NULL;
    $this->name_extension = NULL;

    $this->field_size   = NULL;
    $this->field_width  = NULL;
    $this->field_height = NULL;

    $this->date_created = NULL;
    $this->date_changed = NULL;
    $this->date_locked  = NULL;
    $this->date_deleted = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->id_creator);
    unset($this->id_creator_session);
    unset($this->id_type);
    unset($this->id_group);

    unset($this->name_machine);
    unset($this->name_human);
    unset($this->name_extension);

    unset($this->field_size);
    unset($this->field_width);
    unset($this->field_height);

    unset($this->date_created);
    unset($this->date_changed);
    unset($this->date_locked);
    unset($this->date_deleted);

    parent::__destruct();
  }

  /**
   * Assign the file id.
   *
   * @param int $id
   *   The file id.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
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
   * Assign the file creator.
   *
   * @param int $id_creator
   *   The file creator id.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_creator($id_creator) {
    if (!is_int($id_creator)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_creator', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_creator = $id_creator;
    return new c_base_return_true();
  }

  /**
   * Assign the file creator (session).
   *
   * @param int $id_creator_session
   *   The file creator id.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_creator_session($id_creator_session) {
    if (!is_int($id_creator_session)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_creator_session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_creator_session = $id_creator_session;
    return new c_base_return_true();
  }

  /**
   * Assign a mime type.
   *
   * @param int $id_type
   *   The mime type code.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_type($id_type) {
    if (!is_int($id_type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_type = $id_type;
    return new c_base_return_true();
  }

  /**
   * Assign the file group id.
   *
   * @param int|null $id_group
   *   The group id or NULL if no group is assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_group($id_group) {
    if (!is_null($id_group) && !is_int($id_group)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id_group', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_group = $id_group;
    return new c_base_return_true();
  }

  /**
   * Set the file machine name.
   *
   * @param int $name_machine
   *   The file machine name.
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
   * Set the file human name.
   *
   * @param int $name_human
   *   The file human name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_name_human($name_human) {
    if (!is_string($name_human)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name_human', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name_human = $name_human;
    return new c_base_return_true();
  }

  /**
   * Set the file extension.
   *
   * @param int $name_extension
   *   The file extension.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_name_extension($name_extension) {
    if (!is_string($name_extension)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name_extension', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name_extension = $name_extension;
    return new c_base_return_true();
  }

  /**
   * Assign the file size.
   *
   * @param int $field_size
   *   The file size number.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_field_size($field_size) {
    if (!is_int($field_size)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'field_size', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->field_size = $field_size;
    return new c_base_return_true();
  }

  /**
   * Assign the file width.
   *
   * @param int|null $field_width
   *   The file width (generally applies to images only).
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_field_width($field_width) {
    if (!is_null($field_width) && !is_int($field_width)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'field_width', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->field_width = $field_width;
    return new c_base_return_true();
  }

  /**
   * Assign the file height.
   *
   * @param int|null $field_height
   *   The file height (generally applies to images only).
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_field_height($field_height) {
    if (!is_null($field_height) && !is_int($field_height)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'field_height', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->field_height = $field_height;
    return new c_base_return_true();
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
   * Get the file id.
   *
   * @return c_base_return_int|c_base_return_status
   *   The file id.
   *   FALSE is returned if no type is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->id);
  }

  /**
   * Get file creator id.
   *
   * @return c_base_return_int|c_base_return_status
   *   The file creator user id.
   *   FALSE is returned if no type is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id_creator() {
    if (!is_int($this->id_creator)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->id_creator);
  }

  /**
   * Get the file id_creator_session.
   *
   * @return c_base_return_int|c_base_return_status
   *   The file creator (session).
   *   FALSE is returned if no type is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id_creator_session() {
    if (!is_int($this->id_creator_session)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->id_creator_session);
  }

  /**
   * Get the mime type associated with this file.
   *
   * @return c_base_return_int|c_base_return_status
   *   The mime type code.
   *   FALSE is returned if no type is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id_type() {
    if (!is_int($this->id_type)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->id_type);
  }

  /**
   * Get the file group id.
   *
   * @return c_base_return_int|c_base_return_null
   *   The group id.
   *   FALSE is returned if no group id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id_group() {
    if (!is_int($this->id_group)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->id_group);
  }

  /**
   * Get the machine name associated with the file.
   *
   * @return c_base_return_string|c_base_return_status
   *   The machine name.
   *   FALSE is returned if no machine name is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_name_machine() {
    if (!is_string($this->name_machine)) {
      return new c_base_return_false();
    }

    return c_base_return_string::s_new($this->name_machine);
  }

  /**
   * Get the human name associated with the file.
   *
   * @return c_base_return_string|c_base_return_status
   *   The human name.
   *   FALSE is returned if no machine name is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_name_human() {
    if (!is_string($this->name_human)) {
      return new c_base_return_false();
    }

    return c_base_return_string::s_new($this->name_human);
  }

  /**
   * Get the file name extension associated with the file.
   *
   * @return c_base_return_string|c_base_return_status
   *   The file name extension.
   *   FALSE is returned if no machine name is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_name_extension() {
    if (!is_string($this->name_extension)) {
      return new c_base_return_false();
    }

    return c_base_return_string::s_new($this->name_extension);
  }

  /**
   * Get the file size.
   *
   * @return c_base_return_int|c_base_return_status
   *   The file size.
   *   FALSE is returned if no file size is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_field_size() {
    if (!is_int($this->field_size)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->field_size);
  }

  /**
   * Get the file width (generally for images).
   *
   * @return c_base_return_int|c_base_return_null|c_base_return_status
   *   The file width.
   *   NULL is returned if no file width is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_field_width() {
    if (!is_int($this->field_width)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->field_width);
  }

  /**
   * Get the file height (generally for images).
   *
   * @return c_base_return_int|c_base_return_null|c_base_return_status
   *   The file height.
   *   NULL is returned if no file height is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_field_height() {
    if (!is_int($this->field_height)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->field_height);
  }

  /**
   * Gets the date created setting.
   *
   * @return c_base_return_float|c_base_return_status
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
   * @return c_base_return_float|c_base_return_status
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
   * @return c_base_return_float|c_base_return_status
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
   * @return c_base_return_float|c_base_return_status
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
}
