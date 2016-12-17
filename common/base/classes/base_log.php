<?php
/**
 * @file
 * Provides a class for managing the logs.
 */

/**
 * A generic class for managing the logs.
 */
class c_base_log {
  const TYPE_NONE       = 0;
  const TYPE_BASE       = 1; // for low-level entries.
  const TYPE_REQUEST    = 2; // accessing the site (generally page requests).
  const TYPE_INTERPET   = 3; // interpretting (such as a PHP-related).
  const TYPE_DATABASE   = 4; // the database.
  const TYPE_USER       = 5; // related to users.
  const TYPE_PROXY      = 6; // proxying as some other user.
  const TYPE_ACCESS     = 7; // access control.
  const TYPE_CONTENT    = 8; // content itself.
  const TYPE_THEME      = 9; // theme (such as renderring a theme).
  const TYPE_RESPONSE   = 10; // response to requests.
  const TYPE_CONNECT    = 11; // relating connecting and disconnecting from the site.
  const TYPE_CLIENT     = 12; // client information.
  const TYPE_SERVER     = 13; // server information.
  const TYPE_LEGAL      = 14; // legal or law-based information.
  const TYPE_AUDIT      = 15; // legal or law-based information.
  const TYPE_CACHE      = 16; // caching.
  const TYPE_SYSTEM     = 17; // system.
  const TYPE_FILE       = 18; // files.
  const TYPE_TIME       = 19; // time-related matters (such as cron jobs).
  const TYPE_EVENT      = 20; // time and place related matters.
  const TYPE_SESSION    = 21; // sessions.
  const TYPE_MAIL       = 22; // e-mails.
  const TYPE_SIGN       = 23; // signatures, such as PGP/GPG.
  const TYPE_SYNC       = 24; // synchronization of information.
  const TYPE_WORKFLOW   = 25; // workflow.
  const TYPE_REQUEST    = 26; // workflow: requesting.
  const TYPE_COMMENT    = 27; // workflow: commenting.
  const TYPE_DRAFT      = 28; // workflow: drafting.
  const TYPE_REVIEW     = 29; // workflow: reviewing.
  const TYPE_EDIT       = 30; // workflow: editting.
  const TYPE_AMEND      = 31; // workflow: ammending.
  const TYPE_UNDO       = 32; // workflow: undoing an edit.
  const TYPE_APPROVE    = 33; // workflow: approving.
  const TYPE_DISPROVE   = 34; // workflow: disproving.
  const TYPE_PUBLISH    = 35; // workflow: publushing.
  const TYPE_UNPUBLISH  = 36; // workflow: publushing.
  const TYPE_ACCEPT     = 37; // workflow: accepting.
  const TYPE_DENY       = 38; // workflow: denying.
  const TYPE_CANCEL     = 39; // workflow: cancelling.
  const TYPE_UNCANCEL   = 40; // workflow: cancelling.
  const TYPE_AUDIT      = 41; // workflow: auditing.
  const TYPE_TRANSITION = 42; // workflow: transitioning.
  const TYPE_REVERT     = 43; // workflow: revert.
  const TYPE_DELETE     = 44; // workflow: delete.
  const TYPE_RESTORE    = 45; // workflow: restore (undelete).
  const TYPE_UPGRADE    = 46; // upgrade.
  const TYPE_DOWNGRADE  = 47; // downgrade.

  // severity defines how important or the context of the log entry.
  const SEVERITY_NONE        = 0;
  const SEVERITY_DEBUG       = 1;
  const SEVERITY_INFORMATION = 2; // regular logging information.
  const SEVERITY_NOTICE      = 3; // information worth noting.
  const SEVERITY_WARNING     = 4; // this could be a problem.
  const SEVERITY_ERROR       = 5; // this is a problem.
  const SEVERITY_CRITICAL    = 6; // this is a big problem.
  const SEVERITY_EMERGENCY   = 7; // this is the most serious type of problem.

  private $type;
  private $data;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->type = self::TYPE_NONE;
    $this->data = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->type);
    unset($this->data);
  }

  /**
   * Assigns the type code for this log entry.
   *
   * @param int $tyoe
   *   The type code.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type($type) {
    if (!is_int($type) || $type < 0) {
      return c_base_return_error::s_false();
    }

    $this->type = $type;
    return new c_base_return_true();
  }

  /**
   * Returns the type code for this log entry.
   *
   * @return c_base_return_status|c_base_return_int
   *   The type integer on success.
   *   FALSE with error bit set on error.
   */
  public function get_type() {
    return c_base_return_int::s_new($this->type);
  }

  /**
   * Returns the data as a serialized array string.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array representing the data array on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_data_serialized() {
    return c_base_return_array::s_new($this->data);
  }

  /**
   * Returns the data as a serialized array string.
   *
   * @return c_base_return_status|c_base_return_string
   *   A serialized string representing the data array on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_data_serialized() {
    return c_base_return_string::s_new(serialize($this->data));
  }

  /**
   * Returns the data as a json-serialized array string.
   *
   * @return c_base_return_status|c_base_return_string
   *   A json-serialized string representing the data array on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_data_jsonized($options = 0, $depth = 512) {
    if (!is_int($options)) {
      return c_base_return_error::s_false();
    }

    if (!is_int($depth) || $depth < 1) {
      return c_base_return_error::s_false();
    }

    $encoded = json_encode($this->data, $options, $depth)
    if ($encoded === FALSE) {
      unset($encoded);
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($encoded);
  }

  /**
   * Assigns data to a specific key in the data array.
   *
   * @param string|int $key
   *   The key name string or integer.
   * @param $value
   *   The value to assign.
   *   There is no enforcement on the data type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  protected function pr_set_data($key, $value) {
    if (is_int($key)) {
      if ($key < 0) {
        return c_base_return_error::s_false();
      }
    }
    elseif (!is_string($key)) {
      return c_base_return_error::s_false();
    }

    $this->data[$key] = $value;
    return new c_base_return_true();
  }

  /**
   * Returns the data assigned at the specified key.
   *
   * @param string|int $key
   *   The key name string or integer.
   *
   * @return c_base_return_status|c_base_return_value
   *   The array value is returned on success.
   *   FALSE with error bit set is returned on invalid key.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_get_data($key) {
    if (is_int($key)) {
      if ($key < 0) {
        return c_base_return_error::s_false();
      }
    }
    elseif (!is_string($key)) {
      return c_base_return_error::s_false();
    }

    if (!array_key_exists($key, $this->data)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_value::s_new($this->data[$key]);
  }
}
