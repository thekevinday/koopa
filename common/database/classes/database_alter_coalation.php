<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');
require_once('common/database/classes/database_string.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_set_schema.php');

/**
 * The class for building and returning a Postgresql ALTER COALATION query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altercoalation.html
 */
class c_database_alter_coalation extends c_database_query {
  use t_database_name;
  use t_database_rename_to;
  use t_database_owner_to;
  use t_database_set_schema;

  protected const p_QUERY_COMMAND = 'alter coalation';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name       = NULL;
    $this->rename_to  = NULL;
    $this->owner_to   = NULL;
    $this->set_schema = NULL;

    $this->refreh_version = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->rename_to);
    unset($this->owner_to);
    unset($this->set_schema);

    unset($this->refresh_version);

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
   * Set the refresh version.
   *
   * @param bool|null $refresh_version
   *   Whether or not to use REFRESH VERSION in the query.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_refresh_version($refresh_version) {
    if (is_null($refresh_version)) {
      $this->refresh_version = NULL;
      return new c_base_return_true();
    }

    if (is_bool($refresh_version)) {
      $this->refresh_version = $refresh_version;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'refresh_version', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the refresh version setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE if refresh version is enabled, FALSE if disabled, or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_refresh_version() {
    if (is_null($this->refresh_version)) {
      return new c_base_return_null();
    }

    if (is_bool($this->refresh_version)) {
      return c_base_return_bool::s_new($this->refresh_version);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'refresh_version', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->name)) {
      return new c_base_return_false();
    }

    $value = $this->p_do_build_name();
    if (is_bool($this->refresh_version)) {
      if ($this->refresh_version) {
        $value .= c_database_string::REFRESH_VERSION;
      }
    }
    else if (isset($this->rename_to)) {
      $value .= $this->p_do_build_rename_to();
    }
    else if (isset($this->owner_to)) {
      $value .= $this->p_do_build_owner_to();
    }
    else if (isset($this->set_schema)) {
      $value .= $this->p_do_build_set_schema();
    }
    else {
      unset($value);
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
