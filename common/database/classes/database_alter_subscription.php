<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER COALATION.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_connection.php');
require_once('common/database/traits/database_disable.php');
require_once('common/database/traits/database_enable.php');
require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_refresh_publication.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_set_configuration_parameter.php');
require_once('common/database/traits/database_set_publication_name.php');
require_once('common/database/traits/database_set_schema.php');
require_once('common/database/traits/database_with_publication_option.php');
require_once('common/database/traits/database_with_refresh_option.php');


/**
 * The class for building and returning a Postgresql ALTER SUBSCRIPTION query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-altersubsciption.html
 */
class c_database_alter_subscription extends c_database_query {
  use t_database_connection;
  use t_database_disable;
  use t_database_enable;
  use t_database_name;
  use t_database_owner_to;
  use t_database_refresh_publication;
  use t_database_rename_to;
  use t_database_set_configuration_parameter;
  use t_database_set_publication_name;
  use t_database_set_schema;
  use t_database_with_publication_option;
  use t_database_with_refresh_option;

  protected const p_QUERY_COMMAND = 'alter subsciption';

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->connection                  = NULL;
    $this->disable                     = NULL;
    $this->enable                      = NULL;
    $this->name                        = NULL;
    $this->owner_to                    = NULL;
    $this->refresh_publication         = NULL;
    $this->rename_to                   = NULL;
    $this->set_configuration_parameter = NULL;
    $this->set_publication_name        = NULL;
    $this->set_schema                  = NULL;
    $this->with_publication_option     = NULL;
    $this->with_refresh_option         = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->connection);
    unset($this->disable);
    unset($this->enable);
    unset($this->name);
    unset($this->owner_to);
    unset($this->refresh_publication);
    unset($this->rename_to);
    unset($this->set_configuration_parameter);
    unset($this->set_publication_name);
    unset($this->set_schema);
    unset($this->with_publication_option);
    unset($this->with_refresh_option);

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
    if (isset($this->connection)) {
      $value .= ' ' . $this->p_do_build_connection();
    }
    else if (isset($this->set_publication_name)) {
      $value .= ' ' . $this->p_do_build_set_publication_name();

      if (isset($this->with_publication_option)) {
        $value .= ' ' . $this->p_do_build_with_publication_option();
      }
    }
    else if (isset($this->refresh_publication)) {
      $value .= ' ' . $this->p_do_build_refresh_publication();

      if (isset($this->with_refresh_option)) {
        $value .= ' ' . $this->p_do_build_with_refresh_option();
      }
    }
    else if (isset($this->enable)) {
      $value .= ' ' . $this->p_do_build_enable();
    }
    else if (isset($this->disable)) {
      $value .= ' ' . $this->p_do_build_disable();
    }
    else if (isset($this->set_configuration_parameter)) {
      $value .= ' ' . $this->p_do_build_set_configuration_parameter();
    }
    else if (isset($this->owner_to)) {
      $value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->rename_to)) {
      $value .= ' ' . $this->p_do_build_rename_to();
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
