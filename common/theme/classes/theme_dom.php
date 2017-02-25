<?php
/**
 * @file
 * Provides a class for managing HTML DOM.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');


/**
 * A generic class for managing HTML DOM.
 *
 * This class uses DOMDocument and assigns a type of doctype HTML.
 * This is not technically HTML5, but HTML5 must be used to make sure the dom functionality operates as expected.
 *
 * The purpose of this class is to provide a context-based language for later renderring of content, be it HTML5 or otherwise.
 *
 * It is primarily meant to help simplify management of the DOMDocument and DOMNode classes without re-implementing anything.
 */
class c_theme_dom extends DOMDocument {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct('1.0', 'UTF-8');
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
  }

  /**
   * Initialize the object to explicit HTML5 settings.
   */
  public function initialize_as_html() {
    $this->preserveWhiteSpace = TRUE;
    $this->formatOutput = FALSE;
    @$this->loadHTML('<!DOCTYPE html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body></body>');
  }

  /**
   * Changes the element from one type to another.
   *
   * @param DOMNode $element
   *   The element whose type will be changed.
   * @param string $type
   *   The new element type to use.
   *
   * @return DOMNode|bool
   *   The changed element on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::pr_change_element()
   */
  public function change_element($element, $type) {
    if (!($element instanceof DOMNode)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'element', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($type) || empty($type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'type', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return c_them_return_dom_node::s_new($this->pr_change_element($element, $type));
  }


  /**
   * Change all elements of a given element type to another type.
   *
   * @param string $type_old
   *   The old element type to be replaced.
   * @param string $type_new
   *   The new element type to use.
   *
   * @return bool
   *   TRUE on success.
   *   Otherwise FALSE with error bit set is returned.
   *
   * @see: self::pr_change_elements()
   */
  public function change_elements($type_old, $type_new) {
    if (!is_string($type_old) || strlen($type_old) == 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'type_old', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($type_new) || strlen($type_empty) == 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'type_empty', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!($this->content instanceof DOMNode)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->content', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    return $this->pr_change_elements($type_old, $type_new, $this->content);
  }

  /**
   * Removes the given element from its parent.
   *
   * This preserves child elements.
   * To remove entirely, use removeChild() directly.
   *
   * @param DOMNode $element
   *   The object to convert to markup text.
   * @param bool $preserve_children
   *   (optional) If TRUE, children are re-attached to the parent node to preserve their location in the markup.
   *   If FALSE, the children remain attached to the removed element.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE without error bit set is returned when unable to remove element.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::pr_remove_element()
   * @see: DOMDocument::removeChild()
   */
  public function remove_element($element, $preserve_children = TRUE) {
    if (!($element instanceof DOMNode)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'element', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($preserve_children)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'preserve_children', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($this->pr_remove_element($element, $preserve_children)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Remove all elements of a given element type.
   *
   * This preserves child elements.
   * To remove elements entirely, use removeChild() directly.
   *
   * @param string $type
   *   The new element type to use.
   * @param bool $preserve_children
   *   (optional) If TRUE, children are re-attached to the parent node to preserve their location in the markup.
   *   If FALSE, the children remain attached to the removed element.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE without error bit set is returned when unable to remove element.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::pr_remove_elements()
   * @see: DOMDocument::removeChild()
   */
  public function remove_elements($type, $preserve_children = TRUE) {
    if (!is_string($type) || strlen($type) == 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'type', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($preserve_children)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'preserve_children', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!($this->content instanceof DOMNode)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->content', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if ($this->pr_remove_elements($type, $this->content, $preserve_children)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Converts the element into markup
   *
   * @param bool $include_tag
   *   When TRUE, the head tag itself will be included in the output.
   *   When FALSE, only the contents of the tag will be included in the output.
   * @param DOMNode $parent
   *   The object to operate on.
   *
   * @return string|bool
   *   The markup text that the object was converted from.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_get_markup($include_tag, $parent) {
    if ($include_tag) {
      return $this->saveHTML($parent);
    }

    $markup = '';
    if ($parent->hasChildNodes() > 0) {
      foreach ($parent->childNodes as $child) {
        $markup .= $this->saveHTML($child);
      }
    }
    unset($child);

    return $markup;
  }

  /**
   * Changes the element from one type to another.
   *
   * @param DOMNode $element
   *   The element whose type will be changed.
   * @param string $type
   *   The new element type to use.
   *
   * @return bool
   *   The changed element on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::change_element()
   */
  protected function pr_change_element($element, $type) {
    $parent = $element->parentNode;
    $new = $this->createElement($type);

    if (!($new instanceof DOMNode)) {
      return FALSE;
    }

    if ($element->hasAttributes()) {
      foreach ($element->attributes as $attribute) {
        $new->setAttribute($attribute->name, $attribute->value);
      }
    }

    if ($element->hasChildNodes()) {
      foreach ($element->childNodes as $child) {
        $new->appendChild($child->cloneNode(TRUE));
      }
      unset($child);
    }

    if ($parent instanceOf DOMNode) {
      $child = $parent->replaceChild($new, $element);
    }
    else {
      $this->appendChild($element);
      $child = $this->replaceChild($new, $element);

      if ($child instanceof DOMNode) {
        $parent = $child->parentNode;

        if ($parent instanceOf DOMNode) {
          $this->removeChild($child);
        }
      }
      else {
        $this->removeChild($element);
      }
    }

    unset($new);
    unset($parent);

    if ($child instanceOf DOMNode) {
      return $child;
    }

    return FALSE;
  }

  /**
   * Change all elements of a given element type to another type.
   *
   * @param string $type
   *   The new element type to operate on.
   * @param DOMNode $parent
   *   The object to operate on.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::change_elements()
   */
  protected function pr_change_elements($type, $parent) {
    $result = TRUE;

    $elements = $parent->getElementsByTagName($type);
    foreach ($elements as $element) {
      if ($element instanceof DOMNode) {
        $result = $this->pr_change_element($element, $type);
      }
      else {
        $result = FALSE;
      }

      if (!$result) break;
    }
    unset($element);
    unset($elements);

    return $result;
  }

  /**
   * Removes the given element from its parent.
   *
   * This preserves child elements.
   * To remove entirely, use removeChild() directly.
   *
   * @param DOMNode $element
   *   The object to convert to markup text.
   * @param bool $preserve_children
   *   (optional) If TRUE, children are re-attached to the parent node to preserve their location in the markup.
   *   If FALSE, the children remain attached to the removed element.
   *
   * @return bool
   *   The removed element on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::remove_element()
   * @see: DOMDocument::removeChild()
   */
  protected function pr_remove_element($element, $preserve_children = TRUE) {
    $parent = $element->parentNode;

    if (!($parent instanceof DOMNode)) {
      unset($parent);
      return FALSE;
    }

    if ($preserve_children && $element->hasChildNodes()) {
      $children = array();

      foreach ($element->childNodes as $child) {
        $children[] = $child;
      }
      unset($child);

      foreach ($children as $child) {
        $removed_child = $element->removeChild($child);

        if (is_object($removed_child)) {
          $parent->insertBefore($removed_child, $element);
        }
      }
      unset($child);
      unset($removed_child);
      unset($children);
    }

    $child = $parent->removeChild($element);
    unset($parent);

    if ($child instanceof DOMNode) {
      return $child;
    }
    unset($child);

    return FALSE;
  }

  /**
   * Remove all elements of a given element type.
   *
   * This preserves child elements.
   * To remove elements entirely, use removeChild() directly.
   *
   * @param string $type
   *   The new element type to operate on.
   * @param DOMNode $parent
   *   The object to operate on.
   * @param bool $preserve_children
   *   (optional) If TRUE, children are re-attached to the parent node to preserve their location in the markup.
   *   If FALSE, the children remain attached to the removed element.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::remove_elements()
   * @see: DOMDocument::removeChild()
   */
  protected function pr_remove_elements($type, $parent, $preserve_children = TRUE) {
    $result = TRUE;

    $elements = $parent->getElementsByTagName($type);
    foreach ($elements as $element) {
      $result = $this->pr_remove_element($element, $preserve_children);

      if (!$result) break;
    }
    unset($elements);

    return $result;
  }
}

/**
 * A return class whose value is represented as a c_theme_dom.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_c_theme_dom extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param DOMNode $value
   *   Any value so long as it is a c_theme_dom.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_value($value) {
    if (!$value instanceof c_theme_dom) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof c_theme_dom)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return c_theme_dom $value
   *   The value c_theme_dom stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof c_theme_dom)) {
      $this->value = new c_theme_dom();
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a DOMNode.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_dom_node extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param DOMNode $value
   *   Any value so long as it is a DOMNode.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_value($value) {
    if (!$value instanceof DOMNode) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof DOMNode)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return DOMNode $value
   *   The value DOMNode stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof DOMNode)) {
      $this->value = new DOMNode();
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a DOMComment.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_dom_comment extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param DOMComment $value
   *   Any value so long as it is a DOMComment.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_value($value) {
    if (!$value instanceof DOMComment) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof DOMComment)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return DOMComment $value
   *   The value DOMComment stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof DOMComment)) {
      $this->value = new DOMComment();
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a DOMElement.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_dom_element extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param DOMElement $value
   *   Any value so long as it is a DOMElement.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_value($value) {
    if (!$value instanceof DOMElement) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof DOMElement)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return DOMElement $value
   *   The value DOMElement stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof DOMElement)) {
      $this->value = new DOMElement();
    }

    return $this->value;
  }
}

/**
 * A return class whose value is represented as a DOMText.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_dom_text extends c_base_return_value {
  use t_base_return_value_exact;

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
   * Assign the value.
   *
   * @param DOMText $value
   *   Any value so long as it is a DOMText.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
   */
  public function set_value($value) {
    if (!$value instanceof DOMText) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof DOMText)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return DOMText $value
   *   The value DOMText stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof DOMText)) {
      $this->value = new DOMText();
    }

    return $this->value;
  }
}
