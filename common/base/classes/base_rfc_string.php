<?php
/**
 * @file
 * Provides a class for managing common rfc string testing cases.
 */

// include required files.
require_once('common/base/classes/base_ascii.php');
require_once('common/base/classes/base_utf8.php');
require_once('common/base/classes/base_rfc_char.php');

/**
 * A class for managing common rfc string testing cases.
 *
 * This checks a a string of characters.
 * The c_base_rf_string_is_* functions that require specific characters to start, such as DQUOTE, assume that the start position is after the initial special character, such as DQUOTE.
 *
 * This currently utilizes some of the rules defined in the following rfcs:
 * - rfc 4234
 * - rfc 5234
 * - rfc 6532
 *
 * Most of the rules reference the following rfcs as a guideline (but many of the rules defined therein are replaced by rules in the later rfcs):
 * - rfc 822
 *
 * WARNING: I am very new to utf8 coding and may be interpreting the rfc documentation incorrectly.
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
 * @require class c_base_rfc_char
 */
abstract class c_base_rfc_string extends c_base_rfc_char {
  const STOP_AT_CLOSING_CHARACTER = -1;

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
   * Converts a string into a ordinals array and a characters array.
   *
   * The ordinals and characters returned by this are utf8-friendly such that the caller need only use a counter to navigate.
   * - This eliminates the need for constant checking for utf8 or non-utf8 while traversing the string.
   *
   * @param string $text
   *   The string to convert into ordinals array and characters array.
   *
   * @return array
   *   An array containing:
   *     ordinals: an array of ordinal values representing the string.
   *     characters: an array of characters representing the string (utf8-safe).
   *     invalid: FALSE on success, TRUE otherwise.
   *
   * @see: c_base_utf8::s_string_to_ordinals()
   * @see: c_base_utf8::s_ordinals_to_string_array()
   */
  protected function pr_rfc_string_prepare($text) {
    $result = array(
      'ordinals' => array(),
      'characters' => array(),
      'invalid' => FALSE,
    );

    $ordinals = c_base_utf8::s_string_to_ordinals($text);
    if ($ordinals instanceof c_base_return_false) {
      unset($ordinals);
      $result['invalid'] = TRUE;
      return $result;
    }

    $result['ordinals'] = $ordinals->get_value_exact();
    unset($ordinals);

    $characters = c_base_utf8::s_ordinals_to_string_array($result['ordinals']);
    if (c_base_return::s_has_error($characters)) {
      unset($characters);
      $result['invalid'] = TRUE;
      return $result;
    }

    $result['characters'] = $characters->get_value_exact();
    unset($characters);

    return $result;
  }

  /**
   * Converts a string into a ordinals array but not a characters array.
   *
   * This should be somewhat faster if characters are not intended to be used.
   *
   * @param string $text
   *   The string to convert into ordinals array and characters array.
   *
   * @return array
   *   An array containing:
   *     ordinals: an array of ordinal values representing the string.
   *     characters: an empty array.
   *     invalid: FALSE on success, TRUE otherwise.
   *
   * @see: c_base_utf8::s_string_to_ordinals()
   * @see: c_base_utf8::s_ordinals_to_string_array()
   */
  protected function pr_rfc_string_prepare_ordinals($text) {
    $result = array(
      'ordinals' => array(),
      'characters' => array(),
      'invalid' => FALSE,
    );

    $ordinals = c_base_utf8::s_string_to_ordinals($text);
    if ($ordinals instanceof c_base_return_false) {
      unset($ordinals);
      $result['invalid'] = TRUE;
      return $result;
    }

    $result['ordinals'] = $ordinals->get_value_exact();
    unset($ordinals);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: quoted-string.
   *
   * This assumes that the start position is immediately after the initial DQUOTE.
   * This will stop at the closing DQUOTE or if there is trailing valid CFWS, then it will stop at the end of the CFWS.
   *
   * A quoted-string has the following syntax:
   * - [CFWS] DQUOTE *([FWS] qtext / "\"DQUOTE) [FWS] DQUOTE [CFWS]
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *   If self::STOP_AT_CLOSING_CHARACTER, then stop at the end of a double quote and do no further processing.
   *
   * @return array
   *   The processed information, with comments separated:
   *   - 'comments': an array containg the comment before and comment after, if found.
   *   - 'text': A string containing the processed quoted string.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_qtext()
   */
  protected function pr_rfc_string_is_quoted_string($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'comments' => array(
        'before' => NULL,
        'after' => NULL,
      ),
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    $stop_at_closing_quote = FALSE;
    if ($stop < 0) {
      if ($stop == self::STOP_AT_CLOSING_CHARACTER) {
        $stop_at_closing_quote = TRUE;
      }

      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $comment_first = FALSE;
    $comment_last = FALSE;
    $quote_closed = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::SLASH_BACKWARD) {
        if ($quote_closed) {
          // only comments and FWS are allowed after $closing_quote is reached.
          $result['invalid'] = TRUE;
          break;
        }

        if (!$comment_first) {
          // anything that is not a comment and not FWS means that comments are no longer allowed at this point until end of quoted string.
          $comment_first = TRUE;
        }

        // check for and handle delimiters.
        $result['current']++;

        if ($ordinals[$result['current']] == c_base_ascii::QUOTE_DOUBLE) {
          $result['text'] .= $characters[$result['current']];
          continue;
        }

        if ($ordinals[$result['current']] == c_base_ascii::SLASH_BACKWARD) {
          $result['text'] .= $characters[$result['current']];
          continue;
        }

        $result['current']--;
      }
      elseif ($code == c_base_ascii::QUOTE_DOUBLE) {
        if ($quote_closed) {
          // double quote may be supplied only once.
          $result['invalid'] = TRUE;
          break;
        }

        if ($stop_at_closing_quote) {
          break;
        }

        $quote_closed = TRUE;
        continue;
      }
      elseif ($code == c_base_ascii::PARENTHESIS_OPEN) {
        if ($comment_first || $comment_last) {
          // there may be only 1 comment at the start and only 1 comment at the end.
          $result['invalid'] = TRUE;
          break;
        }

        $parsed = $this->pr_rfc_string_is_comment($ordinals, $characters, $result['current'], $stop);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }

        if ($quote_closed) {
          $comment_last = TRUE;
          $results['comments']['after'] = $parsed['comment'];
        }
        else {
          $comment_first = TRUE;
          $results['comments']['before'] = $parsed['comment'];
        }
        unset($parsed);
      }
      elseif ($code == c_base_ascii::PARENTHESIS_CLOSE) {
        // an isolated parenthesis is invald.
        $result['invalid'] = TRUE;
        break;
      }
      elseif ($quote_closed) {
        if ($this->pr_rfc_char_is_fws($code)) {
          continue;
        }

        // only comments and FWS are allowed after $closing_quote is reached.
        $result['invalid'] = TRUE;
        break;
      }
      else {
        if (!$this->pr_rfc_char_is_ctext($code) && !$this->pr_rfc_char_is_fws($code)) {
          $result['invalid'] = TRUE;
          break;
        }

        if (!$comment_first) {
          // anything that is not a comment and not FWS means that comments are no longer allowed at this point until end of quoted string.
          $comment_first = TRUE;
        }
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);
    unset($comment_first);
    unset($comment_last);
    unset($quote_closed);
    unset($stop_at_closing_quote);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: entity-tag.
   *
   * This assumes that the start position is immediately after the initial DQUOTE.
   * This will stop at the closing DQUOTE.
   *
   * Because the start position after the initial DQUOTE, the calling function must handle the "W/" characters.
   *
   * An entity-tag has the following syntax:
   * - 1*(W/) DQUOTE (vchar, except double qoutes) DQUOTE
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed entity tag.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   */
  protected function pr_rfc_string_is_entity_tag($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::QUOTE_DOUBLE) {
        break;
      }
      elseif (!$this->pr_rfc_char_is_vchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: comment.
   *
   * This assumes that the start position is immediately after the initial "(".
   *
   * Comments allow for delimited text as well as other comments to be embedded.
   * - This function does not recurse embedded comments.
   * - Embedded comments are still processed, so an open parenthesis must be followed by a closing parenthesis or the string is considered invalid.
   *
   * A comment has the following syntax:
   * - "(" *([FWS] ctext / "\"DQUOTE / comment) [FWS] ")"
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param null|int $start
   *   (optional) Specify a starting point in which to begin processing.
   * @param null|int $stop
   *   (optional) Specify a stopping point in which to end processing.
   *
   * @return array
   *   The processed information, with comments separated:
   *   - 'comment': a string containing the comment or NULL if no comments defined (such as an empty comment).
   *   - 'current': an integer representing the position the counter where processing stopped.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   */
  protected function pr_rfc_string_is_comment($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'comment' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $comment_depth = 0;
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::SLASH_BACKWARD) {
        // check for and handle delimiters.
        $result['current']++;

        if ($ordinals[$result['current']] == c_base_ascii::QUOTE_DOUBLE) {
          $result['comment'] .= $characters[$result['current']];

          continue;
        }

        if ($ordinals[$result['current']] == c_base_ascii::SLASH_BACKWARD) {
          $result['comment'] .= $characters[$result['current']];

          continue;
        }

        $result['current']--;
      }
      elseif ($code == c_base_ascii::PARENTHESIS_OPEN) {
        // look for open-parenthesis to handle comments within a comment.
        $comment_depth++;
      }
      elseif ($code == c_base_ascii::PARENTHESIS_CLOSE) {
        // handle end of comment.
        if ($comment_depth == 0) {
          // the current position will remain on the closing ')'.
          // use -1 to designate that the comment has been properly closed.
          $comment_depth = -1;
          break;
        }
        else {
          $comment_depth--;
        }
      }
      elseif (!$this->pr_rfc_char_is_ctext($code) && !$this->pr_rfc_char_is_fws($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['comment'] .= $characters[$result['current']];
    }
    unset($code);

    if ($comment_depth > -1) {
      $result['invalid'] = TRUE;
    }
    unset($comment_depth);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: digit.
   *
   * A string that is a digit has the following syntax:
   * - 1*(digit)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed entity tag.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_digit()
   */
  protected function pr_rfc_string_is_digit($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_digit($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: token.
   *
   * A string that is a digit has the following syntax:
   * - 1*(tchar)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed entity tag (the entire string, not broken down into individual tokens).
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_tchar()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.2.6
   */
  protected function pr_rfc_string_is_token($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_tchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: token68.
   *
   * A string that is a digit has the following syntax:
   * - 1*(tchar68)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed token (the entire string, not broken down into individual tokens).
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_tchar()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.2.6
   */
  protected function pr_rfc_string_is_token68($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_tchar68($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: token_quoted.
   *
   * This is not a literal standard, but is implied in things such as 'cache-control'.
   *
   * A string that is a digit has the following syntax:
   * - 1*(1*(tchar) / quoted-string)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed entity tag (the entire string, not broken down into individual tokens).
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_tchar()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.2.6
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2.3
   */
  protected function pr_rfc_string_is_token_quoted($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $not_quoted = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::QUOTE_DOUBLE) {
        if ($not_quoted) {
          // if the first, non-whitespace, character is not a quote, then a quote anywhere else is invalid.
          $result['invalid'] = TRUE;
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }

        $parsed = $this->pr_rfc_string_is_quoted_string($ordinals, $characters, $result['current'], self::STOP_AT_CLOSING_CHARACTER);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          break;
        }

        // the closing quote must be the last value, so if the stop point was not reached at the closing quote position, then fail.
        if ($stop != ($result['current'] + 1)) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['text'] = $parsed['text'];
        unset($parsed);

        break;
      }
      elseif (!$this->pr_rfc_char_is_tchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $not_quoted = TRUE;
      $result['text'] .= $characters[$result['current']];
    }
    unset($code);
    unset($not_quoted);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: credentials.
   *
   * A string that has the following syntax:
   * - 1*(tchar) 1*(wsp) 1*(tchar) *(wsp) "=" *(wsp) ( 1*(tchar) / quoted-string ) *( *(wsp) "," *(wsp) 1*(tchar) *(wsp) "=" *(wsp) ( 1*(tchar) / quoted-string ) ).
   *
   * @todo: this entire function is incomplete, finish writing it.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed entity tag.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc7235#appendix-C
   */
  protected function pr_rfc_string_is_credentials($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'scheme' => NULL,
      'parameters' => array(),
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }


    // load the scheme.
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      if ($this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
        // reached end of the scheme.
        $result['current']++;

        $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
        if ($result['invalid']) {
          return $result;
        }

        break;
      }
      elseif (!$this->pr_rfc_char_is_tchar($ordinals[$result['current']])) {
        $result['invalid'] = TRUE;
        return $result;
      }

      $result['scheme'] .= $characters[$result['current']];
    }

    if ($result['current'] >= $stop) {
      $result['invalid'] = TRUE;
      return $result;
    }


    // load the parameters.
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        return $result;
      }

      $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
      if ($result['invalid']) {
        return $result;
      }

      // load the parameter name.
      $parameter_name = NULL;
      for (; $result['current'] < $stop; $result['current']++) {
        if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
          // @fixme: should error be reported? do some debugging with this.
          $result['invalid'] = TRUE;
          return $result;
        }

        elseif (!$this->pr_rfc_char_is_tchar($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          return $result;
        }

        $parameter_name .= $characters[$result['current']];
      }

      $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
      if ($result['invalid']) {
        return $result;
      }

      if ($result['current'] >= $stop) {
        $result['invalid'] = TRUE;
        return $result;
      }

      // a parameter name must be followed by an equal sign and then the parameter value..
      if ($ordinals[$result['current']] != c_base_ascii::EQUAL) {
        $result['invalid'] = TRUE;
        return $result;
      }

      $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
      if ($result['invalid']) {
        return $result;
      }

      if ($result['current'] >= $stop) {
        $result['invalid'] = TRUE;
        return $result;
      }

      // load the parameter value.
      if ($ordinals[$result['current']] == c_base_ascii::QUOTE_DOUBLE) {
        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          return $result;
        }

        $parsed = $this->pr_rfc_string_is_quoted_string($ordinals, $characters, $result['current'], self::STOP_AT_CLOSING_CHARACTER);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          unset($parsed);

          $result['invalid'] = TRUE;
          return $result;
        }

        $result['parameters'][$parameter_name] = $parsed['text'];
        unset($parsed);
      }
      else {
        for (; $result['current'] < $stop; $result['current']++) {
          if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
            // @fixme: should error be reported? do some debugging with this.
            $result['invalid'] = TRUE;
            return $result;
          }

          if (!$this->pr_rfc_char_is_tchar($ordinals[$result['current']])) {
            break;
          }

          $result['parameters'][$parameter_name] .= $characters[$result['current']];
        }
      }

      $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
      if ($result['invalid']) {
        return $result;
      }

      if ($result['current'] >= $stop) {
        break;
      }


      // A comma designates a new entry
      if ($ordinals[$result['current']] == c_base_ascii::COMMA) {
        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          return $result;
        }
      }

      $parameter_name = NULL;
    }

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: range.
   *
   * A string that is a digit has the following syntax:
   * - ???
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed entity tag.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc7233#appendix-D
   */
  protected function pr_rfc_string_is_range($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    // @todo: this looks like a lot of work, so deal with this at some point in the future because this is a low to moderate priority function.
    $result['invalid'] = TRUE;
/*
    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_digit($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);
*/
    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: vchar, wsp.
   *
   * A string that has the following syntax:
   * - 1*(vchar / wsp)
   *
   * This is for generic text that is any basic vchar or whitespace.
   * This will often be used as the default when no particular syntax is specified.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_vchar()
   * @see: base_rfc_char::pr_rfc_char_is_wsp()
   */
  protected function pr_rfc_string_is_basic($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_vchar($code) && !$this->pr_rfc_char_is_wsp($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: text.
   *
   * A string that has the following syntax:
   * - 1*(text)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_text()
   */
  protected function pr_rfc_string_is_text($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_text($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: atext.
   *
   * A string that has the following syntax:
   * - 1*(atext)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_atext()
   */
  protected function pr_rfc_string_is_atext($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_atext($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: dtext.
   *
   * A string that has the following syntax:
   * - 1*(dtext)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_dtext()
   */
  protected function pr_rfc_string_is_dtext($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_dtext($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: qtext.
   *
   * A string that has the following syntax:
   * - 1*(qtext)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_qtext()
   */
  protected function pr_rfc_string_is_qtext($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_qtext($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: negotiation.
   *
   * There is no literal "negotiation" rfc syntax, but there are a number of different syntax that utilize this design.
   * This is being considered an implicit standard.
   * Negotation can be referred to as priority, or ranking, where the string is a list of preferred option based on some rank (specified by q=).
   * The structure contradicts normal syntax logic used by most system in that the different groups are broken up by commas and the settings broken up by semicolons.
   *
   * For example, normal syntax logic would have the string "text/html; q=1.0, text/*; q=0.8" be broken up into the following structure:
   * - text/html
   * - q=1.0, text/*
   * - q=0.8
   *
   * The syntax logic used by this standard should instead have the string "text/html; q=1.0, text/*; q=0.8" be broken up into the following structure:
   * - text/html; q=1.0
   * - text/*; q=0.8
   *
   * Which should be read as the following:
   * - text/html, q=1.0
   * - text/*, q=0.8
   *
   * Another bizarre behavior of this standard is the use of decimals (perhaps they are considering values a percentage?).
   * This behavior is rather wasteful because decimals tend to get processed differently, but is logically irrelevant.
   *
   * For example, the string "text/html; q=1.0, text/*; q=0.8" could simply be rewritten:
   * - "text/html; q=10, text/*; q=8"
   *
   * To avoid encoding issues of floating points, all decimal values will be treated as integers and stores as such by this function.
   * - All decimal values will be multiplied by 1000 and then the remaining valid integers will be truncated.
   *
   * The default behavior would be to assume case-sensitive, but just in case, this will accept uppercase "Q" and save it as a lowercase "q".
   *
   * A string that has the following syntax:
   * - 1*(atext) *(wsp) *(";" *(wsp) q=1*(digit / 1*(digit) "." 1*(digit))) *(*(wsp) "," *(wsp) 1*(atext) *(wsp) *(";" *(wsp) 1*(digit / 1*(digit) "." 1*(digit))))
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'choices': An array of negotiation choices.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   */
  protected function pr_rfc_string_is_negotiation($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'choices' => array(),
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $choice = array(
      'choice' => NULL,
      'weight' => NULL,
    );
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::COLON_SEMI) {
        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }


        // search for the "q" character.
        for (; $result['current'] < $stop; $result['current']++) {
          // allow uppercase "Q" but force it to become lowercase "q".
          if ($ordinals[$result['current']] == c_base_ascii::UPPER_Q) {
            $ordinals[$result['current']] = c_base_ascii::LOWER_Q;
            $characters[$result['current']] = c_base_ascii::LOWER_Q;
          }

          if ($ordinals[$result['current']] == c_base_ascii::LOWER_Q) {
            break;
          }

          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            $result['invalid'] = TRUE;
            break;
          }
        }

        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
        }

        if ($result['invalid']) {
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }


        // search for the "=" character.
        for (; $result['current'] < $stop; $result['current']++) {
          if ($ordinals[$result['current']] == c_base_ascii::EQUAL) {
            break;
          }

          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            $result['invalid'] = TRUE;
            break;
          }
        }

        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
        }

        if ($result['invalid']) {
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }


        // Skip past whitespace until the first digit is found.
        for (; $result['current'] < $stop; $result['current']++) {
          if ($this->pr_rfc_char_is_digit($ordinals[$result['current']])) {
            $result['current']--;
            break;
          }

          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            $result['invalid'] = TRUE;
            break;
          }
        }

        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
        }

        if ($result['invalid']) {
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }


        // Process the weight value, removing decimal numbers and multiplying by 1000.
        $weight = 0;
        $multiple = 1000;
        $period = FALSE;
        $base = 1;
        for (; $result['current'] < $stop; $result['current']++) {
          if ($ordinals[$result['current']] == c_base_ascii::PERIOD) {
            if ($period) {
              $result['invalid'] = TRUE;
              break;
            }

            $multiple /= 10;
            $period = TRUE;
            continue;
          }

          if ($this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            break;
          }

          if (!$this->pr_rfc_char_is_digit($ordinals[$result['current']])) {
            if ($ordinals[$result['current']] == c_base_ascii::COMMA) {
              break;
            }

            $result['invalid'] = TRUE;
            break;
          }

          if ($period) {
            $weight += intval($characters[$result['current']]) * $multiple;
            $multiple /= 10;
          }
          else {
            $weight *= $base;
            $weight += (intval($characters[$result['current']]) * $multiple);
            $base *= 10;
          }
        }
        unset($multiple);
        unset($period);
        unset($base);

        if ($result['invalid']) {
          unset($weight);
          break;
        }


        // the weight has been identified, so store its value and prepare for another run.
        $choice['weight'] = $weight;
        if (!isset($result['choices'][$weight])) {
          $result['choices'][$weight] = array();
        }

        // strip out leading and trailing whitespace.
        $choice['choice'] = preg_replace('/(^\s+)|(\s+$)/us', '', $choice['choice']);

        $result['choices'][$weight][$choice['choice']] = $choice;
        unset($weight);

        $choice = array(
          'choice' => NULL,
          'weight' => NULL,
        );

        if ($result['current'] >= $stop) {
          break;
        }


        // skip past trailing whitespace.
        if ($this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
          for (; $result['current'] < $stop; $result['current']++) {
            if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
              break;
            }
          }
        }

        // if stop is reached at this point, then a valid end of string has been reached.
        if ($result['current'] >= $stop) {
          break;
        }


        // look for comma, which will designate that another pass is allowed, otherwise the string is invalid.
        if ($ordinals[$result['current']] == c_base_ascii::COMMA) {
          continue;
        }

        $result['invalid'] = TRUE;
        break;
      }
      elseif ($code == c_base_ascii::COMMA) {
        // this is an unweighted choice.
        $choice['weight'] = NULL;
        if (!isset($result['choices'][NULL])) {
          $result['choices'][NULL] = array();
        }

        // strip out leading and trailing whitespace.
        $choice['choice'] = preg_replace('/(^\s+)|(\s+$)/us', '', $choice['choice']);

        $result['choices'][NULL][$choice['choice']] = $choice;

        $choice = array(
          'choice' => NULL,
          'weight' => NULL,
        );

        continue;
      }
      elseif (!$this->pr_rfc_char_is_atext($code) && !$this->pr_rfc_char_is_wsp($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $choice['choice'] .= $characters[$result['current']];
    }
    unset($code);

    // If there were no commas or semi-colons, then this is a single, unweighted, choice (which is valid).
    if ($choice['choice'] != NULL) {
      $choice['weight'] = NULL;
      if (!isset($result['choices'][NULL])) {
        $result['choices'][NULL] = array();
      }

      // strip out leading and trailing whitespace.
      $choice['choice'] = preg_replace('/(^\s+)|(\s+$)/us', '', $choice['choice']);

      $result['choices'][NULL][$choice['choice']] = $choice;
    }
    unset($choice);

    if ($result['invalid']) {
      return $result;
    }

    // sort the choices array.
    krsort($result['choices']);

    // The NULL key should be the first key in the weight.
    $this->pr_prepend_array_value(NULL, $result['choices']);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: media-type.
   *
   * The standard does not explicitly state that there is whitespace, but it instead implies it.
   * Therefore optional whitespace are added.
   *
   * An media-type has the following syntax:
   * - 1*(tchar) "/" 1*(tchar) *(*(wsp) ";" *(wsp) 1*(1*(tchar) *(wsp) "=" *(wsp) 1*(tchar) / (quoted-string)))
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'media': A string containing the processed media type (without parameters).
   *   - 'parameters': An array of strings representing the parameters.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  protected function pr_rfc_string_is_media_type($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'media' => NULL,
      'parameters' => array(),
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $found_slash = FALSE;
    $process_parameters = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::SLASH_FORWARD) {
        if (!$process_parameters) {
          if ($found_slash) {
            $result['invalid'] = TRUE;
            break;
          }

          $found_slash = TRUE;
          $result['media'] .= $characters[$result['current']];
          continue;
        }
      }
      elseif ($code == c_base_ascii::COLON_SEMI || $found_slash && $this->pr_rfc_char_is_wsp($code)) {
        if ($found_slash && $this->pr_rfc_char_is_wsp($code)) {
          // in this case, the semi-colon has yet to be found, so seek until a semi-colon is found.
          // any and all non-semi-colon and non-whitespace means that the string is invalid.
          for (; $result['current'] < $stop; $result['current']++) {
            if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
              if ($ordinals[$result['current']] == c_base_ascii::COLON_SEMI) {
                break;
              }

              $result['invalid'] = TRUE;
              break;
            }
          }

          if ($result['invalid']) {
            break;
          }
        }

        // begin processing the set of media type parameters, first skipping past the semi-colon.
        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }

        // Check for: *(token "=" (token / quoted-string)).
        $processed_token = NULL;
        $found_equal = FALSE;
        $parameter_name = NULL;
        $parameter_value = NULL;
        for (; $result['current'] < $stop; $result['current']++) {
          $subcode = $ordinals[$result['current']];

          if ($this->pr_rfc_char_is_wsp($subcode)) {
            if (is_null($processed_token)) {
              // skip past leading whitespace.
              continue;
            }

            $processed_token = TRUE;
            continue;
          }
          elseif ($subcode == c_base_ascii::EQUAL) {
            if ($found_equal || $process_whitespace) {
              // it cannot start with an equal sign, so if $process_whitespace is TRUE, then this is an invalid equal sign.
              $result['invalid'] = TRUE;
              break;
            }

            // skip past all whitespace.
            $result['current']++;
            if ($result['current'] >= $stop) {
              $result['invalid'] = TRUE;
              break;
            }

            for (; $result['current'] < $stop; $result['current']++) {
              if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
                break;
              }
            }

            if ($result['current'] >= $stop) {
              $result['invalid'] = TRUE;
              break;
            }

            // check for quoted_string, which must begin with a double quote.
            if ($subcode == c_base_ascii::QUOTE_DOUBLE) {
              // skip past the initial double quote.
              $result['current']++;
              if ($result['current'] >= $stop) {
                $result['invalid'] = TRUE;
                break;
              }

              $parsed = $this->pr_rfc_string_is_quoted_string($ordinals, $characters, $result['current'], self::STOP_AT_CLOSING_CHARACTER);
              $result['current'] = $parsed['current'];

              if ($parsed['invalid']) {
                $result['invalid'] = TRUE;
                break;
              }

              $parameter_value = $parsed['text'];
              unset($parsed);

              if ($result['current'] >= $stop) {
                // must break now so that the 'current' counter remains at the stop point.
                break;
              }

              // check for semi-colon, if one is found then continue, otherwise end if at stop point.
              $result['current']++;
              if ($result['current'] >= $stop) {
                $result['parameters'][$parameter_name] = $parameter_value;
                break;
              }

              // skip past any whitespace to see if there is a semi-colon.
              for (; $result['current'] < $stop; $result['current']++) {
                if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
                  break;
                }
              }

              if ($result['current'] >= $stop || $ordinals[$result['current']] != c_base_ascii::COLON_SEMI) {
                $result['invalid'] = TRUE;
                break;
              }

              $result['parameters'][$parameter_name] = $parameter_value;
            }
            else {
              for (; $result['current'] < $stop; $result['current']++) {
                if (!$this->pr_rfc_char_is_tchar($ordinals[$result['current']])) {
                  $result['invalid'] = TRUE;
                  break;
                }

                $parameter_value .= $characters[$result['current']];
              }

              if ($result['invalid']) {
                break;
              }

              $result['parameters'][$parameter_name] = $parameter_value;
            }

            $parameter_name = NULL;
            $parameter_value = NULL;
            $found_equal = FALSE;
            $processed_token = NULL;

            continue;
          }
          elseif (!$this->pr_rfc_char_is_tchar($subcode)) {
            if ($found_equal) {
              if ($subcode == c_base_ascii::COLON_SEMI) {
                // save parameter and value and continue.
                $result['parameters'][$parameter_name] = $parameter_value;

                $parameter_name = NULL;
                $parameter_value = NULL;
                $found_equal = FALSE;
                $processed_token = NULL;

                continue;
              }
            }

            $result['invalid'] = TRUE;
            break;
          }
          elseif ($processed_token) {
            // processed token is set to TRUE after the first space following the token is found.
            // spaces are not allowed inside an unqouted token and is therefore invalid.
            $result['invalid'] = TRUE;
            break;
          }

          if ($found_equal) {
            $parameter_value .= $characters[$subcode];
          }
          else {
            $parameter_name .= $characters[$subcode];
          }

          $processed_token = FALSE;
        }
        unset($subcode);
        unset($process_whitespace);

        if ($found_equal) {
          if (!is_null($parameter_name)) {
            $result['parameters'][$parameter_name] = $parameter_value;
          }
        }
        else {
          // a parameter name without an equal sign to designate a parameter value is invalid.
          $result['invalid'] = TRUE;
        }
        unset($processed_name);
        unset($processed_value);
        unset($found_equal);

        // all parameters have been processed, should be on or after $stop point or there is an invalid character.
        if (!$result['invalid'] && $result['current'] < $stop) {
          $result['invalid'] = TRUE;
        }

        break;
      }
      elseif (!$this->pr_rfc_char_is_tchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['media'] .= $characters[$result['current']];
      $process_parameters = TRUE;
    }
    unset($code);
    unset($found_slash);
    unset($process_parameters);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: valued_token.
   *
   * There is no explicit "valued_token" standard, but it is implied and used in many ways.
   * One such example is with the HTTP header "content-disposition".
   *
   * The standard does not seem to specify *(wsp) in all cases.
   * Given that this is not a literal standard, strictness will be loosened a little to allow optional whitespace between the ";" and the "=".
   *
   * The standard does not explicitly state that there is whitespace, but in certain cases it implies whitespace usage.
   * Therefore optional whitespace are added.
   *
   * A valued_token has the following syntax:
   * - *(wsp) 1*(tchar) *(*(wsp) ";" *(wsp) 1*(1*(tchar) *(wsp) "=" *(wsp) 1*(tchar) / (quoted-string))) *(wsp)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'tokens': An array of strings representing the parameters, with the keys being the valued token name and the values being the valued token value.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: self::pr_rfc_string_is_valued_token_comma()
   * @see: base_rfc_char::pr_rfc_string_is_media_type()
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  protected function pr_rfc_string_is_valued_token($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'tokens' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $token_name = NULL;
    $token_value = NULL;
    $processed_name = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($this->pr_rfc_char_is_wsp($code)) {
        if (is_null($token_name)) {
          continue;
        }

        // if the whitespace is not leading whitespace, then the end of the token has been reached.
        // A stop point, an equal sign, or a semi-colon must be reached for the token and value pair to be valid.
        for (; $result['current'] < $stop; $result['current']++) {
          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            break;
          }
        }

        if ($result['current'] >= $stop) {
          // must break now so that the 'current' counter remains at the stop point.
          break;
        }

        if ($ordinals[$result['current']] == c_base_ascii::COLON_SEMI) {
          $result['tokens'][$token_name] = $token_value;

          $token_name = NULL;
          $token_value = NULL;
          $processed_name = FALSE;

          // skip past all whitespace following the semi-colon.
          for (; $result['current'] < $stop; $result['current']++) {
            if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
              break;
            }
          }

          if ($result['current'] >= $stop) {
            // must break now so that the 'current' counter remains at the stop point.
            break;
          }

          continue;
        }
        elseif ($ordinals[$result['current']] == c_base_ascii::EQUAL && !$processed_name) {
          $processed_name = TRUE;

          // skip past all whitespace following the equal.
          for (; $result['current'] < $stop; $result['current']++) {
            if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
              break;
            }
          }

          if ($result['current'] >= $stop) {
            // must break now so that the 'current' counter remains at the stop point.
            break;
          }

          continue;
        }

        $result['invalid'] = TRUE;
        break;
      }
      elseif ($code == c_base_ascii::COLON_SEMI) {
        $result['tokens'][$token_name] = $token_value;
        $token_name = NULL;
        $token_value = NULL;
        $processed_name = FALSE;

        // skip past all whitespace following the semi-colon.
        for (; $result['current'] < $stop; $result['current']++) {
          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            break;
          }
        }

        if ($result['current'] >= $stop) {
          // must break now so that the 'current' counter remains at the stop point.
          break;
        }

        continue;
      }
      elseif ($code == c_base_ascii::QUOTE_DOUBLE) {
        if (!$processed_name) {
          // the token name is not allowed to be a quoted string.
          $result['invalid'] = TRUE;
          break;
        }

        // skip past the initial double quote.
        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }

        $parsed = $this->pr_rfc_string_is_quoted_string($ordinals, $characters, $result['current'], self::STOP_AT_CLOSING_CHARACTER);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          break;
        }

        $token_value = $parsed['text'];
        unset($parsed);

        if ($result['current'] >= $stop) {
          // must break now so that the 'current' counter remains at the stop point.
          break;
        }

        $result['tokens'][$token_name] = $token_value;
        $token_name = NULL;
        $token_value = NULL;
        $processed_name = FALSE;

        continue;
      }
      elseif (!$this->pr_rfc_char_is_tchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      if ($processed_name) {
        $token_value .= $characters[$result['current']];
      }
      else {
        $token_name .= $characters[$result['current']];
      }
    }
    unset($code);
    unset($processed_name);

    if (!is_null($token_name) && $result['current'] >= $stop) {
      // the stop point was reached, make sure to the token_name and token_value that were last being processed.
      $result['tokens'][$token_name] = $token_value;
    }
    unset($token_name);
    unset($token_value);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: valued_token_comma.
   *
   * This is similar to valued_token, expect that instead of using ';', a ',' is used as a separator.
   *
   * There is no explicit "valued_token" standard, but it is implied and used in many ways.
   * One such example is with the HTTP header "cache-control".
   *
   * The standard does not seem to specify *(wsp) in all cases.
   * Given that this is not a literal standard, strictness will be loosened a little to allow optional whitespace between the ";" and the "=".
   *
   * The standard does not explicitly state that there is whitespace, but in certain cases it implies whitespace usage.
   * Therefore optional whitespace are added.
   *
   * A valued_token has the following syntax:
   * - *(wsp) 1*(tchar) *(*(wsp) "=" *(wsp) 1*(tchar) / (quoted-string)) *(*(wsp) "," *(wsp) 1*(1*(tchar) *(*(wsp) "=" *(wsp) 1*(tchar) / (quoted-string)))) *(wsp)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'tokens': An array of strings representing the parameters, with the keys being the valued token name and the values being the valued token value.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: self::pr_rfc_string_is_valued_token()
   * @see: base_rfc_char::pr_rfc_string_is_media_type()
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  protected function pr_rfc_string_is_valued_token_comma($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'tokens' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $token_name = NULL;
    $token_value = NULL;
    $processed_name = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($this->pr_rfc_char_is_wsp($code)) {
        if (is_null($token_name)) {
          continue;
        }

        // if the whitespace is not leading whitespace, then the end of the token has been reached.
        // A stop point, an equal sign, or a semi-colon must be reached for the token and value pair to be valid.
        for (; $result['current'] < $stop; $result['current']++) {
          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            break;
          }
        }

        if ($result['current'] >= $stop) {
          // must break now so that the 'current' counter remains at the stop point.
          break;
        }

        if ($ordinals[$result['current']] == c_base_ascii::COLON_SEMI) {
          $result['tokens'][$token_name] = $token_value;

          $token_name = NULL;
          $token_value = NULL;
          $processed_name = FALSE;

          // skip past all whitespace following the semi-colon.
          for (; $result['current'] < $stop; $result['current']++) {
            if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
              break;
            }
          }

          if ($result['current'] >= $stop) {
            // must break now so that the 'current' counter remains at the stop point.
            break;
          }

          continue;
        }
        elseif ($ordinals[$result['current']] == c_base_ascii::EQUAL && !$processed_name) {
          $processed_name = TRUE;

          // skip past all whitespace following the equal.
          for (; $result['current'] < $stop; $result['current']++) {
            if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
              break;
            }
          }

          if ($result['current'] >= $stop) {
            // must break now so that the 'current' counter remains at the stop point.
            break;
          }

          continue;
        }

        $result['invalid'] = TRUE;
        break;
      }
      elseif ($code == c_base_ascii::COMMA) {
        $result['tokens'][$token_name] = $token_value;
        $token_name = NULL;
        $token_value = NULL;
        $processed_name = FALSE;

        // skip past all whitespace following the semi-colon.
        for (; $result['current'] < $stop; $result['current']++) {
          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            break;
          }
        }

        if ($result['current'] >= $stop) {
          // must break now so that the 'current' counter remains at the stop point.
          break;
        }

        continue;
      }
      elseif ($code == c_base_ascii::QUOTE_DOUBLE) {
        if (!$processed_name) {
          // the token name is not allowed to be a quoted string.
          $result['invalid'] = TRUE;
          break;
        }

        // skip past the initial double quote.
        $result['current']++;
        if ($result['current'] >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }

        $parsed = $this->pr_rfc_string_is_quoted_string($ordinals, $characters, $result['current'], self::STOP_AT_CLOSING_CHARACTER);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          break;
        }

        $token_value = $parsed['text'];
        unset($parsed);

        if ($result['current'] >= $stop) {
          // must break now so that the 'current' counter remains at the stop point.
          break;
        }

        $result['tokens'][$token_name] = $token_value;
        $token_name = NULL;
        $token_value = NULL;
        $processed_name = FALSE;

        continue;
      }
      elseif (!$this->pr_rfc_char_is_tchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      if ($processed_name) {
        $token_value .= $characters[$result['current']];
      }
      else {
        $token_name .= $characters[$result['current']];
      }
    }
    unset($code);
    unset($processed_name);

    if (!is_null($token_name) && $result['current'] >= $stop) {
      // the stop point was reached, make sure to the token_name and token_value that were last being processed.
      $result['tokens'][$token_name] = $token_value;
    }
    unset($token_name);
    unset($token_value);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: commad_token.
   *
   * This is a bit bizarre in that the standard allows for leading commas with nothing defined.
   * - This function will accept that syntax, but those commas will be ignored.
   * - Even more oddly, leading white space is not supported, but this too is bizarre and inconsistent.
   * - A simpler syntax will therefore be used.
   *
   * A valued_token has the following syntax:
   * - 1*(*(wsp) "," *(wsp) token)
   *
   * Original valued_token standard syntax:
   * - *("," *(wsp)) token *(*(wsp) "," *(wsp) token)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'tokens': An array of strings representing the tokens.
   *               The array keys will represent the order in which they were processed.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc7230#appendix-B
   */
  protected function pr_rfc_string_is_commad_token($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'tokens' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    $token_value = NULL;
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($this->pr_rfc_char_is_wsp($code)) {
        if (is_null($token_value)) {
          continue;
        }

        // End of token reached, now seek whitespace to end of line or a comma.
        $result['current']++;
        if ($result['current'] >= $stop) {
          break;
        }

        for (; $result['current'] < $stop; $result['current']++) {
          if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
            break;
          }
        }

        if ($ordinals[$result['current']] == c_base_ascii::COMMA) {
          if (is_null($token_value)) {
            // empty values separated by commas are to be ignored.
            continue;
          }

          $result['tokens'][] = $token_value;
          $tokan_value = NULL;
          continue;
        }

        $result['invalid'] = TRUE;
        break;
      }
      elseif ($code == c_base_ascii::COMMA) {
        if (is_null($token_value)) {
          // empty values separated by commas are to be ignored.
          continue;
        }

        $result['tokens'][] = $token_value;
        $tokan_value = NULL;
        continue;
      }
      elseif (!$this->pr_rfc_char_is_tchar($code)) {
        $result['invalid'] = TRUE;
        break;
      }

      $token_value .= $characters[$result['current']];
    }
    unset($code);

    if (!is_null($token_value) && $result['current'] >= $stop) {
      // the stop point was reached, make sure the token_value was last being processed.
      $result['tokens'][] = $token_value;
    }
    unset($token_value);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: path.
   *
   * A path has the following syntax:
   * - *(ALPHA / DIGIT / "-" / "." / "_" / "~" / "%" HEXDIG HEXDIG / "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "=" / ":" / "@" / "/" / "?")
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the validated path.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc3986#section-3.4
   */
  protected function pr_rfc_string_is_path($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::PERCENT) {
        // valid only if two hex digits immediately follow.
        $result['current']++;
        if ($result['current'] >= $stop) {
          // this is invalid because it is cut off before 2 hex digits are required and none is found.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];
        if (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          // this is invalid because it is cut off before 2 hex digits are required and only one is found.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];
        if (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          break;
        }
      }
      elseif (self::pr_rfc_char_is_pchar($code)) {
        // do nothing, valid.
      }
      elseif ($code == c_base_ascii::SLASH_FORWARD) {
        // do nothing, valid.
      }
      else {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: query.
   *
   * This is also used by the rfc syntax: fragment.
   *
   * A query has the following syntax:
   * - *(ALPHA / DIGIT / "-" / "." / "_" / "~" / "%" HEXDIG HEXDIG / "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "=" / ":" / "@" / "/" / "?")
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the validated query.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc3986#section-3.4
   */
  protected function pr_rfc_string_is_query($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::PERCENT) {
        // valid only if two hex digits immediately follow.
        $result['current']++;
        if ($result['current'] >= $stop) {
          // this is invalid because it is cut off before 2 hex digits are required and none is found.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];
        if (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          // this is invalid because it is cut off before 2 hex digits are required and only one is found.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];
        if (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          break;
        }
      }
      elseif (self::pr_rfc_char_is_pchar($code)) {
        // do nothing, valid.
      }
      elseif ($code == c_base_ascii::SLASH_FORWARD || $code == c_base_ascii::QUESTION_MARK) {
        // do nothing, valid.
      }
      else {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: ip-literal.
   *
   * This assumes that the start position is immediately after the initial "[".
   * This will stop at the closing "]".
   *
   * An ip-literal has the following syntax:
   * - "[" (IPv6address / "v" 1*HEXDIG "." 1*(unreserved / sub-delims / ":")) "]"
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed or the closing character is found.
   *
   * @return array
   *   The processed information:
   *   - 'address': A string containing the processed ip address.
   *                When is_future is TRUE, this is an array containing:
   *                - 'version': The ipfuture version.
   *                - 'ip': The ip address.
   *   - 'is_future': A boolean that when TRUE represents an ipvfuture address and when FALSE represents an ipv6 address.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  protected function pr_rfc_string_is_ip_literal($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'address' => NULL,
      'is_future' => FALSE,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    // this first character must be either a "v" or a hex digit.
    if ($ordinals[$result['current']] == c_base_ascii::LOWER_V || $ordinals[$result['current']] == c_base_ascii::UPPER_V) {
      $result['is_future'] = TRUE;
    }
    elseif (!self::pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
      $result['invalid'] = TRUE;
      return $result;
    }

    if ($result['is_future']) {
      $result['address'] = array(
        'version' => NULL,
        'ip' => NULL,
      );

      $result['current']++;
      if ($result['current'] >= $stop) {
        $result['invalid'] = TRUE;
        return $result;
      }

      // store all hexdigits until a non-hexdigit is found as the version number.
      for (; $result['current'] < $stop; $result['current']++) {
        $code = $ordinals[$result['current']];

        if (!self::pr_rfc_char_is_hexdigit($code)) {
          break;
        }

        $result['address']['version'] .= $characters[$result['current']];
      }
      unset($code);

      if ($result['current'] >= $stop) {
        $result['invalid'] = TRUE;
        return $result;
      }

      if ($ordinals[$result['current']] != c_base_ascii::PERIOD) {
        $result['invalid'] = TRUE;
        return $result;
      }

      $result['current']++;
      if ($result['current'] >= $stop) {
        $result['invalid'] = TRUE;
        return $result;
      }

      // record all valid values as the ip address until the stop point or ']' is reached.
      for (; $result['current'] < $stop; $result['current']++) {
        $code = $ordinals[$result['current']];

        if (self::pr_rfc_char_is_unreserved($code)) {
          // do nothing, valid.
        }
        elseif (self::pr_rfc_char_is_sub_delims($code)) {
          // do nothing, valid.
        }
        elseif ($code == c_base_ascii::COLON) {
          // do nothing, valid.
        }
        elseif ($code == c_base_ascii::BRACKET_CLOSE) {
          break;
        }
        else {
          $result['invalid'] = TRUE;
          break;
        }

        $result['address']['ip'] .= $characters[$result['current']];
      }
      unset($code);
    }
    else {
      for (; $result['current'] < $stop; $result['current']++) {
        $code = $ordinals[$result['current']];

        if (self::pr_rfc_char_is_hexdigit($code)) {
          $result['address'] .= $characters[$result['current']];
        }
        elseif ($code == c_base_ascii::COLON) {
          $result['address'] .= $characters[$result['current']];
        }
        elseif ($code == c_base_ascii::BRACKET_CLOSE) {
          break;
        }
        else {
          $result['invalid'] = TRUE;
          break;
        }
      }
      unset($code);

      if (!$result['invalid'] && inet_pton($result['address']) === FALSE) {
        $result['invalid'] = TRUE;
      }
    }

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: alphanumeric or dash.
   *
   * A string that has the following syntax:
   * - 1*((ALPHA) | (DIGIT) | '-')
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_alpha()
   * @see: base_rfc_char::pr_rfc_char_is_digit()
   */
  protected function pr_rfc_string_is_alpha_numeric_dash($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_alpha($code) && !$this->pr_rfc_char_is_digit($code) && $code !== c_base_ascii::MINUS) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Processes a string based on the syntax: numeric.
   *
   * This is not part of any specific rfc.
   *
   * A string that has the following syntax:
   * - *(wsp) [('-' | '+')] 1*(DIGIT) ['.' . 1*(DIGIT)] *(wsp)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_digit()
   */
  protected function pr_rfc_string_is_numeric($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }


    // ignore leading whitespaces
    $result = $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
    if ($result['invalid']) {
      return $result;
    }

    // no numbers found.
    if ($result['current'] >= $stop) {
      $result['invalid'] = TRUE;
      return $result;
    }


    // first look for a leading positive or negative sign.
    if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
      // @fixme: should error be reported? do some debugging with this.
      $result['invalid'] = TRUE;
      return $result;
    }

    $code = $ordinals[$result['current']];
    if ($code === c_base_ascii::MINUS && $code === c_base_ascii::PLUS) {
      $result['text'] .= $code;
      $result['current']++;

      // must have a digit in addition to the leading +/-.
      if ($result['current'] >= $stop) {
        unset($code);

        $result['invalid'] = TRUE;
        return $result;
      }
    }
    unset($code);


    // look for digit, but only allow a single period.
    $found_period = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if ($code === c_base_ascii::PERIOD) {
        if ($found_period) {
          $result['invalid'] = TRUE;
          break;
        }

        $found_period = TRUE;
      }
      elseif (!$this->pr_rfc_char_is_digit($ordinals[$result['current']])) {
        // ignore trailing whitespaces
        if ($this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
          $result = $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
          if ($result['invalid']) {
            unset($found_period);
            return $result;
          }

          if ($result['current'] >= $stop) {
            // this is not an error, because only whitespace was found at the end of the number.
            break;
          }
        }

        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);
    unset($found_period);

    // if there exists only a single period, then this is not a valid number.
    if ($result['text'] == '.') {
      $result['text'] = '';
      $result['invalid'] = TRUE;
    }

    return $result;
  }

  /**
   * Processes a string based on the syntax: hexadecimal numeric.
   *
   * This is not part of any specific rfc.
   *
   * A string that has the following syntax:
   * - *(wsp) [('-' | '+')] 1*(HEXDIGIT) ['.' . 1*(HEXDIGIT)] *(wsp)
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_hexdigit()
   */
  protected function pr_rfc_string_is_hexanumeric($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }


    // ignore leading whitespaces
    $result = $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
    if ($result['invalid']) {
      return $result;
    }
    unset($result);

    // no numbers found.
    if ($result['current'] >= $stop) {
      $result['invalid'] = TRUE;
      return $result;
    }


    // first look for a leading positive or negative sign.
    if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
      // @fixme: should error be reported? do some debugging with this.
      $result['invalid'] = TRUE;
      return $result;
    }

    $code = $ordinals[$result['current']];
    if ($code === c_base_ascii::MINUS && $code === c_base_ascii::PLUS) {
      $result['text'] .= $code;
      $result['current']++;

      // must have a digit in addition to the leading +/-.
      if ($result['current'] >= $stop) {
        unset($code);

        $result['invalid'] = TRUE;
        return $result;
      }
    }
    unset($code);


    // look for digit, but only allow a single period.
    $found_period = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if ($code === c_base_ascii::PERIOD) {
        if ($found_period) {
          $result['invalid'] = TRUE;
          break;
        }

        $found_period = TRUE;
      }
      elseif (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
        // ignore trailing whitespaces
        if ($this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
          $result = $this->p_rfc_string_skip_past_whitespace($ordinals, $characters, $stop, $result);
          if ($result['invalid']) {
            unset($found_period);
            return $result;
          }

          if ($result['current'] >= $stop) {
            // this is not an error, because only whitespace was found at the end of the number.
            break;
          }
        }

        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);
    unset($found_period);

    // if there exists only a single period, then this is not a valid number.
    if ($result['text'] == '.') {
      $result['text'] = '';
      $result['invalid'] = TRUE;
    }

    return $result;
  }

  /**
   * Processes a string based on the rfc syntax: vchar except for semi-colon and comma.
   *
   * A string that has the following syntax:
   * - 1*(vchar, except for ';' and ',')
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'text': A string containing the processed text.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_is_vchar()
   */
  protected function pr_rfc_string_is_directive_value($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if (!$this->pr_rfc_char_is_vchar($code) || ($code === c_base_ascii::COLON_SEMI || $code === c_base_ascii::COMMA)) {
        $result['invalid'] = TRUE;
        break;
      }

      $result['text'] .= $characters[$result['current']];
    }
    unset($code);

    return $result;
  }

  /**
   * Decode and check that the given uri is valid.
   *
   * This does not decode the uri, it separates it into its individual parts.
   *
   * Validation is done according to rfc3986.
   *
   *   foo://example.com:8042/over/there?name=ferret#nose
   *   \_/   \______________/\_________/ \_________/ \__/
   *    |           |            |            |        |
   *  scheme    authority       path        query   fragment
   *    |   _____________________|__
   *   / \ /                        \
   *   foo:example:animal:ferret:nose
   *
   * The standard is misleading in its definition of path.
   * First it says path can be path-abempty, path-absolute, path-noscheme, path-rootless, or path-empty.
   * it then defines those as follows:
   *   path-abempty:  begins with "/" or is empty
   *   path-absolute: begins with "/" but not "//"
   *   path-noscheme: begins with a non-colon segment
   *   path-rootless: begins with a segment
   *   path-empty:    zero characters
   *
   * path-abempty's definition includes a  '//', making path-absolute irrelevant/redundant.
   * path-rootless's definition includes a colon, making path-noscheme irrelevant/redundant.
   * path-empty's definition is inconsistent, why not say 'is empty' as with path-abempty?
   *
   * I am going to assume that path-abempty is meant to be defined as 'begins with "/" but not "//"' or 'is empty'.
   *
   * The standard also provides a regex example that violates their own rules:
   * - Regex: ^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
   * - Their URI: http://www.ics.uci.edu/pub/ietf/uri/#Related
   * - Example URI: example:relative:url/that:is:not/a:urn
   *
   * That example is syntatically valid for 'path-absolute', but has part of it turned into a scheme.
   * While it is currently not popular to use ':' in a url path, it is valid according to the standard.
   * Therefore, their example regex is non-conforming.
   *
   * In fact, as-written, the standard provides no means to distinguish between a scheme and a path.
   * - Example Scheme: my_scheme:
   * - Example Path:   my_path:
   * Both of those are valid for their appropriate syntax.
   *
   * I believe that the way they wrote the standard is very mis-leading.
   * path = path-abempty / path-absolute / path-noscheme / path-rootless / path-empty
   * means that any one of those could be valid, thats an OR.
   * but they are likely interpretting the '/' to be a separation betweem if/then conditionals that are not clearly defined.
   *
   * I am going to assume that what they mean is ':' is supported only in path IF a scheme is supplied.
   * In this case the first colon separates the schema from everything else.
   * Even with this interpretation, there are still problems because the following would still be syntatically valid:
   * - Example URI: http://www.example.com/a:syntatically:valid:path
   * If that example is what is necessary, then how does one make a valid relative uri for paths on that site!?
   * - schemes are not allowed in relative paths, but that path still exists!
   * - The standard supports these paths as absolute but does not support them as relative.
   *
   * For simplicity purposes, this function will violate the literal standard to follow what I am guessing to be the intended standard.
   * If a colon is to appear in the path, it must be a URN and if so then it must have a scheme.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $start
   *   (optional) The position in the arrays to start checking.
   * @param int|null $stop
   *   (optional) The position in the arrays to stop checking.
   *   If NULL, then the entire string is processed.
   *
   * @return array
   *   The processed information:
   *   - 'scheme': The protocol string.
   *   - 'authority': The domain string.
   *   - 'path': The path string.
   *   - 'query': An array of url arguments.
   *   - 'fragment': The id string.
   *   - 'url': A boolean that when TRUE means the string is a url and when FALSE the string is a urn.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: self::p_combine_uri_array()
   * @see: self::pr_rfc_string_is_scheme()
   * @see: self::pr_rfc_string_is_authority()
   * @see: self::pr_rfc_string_is_path()
   * @see: self::pr_rfc_string_is_query()
   * @see: https://tools.ietf.org/html/rfc3986
   */
  protected function pr_rfc_string_is_uri($ordinals, $characters, $start = 0, $stop = NULL) {
    $result = array(
      'scheme' => NULL,
      'authority' => NULL,
      'path' => NULL,
      'query' => NULL,
      'fragment' => NULL,
      'url' => TRUE,
      'current' => $start,
      'invalid' => FALSE,
    );

    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }


    // handle path cases that begin with a forward slash because they are easy to identify.
    if ($ordinals[$result['current']] == c_base_ascii::SLASH_FORWARD) {
      $this->p_rfc_string_is_uri_path($ordinals, $characters, $stop, $result);
      if ($result['invalid'] || $result['current'] >= $stop) {
        return $result;
      }


      // check for query.
      if ($ordinals[$result['current']] == c_base_ascii::QUESTION_MARK) {
        // the first question mark is not recorded so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_query($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }


      // check for fragment.
      if ($ordinals[$result['current']] == c_base_ascii::HASH) {
        // only the first hash is supported in the fragment (and it is not recorded) so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_fragment($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }

      return $result;
    }


    // handle fragment cases first because they are easy to identify.
    if ($ordinals[$result['current']] == c_base_ascii::HASH) {
      for (; $result['current'] < $stop; $result['current']++) {
        if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
          // @fixme: should error be reported? do some debugging with this.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];

        // the syntax for query is identical to fragment.
        if (!$this->pr_rfc_char_is_query($code)) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['fragment'] .= $characters[$result['current']];
      }
      unset($code);

      return $result;
    }


    $not_scheme = FALSE;
    $not_authority = FALSE;
    $not_path = FALSE;
    $processed_string = '';
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      $code = $ordinals[$result['current']];

      if ($this->pr_rfc_char_is_alpha($code)) {
        // allowed in: scheme, authority, path
      }
      elseif ($this->pr_rfc_char_is_digit($code)) {
        // allowed in: scheme, authority, path
      }
      elseif ($code == c_base_ascii::COLON) {
        $not_path = TRUE;

        if ($not_scheme) {
          // must be an authority as per notes in the comments section of the function.
          if ($not_authority) {
            unset($not_scheme);
            unset($not_authority);
            unset($not_path);
            unset($processed_string);

            $result['invalid'] = TRUE;
            return $result;
          }
        }
        else {
          // must be an scheme as per notes in the comments section of the function.
          $not_authority = TRUE;
        }
      }
      elseif ($code == c_base_ascii::PLUS || $code == c_base_ascii::MINUS || $code == c_base_ascii::PERIOD) {
        // allowed in: scheme, authority, path
      }
      elseif ($code == c_base_ascii::AT || $code == c_base_ascii::SLASH_FORWARD) {
        // allowed in: authority, path

        $not_scheme = TRUE;
      }
      elseif ($this->pr_rfc_char_is_unreserved($code)) {
        // allowed in: authority, path

        $not_scheme = TRUE;
      }
      elseif ($code == c_base_ascii::BRACKET_OPEN) {
        // allowed in: authority

        $not_scheme = TRUE;
        $not_path = TRUE;
      }
      else {
        unset($not_scheme);
        unset($not_authority);
        unset($not_path);
        unset($processed_string);

        $result['invalid'] = TRUE;
        return $result;
      }

      if (($not_scheme && $not_path) || ($not_scheme && $not_authority) || ($not_authority && $not_path)) {
        break;
      }

      $processed_string .= $characters[$result['current']];
    }
    unset($code);

    if ($result['current'] >= $stop) {
      unset($not_scheme);
      unset($not_authority);
      unset($not_path);
      unset($processed_string);

      return $result;
    }

    if ($not_authority && $not_path) {
      unset($not_scheme);
      unset($not_authority);
      unset($not_path);

      $result['scheme'] = $processed_string;
      unset($processed_string);

      $result['current']++;
      if ($result['current'] >= $stop) {
        return $result;
      }

      // check to see if '/' immediately follows, if not then this is a urn.
      $code = $ordinals[$result['current']];
      if ($code == c_base_ascii::SLASH_FORWARD) {
        unset($code);

        // at this point it is known that this is a url instead of a urn.
        $this->p_rfc_string_is_uri_path($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }

        // check for query.
        if ($ordinals[$result['current']] == c_base_ascii::QUESTION_MARK) {
          // the first question mark is not recorded so skip past it before validating the fragment.
          $result['current']++;
          if ($result['current'] >= $stop) {
            return $result;
          }

          $this->p_rfc_string_is_uri_query($ordinals, $characters, $stop, $result);
          if ($result['invalid'] || $result['current'] >= $stop) {
            return $result;
          }
        }

        // check for fragment.
        if ($ordinals[$result['current']] == c_base_ascii::HASH) {
        // only the first hash is supported in the fragment (and it is not recorded) so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

          $this->p_rfc_string_is_uri_fragment($ordinals, $characters, $stop, $result);
          if ($result['invalid'] || $result['current'] >= $stop) {
            return $result;
          }
        }

        return $result;
      }
      unset($code);

      // process path argument and if a single ':' is found, then this is a urn.
      for (; $result['current'] < $stop; $result['current']++) {
        if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
          unset($code);

          // @fixme: should error be reported? do some debugging with this.
          $result['invalid'] = TRUE;
          return $result;
        }

        $code = $ordinals[$result['current']];

        if ($code == c_base_ascii::HASH || $code == c_base_ascii::QUESTION_MARK) {
          // found possible query or fragment.
          $result['url'] = TRUE;
          break;
        }
        elseif ($code == c_base_ascii::COLON) {
          $result['url'] = FALSE;
        }
        elseif (!$this->pr_rfc_char_is_pchar($code)) {
          unset($code);

          $result['invalid'] = TRUE;
          return $result;
        }

        $result['path'] .= $characters[$result['current']];
      }
      unset($code);

      if ($result['current'] >= $stop) {
        return $result;
      }

      // check for query.
      if ($ordinals[$result['current']] == c_base_ascii::QUESTION_MARK) {
        // the first question mark is not recorded so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_query($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }

      // check for fragment.
      if ($ordinals[$result['current']] == c_base_ascii::HASH) {
        // only the first hash is supported in the fragment (and it is not recorded) so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_fragment($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }

      return $result;
    }
    elseif ($not_scheme && $not_path) {
      unset($not_scheme);
      unset($not_authority);
      unset($not_path);

      $result['authority'] = $processed_string;
      unset($processed_string);

      $result['current']++;
      if ($result['current'] >= $stop) {
        return $result;
      }

      // check for authority.
      $this->p_rfc_string_is_uri_authority($ordinals, $characters, $stop, $result);
      if ($result['invalid'] || $result['current'] >= $stop) {
        return $result;
      }

      // check for path.
      $this->p_rfc_string_is_uri_path($ordinals, $characters, $stop, $result);
      if ($result['invalid'] || $result['current'] >= $stop) {
        return $result;
      }

      // check for query.
      if ($ordinals[$result['current']] == c_base_ascii::QUESTION_MARK) {
        // the first question mark is not recorded so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_query($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }

      // check for fragment.
      if ($ordinals[$result['current']] == c_base_ascii::HASH) {
        // only the first hash is supported in the fragment (and it is not recorded) so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_fragment($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }
    }
    elseif ($not_scheme && $not_authority) {
      unset($not_scheme);
      unset($not_authority);
      unset($not_path);

      $result['path'] = $processed_string;
      unset($processed_string);

      $result['current']++;
      if ($result['current'] >= $stop) {
        return $result;
      }

      // check for path.
      $this->p_rfc_string_is_uri_path($ordinals, $characters, $stop, $result);
      if ($result['invalid'] || $result['current'] >= $stop) {
        return $result;
      }

      // check for query.
      if ($ordinals[$result['current']] == c_base_ascii::QUESTION_MARK) {
        // the first question mark is not recorded so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_query($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }

      // check for fragment.
      if ($ordinals[$result['current']] == c_base_ascii::HASH) {
        // only the first hash is supported in the fragment (and it is not recorded) so skip past it before validating the fragment.
        $result['current']++;
        if ($result['current'] >= $stop) {
          return $result;
        }

        $this->p_rfc_string_is_uri_fragment($ordinals, $characters, $stop, $result);
        if ($result['invalid'] || $result['current'] >= $stop) {
          return $result;
        }
      }
    }
    unset($not_scheme);
    unset($not_authority);
    unset($not_path);
    unset($processed_string);

    $result['invalid'] = TRUE;

    return $result;
  }

  /**
   * Combine a uri array into a single uri.
   *
   * This does not validate the uri, it simply merges an array.
   *
   *   foo://example.com:8042/over/there?name=ferret#nose
   *   \_/   \______________/\_________/ \_________/ \__/
   *    |           |            |            |        |
   *  scheme    authority       path        query   fragment
   *    |   _____________________|__
   *   / \ /                        \
   *   urn:example:animal:ferret:nose
   *
   *
   * @param array $uri_array
   *   A url array with the following structure:
   *   - 'scheme': The protocol string.
   *   - 'authority': The domain string.
   *   - 'path': The path string.
   *   - 'query': An array of url arguments.
   *   - 'fragment': The id string.
   *
   * @return string|bool
   *   A combined url array on success.
   *   FALSE is returned on error.
   *
   * @see: self::pr_rfc_string_is_uri()
   * @see: https://tools.ietf.org/html/rfc3986
   */
  protected function pr_rfc_string_combine_uri_array($uri_array) {
    if (!$uri_array['url']) {
      // both scheme and path are required for urn.
      if (!isset($uri_array['scheme']) || !isset($uri_array['path'])) {
        return FALSE;
      }

      $combined .= $uri_array['scheme'] . ':' . $uri_array['path'];

      return $combined;
    }

    $combined = NULL;
    if (isset($uri_array['scheme'])) {
      $combined .= $uri_array['scheme'] . '://';
    }

    if (isset($uri_array['authority'])) {
      $combined .= $uri_array['authority'];
    }

    if (isset($uri_array['path'])) {
      $combined .= $uri_array['path'];
    }

    if (!empty($uri_array['query'])) {
      if (is_string($uri_array['query'])) {
        $combined .= '?' . $uri_array['query'];
      }
      elseif (is_array($uri_array['query'])) {
        $combined .= '?' . http_build_query($uri_array['query'], '', '&', PHP_QUERY_RFC3986);
      }
    }

    if (isset($uri_array['fragment'])) {
      $combined .= '#' . $uri_array['fragment'];
    }

    return $combined;
  }

  /**
   * Effectively unshift a value onto a given array with a specified index.
   *
   * The NULL key should be the first key in the weight.
   * PHP does not provide a way to preserve keys when merging arrays nor does PHP provide a way to unshift a value onto an array with a specific key.
   *
   * @param $key
   *   The index name to unshift the value onto the array as.
   * @param array $array
   *   The array to unshift onto.
   */
  protected function pr_prepend_array_value($key, &$array) {
    if (!array_key_exists($key, $array)) {
      return;
    }

    $value = $array[$key];
    unset($array[$key]);

    $new_array = array(
      $key => $value,
    );
    unset($value);

    foreach ($array as $key => $value) {
      $new_array[$key] = $value;
    }

    $array = $new_array;
    unset($new_array);
  }

  /**
   * Helper function for pr_rfc_string_is_uri() to process: authority.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $stop
   *   The position in the arrays to stop checking.
   * @param array $result
   *   An array of return results used by the pr_rfc_string_is_uri().
   *
   * @see: self::pr_rfc_string_is_uri()
   */
  private function p_rfc_string_is_uri_authority(&$ordinals, &$characters, &$stop, &$result) {
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        unset($code);

        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        return $result;
      }

      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::PERCENT) {
        // valid only if two hex digits immediately follow.
        $result['current']++;
        if ($result['current'] >= $stop) {
          // this is invalid because it is cut off before 2 hex digits are required and none is found.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];
        if (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['current']++;
        if ($result['current'] >= $stop) {
          // this is invalid because it is cut off before 2 hex digits are required and only one is found.
          $result['invalid'] = TRUE;
          break;
        }

        $code = $ordinals[$result['current']];
        if (!$this->pr_rfc_char_is_hexdigit($ordinals[$result['current']])) {
          $result['invalid'] = TRUE;
          break;
        }
      }
      elseif ($code == c_base_ascii::AT || $code == c_base_ascii::COLON) {
        // this is valid.
      }
      elseif ($code == c_base_ascii::BRACKET_OPEN || $code == c_base_ascii::BRACKET_CLOSE) {
        // this is valid.
      }
      elseif ($this->pr_rfc_char_is_unreserved($code)) {
        // this is valid.
      }
      elseif ($this->pr_rfc_char_is_sub_delims($code)) {
        // this is valid.
      }
      else {
        unset($code);

        $result['invalid'] = TRUE;
        return $result;
      }

      $result['authority'] .= $characters[$result['current']];
    }
    unset($code);
  }

  /**
   * Helper function for pr_rfc_string_is_uri() to process: path.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $stop
   *   The position in the arrays to stop checking.
   * @param array $result
   *   An array of return results used by the pr_rfc_string_is_uri().
   *
   * @see: self::pr_rfc_string_is_uri()
   */
  private function p_rfc_string_is_uri_path(&$ordinals, &$characters, &$stop, &$result) {
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        unset($code);

        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        return $result;
      }

      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::HASH || $code == c_base_ascii::QUESTION_MARK) {
        // found possible query or fragment.
        break;
      }
      elseif ($code == c_base_ascii::SLASH_FORWARD) {
        // this is valid.
      }
      elseif (!$this->pr_rfc_char_is_pchar($code)) {
        unset($code);

        $result['invalid'] = TRUE;
        return $result;
      }

      $result['path'] .= $characters[$result['current']];
    }
    unset($code);
  }

  /**
   * Helper function for pr_rfc_string_is_uri() to process: query.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $stop
   *   The position in the arrays to stop checking.
   * @param array $result
   *   An array of return results used by the pr_rfc_string_is_uri().
   *
   * @see: self::pr_rfc_string_is_uri()
   */
  private function p_rfc_string_is_uri_query(&$ordinals, &$characters, &$stop, &$result) {
    $query_name = NULL;
    $query_value = NULL;
    $no_value = FALSE;

    $result['query'] = array();
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        unset($code);
        unset($query_name);
        unset($query_value);
        unset($no_value);

        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        return $result;
      }

      $code = $ordinals[$result['current']];

      if ($code == c_base_ascii::HASH) {
        // hash is not part of the query but does mark the end of the query as it is the start of the fragment.
        break;
      }
      elseif ($code == c_base_ascii::AMPERSAND) {
        // The '&' designates a new name and value, separate each individual value inside the array.
        $result['query'][$query_name] = $query_value;

        $query_name = NULL;
        $query_value = NULL;
        $no_value = FALSE;

        continue;
      }
      elseif ($code == c_base_ascii::EQUAL) {
        // The '=' designates a value for the current name.
        if ($no_value || is_null($query_name)) {
          $query_name .= $characters[$result['current']];
          $no_value = TRUE;
          continue;
        }

        $query_value = '';
        continue;
      }
      elseif (!$this->pr_rfc_char_is_query($code)) {
        unset($code);
        unset($query_name);
        unset($query_value);
        unset($no_value);

        $result['invalid'] = TRUE;
        return $result;
      }

      if (is_null($query_value)) {
        $query_name .= $characters[$result['current']];
      }
      else {
        $query_value .= $characters[$result['current']];
      }
    }
    unset($code);
    unset($no_value);

    $result['query'][$query_name] = $query_value;

    unset($query_name);
    unset($query_value);
  }

  /**
   * Helper function for pr_rfc_string_is_uri() to process: fragment.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $stop
   *   The position in the arrays to stop checking.
   * @param array $result
   *   An array of return results used by the pr_rfc_string_is_uri().
   *
   * @see: self::pr_rfc_string_is_uri()
   */
  private function p_rfc_string_is_uri_fragment(&$ordinals, &$characters, &$stop, &$result) {
    for (; $result['current'] < $stop; $result['current']++) {
      if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        unset($code);

        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        return $result;
      }

      $code = $ordinals[$result['current']];

      // the syntax for query is identical to fragment.
      if (!$this->pr_rfc_char_is_query($code)) {
        unset($code);

        $result['invalid'] = TRUE;
        return $result;
      }

      $result['fragment'] .= $characters[$result['current']];
    }
    unset($code);
  }

  /**
   * Helper function for bypassing whitespaces.
   *
   * @param array $ordinals
   *   An array of integers representing each character of the string.
   * @param array $characters
   *   An array of characters representing the string.
   * @param int $stop
   *   The position in the arrays to stop checking.
   * @param array $result
   *   An array of return results used by the pr_rfc_string_is_uri().
   *
   * @see: self::pr_rfc_string_is_uri()
   */
  private function p_rfc_string_skip_past_whitespace(&$ordinals, &$characters, &$stop, &$result) {
    for (; $result['current'] < $stop; $result['current']++) {
     if (!array_key_exists($result['current'], $ordinals) || !array_key_exists($result['current'], $characters)) {
        // @fixme: should error be reported? do some debugging with this.
        $result['invalid'] = TRUE;
        break;
      }

      if (!$this->pr_rfc_char_is_wsp($ordinals[$result['current']])) {
        break;
      }
    }
  }
}
