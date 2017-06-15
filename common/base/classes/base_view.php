<?php
/**
 * @file
 * Provides a class for managing view objects.
 *
 * This is for providing a common api for loading views from the database.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_database.php');
require_once('common/base/classes/base_array.php');

/**
 * A generic class for providing classes that support a loading and processing specific view results.
 *
 * @require class c_base_array
 */
class c_base_view extends c_base_array {

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
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Provides basic database load access for whose data is to be stored in this class.
   *
   * @param c_base_database &$database
   *   The database object to load from.
   * @param string|null $query
   *   (optional) The query string to execute.
   *   If NULL, then the class default will be used.
   * @param array $arguments
   *   (optional) An array containing arguments to be passed along with the query string.
   * @param string|null $conditions
   *   (optional) This can be a string of additional conditions to append to the default query string.
   *   This is appended to the query string along with ' where '.
   *   If a where condition is already specified in the query string then this will result in a query error.
   * @param int|null $limit
   *   (optional) This can be an integer to append to the default query string as a query limit.
   *   This is appended to the query string along with ' limit '.
   *   If a limit condition is already specified in the query string then this will result in a query error.
   * @param int|null $offset
   *   (optional) This can be an integer to append to the default query string as a query offset.
   *   This is appended to the query string along with ' offset '.
   *   If a offset condition is already specified in the query string then this will result in a query error.
   * @param int|null $group_by
   *   (optional) This can be an integer to append to the default query string as a query group by.
   *   This is appended to the query string along with ' group by '.
   *   If a group by condition is already specified in the query string then this will result in a query error.
   * @param int|null $order_by
   *   (optional) This can be an integer to append to the default query string as a query order by.
   *   This is appended to the query string along with ' order by '.
   *   If a order by condition is already specified in the query string then this will result in a query error.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE without error bit set is returned if nothing was done.
   *   FALSE with error bit set is returned on error.
   */
  public function pull(&$database, $query = NULL, $arguments = array(), $conditions = NULL, $limit = NULL, $offset = NULL, $group_by = NULL, $order_by = NULL) {
    if (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($query) && !is_string($query)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'query', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($arguments)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'arguments', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($conditions) && !is_string($conditions)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'conditions', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($limit) && !is_string($limit)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'limit', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($offset) && !is_string($offset)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'offset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($group_by) && !is_string($group_by)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'group_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($order_by) && !is_string($order_by)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'order_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // return FALSE because this is a stub function and it does nothing.
    return new c_base_return_false();
  }
}
