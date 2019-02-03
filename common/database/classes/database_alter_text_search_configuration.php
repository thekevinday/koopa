<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/enumerations/database_mapping_for.php');

require_once('common/database/traits/database_mapping_for.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_with_dictionary.php');


/**
 * The class for building and returning a Postgresql TEXT SEARCH CONFIGURATION query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altertextsearchconfiguration.html
 */
class c_database_alter_text_search_configuration extends c_database_query {
  use t_database_mapping_for;
  use t_database_name;
  use t_database_owner_to;
  use t_database_rename_to;
  use t_database_set_schema;
  use t_database_with_dictionary;

  protected const p_QUERY_COMMAND = 'alter text search configuration';


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->mapping_for     = NULL;
    $this->name            = NULL;
    $this->owner_to        = NULL;
    $this->rename_to       = NULL;
    $this->set_schema      = NULL;
    $this->with_dictionary = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->mapping_for);
    unset($this->name);
    unset($this->owner_to);
    unset($this->rename_to);
    unset($this->set_schema);
    unset($this->with_dictionary);

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
    if (isset($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (isset($this->set_schema)) {
      $value .= ' ' . $this->p_do_build_set_schema();
    }
    else if (isset($this->mapping_for['type'])) {
      $value .= ' ' . $this->p_do_build_mapping_for();

      if ($this->mapping_for['type'] === e_database_mapping_for::REPLACE) {
        if (isset($this->with_dictionary) && count($this->with_dictionary) == 1) {
          // when mapping is REPLACE, there should only be a single with_dictionary entry.
          $value .= ' ' . $this->p_do_build_with_dictionary();
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
      }
      else if ($this->mapping_for['type'] !== e_database_mapping_for::DROP) {
        if (isset($this->with_dictionary)) {
          $value .= ' ' . $this->p_do_build_with_dictionary();
        }
        else {
          unset($value);
          return new c_base_return_false();
        }
      }
    }
    else if (isset($this->mapping_replace)) {
      $value .= ' ' . $this->p_do_build_mapping_replace();
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
