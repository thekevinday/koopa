<?php
/**
 * @file
 * Provides the standard database class.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_database.php');

/**
 * Standard implementation of c_base_database().
 */
class c_standard_database extends c_base_database {
  /**
   * Write a log to the database, associated with the current user.
   */
  public function do_log_user($log_type, $response_code, $data = array()) {
    if (!is_int($log_type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'log_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($response_code)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'response_code', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $query_string = 'insert into v_log_users_self_insert (log_title, log_type, log_type_sub, log_severity, log_facility, request_client, response_code, log_details)';
    $query_string .= ' values ($1, $2, $3, $4, $5, ($6, $7, $8), $9, $10)';

    $query_parameters = array();
    $query_parameters[5] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $query_parameters[6] = isset($_SERVER['REMOTE_PORT']) && is_numeric($_SERVER['REMOTE_PORT']) ? (int) $_SERVER['REMOTE_PORT'] : 0;
    $query_parameters[7] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ;
    $query_parameters[8] = $response_code;

    if ($log_type === c_base_log::TYPE_CONNECT) {
      $expires = NULL;
      if (isset($data['expires']) && is_int($data['expires'])) {
        $expires = $data['expires'];
      }

      $query_parameters[0] = "Logging in to the system.";
      $query_parameters[1] = c_base_log::TYPE_SESSION;
      $query_parameters[2] = c_base_log::TYPE_CONNECT;
      $query_parameters[3] = c_base_error::SEVERITY_INFORMATIONAL;
      $query_parameters[4] = c_base_defaults_global::LOG_FACILITY;
      $query_parameters[9] = json_encode(array('expires' => $expires));

      unset($expires);
    }
    elseif ($log_type === c_base_log::TYPE_DISCONNECT) {
      $query_parameters[0] = "Logging out of the system.";
      $query_parameters[1] = c_base_log::TYPE_SESSION;
      $query_parameters[2] = c_base_log::TYPE_DISCONNECT;
      $query_parameters[3] = c_base_error::SEVERITY_INFORMATIONAL;
      $query_parameters[4] = c_base_defaults_global::LOG_FACILITY;
      $query_parameters[9] = NULL;
    }
    elseif ($log_type === c_base_log::TYPE_CREATE) {
      $query_parameters[0] = "Created the user account.";
      $query_parameters[1] = c_base_log::TYPE_CREATE;
      $query_parameters[2] = c_base_log::TYPE_NONE;
      $query_parameters[3] = c_base_error::SEVERITY_INFORMATIONAL;
      $query_parameters[4] = c_base_defaults_global::LOG_FACILITY;
      $query_parameters[9] = NULL;
    }
    elseif ($log_type === c_base_log::TYPE_FAILURE) {
      $user_name = NULL;
      if (isset($data['user_name']) && is_string($data['user_name'])) {
        $user_name = $data['user_name'];
      }

      $query_parameters[0] = "Failed to login as the user ':{user_name}'.";
      $query_parameters[1] = c_base_log::TYPE_CONNECT;
      $query_parameters[2] = c_base_log::TYPE_FAILURE;
      $query_parameters[3] = c_base_error::SEVERITY_NOTICE;
      $query_parameters[4] = c_base_defaults_global::LOG_FACILITY;
      $query_parameters[9] = json_encode(array('user_name' => $user_name));

      unset($user_name);
    }
    else {
      return new c_base_return_false();
    }

    ksort($query_parameters);

    $query_result = $this->do_query($query_string, $query_parameters);
    if (c_base_return::s_has_error($query_result)) {
      $last_error = $this->get_last_error()->get_value_exact();

      $false = c_base_return_error::s_false($query_result->get_error());
      unset($query_result);

      if (!empty($last_error)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{database_error_message}' => $last_error, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_ERROR);
        $false->set_error($error);
      }
      unset($last_error);

      return $false;
    }
    unset($query_result);

    return new c_base_return_true();
  }
}
