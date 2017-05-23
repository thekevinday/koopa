<?php
/**
 * @file
 * Provides a class for managing addresses.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A class for managing human name of a user.
 */
class c_base_address_email extends c_base_return {
  private $name;
  private $domain;

  private $is_private;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->name   = NULL;
    $this->domain = NULL;

    $this->is_private = TRUE;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->domain);

    unset($this->is_private);

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
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Set the name.
   *
   * @param string $name
   *   The user name of the e-mail address.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->name = $name;
    return new c_base_return_true();
  }

  /**
   * Set the domain name.
   *
   * @param string $domain
   *   The domain name of the e-mail address.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_domain($domain) {
    if (!is_string($domain)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'domain', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->domain = $domain;
    return new c_base_return_true();
  }

  /**
   * Get the name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_name() {
    if (!is_string($this->name)) {
      $this->name = '';
    }

    return c_base_return_string::s_new($this->name);
  }

  /**
   * Get the domain name.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function get_domain() {
    if (!is_string($this->domain)) {
      $this->domain = '';
    }

    return c_base_return_string::s_new($this->domain);
  }

  /**
   * Get the is private setting.
   *
   * @param bool|null $is_private
   *   When a boolean, this is assigned as the current is private setting.
   *   When NULL, the private setting is returned.
   *
   * @return c_base_return_bool|c_base_return_status
   *   When $is_private is NULL, is content boolean setting on success.
   *   FALSE with error bit is set on error.
   */
  public function is_private($is_private = NULL) {
    if (!is_null($is_private) && !is_bool($is_private)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'is_private', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($is_private)) {
      if (!is_bool($this->is_private)) {
        $this->is_private = FALSE;
      }

      return c_base_return_bool::s_new($this->is_private);
    }

    $this->is_private = $is_private;
    return new c_base_return_true();
  }
}
