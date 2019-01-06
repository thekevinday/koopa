<?php
/**
 * @file
 * Provides traits for managing return values.
 */
namespace n_koopa;

/**
 * A generic trait for a return value class to have some value data.
 *
 * Return classes can be defined without this.
 * - An example would be a boolean class whose name defines its state.
 *
 * Most return classes will use this trait.
 *
 * This does not define has_value(), get_value(), and get_value_exact().
 * Those functions are always gauranteed by the class c_base_return.
 *
 * @require class base_error
 */
trait t_base_return_value {
  protected $value;

  /**
   * Provide to string object override.
   *
   * @return string
   *   The string representation of the value contained in this object.
   *
   * @see: http://php.net/manual/en/language.oop5.magic.php@object.tostring
   */
  public function __toString() {
    return strval($this->value);
  }

  /**
   * Assign the value.
   *
   * If the value is an object, then this should create a copy of the object (a clone).
   *
   * @param $value
   *   This can be anything that is to be considered a return value.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (is_object($value)) {
      $this->value = clone($value);
    }
    else {
      $this->value = $value;
    }

    return TRUE;
  }

  /**
   * Determine if this class has a value assigned to it.
   *
   * @return bool
   *   TRUE if the value is assigned to something other than NULL, FALSE otherwise.
   */
  public function has_value() {
    return !is_null($this->value);
  }

  /**
   * Return the value.
   *
   * @return $value
   *   The value stored within this class.
   */
  public function get_value() {
    return $this->value;
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
   * Return the value of the expected type.
   *
   * @return $value
   *   The value stored within this class.
   */
  public function get_value_exact() {
    return $this->value;
  }

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
  protected $message;

  /**
   * Assign the message.
   */
  public function set_message($message) {
    if (!is_string($message)) {
      return;
    }

    $this->message = $message;
  }

  /**
   * Return the message.
   *
   * @return string
   *   A message string is returned.
   *   If no message is assigned, an empty string is returned.
   */
  public function get_message() {
    if (is_string($this->message)) {
      return $this->message;
    }

    return '';
  }
}

/**
 * A trait for a return value that may be assigned an object via reference.
 */
trait t_base_return_reference_set {
  /**
   * Assign the value, using reference instead of a copy.
   *
   * If the value is an object, then this should contain a reference to the object.
   *
   * @param $value
   *   This can be anything that is to be considered a return value.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   FALSE is returned when $value is not an object.
   */
  public function set_value_reference($value) {
    if (is_object($value)) {
      $this->value = $value;
      return TRUE;
    }

    return FALSE;
  }
}

/**
 * A trait for a return value that may return an object via reference.
 */
trait t_base_return_reference_get {

  /**
   * Return the value, by reference.
   *
   * @return null|object $value
   *   A reference to the value within this class.
   *   NULL is returned if no reference can be returned.
   */
  public function get_value_reference() {
    if (is_object($this->value)) {
      return $this->value;
    }

    return NULL;
  }
}

/**
 * A trait for a return value that may return an object via reference of the expected type.
 */
trait t_base_return_reference_get_exact {

  /**
   * Return the value, by reference, of the expected type.
   *
   * @return object $value
   *   A reference to the value within this class.
   *   A new object is created and returned if a reference would otherwise be unreturnable.
   */
  public abstract function get_value_reference_exact();
}
