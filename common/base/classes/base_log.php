<?php
/**
 * @file
 * Provides a class for managing the logs.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for managing the logs.
 */
class c_base_log extends c_base_return_array {
  const TYPE_NONE        = 0;
  const TYPE_ACCESS      = 1;  // access control.
  const TYPE_ACCEPT      = 2;  // accept something, workflow: accepting.
  const TYPE_AMEND       = 3;  // amend something, workflow: ammending.
  const TYPE_APPROVE     = 4;  // approve something, workflow: approving.
  const TYPE_AUDIT       = 5;  // audit something, legal or law-based information, workflow: auditing.
  const TYPE_BASE        = 6;  // for low-level entries.
  const TYPE_CACHE       = 7;  // caching.
  const TYPE_CANCEL      = 8;  // cancel something, workflow: cancelling.
  const TYPE_CREATE      = 9;  // create something.
  const TYPE_CLIENT      = 10; // client information.
  const TYPE_CONNECT     = 11; // connect to something.
  const TYPE_CONTENT     = 12; // content.
  const TYPE_COMMENT     = 13; // workflow: commenting.
  const TYPE_DATABASE    = 14; // the database, sql.
  const TYPE_DELETE      = 15; // delete something, workflow: delete.
  const TYPE_DENY        = 16; // deny something, workflow: denying.
  const TYPE_DRAFT       = 17; // draft of something, workflow: drafting.
  const TYPE_DISPROVE    = 18; // disapprove something, workflow: disproving.
  const TYPE_DISCONNECT  = 19; // disconnect something.
  const TYPE_DOWNGRADE   = 20; // downgrade.
  const TYPE_EDIT        = 21; // edit something, workflow: editting.
  const TYPE_EVENT       = 22; // time and place related matters.
  const TYPE_FAILURE     = 23; // failure.
  const TYPE_FILE        = 24; // files.
  const TYPE_INTERPETOR  = 25; // interpretting (such as a PHP-related).
  const TYPE_LEGAL       = 26; // legal or law-based information.
  const TYPE_LOCK        = 27; // lock something.
  const TYPE_MAIL        = 28; // e-mails.
  const TYPE_PROXY       = 29; // proxying as some other user.
  const TYPE_PUBLISH     = 30; // publish something, workflow: publushing.
  const TYPE_RESPONSE    = 31; // response to requests.
  const TYPE_RESTORE     = 32; // restore something, workflow: restore (undelete).
  const TYPE_REQUEST     = 33; // accessing the site (generally page requests), workflow: requesting..
  const TYPE_REVERT      = 34; // revert something, workflow: revert.
  const TYPE_REVIEW      = 35; // review something, workflow: reviewing.
  const TYPE_SCHEDULE    = 36; // schedule something.
  const TYPE_SEARCH      = 37; // search.
  const TYPE_SESSION     = 38; // sessions.
  const TYPE_SIGN        = 39; // signatures, such as PGP/GPG.
  const TYPE_SYNCHRONIZE = 40; // synchronization of information.
  const TYPE_SYSTEM      = 41; // system/server.
  const TYPE_THEME       = 42; // theme (such as renderring a theme).
  const TYPE_TIME        = 43; // time-related matters (such as cron jobs).
  const TYPE_TRANSITION  = 44; // transition, workflow: transitioning.
  const TYPE_UNCANCEL    = 45; // uncancel something, workflow: cancelling.
  const TYPE_UNDO        = 46; // undo somethingm workflow: undoing an edit.
  const TYPE_UNPUBLISH   = 47; // unpublish something, workflow: publushing.
  const TYPE_UPDATE      = 48; // update something.
  const TYPE_UPGRADE     = 49; // upgrade something.
  const TYPE_USER        = 50; // related to users.
  const TYPE_VOID        = 51; // void something, such as a signature.
  const TYPE_WORKFLOW    = 52; // workflow.

  protected $type;
  protected $type_sub;

  protected $severity;
  protected $facility;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->type     = static::TYPE_NONE;
    $this->type_sub = static::TYPE_NONE;

    $this->severity = c_base_error::SEVERITY_NONE;
    $this->facility = c_base_error::FACILITY_NONE;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->type);
    unset($this->type_sub);

    unset($this->severity);
    unset($this->facility);

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
   * Assigns the type code for this log entry.
   *
   * @param int $type
   *   The type code.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type($type) {
    if (!is_int($type) || $type < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->type = $type;
    return new c_base_return_true();
  }

  /**
   * Assigns the sub-type code for this log entry.
   *
   * @param int $type_sub
   *   The sub-type code.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type_sub($type_sub) {
    if (!is_int($type_sub) || $type_sub < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type_sub', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->type_sub = $type_sub;
    return new c_base_return_true();
  }

  /**
   * Assigns the severity code for this log entry.
   *
   * @param int $type
   *   The severity code.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_severity($severity) {
    if (!is_int($severity) || $severity < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'severity', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->severity = $severity;
    return new c_base_return_true();
  }

  /**
   * Assigns the facility code for this log entry.
   *
   * This is generally used for syslog compatibility.
   *
   * @param int $facility
   *   The facility code.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_facility($facility) {
    if (!is_int($facility) || $facility < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'facility', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->facility = $facility;
    return new c_base_return_true();
  }

  /**
   * Returns the type code for this log entry.
   *
   * @return c_base_return_status|c_base_return_int
   *   The type integer on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_type() {
    if (!is_int($this->type)) {
      $this->type = static::TYPE_NONE;
    }

    return c_base_return_int::s_new($this->type);
  }

  /**
   * Returns the sub-type code for this log entry.
   *
   * @return c_base_return_status|c_base_return_int
   *   The type integer on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_type_sub() {
    if (!is_int($this->type_sub)) {
      $this->type_sub = static::TYPE_NONE;
    }

    return c_base_return_int::s_new($this->type_sub);
  }

  /**
   * Returns the severity code for this log entry.
   *
   * @return c_base_return_status|c_base_return_int
   *   The severity integer on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_severity() {
    if (!is_int($this->severity)) {
      $this->severity = c_base_error::SEVERITY_NONE;
    }

    return c_base_return_int::s_new($this->severity);
  }

  /**
   * Returns the facility code for this log entry.
   *
   * This is generally used for syslog compatibility.
   *
   * @return c_base_return_status|c_base_return_int
   *   The facility integer on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_facility() {
    if (!is_int($this->facility)) {
      $this->facility = c_base_error::FACILITY_NONE;
    }

    return c_base_return_int::s_new($this->facility);
  }
}
