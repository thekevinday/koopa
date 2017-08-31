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
final class c_base_error_messages_english implements i_base_error_messages {

  /**
   * Converts a given error message into a processed string.
   *
   * @param c_base_error $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are provided.
   *   All placeholders should begin with a single colon ':'.
   * @param bool $function_name
   *   (optional) When TRUE, the function name is included with the message.
   *   When FALSE, no funciton name is provided.
   * @param null|string $additional_message
   *   (optional) Any additional messages to display.
   * @param bool $use_html
   *   (optional) When TRUE, the message is escaped and then wrapped in HTML.
   *   When FALSE, no HTML wrapping or escaping is peformed.
   *
   * @return c_base_return_string
   *   A processed string is returned on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: s_get_message()
   */
  static function s_render_error_message($error, $arguments = TRUE, $function_name = FALSE, $additional_message = NULL, $html = TRUE) {
    if (!($error instanceof c_base_error)) {
      return c_base_return_string::s_new('');
    }

    $code = $error->get_code();
    if (is_null($code)) {
      unset($code);
      return c_base_return_string::s_new('');
    }

    $message = self::s_get_message($code, $arguments, $function_name)->get_value_exact();
    if (is_string($additional_message)) {
      $message .= $additional_message;
    }

    if (empty($message)) {
      unset($message);
      unset($code);
      return c_base_return_string::s_new('');
    }
    unset($code);

    if ($arguments === FALSE) {
      unset($arguments);

      if ($html) {
        return c_base_return_string::s_new('<div class="error_message error_message-' . $code . '">' . $message . '</div>');
      }

      return c_base_return_string::s_new($message);
    }

    $details = $error->get_details();
    if (isset($details['arguments']) && is_array($details['arguments'])) {
      if ($html) {
        foreach ($details['arguments'] as $detail_name => $detail_value) {
          $detail_name_css = 'error_message-argument-' . preg_replace('/[^[:word:]-]/i', '', $detail_name);
          $message = preg_replace('/' . preg_quote($detail_name, '/') . '\b/i', '<div class="error_message-argument ' . $detail_name_css . '">' . htmlspecialchars($detail_value, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</div>', $message);
        }
        unset($detail_name_css);
      }
      else {
        foreach ($details as $detail_name => $detail_value) {
          $message = preg_replace('/' . preg_quote($detail_name, '/') . '\b/i', $detail_value, $message);
        }
      }
      unset($detail_name);
      unset($detail_value);
      unset($details);

      if ($html) {
        return c_base_return_string::s_new('<div class="error_message error_message-' . $code . '">' . $message . '</div>');
      }

      return c_base_return_string::s_new($message);
    }
    unset($details);

    if ($html) {
      return c_base_return_string::s_new('<div class="error_message error_message-' . $code . '">' . $message . '</div>');
    }

    return c_base_return_string::s_new($message);
  }

  /**
   * Returns a standard error message associated with the given code.
   *
   * @param int $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are provided.
   *   All placeholders should begin with a single colon ':'.
   * @param bool $function_name
   *   (optional) When TRUE, the function name is included with the message.
   *   When FALSE, no funciton name is provided.
   *
   * @return c_base_return_string
   *   A processed string is returned on success.
   *   FALSE with error bit set is returned on error.
   */
  static function s_get_message($code, $arguments = TRUE, $function_name = FALSE) {
    $function_name_string = NULL;
    if ($function_name) {
      $function_name_string = ' while calling :{function_name}';
    }

    if ($code === static::INVALID_ARGUMENT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('An invalid argument, :{argument_name}, has been specified' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('An invalid argument has been specified' . $function_name_string . '.');
      }
    }
    elseif ($code === static::INVALID_FORMAT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The argument, :{format_name}, has an invalid format' . $function_name_string . '.:{expected_format}');
      }
      else {
        return c_base_return_string::s_new('An invalid format has been specified.');
      }
    }
    elseif ($code === static::INVALID_SESSION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The requested session is invalid' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('The requested session is invalid.');
      }
    }
    elseif ($code === static::INVALID_VARIABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The variable, :{variable_name}, is invalid' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('An invalid variable has been specified.');
      }
    }
    elseif ($code === static::OPERATION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Failed to perform operation, :{operation_name}' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to perform operation.');
      }
    }
    elseif ($code === static::OPERATION_UNECESSARY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Did not perform unnecessary operation, :{operation_name}' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Did not perform unnecessary operation.');
      }
    }
    elseif ($code === static::FUNCTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The function, :{function_name}, has failed execution.');
      }
      else {
        return c_base_return_string::s_new('A function has failed execution.');
      }
    }
    elseif ($code === static::NOT_FOUND) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Not found' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Not found.');
      }
    }
    elseif ($code === static::NOT_FOUND_ARRAY_INDEX) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The index, :{index_name}, was not found in the array, :{array_name}' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to find index within specified array.');
      }
    }
    elseif ($code === static::NOT_FOUND_FILE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The file, :{file_name}, was not found or cannot be accessed' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('File not found or cannot be accessed.');
      }
    }
    elseif ($code === static::NOT_FOUND_DIRECTORY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The directory, :{directory_name}, was not found or cannot be accessed' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('File not found or cannot be accessed.');
      }
    }
    elseif ($code === static::NOT_FOUND_PATH) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The path, :{path_name}, was not found or cannot be accessed' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Path not found or cannot be accessed.');
      }
    }
    elseif ($code === static::NOT_DEFINED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The requested data, :{data_name}, is not defined' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('The requested data is not defined.');
      }
    }
    elseif ($code === static::NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The resource, :{resource_name}, is not connected' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('The resource is not connected.');
      }
    }
    elseif ($code === static::NO_SUPPORT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The functionality, :{functionality_name}, is currently not supported.' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('The requested functionality is not supported.');
      }
    }
    elseif ($code === static::POSTGRESQL_CONNECTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Failed to connect to the database, :{database_name}, reasons: :{failure_reasons}' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to connect to the database.');
      }
    }
    elseif ($code === static::POSTGRESQL_NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The database, :{database_name}, is not connected' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('The database is not connected.');
      }
    }
    elseif ($code === static::POSTGRESQL_NO_RESOURCE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('No database resource is available' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('No database resource is available.');
      }
    }
    elseif ($code === static::POSTGRESQL_ERROR) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Database query error: :{database_error_message}' . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to perform database query.');
      }
    }
    elseif ($code === static::SOCKET_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Failed to perform socket operation, :{operation_name}, socket error (:{socket_error}) \':{socket_error_message}\'' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to perform socket operation.');
      }
    }
    elseif ($code === static::ACCESS_DENIED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied, :{operation_name}' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied.');
      }
    }
    elseif ($code === static::ACCESS_DENIED_UNAVAILABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied due to unavailability, :{operation_name} ' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied due to unavailability.');
      }
    }
    elseif ($code === static::ACCESS_DENIED_USER) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied for user :name_machine_user (:{id_user}), :{operation_name} ' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied for user.');
      }
    }
    elseif ($code === static::ACCESS_DENIED_ADMINISTRATION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied for administrative reasons, :{operation_name} ' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied for administrative reasons.');
      }
    }
    elseif ($code === static::SERVER_ERROR) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('A server error has occurred, :{operation_name} ' . (is_null($function_name_string) ? '' : ',') . $function_name_string . '.');
      }
      else {
        return c_base_return_string::s_new('A server error has occurred.');
      }
    }

    return c_base_return_string::s_new('');
  }
}
