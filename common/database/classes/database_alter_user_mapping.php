<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_server_name.php');
require_once('common/database/traits/database_server_options.php');
require_once('common/database/traits/database_user_name.php');

/**
 * The class for building and returning a Postgresql ALTER USER MAPPING query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterusermapping.html
 */
class c_database_alter_user_mapping extends c_database_query {
  use t_database_server_name;
  use t_database_server_options;
  use t_database_user_name;

  protected const p_QUERY_COMMAND = 'alter user mapping for';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->server_name    = NULL;
    $this->server_options = NULL;
    $this->user_name      = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->server_name);
    unset($this->server_options);
    unset($this->user_name);

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
   * Implements do_build().
   */
  public function do_build() {
    if (!isset($this->user_name) || !isset($this->server_name) || !isset($this->server_options)) {
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->p_do_build_user_name();
    $this->value .= ' ' . $this->p_do_build_server_name();
    $this->value .= ' ' . $this->p_do_build_server_options();
    unset($value);

    return new c_base_return_true();
  }
}
