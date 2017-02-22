<?php
/**
 * @file
 * Provides a class for managing common charsets.
 */

/**
 * A class for managing common rfc character sets.
 */
class c_base_charset {
  const UNDEFINED   = 0;
  const ASCII       = 1;
  const UTF_8       = 2;
  const UTF_16      = 3;
  const UTF_32      = 4;
  const ISO_8859_1  = 5;
  const ISO_8859_2  = 6;
  const ISO_8859_3  = 7;
  const ISO_8859_4  = 8;
  const ISO_8859_5  = 9;
  const ISO_8859_6  = 10;
  const ISO_8859_7  = 11;
  const ISO_8859_8  = 12;
  const ISO_8859_9  = 13;
  const ISO_8859_10 = 14;
  const ISO_8859_11 = 15;
  const ISO_8859_12 = 16;
  const ISO_8859_13 = 17;
  const ISO_8859_14 = 18;
  const ISO_8859_15 = 19;
  const ISO_8859_16 = 20;

  /**
   * Determine if the given code is a valid charset code.
   *
   * @param int $charset
   *   The integer to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_is_valid($charset) {
    if (!is_int($charset)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'charset', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($charset < self::ASCII || $charset > self::ISO_8859_16) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }

  /**
   * Convert the given code to a string.
   *
   * @param int $charset
   *   The integer to convert.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_to_string($charset) {
    if (!is_int($charset)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'charset', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($charset) {
      case self::UTF_8:
        return c_base_return_string::s_new('UTF-8');
      case self::ASCII:
        return c_base_return_string::s_new('ASCII');
      case self::UTF_16:
        return c_base_return_string::s_new('UTF-16');
      case self::UTF_32:
        return c_base_return_string::s_new('UTF-32');
      case self::ISO_8859_1:
        return c_base_return_string::s_new('ISO-8859-1');
      case self::ISO_8859_2:
        return c_base_return_string::s_new('ISO-8859-2');
      case self::ISO_8859_3:
        return c_base_return_string::s_new('ISO-8859-3');
      case self::ISO_8859_4:
        return c_base_return_string::s_new('ISO-8859-4');
      case self::ISO_8859_5:
        return c_base_return_string::s_new('ISO-8859-5');
      case self::ISO_8859_6:
        return c_base_return_string::s_new('ISO-8859-6');
      case self::ISO_8859_7:
        return c_base_return_string::s_new('ISO-8859-7');
      case self::ISO_8859_8:
        return c_base_return_string::s_new('ISO-8859-8');
      case self::ISO_8859_9:
        return c_base_return_string::s_new('ISO-8859-9');
      case self::ISO_8859_10:
        return c_base_return_string::s_new('ISO-8859-10');
      case self::ISO_8859_11:
        return c_base_return_string::s_new('ISO-8859-11');
      case self::ISO_8859_12:
        return c_base_return_string::s_new('ISO-8859-12');
      case self::ISO_8859_13:
        return c_base_return_string::s_new('ISO-8859-13');
      case self::ISO_8859_14:
        return c_base_return_string::s_new('ISO-8859-14');
      case self::ISO_8859_15:
        return c_base_return_string::s_new('ISO-8859-15');
      case self::ISO_8859_16:
        return c_base_return_string::s_new('ISO-8859-16');
    }

    $error = c_base_error::s_log(NULL, array('arguments' => array(':function_name' => __CLASS__ . '::' . __FUNCTION__), i_base_error_messages::FUNCTION_FAILURE));
    return c_base_return_error::s_false($error);
  }
}
