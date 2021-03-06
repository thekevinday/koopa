<?php
/**
 * @file
 * Provides a class for specific Postgesql query: ALTER AGGREGATE.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/classes/database_query.php');

require_once('common/database/traits/database_name.php');
require_once('common/database/traits/database_rename_to.php');
require_once('common/database/traits/database_owner_to.php');
require_once('common/database/traits/database_set_schema.php');

/**
 * The class for building and returning a Postgresql ALTER AGGREGATE query string.
 *
 * When no argument mode is specified, then a wildcard * is auto-provided for the aggregate_signature parameter.
 *
 * @todo: review this implementation, may be outdated.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-alteraggregate.html
 */
class c_database_alter_aggregate extends c_database_query {
  use t_database_name;
  use t_database_rename_to;
  use t_database_owner_to;
  use t_database_set_schema;

  protected const p_QUERY_COMMAND = 'alter aggregate';

  // @todo: move these into their own traits.
  protected $aggregate_signatures;
  protected $order_by_signatures;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name       = NULL;
    $this->rename_to  = NULL;
    $this->owner_to   = NULL;
    $this->set_schema = NULL;

    $this->aggregate_signatures = NULL;
    $this->order_by_signatures  = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->rename_to);
    unset($this->owner_to);
    unset($this->set_schema);

    unset($this->aggregate_signatures);
    unset($this->order_by_signatures);

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
   * Assign the settings.
   *
   * @param c_database_argument_aggregate_signature|null $aggregate_signature
   *   The aggregate signatures to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_aggregate_signature($aggregate_signature) {
    if (is_null($aggregate_signature)) {
      $this->aggregate_signatures = NULL;
      return new c_base_return_true();
    }

    if ($aggregate_signature instanceof c_database_argument_aggregate_signature) {
      if (!is_array($this->aggregate_signatures)) {
        $this->aggregate_signatures = [];
      }

      $this->aggregate_signatures[] = $aggregate_signature;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'aggregate_signature', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assign the settings.
   *
   * @param c_database_argument_aggregate_signature_base|null $order_by_signature
   *   The order by aggregate signature to use.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_order_by_signature($order_by_signature) {
    if (is_null($order_by_signature)) {
      $this->order_by_signature = NULL;
      return new c_base_return_true();
    }

    if ($order_by_signature instanceof c_database_argument_aggregate_signature_base) {
      if (!is_array($this->order_by_signature)) {
        $this->order_by_signatures = [];
      }

      $this->order_by_signatures[] = $order_by_signature;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'order_by_signature', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the aggregate signatures.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of aggregate signatures or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_aggregate_signature() {
    if (is_null($this->aggregate_signatures)) {
      return new c_base_return_null();
    }

    if (is_array($this->aggregate_signatures)) {
      return c_base_return_array::s_new($this->aggregate_signatures);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'aggregate_signatures', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the total aggregate signatures.
   *
   * @return c_base_return_int
   *   The total number of aggregate signatures.
   *   0 with the error bit set is returned on error.
   */
  public function get_aggregate_signature_count() {
    if (is_null($this->aggregate_signatures)) {
      return new c_base_return_null();
    }

    if (is_array($this->aggregate_signatures)) {
      return new c_base_return_int(count($this->aggregate_signatures));
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'aggregate_signatures', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_value(0, 'c_base_return_int', $error);
  }

  /**
   * Get the order by aggregate signatures.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of order by aggregate signatures or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_order_by_signature() {
    if (is_null($this->order_by_signatures)) {
      return new c_base_return_null();
    }

    if (is_array($this->order_by_signatures)) {
      return c_base_return_array::s_new($this->order_by_signatures);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'order_by_signatures', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the total order by signatures.
   *
   * @return c_base_return_int
   *   The total number of aggregate signatures.
   *   0 with the error bit set is returned on error.
   */
  public function get_order_by_signature_count() {
    if (is_null($this->order_by_signatures)) {
      return new c_base_return_null();
    }

    if (is_array($this->aggregate_signatures)) {
      return new c_base_return_int(count($this->aggregate_signatures));
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'aggregate_signatures', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_value(0, 'c_base_return_int', $error);
  }

  /**
   * Implements do_build().
   */
  public function do_build() {
    if (is_null($this->name)) {
      return new c_base_return_false();
    }

    $aggregate_signatures = NULL;
    if (!is_array($this->aggregate_modes) || empty($this->aggregate_modes)) {
      $aggregate_signatures = ' *';
    }
    else {
      foreach ($this->aggregate_signatures as $aggregate_signature) {
        if ($aggregate_signature instanceof c_database_argument_aggregate_signature) {
          if (is_null($aggregate_signatures)) {
            $aggregate_signatures = '';
          }
          else {
            $aggregate_signatures .= ', ';
          }

          $aggregate_signature->do_build_argument();
          $aggregate_signatures .= $aggregate_signature->get_value_exact();
        }
      }
      unset($aggregate_signature);

      $order_by_signatures = NULL;
      foreach ($this->order_by_signatures as $order_by_signature) {
        if ($order_by_signature instanceof c_database_argument_aggregate_signature_base) {
          if (is_null($order_by_signatures)) {
            $order_by_signatures = '';
          }
          else {
            $order_by_signatures .= ', ';
          }

          $order_by_signature->do_build_argument();
          $aggregate_signatures .= $order_by_signature->get_value_exact();
        }
      }
      unset($order_by_signature);

      if (isset($aggregate_signatures)) {
        $aggregate_signatures = ' (' . $aggregate_signatures;
        if (isset($order_by_signatures)) {
          $aggregate_signatures = ' ' . c_database_string::ORDER . ' ' . c_database_string::BY . ' ' . $order_by_signatures . '';
        }
        $aggregate_signatures .= ')';
      }
      unset($order_by_signatures);
    }

    $value = $this->p_do_build_name() . ' ';
    if (isset($this->rename_to)) {
      $value .= $aggregate_signatures . ' ' . $this->p_do_build_rename_to();
    }
    else if (isset($this->owner_to)) {
      $value .= $aggregate_signatures . ' ' . $this->p_do_build_owner_to();
    }
    else if (isset($this->set_schema)) {
      $value .= $aggregate_signatures . ' ' . $this->p_do_build_set_schema();
    }
    else {
      unset($aggregate_signatures);
      unset($value);
      return new c_base_return_false();
    }
    unset($aggregate_signatures);

    $this->value = static::p_QUERY_COMMAND;
    $this->value .= ' ' . $value;
    unset($value);

    return new c_base_return_true();
  }
}
