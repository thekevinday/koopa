<?php
/**
 * @file
 * Provides classes for specific Postgesql Queries Parameter support.
 *
 * @errata:
 *   There is a lot of indecision on how this is to be design/used.
 *   Query parameters are being provided as a possible solution and may be removed in the future.
 *   I need to sit down and review the design before coming to a decision.
 *   Until then, this is being provided as a possibility.
 *   That said, I am strongly considering having the SQL generation functions do the actual generation of the query parameters.
 *   All would leave to implement would be embeded queries, which would be handled by some aspect of this implementation.
 *   The query parameters (or something like that) is needed for sub-query support.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/interfaces/database_query_parameter.php');

require_once('common/database/classes/database_query.php');

/**
 * A query parameter representing a string.
 *
 * @see: c_base_return_string
 */
class c_database_query_parameter extends c_base_return_string implements i_database_query_parameter {

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
}

/**
 * A query parameter representing a boolean.
 *
 * @see: c_base_return_bool
 */
class c_database_query_parameter_bool extends c_base_return_bool implements i_database_query_parameter {

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
}

/**
 * A query parameter representing a integer.
 *
 * @see: c_base_return_int
 */
class c_database_query_parameter_int extends c_base_return_int implements i_database_query_parameter {

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
}

/**
 * A query parameter representing a float.
 *
 * @see: c_base_return_float
 */
class c_database_query_parameter_float extends c_base_return_float implements i_database_query_parameter {

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
}

/**
 * A query parameter representing a json array, stored in this object as an array.
 *
 * @see: c_base_return_array
 */
class c_database_query_parameter_json extends c_base_return_array implements i_database_query_parameter {

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
   *   FALSE is returned if any value in the array is not of type i_base_query_parameter.
   */
  public function set_value($value) {
    if (!is_array($value)) {
      return FALSE;
    }

    // guarantee that only i_base_query_parameter are assigend in the array.
    foreach ($value as $v) {
      if (!($v instanceof i_base_query_parameter)) {
        unset($v);
        return FALSE;
      }
    }
    unset($v);

    $this->value = $value;
    return TRUE;
  }

  /**
   * Assign the value at a specific index in the array.
   *
   * @param i_base_query_parameter $value
   *   Any i_base_query_parameter implementation to be assigned at the specified position in the array.
   * @param int|string $key
   *   A key to assign a specific value to.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   FALSE is returned if value is not of type i_base_query_parameter.
   */
  public function set_value_at($value, $key) {
    if (!($value instanceof i_base_query_parameter)) {
      return FALSE;
    }

    return parent::set_value_at($value, $key);
  }

  /**
   * Append the value at the end of the array.
   *
   * @param i_base_query_parameter $value
   *   Any i_base_query_parameter to be appended in the array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   FALSE is returned if value is not of type i_base_query_parameter.
   */
  public function set_value_append($value) {
    if (!($value instanceof i_base_query_parameter)) {
      return FALSE;
    }

    return parent::set_value_append($value);
  }

  /**
   * Assigns the array from a serialized array string.
   *
   * @param string $serialized
   *   A serialized string to convert to an array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   if converted string does not produce an array, FALSE is returned and value is set to an empty array.
   *   FALSE is returned if any value in the array is not of type i_base_query_parameter.
   *
   * @see: unserialize()
   */
  public function set_value_serialized($serialized) {
    if (!is_string($serialized)) {
      return FALSE;
    }

    $unserialized = unserialize($serialized);
    if (is_array($unserialized)) {
      // guarantee that only i_base_query_parameter are assigend in the array.
      foreach ($unserialized as $v) {
        if (!($v instanceof i_base_query_parameter)) {
          unset($v);
          return FALSE;
        }
      }
      unset($v);

      $this->value = $unserialized;
      unset($unserialized);

      return TRUE;
    }
    unset($unserialized);

    $this->value = [];
    return FALSE;
  }

  /**
   * Assigns the array from a json-serialized array string.
   *
   * @param string $jsonized
   *   A jsonized string to convert to an array.
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
   *   FALSE is returned if any value in the array is not of type i_base_query_parameter.
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
      // guarantee that only i_base_query_parameter are assigend in the array.
      foreach ($unserialized as $v) {
        if (!($v instanceof i_base_query_parameter)) {
          unset($v);
          return FALSE;
        }
      }
      unset($v);

      $this->value = $decoded;
      unset($decoded);

      return TRUE;
    }
    unset($decoded);

    $this->value = [];
    return FALSE;
  }
}

/**
 * A query parameter representing an array of query parameters, stored in this object as an array.
 *
 * All array values must be an implementation of type i_database_query_parameter.
 *
 * @see: c_base_return_array
 */
class c_database_query_parameter_set extends c_base_return_array implements i_database_query_parameter {

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
   *   FALSE is returned if any value in the array is not of type i_base_query_parameter.
   */
  public function set_value($value) {
    if (!is_array($value)) {
      return FALSE;
    }

    // guarantee that only i_base_query_parameter are assigend in the array.
    foreach ($value as $v) {
      if (!($v instanceof i_base_query_parameter)) {
        unset($v);
        return FALSE;
      }
    }
    unset($v);

    $this->value = $value;
    return TRUE;
  }

  /**
   * Assign the value at a specific index in the array.
   *
   * @param i_base_query_parameter $value
   *   Any i_base_query_parameter implementation to be assigned at the specified position in the array.
   * @param int|string $key
   *   A key to assign a specific value to.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   FALSE is returned if value is not of type i_base_query_parameter.
   */
  public function set_value_at($value, $key) {
    if (!($value instanceof i_base_query_parameter)) {
      return FALSE;
    }

    return parent::set_value_at($value, $key);
  }

  /**
   * Append the value at the end of the array.
   *
   * @param i_base_query_parameter $value
   *   Any i_base_query_parameter to be appended in the array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   FALSE is returned if value is not of type i_base_query_parameter.
   */
  public function set_value_append($value) {
    if (!($value instanceof i_base_query_parameter)) {
      return FALSE;
    }

    return parent::set_value_append($value);
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
   *   FALSE is returned if any value in the array is not of type i_base_query_parameter.
   *
   * @see: unserialize()
   */
  public function set_value_serialized($serialized) {
    if (!is_string($serialized)) {
      return FALSE;
    }

    $unserialized = unserialize($serialized);
    if (is_array($unserialized)) {
      // guarantee that only i_base_query_parameter are assigend in the array.
      foreach ($unserialized as $v) {
        if (!($v instanceof i_base_query_parameter)) {
          unset($v);
          return FALSE;
        }
      }
      unset($v);

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
   *   FALSE is returned if any value in the array is not of type i_base_query_parameter.
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
      // guarantee that only i_base_query_parameter are assigend in the array.
      foreach ($unserialized as $v) {
        if (!($v instanceof i_base_query_parameter)) {
          unset($v);
          return FALSE;
        }
      }
      unset($v);

      $this->value = $decoded;
      unset($decoded);

      return TRUE;
    }
    unset($decoded);

    $this->value = [];
    return FALSE;
  }
}

/**
 * A query parameter representing an embedded query.
 *
 * @see: c_base_return_object
 * @see: i_database_query
 */
class c_database_query_parameter_query extends c_base_return_object implements i_database_query_parameter {

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
    $this->value = clone($value);
    return parent::set_value($value);
  }

  /**
   * Return the value.
   *
   * @return object|null $value
   *   The value object stored within this class.
   *   NULL may be returned if there is no defined valid resource.
   */
  public function get_value() {
    if ($this->value instanceof i_database_query) {
      return $this->value;
    }

    $this->value = NULL;
    return NULL;
  }

  /**
   * Return the value of the expected type.
   *
   * @return array $value
   *   The value array stored within this class.
   */
  public function get_value_exact() {
    if ($this->value instanceof i_database_query) {
      return $this->value;
    }

    $class = __CLASS__;
    return new $class();
  }
}
