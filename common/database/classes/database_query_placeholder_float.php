<?php
/**
 * @file
 * Provides a class for specific Postgesql query placeholder generation.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/database/interfaces/database_query_placeholder.php');

require_once('common/database/traits/database_query_placeholder.php');


/**
 * The class for managing an float query placeholder.
 */
class c_database_query_placeholder_float extends c_base_return_float implements i_database_query_placeholder {
  use t_database_placeholder {
    t_database_placeholder::has_value insteadof t_base_return_value;
  }


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->assigned = FALSE;
    $this->id       = NULL;
    $this->prefix   = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->assigned);
    unset($this->id);
    unset($this->pefix);

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
   * Custom override of set_value for assigning a float value.
   *
   * @see: t_base_return_value::set_value()
   */
  public function set_value($value) {
    if (!is_float($value)) {
      return FALSE;
    }

    $this->value = $value;
    $this->assigned = TRUE;
    return TRUE;
  }
}
