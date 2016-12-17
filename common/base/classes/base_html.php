<?php
/**
 * @file
 * Provides a class for managing HTML5 Markup.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 *
 * @see: https://www.w3.org/TR/html5/
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for HTML tag attributes.
 *
 * This is for the internal storage of the attributes and not external.
 * Externally, every attribute type is a string.
 * The internal uses PHP structures where easily possible.
 * Special case string values, such as percentage symbols, are not used internally.
 *
 * @todo: should the class name include internal?
 * @todo: should external be in the html output class?
 * @todo: what about form processing/validation (which has external values)?
 * ---- Above is old comments to be reviewed. ----
 *
 * A generic class for HTML tag attributes.
 * This should accept and handle all
 *
 * @see: https://www.w3.org/TR/html5/forms.html#forms
 */
class c_base_html_attribute_values {
  const TYPE_NONE                  = 0;
  const TYPE_BOOLEAN               = 1; // https://www.w3.org/TR/html5/infrastructure.html#boolean-attributes
  const TYPE_ENUMERATED            = 2; // https://www.w3.org/TR/html5/infrastructure.html#keywords-and-enumerated-attributes
  const TYPE_NUMBER                = 3; // https://www.w3.org/TR/html5/infrastructure.html#numbers
  const TYPE_NUMBER_SIGNED         = 4; // https://www.w3.org/TR/html5/infrastructure.html#signed-integers
  const TYPE_NUMBER_UNSIGNED       = 5; // https://www.w3.org/TR/html5/infrastructure.html#non-negative-integers
  const TYPE_NUMBER_FLOAT          = 6; // https://www.w3.org/TR/html5/infrastructure.html#floating-point-numbers
  const TYPE_NUMBER_DIMENSION      = 7; // https://www.w3.org/TR/html5/infrastructure.html#percentages-and-dimensions
  const TYPE_NUMBER_LIST           = 8; // https://www.w3.org/TR/html5/infrastructure.html#lists-of-integers
  const TYPE_NUMBER_DIMENSION_LIST = 9; // https://www.w3.org/TR/html5/infrastructure.html#lists-of-dimensions
  const TYPE_DATE                  = 10; // https://www.w3.org/TR/html5/infrastructure.html#dates-and-times
  const TYPE_DATE_MONTH            = 11; // https://www.w3.org/TR/html5/infrastructure.html#months
  const TYPE_DATE_DATES            = 12; // https://www.w3.org/TR/html5/infrastructure.html#dates
  const TYPE_DATE_DATES_YEARLESS   = 13; // https://www.w3.org/TR/html5/infrastructure.html#yearless-dates
  const TYPE_DATE_TIMES            = 14; // https://www.w3.org/TR/html5/infrastructure.html#times
  const TYPE_DATE_DATES_FLOATING   = 15; // https://www.w3.org/TR/html5/infrastructure.html#floating-dates-and-times
  const TYPE_DATE_TIMEZONE         = 16; // https://www.w3.org/TR/html5/infrastructure.html#time-zones
  const TYPE_DATE_GLOBAL           = 17; // https://www.w3.org/TR/html5/infrastructure.html#global-dates-and-times
  const TYPE_DATE_WEEKS            = 18; // https://www.w3.org/TR/html5/infrastructure.html#weeks
  const TYPE_DATE_DURATION         = 19; // https://www.w3.org/TR/html5/infrastructure.html#durations
  const TYPE_DATE_VAGUE            = 20; // https://www.w3.org/TR/html5/infrastructure.html#vaguer-moments-in-time
  const TYPE_COLOR                 = 21; // https://www.w3.org/TR/html5/infrastructure.html#colors
  const TYPE_TOKENS_SPACE          = 22; // https://www.w3.org/TR/html5/infrastructure.html#space-separated-tokenss
  const TYPE_TOKENS_COMMA          = 23; // https://www.w3.org/TR/html5/infrastructure.html#comma-separated-tokens
  const TYPE_REFERENCE             = 24; // https://www.w3.org/TR/html5/infrastructure.html#syntax-references
  const TYPE_MEDIA                 = 25; // https://www.w3.org/TR/html5/infrastructure.html#mq
  const TYPE_URL                   = 26; // https://www.w3.org/TR/html5/infrastructure.html#urls

  const VALUE_NONE      = 0;
  const VALUE_TRUE      = 1;
  const VALUE_FALSE     = 2;
  const VALUE_INHERITED = 3;

  /**
   * Class constructor.
   */
  public function __construct() {
    // do nothing.
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    // do nothing.
  }

  /**
   * Validate that value is a boolean.
   *
   * @param int $value
   *   The value to validate.
   * @param bool $include_inherited
   *   (optional) When TRUE, the "Inherited" state is supported.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * see: https://www.w3.org/TR/html5/infrastructure.html#boolean-attributes
   */
  public static function is_boolean($value, $include_inherited = FALSE) {
    if (!is_int($value)) {
      return new c_base_return_false();
    }

    if (!is_bool($include_inherited)) {
      return c_base_return_error::s_false();
    }

    switch ($value) {
      self::VALUE_NONE:
      self::VALUE_TRUE:
      self::VALUE_FALSE:
        return new c_base_return_true();
      self::VALUE_INHERITED:
        if ($include_inherited) {
          return new c_base_return_true();
        }
        break;
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is an enumerated (or keyword) value.
   *
   * @param string $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#keywords-and-enumerated-attributes
   */
  public static function is_enumerated($value) {
    if (is_string($value)) {
      // an enumerated (or keyword) value is simply a fancy name for string/text.
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is a number value.
   *
   * @param int $value
   *   The value to validate.
   * @param bool $as_unsigned
   *   (optional) Wnen TRUE, number is treated as unsigned.
   *   When FALSE, number is treated as signed.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#numbers
   * @see: https://www.w3.org/TR/html5/infrastructure.html#non-negative-integers
   * @see: https://www.w3.org/TR/html5/infrastructure.html#time-zones
   * @see: https://www.w3.org/TR/html5/infrastructure.html#global-dates-and-times
   * @see: https://www.w3.org/TR/html5/infrastructure.html#durations
   * @see: https://www.w3.org/TR/html5/infrastructure.html#colors
   */
  public static function is_number($value, $as_unsigned = FALSE) {
    if (!is_bool($as_unsigned)) {
      return c_base_return_error::s_false();
    }

    if ($as_unsigned) {
      if (is_int($value)) {
        if ($value >= 0) {
          return new c_base_return_true();
        }
      }
    }
    else {
      if (is_int($value)) {
        return new c_base_return_true();
      }
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is a float value.
   *
   * @param float $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#floating-point-numbers
   */
  public static function is_float($value) {
    if (is_float($value)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is an unsigned number value.
   *
   * This is functionaly the same as a float or an unsigned integer.
   *
   * @param float $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::is_float()
   * @see: https://www.w3.org/TR/html5/infrastructure.html#percentages-and-dimensions
   */
  public static function is_dimension($value) {
    if (is_int($value) || is_float($value)) {
      if ($value >= 0) {
        return new c_base_return_true();
      }
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is a list of numbers.
   *
   * @param array $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#lists-of-integers
   */
  public static function is_number_list($value) {
    if (!is_array($value)) {
      return new c_base_return_false();
    }

    foreach ($value as $part) {
      if (!is_int($part)) {
        unset($part);
        return c_base_return_false();
      }
    }
    unset($part);

    return new c_base_return_true();
  }

  /**
   * Validate that value is a list of dimensions.
   *
   * @param array $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#lists-of-dimensions
   */
  public static function is_dimension_list($value) {
    if (!is_array($value)) {
      return new c_base_return_false();
    }

    foreach ($value as $part) {
      if (!is_int($part) && !is_float($part)) {
        unset($part);
        return c_base_return_false();
      }
    }
    unset($part);

    return new c_base_return_true();
  }

  /**
   * Validate that value is a date or time value.
   *
   * A date or time is expected to be a unix timestamp, which is a valid integer.
   *
   * @param int $value
   *   The value to validate.
   * @param bool $as_float
   *   (optional) When TRUE, allow the date to be a float.
   *   When FALSE, date is treated as an integer.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#dates-and-times
   * @see: https://www.w3.org/TR/html5/infrastructure.html#months
   * @see: https://www.w3.org/TR/html5/infrastructure.html#floating-dates-and-times
   * @see: https://www.w3.org/TR/html5/infrastructure.html#vaguer-moments-in-time
   */
  public static function is_date($value, $as_float = FALSE) {
    if (!is_bool($as_float)) {
      return c_base_return_error::s_false();
    }

    if ($as_float) {
      if (is_float($value)) {
        return new c_base_return_true();
      }
    }
    else {
      if (is_int($value)) {
        return new c_base_return_true();
      }
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is a list of dates or times.
   *
   * @param array $value
   *   The value to validate.
   * @param bool $as_float
   *   (optional) When TRUE, allow the date to be a float.
   *   When FALSE, date is treated as an integer.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#dates
   * @see: https://www.w3.org/TR/html5/infrastructure.html#yearless-dates
   * @see: https://www.w3.org/TR/html5/infrastructure.html#times
   */
  public static function is_date_list($value, $as_float = FALSE) {
    if (!is_bool($as_float)) {
      return c_base_return_error::s_false();
    }

    if (!is_array($value)) {
      return new c_base_return_false();
    }

    if ($as_float) {
      foreach ($value as $part) {
        if (!is_float($part)) {
          unset($part);
          return c_base_return_false();
        }
      }
      unset($part);
    }
    else {
      foreach ($value as $part) {
        if (!is_int($part)) {
          unset($part);
          return c_base_return_false();
        }
      }
      unset($part);
    }

    return new c_base_return_true();
  }

  /**
   * Validate that value is a token value.
   *
   * This processes a single token.
   * There are two types:
   * 1) Space Separated
   * 2) Comma Separated
   *
   * Perhaps I am misreading but the standard does not seem clear about other symbols.
   * For now, I am just allowing them (I can come back later and fix this at any time).
   *
   * @param string $value
   *   The value to validate.
   * @param bool $comma_separated
   *   (optional) When TRUE, token is treated as a comma separated token.
   *   When FALSE, token is treated as space separated token.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/html5/infrastructure.html#space-separated-tokens
   * @see: https://www.w3.org/TR/html5/infrastructure.html#comma-separated-tokens
   */
  public static function is_token($value, $comma_separated = FALSE) {
    if (!is_bool($comma_separated)) {
      return c_base_return_error::s_false();
    }

    if (is_string($value)) {
      if ($comma_Separated) {
        if (strpos($value, ',') !== FALSE) {
          return new c_base_return_true();
        }
      }
      else {
        if (preg_match('/\s/i', $value) === 0) {
          return new c_base_return_true();
        }
      }
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is a list of syntax references.
   *
   * @param array $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * https://www.w3.org/TR/html5/infrastructure.html#syntax-references
   */
  public static function is_syntax_reference($value) {
    if (!is_string($value)) {
      return new c_base_return_false();
    }

    if (preg_match('/^#(^s)+/i', $value) > 0) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Validate that value is a list of syntax references.
   *
   * @param array $value
   *   The value to validate.
   *
   * @return c_base_return_status
   *   TRUE on valid, FALSE on invalid.
   *   FALSE with error bit set is returned on error.
   *
   * https://www.w3.org/TR/html5/infrastructure.html#urls
   */
  public static function is_url($value) {
    if (!is_string($value)) {
      return new c_base_return_false();
    }

    // @todo.

    return new c_base_return_false();
  }
}
