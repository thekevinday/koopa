<?php
/**
 * @file
 * Provides a class for provided global defaults.
 *
 * This is not indended to be included by default and is not included by default.
 *
 * The purpose here is to grant project developers full control over defaults through a single file.
 * All php source files in this project should be assumed to depend on this.
 *
 * However, no files in this project should include this file manually.
 * Instead, the caller must include this in their index.php (or equivalent) before including any other files.
 * This should grant the caller control over which file to select.
 *
 * It is recommended to set PHP precision setting to at least 16 for microtime values.
 */
namespace n_koopa;

/**
 * A collection of global settings for use by the entire project.
 *
 * This is intended to be modified by the developers or site developers of a project.
 *
 * Warning: Any variables defined here (due to being global) must be considered non-thread-safe.
 *          Be sure to handle carefully when using threads.
 *          It is recommended to process and pre-set as much of this as possible before starting threads.
 */
class c_base_defaults_global {
  // set to NULL for auto, TRUE to always enable backtrace on error, and FALSE to always disable backtrace on error.
  const ERROR_BACKTRACE_ALWAYS = NULL;

  // set to NULL for auto, TRUE to enable backtrace for parameter-type errors, and FALSE to disable.
  // this is overriden when ERROR_BACKTRACE_ALWAYS is not NULL.
  const ERROR_BACKTRACE_ARGUMENTS = FALSE;

  // provide a language to fallback to if none is set.
  const LANGUAGE_CLASS_DEFAULT = '\n_koopa\c_base_languages_us_only';

  // provide an error message handler to fallback to if none is set.
  const ERROR_MESSAGE_HANDLER_CLASS_DEFAULT = '\n_koopa\c_base_error_messages_english';

  // provide the include path for the default/fallback error message handler class.
  const ERROR_MESSAGE_HANDLER_PATH_DEFAULT = 'common/base/classes/base_error_messages_english.php';

  // reserved path groups: [97, 99, 100, 102, 109, 115, 116, 120, 121].
  const RESERVED_PATH_GROUP = [c_base_ascii::LOWER_A, c_base_ascii::LOWER_C, c_base_ascii::LOWER_D, c_base_ascii::LOWER_F, c_base_ascii::LOWER_M, c_base_ascii::LOWER_S, c_base_ascii::LOWER_T, c_base_ascii::LOWER_U, c_base_ascii::LOWER_X];

  // default log facility (17 = c_base_error::FACILITY_LOCAL_0).
  const LOG_FACILITY = 17;

  // default backtrace setting (TRUE = perform backtrace on error, FALSE do not perform backtrace on error).
  const BACKTRACE_PERFORM = TRUE;

  // a machine-friendly date and time format string.
  const FORMAT_DATE_MACHINE = 'Y/m/d';

  // a machine-friendly date and time format string.
  const FORMAT_DATE_TIME_MACHINE = 'Y/m/d h:i a';

  // a machine-friendly date and time format string, with seconds.
  const FORMAT_DATE_TIME_SECONDS_MACHINE = 'Y/m/d h:i:s a';

  // a human-friendly date and time format string.
  const FORMAT_DATE_HUMAN = 'l, F jS Y';

  // a human-friendly date and time format string.
  const FORMAT_DATE_TIME_HUMAN = 'l, F jS Y h:ia';

  // a human-friendly date and time format string, with seconds.
  const FORMAT_DATE_TIME_SECONDS_HUMAN = 'l, F jS Y h:i:sa';

  // a machine-friendly time format string.
  const FORMAT_TIME_MACHINE = 'h:i a';

  // a machine-friendly time format string, with seconds.
  const FORMAT_TIME_SECONDS_MACHINE = 'h:i:s a';

  // a human-friendly time format string.
  const FORMAT_TIME_HUMAN = 'h:i a';

  // a human-friendly time format string, with seconds.
  const FORMAT_TIME_SECONDS_HUMAN = 'h:i:s a';


  // Represents the current timestamp of this PHP process/session, see: self::s_get_timestamp_session().
  private static $s_timestamp_session = NULL;

  // Represents the default timezone in use by this project.
  // This value is not used for timestamps, instead, all timestamps are processed as UTC to prevent issues.
  // When NULL, the default is assumed to be UTC.
  private static $s_timezone = NULL;

  // Represents the default language class in use.
  // This must be a class that implements: i_base_languages.
  // In most cases, this should be expected to be defined.
  private static $s_languages = NULL;

  // Represents a language-specific error message handler static class.
  // This assists in providing language-specific error messages.
  private static $s_error_message_handler = NULL;


  /**
   * Set the default timezone.
   *
   * @param string $timezone
   *   The timezone string.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public static function s_set_timezone($timezone) {
    if (!($timezone instanceof i_base_languages)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'timezone', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    self::$s_timezone = $timezone;
    return new c_base_return_true();
  }

  /**
   * Set the default language.
   *
   * @param i_base_languages $languages
   *   Must be a class that implements i_base_languages.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: i_base_languages()
   */
  public static function s_set_languages($languages) {
    if (!($languages instanceof i_base_languages)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'languages', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    self::$s_languages = $languages;
    return new c_base_return_true();
  }

  /**
   * Assign an error message handler.
   *
   * @param i_base_error_messages $error_message_handler
   *   An error message handler to assign.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: i_base_error_messages
   */
  public static function s_set_error_message_handler($error_message_handler) {
    if (!($error_message_handler instanceof i_base_error_messages)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'languages', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    self::$s_error_message_handler = $error_message_handler;
    return new c_base_return_true();
  }

  /**
   * Get a date string, relative to UTC, with support for milliseconds and microseconds.
   *
   * Use this in place of date() to ensure consistent timestamps in UTC format, with microseconds.
   *
   * To support specific locales, strftime() should still be used.
   *
   * @param string $string
   *   The date string to get the timestamp of.
   * @param int|float|null $timestamp
   *   (optional) If not NULL, a unix timestamp representing the timestamp to get the date string of.
   *   If NULL, the current session time is used.
   *   Timestamp is expected to be in UTC.
   *
   * @return c_base_return_string
   *   A date and time string in the format specified by $string.
   *   An empty string with the error bit set is returned on error.
   *
   * @see: date()
   * @see: strftime()
   */
  public static function s_get_date($string, $timestamp = NULL) {
    if (!is_string($string)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'string', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (!is_null($timestamp) && !is_float($timestamp) && !is_int($timestamp)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'timestamp', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (!is_null(self::$s_timezone)) {
      date_default_timezone_set('UTC');
    }

    // To ensure support for microseconds (and milliseconds), datetime must be initialized woth microseconds.
    if (is_null($timestamp)) {
      $now = self::s_get_timestamp_session()->get_value_exact();
      $microseconds = (int) (($now - ((int) $now)) * 1000000);

      $date = new \DateTime(date('Y/m/d h:i:s', (int) $now) . '.' . $microseconds . date(' P', (int) $now));
      unset($now);
    }
    else {
      if (is_float($timestamp)) {
        $microseconds = (int) (($timestamp - ((int) $timestamp)) * 1000000);
      }
      else {
        $microseconds = 0;
      }

      $date = new \DateTime(date('Y/m/d h:i:s', (int) $timestamp) . '.' . $microseconds . date(' P', (int) $timestamp));
    }
    unset($microseconds);

    if (!($date instanceof \DateTime)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'date', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (!is_null(self::$s_timezone)) {
      date_default_timezone_set(self::$s_timezone);
      $date->setTimeZone(new \DateTimeZone(self::$s_timezone));
    }

    $formatted = $date->format($string);
    if (!is_string($formatted)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'date->format', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    return c_base_return_string::s_new($formatted);
  }

  /**
   * Get a timestamp, relative to UTC, with support for milliseconds and microseconds.
   *
   * Use this in place of strtotime() to ensure consistent timestamps in UTC format, with microseconds.
   * To ensure proper support for microseconds (and milliseconds), both $string and $format must contain microseconds, even if it is set to 0.
   *
   * @param string $string
   *   The time string to get the timestamp of.
   * @param string $format
   *   (optional) The format the $string is structured as.
   *
   * @return c_base_return_float
   *   A timestamp in floating format.
   *   0.0 with error bit set is returned on error.
   *
   * @see: strtotime()
   */
  public static function s_get_timestamp($string, $format = 'Y/m/d h:i:s.u P') {
    if (!is_string($string)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'string', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    if (!is_null(self::$s_timezone)) {
      date_default_timezone_set('UTC');
    }

    // To ensure support for microseconds (and milliseconds), datetime must be initialized woth microseconds.
    $date = \DateTime::createFromFormat($format, $string);

    if (!($date instanceof \DateTime)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'date', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    $resulting_timestamp = $date->getTimestamp();
    if (!is_int($resulting_timestamp) && !is_float($resulting_timestamp)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{operation_name}' => 'date->get_timestamp', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    $resulting_timestamp = (float) $resulting_timestamp + (float) $date->format('0.u');

    if (!is_null(self::$s_timezone)) {
      date_default_timezone_set(self::$s_timezone);
    }

    return c_base_return_float::s_new($resulting_timestamp);
  }

  /**
   * Get the current microtime, relative to UTC.
   *
   * @return float
   *   A timestamp in floating format.
   */
  public static function s_get_timestamp_current() {
    if (is_null(self::$s_timezone)) {
      // this assumes UTC, when timezone is NULL.
      return c_base_return_float::s_new(microtime(TRUE));
    }

    date_default_timezone_set('UTC');

    $now = microtime(TRUE);
    date_default_timezone_set(self::$s_timezone);

    return c_base_return_float::s_new($now);
  }

  /**
   * Get the current timestamp of the session, relative to UTC.
   *
   * @param bool $use_request_time
   *   (optional) Set to TRUE to attempt to use REQUEST_TIME_FLOAT or REQUEST_TIME where available.
   *   Set to FALSE to use microtime()
   *   The REQUEST_TIME_FLOAT and REQUEST_TIME, the timestamp cannot be guaranteed to be relative to UTC.
   *   It is recommended to always set this to FALSE.
   *
   * @return float|int
   *   A timestamp in floating format (for higher precision), where possible.
   *   Otherwise a timestamp in integer format is returned.
   */
  public static function s_get_timestamp_session($use_request_time = FALSE) {
    if (is_null(self::$s_timestamp_session)) {
      if (isset($_SERVER['REQUEST_TIME_FLOAT']) && is_float($_SERVER['REQUEST_TIME_FLOAT'])) {
        // find and process potentially useful additional environment variables.
        if (array_key_exists('REQUEST_TIME_FLOAT', $_SERVER)) {
          self::$s_timestamp_session = $_SERVER['REQUEST_TIME_FLOAT'];
        }
        else if (array_key_exists('REQUEST_TIME', $_SERVER)) {
          self::$s_timestamp_session = $_SERVER['REQUEST_TIME'];
        }
      }

      if (is_null(self::$s_timestamp_session)) {
        self::$s_timestamp_session = self::s_get_timestamp_current();
      }
    }

    if (is_float(self::$s_timestamp_session)) {
      return c_base_return_float::s_new(self::$s_timestamp_session);
    }

    return c_base_return_int::s_new(self::$s_timestamp_session);
  }

  /**
   * Get the currently assigned language class.
   *
   * @return i_base_languages
   *   A class that implements i_base_languages.
   *
   * @see: i_base_languages
   */
  public static function s_get_languages() {
    if (is_null(self::$s_languages)) {
      $class = self::LANGUAGE_CLASS_DEFAULT;
      self::$s_languages = new $class();
      unset($class);
    }

    return self::$s_languages;
  }

  /**
   * Get the name of the currently assigned language class.
   *
   * @return string
   *   A string representing the class of the language.
   *
   * @see: i_base_languages
   */
  public static function s_get_languages_class() {
    if (is_null(self::$s_languages)) {
      $class = self::LANGUAGE_CLASS_DEFAULT;
      self::$s_languages = new $class();
      return c_base_return_string::s_new($class);
    }

    $class = get_class(self::$s_languages);
    return c_base_return_string::s_new($class);
  }

  /**
   * Get the currently assigned error message handler class.
   *
   * @return i_base_error_messages
   *   A class that implements i_base_error_messages.
   *
   * @see: i_base_error_messages
   */
  public static function s_get_error_message_handler() {
    if (is_null(self::$s_error_message_handler)) {
      require_once(self::ERROR_MESSAGE_HANDLER_PATH_DEFAULT);

      $class = self::ERROR_MESSAGE_HANDLER_CLASS_DEFAULT;
      self::$s_error_message_handler = new $class();
      unset($class);
    }

    return self::$s_error_message_handler;
  }

  /**
   * Get the assigned error message handler.
   *
   * @return i_base_error_messages
   *   The assigned error message or NULL if unassigned.
   *
   * @see: i_base_error_messages
   */
  public static function s_get_error_message_handler_class() {
    if (is_null(self::$s_error_message_handler)) {
      require_once(self::ERROR_MESSAGE_HANDLER_PATH_DEFAULT);

      $class = self::ERROR_MESSAGE_HANDLER_CLASS_DEFAULT;
      self::$s_error_message_handler = new $class();
      return c_base_return_string::s_new($class);
    }

    $class = get_class(self::$s_error_message_handler);
    return c_base_return_string::s_new($class);
  }
}
