<?php
/**
 * @file
 * Provides a classes for managing the view: v_log_users_self.
 *
 * This is for providing a common api for loading views from the database.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_view.php');

/**
 * A specific class for processing view results: v_log_users_self.
 *
 * @require class c_base_rfc_string
 */
class c_view_log_users_self extends c_base_view {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id = NULL;
  }

  /**
   * Class constructor.
   */
  public function __destruct() {
    unset($this->id);

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
    $result = parent::pull($database, $query, $arguments, $conditions, $limit, $offset, $group_by, $order_by);
    if (c_base_return::s_has_error($result)) {
      return $result;
    }
    unset($result);

    $query_string = '';
    if (is_null($query)) {
    }
    else {
      $query_string = $query;
    }

  }
}

/**
 * A specific class for storing view results: v_log_users_self.
 */
class c_view_log_users_self extends c_base_return {
  protected $id;
  protected $id_user;

  protected $log_title;
  protected $log_type;
  protected $log_type_sub;
  protected $log_severity;
  protected $log_facility;
  protected $log_details;
  protected $log_date;

  protected $request_client;
  protected $response_code;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id      = NULL;
    $this->id_user = NULL;

    $this->log_title    = NULL;
    $this->log_type     = NULL;
    $this->log_type_sub = NULL;
    $this->log_severity = NULL;
    $this->log_facility = NULL;
    $this->log_details  = NULL;
    $this->log_date     = NULL;

    $this->request_client = NULL;
    $this->response_code  = NULL;
  }

  /**
   * Class constructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->id_user);

    unset($this->log_title);
    unset($this->log_type);
    unset($this->log_type_sub);
    unset($this->log_severity);
    unset($this->log_facility);
    unset($this->log_details);
    unset($this->log_date);

    unset($this->request_client);
    unset($this->response_code);

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
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Set the log id.
   *
   * @param int $id
   *   The log id.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id($id) {
    if (!is_int($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Set the user id.
   *
   * @param int $id_user
   *   The user id.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_user($id_user) {
    if (!is_int($id_user)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'id_user', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id_user = $id_user;
    return new c_base_return_true();
  }

  /**
   * Set the log title.
   *
   * @param string $title
   *   The log title.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_title($title) {
    if (!is_string($title)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'title', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->title = $title;
    return new c_base_return_true();
  }

  /**
   * Set the log type.
   *
   * @param string $type
   *   The log type.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type($type) {
    if (!is_string($type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->type = $type;
    return new c_base_return_true();
  }

  /**
   * Set the log type sub.
   *
   * @param string $type_sub
   *   The log sub-type.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type_sub($type_sub) {
    if (!is_string($type_sub)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type_sub', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->type_sub = $type_sub;
    return new c_base_return_true();
  }

  /**
   * Set the log severity.
   *
   * @param string $log_severity
   *   The log severity.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_log_severity($log_severity) {
    if (!is_int($log_severity)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'log_severity', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->log_severity = $log_severity;
    return new c_base_return_true();
  }

  /**
   * Set the log facility.
   *
   * @param string $log_facility
   *   The log facility.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_log_facility($log_facility) {
    if (!is_string($log_facility)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'log_facility', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->log_facility = $log_facility;
    return new c_base_return_true();
  }

  /**
   * Set the log details.
   *
   * @param array|string $log_details
   *   The log details.
   *   If a string, then this is a json encoded string to be converted into an array.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_log_details($log_details) {
    if (is_string($log_details)) {
      $log_details_decoded = json_decode($log_details, TRUE);

      if (!is_array($log_details_decoded)) {
        unset($log_details_decoded);
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'log_details', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->log_details = $log_details_decoded;
      unset($log_details_decoded);

      return new c_base_return_true();
    }

    if (!is_array($log_details)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'log_details', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->log_details = $log_details;
    return new c_base_return_true();
  }

  /**
   * Set the log date.
   *
   * @param int|float|string $date
   *   The log date.
   *   If this is a string, then it is a date string to be converted into a timestamp.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_date($date) {
    if (is_string($date)) {
      $this->date = c_base_defaults_global::s_get_timestamp($date)->get_value_exact();;
      return new c_base_return_true();
    }

    if (!is_int($date) && !is_float($date)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'date', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->date = $date;
    return new c_base_return_true();
  }

  /**
   * Set the log request_client.
   *
   * @param string $request_client
   *   The log request_client.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_request_client($request_client) {
    if (!is_string($request_client)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'request_client', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->request_client = $request_client;
    return new c_base_return_true();
  }

  /**
   * Set the log response_code.
   *
   * @param string $response_code
   *   The log response_code.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_code($response_code) {
    if (!is_string($response_code)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'response_code', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response_code = $response_code;
    return new c_base_return_true();
  }

  /**
   * Get the log id.
   *
   * @return c_base_return_int
   *   The unique numeric id assigned to this object.
   *   0 with error bit set is returned on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      $this->id = 0;
    }

    return c_base_return_int::s_new($this->id);
  }

  /**
   * Get the user id.
   *
   * @return c_base_return_int
   *   The numeric id assigned to this object.
   *   0 with error bit set is returned on error.
   */
  public function get_id_user() {
    if (!is_int($this->id_user)) {
      $this->id_user = 0;
    }

    return c_base_return_int::s_new($this->id_user);
  }

  /**
   * Get the log title.
   *
   * @return c_base_return_string
   *   The unique log title assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_log_title() {
    if (!is_string($this->log_title)) {
      $this->log_title = '';
    }

    return c_base_return_string::s_new($this->log_title);
  }

  /**
   * Get the log type.
   *
   * @return c_base_return_int
   *   The log type assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_log_type() {
    if (!is_int($this->log_type)) {
      $this->log_type = 0;
    }

    return c_base_return_int::s_new($this->log_type);
  }

  /**
   * Get the log type_sub.
   *
   * @return c_base_return_int
   *   The log sub-type assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_log_type_sub() {
    if (!is_int($this->log_type_sub)) {
      $this->log_type_sub = 0;
    }

    return c_base_return_int::s_new($this->log_type_sub);
  }

  /**
   * Get the log severity.
   *
   * @return c_base_return_int
   *   The log severity assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_log_severity() {
    if (!is_int($this->log_severity)) {
      $this->log_severity = 0;
    }

    return c_base_return_int::s_new($this->log_severity);
  }

  /**
   * Get the log facility.
   *
   * @return c_base_return_int
   *   The log facility assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_log_facility() {
    if (!is_int($this->log_facility)) {
      $this->log_facility = 0;
    }

    return c_base_return_int::s_new($this->log_facility);
  }

  /**
   * Get the log details.
   *
   * @return c_base_return_array
   *   The log details assigned to this object.
   *   An empty array with error bit set is returned on error.
   */
  public function get_log_details() {
    if (!is_array($this->log_details)) {
      $this->log_details = array();
    }

    return c_base_return_array::s_new($this->log_details);
  }

  /**
   * Get the log date.
   *
   * @return c_base_return_int|c_base_return_float
   *   The log date assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_log_date() {
    if (!is_int($this->log_date) && !is_float($this->log_date)) {
      $this->log_date = 0;
    }

    if (is_float($this->log_date)) {
      return c_base_return_float::s_new($this->log_date);
    }

    return c_base_return_int::s_new($this->log_date);
  }

  /**
   * Get the log request_client.
   *
   * @return c_base_return_string
   *   The request client assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_request_client() {
    if (!is_string($this->request_client)) {
      $this->request_client = '';
    }

    return c_base_return_string::s_new($this->request_client);
  }

  /**
   * Get the log response_code.
   *
   * @return c_base_return_int
   *   The response code assigned to this object.
   *   An empty string with error bit set is returned on error.
   */
  public function get_response_code() {
    if (!is_int($this->response_code)) {
      $this->response_code = 0;
    }

    return c_base_return_int::s_new($this->response_code);
  }
}
