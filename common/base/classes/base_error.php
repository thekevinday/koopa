<?php
/**
 * @file
 * Provides classes for managing return values.
 */
namespace n_koopa;

require_once('common/base/interfaces/base_error.php');

/**
 * A generic class for managing errors.
 *
 * This class is a dependency of classes provided by base_return.php.
 * Therefore, it is an exception case to the use of base_return classes as a return value and must instead return raw/native PHP values.
 *
 * This provides a custom facility for syslog/openlog calls so that a 'none' facility can be supported.
 */
class c_base_error {
  const SEVERITY_NONE          = 0;
  const SEVERITY_EMERGENCY     = 1;
  const SEVERITY_ALERT         = 2;
  const SEVERITY_CRITICAL      = 3;
  const SEVERITY_ERROR         = 4;
  const SEVERITY_WARNING       = 5;
  const SEVERITY_NOTICE        = 6;
  const SEVERITY_INFORMATIONAL = 7;
  const SEVERITY_DEBUG         = 8;
  const SEVERITY_UNKNOWN       = 9;

  const FACILITY_NONE          = 0;
  const FACILITY_KERNEL        = 1;
  const FACILITY_USER          = 2;
  const FACILITY_MAIL          = 3;
  const FACILITY_DAEMON        = 4;
  const FACILITY_SECURITY      = 5;
  const FACILITY_MESSAGES      = 6;
  const FACILITY_PRINTER       = 7;
  const FACILITY_NETWORK       = 8;
  const FACILITY_UUCP          = 9;
  const FACILITY_CLOCK         = 10;
  const FACILITY_AUTHORIZATION = 11;
  const FACILITY_FTP           = 12;
  const FACILITY_NTP           = 13;
  const FACILITY_AUDIT         = 14;
  const FACILITY_ALERT         = 15;
  const FACILITY_CRON          = 16;
  const FACILITY_LOCAL_0       = 17;
  const FACILITY_LOCAL_1       = 18;
  const FACILITY_LOCAL_2       = 19;
  const FACILITY_LOCAL_3       = 20;
  const FACILITY_LOCAL_4       = 21;
  const FACILITY_LOCAL_5       = 22;
  const FACILITY_LOCAL_6       = 23;
  const FACILITY_LOCAL_7       = 24;

  const DEFAULT_BACKTRACE_LIMIT = 4;

  private $message;
  private $details;
  private $severity;
  private $limit;
  private $recovered;

  private $backtrace;
  private $backtrace_perform;
  private $ignore_arguments;

  private $code;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->message   = NULL;
    $this->details   = NULL;
    $this->severity  = NULL;
    $this->limit     = self::DEFAULT_BACKTRACE_LIMIT;
    $this->recovered = FALSE;

    $this->backtrace         = [];
    $this->backtrace_perform = FALSE;
    $this->ignore_arguments  = TRUE;

    $this->code = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->message);
    unset($this->details);
    unset($this->severity);
    unset($this->limit);
    unset($this->recovered);

    unset($this->backtrace);
    unset($this->backtrace_perform);
    unset($this->ignore_arguments);

    unset($this->code);
  }

  /**
   * Write an error log message and returns the resulting new error object.
   *
   * This will silently ignore invalid arguments, with the exception of the reporting controls.
   * For reporting information, use self::get_reporting().
   *
   * @param string|null $message
   *   (optional) A message string describing the problem, in some manner.
   * @param array|null $details
   *   (optional) An array containing further, detailed, information.
   * @param int|null $code
   *   (optional) An integer identifying the message in some manner.
   * @param int|null $severity
   *   (optional) A number representing the severity level.
   *   The c_base_error constants, such as SEVERITY_SEVERITY_EMERGENCY or SEVERITY_SEVERITY_ERROR should be use here.
   * @param int|bool|null $limit
   *   (optional) A number representing the backtrace limit.
   *   If set to FALSE, then no backtrace is generated.
   * @param bool $recovered
   *   (optional) If TRUE, then this designates that the error was recovered from.
   *   If FALSE, then the error has not been recovered from.
   *
   * @return c_base_error
   *   Always returns a newly created c_base_error object.
   *   No error status is ever returned.
   */
  public static function s_log($message = NULL, $details = NULL, $code = NULL, $severity = NULL, $limit = NULL, $recovered = FALSE) {
    $class = __CLASS__;
    $entry = new $class();
    unset($class);

    if (is_string($message)) {
      $entry->set_message($message);
    }
    else if (is_null($message)) {
      $entry->set_message('');
    }

    if (is_array($details)) {
      $entry->set_details($details);
    }
    else if (is_null($details)) {
      $entry->set_details([]);
    }

    if (is_int($code)) {
      $entry->set_code($code);
    }
    else if (is_null($code)) {
      $entry->set_code(0);
    }

    if (is_int($severity) && $severity >= static::SEVERITY_EMERGENCY && $severity < static::SEVERITY_UNKNOWN) {
      $entry->set_severity($severity);
    }
    else if (is_null($severity)) {
      $entry->set_severity(static::SEVERITY_ERROR);
    }

    if (is_int($limit) && $limit >= 0) {
      $entry->set_limit($limit);
    }

    if (is_bool($recovered)) {
      $entry->set_recovered($recovered);
    }

    // build the backtrace, but ignore this function call when generating.
    $entry->set_backtrace(1);

    return $entry;
  }

  /**
   * Assign an error message string.
   *
   * @param string $message
   *   An error message string
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_message($message) {
    if (!is_string($message)) {
      return FALSE;
    }

    $this->message = $message;
    return TRUE;
  }

  /**
   * Assigns the details array.
   *
   * The details array is defined by the caller and may have any structure, so long as it is an array.
   *
   * @param array $details
   *   An array of details.
   *
   * @param bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_details($details) {
    if (!is_array($details)) {
      return FALSE;
    }

    $this->details = $details;
    return TRUE;
  }

  /**
   * Assigns a severity level.
   *
   * @param int $severity
   *   A severity integer, representing the severity level.
   *   Such as self::SEVERITY_ERROR.
   */
  public function set_severity($severity) {
    if (!is_int($severity) || $severity < 0 || $severity > 7) {
      return FALSE;
    }

    $this->severity = $severity;
    return TRUE;
  }

  /**
   * Assigns a limit for use with debug_backtrace().
   *
   * @param int|bool $limit
   *   An integer representing the number of backtraces.
   *   A value of 0 means no limit (use with care, can result in performance/resource/timeout problems).
   *   A value of FALSE means disable backtrace for errors.
   *
   * @see: c_base_error::set_backtrace()
   */
  public function set_limit($limit) {
    if ($limit !== FALSE && (!is_int($limit) || $limit < 0)) {
      return FALSE;
    }

    $this->limit = $limit;
    return TRUE;
  }

  /**
   * Assigns a backtrace.
   *
   * This is auto-performed by the class.
   * All settings should be assigned prior to utilizing this.
   *
   * @param int|false|null $count
   *   (optional) assign a backtrace ignore account.
   *   This is useful for when you have other debugging functions being called prior to this that should not appear in the backtrace.
   *   This function auto-adds 1 to account for this function call to this value.
   *   If set to FALSE, the backtrace will be reset to an empty array.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_error::set_limit()
   */
  public function set_backtrace($count = NULL) {
    if (is_null($count)) {
      $this->p_backtrace(1);
    }
    else if (is_int($count)) {
      $this->p_backtrace($count + 1);
    }
    else if ($count === FALSE) {
      $this->backtrace = [];
    }
    else {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Assign the backtrace perform boolean.
   *
   * @param bool $backtrace_perform
   *   The backtrace perform boolean.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_backtrace_perform($backtrace_perform) {
    if (!is_bool($backtrace_perform)) {
      return FALSE;
    }

    $this->backtrace_perform = $backtrace_perform;
    return TRUE;
  }

  /**
   * Assign an error ignore arguments boolean.
   *
   * @param bool $ignore_arguments
   *   The ignore arguments boolean.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_ignore_arguments($ignore_arguments) {
    if (!is_bool($ignore_arguments)) {
      return FALSE;
    }

    $this->ignore_arguments = $ignore_arguments;
    return TRUE;
  }

  /**
   * Assign an error code integer.
   *
   * A code is used to categorize the error in some manner.
   *
   * @param int $code
   *   An error code integer
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_code($code) {
    if (!is_int($code)) {
      return FALSE;
    }

    $this->code = $code;
    return TRUE;
  }

  /**
   * Assigns a reported.
   *
   * The reported variable is auto-processed by this class.
   * All this does is reset the value to NULL.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_error::set_limit()
   */
  public function set_reported() {
    unset($this->reported);

    $this->reported = NULL;

    return TRUE;
  }

  /**
   * Assign an error recovered boolean.
   *
   * @param bool $recovered
   *   The recovered boolean.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_recovered($recovered) {
    if (!is_bool($recovered)) {
      return FALSE;
    }

    $this->recovered = $recovered;
    return TRUE;
  }

  /**
   * Returns the assigned message.
   *
   * @return string|null
   *   An error message string or NULL if not defined.
   */
  public function get_message() {
    return $this->message;
  }

  /**
   * Returns the details array.
   *
   * The details array is defined by the caller and may have any structure, so long as it is an array.
   *
   * @return array|null
   *   An array of additional details or NULL if not defined.
   */
  public function get_details() {
    return $this->details;
  }

  /**
   * Returns the currently assigned severity level.
   *
   * @return int
   *   The currently assigned severity level.
   *   This defaults to self::SEVERITY_ERROR when undefined.
   */
  public function get_severity() {
    if (is_null($this->severity)) {
      $this->severity = static::SEVERITY_ERROR;
    }

    return $this->severity;
  }

  /**
   * Returns the currently assigned limit.
   *
   * @return int|bool
   *   The currently assigned limit integer.
   *   FALSE is returned if backtracing is disabled.
   *   This defaults to self::DEFAULT_BACKTRACE_LIMIT.
   *
   * @see: c_base_error::set_backtrace()
   */
  public function get_limit() {
    if ($limit !== FALSE && (!is_int($limit) || $limit < 0)) {
      $this->limit = static::DEFAULT_BACKTRACE_LIMIT;
    }

    return $this->limit;
  }

  /**
   * Returns the backtrace object.
   *
   * @return array|null
   *   A populate backtrace array of objects or NULL if no backtrace is defined.
   *
   * @see: c_base_error::set_limit()
   */
  public function get_backtrace() {
    return $this->backtrace;
  }

  /**
   * Returns the assigned backtrace perform boolean.
   *
   * @return bool|null
   *   A boolean representing whether or not to the backtrace should be performed.
   *   NULL is returned if not defined.
   */
  public function get_backtrace_perform() {
    return $this->backtrace_perform;
  }

  /**
   * Returns the assigned ignore arguments boolean.
   *
   * @return bool|null
   *   A boolean representing whether or not to get the arguments when building the backtrace.
   *   NULL is returned if not defined.
   */
  public function get_ignore_arguments() {
    return $this->ignore_arguments;
  }

  /**
   * Returns the assigned code.
   *
   * A code is used to categorize the error in some manner.
   *
   * @return int|null
   *   A code to associate with the error.
   *   NULL is returned if not defined.
   */
  public function get_code() {
    return $this->code;
  }

  /**
   * Returns the reported object.
   *
   * Use this to determine the results of the last report status.
   *
   * @return object|null
   *   A populate reported object or NULL if no report was performed.
   *
   * @see: c_base_error::set_limit()
   */
  public function get_reported() {
    return $this->reported;
  }

  /**
   * Returns the recovered setting.
   *
   * @return bool|null
   *   The recovered boolean or NULL if not assigned.
   */
  public function get_recovered() {
    return $this->recovered;
  }

  /**
   * Build the debug backtrace.
   *
   * This will not include this function in the backtrace.
   * The backtrace will be stored as an object.
   *
   * @param int $count
   *   (optional) Assign a custom count that is used to prevent unnecessary function calls from being included in the backtrace.
   *   This is essentially to remove the functions called to generate this backtrace, which do not matter.
   *   Instead, only the function where the error happens should the backtrace limit count apply.
   *   This does nothing when the limit is set to 0 (which means unlimited).
   *
   * @see: debug_backtrace()
   */
  private function p_backtrace($count = 0) {
    if (!c_base_defaults_global::BACKTRACE_PERFORM || !$this->backtrace_perform) {
      return;
    }

    $this->backtrace = [];

    // when limit is set to FALSE, backtrace is disabled.
    if ($this->limit === FALSE) {
      return;
    }

    if (is_null($this->limit)) {
      $this->limit = static::DEFAULT_BACKTRACE_LIMIT;
    }

    // Make sure unnecessary backtrace logs are not part of the count.
    $limit = $this->limit;
    if ($limit > 0) {
      $limit += $count;
    }

    if ($this->ignore_arguments) {
      $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }
    else {
      $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
    }
    unset($limit);

    if (is_array($backtrace)) {
      // Remove the call to the debug_backtrace() from the backtrace log and this function call.
      $total = $count + 2;
      $i = 0;
      for (; $i < $total; $i++) {
        array_shift($backtrace);
      }
      unset($i);
      unset($total);

      $this->backtrace = $backtrace;
    }
    unset($backtrace);

    // do not perform this backtrace multiple times.
    $this->backtrace_perform = TRUE;
  }
}
