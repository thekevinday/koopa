<?php
/**
 * @file
 * Provides a class for managing return values.
 *
 * Each function needs to return TRUE/FALSE to represent success or failure.
 * However, if TRUE/FALSE could also represent a specific value, then TRUE/FALSE can have different contexts.
 *
 * This is a problem and the return result needs to be as context free as possible (aka: only 1 context).
 * Having custom error codes can also be useful.
 *
 * Each class should instead return a type of base_return that can be easily tested for success or failure.
 *
 * The downside of this is that returning an object can waste more resources than a simple TRUE/FALSE.
 * For consistency purposes, TRUE/FALSE should never be directly returned.
 *
 * Functions defined in this class will return the normal TRUE/FALSE and not the class-based TRUE/FALSE as an exception to this rule.
 * - This is done because this class defines those objects.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');

require_once('common/base/traits/base_return.php');

/**
 * A generic class for managing return values.
 *
 * This is the base template class used for specific return classes.
 *
 * All base returns will have an error variable.
 *
 * @require class c_base_error
 */
class c_base_return {
  private $errors;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->errors = [];
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->errors);
  }

  /**
   * Provide a simple way to check for error in a single step.
   *
   * This is intended to help clean up code and make code more readable.
   *
   * @return bool
   *   return TRUE if the passed argument is an object class of type __CLASS__ and has an error flag set.
   */
  public static function s_has_error($return) {
    return is_object($return) && $return instanceof c_base_return && $return->has_error();
  }

  /**
   * Provide a simple way to check for (unrecovered) error in a single step.
   *
   * This is intended to help clean up code and make code more readable.
   *
   * @return bool
   *   return TRUE if the passed argument is an object class of type __CLASS__ and has an error flag set.
   */
  public static function s_has_error_unrecovered($return) {
    if (!is_object($return) && $return instanceof c_base_return && $return->has_error()) {
      return FALSE;
    }

    return $return->has_error_unrecovered();
  }

  /**
   * Provide a simple way to check for (recovered) error in a single step.
   *
   * This is intended to help clean up code and make code more readable.
   *
   * @return bool
   *   return TRUE if the passed argument is an object class of type __CLASS__ and has an error flag set.
   */
  public static function s_has_error_recovered($return) {
    if (!is_object($return) && $return instanceof c_base_return && $return->has_error()) {
      return FALSE;
    }

    return $return->has_error_recovered();
  }

  /**
   * Copy errors from one return type to another.
   *
   * Invalid parameters are silently ignored and no actions are performed.
   *
   * @param c_base_return $source
   *   The return value to copy errors from.
   * @param c_base_return &$destination
   *   The return value to copy errors to.
   */
  public static function s_copy_errors($source, &$destination) {
    if (!($source instanceof c_base_return) || !($destination instanceof c_base_return)) {
      return;
    }

    $errors = $source->get_error()->get_value_exact();
    if (is_array($errors)) {
      foreach ($errors as $error) {
        $destination->set_error($error);
      }
      unset($error);
    }
    unset($errors);
  }

  /**
   * Assign the error code.
   *
   * @param null|c_base_error|array $error
   *   The error code class defining what the error is.
   *   Setting this to NULL will clear all errors.
   *   May be an array of c_base_error objects, in which case $delta is ignored.
   * @param null|int|bool $delta
   *   (optional) When an integer, the error is assigned an explicit position in the errors array.
   *   When NULL, the error is appended to the errors array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_error($error, $delta = NULL) {
    if (!is_null($error) && !($error instanceof c_base_error || is_array($error))) {
      return FALSE;
    }

    if (is_null($error)) {
      $this->errors = [];
      return TRUE;
    }

    if (!is_array($this->errors)) {
      $this->errors = [];
    }

    if (is_array($error)) {
      foreach ($error as $error_object) {
        if ($error_object instanceof c_base_error) {
          $this->errors[] = $error_object;
        }
      }
      unset($error_object);
    }
    else if (is_null($delta)) {
      $this->errors[] = $error;
    }
    else if (is_int($delta) && $delta >= 0) {
      $this->errors[$delta] = $error;
    }
    else {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Return the error code.
   *
   * @param null|int $delta
   *   (optional) When an integer, the error assigned at the specified position in the errors array is returned.
   *   When NULL, the entire array of errors is retuned.
   *
   * @return null|array|c_base_error
   *   When $delta is an integer, an error object is returned.
   *   An array of errors are returned when $delta is NULL.
   *   NULL is returned when there is no error or there is no error at the specified delta.
   */
  public function get_error($delta = NULL) {
    if (!is_array($this->errors)) {
      $this->errors = [];
    }

    if (is_null($delta)) {
      return $this->errors;
    }

    if (array_key_exists($delta, $this->errors)) {
      if ($this->errors[$delta] instanceof c_base_error) {
        return $this->errors[$delta];
      }
    }

    return NULL;
  }

  /**
   * Return the error state in a simple TRUE/FALSE manner.
   *
   * This is similar to get_error(), but should instead be used to to check to see if there is an error and not check what the error is set to.
   *
   * @param null|int $delta
   *   (optional) When an integer, the error assigned at the specified position in the errors array is checked.
   *   When NULL, the entire errors array is checked for any error.
   *
   * @return bool
   *   TRUE if any error is assigned and FALSE if no errors are assigned.
   *
   * @see: get_error()
   */
  public function has_error($delta = NULL) {
    if (is_int($delta)) {
      if (array_key_exists($delta, $this->errors)) {
        return ($this->errors[$delta]) instanceof c_base_error;
      }

      return FALSE;
    }

    // when there is no error flag assigned, its value should be NULL so a simple existence check should be all that is needed.
    return !empty($this->errors);
  }

  /**
   * Return the error state in a simple TRUE/FALSE manner, but only if the error is designated as recovered.
   *
   * This is similar to get_error(), but should instead be used to to check to see if there is an error and not check what the error is set to.
   *
   * @param null|int $delta
   *   (optional) When an integer, the error assigned at the specified position in the errors array is checked.
   *   When NULL, the entire errors array is checked for any error.
   *
   * @return bool
   *   TRUE if any error is assigned and is has recovered set to TRUE, otherwise FALSE is returned.
   *
   * @see: has_error()
   * @see: get_error()
   */
  public function has_error_recovered($delta = NULL) {
    if (is_int($delta)) {
      if (array_key_exists($delta, $this->errors)) {
        if ($this->errors[$delta] instanceof c_base_error) {
          return $this->errors[$delta]->get_recovered();
        }
      }

      return FALSE;
    }

    // when there is no error flag assigned, its value should be NULL so a simple existence check should be all that is needed.
    if (empty($this->errors)) {
      return FALSE;
    }

    foreach ($this->errors as $error) {
      if ($error->get_recovered()) {
        unset($error);
        return TRUE;
      }
    }
    unset($error);

    return FALSE;
  }

  /**
   * Return the error state in a simple TRUE/FALSE manner, but only if the error is designated as unrecovered.
   *
   * This is similar to get_error(), but should instead be used to to check to see if there is an error and not check what the error is set to.
   *
   * @param null|int $delta
   *   (optional) When an integer, the error assigned at the specified position in the errors array is checked.
   *   When NULL, the entire errors array is checked for any error.
   *
   * @return bool
   *   TRUE if any error is assigned and is has recovered set to TRUE, otherwise FALSE is returned.
   *
   * @see: has_error()
   * @see: get_error()
   */
  public function has_error_unrecovered($delta = NULL) {
    if (is_int($delta)) {
      if (array_key_exists($delta, $this->errors)) {
        if ($this->errors[$delta] instanceof c_base_error) {
          return !$this->errors[$delta]->get_recovered();
        }
      }

      return FALSE;
    }

    // when there is no error flag assigned, its value should be NULL so a simple existence check should be all that is needed.
    if (empty($this->errors)) {
      return FALSE;
    }

    foreach ($this->errors as $error) {
      if (!$error->get_recovered()) {
        unset($error);
        return TRUE;
      }
    }
    unset($error);

    return FALSE;
  }

  /**
   * Return the value.
   *
   * @return null $value
   *   The value within this class.
   */
  public function get_value() {
    return NULL;
  }

  /**
   * Return the value of the expected type.
   *
   * @return NULL $value
   *   The value c_base_markup_tag stored within this class.
   */
  public function get_value_exact() {
    return NULL;
  }

  /**
   * Determine if this class has a value assigned to it.
   *
   * @return bool
   *   TRUE if a value is assigned, FALSE otherwise.
   */
  public function has_value() {
    return FALSE;
  }
}

/**
 * A boolean return class representing a return value of either TRUE or FALSE.
 *
 * This is used as a return status for a function that does not return any expected valid values.
 * This class does not have any values of its own.
 */
class c_base_return_status extends c_base_return {
}

/**
 * A boolean return class representing a return value of TRUE.
 *
 * This class will not have any values.
 */
class c_base_return_true extends c_base_return_status {

  /**
   * Assign the value.
   *
   * @param $value
   *   This is ignored.
   *
   * @return bool
   *   Always returns TRUE.
   */
  public function set_value($value) {
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return bool $value
   *   Always returns TRUE.
   */
  public function get_value() {
    return TRUE;
  }

  /**
   * Return the value of the expected type.
   *
   * @return bool $value
   *   Always returns TRUE.
   */
  public function get_value_exact() {
    return TRUE;
  }
}

/**
 * A boolean return class representing a return value of FALSE.
 *
 * This class will not have any values.
 */
class c_base_return_false extends c_base_return_status {

  /**
   * Assign the value.
   *
   * @param $value
   *   This is ignored.
   *
   * @return bool
   *   Always returns TRUE.
   */
  public function set_value($value) {
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return bool $value
   *   Always returns FALSE.
   */
  public function get_value() {
    return FALSE;
  }

  /**
   * Return the value of the expected type.
   *
   * @return bool $value
   *   Always returns FALSE.
   */
  public function get_value_exact() {
    return FALSE;
  }
}

/**
 * A return class representing a return value of NULL.
 *
 * This class will not have any values.
 */
class c_base_return_null extends c_base_return_status {

  /**
   * Assign the value.
   *
   * @param $value
   *   This is ignored.
   *
   * @return bool
   *   Always returns TRUE.
   */
  public function set_value($value) {
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return $value
   *   Always returns NULL.
   */
  public function get_value() {
    return NULL;
  }

  /**
   * Return the value of the expected type.
   *
   * @return $value
   *   Always returns NULL.
   */
  public function get_value_exact() {
    return NULL;
  }
}

/**
 * A return class representing a return value with a specific value.
 */
class c_base_return_value extends c_base_return {
  use t_base_return_value;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->value = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->value);
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
   * Assign the value.
   *
   * @param bool $value
   *   Any value so long as it is a bool.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return $value
   *   The value bool stored within this class.
   */
  public function get_value() {
    if (!isset($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return $value
   *   The value bool stored within this class.
   */
  public function get_value_exact() {
    if (!isset($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Determine if this class has a value assigned to it.
   *
   * @return bool
   *   TRUE if a value is assigned, FALSE otherwise.
   */
  public function has_value() {
    return !is_null($this->value);
  }
}

/**
 * A return class whose value is represented as a bool.
 *
 * Do not use this class for returning status or errors for functions.
 * Instead use anything of type c_base_return_status().
 */
class c_base_return_bool extends c_base_return_value {
  use t_base_return_value_exact;

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
    return self::p_s_value_exact($return, __CLASS__, FALSE);
  }

  /**
   * Assign the value.
   *
   * @param bool $value
   *   Any value so long as it is a bool.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_bool($value)) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return bool|null $value
   *   The value bool stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_bool($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return bool $value
   *   The value bool stored within this class.
   */
  public function get_value_exact() {
    if (!is_bool($this->value)) {
      return FALSE;
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a string.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_base_return_string extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param string $value
   *   Any value so long as it is a string.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_string($value)) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_string($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return string $value
   *   The value string stored within this class.
   */
  public function get_value_exact() {
    if (!is_string($this->value)) {
      return '';
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a int.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 * Numeric types are converted to integers and stored as an integer.
 */
class c_base_return_int extends c_base_return_value {
  use t_base_return_value_exact;

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
    return self::p_s_value_exact($return, __CLASS__, 0);
  }

  /**
   * Assign the value.
   *
   * @param int|numeric $value
   *   Any value so long as it is an integer or a numeric.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_int($value) && !is_numeric($value)) {
      return FALSE;
    }

    if (is_numeric($value)) {
      $this->value = (int) $value;
    }
    else {
      $this->value = $value;
    }

    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return int|null $value
   *   The value integer stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_int($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return int $value
   *   The value int stored within this class.
   */
  public function get_value_exact() {
    if (!is_int($this->value)) {
      return 0;
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a float.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 * Numeric types are converted to float and stored as a float.
 */
class c_base_return_float extends c_base_return_value {
  use t_base_return_value_exact;

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
    return self::p_s_value_exact($return, __CLASS__, 0.0);
  }

  /**
   * Assign the value.
   *
   * @param float|numeric $value
   *   Any value so long as it is an integer or a numeric.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_float($value) && !is_numeric($value)) {
      return FALSE;
    }

    if (is_numeric($value)) {
      $this->value = (float) $value;
    }
    else {
      $this->value = $value;
    }

    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return float|null $value
   *   The value float stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_float($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return float $value
   *   The value float stored within this class.
   */
  public function get_value_exact() {
    if (!is_float($this->value)) {
      return 0.0;
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as an array.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 * This provides basic functionality for managing keys and values in the class.
 */
class c_base_return_array extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param array $value
   *   Any value so long as it is an array.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_array($value)) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Assign the value at a specific index in the array.
   *
   * @param $value
   *   Any value to be assigned at the specified position in the array.
   * @param int|string $key
   *   A key to assign a specific value to.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value_at($value, $key) {
    if (!is_int($key) && !is_string($key)) {
      return FALSE;
    }

    if (!is_array($this->value)) {
      $this->value = [];
    }

    $this->value[$key] = $value;
    return TRUE;
  }

  /**
   * Append the value at the end of the array.
   *
   * @param $value
   *   Any value to be appended in the array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value_append($value) {
    if (!is_array($this->value)) {
      $this->value = [];
    }

    $this->value[] = $value;
    return TRUE;
  }

  /**
   * Assigns the array from a serialized array string.
   *
   * @param string $serialized
   *  A serialized string to convert to an array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   if converted string does not produce an array, FALSE is returned and value is set to an empty array.
   *
   * @see: unserialize()
   */
  public function set_value_serialized($serialized) {
    if (!is_string($serialized)) {
      return FALSE;
    }

    $unserialized = unserialize($serialized);
    if (is_array($unserialized)) {
      $this->value = $unserialized;
      unset($unserialized);

      return TRUE;
    }
    unset($unserialized);

    $this->value = [];
    return FALSE;
  }

  /**
   * Returns the data as a json-serialized array string.
   *
   * @param string $jsonized
   *  A jsonized string to convert to an array.
   * @param bool $associative
   *   (optional) When TRUE array is return as an associative array.
   * @param int $options
   *   (optional) bitmask of json constants.
   * @param int $depth
   *   (optional) Maximum array depth.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   if converted string does not produce an array, FALSE is returned and value is set to an empty array.
   *
   * @see: json_decode()
   */
  public function set_value_jsonized($jsonized, $associative = TRUE, $options = 0, $depth = 512) {
    if (!is_string($jsonized)) {
      return FALSE;
    }

    if (!is_bool($associative)) {
      $associative = TRUE;
    }

    if (!is_int($options)) {
      $options = 0;
    }

    if (!is_int($depth) || $depth < 1) {
      $depth = 512;
    }

    $decoded = json_decode($jsonized, $associative, $options, $depth);
    if (is_array($decoded)) {
      $this->value = $decoded;
      unset($decoded);

      return TRUE;
    }
    unset($decoded);

    $this->value = [];
    return FALSE;
  }

  /**
   * Return the value.
   *
   * @return array|null $value
   *   The value array stored within this class.
   *   NULL may be returned if there is no defined valid array.
   */
  public function get_value() {
    if (!is_array($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return array $value
   *   The value array stored within this class.
   */
  public function get_value_exact() {
    if (!is_array($this->value)) {
      return [];
    }

    return $this->value;
  }

  /**
   * Return the value at a specific index in the array.
   *
   * Note: ideally, this should return specific c_base_return_* types.
   *   The problem is that this would then make this class dependent on those type, which I am trying to avoid.
   *   No c_base_return_* type should use another c_base_return_* as their return values for their non-static functions.
   *   @todo: This design might be reviewed and changed before this project is finalized.
   *
   * @param int|string $key
   *   A key to assign a specific value to.
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_value_at($key) {
    if (!is_int($key) && !is_string($key)) {
      return NULL;
    }

    if (!is_array($this->value)) {
      return NULL;
    }

    if (!array_key_exists($key, $this->value)) {
      return NULL;
    }

    return $this->value[$key];
  }

  /**
   * Return the first value in the array after calling reset().
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_value_reset() {
    if (!is_array($this->value) || empty($this->value)) {
      return NULL;
    }

    return reset($this->value);
  }

  /**
   * Return the first value in the array after calling current().
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_value_current() {
    if (!is_array($this->value) || empty($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * Return the first item in the array after calling each().
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_value_each() {
    if (!is_array($this->value) || empty($this->value)) {
      return NULL;
    }

    return each($this->value);
  }

  /**
   * Return the first value in the array after calling next().
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_value_next() {
    if (!is_array($this->value) || empty($this->value)) {
      return NULL;
    }

    return next($this->value);
  }

  /**
   * Return the first value in the array after calling prev().
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_item_previous() {
    if (!is_array($this->value) || empty($this->value)) {
      return NULL;
    }

    return prev($this->value);
  }

  /**
   * Return the first value in the array after calling end().
   *
   * @return
   *   Value on success, NULL otherwise.
   */
  public function get_value_end() {
    if (!is_array($this->value) || empty($this->value)) {
      return NULL;
    }

    return end($this->value);
  }

  /**
   * Return the total number of values in the array.
   *
   * @return int
   *   A positive integer.
   */
  public function get_value_count() {
    if (empty($this->value)) {
      return 0;
    }

    return count($this->value);
  }

  /**
   * Return the array keys assigned to the value.
   *
   * @return array
   *   An array of array keys.
   */
  public function get_value_keys() {
    if (empty($this->value)) {
      return [];
    }

    return array_keys($this->value);
  }

  /**
   * Returns the data as a serialized array string.
   *
   * @return string|bool
   *   A serialized string representing the value array on success.
   *   FALSE on failure.
   *
   * @see: serialize()
   */
  public function get_value_serialized() {
    return serialize($this->value);
  }

  /**
   * Returns the data as a json-serialized array string.
   *
   * @param int $options
   *   (optional) bitmask of json constants.
   * @param int $depth
   *   (optional) Maximum array depth.
   *
   * @return string|bool
   *   A json-serialized string representing the value array on success.
   *   FALSE on failure.
   *
   * @see: json_encode()
   */
  public function get_value_jsonized($options = 0, $depth = 512) {
    if (!is_int($options)) {
      $options = 0;
    }

    if (!is_int($depth) || $depth < 1) {
      $depth = 512;
    }

    return json_encode($this->value, $options, $depth);
  }
}

/**
 * A return class whose value is represented as a generic object.
 *
 * This should be used to return either a generic object or used as a base class for extending to a specific object type.
 * All specific object types should extend this class so that instanceof tests against c_base_return_object are always accurate.
 */
class c_base_return_object extends c_base_return_value {

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
   * Assign the value.
   *
   * @param object $value
   *   Any value so long as it is an object.
   *   This object is cloned.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *
   * @see: clone()
   */
  public function set_value($value) {
    if (!is_object($value)) {
      return FALSE;
    }

    $this->value = clone($value);
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return object|null $value
   *   The value object stored within this class.
   *   NULL may be returned if there is no defined valid resource.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_object($this->value)) {
      $this->value = NULL;
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a generic resource.
 *
 * This should be used to return either a generic resource or used as a base class for extending to a specific resource type.
 * All specific resource types should extend this class so that instanceof tests against c_base_return_resource are always accurate.
 */
class c_base_return_resource extends c_base_return_value {

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
   * Assign the value.
   *
   * @param resource $value
   *   Any value so long as it is an resource.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_resource($value)) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return resource|null $value
   *   The value resource stored within this class.
   *   NULL may be returned if there is no defined valid resource.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_resource($this->value)) {
      $this->value = NULL;
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a stream resource.
 */
class c_base_return_resource_stream extends c_base_return_resource {

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
   * Assign the value.
   *
   * @param stream $value
   *   Any value so long as it is an resource.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_resource($value) || get_resource_type($value) != 'stream') {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return resource|null $value
   *   The value resource stored within this class.
   *   NULL may be returned if there is no defined valid resource.
   */
  public function get_value() {
    if (!is_resource($this->value) || get_resource_type($this->value) != 'stream') {
      $this->value = NULL;
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a socket resource.
 */
class c_base_return_resource_socket extends c_base_return_resource {

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
   * Assign the value.
   *
   * @param stream $value
   *   Any value so long as it is an resource.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_resource($value) || get_resource_type($value) != 'socket') {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return resource|null $value
   *   The value resource stored within this class.
   *   NULL may be returned if there is no defined valid resource.
   */
  public function get_value() {
    if (!is_resource($this->value) || get_resource_type($this->value) != 'socket') {
      $this->value = NULL;
    }

    return $this->value;
  }
}

/**
 * A generic class for pre-populating specific error return codes.
 *
 * This is used to simplify the code by pre-setting the error code.
 *
 * @require class c_base_error
 */
class c_base_return_error {

  /**
   * Creates a return boolean TRUE with the error value populated.
   *
   * @param c_base_error|array|null $error
   *   (optional) a custom error.
   *   Can be an array of c_base_error for returning multiple errors.
   *   When NULL, no errors are defined.
   *
   * @return c_base_return_true
   *   A c_base_return_true object with the error value populated.
   */
  public static function s_true($error = NULL) {
    $object_return = new c_base_return_true();

    if (is_null($error)) {
      $object_return->set_error(NULL);
    }
    else if (is_array($error)) {
      foreach ($error as $delta => $value) {
        $object_return->set_error($error, $delta);
      }
      unset($delta);
      unset($value);
    }
    else {
      $object_return->set_error($error);
    }

    return $object_return;
  }

  /**
   * Creates a return boolean TRUE with the error value populated.
   *
   * @param c_base_error|array|null $error
   *   (optional) a custom error setting.
   *   Can be an array of c_base_error for returning multiple errors.
   *   When NULL, no errors are defined.
   *
   * @return c_base_return_false
   *   A c_base_return_true object with the error value populated.
   */
  public static function s_false($error = NULL) {
    $object_return = new c_base_return_false();

    if (is_null($error)) {
      $object_return->set_error(null);
    }
    else if (is_array($error)) {
      foreach ($error as $delta => $value) {
        $object_return->set_error($error, $delta);
      }
      unset($delta);
      unset($value);
    }
    else {
      $object_return->set_error($error);
    }

    return $object_return;
  }

  /**
   * Creates a return NULL with the error value populated.
   *
   * @param c_base_error|array|null $error
   *   (optional) a custom error setting.
   *   Can be an array of c_base_error for returning multiple errors.
   *   When NULL, no errors are defined.
   *
   * @return c_base_return_false
   *   A c_base_return_null object with the error value populated.
   */
  public static function s_null($error = NULL) {
    $object_return = new c_base_return_null();

    if (is_null($error)) {
      $object_return->set_error(null);
    }
    else if (is_array($error)) {
      foreach ($error as $delta => $value) {
        $object_return->set_error($error, $delta);
      }
      unset($delta);
      unset($value);
    }
    else {
      $object_return->set_error($error);
    }

    return $object_return;
  }

  /**
   * Creates a return boolean TRUE with the error value populated.
   *
   * This will assign a value to the class.
   *
   * @param $value
   *   A value to provide
   * @param $class
   *   A custom class name of any class that is an instance of c_base_return_value.
   * @param c_base_error|null $error
   *   (optional) a custom error setting.
   *   Can be an array of c_base_error for returning multiple errors.
   *   When NULL, no errors are defined.
   *
   * @return c_base_return_false|c_base_return_value
   *   A c_base_return_value object is returned with the error value populated
   *   If the passed class is invalid or not of type c_base_return_value, then a c_base_return_true object with the error value populated.
   *
   * * @see: self::s_return()
   */
  public static function s_value($value, $class, $error = NULL) {
    if (!class_exists($class) || !($class instanceof c_base_return_value)) {
      return self::s_false($error);
    }

    $object_return = new $class();

    if (is_null($error)) {
      $object_error = new c_base_error();
      $object_return->set_error($object_error);
      unset($object_error);
    }
    else if (is_array($error)) {
      foreach ($error as $delta => $value) {
        $object_return->set_error($error, $delta);
      }
      unset($delta);
      unset($value);
    }
    else {
      $object_return->set_error($error);
    }

    $object_return->set_value($value);
    return $object_return;
  }

  /**
   * Creates a return boolean TRUE with the error value populated.
   *
   * This will not assign any value to the class.
   *
   * @param $class
   *   A custom class name of any class that is an instance of c_base_return.
   * @param c_base_error|null $error
   *   (optional) a custom error setting.
   *   Can be an array of c_base_error for returning multiple errors.
   *   When NULL, no errors are defined.
   *
   * @return c_base_return_false|c_base_return_value
   *   A c_base_return_value object is returned with the error value populated
   *   If the passed class is invalid or not of type c_base_return_value, then a c_base_return_true object with the error value populated.
   *
   * @see: self::s_value()
   */
  public static function s_return($class, $error = NULL) {
    if (!class_exists($class) && !($class instanceof c_base_return)) {
      return self::s_false($error);
    }

    $object_return = new $class();

    if (is_null($error)) {
      $object_error = new c_base_error();
      $object_return->set_error($object_error);
      unset($object_error);
    }
    else if (is_array($error)) {
      foreach ($error as $delta => $value) {
        $object_return->set_error($error, $delta);
      }
      unset($delta);
      unset($value);
    }
    else {
      $object_return->set_error($error);
    }

    return $object_return;
  }
}
