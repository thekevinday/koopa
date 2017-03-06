<?php
/**
 * @file
 * Provides UTF-8 support.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A class for managing and processing utf-8.
 */
class c_base_utf8 {
  const BOM = "\xef\xbb\xbf";
  const UTF_8 = 'UTF-8';

  const SPACE_NO_BREAK        = 160;
  const SPACE_NO_BREAK_NARROW = 8239;
  const SPACE_OGHAM_MARK      = 5760;
  const SPACE_MONGOLIAN_VOWEL = 6158;
  const SPACE_EN              = 8194;
  const SPACE_EN_QUAD         = 8192;
  const SPACE_EM              = 8195;
  const SPACE_EM_QUAD         = 8193;
  const SPACE_EM_THREE_PER    = 8196;
  const SPACE_EM_FOUR_PER     = 8197;
  const SPACE_EM_SIX_PER      = 8198;
  const SPACE_FIGURE          = 8199;
  const SPACE_PUNCTUTION      = 8200;
  const SPACE_THIN            = 8201;
  const SPACE_HAIR            = 8202;
  const SPACE_LINE            = 8232;
  const SPACE_PARAGRAPH       = 8233;
  const SPACE_MATHEMATICAL    = 8287;
  const SPACE_IDEOGRAPHIC     = 12288;

  /**
   * UTF_8-X masks and marks.
   * - UTF_8-1: 0bxxxxxxxx (0b0xxxxxxx) (0000 0000)
   * - UTF_8-2: 0byyyyyxxxxxx (0b110yyyyy, 0b10xxxxxx) (1100 0000 1000 0000)
   * - UTF_8-3: 0bzzzzyyyyyyxxxxxx (0b1110zzzz, 0b10yyyyyy, 0b10xxxxxx) (1110 0000 1000 0000 1000 0000)
   * - UTF_8-4: 0bvvvzzzzzzyyyyyyxxxxxx (0b11110vvv, 0b10zzzzzz, 0b10yyyyyy, 0b10xxxxxx) (1111 1000 1000 0000 1000 0000 1000 0000)
   *
   * The masks for UTF_8 are intended to represent the bits that will never be present for the given UTF_8 group.
   *
   * The marks for UTF_8 are intended to represent the exact "left" order bit combination that are required to represent the UTF_8 group.
   *
   * The bits are for checking a single 8-bit character value (specifically, checking the first bits).
   */
  const MASK_1 = 0b11111111111111111111111110000000;
  const MASK_2 = 0b11111111111111110000000000000000;
  const MASK_3 = 0b11111111000000000000000000000000;
  const MASK_4 = 0b00000000000000000000000000000000;

  const MARK_1 = 0b00000000000000000000000000000000;
  const MARK_2 = 0b00000000000000001100000010000000;
  const MARK_3 = 0b00000000111000001000000010000000;
  const MARK_4 = 0b11110000100000001000000010000000;

  const BIT_1 = 0b10000000;
  const BIT_2 = 0b11000000;
  const BIT_3 = 0b11100000;
  const BIT_4 = 0b11110000;


  /**
   * Checks whether the passed string contains only byte sequances that appear valid UTF-8 characters.
   *
   * @param string $text
   *   The string to be checked.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: mb_detect_encoding()
   */
  public static function s_is_UTF_8($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (mb_check_encoding($text, self::UTF_8)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Calculates integer value of the given UTF-8 encoded character.
   *
   * @param string $character
   *   The character of which to calculate ordinal of.
   *
   * @return c_base_return_int|c_base_return_status
   *   Ordinal value of the given character.
   *   FALSE without error bit set is returned on invalid UTF-8 byte sequence
   *   FALSE with error bit set is returned on error.
   */
  public static function s_character_to_ordinal($character) {
    if (!is_string($character)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'character', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $ordinal = self::p_s_character_to_ordinal($character);
    if ($ordinal === FALSE) {
      unset($ordinal);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_character_to_ordinal', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($ordinal);
  }

  /**
   * Generates a UTF-8 encoded character from the given ordinal.
   *
   * @param int $ordinal
   *   The ordinal for which to generate a character.
   *
   * @return c_base_return_string|c_base_return_status
   *   Milti-Byte character returns empty string on failure to encode.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_ordinal_to_character($ordinal) {
    if (!is_int($ordinal)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ordinal', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new(self::p_s_ordinal_to_character($ordinal));
  }

  /**
   * Finds the length of the given string in terms of number of valid UTF-8 characters it contains.
   *
   * Invalid characters are ignored.
   *
   * @param string $text
   *   The string for which to find the character length.
   *
   * @return c_base_return_int|c_base_return_status
   *   Length of the Unicode String.
   *   FALSE with error bit set is returned on error.
   *
   * @see: mb_strlen()
   */
  public static function s_length_string($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $length = self::p_s_length_string($text);
    if ($length === FALSE) {
      unset($length);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_length_string', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($length);
  }

  /**
   * Removes all non-UTF-8 characters from a given string.
   *
   * @param string $text
   *   The string to be sanitized.
   * @param bool $remove_bom
   *   (optional) When TRUE, the UTF_8 BOM character is removed.
   *   When FALSE, no action related to the BOM character is performed.
   *
   * @return c_base_return_string|c_base_return_status
   *   Clean UTF-8 encoded string.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_clean($text, $remove_bom = FALSE) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($remove_bom)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'remove_bom', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $sanitized = self::p_s_clean($text);
    if ($sanitized === FALSE) {
      unset($sanitized);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_clean', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($sanitized);
  }

  /**
   * Convert a string to an array of Unicode characters.
   *
   * @param string $text
   *   The string to split into array.
   * @param int $split_length
   *   Max character length of each array element.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing chunks of the string.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_split($text, $split_length = 1) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($split_length)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'split_length', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $split = self::p_s_split($text, $split_length);
    if ($split === FALSE) {
      unset($split);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_split', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($split);
  }

  /**
   * Generates an array of each character of a Unicode string separated into individual bytes.
   *
   * @param string $text
   *   The original Unicode string.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of byte lengths of each character.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_character_size_list($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $size_list = self::p_s_character_size_list($text);
    if ($size_list === FALSE) {
      unset($size_list);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_character_size_list', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($size_list);
  }

  /**
   * Calculates and returns the maximum number of bytes taken by any UTF-8 encoded character in the given string.
   *
   * @param string $text
   *   The original Unicode string.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer representing the max character width found within the passed string.
   *   FALSE with error bit set is returned on error.
   *
   * @see: p_s_character_size_list()
   */
  public static function s_character_max_width($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $size_list = self::p_s_character_size_list($text);
    if ($size_list === FALSE) {
      unset($size_list);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_character_size_list', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new(max($size_list));
  }

  /**
   * Converts a UTF-8 character to HTML Numbered Entity like &#123;
   *
   * @param string $character
   *   The Unicode character to be encoded as numbered entity
   *
   * @return c_base_return_string|c_base_return_status
   *   HTML numbered entity.
   *   FALSE with error bit set is returned on error.
   *
   * @see: p_s_encode_html_character()
   */
  public static function s_encode_html_character($character) {
    if (!is_string($character)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'character', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $encoded = self::p_s_encode_html_character($character);
    if ($encoded === FALSE) {
      unset($encoded);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_encode_html_character', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($encoded);
  }

  /**
   * Converts a UTF-8 string to a series of HTML Numbered Entities, such as: &#123;&#39;&#1740;...
   *
   * @param string $text
   *   The Unicode string to be encoded as numbered entities.
   *
   * @return c_base_return_string|c_base_return_status
   *   HTML numbered entities.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_encode_html($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $split = self::p_s_split($text);
    if ($split === FALSE) {
      unset($split);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_split', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $new_array = array();
    foreach ($split as $character) {
      $encoded = self::p_s_character_to_ordinal($character);
      if ($encoded === FALSE) {
        // should some error reporting/handling be performed here?
        continue;
      }

      $new_array[] = $ordinal;
    }
    unset($encoded);
    unset($character);

    return c_base_return_string::s_new(implode($new_array));
  }

  /**
   * UTF-8 aware substr().
   *
   * substr() works with bytes, while s_substring works with characters and are identical in all other aspects.
   *
   * @param string $text
   *   The string to obtain a substring from.
   * @param int $start
   *   (optional) The start position.
   * @param int|null $length
   *   (optional) The length of the substring.
   *   If NULL, then the length is PHP_INT_MAX.
   *
   * @see: mb_substr()
   */
  public static function s_substring($text, $start = 0, $length = NULL) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($start)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'start', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($length) && !is_int($length)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'length', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new(self::p_s_substring($text, $start, $length));
  }

  /**
   * Checks if the given string is a Byte Order Mark.
   *
   * @param string $text
   *   The input string.
   *
   * @return c_base_return_status
   *   TRUE if the $text is Byte Order Mark, FALSE otherwise.
   *
   * @see: p_s_is_bom()
   */
  public static function s_is_bom($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (self::p_s_is_bom($text)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Checks if a file starts with BOM character.
   *
   * @param string $file_path
   *   Path to a valid file.
   *
   * @return c_base_return_status
   *   TRUE if the file has BOM at the start, FALSE otherwise.
   */
  public static function s_file_has_bom($file_path) {
    if (!is_string($file_path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'file_path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!file_exists($file_path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':file_name' => $file_path, ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
      return c_base_return_error::s_false($error);
    }

    if (self::p_s_is_bom(file_get_contents($file_path, 0, NULL, -1, 3))) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Checks if string starts with BOM character.
   *
   * @param string $text
   *   The input string.
   *
   * @return c_base_return_status
   *   TRUE if the string has BOM at the start, FALSE otherwise.
   */
  public static function s_string_has_bom($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (self::p_s_is_bom(substr($text, 0, 3))) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Prepends BOM character to the string and returns the whole string.
   *
   * If BOM already existed there, the Input string is returned.
   *
   * @param string $text
   *   The input string.
   *
   * @return c_base_return_string|c_base_return_status
   *   The output string that contains BOM.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_prepend_bom($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!s_is_bom(substr($text, 0, 3))) {
      return c_base_return_string::s_new(self::BOM . $text);
    }

    return c_base_return_string::s_new($text);
  }

  /**
   * Reverses characters order in the string.
   *
   * @param string $text
   *   The input string.
   *
   * @return c_base_return_string|c_base_return_status
   *   The string with characters in the reverse sequence.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_reverse($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $split = self::p_s_split($text);
    if ($split === FALSE) {
      unset($split);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_split', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new(implode(array_reverse($split)));
  }

  /**
   * Finds the number of Characters to the left of first occurance of the needle.
   *
   * strpos works with bytes, while s_position_string works with characters and are identical in all other aspects.
   *
   * @param string $haystack
   *   The string to search within.
   * @param string|int $needle
   *   The characters to search for or a single ordinal integer to search for.
   * $param int $offset
   *   (optional) position in the string to start searching.
   *   The offset cannot be negative.
   *
   * @return c_base_return_int|c_base_return_status
   *   A number representing the character position is returned when the needle is found in the haystack.
   *   FALSE with error bit set is returned on error.
   *   FALSE without the error bit set is returned when the needle is not found in the haystack.
   *
   * @see: mb_strpos()
   */
  public static function s_position_string($haystack, $needle, $offset = 0) {
    if (!is_string($haystack)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'haystack', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($needle) && !is_string($needle)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'needle', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($offset) || $offset < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'offset', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $search_for = $needle;
    if (is_int($needle)) {
      $search_for = self::p_s_ordinal_to_character($needle);
    }

    $search_for = self::p_s_clean($search_for);
    $target = self::p_s_clean($haystack);

    return c_base_return_int::s_new(mb_strpos($target, $search_for, $offset, self::UTF_8));
  }

  /**
   * Accepts a string and returns an array of ordinals.
   *
   * @param string|array $text
   *   A UTF-8 encoded string or an array of such strings.
   *
   * @return c_base_return_array|c_base_return_status
   *   The array of ordinals.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_string_to_ordinals($text) {
    if (!is_string($text) && !is_array($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new(self::p_s_string_to_ordinals($text));
  }

  /**
   * Converts an array of ordinals into a string.
   *
   * @param array $ordinals
   *   An array of Unicode code points.
   *
   * @return c_base_return_string|c_base_return_status
   *   A string representing the ordinals array.
   *   A string representing the ordinals array with error bit set is returned on error for non-critical string-processing errors.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_ordinals_to_string($ordinals) {
    if (!is_array($ordinals)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ordinals', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $errors = array();
    $string = '';
    foreach ($ordinals as $ordinal) {
      $character = self::p_s_ordinal_to_character($ordinal);
      if ($character === FALSE) {
        $errors[] = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_s_ordinal_to_character', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        continue;
      }

      $string .= $character;
    }
    unset($ordinal);
    unset($character);

    if (empty($errors)) {
      unset($errors);

      return c_base_return_string::s_new($string);
    }

    $return_string = c_base_return_string::s_new($string);
    foreach ($errors as $error) {
      $return_string->set_error($error);
    }
    unset($errors);
    unset($error);

    return $return_string;
  }

  /**
   * Converts an array of ordinals into an array of characters.
   *
   * @param array $ordinals
   *   An array of ordinals.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of characters represnting the ordinals array.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_ordinals_to_string_array($ordinals) {
    if (!is_array($ordinals)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ordinals', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $errors = array();
    $array = array();
    foreach ($ordinals as $ordinal) {
      $character = self::p_s_ordinal_to_character($ordinal);
      if ($character === FALSE) {
        $errors[] = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_s_ordinal_to_character', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        continue;
      }

      $array[] = $character;
    }
    unset($ordinal);
    unset($character);

    if (empty($errors)) {
      unset($errors);

      return c_base_return_array::s_new($array);
    }

    $return_array = c_base_return_array::s_new($array);
    foreach ($errors as $error) {
      $return_array->set_error($error);
    }
    unset($errors);
    unset($error);

    return $return_array;
  }

  /**
   * Makes a UTF-8 string from a ordinal array.
   *
   * @param array $ordinals
   *   An array of rdinals.
   *
   * @return c_base_return_string|c_base_return_status
   *   UTF-8 encoded string.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_ordinal_array_to_string($ordinals) {
    if (!is_array($ordinals)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ordinals', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $array = array();
    foreach ($ordinals as $key => $ordinal) {
      $array[$key] = self::p_s_ordinal_to_character($ordinal);
    }
    unset($key);
    unset($ordinal);

    return c_base_return_string::s_new(implode($array));
  }

  /**
   * Converts ordinal to a unicode codepoint.
   *
   * @param int $ordinal
   *   The ordinal to be converted to codepoint.
   *
   * @return c_base_return_int|c_base_return_status
   *   The codepoint.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_ordinal_to_codepoint($ordinal) {
    if (!is_int($ordinal)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'ordinal', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $codepoint = self::p_s_ordinal_to_codepoint($ordinal);
    if ($codepoint === FALSE) {
      unset($codepoint);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_orginal_to_codepoint', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_int::s_new($codepoint);
  }

  /**
   * Count the number of sub string occurances.
   *
   * @param string $haystack
   *   The string to search in.
   * @param string $needle
   *   The string to search for.
   * @param int $offset
   *   The offset where to start counting.
   * @param null|int $length
   *   The maximum length after the specified offset to search for the substring.
   *
   * @return c_base_return_int|c_base_return_status
   *   Number of occurances of $needle.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_count_substrings($haystack, $needle, $offset = 0, $length = NULL) {
    if (!is_string($haystack)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'haystack', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($needle)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'needle', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($offset)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'offset', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($length) && !is_int($length)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'length', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($offset || $length) {
      $haystack = s_substring($haystack, $offset, $length);
    }

    if (is_null($length)) {
      return c_base_return_int::s_new(substr_count($haystack, $needle, $offset));
    }

    return c_base_return_int::s_new(substr_count($haystack, $needle, $offset, $length));
  }

  /**
   * Checks if a string is 7 bit ASCII.
   *
   * @param string
   *   $text The string to check.
   *
   * @return c_base_return_status
   *   TRUE if ASCII, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_is_ascii($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (preg_match('/[\x80-\xff]/', $text)) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }

  /**
   * Strip HTML and PHP tags from a string.
   *
   * @param string $text
   *   UTF-8 string.
   * @param string $allowable_tags
   *   The tags to allow in the string.
   *
   * @return c_base_return_string|c_base_return_status
   *   The stripped string.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_strip_tags($text, $allowable_tags = '') {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'text', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($allowable_tags)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'allowable_tags', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // clean broken UTF_8.
    $sanitized = self::p_s_clean($text);
    if ($sanitized === FALSE) {
      unset($sanitized);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'self::p_s_clean', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new(strip_tags($sanitized, $allowable_tags));
  }

  /**
   * Private version of s_character_to_ordinal().
   *
   * @see: self::s_character_to_ordinal()
   */
  private static function p_s_character_to_ordinal($character) {
    $split = self::p_s_split($character);
    if ($split === FALSE) {
      unset($split);
      return FALSE;
    }

    $first = $split[0];
    unset($split);

    switch(strlen($first)) {
      case 1:
        return ord($first);

      case 2:
        return ((ord($first[0]) << 8) | ord($first[1]));

      case 3:
        return ((ord($first[0]) << 16) | (ord($first[1]) << 8) | ord($first[2]));

      case 4:
        return ((ord($first[0]) << 24) | (ord($first[1]) << 16) | (ord($first[2]) << 8) | ord($first[3]));
    }

    unset($first);
    return FALSE;
  }

  /**
   * Private version of s_ordinal_to_codepoint().
   *
   * @see: self::s_ordinal_to_codepoint()
   */
  private static function p_s_ordinal_to_codepoint($ordinal) {
    if ($ordinal == 0) {
      return 0;
    }

    if (($ordinal & self::MASK_1) == 0) {
      return $ordinal;
    }

    if (($ordinal & self::MASK_2) == 0) {
      $high = ($ordinal >> 8);
      $low = $ordinal - ($high << 8);

      return ((($high & 0x1f) << 6) | ($low & 0x3f));
    }

    if (($ordinal & self::MASK_3) == 0) {
      $high = ($ordinal >> 16);
      $medium = ($ordinal - ($high << 16) >> 8);
      $low = ($ordinal - ($high << 16) - ($medium << 8));

      return ((($high & 0x0f) << 12) | (($medium & 0x3f) << 6) | ($low & 0x3f));
    }


    $high = ($ordinal >> 24);
    $medium_1 = ($ordinal - ($high << 24) >> 16);
    $medium_2 = ($ordinal - ($high << 24) - ($medium_1 << 16) >> 8);
    $low = ($ordinal - ($high << 24) - ($medium_1 << 16) - ($medium_2 << 8));

    return ((($high & 0x07) << 18) | (($medium_1 & 0x3f) << 12) | (($medium_2 & 0x3f) << 6) | ($low & 0x3f));
  }

  /**
   * Private version of s_ordinal_to_character().
   *
   * @see: self::s_ordinal_to_character()
   */
  private static function p_s_ordinal_to_character($ordinal) {
    // mb_convert_encoding() accepts codepoints, so first convert the ordinal to a codepoint.
    $codepoint = self::p_s_ordinal_to_codepoint($ordinal);

    return mb_convert_encoding('&#' . $codepoint . ';', self::UTF_8, 'HTML-ENTITIES');
  }

  /**
   * Private version of: s_string_to_ordinals()
   *
   * @see: self::s_string_to_ordinals()
   */
  private static function p_s_string_to_ordinals($text) {
    if (is_string($text)) {
      $text = self::p_s_split($text);
    }

    $ordinals = array();
    foreach ($text as $character) {
      $value = self::p_s_character_to_ordinal($character);
      if ($value === FALSE) {
        continue;
      }

      $ordinals[] = $value;
    }
    unset($value);

    return $ordinals;
  }

  /**
   * Private version of s_length_string().
   *
   * @see: self::s_length_string()
   */
  private static function p_s_length_string($text) {
    return mb_strlen($text, self::UTF_8);
  }

  /**
   * Private version of s_clean().
   *
   * @see: self::s_clean()
   */
  private static function p_s_clean($text, $remove_bom = FALSE) {
    $sanitized = preg_replace('/([\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})|./s', '$1', $text);

    if (is_null($sanitized)) {
      unset($sanitized);
      return FALSE;
    }

    if ($remove_bom) {
      $sanitized = str_replace(self::BOM, '', $sanitized);
    }

    return $sanitized;
  }

  /**
   * Private version of s_split().
   *
   * @see: self::s_split()
   */
  private static function p_s_split($text, $split_length = 1) {
    $split = array();
    $text = self::p_s_clean($text);

    preg_match_all('/\X/u', $text, $split);
    $split = $split[0];

    if ($split_length > 1) {
      $split = array_chunk($split, $split_length);
      if (is_null($split)) {
        return FALSE;
      }

      $array = array();
      foreach ($split as $key => $value) {
        $array[$key] = implode($value);
      }
      unset($key);
      unset($value);

      $split = $array;
      unset($array);
    }

    if (empty($split) || $split[0] === '') {
      return array();
    }

    return $split;
  }

  /**
   * Private version of s_character_size_list().
   *
   * @see: self::s_character_size_list()
   */
  private static function p_s_character_size_list($text) {
    $split = self::p_s_split($text);
    if ($split === FALSE) {
      unset($split);
      return FALSE;
    }

    $array = array();
    foreach ($split as $key => $value) {
      $array[$key] = strlen($value);
    }
    unset($key);
    unset($value);
    unset($split);

    return $array;
  }

  /**
   * Private version of s_substring().
   *
   * @see: self::s_substring()
   */
  private static function p_s_substring($text, $start = 0, $length = NULL) {
    if (is_null($length)) {
      $length = PHP_INT_MAX;
    }

    $sanitized = self::p_s_clean($text);

    return mb_substr($sanitized, $start, $length, self::UTF_8);
  }

  /**
   * Private version of s_encode_html_character().
   *
   * @see: self::s_encode_html_character()
   */
  private static function p_s_encode_html_character($character) {
    $ordinal = p_s_character_to_ordinal($character);
    if ($ordinal === FALSE) {
      unset($ordinal);
      return FALSE;
    }

    $codepoint = p_s_ordinal_to_codepoint($ordinal);
    unset($ordinal);
    if ($codepoint === FALSE) {
      unset($codepoint);
      return FALSE;
    }

    return '&#' . $codepoint . ';';
  }

  /**
   * Private version of s_is_bom().
   *
   * @see: self::s_is_bom()
   */
  private static function p_s_is_bom($text) {
    if ($text === self::BOM) {
      return TRUE;
    }

    return FALSE;
  }
}
