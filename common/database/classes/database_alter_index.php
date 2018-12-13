<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER INDEX.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_depends_on_extension.php');
require_once('common/database/traits/database_if_exists.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_no_wait.php');
require_once('common/database/traits/database_owned_by.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_reset_storage_parameter.php');
require_once('common/database/traits/database_set_storage_parameter.php');
require_once('common/database/traits/database_set_tablespace.php');


/**
 * The class for building and returning a Postgresql ALTER INDEX query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterindex.html
 */
class c_database_alter_index extends c_database_query {
  use t_database_depends_on_extension;
  use t_database_if_exists;
  use t_database_name;
  use t_database_no_wait;
  use t_database_owned_by;
  use t_database_rename_to;
  use t_database_reset_storage_parameter;
  use t_database_set_storage_parameter; // @todo: override the storage parameter to limit/restrict parameters to index storage parameters.
  use t_database_set_tablespace;

  protected const p_QUERY_COMMAND = 'alter index';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->depends_on_extension    = NULL;
    $this->if_exists               = NULL;
    $this->name                    = NULL;
    $this->no_wait                 = NULL;
    $this->owned_by                = NULL;
    $this->rename_to               = NULL;
    $this->reset_storage_parameter = NULL;
    $this->set_storage_parameter   = NULL;
    $this->set_tablespace          = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->depends_on_extension);
    unset($this->if_exists);
    unset($this->name);
    unset($this->no_wait);
    unset($this->owned_by);
    unset($this->rename_to);
    unset($this->reset_storage_parameter);
    unset($this->set_storage_parameter);
    unset($this->set_tablespace);

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
    if (!is_string($this->name)) {
      return new c_base_return_false();
    }

    $if_exists = NULL;
    if (is_bool($this->if_exists)) {
      $if_exists = ' ' . $this->p_do_build_if_exists();
    }

    $value = $this->p_do_build_name();
    if (is_string($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (is_string($this->set_tablespace)) {
      $value .= ' ' . $this->p_do_build_set_tablespace();
    }
    else if (is_string($this->depends_on_extension)) {
      $if_exists = NULL;
      $value .= ' ' . $this->p_do_build_depends_on_extension();
    }
    else if (is_array($this->set_storage_parameter)) {
      $value .= ' ' . $this->p_do_build_set_storage_parameter();
    }
    else if (is_array($this->reset_storage_parameter)) {
      $value .= ' ' . $this->p_do_build_reset_storage_parameter();
    }
    else if (is_string($this->set_tablespace)) {
      $if_exists = NULL;
      $value = c_database_string::ALL_IN_TABLESPACE . ' ' . $value;

      if (!is_null($this->owned_by)) {
        $value .= ' ' . $this->p_do_build_owned_by();
      }

      $value .= ' ' . $this->p_do_build_set_tablespace();

      if (is_bool($this->no_wait)) {
        $value .= ' ' . $this->p_do_build_no_wait();
      }
    }

    $this->value = static::p_QUERY_COMMAND;

    if (is_string($if_exists)) {
      $this->value .= $if_exists;
    }
    unset($if_exists);

    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
