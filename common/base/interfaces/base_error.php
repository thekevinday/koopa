<?php
/**
 * @file
 * Provides interfaces for managing return values.
 */
namespace n_koopa;

/**
 * A generic interface for providing basic error messages.
 *
 * This is for generating common error messages.
 *
 * @warning: this will be constantly updated and reogranized as the project is being developed.
 *           it is expected that the number of codes will get very large.
 *           expect major changes.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
interface i_base_error_messages {
  const NONE                          = 0;
  const INVALID_ARGUMENT              = 1;
  const INVALID_FORMAT                = 2;
  const INVALID_SESSION               = 3;
  const INVALID_VARIABLE              = 4;
  const OPERATION_FAILURE             = 5;
  const OPERATION_UNECESSARY          = 6;
  const FUNCTION_FAILURE              = 7;
  const NOT_FOUND                     = 8;
  const NOT_FOUND_ARRAY_INDEX         = 9;
  const NOT_FOUND_DIRECTORY           = 10;
  const NOT_FOUND_FILE                = 11;
  const NOT_FOUND_PATH                = 12;
  const NOT_DEFINED                   = 13;
  const NO_CONNECTION                 = 14;
  const NO_SESSION                    = 15;
  const NO_SUPPORT                    = 16;
  const POSTGRESQL_CONNECTION_FAILURE = 17;
  const POSTGRESQL_NO_ACCOUNT         = 18;
  const POSTGRESQL_NO_CONNECTION      = 19;
  const POSTGRESQL_NO_RESOURCE        = 20;
  const POSTGRESQL_ERROR              = 21;
  const SOCKET_FAILURE                = 22;
  const ACCESS_DENIED                 = 23;
  const ACCESS_DENIED_UNAVAILABLE     = 24;
  const ACCESS_DENIED_USER            = 25;
  const ACCESS_DENIED_ADMINISTRATION  = 26;
  const SERVER_ERROR                  = 27;


  /**
   * Converts a given error message into a processed string.
   *
   * @param c_base_error $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are added.
   *   All placeholders should begin with a single colon ':' and be wrapped within '{}', such that 'example' placeholder is ':{example}'.
   * @param bool $error_message
   *   (optional) When TRUE, a reserved ':{error_message}' placeholder is added.
   *   This placeholder is processed independent of the $arguments parameter.
   *   When FALSE, the reserved placeholder is not added.
   *   If NULL, then error_message is auto-added depending on the existance of an attached error message.
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
  static function s_render_error_message($error, $arguments = TRUE, $error_message = NULL, $function_name = FALSE, $additional_message = NULL, $html = TRUE);

  /**
   * Returns a standard error message associated with the given code.
   *
   * @param int $code
   *   The error message code.
   * @param bool $arguments
   *   (optional) When TRUE, argument placeholders are added.
   *   When FALSE, no placeholders are added.
   *   All placeholders should begin with a single colon ':' and be wrapped within '{}', such that 'example' placeholder is ':{example}'.
   * @param bool $error_message
   *   (optional) When TRUE, a reserved ':{error_message}' placeholder is added.
   *   This placeholder is processed independent of the $arguments parameter.
   *   When FALSE, the reserved placeholder is not added.
   * @param bool $function_name
   *   (optional) When TRUE, the function name is included with the message.
   *   When FALSE, no funciton name is provided.
   *
   * @return string
   *   An error message associated with the error code.
   *   An empty sting is returned for unsupported or unknown codes.
   */
  static function s_get_message($code, $arguments = TRUE, $error_message = TRUE, $function_name = FALSE);
}
