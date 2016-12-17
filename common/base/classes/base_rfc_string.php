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
    if ($characters instanceof c_base_return_error) {
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
    if ($ordinals instanceof c_base_return_error) {
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
   *
   * @see: base_rfc_char::pr_rfc_char_qtext()
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
   * A string that is a digit has the following syntax:
   * - token [ 1*(ws) ( token68 / [ ( "," / token *(ws) "=" *(ws) ( token / quoted-string ) ) *( *(ws) "," [ *(ws) token *(ws) "=" *(ws) ( token / quoted-string ) ] ) ] ) ]
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
      'text' => NULL,
      'current' => $start,
      'invalid' => FALSE,
    );

    // @todo: this looks like a lot of work, so deal with this at some point in the future because this is a very low priority function.
    $result['invalid'] = TRUE;
/*
    if (is_null($stop)) {
      $stop = count($ordinals);
    }

    if ($start >= $stop) {
      return $result;
    }

    for (; $result['current'] < $stop; $result['current']++) {
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
    $this->p_prepend_array_value(NULL, $result['choices']);

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
   * @see: base_rfc_char::pr_rfc_char_tchar()
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
   * @see: base_rfc_char::pr_rfc_char_tchar()
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
        // @todo: handle whitespace between '='.
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
   * @see: base_rfc_char::pr_rfc_char_tchar()
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
        // @todo: handle whitespace between '='.
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
   * - 1*(*(ws) "," *(ws) token)
   *
   * Original valued_token standard syntax:
   * - *("," *(ws)) token *(*(ws) "," *(ws) token)
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
   * @see: base_rfc_char::pr_rfc_char_tchar()
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
   *   - 'address': A string containing the processed ip address.
   *                When is_future is TRUE, this is an array containing:
   *                - 'version': The ipfuture version.
   *                - 'ip': The ip address.
   *   - 'is_future': A boolean that when TRUE represents an ipvfuture address and when FALSE represents an ipv6 address.
   *   - 'current': an integer representing the position the counter stopped at.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: base_rfc_char::pr_rfc_char_tchar()
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  protected function pr_rfc_string_is_query($ordinals, $characters, $start = 0, $stop = NULL) {
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
   * @see: base_rfc_char::pr_rfc_char_tchar()
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

        if (self::pr_rfc_char_is_digit($code)) {
          // do nothing, valid
        }
        elseif (self::pr_rfc_char_is_alpha($code)) {
          // do nothing, valid
        }
        elseif (self::pr_rfc_char_is_unreserved($code)) {
          // do nothing, valid
        }
        elseif (self::pr_rfc_char_is_sub_delims($code)) {
          // do nothing, valid
        }
        elseif ($code == c_base_ascii::COLON) {
          // do nothing, valid
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
  protected function p_prepend_array_value($key, &$array) {
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
}
