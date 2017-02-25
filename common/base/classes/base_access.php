<?php
/**
 * @file
 * Provides a class for managing system access.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_session.php');
require_once('common/base/classes/base_ldap.php');

/**
 * A class for managing roles.
 *
 * Roles defined here are general top-level roles used for separating database activity.
 * The intentions here is to keep the roles as simple and as few as possible while allowing considerable flexibility.
 * This should cut down on the complexity of the database access control.
 *
 * Additional granularity may be supplied via PHP access checks or by extending this class.
 *
 * Roles:
 * - None: no access to anything.
 * - Public: access to only public information (users who are not logged in have this, such as anonymous).
 * - System: account is a machine and should not be a human (such as with cron jobs).
 * - User: account is a user and that user is logged in.
 * - Requester: account is for requesting something, generally via some sort of form.
 * - Drafter: account is for making templates, drafts, ideas, etc.. (this is a lesser form of "editer").
 * - Editer: account is for editors who add/manage/create content.
 * - Reviewer: account is for users who review something (such as a user who approves content for publishing).
 * - Insurer: account is for users who deal with insurance related information.
 * - Financer: account is for users who deal with financial related information.
 * - Publisher: account is for users who perform publishing (marking content available and complete).
 * - Auditor: account is for users who perform auditing. This account has read access to almost all data on the system.
 * - Manager: account is for users who manager the entire system. This is a non-technical administration account.
 * - Administer: account is for users who have full administrative access to the system. This is a technical administration account and supercedes Manager.
 */
class c_base_roles extends c_base_return {
  const NONE       = 0;
  const PUBLIC     = 1;
  const SYSTEM     = 2;
  const USER       = 3;
  const REQUESTER  = 4;
  const DRAFTER    = 5;
  const EDITOR     = 6;
  const REVIEWER   = 7;
  const INSURER    = 8;
  const FINANCER   = 9;
  const PUBLISHER  = 10;
  const AUDITOR    = 11;
  const MANAGER    = 12;
  const ADMINISTER = 13;

  private $public;
  private $system;
  private $user;
  private $requester;
  private $drafter;
  private $editer;
  private $reviewer;
  private $insurer;
  private $financer;
  private $publisher;
  private $auditor;
  private $manager;
  private $administer;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->public     = TRUE;
    $this->system     = FALSE;
    $this->user       = FALSE;
    $this->requester  = FALSE;
    $this->drafter    = FALSE;
    $this->editer     = FALSE;
    $this->reviewer   = FALSE;
    $this->financer   = FALSE;
    $this->insurer    = FALSE;
    $this->publisher  = FALSE;
    $this->auditor    = FALSE;
    $this->manager    = FALSE;
    $this->administer = FALSE;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->public);
    unset($this->system);
    unset($this->user);
    unset($this->requester);
    unset($this->drafter);
    unset($this->editer);
    unset($this->reviewer);
    unset($this->financer);
    unset($this->insurer);
    unset($this->publisher);
    unset($this->auditor);
    unset($this->manager);
    unset($this->administer);

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
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Assign a role.
   *
   * When role is set to NONE, and value is TRUE, then all roles are set to FALSE.
   * When role is set to NONE and value is FALSE, nothing happens.
   *
   * @param int $role
   *   The role id to assign.
   * @param bool $value
   *   Set the role value to TRUE/FALSE.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_role($role, $value) {
    if (!is_int($role)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'role', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'value', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($role === self::NONE) {
      if ($value) {
        $this->public     = FALSE;
        $this->system     = FALSE;
        $this->user       = FALSE;
        $this->requester  = FALSE;
        $this->drafter    = FALSE;
        $this->editer     = FALSE;
        $this->reviewer   = FALSE;
        $this->publisher  = FALSE;
        $this->auditor    = FALSE;
        $this->manager    = FALSE;
        $this->administer = FALSE;
      }
    }
    elseif ($role === self::PUBLIC) {
      $this->public = $value;
    }
    elseif ($role === self::SYSTEM) {
      $this->system = $value;
    }
    elseif ($role === self::USER) {
      $this->user = $value;
    }
    elseif ($role === self::REQUESTER) {
      $this->requester = $value;
    }
    elseif ($role === self::DRAFTER) {
      $this->drafter = $value;
    }
    elseif ($role === self::EDITOR) {
      $this->editer = $value;
    }
    elseif ($role === self::REVIEWER) {
      $this->reviewer = $value;
    }
    elseif ($role === self::FINANCER) {
      $this->financer = $value;
    }
    elseif ($role === self::INSURER) {
      $this->insurer = $value;
    }
    elseif ($role === self::PUBLISHER) {
      $this->publisher = $value;
    }
    elseif ($role === self::AUDITOR) {
      $this->auditor = $value;
    }
    elseif ($role === self::MANAGER) {
      $this->manager = $value;
    }
    elseif ($role === self::ADMINISTER) {
      $this->administer = $value;
    }
    else {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }

  /**
   * Get the current status of the specified role.
   *
   * When role is set to NONE, TRUE is returned when all values are set to FALSE.
   * When role is set to NONE, FALSE is returned when any values are set to TRUE.
   *
   * @param int $role
   *   The role id to get the value of.
   *
   * @return c_base_return_status
   *   TRUE on enabled, FALSE on disabled.
   *   FALSE with error bit set is returned on error.
   */
  public function get_role($role) {
    if (!is_int($role)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'role', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($role === self::NONE) {
      if (!($this->public || $this->system || $this->user || $this->requester || $this->drafter || $this->editer || $this->reviewer || $this->publisher || $this->auditor || $this->manager || $this->administer)) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::PUBLIC) {
      if ($this->public) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::SYSTEM) {
      if ($this->system) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::USER) {
      if ($this->user) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::REQUESTER) {
      if ($this->requester) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::DRAFTER) {
      if ($this->drafter) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::EDITOR) {
      if ($this->editer) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::REVIEWER) {
      if ($this->reviewer) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::FINANCER) {
      if ($this->financer) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::INSURER) {
      if ($this->insurer) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::PUBLISHER) {
      if ($this->publisher) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::AUDITOR) {
      if ($this->auditor) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::MANAGER) {
      if ($this->manager) {
        return new c_base_return_true();
      }
    }
    elseif ($role === self::ADMINISTER) {
      if ($this->administer) {
        return new c_base_return_true();
      }
    }

    return new c_base_return_false();
  }

  /**
   * Get an array of all currently assigned roles.
   *
   * @return c_base_return_array
   *   An array of roles are returned.
   *   An array with error bit set is returned on error.
   */
  public function get_roles() {
    $roles = array();

    if ($this->public) {
      $roles[self::PUBLIC] = self::PUBLIC;
    }

    if ($this->system) {
      $roles[self::SYSTEM] = self::SYSTEM;
    }

    if ($this->user) {
      $roles[self::USER] = self::USER;
    }

    if ($this->requester) {
      $roles[self::REQUESTER] = self::REQUESTER;
    }

    if ($this->drafter) {
      $roles[self::DRAFTER] = self::DRAFTER;
    }

    if ($this->editer) {
      $roles[self::EDITOR] = self::EDITOR;
    }

    if ($this->reviewer) {
      $roles[self::REVIEWER] = self::REVIEWER;
    }

    if ($this->financer) {
      $roles[self::FINANCER] = self::FINANCER;
    }

    if ($this->insurer) {
      $roles[self::INSURER] = self::INSURER;
    }

    if ($this->publisher) {
      $roles[self::PUBLISHER] = self::PUBLISHER;
    }

    if ($this->auditor) {
      $roles[self::AUDITOR] = self::AUDITOR;
    }

    if ($this->manager) {
      $roles[self::MANAGER] = self::MANAGER;
    }

    if ($this->administer) {
      $roles[self::ADMINISTER] = self::ADMINISTER;
    }

    return c_base_return_array::s_new($roles);
  }
}
