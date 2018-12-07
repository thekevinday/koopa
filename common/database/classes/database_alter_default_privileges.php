<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DEFAULT PRIVILEGES.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_cascade.php');
require_once('common/database/enumerations/database_privilege.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/enumerations/database_cascade.php');
require_once('common/database/enumerations/database_grant.php');

require_once('common/database/traits/database_cascade.php');
require_once('common/database/traits/database_for_role.php');
require_once('common/database/traits/database_grant.php');
require_once('common/database/traits/database_grant_option_for.php');
require_once('common/database/traits/database_in_schema.php');
require_once('common/database/traits/database_on.php');
require_once('common/database/traits/database_privilege.php');
require_once('common/database/traits/database_to_role.php');
require_once('common/database/traits/database_with_grant_option.php');

/**
 * The class for building and returning a Postgresql ALTER DEFAULT PRIVILEGES query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdefaultpriveleges.html
 */
class c_database_alter_default_priveleges extends c_database_query {
  use t_database_cascade;
  use t_database_for_role;
  use t_database_grant;
  use t_database_grant_option_for;
  use t_database_in_schema;
  use t_database_on;
  use t_database_privilege;
  use t_database_to_role;
  use t_database_with_grant_option;

  protected const p_QUERY_COMMAND = 'alter default privileges';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->cascade           = NULL;
    $this->for_role          = NULL;
    $this->grant             = NULL;
    $this->grant_option_for  = NULL;
    $this->in_schema         = NULL;
    $this->on                = NULL;
    $this->privilege         = NULL;
    $this->to_role           = NULL;
    $this->with_grant_option = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->cascade);
    unset($this->for_role);
    unset($this->grant);
    unset($this->grant_option_for);
    unset($this->in_schema);
    unset($this->on);
    unset($this->privilege);
    unset($this->to_role);
    unset($this->with_grant_option);
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
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->grant) || !(is_array($this->to_role) && !empty($this->to_role))) {
      return new c_base_return_false();
    }

    $value = NULL;
    if ($this->for_role) {
      $value .= $this->p_do_build_for_role();
    }

    if (is_array($this->in_schema) && !empty($this->in_schema)) {
      $value .= is_null($value) ? '' : ' ';
      $value .= $this->p_do_build_in_schema();
    }

    if (is_int($this->grant)) {
      $value .= is_null($value) ? '' : ' ';
      $value .= $this->p_do_build_grant();

      if ($this->grant === e_database_action::ACTION_REVOKE) {
        if ($this->grant_option_for) {
          $value .= ' ' . $this->p_do_build_grant_option_for();
        }
      }
    }

    $value .= is_null($value) ? '' : ' ';
    $value .= $this->p_do_build_privilege();

    if (is_int($this->on)) {
      $value .= ' ' . $this->p_do_build_on();
    }

    if ($this->grant === e_database_action::GRANT) {
      $value .= ' ' . c_database_string::TO;
    }
    else if ($this->grant === e_database_action::REVOKE) {
      $value .= ' ' . c_database_string::FROM;
    }

    if (is_array($this->to_role)) {
      $value .= ' ' . $this->p_do_build_to_role();
    }

    if ($this->grant === e_database_action::GRANT) {
      if ($this->with_grant_option) {
        $value .= ' ' . $this->p_do_build_with_grant_option();
      }
    }
    else if ($this->grant === e_database_action::REVOKE) {
      if (is_int($this->cascade)) {
        $value .= ' ' . $this->p_do_build_cascade();
      }
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
