<?php
/**
 * @file
 * Provides a class for managing PHP warnings.
 */
namespace n_koopa;

require_once('common/base/classes/base_return.php');

/**
 * Class for catching PHP warnings.
 *
 * In particular, PHP fails to provide a try { .. } catch { .. } mechanism for warnings.
 * To work around this, a new default error handler must be defined and then used.
 *
 * This is necessary because some extensions, like PHP's postgresql extension, do not return any details on the error on a database connection failure.
 * It does, however, throw the error as a warning.
 *
 * Because this is an error handling class, errors produced by this class will be ignored and bypassed wherever possible.
 */
class c_base_warning_handler {
  private $warnings = NULL;
  private $handler = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->warnings = FALSE;
    $this->handler = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    // restore previous handler on destruction.
    if (!is_null($this->handler)) {
      set_error_handler($this->handler, E_WARNING | E_USER_WARNING);
    }

    unset($this->warnings);
    unset($this->handler);
  }

  /**
   * Setup the PHP default error handler to catch warnings.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   The error bit is not set on error.
   */
  public function do_handle() {
    if (is_array($this->warnings) || $this->warnings === TRUE) {
      return new c_base_return_true();
    }

    $this->handler = set_error_handler(array($this, 'warning_handler'), E_WARNING | E_USER_WARNING);
    if (!is_null($this->handler)) {
      $this->warnings = TRUE;
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Reset the PHP default error handler behavior.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   The error bit is not set on error.
   */
  public function do_restore() {
    if (is_null($this->warnings) || $this->warnings === FALSE) {
      return new c_base_return_false();
    }

    if (is_null($this->handler)) {
      return new c_base_return_true();
    }

    // reset to previous error handler.
    set_error_handler($this->handler, E_WARNING | E_USER_WARNING);

    $this->warnings = FALSE;
    $this->handler = NULL;
    return new c_base_return_true();
  }

  /**
   * Returns any detected warnings.
   *
   * @return c_base_return_status|c_base_return_array
   *   TRUE is returned when the warning handler is active but no errors have been detected.
   *   FALSE is returned when the warning handler is inactive.
   */
  public function get_warnings() {
    if (is_array($this->warnings)) {
      return c_base_return_array::s_new($this->warnings);
    }

    if (is_bool($this->warnings) && $this->warnings) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Handle PHP's warnings.
   *
   * @param int $code
   *   The error level.
   * @param string $message
   *   The error message.
   * @param string|null $file_name
   *   (optional) The name of the file, if provided.
   * @param string|null $line_number
   *   (optional) The line number in the file, if provided.
   * @param array|null $context
   *   (optional) An array of variables that exist during the scope of the error, if provided.
   *
   * @see: set_error_handler()
   */
  public function warning_handler($code, $message, $file_name = NULL, $line_number = NULL, $context = NULL) {
    if ($code !== E_WARNING && $code !== E_USER_WARNING) {
      // shouldn't happen, but just in case, add a failsafe to catch any errors that PHP's error handler should still handle.
      return FALSE;
    }

    if (!is_array($this->warnings)) {
      $this->warnings = [];
    }

    $this->warnings[] = [
      'code' => (int) $code,
      'message' => (string) $message,
      'file_name' => (is_null($file_name) ? NULL : (string) $file_name),
      'line_number' => (is_null($line_number) ? NULL : (int) $line_number),
      'context' => (is_null($context) ? NULL : (array) $context),
    ];

    return TRUE;
  }
}
