<?php
/**
 * @file
 * Provides a class for managing forms.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A class for form problems.
 *
 * Prolems include both errors and warnings, but the distinction between the two is left to the caller.
 * The stored string value is intended to be the problem message.
 *
 * @todo: come up with a list of problem codes and add a problem id variable.
 */
class c_base_form_problem extends c_base_return_string {
  protected $fields;
  protected $arguments;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->fields = NULL;
    $this->arguments = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->fields);
    unset($this->arguments);

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
   * Create a new problem using the given field and value.
   *
   * @param string|null $field_name
   *   A field name to assign, may be NULL if error applies globally.
   * @param string $message
   *   The error message.
   *
   * @return c_base_form_problem
   *   Always returns c_base_form_problem.
   *   Error bit is set on error.
   */
  public static function s_create_error($field_name, $message) {
    $class_string = __CLASS__;
    $problem = new $class_string();
    unset($class_string);

    $result = $problem->set_field($field_name);
    if (c_base_return::s_has_error($result)) {
      $problem->set_error($result->get_error());
      unset($result);

      return $problem;
    }
    unset($result);

    $result = $problem->set_value($message);
    if (c_base_return::s_has_error($result)) {
      $problem->set_error($result->get_error());
      unset($result);

      return $problem;
    }
    unset($result);

    return $problem;
  }

  /**
   * Associations a field via the field name with this problem.
   *
   * @param string|null $field_name
   *   The field name to assign.
   *   Set to NULL remove all field names (useful for errors that affect the entire form).
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: c_base_session::save()
   */
  public function set_field($field_name) {
    if (is_null($field_name)) {
      $this->fields = array();
      return new c_base_return_true();
    }

    if (!is_string($field_name) || empty($field_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'field_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->fields)) {
      $this->fields = array();
    }

    $this->fields[$field_name] = $field_name;
    return new c_base_return_true();
  }

  /**
   * Unassociates a field via the field name with this problem.
   *
   * @param string $field_name
   *   The field name to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *   FALSE without error bit set is returned if the field name is not assigned.
   *
   * @see: c_base_session::save()
   */
  public function unset_field($field_name) {
    if (!is_string($field_name) && !empty($field_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'field_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->fields) || !array_key_exists($field_name, $this->fields)) {
      return new c_base_return_false();
    }

    unset($this->fields[$field_name]);
    return new c_base_return_true();
  }

  /**
   * Returns all fields associated with this problem.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of field names.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_fields() {
    if (!is_array($this->fields)) {
      $this->fields = array();
    }

    return c_base_return_array::s_new($this->fields);
  }

  /**
   * Associations a field via the field name with this problem.
   *
   * @param string $index
   *   The index name to be associated with this message.
   * @param string $value
   *   A string representing the value to be associated with the index.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: c_base_session::save()
   */
  public function set_argument($index, $value) {
    if (!is_string($index) && !empty($index)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->arguments)) {
      $this->arguments = array();
    }

    $this->arguments[$index] = $value;
    return new c_base_return_true();
  }

  /**
   * Gets the argument value assigned at the specified index.
   *
   * @return c_base_return_value|c_base_return_status
   *   An array of field names.
   *   FALSE with the error bit set is returned on error.
   *   FALSE without error bit set is returned if the index is not assigned.
   */
  public function get_argument($index) {
    if (!is_string($index) && !empty($index)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->arguments)) {
      $this->arguments = array();
    }

    if (!array_key_exists($index, $this->arguments)) {
      return new c_base_return_false();
    }

    return c_base_return_value::s_new($this->fields);
  }

  /**
   * Returns all arguments associated with this problem.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of field names.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_arguments() {
    if (!is_array($this->arguments)) {
      $this->arguments = array();
    }

    return c_base_return_array::s_new($this->arguments);
  }
}
