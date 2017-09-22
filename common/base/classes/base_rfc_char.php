<?php
/**
 * @file
 * Provides a class for managing common rfc character testing cases.
 */
namespace n_koopa;

require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_ascii.php');
require_once('common/base/classes/base_utf8.php');


/**
 * A class for managing common rfc character testing cases.
 *
 * This currently utilizes some of the rules defined in the following rfcs:
 * - rfc 4234
 * - rfc 5234
 * - rfc 6532
 *
 * Most of the rules reference the following rfcs as a guideline (but many of the rules defined therein are replaced by rules in the later rfcs):
 * - rfc 822
 *
 * This checks a single character, therefore:
 * - This does not perform tests that require more than a single character to identify what the text represents.
 * - This expects UTF_8 characters to be provided as a single 32-bit ordinal integer (as opposed to two distinct integers).
 *
 * WARNING: I am very new to UTF_8 coding and may be interpreting the rfc documentation incorrectly.
 *
 * Note: rfc 6532 does not mention whitespace of any kind and so non-ASCII "whitespace" is _not_ considered whitespace by this standard.
 *
 * @see: https://tools.ietf.org/html/rfc822
 * @see: https://tools.ietf.org/html/rfc4234
 * @see: https://tools.ietf.org/html/rfc5234
 * @see: https://tools.ietf.org/html/rfc5335
 * @see: https://tools.ietf.org/html/rfc6530
 * @see: https://tools.ietf.org/html/rfc6531
 * @see: https://tools.ietf.org/html/rfc6532
 * @see: https://tools.ietf.org/html/rfc6533
 * @see: http://www.herongyang.com/Unicode/UTF-8-UTF-8-Encoding.html
 *
 * @require class c_base_ascii
 * @require class c_base_utf8
 */
abstract class c_base_rfc_char extends c_base_return {

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
   * Check to see if character is: text.
   *
   * dtext = ASCII character codes 33, 35->91 and 93-126.
   * dtext = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_text($ordinal) {
    if (($ordinal > c_base_ascii::SPACE && $ordinal < c_base_ascii::BRACKET_OPEN) || ($ordinal > c_base_ascii::BRACKET_CLOSE && $ordinal < c_base_ascii::DELETE)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: ctext.
   *
   * ctext = ASCII character codes 33->39, 42->91, and 93-126.
   * ctext = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_ctext($ordinal) {
    if (($ordinal > c_base_ascii::SPACE && $ordinal < c_base_ascii::PARENTHESIS_OPEN) || ($ordinal > c_base_ascii::PARENTHESIS_CLOSE && $ordinal < c_base_ascii::SLASH_BACKWARD) || ($ordinal > c_base_ascii::SLASH_BACKWARD && $ordinal < c_base_ascii::DELETE)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: dtext.
   *
   * dtext = ASCII character codes 33->90 and 94-126.
   * dtext = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_dtext($ordinal) {
    if (($ordinal > c_base_ascii::SPACE && $ordinal < c_base_ascii::BRACKET_OPEN) || ($ordinal > c_base_ascii::BRACKET_CLOSE && $ordinal < c_base_ascii::DELETE)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: qtext.
   *
   * qtext = ASCII character codes 33, 35->91 and 93-126.
   * qtext = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_qtext($ordinal) {
    if ($ordinal == c_base_ascii::SPACE) {
      return TRUE;
    }

    if (($ordinal > c_base_ascii::QUOTE_DOUBLE && $ordinal < c_base_ascii::SLASH_BACKWARD) || ($ordinal > c_base_ascii::SLASH_BACKWARD && $ordinal < c_base_ascii::DELETE)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: vchar.
   *
   * vchar = Visible ASCII character codes 33->126.
   * vchar = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_vchar($ordinal) {
    if ($ordinal > c_base_ascii::SPACE && $ordinal < c_base_ascii::DELETE) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: tchar.
   *
   * tchar = Visible ASCII character codes 33->126, excluding: DQUOTE and "(),/:;<=>?@[\]{}".
   * tchar = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-3.2.6
   */
  protected function pr_rfc_char_is_tchar($ordinal) {
    if ($ordinal > c_base_ascii::SPACE && $ordinal < c_base_ascii::DELETE) {
      if ($ordinal == c_base_ascii::QUOTE_DOUBLE || $ordinal == c_base_ascii::PARENTHESIS_OPEN || $ordinal == c_base_ascii::PARENTHESIS_CLOSE ) {
        return FALSE;
      }

      if ($ordinal == c_base_ascii::COMMA || $ordinal == c_base_ascii::SLASH_FORWARD) {
        return FALSE;
      }

      if (($ordinal > c_base_ascii::NINE && $ordinal < c_base_ascii::UPPER_A) || ($ordinal > c_base_ascii::UPPER_Z && $ordinal < c_base_ascii::CARET)) {
        return FALSE;
      }

      if ($ordinal == c_base_ascii::BRACE_OPEN || $ordinal == c_base_ascii::BRACE_CLOSE) {
        return FALSE;
      }

      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: tchar68.
   *
   * tchar = Visible ASCII character codes: 43, 45->57, 65->90, 95, 97->122, 126.
   * tchar = UTF_8-2, UTF_8-3, UTF_8-4.
   * tchar = ALPHA, DIGIT, '-', '.', '_', '~', '+', '/'.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc7235#appendix-C
   */
  protected function pr_rfc_char_is_tchar68($ordinal) {
    if ($ordinal > c_base_ascii::COMMA && $ordinal < c_base_ascii::COLON) {
      return TRUE;
    }

    if (($ordinal > c_base_ascii::AT && $ordinal < c_base_ascii::BRACKET_OPEN) || ($ordinal > c_base_ascii::GRAVE && $ordinal < c_base_ascii::BRACE_OPEN)) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::UNDERSCORE || $ordinal == c_base_ascii::TILDE) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      // UTF_8-1/ASCII characters have already been checked, so return FALSE without doing additional pointless processing.
      return FALSE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: CRLF.
   *
   * CRLF = carraige return or line feed (new line), codes 10, 13.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234#appendix-B
   */
  protected function pr_rfc_char_is_crlf($ordinal) {
    return $ordinal == c_base_ascii::NEW_LINE || $ordinal == c_base_ascii::CARRIAGE_RETURN;
  }

  /**
   * Check to see if character is: WSP.
   *
   * WSP = space or horizontal tab, codes 9, 32.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   * @see: https://tools.ietf.org/html/rfc5234#appendix-B
   */
  protected function pr_rfc_char_is_wsp($ordinal) {
    return $ordinal == c_base_ascii::TAB_HORIZONTAL || $ordinal == c_base_ascii::SPACE;
  }

  /**
   * Check to see if character is: SP.
   *
   * SP = space, codes 32.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_sp($ordinal) {
    return $ordinal == c_base_ascii::SPACE;
  }

  /**
   * Check to see if character is: special.
   *
   * special = special/reserved visible characters, codes 34, 40, 41, 44, 46, 58, 59, 60, 62, 64, 91, 92, 93
   * - which are the characters: '(', ')', '<', '>', '[', ']', ':', ';', '@', '\', ',', '.', '"'.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   */
  protected function pr_rfc_char_is_special($ordinal) {
    switch ($ordinal) {
      case c_base_ascii::PARENTHESIS_OPEN:
      case c_base_ascii::PARENTHESIS_CLOSE:
      case c_base_ascii::LESS_THAN:
      case c_base_ascii::GREATER_THAN:
      case c_base_ascii::BRACKET_OPEN:
      case c_base_ascii::BRACKET_CLOSE:
      case c_base_ascii::COLON:
      case c_base_ascii::COLON_SEMI:
      case c_base_ascii::AT:
      case c_base_ascii::SLASH_BACKWARD:
      case c_base_ascii::COMMA:
      case c_base_ascii::PERIOD:
      case c_base_ascii::QUOTE_DOUBLE:
        return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: atext.
   *
   * atext = Visible ASCII characters, discluding special.
   * atext = UTF_8-2, UTF_8-3, UTF_8-4.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc5234
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_atext($ordinal) {
    return !$this->pr_rfc_char_is_special($ordinal) && $this->pr_rfc_char_is_vchar($ordinal);
  }

  /**
   * Check to see if character is: FWS or LWSP.
   *
   * FWS = space, tab, or CRLF, codes 9, 10, 13, 32.
   * LWSP = identical to FWS (defined in rfc 4234).
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   * @see: https://tools.ietf.org/html/rfc5234
   */
  protected function pr_rfc_char_is_fws($ordinal) {
    return $this->pr_rfc_char_is_crlf($ordinal) || $this->pr_rfc_char_is_wsp($ordinal);
  }

  /**
   * Check to see if character is: ALPHA.
   *
   * ALPHA = Visible ASCII character codes 65->90, 97->122.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_alpha($ordinal) {
    if (($ordinal > c_base_ascii::AT && $ordinal < c_base_ascii::BRACKET_OPEN) || ($ordinal > c_base_ascii::GRAVE && $ordinal < c_base_ascii::BRACE_OPEN)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: BIT.
   *
   * BIT = ASCII character codes 48, 49.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_bit($ordinal) {
    if ($ordinal == c_base_ascii::ZERO || $ordinal == c_base_ascii::ONE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: CHAR.
   *
   * CHAR = ASCII character codes 1->127.
   * CHAR = UTF_8-2, UTF_8-3, or UTF_8-4.
   *
   * Another name for this is UTF_8-OCTETS, except that based on the standard UTF_8-OCTETS refers to 1 or more UTF_8-chars.
   * Given that this is a test against a single character, UTF_8-char and UTF_8-octets are synonymous.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: $this->pr_rfc_char_is_octet()
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   * @see: https://tools.ietf.org/html/rfc3629
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_char($ordinal) {
    // null is not allowed, otherwise this is identical to UTF_8-octet.
    if ($ordinal == c_base_ascii::NULL) {
      return FALSE;
    }

    return $this->pr_rfc_char_is_octet($ordinal);
  }

  /**
   * Check to see if character is: UTF_8-CHAR or UTF_8-OCTET.
   *
   * UTF_8-CHAR = ASCII character codes 0->127.
   * UTF_8-CHAR = UTF_8-1, UTF_8-2, UTF_8-3, or UTF_8-4.
   *
   * Another name for this is UTF_8-OCTETS, except that based on the standard UTF_8-OCTETS refers to 1 or more UTF_8-chars.
   * Given that this is a test against a single character, UTF_8-char and UTF_8-octets are synonymous.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   * @see: https://tools.ietf.org/html/rfc3629
   * @see: https://tools.ietf.org/html/rfc6532
   */
  protected function pr_rfc_char_is_octet($ordinal) {
    if ($this->pr_rfc_char_is_utf8_1($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_2($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_3($ordinal)) {
      return TRUE;
    }

    if ($this->pr_rfc_char_is_utf8_4($ordinal)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: CR.
   *
   * CR = Carriage Return, codes 13.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_cr($ordinal) {
    if ($ordinal == c_base_ascii::CARRIAGE_RETURN) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: LF.
   *
   * LF = line feed (new line), codes 10.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_lf($ordinal) {
    return $ordinal == c_base_ascii::NEW_LINE;
  }

  /**
   * Check to see if character is: CTL.
   *
   * CTL = ASCII control character codes 1->31, 127.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_ctl($ordinal) {
    if (($ordinal > c_base_ascii::NULL && $ordinal < c_base_ascii::SPACE) || $ordinal == c_base_ascii::DELETE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: DIGIT.
   *
   * DIGIT = ASCII character codes 48->57.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_digit($ordinal) {
    if ($ordinal > c_base_ascii::SLASH_FORWARD && $ordinal < c_base_ascii::COLON) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: HEXDIG.
   *
   * HEXDIG = ASCII character codes 48->57, 65-70, 97->103.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   * @param bool $lower_case
   *   (optional) When TRUE, lower case a-f will also be supported (ascii codes 97-103).
   *   Using lower case might be a violation of the standard, but this is unclear to me.
   *   Most things are forced to lower case for simplicity during tests anyway so this might be a necessary functionality.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_hexdigit($ordinal, $lower_case = FALSE) {
    if ($ordinal > c_base_ascii::SLASH_FORWARD && $ordinal < c_base_ascii::COLON) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::GRAVE && $ordinal < c_base_ascii::LOWER_G) {
      return TRUE;
    }

    if ($lower_case) {
      return FALSE;
    }

    if ($ordinal > c_base_ascii::AT && $ordinal < c_base_ascii::UPPER_G) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: DQUOTE.
   *
   * DQUOTE = ASCII character codes 34.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc4234
   */
  protected function pr_rfc_char_is_dquote($ordinal) {
    if ($ordinal == c_base_ascii::QUOTE_DOUBLE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: unreserved.
   *
   * unreserved = ASCII character codes 45, 46, 48->57, 65->90, 95, 97->122, 126.
   *            = ALPHA, DIGIT, '-', '.', '_', '~'.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * https://tools.ietf.org/html/rfc3986#appendix-A
   */
  protected function pr_rfc_char_is_unreserved($ordinal) {
    if (self::pr_rfc_char_is_alpha($ordinal) || self::pr_rfc_char_is_digit($ordinal) ) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::MINUS || $ordinal == c_base_ascii::PERIOD || $ordinal == c_base_ascii::UNDERSCORE || $ordinal == c_base_ascii::TILDE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: reserved.
   *
   * reserved = ASCII character codes 33, 35, 36, 38->44, 47, 58, 59, 61, 63, 64, 91, 93.
   *          = ':',  '/',  '?',  '#',  '[',  ']',  '@', '!',  '$',  '&',  ''',  '(',  ')',  '*',  '+',  ',',  ';',  '='.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc3986#appendix-A
   */
  protected function pr_rfc_char_is_reserved($ordinal) {
    if ($ordinal == c_base_ascii::EXCLAMATION || $ordinal == c_base_ascii::HASH || $ordinal == c_base_ascii::DOLLAR) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::PERCENT && $ordinal < c_base_ascii::MINUS) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::SLASH_FORWARD || $ordinal == c_base_ascii::COLON || $ordinal == c_base_ascii::COLON_SEMI || $ordinal == c_base_ascii::EQUAL) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::QUESTION_MARK || $ordinal == c_base_ascii::AT || $ordinal == c_base_ascii::BRACKET_OPEN || $ordinal == c_base_ascii::BRACKET_CLOSE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: gen-delims.
   *
   * gen-delims = ASCII character codes 35, 47, 58, 63, 64, 91, 93.
   *            = ':', '/', '?', '#', '[', ']', '@'.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc3986#appendix-A
   */
  protected function pr_rfc_char_is_gen_delims($ordinal) {
    if ($ordinal == c_base_ascii::COLON || $ordinal == c_base_ascii::SLASH_FORWARD || $ordinal == c_base_ascii::QUESTION_MARK || $ordinal == c_base_ascii::HASH) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::BRACKET_OPEN || $ordinal == c_base_ascii::BRACKET_CLOSE || $ordinal == c_base_ascii::AT) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: sub-delims.
   *
   * sub-delims = ASCII character codes 33, 36, 38->44, 59, 61.
   *            = '!', '$', '&', ''', '(', ')', '*', '+', ',', ';', '='.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc3986#appendix-A
   */
  protected function pr_rfc_char_is_sub_delims($ordinal) {
    if ($ordinal == c_base_ascii::EXCLAMATION || $ordinal == c_base_ascii::DOLLAR) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::PERCENT && $ordinal < c_base_ascii::MINUS) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::COLON_SEMI || $ordinal == c_base_ascii::EQUAL) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: pchar.
   *
   * sub-delims = ASCII character codes 33, 36->46, 48->59, 61, 64->90, 95, 97->122, 126.
   *            = ALPHA, DIGIT, '-', '.', '_', '~', '!', '$', '&', ''', '(', ')', '*', '+', ',', ';', '='. ':', '@', '%'.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc3986#appendix-A
   */
  protected function pr_rfc_char_is_pchar($ordinal) {
    if ($ordinal == c_base_ascii::EXCLAMATION) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::HASH && $ordinal < c_base_ascii::SLASH_FORWARD) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::SLASH_FORWARD && $ordinal < c_base_ascii::LESS_THAN) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::EQUAL) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::QUESTION_MARK && $ordinal < c_base_ascii::BRACKET_OPEN) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::UNDERSCORE) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::GRAVE && $ordinal < c_base_ascii::BRACE_OPEN) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::TILDE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: query (or fragment).
   *
   * query = ASCII character codes 33, 36->59, 61, 63->90, 95, 97->122, 126.
   *       = ALPHA, DIGIT, '-', '.', '_', '~', '!', '$', '&', ''', '(', ')', '*', '+', ',', ';', '='. ':', '@', '%', '?', '/'.
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE on match, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc3986#appendix-A
   */
  protected function pr_rfc_char_is_query($ordinal) {
    if ($ordinal == c_base_ascii::EXCLAMATION) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::HASH && $ordinal < c_base_ascii::LESS_THAN) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::EQUAL) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::GREATER_THAN && $ordinal < c_base_ascii::BRACKET_OPEN) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::UNDERSCORE) {
      return TRUE;
    }

    if ($ordinal > c_base_ascii::GRAVE && $ordinal < c_base_ascii::BRACE_OPEN) {
      return TRUE;
    }

    if ($ordinal == c_base_ascii::TILDE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check to see if character is: UTF_8-1 (ASCII).
   *
   * The standard claims the following ranges:
   * - [0x00 -> 0x7f]
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE when UTF_8-1 or ASCII, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc3629
   */
  protected function pr_rfc_char_is_utf8_1($ordinal) {
    return ($ordinal & c_base_utf8::MASK_1) == 0;
  }

  /**
   * Check to see if character is: UTF_8-2 (ASCII).
   *
   * The standard claims the following ranges:
   * - [0xc2 -> 0xdf] [0x80 -> 0xbf]
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE when UTF_8-1 or ASCII, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc3629
   */
  protected function pr_rfc_char_is_utf8_2($ordinal) {
    return ($ordinal & c_base_utf8::MASK_2) == 0 && ($ordinal & c_base_utf8::MARK_2) == c_base_utf8::MARK_2;
  }

  /**
   * Check to see if character is: UTF_8-3 (ASCII).
   *
   * The standard claims the following ranges:
   * - [0xe0] [0xa0 -> 0xbf] [0x80 -> 0xbf]
   * - [0xe1 -> 0xec] [0x80 -> 0xbf] [0x80 -> 0xbf]
   * - [0xed] [0x80 -> 0x9f] [0x80 -> 0xbf]
   * - [0xee -> 0xef] [0x80 -> 0xbf] [0x80 -> 0xbf]
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE when UTF_8-1 or ASCII, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc3629
   */
  protected function pr_rfc_char_is_utf8_3($ordinal) {
    return ($ordinal & c_base_utf8::MASK_3) == 0 && ($ordinal & c_base_utf8::MARK_3) == c_base_utf8::MARK_3;
  }

  /**
   * Check to see if character is: UTF_8-4 (ASCII).
   *
   * The standard claims the following ranges:
   * - [0xf0] [0x90 -> 0xbf] [0x80 -> 0xbf] [0x80 -> 0xbf]
   * - [0xf1 -> 0xf3] [0x80 -> 0xbf] [0x80 -> 0xbf] [0x80 -> 0xbf]
   * - [0xf4] [0x80 -> 0x8f] [0x80 -> 0xbf] [0x80 -> 0xbf]
   *
   * @param int $ordinal
   *   A code representing a single character to test.
   *
   * @return bool
   *   TRUE when UTF_8-1 or ASCII, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc822
   * @see: https://tools.ietf.org/html/rfc3629
   */
  protected function pr_rfc_char_is_utf8_4($ordinal) {
    return ($ordinal & c_base_utf8::MARK_4) == c_base_utf8::MARK_4;
  }
}
