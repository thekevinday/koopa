<?php
/**
 * @file
 * Provides reservation database functions.
 */
  #require_once('../../common/base/classes/base_error.php');
  #require_once('../../common/base/classes/base_return.php');
  #require_once('../../common/base/classes/base_session.php');
  #require_once('../../common/base/classes/base_database.php');

  /**
   * Build the database connection string.
   *
   * @param c_base_database &$database
   *   The database to connect to.
   * @param array $settings
   *   Custom settings.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set on error.
   */
  function reservation_database_string(&$database, $settings) {
    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'database', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'settings', ':function_name' => __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $connection_string = new c_base_connection_string();
    $connection_string->set_host($settings['database_host']);
    $connection_string->set_port($settings['database_port']);
    $connection_string->set_database_name($settings['database_name']);
    $connection_string->set_user($settings['database_user']);

    if (!is_null($settings['database_password'])) {
      $connection_string->set_password($settings['database_password']);
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
    $database->do_query('set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,drafters,users,public;');
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

    $user_data = array(
      'id_user' => NULL,
      'id_sort' => $id_sort,
    );

    $id_sort = (int) ord($user_name[0]);

    $parameters = array(
      $id_sort,
      $user_name,
    );

    $query_result = $database->do_query('select id, id_sort, id_external, name_human, address_email, is_private, is_locked, date_created, date_changed, settings from v_users_self where id_sort = $1 and name_machine like $2', $parameters);
    unset($parameters);

    if ($query_result instanceof c_base_database_result) {
      if ($query_result->number_of_rows()->get_value_exact() > 0) {
        $result = $query_result->fetch_row();
        if (!($result instanceof c_base_return_false)) {
          $result_array = $result->get_value();
          if (is_array($result_array) && !empty($result_array)) {
            $user_data['id_user'] = $result_array[0];
            $user_data['id_sort'] = $result_array[1];
            $user_data['id_external'] = $result_array[2];
            $user_data['name_human'] = $result_array[3];
            $user_data['address_email'] = $result_array[4];
            $user_data['is_private'] = $result_array[5];
            $user_data['is_locked'] = $result_array[6];
            $user_data['date_created'] = $result_array[7];
            $user_data['date_changed'] = $result_array[8];
            $user_data['settings'] = json_decode($result_array[9], TRUE);
          }
        }
      }
    }
    unset($query_result);

    if (is_null($user_data['id_user'])) {
      if (is_null($ldap_data)) {
        $parameters = array(
          $id_sort,
        );

        $query_result = $database->do_query('insert into v_users_self_insert (id_sort, name_machine) values ($1, user)', $parameters);
        unset($query_result);
        unset($parameters);
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
        $query_result = $database->do_query('insert into v_users_self_insert (id_sort, name_machine, name_human.first, name_human.last, name_human.complete, address_email, id_external) values (' . $id_sort . ', user, $1, $2, $3, ($4, $5, TRUE), $6)', $parameters);
        unset($query_result);
      }

      $parameters = array(
        $id_sort,
        $user_name,
      );

      $query_result = $database->do_query('select id, id_sort, id_external, name_human, address_email, is_private, is_locked, date_created, date_changed, settings from v_users_self where id_sort = $1 and name_machine = $2');
      unset($parameters);

      if ($query_result instanceof c_base_database_result) {
        if ($query_result->number_of_rows()->get_value_exact() > 0) {
          $result = $query_result->fetch_row();
          if (!($result instanceof c_base_return_false)) {
            $result_array = $result->get_value();
            if (is_array($result_array) && !empty($result_array)) {
              $result_array = $result->get_value();
              $user_data['id_user'] = $result_array[0];
              $user_data['id_sort'] = $result_array[1];
              $user_data['id_external'] = $result_array[2];
              $user_data['name_human'] = $result_array[3];
              $user_data['address_email'] = $result_array[4];
              $user_data['is_private'] = $result_array[5];
              $user_data['is_locked'] = $result_array[6];
              $user_data['date_created'] = $result_array[7];
              $user_data['date_changed'] = $result_array[8];
              $user_data['settings'] = json_decode($result_array[9], TRUE);
            }
          }
        }
      }
      unset($query_result);
    }

    return c_base_return_array::s_new($user_data);
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
