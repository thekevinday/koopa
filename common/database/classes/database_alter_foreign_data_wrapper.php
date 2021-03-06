<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER FOREIGN DATA WRAPPER.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_handler.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_options.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_validator.php');

/**
 * The class for building and returning a Postgresql ALTER FOEIGN DATA WRAPPER query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterforeigndatawrapper.html
 */
class c_database_alter_foreign_data_wrapper extends c_database_query {
  use t_database_handler;
  use t_database_name;
  use t_database_options;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_validator;

  protected const p_QUERY_COMMAND = 'alter foreign data wrapper';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->handler   = NULL;
    $this->name      = NULL;
    $this->options   = NULL;
    $this->owner_to  = NULL;
    $this->rename_to = NULL;
    $this->validator = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->handler);
    unset($this->name);
    unset($this->options);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->validator);

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
    if (is_null($this->name)) {
      return new c_base_return_false();
    }

    // @fixme: rewrite this.
    $value = NULL;
    if ($this->action === e_database_action_deprecated::OWNER_TO) {
      if (isset($this->owner_to)) {
        $value = $this->p_do_build_owner_to();
      }
      else {
        unset($value);
        return new c_base_return_false();
      }
    }
    else if ($this->action === e_database_action_deprecated::RENAME_TO) {
      if (isset($this->rename_to)) {
        $value = $this->p_do_build_rename_to();
      }
      else {
        unset($value);
        return new c_base_return_false();
      }
    }
    else {
      if (is_array($this->handler)) {
        $value .= ' ' . $this->p_do_build_handler();
      }

      if (is_array($this->validator)) {
        $value .= ' ' . $this->p_do_build_validator();
      }

      $value .= ' ' . $this->p_do_build_options();
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $this->p_do_build_name();
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
