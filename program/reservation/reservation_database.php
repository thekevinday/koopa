<?php
/**
 * @file
 * Provides reservation database functions.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_session.php');
require_once('common/base/classes/base_database.php');
require_once('common/base/classes/base_access.php');

  /**
 * Build the database connection string.
 *
 * @param c_base_database &$database
 *   The database to connect to.
 * @param array $settings
 *   Custom settings.
 * @param string|null $username
 *   The username string.
 *   If NULL, then the global username is used.
 * @param string|null $password
 *   The password string.
 *   If NULL, then the global password is used.
 *
 * @return c_base_return_status
 *   TRUE on success, FALSE otherwise.
 *   FALSE with error bit set on error.
 */
function reservation_database_string(&$database, $settings, $user_name = NULL, $password = NULL) {
  if (!($database instanceof c_base_database)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_array($settings)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_null($user_name) && !is_string($user_name)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'user_name', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_null($password) && !is_string($password)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'password', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  $connection_string = new c_base_connection_string();
  $connection_string->set_host($settings['database_host']);
  $connection_string->set_port($settings['database_port']);
  $connection_string->set_database($settings['database_name']);

  if (is_null($user_name)) {
    $connection_string->set_user($settings['database_user']);

    if (!is_null($settings['database_password'])) {
      $connection_string->set_password($settings['database_password']);
    }
  }
  else {
    $connection_string->set_user($user_name);

    if (!is_null($password)) {
      $connection_string->set_password($password);
    }
  }

  $connection_string->set_ssl_mode($settings['database_ssl_mode']);
  $connection_string->set_connect_timeout($settings['database_timeout']);

  $database->set_connection_string($connection_string);
  unset($connection_string);

  return new c_base_return_true();
}

/**
 * Connect the database and configure default settings.
 *
 * @param c_base_database &$database
 *   The database to connect to.
 *
 * @return c_base_return_status
 *   TRUE on success, FALSE otherwise.
 *   FALSE with error bit set on error.
 */
function reservation_database_connect(&$database) {
  if (!($database instanceof c_base_database)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  $status = $database->do_connect();
  if (!($status instanceof c_base_return_true)) {
    return $status;
  }
  unset($status);

  // configure default settings.
  $database->do_query('set bytea_output to hex;');
  $database->do_query('set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;');
  $database->do_query('set datestyle to us;');

  return new c_base_return_true();
}

/**
 * Load the user data.
 *
 * This is necessary to load and process session data.
 *
 * @param c_base_database &$database
 *   The database to connect to.
 * @param string $user_name
 *   The name of the user to load.
 * @param array|null $ldap_data
 *   (optional) An array of ldap data (if available).
 *   This is used to auto-populate user information in the database when an account is auto-created.
 *
 * @return c_base_return_array|c_base_return_status
 *   An array of user data is returned on success.
 *   FALSE with error bit set on error.
 */
function reservation_database_get_user_data(&$database, $user_name, $ldap_data = NULL) {
  if (!($database instanceof c_base_database)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_string($user_name)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'user_name', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_null($ldap_data) && !is_array($ldap_data)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ldap_data', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  $parameters = array(
    $user_name,
  );

  $query_result = $database->do_query('select id, id_sort, id_external, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, date_created, date_changed, date_synced, date_locked, settings from v_users_self where id_sort = ascii($1) and name_machine = $1', $parameters);
  unset($parameters);

  if (c_base_return::s_has_error($query_result)) {
    return reservation_error_get_query('database->do_query(select from v_users_self)', __FUNCTION__, $query_result);
  }

  if ($query_result instanceof c_base_database_result && $query_result->number_of_rows()->get_value_exact() > 0) {
    $result = $query_result->fetch_row();
    unset($query_result);

    if (c_base_return::s_has_error($result)) {
      return reservation_error_get_query('database->do_query(select from v_users_self)', __FUNCTION__, $result);
    }

    $result_array = $result->get_value();
    unset($result);

    if (is_array($result_array) && !empty($result_array)) {
      $user_data = array();
      $user_data['id'] = $result_array[0];
      $user_data['id_sort'] = $result_array[1];
      $user_data['id_external'] = $result_array[2];
      $user_data['name_machine'] = $result_array[3];
      $user_data['name_human'] = $result_array[4];
      $user_data['address_email'] = $result_array[5];
      $user_data['is_administer'] = $result_array[6];
      $user_data['is_manager'] = $result_array[7];
      $user_data['is_auditor'] = $result_array[8];
      $user_data['is_publisher'] = $result_array[9];
      $user_data['is_financer'] = $result_array[10];
      $user_data['is_reviewer'] = $result_array[11];
      $user_data['is_editor'] = $result_array[12];
      $user_data['is_drafter'] = $result_array[13];
      $user_data['is_requester'] = $result_array[14];
      $user_data['is_system'] = $result_array[15];
      $user_data['is_public'] = $result_array[16];
      $user_data['is_locked'] = $result_array[17];
      $user_data['is_private'] = $result_array[18];
      $user_data['date_created'] = $result_array[19];
      $user_data['date_changed'] = $result_array[20];
      $user_data['date_synced'] = $result_array[21];
      $user_data['date_locked'] = $result_array[22];
      $user_data['settings'] = json_decode($result_array[23], TRUE);

      unset($result_array);
      return c_base_return_array::s_new($user_data);
    }
    unset($result_array);
  }
  unset($query_result);

  // at this ppint the user account likely does not exist in the database, so create it using any ldap information if available.
  if (is_null($ldap_data)) {
    $query_result = $database->do_query('insert into v_users_self_insert (name_human.first) values (null)');

    if (c_base_return::s_has_error($query_result)) {
      return reservation_error_get_query('database->do_query(insert into v_users_self_insert)', __FUNCTION__, $query_result);
    }
  }
  else {
    $email = explode('@', $ldap_data['mail']);
    $parameters = array(
      $ldap_data['givenname'],
      $ldap_data['sn'],
      $ldap_data['cn'],
      $email[0],
      $email[1],
      $ldap_data['employeenumber'],
    );

    $query_result = $database->do_query('insert into v_users_self_insert (name_human.first, name_human.last, name_human.complete, address_email, id_external) values ($1, $2, $3, ($4, $5, TRUE), $6)', $parameters);

    if (c_base_return::s_has_error($query_result)) {
      return reservation_error_get_query('database->do_query(insert into v_users_self_insert)', __FUNCTION__, $query_result);
    }
  }
  unset($query_result);


  // try loading the user information again now that the user information exists in the database.
  $parameters = array(
    $user_name,
  );

  $query_result = $database->do_query('select id, id_sort, id_external, name_machine, name_human, address_email, is_administer, is_manager, is_auditor, is_publisher, is_financer, is_reviewer, is_editor, is_drafter, is_requester, is_system, is_public, is_locked, is_private, date_created, date_changed, date_synced, date_locked, settings from v_users_self where id_sort = ascii($1) and name_machine = $1', $parameters);
  unset($parameters);

  if (c_base_return::s_has_error($query_result)) {
    return reservation_error_get_query('database->do_query(select from v_users_self)', __FUNCTION__, $query_result);
  }

  if ($query_result instanceof c_base_database_result && $query_result->number_of_rows()->get_value_exact() > 0) {
    $result = $query_result->fetch_row();
    unset($query_result);

    if (c_base_return::s_has_error($result)) {
      return reservation_error_get_query('database->do_query(select from v_users_self)', __FUNCTION__, $result);
    }

    $result_array = $result->get_value();
    unset($result);

    if (is_array($result_array) && !empty($result_array)) {
      $user_data = array();
      $user_data['id'] = $result_array[0];
      $user_data['id_sort'] = $result_array[1];
      $user_data['id_external'] = $result_array[2];
      $user_data['name_machine'] = $result_array[3];
      $user_data['name_human'] = $result_array[4];
      $user_data['address_email'] = $result_array[5];
      $user_data['is_administer'] = $result_array[6];
      $user_data['is_manager'] = $result_array[7];
      $user_data['is_auditor'] = $result_array[8];
      $user_data['is_publisher'] = $result_array[9];
      $user_data['is_financer'] = $result_array[10];
      $user_data['is_reviewer'] = $result_array[11];
      $user_data['is_editor'] = $result_array[12];
      $user_data['is_drafter'] = $result_array[13];
      $user_data['is_requester'] = $result_array[14];
      $user_data['is_system'] = $result_array[15];
      $user_data['is_public'] = $result_array[16];
      $user_data['is_locked'] = $result_array[17];
      $user_data['is_private'] = $result_array[18];
      $user_data['date_created'] = $result_array[19];
      $user_data['date_changed'] = $result_array[20];
      $user_data['date_synced'] = $result_array[21];
      $user_data['date_locked'] = $result_array[22];
      $user_data['settings'] = json_decode($result_array[23], TRUE);

      unset($result_array);
      return c_base_return_array::s_new($user_data);
    }
    unset($result_array);
  }
  unset($query_result);


  return c_base_return_array::s_new(array());
}

/**
 * Loads LDAP information for the given user (if available).
 *
 * @param array $settings
 *   System settings array.
 * @param string $user_name
 *   The user name to load
 *
 * @return c_base_return_array|c_base_return_status
 *   An array of ldap data associated with the given user.
 *   This array may contain error information if there were connection problems to the ldap database.
 *   The array structure is:
 *     'title': a Title for any error that occured.
 *     'message': The detailed ldap error message.
 *     'status': c_base_return_true if there were no problems and c_base_return_false if there were problems.
 *     'data': Any ldap data found for the given user name.
 *   FALSE with the error bit set is returned on error.
 */
function reservation_database_load_ldap_data($settings, $user_name) {
  if (!is_array($settings)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  if (!is_string($user_name)) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'user_name', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  $return_data = array(
    'title' => NULL,
    'message' => NULL,
    'status' => new c_base_return_true(),
    'data' => NULL,
  );

  $ldap = new c_base_ldap();
  $ldap->set_name($settings['ldap_server']);
  #$ldap->set_bind_name('');
  #$ldap->set_bind_password('');
  $connected = $ldap->do_connect();
  if (c_base_return::s_has_error($connected)) {
    $message = $ldap->get_error_message();

    $return_data['title'] = 'Connection Failed';
    $return_data['message'] = $message->get_value_exact();
    $return_data['status'] = new c_base_return_false();
    unset($message);

    return c_base_return_array::s_new($return_data);
  }

  $read = $ldap->do_search($settings['ldap_base_dn'], '(uid=' . $user_name . ')', $settings['ldap_fields']);
  if (c_base_return::s_has_error($read)) {
    $message = $ldap->get_error_message();

    $return_data['title'] = 'Search Failed';
    $return_data['message'] = $message->get_value_exact();
    $return_data['status'] = new c_base_return_false();
    unset($message);

    $ldap->do_disconnect();

    return c_base_return_array::s_new($return_data);
  }

  $entries = $read->get_entry_all();
  if ($entries instanceof c_base_return_array) {
    $entries = $entries->get_value();
  }
  else {
    $entries = array();
  }

  if ($entries['count'] > 0) {
    $return_data['data'] = array(
      'uid' => $user_name,
    );

    foreach ($settings['ldap_fields'] as $ldap_field) {
      $return_data['data'][$ldap_field] = $entries[0][$ldap_field][0];
    }
    unset($ldap_field);
  }
  else {
    $return_data['title'] = 'Username Not Found';
    $return_data['message'] = 'The user \'' . $user_name . '\' was not found.';
    $return_data['status'] = new c_base_return_false();
  }
  unset($entries);

  return c_base_return_array::s_new($return_data);
}

/**
 * Get all roles assigned to the current user.
 *
 * @todo: this might be unecessary as it may be automated via the user view table and sql triggers.
 * @todo: review and update or delete this function as necessary.
 *
 * @param c_base_database &$database
 *   The database object.
 * @param c_base_session &$session
 *   The current session.
 * @param array $settings
 *   The system settings array.
 *
 * @return c_base_return_status
 *   TRUE on success, FALSE otherwise.
 *   FALSE with error bit set is returned on error.
 */
function reservation_get_current_roles(&$database, &$session, $settings) {
  $pre_connected = TRUE;
  $connected = $database->is_connected();
  if ($connected instanceof c_base_return_false) {
    $pre_connected = FALSE;
    $connected = reservation_database_connect($database);
  }

  $roles = new c_base_roles();


  // if there is no session, then assume that this is a public account.
  if (empty($session->get_session_id()->get_value_exact())) {
    unset($pre_connected);

    $roles->set_role(c_base_roles::PUBLIC, TRUE);
    $session->set_setting('roles', $roles);
    unset($roles);

    return new c_base_return_true();
  }


  // if unable to connect to database to retrieve other roles, just return the ppublic role.
  if ($connected instanceof c_base_return_false) {
    unset($pre_connected);

    $roles->set_role(c_base_roles::PUBLIC, TRUE);
    $session->set_setting('roles', $roles);
    unset($roles);

    $connection_string = $database->get_connection_string();
    $database_name = ($connection_string instanceof c_base_connection_string) ? $connection_string->get_database()->get_value_exact() : '';
    unset($connection_string);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':database_name' => $database_name, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::POSTGRESQL_NO_CONNECTION);
    unset($database_name);

    return c_base_return_error::s_false($error);
  }
  unset($connected);


  // assign default roles.
  $roles->set_role(c_base_roles::PUBLIC, FALSE);
  $roles->set_role(c_base_roles::USER, TRUE);


  // load all postgresql roles.
  $result = $database->do_query('SELECT role_name FROM information_schema.enabled_roles');
  if ($result instanceof c_base_database_result) {
    $rows = $result->fetch_all()->get_value_exact();

    foreach ($rows as $row) {
      if (!array_key_exists('role_name', $row)) {
        continue;
      }

      switch ($row['role_name']) {
        case 'r_' . $settings['database_name'] . '_requester':
          $roles->set_role(c_base_roles::REQUESTER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_drafter':
          $roles->set_role(c_base_roles::DRAFTER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_editor':
          $roles->set_role(c_base_roles::EDITOR, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_reviewer':
          $roles->set_role(c_base_roles::REVIEWER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_financer':
          $roles->set_role(c_base_roles::FINANCER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_insurer':
          $roles->set_role(c_base_roles::INSURER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_publisher':
          $roles->set_role(c_base_roles::PUBLISHER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_auditor':
          $roles->set_role(c_base_roles::AUDITOR, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_manager':
          $roles->set_role(c_base_roles::MANAGER, TRUE);
          break;
        case 'r_' . $settings['database_name'] . '_administer':
          $roles->set_role(c_base_roles::ADMINISTER, TRUE);
          break;
      }
    }
    unset($row);
    unset($rows);
  }
  unset($result);

  if (!$pre_connected) {
    $database->do_disconnect();
  }
  unset($pre_connected);

  $session->set_setting('roles', $roles);
  unset($roles);

  return new c_base_return_true();
}

/**
 * Builds a return object for a query.
 *
 * This functions is provided to simplify the return of specific code.
 * Error handling is not performed, instead simple failsafes are used.
 *
 * @param string $operation_name
 *   The name of the operation, which is generally something like: database->do_query.
 * @param string $function_name
 *   The name of the function.
 *   The caller should usually use __FUNCTION__ here.
 * @param c_base_return $result
 *   The query return result.
 *
 * @return c_base_error
 *   A generated oepration failure error.
 */
function reservation_error_get_query($operation_name, $function_name, $result) {
  if (!is_string($operation_name)) {
    $operation_name = '';
  }

  if (!is_string($function_name)) {
    $function_name = '';
  }

  $failure = new c_base_return_false();
  $found_errors = FALSE;
  if ($result instanceof c_base_return) {
    $errors = $result->get_error();
    if (is_array($errors)) {
      $found_errors = TRUE;

      foreach ($errors as $error) {
        $failure->set_error($error);
      }
      unset($error);
    }
    unset($errors);
  }

  if (!$found_errors) {
    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => $operation_name, ':function_name' => $function_name)), i_base_error_messages::OPERATION_FAILURE);
    $failure->set_error($error);
    unset($error);
  }
  unset($found_errors);

  return $failure;
}
