<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_as_data_type.php');
require_once('common/database/traits/database_cache.php');
require_once('common/database/traits/database_cycle.php');
require_once('common/database/traits/database_if_exists.php');
require_once('common/database/traits/database_increment_by.php');
require_once('common/database/traits/database_max_value.php');
require_once('common/database/traits/database_min_value.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owned_by.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_restart_with.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_start_with.php');


/**
 * The class for building and returning a Postgresql ALTER SEQUENCE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altersequence.html
 */
class c_database_alter_sequence extends c_database_query {
  use t_database_as_data_type;
  use t_database_cache;
  use t_database_cycle;
  use t_database_if_exists;
  use t_database_increment_by;
  use t_database_max_value;
  use t_database_min_value;
  use t_database_name;
  use t_database_owned_by;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_restart_with;
  use t_database_set_schema;
  use t_database_start_with;

  protected const p_QUERY_COMMAND = 'alter sequence';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->as_data_type = NULL;
    $this->cache        = NULL;
    $this->cycle        = NULL;
    $this->if_exists    = NULL;
    $this->increment_by = NULL;
    $this->max_value    = NULL;
    $this->min_value    = NULL;
    $this->name         = NULL;
    $this->owned_by     = NULL;
    $this->owner_to     = NULL;
    $this->rename_to    = NULL;
    $this->restart_with = NULL;
    $this->set_schema   = NULL;
    $this->start_with   = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->as_data_type);
    unset($this->cache);
    unset($this->cycle);
    unset($this->if_exists);
    unset($this->increment_by);
    unset($this->max_value);
    unset($this->min_value);
    unset($this->name);
    unset($this->owned_by);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->restart_with);
    unset($this->set_schema);
    unset($this->start_with);

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

    $value = $this->p_do_build_name();
    if (isset($this->if_exists)) {
      $value = $this->p_do_build_if_exists() . ' ' . $value;
    }

    if (isset($this->owner_to)) {
        $value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->rename_to)) {
        $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (isset($this->set_schema)) {
        $value .= ' ' . $this->p_do_build_set_schema();
    }
    else {
      if (isset($this->as_data_type)) {
        $value .= ' ' . $this->p_do_build_as_data_type();
      }

      if (isset($this->increment_by)) {
        $value .= ' ' . $this->p_do_build_increment_by();
      }

      if (isset($this->min_value)) {
        $value .= ' ' . $this->p_do_build_min_value();
      }

      if (isset($this->max_value)) {
        $value .= ' ' . $this->p_do_build_max_value();
      }

      if (isset($this->start_with)) {
        $value .= ' ' . $this->p_do_build_start_with();
      }

      if (isset($this->restart_with)) {
        $value .= ' ' . $this->p_do_build_restart_with();
      }

      if (isset($this->cache)) {
        $value .= ' ' . $this->p_do_build_cache();
      }

      if (isset($this->cycle)) {
        $value .= ' ' . $this->p_do_build_cycle();
      }

      if (isset($this->owned_by)) {
        $value .= ' ' . $this->p_do_build_owned_by();
      }
    }

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
