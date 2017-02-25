<?php
/**
 * @file
 * Provides a class for performing debugging.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for performing debugging.
 */
class c_base_debug extends c_base_return {
  private static $ps_debugging = FALSE;

  private $time_start;
  private $time_stop;

  private $memory_usage_start;
  private $memory_usage_stop;
  private $memory_usage_peak;
  private $memory_allocated_start;
  private $memory_allocated_stop;
  private $memory_allocated_peak;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->time_start = NULL;
    $this->time_stop = NULL;

    $this->memory_usage_start = NULL;
    $this->memory_usage_stop = NULL;
    $this->memory_usage_peak = NULL;
    $this->memory_allocated_start = NULL;
    $this->memory_allocated_stop = NULL;
    $this->memory_allocated_peak = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->time_start);
    unset($this->time_stop);

    unset($this->memory_usage_start);
    unset($this->memory_usage_stop);
    unset($this->memory_usage_peak);
    unset($this->memory_allocated_start);
    unset($this->memory_allocated_stop);
    unset($this->memory_allocated_peak);

    parent::__destruct();
  }

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
   * Turn on/off debugging for every instance of this class.
   *
   * @param bool $debug
   *   Set to TRUE to enable debugging, FALSE to disable.
   *
   * @param c_base_return_status
   *   TRUE is returned on success, FALSE otherwise.
   */
  public static function s_set_debugging($debug) {
    if (!is_bool($debug)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'debug', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    self::$ps_debugging = $debug;

    return new c_base_return_true();
  }

  /**
   * Get debugging enabled/disabled status.
   *
   * @return c_base_return_status
   *   TRUE when debugging is enabled, FALSE otherwise.
   */
  public static function s_get_debugging() {
    if (self::$ps_debugging) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Begin recording the time and memory consumption.
   */
  public function do_start_consumption_recording() {
    if (!self::$ps_debugging) {
      return;
    }

    $this->time_start = microtime(TRUE);
    $this->memory_usage_start = memory_get_usage();
    $this->memory_allocated_start = memory_get_usage(TRUE);
  }

  /**
   * Recording begin time and memory consumption.
   */
  public function do_stop_consumption_recording() {
    if (!self::$ps_debugging) {
      return;
    }

    // don't do anything if the start consumption has yet to be called.
    if (is_null($this->time_start)) {
      return;
    }

    $this->time_stop = microtime(TRUE);
    $this->memory_usage_stop = memory_get_usage();
    $this->memory_allocated_stop = memory_get_usage(TRUE);

    $this->memory_usage_peak = memory_get_peak_usage();
    $this->memory_allocated_peak = memory_get_peak_usage(TRUE);
  }

  /**
   * Record end time and memory consumption.
   */
  public function do_reset_consumption_recording() {
    if (!self::$ps_debugging) {
      return;
    }

    $this->time_start = NULL;
    $this->time_stop = NULL;

    $this->memory_usage_start = NULL;
    $this->memory_usage_stop = NULL;
    $this->memory_usage_peak = NULL;

    $this->memory_allocated_start = NULL;
    $this->memory_allocated_stop = NULL;
    $this->memory_allocated_peak = NULL;
  }

  /**
   * Get the amount of time consumed between the requested start/stop commands.
   *
   * @param bool $milliseconds
   *   Return time in milliseconds when TRUE, otherwise return time in microseconds.
   *
   * @return c_return_status|c_return_int
   *   An integer is returned representing the time difference or FALSE with error bit set is returned on error.
   *   If debugging is disabled, then no error bit will be set on FALSE.
   *
   * @see: do_start_consumption_recording()
   * @see: do_stop_consumption_recording()
   * @see: do_reset_consumption_recording()
   */
  public function get_consumption_time($milliseconds = TRUE) {
    if (!self::$ps_debugging) {
      return new c_base_return_false();
    }

    if (!is_bool($milliseconds)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'milliseconds', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->time_start) || is_null($this->time_stop)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->time_stop', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if ($milliseconds) {
      return c_base_return_int::s_new(($this->time_stop - $this->time_start) * 1000);
    }

    return c_base_return_int::s_new($this->time_stop - $this->time_start);
  }

  /**
   * Get the amount of memory used between the requested start/stop commands.
   *
   * @param int $option
   *   When set to 1: return peak usage.
   *   When set to 2: return stop usage.
   *   When set to 3: return the difference between the start and stop usage (may be negative).
   * @param bool $megabytes
   *   Return the value in megabytes when TRUE, otherwise return the value in bytes.
   *
   * @return c_return_status|c_return_int
   *   An integer is returned representing the time difference or FALSE with error bit set is returned on error.
   *   If debugging is disabled, then no error bit will be set on FALSE.
   *
   * @see: do_start_consumption_recording()
   * @see: do_stop_consumption_recording()
   * @see: do_reset_consumption_recording()
   */
  public function get_consumption_memory_usage($option = 1, $megabytes = TRUE) {
    if (!self::$ps_debugging) {
      return new c_base_return_false();
    }

    if (!is_int($option) || $option < 1 || $option > 3) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'option', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($megabytes)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'megabytes', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($option == 1) {
      if (is_null($this->memory_usage_peak)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->memory_usage_peak', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
      }

      if ($megabytes) {
        return c_base_return_int::s_new($this->memory_usage_peak / 1024 / 1024);
      }

      return c_base_return_int::s_new($this->memory_usage_peak);
    }
    elseif ($option == 2) {
      if (is_null($this->time_stop)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->time_stop', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
        return c_base_return_error::s_false($error);
      }

      if ($megabytes) {
        return c_base_return_int::s_new($this->memory_usage_stop / 1024 / 1024);
      }

      return c_base_return_int::s_new($this->memory_usage_stop);
    }
    else {
      if (is_null($this->time_start)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->time_start', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
        return c_base_return_error::s_false($error);
      }

      if (is_null($this->time_stop)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->time_stop', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
        return c_base_return_error::s_false($error);
      }

      if ($megabytes) {
        return c_base_return_int::s_new(($this->memory_usage_stop - $this->memory_usage_start) / 1024 / 1024);
      }

      return c_base_return_int::s_new($this->memory_usage_stop - $this->memory_usage_start);
    }
  }
}
