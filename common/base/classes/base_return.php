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

// include required files.
require_once('common/base/classes/base_error.php');

/**
 * A generic trait for a return value class to have some value data.
 *
 * Return classes can be defined without this.
 * - An example would be a boolean class whose name defines its state.
 *
 * Most return classes will use this trait.
 *
 * @require class base_error
 */
trait t_base_return_value {
  protected $value = NULL;

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->value);
  }

  /**
   * Assign the value.
   *
   * @param $value
   *   This can be anything that is to be considered a return value.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    $this->value = $value;
    return TRUE;
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

  /**
   * Creates a new return __class__ type.
   *
   * This is used to simplify the returning of a new class value.
   *
   * Errata: My theory was that by using __CLASS__ child classes could call this static function and __CLASS__ would be replaced with the child class type.
   * This is not the case, so I am left with using an abstract static class, which is also not supported by PHP.
   * Therefore, API-wise, this is an abstract static class and child classes are expected to implement this function, even if PHP does not enforce this.
   *
   * @param $value
   *   The value to assign.
   *   Validation is performed by the current class.
   *   No error will be set if an invalid parameter is provided.
   *   Child classes are expected to assign a value of NULL for invalid parameters.
   *
   * @return __class__
   *   A newly created __class__ type, without an error set.
   */
  //public abstract static function s_new($value);

  /**
   * Private implemntation of s_new().
   *
   * @param $value
   *   The value to assign.
   *   Validation is performed by the current class.
   *   No error will be set if an invalid parameter is provided.
   *   Child classes are expected to assign a value of NULL for invalid parameters.
   * @param string $class
   *   This is the class name of the type to create.
   *   Essentially just pass __CLASS__ to this argument.
   *
   * @see: t_base_return_value::s_new()
   */
  final protected static function p_s_new($value, $class) {
    $object_return = new $class();
    unset($class);

    // allow for NULL to be passed without generating any errors.
    // the default value, when undefined is always null.
    if (!is_null($value)) {
      $object_return->set_value($value);
    }

    return $object_return;
  }

  /**
   * Perform a very basic, safe, value retrieval.
   *
   * PHP allows for things like $account->get_password()->get_value().
   * If get_password() returns an non-object or an object without the get_value() function, a PHP error happens.
   * This provides a simple way to obtain the value of a specific class that supports this trait without generating errors.
   *
   * Errata: My theory was that by using __CLASS__ child classes could call this static function and __CLASS__ would be replaced with the child class type.
   * This is not the case, so I am left with using an abstract static class, which is also not supported by PHP.
   * Therefore, API-wise, this is an abstract static class and child classes are expected to implement this function, even if PHP does not enforce this.
   *
   * @return
   *   The value is returned or NULL is returned if value retrieval is not possible.
   */
  //public abstract static function s_value($return);

  /**
   * Private implementation of s_value().
   *
   * @param object $return
   *   The appropriate c_base_return class that supports the t_base_return_value_exact trait.
   * @param string $class
   *   The class name to expect.
   *
   * @return
   *   The value is returned or a generated expected type is returned if value retrieval is not possible.
   *
   * @see: t_base_return_value::p_s_value()
   */
  final protected static function p_s_value($return, $class) {
    if (!is_object($return) || !($return instanceof $class)) {
      return NULL;
    }

    return $return->get_value();
  }
}

/**
 * A generic trait for a getting a return value that is an exact type.
 *
 * No NULL values are allowed.
 */
trait t_base_return_value_exact {
  // PHP does not support this. This project's API does require this trait to be used even if its not enforced by PHP.
  //use t_base_return_value;

  /**
   * Perform a very basic, safe, value retrieval of the expected type.
   *
   * This guarantees that a specific type is returned.
   *
   * PHP allows for things like $account->get_password()->get_value().
   * If get_password() returns an non-object or an object without the get_value() function, a PHP error happens.
   * This provides a simple way to obtain the value of a specific class that supports this trait without generating errors.
   *
   * Errata: API-wise, this is an abstract static class and child classes are expected to implement this function, even if PHP does not enforce this.
   *
   * @param object $return
   *   The appropriate c_base_return class that supports the t_base_return_value_exact trait.
   *
   * @return
   *   The value is returned or a generated expected type is returned if value retrieval is not possible.
   */
  //abstract public static function s_value_exact($return);

  /**
   * Private implementation of s_value_exact().
   *
   * @param object $return
   *   The appropriate c_base_return class that supports the t_base_return_value_exact trait.
   * @param string $class
   *   The class name to expect.
   * @param $failsafe
   *   The variable to return in case $return is invalid in some way.
   *   This is used to guarantee that the return value is of the exact expected type.
   *
   * @return
   *   The value is returned or a generated expected type is returned if value retrieval is not possible.
   *
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  final protected static function p_s_value_exact($return, $class, $failsafe) {
    if (!is_object($return) || !($return instanceof $class)) {
      return $failsafe;
    }

    return $return->get_value_exact();
  }
}

/**
 * A generic trait for a message associated with a return value.
 *
 * This is added as a consideration and may be removed it ends up being unused.
 */
trait t_base_return_message {
  protected $message = NULL;

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->message);
  }

  /**
   * Assign the message.
   */
  public function set_message($message) {
    $this->message = $message;
  }

  /**
   * Return the value.
   *
   * @return $value
   *   This can be anything that is to be considered a return value.
   */
  public function get_message() {
    return $this->value;
  }
}

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
  private $error;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->error = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->error);
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
   * Assign the error code.
   *
   * @param null|c_base_error $error
   *   The error code class defining what the error is.
   *   Setting this to NULL will clear the error flag.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_error($error) {
    if (!is_null($error) && !($error instanceof c_base_error)) {
      return FALSE;
    }

    $this->error = $error;
    return TRUE;
  }

  /**
   * Return the error code.
   *
   * @return null|c_base_error
   *   @todo: finish this once c_base_error is implemented.
   */
  public function get_error() {
    if (!($this->error instanceof c_base_error)) {
      $this->error = NULL;
    }

    return $this->error;
  }

  /**
   * Return the error state in a simple TRUE/FALSE manner.
   *
   * This is similar to get_error(), but should instead be used to to check to see if there is an error and not check what the error is set to.
   *
   * @return bool
   *   TRUE if an error is assigned and FALSE if no error is assigned.
   *
   * @see: get_error()
   */
  public function has_error() {
    // when there is no error flag assigned, its value should be NULL so a simple existence check should be all that is needed.
    return $this->error instanceof c_base_error;
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
   * Return the value.
   *
   * @return bool $value
   *   The value within this class.
   */
  public function get_value() {
    return TRUE;
  }

  /**
   * Return the value of the expected type.
   *
   * @return bool $value
   *   The value c_base_markup_tag stored within this class.
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
   * Return the value.
   *
   * @return bool $value
   *   The value within this class.
   */
  public function get_value() {
    return FALSE;
  }

  /**
   * Return the value of the expected type.
   *
   * @return bool $value
   *   The value c_base_markup_tag stored within this class.
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
}

/**
 * A return class representing a return value with a specific value.
 */
class c_base_return_value extends c_base_return {
  use t_base_return_value;

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
      $this->value = NULL;
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
      $this->value = NULL;
    }

    return $this->value;
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
      $this->value = NULL;
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
      $this->value = FALSE;
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
      $this->value = NULL;
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
      $this->value = '';
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
      $this->value = NULL;
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
      $this->value = 0;
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
    if (!is_null($this->value) && !is_int($this->value)) {
      $this->value = NULL;
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
      $this->value = 0.0;
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
    return self::p_s_value_exact($return, __CLASS__, array());
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
   * @param array $value
   *   Any value so long as it is an array.
   *   NULL is not allowed.
   * @param string $key
   *   A key to assign a specific value to.
   * @param string $type
   *   (optional) When key is not NULL, a specific known type to assign.
   *   This does nothing if $key is not provided.
   *   This is used for validation purposes.
   *
   *   Supported known types:
   *     'bool': a boolean value.
   *     'int': an integer value.
   *     'string': a string value.
   *     'array': an array value.
   *     'object': an object value.
   *     'resource': a generic resource value.
   *     'stream': a stream resource value.
   *     'socket': a socket resource value.
   *     'numeric': a string value that represents either an int or float.
   *     'null': A null value.
   *     NULL: no specific type requirements.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value_at($value, $key, $type = NULL) {
    if (!is_string($key) || empty($key)) {
      return FALSE;
    }

    // when type is not supplied, return a generic type.
    if (is_null($type)) {
      $this->value[$key] = $value;
      return TRUE;
    }

    // if type is supplied, it must be string.
    if (!is_string($type) || empty($type)) {
      return FALSE;
    }

    if ($type == 'bool' && !is_bool($value)) {
      return FALSE;
    }
    elseif ($type == 'int' && !is_int($value)) {
      return FALSE;
    }
    elseif ($type == 'float' && !is_float($value)) {
      return FALSE;
    }
    elseif ($type == 'numeric' && !is_numeric($value)) {
      return FALSE;
    }
    elseif ($type == 'string' && !is_string($value)) {
      return FALSE;
    }
    elseif ($type == 'array' && !is_array($value)) {
      return FALSE;
    }
    elseif ($type == 'object' && !is_object($value)) {
      return FALSE;
    }
    elseif ($type == 'resource' && !is_resource($value)) {
      return FALSE;
    }
    elseif ($type == 'stream') {
      if (!is_resource($value) || get_resource_type($value) != 'stream') {
        return FALSE;
      }
    }
    elseif ($type == 'socket') {
      if (!is_resource($value) || get_resource_type($value) != 'socket') {
        return FALSE;
      }
    }
    elseif ($type == 'null' && !is_null($value)) {
      return FALSE;
    }

    $this->value[$key] = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return array|null $value
   *   The value array stored within this class.
   *   NULL may be returned if there is no defined valid array.
   */
  public function get_value() {
    if (!is_null($this->value) && !is_array($this->value)) {
      $this->value = NULL;
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
      $this->value = array();
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
   * @param string $key
   *   A key to assign a specific value to.
   * @param string $type
   *   (optional) When key is not NULL, a specific known type to assign.
   *   This does nothing if $key is not provided.
   *   This is used for validation purposes.
   *
   *   Supported known types:
   *     'bool': a boolean value.
   *     'int': an integer value.
   *     'string': a string value.
   *     'array': an array value.
   *     'object': an object value.
   *     'resource': a generic resource value.
   *     'stream': a stream resource value.
   *     'socket': a socket resource value.
   *     'numeric': a string value that represents either an int or float.
   *     'null': A null value.
   *     NULL: no specific type requirements.
   *
   * @return
   *   Value on success, FALSE otherwise.
   *   Warning: There is no way to distinguish a return value of FALSE for an error to a valid FALSE when $type is set to 'bool'.
   */
  public function get_value_at($key, $type = NULL) {
    if (!is_string($key) || empty($key)) {
      return FALSE;
    }

    // if type is supplied, it must be string.
    if (!is_null($type) && (!is_string($type) || empty($type))) {
      return FALSE;
    }

    if (!is_array($this->value)) {
      $this->value = array();
    }

    if (!array_key_exists($key, $this->value)) {
      return FALSE;
    }

    if (!is_null($type)) {
      if ($type == 'bool') {
        if (!is_bool($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'int') {
        if (!is_int($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'float') {
        if (!is_float($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'numeric') {
        if (!is_numeric($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'string') {
        if (!is_string($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'array') {
        if (!is_array($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'object') {
        if (!is_object($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'resource') {
        if (!is_resource($this->value[$key])) {
          return FALSE;
        }
      }
      elseif ($type == 'stream') {
        if (!is_resource($this->value[$key]) || get_resource_type($this->value[$key]) != 'stream') {
          return FALSE;
        }
      }
      elseif ($type == 'socket') {
        if (!is_resource($this->value[$key]) || get_resource_type($this->value[$key]) != 'socket') {
          return FALSE;
        }
      }
      elseif ($type == 'null') {
        if (!is_null($this->value[$key])) {
          return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }

    return $this->value[$key];
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
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!is_object($value)) {
      return FALSE;
    }

    $this->value = $value;
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
   * @todo: this is incomplete because the base_error class is not yet written.
   *
   * @param todo|null $error
   *   (optional) a custom error setting.
   *
   * @return c_base_return_true
   *   A c_base_return_true object with the error value populated.
   */
  public static function s_true($error = NULL) {
    $object_error = new c_base_error();

    $object_return = new c_base_return_true();
    $object_return->set_error($object_error);

    if (!is_null($error)) {
      // @todo: do something with the code.
    }

    return $object_return;
  }

  /**
   * Creates a return boolean TRUE with the error value populated.
   *
   * @todo: this is incomplete because the base_error class is not yet written.
   *
   * @param todo|null $error
   *   (optional) a custom error setting.
   *
   * @return c_base_return_false
   *   A c_base_return_true object with the error value populated.
   */
  public static function s_false($error = NULL) {
    $object_error = new c_base_error();

    $object_return = new c_base_return_false();
    $object_return->set_error($object_error);

    if (!is_null($error)) {
      // @todo: do something with the code.
    }

    return $object_return;
  }

  /**
   * Creates a return boolean TRUE with the error value populated.
   *
   * @todo: this is incomplete because the base_error class is not yet written.
   *
   * @param $value
   *   A value to provide
   * @param $class
   *   A custom class name.
   * @param todo|null $error
   *   (optional) a custom error setting.
   *
   * @return c_base_return_false|c_base_return_value
   *   A c_base_return_value object is returned with the error value populated
   *   If the passed class is invalid or not of type c_base_return_value, then a c_base_return_true object with the error value populated.
   */
  public static function s_value($value, $class, $error = NULL) {
    if (!class_exists($class) || !($class instanceof c_base_return_value)) {
      return self::s_false($error);
    }

    $object_error = new c_base_error();

    $object_return = new $class();
    $object_return->set_error($object_error);
    $object_return->set_value($value);

    if (!is_null($error)) {
      // @todo: do something with the code.
    }

    return $object_return;
  }
}