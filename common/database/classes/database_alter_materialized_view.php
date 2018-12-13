<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_all_in_tablespace.php');
require_once('common/database/traits/database_cluster_on.php');
require_once('common/database/traits/database_column_reset.php');
require_once('common/database/traits/database_column_set.php');
require_once('common/database/traits/database_column_set_statistics.php');
require_once('common/database/traits/database_column_set_storage.php');
require_once('common/database/traits/database_depends_on_extension.php');
require_once('common/database/traits/database_if_exists.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_no_wait.php');
require_once('common/database/traits/database_owned_by.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_column.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_reset_storage_parameter.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_set_storage_parameter.php');
require_once('common/database/traits/database_set_tablespace.php');
require_once('common/database/traits/database_set_without_cluster.php');


/**
 * The class for building and returning a Postgresql ALTER MATERIALIZED VIEW query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altermaterializedview.html
 */
class c_database_alter_materialized_view extends c_database_query {
  use t_database_all_in_tablespace;
  use t_database_cluster_on;
  use t_database_column_reset;
  use t_database_column_set;
  use t_database_column_set_statistics;
  use t_database_column_set_storage;
  use t_database_depends_on_extension;
  use t_database_if_exists;
  use t_database_name;
  use t_database_no_wait;
  use t_database_owned_by;
  use t_database_owner_to;
  use t_database_rename_column;
  use t_database_rename_to;
  use t_database_reset_storage_parameter;
  use t_database_set_schema;
  use t_database_set_storage_parameter;
  use t_database_set_tablespace;
  use t_database_set_without_cluster;

  protected const p_QUERY_COMMAND = 'alter materialized view';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->all_in_tablespace       = NULL;
    $this->cluster_on              = NULL;
    $this->column_reset            = NULL;
    $this->column_set              = NULL;
    $this->column_set_statistics   = NULL;
    $this->column_set_storage      = NULL;
    $this->depends_on_extension    = NULL;
    $this->if_exists               = NULL;
    $this->name                    = NULL;
    $this->no_wait                 = NULL;
    $this->owned_by                = NULL;
    $this->owner_to                = NULL;
    $this->rename_column           = NULL;
    $this->rename_to               = NULL;
    $this->reset_storage_parameter = NULL;
    $this->set_schema              = NULL;
    $this->set_storage_parameter   = NULL;
    $this->set_tablespace          = NULL;
    $this->set_without_cluster     = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->all_in_tablespace);
    unset($this->cluster_on);
    unset($this->column_reset);
    unset($this->column_set);
    unset($this->column_set_statistics);
    unset($this->column_set_storage);
    unset($this->depends_on_extension);
    unset($this->if_exists);
    unset($this->name);
    unset($this->no_wait);
    unset($this->owner_to);
    unset($this->owned_by);
    unset($this->rename_column);
    unset($this->rename_to);
    unset($this->reset_storage_parameter);
    unset($this->set_schema);
    unset($this->set_storage_parameter);
    unset($this->set_tablespace);
    unset($this->set_without_cluster);

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
    if (is_string($this->depends_on_extension)) {
      $value .= ' ' . $this->p_do_build_depends_on_extension();
      $if_exists = NULL;
    }
    else if (is_array($this->rename_column)) {
      $value .= ' ' . $this->p_do_build_rename_column();
    }
    else if (is_string($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (is_string($this->set_schema)) {
      $value .= ' ' . $this->p_do_build_set_schema();
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
    else if (is_array($this->column_set_statistics)) {
      $value .= ' ' . $this->p_do_build_column_set_statistics();
    }
    else if (is_array($this->column_set)) {
      $value .= ' ' . $this->p_do_build_column_set();
    }
    else if (is_array($this->column_reset)) {
      $value .= ' ' . $this->p_do_build_column_reset();
    }
    else if (is_array($this->column_set_storage)) {
      $value .= ' ' . $this->p_do_build_column_set_storage();
    }
    else if (is_string($this->cluster_on)) {
      $value .= ' ' . $this->p_do_build_cluster_on();
    }
    else if (is_string($this->set_without_cluster)) {
      $value .= ' ' . $this->p_do_build_set_without_cluster();
    }
    else if (is_string($this->set_storage_parameter)) {
      $value .= ' ' . $this->p_do_build_set_storage_parameter();
    }
    else if (is_string($this->reset_storage_parameter)) {
      $value .= ' ' . $this->p_do_build_reset_storage_parameter();
    }
    else if (is_string($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else {
      unset($value);
      unset($if_exists);
      return new c_base_return_false();
    }

    $this->value = static::p_QUERY_COMMAND;

    if (is_string($if_exists)) {
      $this->value .= ' ' . $if_exists;
    }
    unset($if_exists);

    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
