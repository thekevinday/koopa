<?php
/**
 * @file
 * Provides a class for managing array objects.
 *
 * This is provided mainly for a consistent class type that handles arrays instead of simply extending c_base_return_array().
 * The reason for using this over extending c_base_return_array is to ensure that the return values follow the consistent api.
 * - Only the return values of the core c_base_return_* functions return direct/raw values.
 * - All other functions are expected to return some class or sub-class of c_base_return.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_rfc_string.php');

/**
 * A generic class for providing classes that support a single array value.
 *
 * This is only intended for classes that contain a single array value.
 * Other classes may need a more complex implementation of this class.
 *
 * This does not use the traits t_base_return_value_exact or t_base_return_value because this is non-confirming to those traits.
 *
 * @require class c_base_rfc_string
 */
class c_base_array extends c_base_rfc_string {
  protected $items;

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
   * Assign the array.
   *
   * @param array $array
   *   Replace the current array with this value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_items($array) {
    if (!is_array($array)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'array', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->items = $array;
    return new c_base_return_true();
  }

  /**
   * Assign the item at a specific index in the array.
   *
   * @param $item
   *   Any item to assign.
   * @param int|string $key
   *   A key to assign a specific value to.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_item_at($item, $key) {
    if (!is_int($key) && !is_string($key)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'key', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->items)) {
      $this->items = array();
    }

    $this->items[$key] = $item;
    return new c_base_return_true();
  }

  /**
   * Append the item at the end of the array.
   *
   * @param $item
   *   Any value.
   *   This does not perform clone() on objects.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_item_append($item) {
    if (!is_array($this->items)) {
      $this->items = array();
    }

    $this->items[] = $item;
    return new c_base_return_true();
  }

  /**
   * Assigns the array from a serialized array string.
   *
   * @param string $serialized
   *  A serialized string to convert to an array.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   If converted string does not produce an array, FALSE is returned and items is set to an empty array.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: unserialize()
   */
  public function set_items_serialized($serialized) {
    if (!is_string($serialized)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'serialized', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $unserialized = unserialize($serialized);
    if (is_array($unserialized)) {
      $this->items = $unserialized;
      unset($unserialized);

      return new c_base_return_true();
    }
    unset($unserialized);

    $this->items = array();
    return new c_base_return_false();
  }

  /**
   * Returns the data as a json-serialized array string.
   *
   * @param jsonized
   *  A jsonized string to convert to an array.
   * @param bool $associative
   *   (optional) When TRUE array is return as an associative array.
   * @param int $options
   *   (optional) bitmask of json constants.
   * @param int $depth
   *   (optional) Maximum array depth.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   if converted string does not produce an array, FALSE is returned and items is set to an empty array.
   *
   * @see: json_decode()
   */
  public function set_items_jsonized($jsonized, $associative = TRUE, $options = 0, $depth = 512) {
    if (!is_string($jsonized)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'jsonized', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($associative)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'associative', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'options', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($depth) || $depth < 1) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'depth', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $decoded = json_decode($jsonized, $associative, $options, $depth);
    if (is_array($decoded)) {
      $this->items = $decoded;
      unset($decoded);

      return new c_base_return_true();
    }
    unset($decoded);

    $this->items = array();
    return new c_base_return_false();
  }

  /**
   * Return the array.
   *
   * @return c_base_return_array
   *   The array stored within this class.
   *   An empty array with error bit set is returned on error.
   */
  public function get_items() {
    if (!is_null($this->items) && !is_array($this->items)) {
      return c_base_return_array::s_new(array());
    }

    return c_base_return_array::s_new($this->items);
  }

  /**
   * Return the item at a specific index in the array.
   *
   * @param string $key
   *   A key to assign a specific value to.
   *
   * @return c_base_return_status|c_base_return_value
   *   Value on success, FALSE otherwise.
   *   FALSE without error bit set is returned if $key us not defined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_item_at($key) {
    if (!is_string($key) || empty($key)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'key', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->items) || !array_key_exists($key, $this->items)) {
      return new c_base_return_false();
    }

    return c_base_return_value::s_new($this->items[$key]);
  }

  /**
   * Return the total number of values in the array.
   *
   * @return c_base_return_int
   *   A positive integer.
   *   0 with the error bit set is returned on error.
   */
  public function get_items_count() {
    if (empty($this->items)) {
      return 0;
    }

    return count($this->items);
  }

  /**
   * Return the array keys of the array.
   *
   * @return c_base_return_array
   *   An array of array keys.
   *   An empty array with the error bit set is returned on error.
   */
  public function get_items_keys() {
    if (empty($this->items)) {
      return array();
    }

    return array_keys($this->items);
  }

  /**
   * Returns the data as a serialized array string.
   *
   * @return c_base_return_string|c_base_return_bool
   *   A serialized string representing the array on success.
   *   FALSE on failure.
   *   An empty string with the error bit set is returned on error.
   *
   * @see: serialize()
   */
  public function get_items_serialized() {
    if (!is_array($this->items)) {
      return c_base_return_string::s_new(serialize(array()));
    }

    return c_base_return_string::s_new(serialize($this->items));
  }

  /**
   * Returns the data as a json-serialized array string.
   *
   * @param int $options
   *   (optional) bitmask of json constants.
   * @param int $depth
   *   (optional) Maximum array depth.
   *
   * @return c_base_return_string|c_base_return_bool
   *   A json-serialized string representing the array on success.
   *   FALSE on failure.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: json_encode()
   */
  public function get_items_jsonized($options = 0, $depth = 512) {
    if (!is_int($options)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'options', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($depth)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'depth', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($options)) {
      $options = 0;
    }

    if (!is_int($depth) || $depth < 1) {
      $depth = 512;
    }

    $encoded = json_encode($this->items, $options, $depth);
    if ($encoded === FALSE) {
      unset($encoded);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'json_encode', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($encoded);
  }
}
