<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER DATABASE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/enumerations/database_reset.php');
require_once('common/database/enumerations/database_set.php');

require_once('common/database/classes/database_query.php');
require_once('common/database/classes/database_string.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_set_tablespace.php');
require_once('common/database/traits/database_set.php');
require_once('common/database/traits/database_reset.php');

/**
 * The class for building and returning a Postgresql ALTER DATABASE query string.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alterdatabase.html
 */
class c_database_alter_database extends c_database_query {
  use t_database_name;
  use t_database_rename_to;
  use t_database_owner_to;
  use t_database_set;
  use t_database_set_tablespace;
  use t_database_reset;

  protected const pr_QUERY_COMMAND = 'alter database';

  protected $option;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name           = NULL;
    $this->rename_to      = NULL;
    $this->owner_to       = NULL;
    $this->set            = NULL;
    $this->set_tablespace = NULL;
    $this->reset          = NULL;

    // TODO: it may be better (and more consistent) to convert option into a trait, just like all of the others.
    $this->option = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->rename_to);
    unset($this->owner_to);
    unset($this->set);
    unset($this->set_tablespace);
    unset($this->reset);

    unset($this->option);

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
   * Set the option.
   *
   * @param c_database_argument_database_option|null $option
   *   The database options to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_option($option) {
    if (is_null($option)) {
      $this->option = NULL;
      return new c_base_return_true();
    }

    if (!($option instanceof c_database_argument_database_option)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->option = $option;
    return new c_base_return_true;
  }

  /**
   * Get assigned option.
   *
   * @return c_database_argument_database_option|c_base_return_null
   *   The assigned option or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_option() {
    if (is_null($this->option)) {
      return new c_base_return_null();
    }

    if ($this->option instanceof c_database_argument_database_option) {
      return clone($this->option);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    // the database name is required.
    if (!is_string($this->name)) {
      return new c_base_return_false();
    }

    $this->value = static::pr_QUERY_COMMAND;
    $this->value .= ' ' . $this->name;

    if ($this->option instanceof c_database_argument_database_option) {
      $this->option->do_build_argument();
      $this->value .= ' ' . $this->option->get_value_exact();
    }
    else if (is_string($this->rename_to)) {
      $this->value .= ' ' . $this->p_do_build_rename_to();
    }
    else if (is_string($this->owner_to)) {
      $this->value .= ' ' . $this->p_do_build_owner_to();
    }
    else if (is_string($this->set_tablespace)) {
      $this->value .= ' ' . $this->p_do_build_set_tablespace();
    }
    else if (is_array($this->set)) {
        $this->value .= ' ' . $this->p_do_build_set();
    }
    else if (is_array($this->reset)) {
      $this->value .= ' ' . $this->p_do_build_reset();
    }

    return new c_base_return_true();
  }
}
