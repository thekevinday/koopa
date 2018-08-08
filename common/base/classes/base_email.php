<?php
/**
 * @file
 * Provides a class for managing e-mail related functionality.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_ascii.php');

require_once('common/base/traits/base_rfc_string.php');

/**
 * A generic class for managing the e-mail related functionality.
 *
 * PHP fails to follow the more recent rfc standards at this time.
 * A custom implementation is provided to handle and process the rfc standards:
 * - rfc 5322
 * - rfc 6854
 * - rfc 7231
 *
 * @see: https://tools.ietf.org/html/rfc5322#section-3.4
 * @see: https://tools.ietf.org/html/rfc6854
 * @see: https://tools.ietf.org/html/rfc7231#section-5.5.1
 *
 * @require class c_base_ascii
 * @require class c_base_utf8
 */
class c_base_email extends c_base_return_string {
  use t_base_rfc_string;

  const LINE_LENGTH_LIMIT_SOFT = 78;
  const LINE_LENGTH_LIMIT_HARD = 998;


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
   * Decode and check that the given e-mail address is valid.
   *
   * Validation is done according to rfc5322, rfc6854, and rfc7231.
   *
   * E-mails will be processed in the following approximate manners:
   *   [[name_group]: ["[name_human]"] <[name_machine]@[name_address]>, [name_machine]@[name_address]];
   *
   * Comments can get out of hand but is still technical part of the standard, so the following must also be supported:
   *  [[name_group]: ([comment])["[name_human]"]([comment]) <([comment])[name_machine]([comment])@([comment])[name_address]([comment])>, ([comment])[name_machine]([comment])@([comment])[name_address]([comment])];
   *
   * This is incredibly prone to abuse, mis-use, and exploitation.
   * To prevent abuse, comments will be stripped out of the e-mail.
   *
   * @param string $email
   *   The email to validate and decode.
   * @param null|bool $custom_processing
   *   @fixme: on second thought, it might be cleaner to implement these "custom_processing" cases in separate functions.
   *   (optional) If NULL, then support entire email possibilities.
   *   If FALSE, then only process a single (ungrouped) email with entire remaining possibilities.
   *   IF TRUE, then support only the machine name portions of a single e-mail (the "id_left @ id_right" part of what is called "message id").
   *
   * @return array
   *   A decoded e-mail split into its different parts inside an array.
   *   An array key called 'invalid' exists to designate that the uri is invalid.
   *
   * @see: base_rfc_string::pr_rfc_string_prepare()
   * @see: https://tools.ietf.org/html/rfc5322#section-3.4
   * @see: https://tools.ietf.org/html/rfc6854
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.1
   */
  private static function p_parse_email_full($email) {
    $result = [
      'emails' => [],
      'invalid' => FALSE,
    ];

    $processed = [];
    $group = NULL;
    $group_id = NULL;
    $process_machine_part = FALSE;
    $current = 0;
    $current_string = NULL;
    $current_chunk_start = 0;
    $delimited = FALSE;
    $name_human = NULL;
    $name_machine = NULL;
    $name_group = NULL;

    $email_text = $this->pr_rfc_string_prepare($email);
    if ($email_text['invalid']) {
      unset($email_text);
      $result['invalid'] = TRUE;
      return $result;
    }

    $stop = count($email_text['codepoints']) + 1;

    $is_quoted = FALSE;
    $is_named_human = FALSE;
    $is_named_machine = FALSE;
    $has_found_comment = FALSE;
    for (; $current < $stop; $current++) {
      if ($email_text['codepoints'][$current] == c_base_ascii::LESS_THAN) {
        if (!$is_quoted && !$is_named_human && !is_null($current_string)) {
          $name_human = $current_string;
          $is_named_human = TRUE;
        }

        $current_string = NULL;
        $has_found_comment = FALSE;

        $parsed = p_parse_email_machine($email_text['codepoints'], $email_text['characters'], $current, $stop, c_base_ascii::GREATER_THAN);
        $current = $parse_result['current'];

        // if the proper stop point was reached, then the '<' is a valid opening.
        if ($parsed['stopped_at'] && !$parsed['invalid'] && !empty($parsed['name_machine'])) {
          $name_machine = $parsed['name_machine'] . '@' . $parsed['name_address'];
          $result['emails'][$name_machine] = [
            'name_human' => $name_human,
            'name_machine' => $name_machine,
            'name_address' => $parsed['name_address'],
          ];

          $current_chunk_start = $current + 1;
          $is_named_machine = TRUE;

          unset($parsed);
          continue;
        }

        $result['invalid'] = TRUE;
        unset($parsed);
        break;
      }
      else if ($email_text['codepoints'][$current] == c_base_ascii::QUOTE_DOUBLE) {
        if ($is_quoted || $is_named_human || $is_named_machine) {
          // cannot have more than one quoted or unquoted string and human names cannot follow machine names.
          $result['invalid'] = TRUE;
          break;
        }

        $current_string = NULL;
        $has_found_comment = FALSE;

        $is_quoted = TRUE;
        $closing_quote = FALSE;
        for (; $current < $stop; $current++) {

          if ($email_text['codepoints'][$current] == c_base_ascii::SLASH_BACKWARD) {
            if ($current + 1 >= $stop) {
              $result['invalid'] = TRUE;
              break;
            }

            // only a double quote may be delimited, otherwise the backwards slash is invalid.
            if ($email_text['codepoints'][$current + 1] == c_base_ascii::QUOTE_DOUBLE) {
              $current++;
            }
            else {
              $result['invalid'] = TRUE;
              break;
            }
          }
          else if ($email_text['codepoints'][$current] == c_base_ascii::QUOTE_DOUBLE) {
            $closing_quote = TRUE;
            break;
          }
          else if ($this->pr_rfc_char_is_crlf($email_text['codepoints'][$current])) {
            // not allowed inside a quoted string.
            $result['invalid'] = TRUE;
            break;
          }

          $name_human .= $email[$current];
        }

        if (!$closing_quote) {
          $result['invalid'] = TRUE;
          unset($closing_quote);
          break;
        }
        unset($closing_quote);

        $current_chunk_start = $current + 1;
        $is_named_human = TRUE;

        continue;
      }
      else if ($this->pr_rfc_char_is_fws($email_text['codepoints'][$current])) {
        // though unusual, starting with whitespace appears to be technically allowed unless I am misunderstanding or overlooking something.
        if (!$is_named_human && !$is_named_machine && is_null($current_string)) {
          continue;
        }
      }
      else if ($email_text['codepoints'][$current] == c_base_ascii::PARENTHESIS_OPEN) {
        if ($has_found_comment) {
          // there may be only one comment between non-comments.
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }

        // start with a comment and comments may have comments within themselves.
        // though bizarre, this appears to be technically allowed unless I am misunderstanding or overlooking something.
        // this includes delimiters.
        $parsed = $this->pr_rfc_string_is_comment($email_text['codepoints'], $email_text['characters'], $current, $stop);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }

        $has_found_comment = TRUE;
        $current_chunk_start = $current + 1;

        unset($parsed);
        continue;
      }
      else if ($email_text['codepoints'][$current] == c_base_ascii::COLON) {
        if ($is_named_human || $is_named_machine) {
          // A colon may not be specified following human or machine names.
          $result['invalid'] = TRUE;
          break;
        }

        if (is_null($current_string)) {
          // A colon without any preceding valid text is considered an invalid group (groups must have group names).
          $result['invalid'] = TRUE;
          break;
        }

        // @todo: implement this via another loop or function due to the complexity.
        //        do not nullify $current_string because it is needed to define/provide the name of the group.
        // @todo: if the colon is supplied, but double quotes were used, then does this mean it is a double quoted group name? (in which case the test against $is_named_human is invalid.)
      }
      else if ($email_text['codepoints'][$current] == c_base_ascii::AT) {
        if ($is_named_human || $is_named_machine) {
          // when a human name is supplied, then the e-mail address must be inside of '<' and '>'.
          // multiple machine names may not be specified.
          $result['invalid'] = TRUE;
          break;
        }

        if ($current == $current_chunk_start) {
          // if the machine name starts with an '@', then it is invalid.
          $result['invalid'] = TRUE;
          break;
        }

        $current_string = NULL;
        $has_found_comment = FALSE;

        // the machine name is processed using the current chunk start instead of the current value because the '@' is not the first character in the machine name.
        $parsed = p_parse_email_machine($email_text['codepoints'], $email_text['characters'], $current_chunk_start, $stop);
        $current = $parse_result['current'];

        // if the proper stop point was reached, then a valid machine name was found.
        if ($parsed['stopped_at'] && !$parsed['invalid'] && !empty($parsed['name_machine'])) {
          $name_machine = $parsed['name_machine'] . '@' . $parsed['name_address'];
          $result['emails'][$name_machine] = [
            'name_human' => $name_human,
            'name_machine' => $name_machine,
            'name_address' => $parsed['name_address'],
          ];

          $current_chunk_start = $current + 1;

          unset($parsed);
          continue;
        }

        $result['invalid'] = TRUE;
        unset($parsed);
        break;
      }

      $current_string .= $email_text['characters'][$current];
    }
    unset($name_group);
    unset($name_machine);
    unset($name_human);
    unset($delimited);
    unset($is_quoted);
    unset($is_named_human);
    unset($is_named_machine);
    unset($has_found_comment);
    unset($process_machine_part);
    unset($current);
    unset($current_string);
    unset($current_chunk_start);
    unset($group_id);
    unset($group);
    unset($processed);
    unset($stop);
    unset($email_text);

    return $result;
  }

  private static function p_parse_email_single($email, $start = NULL, $stop = NULL) {
    // @todo: implement this.
  }

  /**
   * Process only the machine part of a single e-mail address.
   *
   * This parses an e-mail string of the form: [name_machine]@[name_address].
   *
   * The standard allows for (comment)[name_machine](comment)@(comment)[name_address](comment).
   * - These comments will be ignored and stripped out.
   *
   * This does not validate [name_address] to be a correct host name or ip address.
   * @todo: consider doing this filter_var() and options like: FILTER_VALIDATE_IP (I must review whether or not FILTER_VALIDATE_IP is standards compliant).
   *
   * The arguments $start, $stop, and $end_at_brace allow for the other more complex email parsers to call this function when necessary.
   *
   * @param array $email_codes
   *   An array containing codepoint values for an e-mail address string.
   * @param array $email_characters
   *   An array containing characters for the e-mail address string.
   * @param null|int $start
   *   (optional) Specify a starting point in which to begin processing.
   * @param null|int $stop
   *   (optional) Specify a stopping point in which to end processing.
   *
   * @param bool|int $stop_at
   *   (optional) When FALSE, this function returns only when the end of string or an invalid character is found (including brace).
   *   Invalid characters result in an error.
   *   When not FALSE, this is an integer representing a single character to halt processing on.
   *
   * @return array
   *   An array containing the processed pieces.
   *   The array key 'invalid' is set TRUE on error.
   *   The array key 'current' is set to location in the string of where processing stopped.
   *   - For example, if execution was stopped on an unexpected brace, the location would be the position of that specific brace.
   */
  private static function p_parse_email_machine($email_codes, $email_characters, $start = NULL, $stop = NULL, $stop_at = FALSE) {
    $result = [
      'name_machine' => '',
      'name_address' => '',
      'invalid' => FALSE,
      'current' => 0,
      'stopped_at' => FALSE,
    ];

    if (!is_null($start)) {
      $result['current'] = $start;
    }

    if (is_null($stop)) {
      $stop = count($email_codes) + 1;
    }

    // first check for and ignore comments at the start.
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $email_codes[$result['current']];

      if ($code === $stop_at) {
        // stopping at this point means that the address is invalid.
        $result['invalid'] = TRUE;
        $result['stopped_at'] = TRUE;
        break;
      }

      if ($email[$current] == '(') {
        $parsed = $this->pr_rfc_string_is_comment($email_codes, $email_characters, $result['current'], $stop);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }
        unset($parsed);

        // there may be multiple comments, so do not break at this point.
      }
      else if (!$this->pr_rfc_char_is_fws($code)) {
        // the first non-comment, non-fws char should be the start of the [name_machine].
        break;
      }
    }
    unset($code);

    if ($result['current'] >= $stop) {
      $result['invalid'] = TRUE;
    }

    if ($result['invalid']) {
      return $result;
    }

    // process [name_machine].
    $started = FALSE;
    $stopped = FALSE;
    $comments = FALSE;
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $email_codes[$result['current']];

      if ($code === $stop_at) {
        // stopping at this point means that the address is invalid.
        $result['invalid'] = TRUE;
        $result['stopped_at'] = TRUE;
        break;
      }

      if ($code == c_base_ascii::PARENTHESIS_OPEN) {
        if ($started) {
          $started = FALSE;
          $stopped = TRUE;
        }

        $comments = TRUE;

        // comments may follow a machine name.
        $parsed = $this->pr_rfc_string_is_comment($email_codes, $email_characters, $result['current'], $stop);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }
        unset($parsed);

        continue;
      }
      else if ($this->pr_rfc_char_is_fws($code)) {
        if (!$started || $stopped) {
          // ignore leading/trailing whitespace.
          continue;
        }

        // whitespace at this point should mean a stop point has been reached.
        $started = FALSE;
        $stopped = TRUE;
        $comments = FALSE;
      }
      else if ($code != c_base_ascii::PERIOD && !$this->pr_rfc_char_is_atext($code)) {
        if ($code != c_base_ascii::AT) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['current']++;
        break;
      }
      else if ($stopped) {
        // comments or whitespace may not appear between text.
        $result['invalid'] = TRUE;
        break;
      }
      else if (!$started) {
        $comments = FALSE;
        $started = TRUE;
      }

      $result['name_machine'] .= $email_characters[$result['current']];
    }
    unset($code);
    unset($started);
    unset($stopped);
    unset($comments);

    if ($result['current'] >= $stop) {
      $result['invalid'] = TRUE;
    }

    if ($result['invalid']) {
      return $result;
    }

    // comments may appear before the [name_address], skip past them.
    for (; $result['current'] < $stop; $result['current']++) {
      $code = self::pr_char_to_code($email[$result['current']]);

      if ($code === $stop_at) {
        // stopping at this point means that the address is invalid.
        $result['invalid'] = TRUE;
        $result['stopped_at'] = TRUE;
        break;
      }

      if ($code == c_base_ascii::PARENTHESIS_OPEN) {
        $parsed = $this->pr_rfc_string_is_comment($email_codes, $email_characters, $result['current'], $stop);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }
        unset($parsed);

        // there may be multiple comments, so do not break at this point.
      }
      else if (!$this->pr_rfc_char_is_fws($code)) {
        // the first non-comment, non-fws char should be the start of the [ip_address].
        break;
      }
    }
    unset($code);

    if ($result['current'] >= $stop) {
      $result['invalid'] = TRUE;
    }

    if ($result['invalid']) {
      return $result;
    }

    // process [name_address].
    if ($email_codes[$result['current']] == c_base_ascii::BRACKET_OPEN) {
      // process as a literal domain.
      for (; $result['current'] < $stop; $result['current']++) {
        $code = $email_codes[$result['current']];

        if ($code === $stop_at) {
          // stopping at this point means that the address is invalid.
          $result['invalid'] = TRUE;
          $result['stopped_at'] = TRUE;
          break;
        }

        if (!$this->pr_rfc_char_is_dtext($code)) {
          if ($code != c_base_ascii::BRACKET_CLOSE) {
            $result['invalid'] = TRUE;
            unset($stop);
            return $result;
          }

          $result['current']++;
          break;
        }

        $result['name_address'] .= $email_characters[$result['name_address']];
      }
      unset($code);
    }
    else {
      // process as a non-literal domain.
      for (; $result['current'] < $stop; $result['current']++) {
        $code = $email_codes[$result['current']];

        if ($code === $stop_at) {
          // stopping at this point, there is no way to know if it is valid or not.
          // @todo: this would be a good place to perform the name_address spot check for valid ip or host name.
          $result['stopped_at'] = TRUE;
          break;
        }

        if ($code != c_base_ascii::PERIOD && !$this->pr_rfc_char_is_atext($code)) {
          $result['invalid'] = TRUE;
          break;
        }

        $result['name_address'] .= $email_characters[$result['name_address']];
      }
      unset($code);
    }

    // @todo: the spot to check if name_address is a valid ip or host name.

    if ($result['invalid']) {
      return $result;
    }

    // comments may appear after the [name_address], skip past them.
    for (; $result['current'] < $stop; $result['current']++) {
      $code = $email_codes[$result['current']];

      if ($code === $stop_at) {
        // this is a valid name, comments are irrelevant.
        // that said, the comment was not completed, so it may be invalid.
        $result['stopped_at'] = TRUE;
        break;
      }

      if ($code == c_base_ascii::PARENTHESIS_OPEN) {
        $parsed = $this->pr_rfc_string_is_comment($email_codes, $email_characters, $result['current'], $stop);
        $result['current'] = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }
        unset($parsed);

        // there may be multiple comments, so do not break at this point.
      }
      else if (!$this->pr_rfc_char_is_fws($code)) {
        // the first non-comment, non-fws char should be the start of the [ip_address].
        break;
      }
    }
    unset($code);

    if ($current < $stop) {
      // There should be nothing at the end of a valid string.
      // If there is, then this is an invalid e-mail.
      // If stop_at is not FALSE, then stopping before the final stop point is not automatically a problem.
      if ($stop_at !== FALSE) {
        $result['invalid'] = TRUE;
      }
    }

    return $result;
  }
}
