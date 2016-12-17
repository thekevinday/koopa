<?php
/**
 * @file
 * Provides a class for managing return values.
 */

/*
  const KERNEL = 0;
  const USER = 1;
  const MAIL = 2;
  const DAEMON = 3;
  const SECURITY = 4;
  const MESSAGES = 5;
  const PRINTER = 6;
  const NETWORK = 7;
  const UUCP = 8;
  const CLOCK = 9;
  const AUTHORIZATION = 10;
  const FTP = 11;
  const NTP = 12;
  const AUDIT = 13;
  const ALERT = 14;
  const CRON = 15;
  const LOCAL_0 = 16;
  const LOCAL_1 = 17;
  const LOCAL_2 = 18;
  const LOCAL_3 = 19;
  const LOCAL_4 = 20;
  const LOCAL_5 = 21;
  const LOCAL_6 = 22;
  const LOCAL_7 = 23;
*/

/**
 * A generic class for managing errors.
 *
 * This class is a dependency of classes provided by base_return.php.
 * Therefore, it is an exception case to the use of base_return classes as a return value.
 *
 * @todo: write this based on my cf_error code.
 */
class c_base_error {
  const EMERGENCY = 0;
  const ALERT = 1;
  const CRITICAL = 2;
  const ERROR = 3;
  const WARNING = 4;
  const NOTICE = 5;
  const INFORMATIONAL = 6;
  const DEBUG = 7;
  const UNKNOWN = 8;

  const DEFAULT_BACKTRACE_LIMIT = 4;

  private $name;
  private $message;
  private $details;
  private $severity;
  private $limit;
  private $backtrace;
  private $code;
  private $reported;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->name = NULL;
    $this->message = NULL;
    $this->details = NULL;
    $this->severity = NULL;
    $this->limit = NULL;
    $this->backtrace = NULL;
    $this->code = NULL;
    $this->reported = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->message);
    unset($this->details);
    unset($this->severity);
    unset($this->limit);
    unset($this->backtrace);
    unset($this->code);
    unset($this->reported);
  }

  /**
   * Write an error log message and returns the resulting new error object.
   *
   * This will silently ignore invalid arguments, with the exception of the reporting controls.
   * For reporting information, use self::get_reporting().
   *
   * @todo: this is incomplete.
   *
   * @param string|null $name
   *   (optional) The name of something associated with a problem.
   *   This is often a variable name or a function name.
   * @param string|null $message
   *   (optional) A message string describing the problem, in some manner.
   * @param array|null $details
   *   (optional) An array containing further, detailed, information.
   * @param int|null $code
   *   (optional) An integer identifying the message in some manner.
   * @param int|null $severity
   *   (optional) A number representing the severity level.
   *   The c_base_error constants, such as EMERGENCY or ERROR should be use here.
   *   This defaults to: self::ERROR.
   * @param int|bool|null $limit
   *   (optional) A number representing the backtrace limit.
   *   If set to FALSE, then no backtrace is generated.
   * @param bool $report
   *   (optional) If TRUE, then report the error using the appropriate methods.
   *   @fixme: it would probably be best to make this an object that knows how to report so that the object can do what it needs to do.
   *
   * @return c_base_error
   *   Always returns a newly created c_base_error object.
   *   No error status is ever returned.
   *
   * @see: self::get_reporting()
   */
  public static function s_log($name = NULL, $message = NULL, $details = NULL, $code = NULL, $severity = NULL, $limit = NULL, $report = TRUE) {
    $class = __CLASS__;
    $entry = new $class();
    unset($class);

    if (is_string($name)) {
      $entry->set_name($name);
    }
    elseif (is_null($this->name)) {
      $entry->set_name('');
    }

    if (is_string($message)) {
      $entry->set_message($message);
    }
    elseif (is_null($this->message)) {
      $entry->set_message('');
    }

    if (is_array($details)) {
      $entry->set_details($details);
    }
    elseif (is_null($this->details)) {
      $entry->set_details(array());
    }

    if (is_int($code)) {
      $entry->set_code($code);
    }
    elseif (is_null($this->message)) {
      $entry->set_code(0);
    }

    if (is_int($severity) && $severity >= self::EMERGENCY && $severity < self::UNKNOWN) {
      $entry->set_severity($severity);
    }
    elseif (is_null($this->message)) {
      $entry->set_severity(self::ERROR);
    }

    if ($limit === FALSE || (is_int($limit) && $limit >= 0)) {
      $entry->set_limit($limit);
    }
    elseif (is_null($this->limit)) {
      $entry->set_limit(self::DEFAULT_BACKTRACE_LIMIT);
    }

    // @todo: call self::p_backtrace() accordingly.

    if (is_bool($report) && $report) {
      // @todo: use the report object to report the problem.
      // @fixme: this is setup as a bool, but I know I need to create and use a report object (which has yet to be created).
      $this->reported = NULL;
    }
    else {
      $this->reported = NULL;
    }

    return $entry;
  }

  /**
   * Assign an error name string.
   *
   * @param string $name
   *   An error name string
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_name($name) {
    if (!is_string($name)) {
      return FALSE;
    }

    $this->name = $name;
  }

  /**
   * Returns the assigned name.
   *
   * @return string|null
   *   A name to associate with the error, such as a variable or function name.
   *   NULL is returned if not defined.
   */
  public function get_name() {
    return $this->name;
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
   * Assigns the details array.
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
   * Assigns a severity level.
   *
   * @param int $severity
   *   A severity integer, representing the severity level.
   *   Such as self::ERROR.
   */
  public function set_severity($severity) {
    if (!is_int($severity) || $severity < 0 || $severity > 7) {
      return FALSE;
    }

    $this->severity = $severity;
    return TRUE;
  }

  /**
   * Returns the currently assigned severity level.
   *
   * @return int
   *   The currently assigned severity level.
   *   This defaults to self::ERROR when undefined.
   */
  public function get_severity() {
    if (is_null($this->severity)) {
      $this->severity = self::ERROR;
    }

    return $this->severity;
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
    if ($limit !== FALSE || !is_int($limit) || $limit < 0) {
      return FALSE;
    }

    $this->limit = $limit;
    return TRUE;
  }

  /**
   * Returns the currently assigned limit.
   *
   * @return int|bool
   *   The currently assigned limit.
   *   This defaults to self::DEFAULT_BACKTRACE_LIMIT.
   *
   * @see: c_base_error::set_backtrace()
   */
  public function get_limit() {
    if ($limit !== FALSE || !is_int($limit) || $limit < 0) {
      $this->limit = self::DEFAULT_BACKTRACE_LIMIT;
    }

    return $this->limit;
  }

  /**
   * Assigns a backtrace.
   *
   * This is auto-performed by the class.
   * All settings should be assigned prior to utilizing this.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *
   * @see: c_base_error::set_limit()
   */
  public function set_backtrace() {
    $this->p_backtrace(1);

    return TRUE;
  }

  /**
   * Returns the backtrace object.
   *
   * @return object|null
   *   A populate backtrace object or NULL if no backtrace is defined.
   *
   * @see: c_base_error::set_limit()
   */
  public function get_backtrace() {
    return $this->backtrace;
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
    if (!is_string($code)) {
      return FALSE;
    }

    $this->code = $code;
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
   * Build the debug backtrace.
   *
   * This will not include this function in the backtrace.
   * The backtrace will be stored as an object.
   *
   * @param int $count
   *   (optional) Assign a custom count that is used to prevent unecessary function calls from being included in the backtrace.
   *   This is essentially to remove the functions called to generate this backtrace, which do not matter.
   *   Instead, only the function where the error happens should the backtrace limit count apply.
   *   This does nothing when the limit is set to 0 (which means unlimited).
   *
   * @see: debug_backtrace()
   */
  private function p_backtrace($count = 0) {
    if ($this->limit === FALSE) {
      $this->backtrace = NULL;
      return;
    }

    // Make sure unnecessary backtrace logs are not part of the count.
    $limit = $this->limit;
    if ($this->limit > 0) {
      $limit = $this->limit + 1;

      if (is_int($count) && $count > 0) {
        $limit += $count;
      }
    }

    $this->backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
    unset($limit);

    // Remove unecessary backtrace logs.
    $count = $count + 1;
    $i = 0;
    for (; $i < $count; $i++) {
      array_shift($this->backtrace);
    }
    unset($i);
  }
}
