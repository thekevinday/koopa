<?php
/**
 * @file
 * Implements english language support for common error messages.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_languages.php');

/**
 * English language version of common error messages.
 */
final class c_base_error_messages_english implements i_base_error_messages {

  /**
   * Implements i_base_error_messages().
   */
  static function s_render_error_message($error, $arguments = TRUE, $error_message = NULL, $function_name = FALSE, $additional_message = NULL, $html = TRUE) {
    if (!($error instanceof c_base_error)) {
      return c_base_return_string::s_new('');
    }

    $code = $error->get_code();
    if (is_null($code)) {
      unset($code);
      return c_base_return_string::s_new('');
    }

    $display_error_message = $error_message;
    if (is_null($error_message)) {
      if (strlen($error->get_message()) > 0) {
        $display_error_message = TRUE;
      }
      else {
        $display_error_message = FALSE;
      }
    }

    $message = self::s_get_message($code, $arguments, $display_error_message, $function_name)->get_value_exact();
    if (is_string($additional_message)) {
      $message .= $additional_message;
    }
    unset($display_error_message);

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

    // replace the reserved ':{error_message}', with the message assigned to the error object.
    $error_message = $error->get_message();
    if (!is_string($error_message)) {
      $error_message = '';
    }

    if ($html) {
      $processed_message = preg_replace('/:{error_message}/i', '<div class="error_message-error_message">' . htmlspecialchars($error_message, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</div>', $message);
    }
    else {
      $processed_message = preg_replace('/:{error_message}/i', $error_message, $message);
    }
    unset($error_message);

    if (is_string($processed_message)) {
      $message = $processed_message;
    }
    unset($processed_message);

    $details = $error->get_details();

    if (isset($details['arguments']) && is_array($details['arguments'])) {
      if ($html) {
        foreach ($details['arguments'] as $detail_name => $detail_value) {
          if (!is_string($detail_value)) {
            $detail_value = '';
          }

          $detail_name_css = 'error_message-argument-' . preg_replace('/[^[:word:]-]/i', '', $detail_name);
          $processed_message = preg_replace('/' . preg_quote($detail_name, '/') . '/i', '<div class="error_message-argument ' . $detail_name_css . '">' . htmlspecialchars($detail_value, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8') . '</div>', $message);
          if (is_string($processed_message)) {
            $message = $processed_message;
          }
        }
        unset($detail_name_css);
        unset($processed_message);
      }
      else {
        foreach ($details['arguments'] as $detail_name => $detail_value) {
          if (!is_string($detail_value)) {
            $detail_value = '';
          }

          $processed_message = preg_replace('/' . preg_quote($detail_name, '/') . '/i', $detail_value, $message);
          if (is_string($processed_message)) {
            $message = $processed_message;
          }
        }
        unset($processed_message);
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
   * @Implements s_get_message().
   */
  static function s_get_message($code, $arguments = TRUE, $error_message = TRUE, $function_name = FALSE) {
    $function_name_prepend = NULL;
    $function_name_string = NULL;
    if ($function_name) {
      $function_name_prepend = ',';
      $function_name_string = ' while calling :{function_name}';
    }

    $error_message_string = NULL;
    if ($error_message) {
      $error_message_string = ', reasons: :{error_message}';
    }

    if ($code === static::INVALID_ARGUMENT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('An invalid argument, :{argument_name}, has been specified' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('An invalid argument has been specified' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::INVALID_FORMAT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The argument, :{format_name}, has an invalid format' . $function_name_string . $error_message_string . '.:{expected_format}');
      }
      else {
        return c_base_return_string::s_new('An invalid format has been specified' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::INVALID_SESSION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The requested session is invalid' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('The requested session is invalid' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::INVALID_VARIABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The variable, :{variable_name}, is invalid' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('An invalid variable has been specified' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::OPERATION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Failed to perform operation, :{operation_name}' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to perform operation' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::OPERATION_UNECESSARY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Did not perform unnecessary operation, :{operation_name}' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Did not perform unnecessary operation' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::FUNCTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The function, :{function_name}, has failed execution' . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('A function has failed execution' . $error_message_string . '.');
      }
    }
    else if ($code === static::NOT_FOUND) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Not found' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Not found' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NOT_FOUND_ARRAY_INDEX) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The index, :{index_name}, was not found in the array, :{array_name}' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to find index within specified array' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NOT_FOUND_FILE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The file, :{file_name}, was not found or cannot be accessed' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('File not found or cannot be accessed' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NOT_FOUND_DIRECTORY) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The directory, :{directory_name}, was not found or cannot be accessed' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('File not found or cannot be accessed' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NOT_FOUND_PATH) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The path, :{path_name}, was not found or cannot be accessed' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Path not found or cannot be accessed' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NOT_DEFINED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The requested data, :{data_name}, is not defined' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('The requested data is not defined' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The resource, :{resource_name}, is not connected' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('The resource is not connected' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::NO_SUPPORT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The functionality, :{functionality_name}, is currently not supported.' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('The requested functionality is not supported' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::POSTGRESQL_CONNECTION_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Failed to connect to the database, :{database_name}' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to connect to the database' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::POSTGRESQL_NO_ACCOUNT) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Database access denied: the account :{database_account} does not exist or does not have the required access' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Database access denied: the account does not exist or does not have the required access' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::POSTGRESQL_NO_CONNECTION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('The database, :{database_name}, is not connected' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('The database is not connected' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::POSTGRESQL_NO_RESOURCE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('No database resource is available' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('No database resource is available' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::POSTGRESQL_ERROR) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Database query error: :{database_error_message}' . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to perform database query' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::SOCKET_FAILURE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Failed to perform socket operation, :{operation_name}, socket error (:{socket_error}) \':{socket_error_message}\'' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Failed to perform socket operation' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::ACCESS_DENIED) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied, :{operation_name}' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::ACCESS_DENIED_UNAVAILABLE) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied due to unavailability, :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied due to unavailability' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::ACCESS_DENIED_USER) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied for user :name_machine_user (:{id_user}), :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied for user' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::ACCESS_DENIED_ADMINISTRATION) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('Access is denied for administrative reasons, :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('Access is denied for administrative reasons' . $function_name_string . $error_message_string . '.');
      }
    }
    else if ($code === static::SERVER_ERROR) {
      if ($arguments === TRUE) {
        return c_base_return_string::s_new('A server error has occurred, :{operation_name} ' . $function_name_prepend . $function_name_string . $error_message_string . '.');
      }
      else {
        return c_base_return_string::s_new('A server error has occurred' . $function_name_string . $error_message_string . '.');
      }
    }

    return c_base_return_string::s_new('');
  }
}
