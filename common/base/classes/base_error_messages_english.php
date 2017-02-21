<?php
/**
 * @file
 * Implements english language support for common error messages.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_languages.php');

/**
 * English language version of common error messages.
 */
class c_base_error_messages_eng extends i_base_error_messages {

  /**
   * Returns a standard error message associated with the given code.
   *
   * @param int $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are provided.
   *   All placeholders should begin with a single colon ':'.
   */
  static function s_get_message($code, $arguments = TRUE) {
    if ($code === self::ARGUMENT_INVALID) {
      if ($arguments === TRUE) {
        return 'An invalid argument, :argument_name, has been specified.';
      }
      else {
        return 'An invalid argument has been specified.';
      }
    }
    elseif ($code === self::OPERATION_FAILURE) {
      if ($arguments === TRUE) {
        return 'Failed to perform operation: :operation_name.';
      }
      else {
        return 'Failed to perform operation.';
      }
    }
    elseif ($code === self::INVALID_FORMAT) {
      if ($arguments === TRUE) {
        return 'The argument, :format_name, has an invalid format.';
      }
      else {
        return 'An invalid format has been specified.';
      }
    }
    elseif ($code === self::INVALID_VARIABLE) {
      if ($arguments === TRUE) {
        return 'The variable, :variable_name, is invalid.';
      }
      else {
        return 'An invalid variable has been specified.';
      }
    }

    return '';
  }
}
