<?php
/**
 * @file
 * Provides a class for managing system roles.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_roles.php');
require_once('common/base/classes/base_database.php');

/**
 * Standard implementation of c_base_users_user().
 */
class c_standard_users_user extends c_base_users_user {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

  }

  /**
   * Class destructor.
   */
  public function __destruct() {

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
   * @see: c_base_users_user::do_load()
   */
  public function do_load(&$database, $user_name_or_id = NULL, $administrative = FALSE) {
    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($user_name_or_id) && !is_int($user_name_or_id) && $user_name_or_id !== TRUE && !is_string($user_name_or_id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'user_name_or_id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($administrative)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'administrative', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $query_string = 'select id, id_external, id_sort, name_machine, name_human, address_email, is_public, is_system, is_requester, is_drafter, is_editor, is_reviewer, is_insurer, is_financer, is_publisher, is_auditor, is_manager, is_administer, is_private, is_locked, is_deleted, is_roler, date_created, date_changed, date_synced, date_locked, date_deleted, image_original, image_cropped, image_icon, settings ';
    $query_arguments = [];

    if (is_null($user_name_or_id)) {
      $query_string .= 'from v_users_self';
    }
    elseif ($user_name_or_id === TRUE) {
      $query_string .= 'from v_users_self_session';
    }
    else {
      if ($administrative) {
        $query_string .= 'from s_tables.t_users ';
      }
      else {
        $query_string .= 'from v_users ';
      }

      $query_string .= 'where ';

      if (is_int($user_name_or_id)) {
        $query_string .= 'id = $1 ';
        $query_arguments[] = $user_name_or_id;
      }
      else {
        $query_string .= 'name_machine = $1 ';
        $query_arguments[] = $user_name_or_id;
      }
    }

    $query_result = $database->do_query($query_string, $query_arguments);
    unset($query_string);
    unset($query_arguments);

    if (c_base_return::s_has_error($query_result)) {
      $false = c_base_return_error::s_false($query_result->get_error());

      $last_error = $database->get_last_error()->get_value_exact();

      if (!empty($last_error)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{database_error_message}' => $last_error, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::POSTGRESQL_ERROR);
        $false->set_error($error);
        unset($error);
      }
      unset($last_error);

      return $false;
    }

    $columns = $query_result->fetch_row()->get_value();

    if (is_array($columns) && !empty($columns)) {
      $this->roles = new c_base_roles();

      $this->id          = (int) $columns[0];
      $this->id_external = (int) $columns[1];
      $this->id_sort     = (int) $columns[2];

      $this->name_machine = (string) $columns[3];

      $this->name_human = new c_base_users_user_name();
      $name_human_parts = c_base_database::s_explode_array((string) $columns[4])->get_value_exact();
      if (count($name_human_parts) == 6) {
        $this->name_human->set_prefix($name_human_parts[0]);
        $this->name_human->set_first($name_human_parts[1]);
        $this->name_human->set_middle($name_human_parts[2]);
        $this->name_human->set_last($name_human_parts[3]);
        $this->name_human->set_suffix($name_human_parts[4]);
        $this->name_human->set_complete($name_human_parts[5]);
      }

      $this->address_email = new c_base_address_email();
      $address_email_parts = c_base_database::s_explode_array((string) $columns[5])->get_value_exact();
      if (count($address_email_parts) == 3) {
        $this->address_email->set_name($address_email_parts[0]);
        $this->address_email->set_domain($address_email_parts[1]);

        if ($address_email_parts[2] == 'f') {
          $this->address_email->is_private(FALSE);
        }
        else {
          $this->address_email->is_private(TRUE);
        }
      }
      unset($address_email_parts);

      if ($columns[6] == 't') {
        $this->roles->set_role(c_base_roles::PUBLIC, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::PUBLIC, FALSE);
      }

      if ($columns[7] == 't') {
        $this->roles->set_role(c_base_roles::SYSTEM, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::SYSTEM, FALSE);
      }

      if ($columns[8] == 't') {
        $this->roles->set_role(c_base_roles::REQUESTER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::REQUESTER, FALSE);
      }

      if ($columns[9] == 't') {
        $this->roles->set_role(c_base_roles::DRAFTER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::DRAFTER, FALSE);
      }

      if ($columns[10] == 't') {
        $this->roles->set_role(c_base_roles::EDITOR, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::EDITOR, FALSE);
      }

      if ($columns[11] == 't') {
        $this->roles->set_role(c_base_roles::REVIEWER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::REVIEWER, FALSE);
      }

      if ($columns[12] == 't') {
        $this->roles->set_role(c_base_roles::FINANCER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::FINANCER, FALSE);
      }

      if ($columns[13] == 't') {
        $this->roles->set_role(c_base_roles::INSURER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::INSURER, FALSE);
      }

      if ($columns[14] == 't') {
        $this->roles->set_role(c_base_roles::PUBLISHER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::PUBLISHER, FALSE);
      }

      if ($columns[15] == 't') {
        $this->roles->set_role(c_base_roles::AUDITOR, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::AUDITOR, FALSE);
      }

      if ($columns[16] == 't') {
        $this->roles->set_role(c_base_roles::MANAGER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::MANAGER, FALSE);
      }

      if ($columns[17] == 't') {
        $this->roles->set_role(c_base_roles::ADMINISTER, TRUE);
      }
      else {
        $this->roles->set_role(c_base_roles::ADMINISTER, FALSE);
      }

      if ($columns[18] == 't') {
        $this->is_private = TRUE;
      }
      else {
        $this->is_private = FALSE;
      }

      if ($columns[19] == 't') {
        $this->is_locked = TRUE;
      }
      else {
        $this->is_locked = FALSE;
      }

      if ($columns[20] == 't') {
        $this->is_deleted = TRUE;
      }
      else {
        $this->is_deleted = FALSE;
      }

      if ($columns[21] == 't') {
        $this->is_roler = TRUE;
      }
      else {
        $this->is_roler = FALSE;
      }

      $this->date_created = c_base_defaults_global::s_get_timestamp($columns[22], c_base_database::STANDARD_TIMESTAMP_FORMAT)->get_value_exact();
      $this->date_changed = c_base_defaults_global::s_get_timestamp($columns[23], c_base_database::STANDARD_TIMESTAMP_FORMAT)->get_value_exact();
      $this->date_synced = c_base_defaults_global::s_get_timestamp($columns[24], c_base_database::STANDARD_TIMESTAMP_FORMAT)->get_value_exact();
      $this->date_locked = c_base_defaults_global::s_get_timestamp($columns[25], c_base_database::STANDARD_TIMESTAMP_FORMAT)->get_value_exact();
      $this->date_deleted = c_base_defaults_global::s_get_timestamp($columns[26], c_base_database::STANDARD_TIMESTAMP_FORMAT)->get_value_exact();

      if (is_null($columns[27])) {
        $this->image_original = NULL;
      }
      else {
        $this->image_original = (int) $columns[27];
      }

      if (is_null($columns[28])) {
        $this->image_cropped = NULL;
      }
      else {
        $this->image_cropped = (int) $columns[28];
      }

      if (is_null($columns[29])) {
        $this->image_icon = NULL;
      }
      else {
        $this->image_icon = (int) $columns[29];
      }

      if (isset($columns[30])) {
        $this->settings = json_decode($columns[30], TRUE);
      }
      else {
        $this->settings = [];
      }
    }
    else {
      return new c_base_return_false();
    }
    unset($columns);

    return new c_base_return_true();
  }
}
