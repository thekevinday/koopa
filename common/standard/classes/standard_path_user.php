<?php
/**
 * @file
 * Provides the standard path handling class with user-path specific parts.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_database.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides user-specific extensions to standard paths.
 *
 * This extension specifically provides a user id and a user object that the path is expected to present the content of.
 * This is not to be confused with the currently logged in user.
 */
class c_standard_path_user extends c_standard_path {
  protected const ID_USER_MINIMUM = 1000;

  protected const CLASS_ID_USER          = 'id-user';
  protected const CLASS_ID_USER_EXTERNAL = 'id-user-external';

  protected $path_user;
  protected $path_user_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->path_user    = NULL;
    $this->path_user_id = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->path_user);
    unset($this->path_user_id);

    parent::__destruct();
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = []) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Provides a standard argument handler function.
   *
   * This is generally intended to be called by do_execute().
   *
   * This will load and then validate the standard argument structure.
   * The standard user path argument structure is as follows:
   *   - No Arguments: default user account and settings.
   *   - Argument 0 (optional): The user ID argument to present the content of (may also be non-numeric values of 'Argument 1' for current user).
   *   - Argument 1 (optional): Action argument, at the very least supports the following values 'html', 'rss', 'ical', 'pdf', 'ps', 'print', 'json', and 'text'.
   *
   * This alters the path_user and path_user_id class variables.
   * - path_user is set to either the current user or the user specified by the url arguments.
   * - path_user_id is either assigned to the id of the user represented by path_user or FALSE.
   *   - If FALSE, then the user ID is either invalid or is somehow unavailable.
   *
   * This alters the format_output class variable as follows:
   * - provides output mime-type integer code for expected output format.
   * - special case formats (non-integer/string values), such as 'print', are stored in this variable to represent print-friendly output.
   *
   * This will alter the current position of the arguments array as per PHP array functions.
   * - This allows for child classes to process further arguments after calling this function without re-processing.
   * - reset() should be called on the array argument to bypass this behavior.
   *
   * @param c_base_path_executed &$executed
   *   The execution array for making changes to.
   *   Any detected errors are assigned to this.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  protected function pr_process_arguments(&$executed) {
    $this->path_user = $this->session->get_user_current();
    $this->path_user_id = NULL;
    $this->output_format = c_base_mime::TYPE_TEXT_HTML;

    if ($this->pr_process_path_arguments(static::PATH_SELF)) {
      $argument = reset($this->arguments);

      if (is_numeric($argument)) {
        $this->path_user_id = (int) $argument;

        // do not allow view access to reserved/special accounts.
        if ($this->path_user_id < static::ID_USER_MINIMUM) {
          $this->path_user_id = FALSE;
          unset($argument);

          $error = c_base_error::s_log(NULL, ['arguments' => [':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::NOT_FOUND_PATH);
          $executed->set_error($error);
          unset($error);

          return FALSE;
        }
      }
      else {
        $argument = NULL;
      }

      $arguments_total = count($this->arguments);
      if (is_null($argument) || $arguments_total > 1) {
        if (is_null($argument)) {
          $argument = current($this->arguments);
        }
        else {
          $argument = next($this->arguments);
        }

        if ($argument == 'print') {
          $this->output_format = 'print';
        }
        elseif ($argument == 'html') {
          $this->output_format = c_base_mime::TYPE_TEXT_HTML;
        }
        elseif ($argument == 'pdf') {
          $this->output_format = c_base_mime::TYPE_DOCUMENT_PDF;
        }
        elseif ($argument == 'ps') {
          $this->output_format = c_base_mime::TYPE_TEXT_PS;
        }
        elseif ($argument == 'rss') {
          $this->output_format = c_base_mime::TYPE_TEXT_RSS;
        }
        elseif ($argument == 'ical') {
          $this->output_format = c_base_mime::TYPE_TEXT_ICAL;
        }
        elseif ($argument == 'text') {
          $this->output_format = c_base_mime::TYPE_TEXT_PLAIN;
        }
        elseif ($argument == 'json') {
          $this->output_format = c_base_mime::TYPE_TEXT_JSON;
        }
        else {
          unset($argument);
          unset($arguments_total);

          $error = c_base_error::s_log(NULL, ['arguments' => [':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::NOT_FOUND_PATH);
          $executed->set_error($error);
          unset($error);

          return FALSE;
        }
      }
      unset($argument);
      unset($arguments_total);
    }

    if (is_null($this->path_user_id)) {
      $this->path_user = $this->session->get_user_current();
      $this->path_user_id = $this->path_user->get_id()->get_value_exact();

      // do not allow view access to reserved/special accounts.
      if ($this->path_user_id < static::ID_USER_MINIMUM) {
        $this->path_user_id = FALSE;
      }
    }
    else {
      $this->path_user = new c_standard_users_user();

      // @todo: handle database errors.
      $loaded = $this->path_user->do_load($this->database, $this->path_user_id);
      if ($loaded instanceof c_base_return_false) {
        $this->path_user_id = FALSE;
      }
      else {
        // @todo: check to see if user id is accessible.
      }
      unset($loaded);
    }

    if ($this->path_user_id === FALSE) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);
      unset($error);

      return FALSE;
    }

    return TRUE;
  }
}
