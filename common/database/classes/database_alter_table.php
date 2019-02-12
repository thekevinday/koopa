<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER TABLE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_action_add_column.php');
require_once('common/database/traits/database_action_alter_column.php');
require_once('common/database/traits/database_action_alter_column_options.php');
require_once('common/database/traits/database_action_alter_column_reset.php');
require_once('common/database/traits/database_action_alter_column_set.php');
require_once('common/database/traits/database_action_cluster_on.php');
require_once('common/database/traits/database_action_constraint.php');
require_once('common/database/traits/database_action_disable_rule.php');
require_once('common/database/traits/database_action_disable_trigger.php');
require_once('common/database/traits/database_action_drop_columm.php');
require_once('common/database/traits/database_action_enable_rule.php');
require_once('common/database/traits/database_action_enable_trigger.php');
require_once('common/database/traits/database_action_inherit.php');
require_once('common/database/traits/database_action_owner_to.php');
require_once('common/database/traits/database_action_replica_identity.php');
require_once('common/database/traits/database_action_row_level_security.php');
require_once('common/database/traits/database_action_set_logged.php');
require_once('common/database/traits/database_action_set_of.php');
require_once('common/database/traits/database_action_set_oids.php');
require_once('common/database/traits/database_action_set_tablespace.php');
require_once('common/database/traits/database_action_without_cluster.php');
require_once('common/database/traits/database_attach_partition.php');
require_once('common/database/traits/database_detach_partition.php');
require_once('common/database/traits/database_if_exists.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_no_wait.php');
require_once('common/database/traits/database_only.php');
require_once('common/database/traits/database_owned_by.php');
require_once('common/database/traits/database_rename_column.php');
require_once('common/database/traits/database_rename_constraint.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_set_tablespace.php');
require_once('common/database/traits/database_wildcard.php');


/**
 * The class for building and returning a Postgresql ALTER TABLE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altertable.html
 */
class c_database_alter_table extends c_database_query {
  use t_database_action_add_column;
  use t_database_action_alter_column;
  use t_database_action_alter_column_options;
  use t_database_action_alter_column_reset;
  use t_database_action_alter_column_set;
  use t_database_action_cluster_on;
  use t_database_action_constraint;
  use t_database_action_disable_rule;
  use t_database_action_disable_trigger;
  use t_database_action_drop_columm;
  use t_database_action_enable_rule;
  use t_database_action_enable_trigger;
  use t_database_action_inherit;
  use t_database_action_not_of;
  use t_database_action_of;
  use t_database_action_owner_to;
  use t_database_action_replica_identity;
  use t_database_action_row_level_security;
  use t_database_action_set_logged;
  use t_database_action_set_oids;
  use t_database_action_set_tablespace;
  use t_database_action_without_cluster;
  use t_database_attach_partition;
  use t_database_detach_partition;
  use t_database_if_exists;
  use t_database_name;
  use t_database_no_wait;
  use t_database_only;
  use t_database_owned_by;
  use t_database_rename_column;
  use t_database_rename_constraint;
  use t_database_rename_to;
  use t_database_set_schema;
  use t_database_set_tablespace;
  use t_database_wildcard;

  protected const p_QUERY_COMMAND = 'alter table';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->action_add_column           = NULL;
    $this->action_alter_column         = NULL;
    $this->action_alter_column_options = NULL;
    $this->action_alter_column_reset   = NULL;
    $this->action_alter_column_set     = NULL;
    $this->action_cluster_on           = NULL;
    $this->action_constraint           = NULL;
    $this->action_disable_rule         = NULL;
    $this->action_disable_trigger      = NULL;
    $this->action_drop_columm          = NULL;
    $this->action_enable_rule          = NULL;
    $this->action_enable_trigger       = NULL;
    $this->action_inherit              = NULL;
    $this->action_not_of               = NULL;
    $this->action_options              = NULL;
    $this->action_owner_to             = NULL;
    $this->action_replica_identity     = NULL;
    $this->action_row_level_security   = NULL;
    $this->action_set_logged           = NULL;
    $this->action_set_of               = NULL;
    $this->action_set_oids             = NULL;
    $this->action_set_tablespace       = NULL;
    $this->action_without_cluster      = NULL;
    $this->attach_partition            = NULL;
    $this->detach_partition            = NULL;
    $this->if_exists                   = NULL;
    $this->name                        = NULL;
    $this->no_wait                     = NULL;
    $this->only                        = NULL;
    $this->owned_by                    = NULL;
    $this->rename_column               = NULL;
    $this->rename_constraint           = NULL;
    $this->rename_to                   = NULL;
    $this->set_schema                  = NULL;
    $this->set_tablespace              = NULL;
    $this->wildcard                    = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->action_add_column);
    unset($this->action_alter_column);
    unset($this->action_alter_column_options);
    unset($this->action_alter_column_reset);
    unset($this->action_alter_column_set);
    unset($this->action_cluster_on);
    unset($this->action_constraint);
    unset($this->action_disable_rule);
    unset($this->action_disable_trigger);
    unset($this->action_drop_columm);
    unset($this->action_enable_rule);
    unset($this->action_enable_trigger);
    unset($this->action_inherit);
    unset($this->action_not_of);
    unset($this->action_options);
    unset($this->action_owner_to);
    unset($this->action_replica_identity);
    unset($this->action_row_level_security);
    unset($this->action_set_logged);
    unset($this->action_set_of);
    unset($this->action_set_oids);
    unset($this->action_set_tablespace);
    unset($this->action_without_cluster);
    unset($this->attach_partition);
    unset($this->detach_partition);
    unset($this->if_exists);
    unset($this->name);
    unset($this->no_wait);
    unset($this->only);
    unset($this->owned_by);
    unset($this->rename_column);
    unset($this->rename_constraint);
    unset($this->rename_to);
    unset($this->set_schema);
    unset($this->set_tablespace);
    unset($this->wildcard);

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

    $if_exists = NULL;
    if ($this->if_exists) {
      $if_exists = $this->p_do_build_if_exists() . ' ';
    }

    $only = NULL;
    if ($this->only) {
      $only = $this->p_do_build_only() . ' ';
    }

    $wildcard = NULL;
    if ($this->wildcard) {
      $wildcard = ' ' . $this->p_do_build_wildcard();
    }

    $value = $this->p_do_build_name();
    if (isset($this->rename_column)) {
      $value = $if_exists . $only . $value . ' ' . $wildcard . $this->p_do_build_rename_column();
    }
    else if (isset($this->rename_constraint)) {
      $value = $if_exists . $only . $value . ' ' . $wildcard . $this->p_do_build_rename_constraint();
    }
    else if (isset($this->rename_to)) {
      $value = $if_exists . $wildcard . $this->p_do_build_rename_to();
    }
    else if (isset($this->set_schema)) {
      $value = $if_exists . $wildcard . $this->p_do_build_set_schema();
    }
    else if (isset($this->set_tablespace)) {
      $value = c_database_string::ALL_IN_TABLESPACE . ' ' . $value;

      if (!is_null($this->owned_by)) {
        $value .= ' ' . $this->p_do_build_owned_by();
      }

      $value .= ' ' . $this->p_do_build_set_tablespace();

      if (is_bool($this->no_wait)) {
        $value .= ' ' . $this->p_do_build_no_wait();
      }
    }
    else {
      $value = $if_exists . $only . $value . ' ' . $wildcard;
      if (isset($this->action_add_column)) {
        $value .= $this->p_do_build_action_add_column();
      }
      else if (isset($this->action_alter_column)) {
        $value .= $this->p_do_build_action_alter_column();
      }
      else if (isset($this->action_alter_column_options)) {
        $value .= $this->p_do_build_action_alter_column_options();
      }
      else if (isset($this->action_alter_column_reset)) {
        $value .= $this->p_do_build_action_alter_column_reset();
      }
      else if (isset($this->action_alter_column_set)) {
        $value .= $this->p_do_build_action_alter_column_set();
      }
      else if (isset($this->action_cluster_on)) {
        $value .= $this->p_do_build_action_cluster_on();
      }
      else if (isset($this->action_constraint)) {
        $value .= $this->p_do_build_action_constraint();
      }
      else if (isset($this->action_disable_rule)) {
        $value .= $this->p_do_build_action_disable_rule();
      }
      else if (isset($this->action_disable_trigger)) {
        $value .= $this->p_do_build_action_disable_trigger();
      }
      else if (isset($this->action_drop_columm)) {
        $value .= $this->p_do_build_action_drop_columm();
      }
      else if (isset($this->action_enable_rule)) {
        $value .= $this->p_do_build_action_enable_rule();
      }
      else if (isset($this->action_enable_trigger)) {
        $value .= $this->p_do_build_action_enable_trigger();
      }
      else if (isset($this->action_inherit)) {
        $value .= $this->p_do_build_action_inherit();
      }
      else if (isset($this->action_not_of)) {
        $value .= $this->p_do_build_action_not_of();
      }
      else if (isset($this->action_options)) {
        $value .= $this->p_do_build_action_options();
      }
      else if (isset($this->action_owner_to)) {
        $value .= $this->p_do_build_action_owner_to();
      }
      else if (isset($this->action_replica_identity)) {
        $value .= $this->p_do_build_action_replica_identity();
      }
      else if (isset($this->action_row_level_security)) {
        $value .= $this->p_do_build_action_row_level_security();
      }
      else if (isset($this->action_set_logged)) {
        $value .= $this->p_do_build_action_set_logged();
      }
      else if (isset($this->action_set_of)) {
        $value .= $this->p_do_build_action_set_of();
      }
      else if (isset($this->action_set_oids)) {
        $value .= $this->p_do_build_action_set_oids();
      }
      else if (isset($this->action_set_tablespace)) {
        $value .= $this->p_do_build_action_set_tablespace();
      }
      else if (isset($this->action_without_cluster)) {
        $value .= $this->p_do_build_action_without_cluster();
      }
      else if (isset($this->attach_partition)) {
        $value .= $this->p_do_build_attach_partition();
      }
      else if (isset($this->detach_partition)) {
        $value .= $this->p_do_build_detach_partition();
      }
      else {
        unset($value);
        unset($if_exists);
        unset($only);
        unset($wildcard);
        return new c_base_return_false();
      }
    }
    unset($if_exists);
    unset($only);
    unset($wildcard);

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
