<?php
/**
 * @file
 * Provides a class for managing the menus.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_rfc_string.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_database.php');
require_once('common/base/classes/base_session.php');
require_once('common/base/classes/base_array.php');

/**
 * A generic class for managing a menu.
 *
 * This can be converted to HTML <nav>, <menu>, or even breadcrumbs.
 */
class c_base_menu extends c_base_rfc_string {

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
   * Build the menu structure.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   * @param array|null $items
   *   (optional) An array of menu items to use for the menu.
   *   How this is treated is left to the implementing class.
   *   This is generally intended to be used for certain types of dynamic content, such as breadcrumbs.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   An HTML tag containing the menu.
   *   FALSE without error bit set is returned if menu is undefined.
   *   FALSE with error bit set is returned on error.
   */
  public function do_build(&$http, &$database, &$session, $settings, $items = NULL) {
    if (!($http instanceof c_base_http)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'http', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    elseif (!($database instanceof c_base_database)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'database', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    elseif (!($session instanceof c_base_session)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'session', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    elseif (!is_array($settings)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'settings', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    elseif (!is_null($items) && !is_array($items)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'items', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return new c_base_return_false();
  }
}

/**
 * A generic class for managing a menu item.
 *
 * This can also be used as a menu of items.
 */
class c_base_menu_item extends c_base_array {
  private $text;
  private $uri;
  private $attributes;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->text       = NULL;
    $this->uri        = NULL;
    $this->attributes = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->text);
    unset($this->uri);
    unset($this->attributes);

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
    return self::p_s_value_exact($return, __CLASS__, []);
  }

  /**
   * Assign the attribute text.
   *
   * @param string|int $text
   *   The text representing the item name.
   *   May be a number to represent a code used for converting itno a language-specific text.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with the error bit set on error.
   */
  public function set_text($text) {
    if (!is_int($text) && !is_string($text)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->text = $text;
    return new c_base_return_true();
  }

  /**
   * Assign or unassign the menu items URI.
   *
   * @param string|array|null $uri
   *   The uri string or array to assign.
   *   Set to NULL to remove any existing uri string.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with the error bit set on error.
   */
  public function set_uri($uri) {
    if (!is_null($uri) && !is_string($uri) && !is_array($uri)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'uri', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->uri = $uri;
    return new c_base_return_true();
  }

  /**
   * Assign a single attribute.
   *
   * @param int $attribute
   *   The attribute id to assign.
   * @param $value
   *   The attribute value to assign.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with the error bit set on error.
   */
  public function set_attribute($attribute, $value) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->attributes)) {
      $this->attributes = [];
    }

    $this->attributes[$attribute] = $value;
    return new c_base_return_true();
  }

  /**
   * Assign an array of attributes.
   *
   * @param array $attributes
   *   An array of attributes to assign.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with the error bit set on error.
   */
  public function set_attributes($attributes) {
    if (!is_array($attributes)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attributes', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->attributes = $attributes;
    return new c_base_return_true();
  }

  /**
   * Assign the item at a specific index in the array.
   *
   * @param c_base_menu_item $item
   *   An instance of c_base_menu_item to assign.
   *   This object is cloned.
   * @param int|string|NULL $index
   *   An index to assign a specific value to.
   *   Set to NULL to append item.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_item($item, $index = NULL) {
    if (!($item instanceof c_base_menu_item)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'item', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($index) && !is_int($index) && !is_string($index)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->items)) {
      $this->items = [];
    }

    if (is_null($index)) {
      $this->items[] = clone($item);
    }
    else {
      $this->items[$index] = clone($item);
    }

    return new c_base_return_true();
  }

  /**
   * Assign the items array.
   *
   * @param c_base_array $items
   *   Replace the current array object with this object.
   *   This object is cloned.
   *   If NULL, then a new array is created.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_items($items) {
    if (!is_null($items) && !($items instanceof c_base_menu_item)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'items', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->items = clone($items);
    return new c_base_return_true();
  }

  /**
   * Get any text assigned to this menu item.
   *
   * @return c_base_return_int|return c_base_return_string
   *   A URI string is returned or an integer for language-specific conversion.
   *   An empty string with the error bit set is returned on error.
   */
  public function get_text() {
    if (!is_int($this->text) && !is_string($this->text)) {
      return c_base_return_string::s_new('');
    }

    if (is_int($this->text)) {
      return c_base_return_int::s_new($this->text);
    }

    return c_base_return_string::s_new($this->text);
  }

  /**
   * Get any uri assigned to this menu item.
   *
   * @return c_base_return_array|c_base_return_string|c_base_return_null
   *   A URI string or URI array, if defined.
   *   Otherwise NULL is returned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_uri() {
    if (!is_array($this->uri) && !is_string($this->uri)) {
      return new c_base_return_null();
    }

    if (is_array($this->uri)) {
      return c_base_return_array::s_new($this->uri);
    }

    return c_base_return_string::s_new($this->uri);
  }

  /**
   * Get a single attribute assigned to this menu item.
   *
   * @param int $attribute
   *   The attribute to get.
   *
   * @return c_base_return_value
   *   The value assigned to the attribute (the data type is different per attribute).
   *   FALSE is returned if the element does not exist.
   *   FALSE with error bit set is returned on error.
   */

  public function get_attribute($attribute) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->attributes) || !array_key_exists($attribute, $this->attributes)) {
      return new c_base_return_false();
    }

    return c_base_return_value::s_new($this->attributes[$attribute]);
  }

  /**
   * Get all attributes assigned to this menu item.
   *
   * @return c_base_return_array
   *   An array of assigned attributes.
   *   An empty array with the error bit set on error.
   */
  public function get_attributes() {
    if (!is_array($this->attributes)) {
      return c_base_return_array::s_new([]);
    }

    return c_base_return_array::s_new($this->attributes);
  }

  /**
   * Return the item at a specific index in the array.
   *
   * @param string $index
   *   An index to assign a specific value to.
   *
   * @return c_base_return_status|c_base_menu_item
   *   The (cloned) value object.
   *   FALSE without error bit set is returned if $index us not defined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_item($index) {
    if (!is_string($index) || empty($index)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->items) || !array_key_exists($index, $this->items)) {
      return new c_base_return_false();
    }

    return clone($this->items[$index]);
  }

  /**
   * Return the items array.
   *
   * @return c_base_return_array
   *   The array stored within this class.
   *   An empty array with error bit set is returned on error.
   */
  public function get_items() {
    if (!is_null($this->items) && !is_array($this->items)) {
      return c_base_return_array::s_new([]);
    }

    return c_base_return_array::s_new($this->items);
  }

  /**
   * Return the total number of items in the array.
   *
   * @return c_base_return_int
   *   A positive integer.
   *   0 with the error bit set is returned on error.
   */
  public function get_items_count() {
    if (empty($this->items)) {
      return 0;
    }

    return count($this->items);
  }

  /**
   * Return the array keys assigned to the array.
   *
   * @return c_base_return_array
   *   An array of array keys.
   *   An empty array with the error bit set is returned on error.
   */
  public function get_items_keys() {
    if (empty($this->items)) {
      return [];
    }

    return array_keys($this->items);
  }
}
