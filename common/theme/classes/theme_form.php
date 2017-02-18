<?php
/**
 * @file
 * Provides a class for managing HTML5 Markup.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 *
 * @see: https://www.w3.org/TR/html5/
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

// global:
//accesskey
//class
//contenteditable
//dir
//hidden
//id
//lang
//spellcheck
//style
//tabindex
//title
//translate
//onabort
//onblur*
//oncancel
//oncanplay
//oncanplaythrough
//onchange
//onclick
//oncuechange
//ondblclick
//ondurationchange
//onemptied
//onended
//onerror*
//onfocus*
//oninput
//oninvalid
//onkeydown
//onkeypress
//onkeyup
//onload*
//onloadeddata
//onloadedmetadata
//onloadstart
//onmousedown
//onmouseenter
//onmouseleave
//onmousemove
//onmouseout
//onmouseover
//onmouseup
//onmousewheel
//onpause
//onplay
//onplaying
//onprogress
//onratechange
//onreset
//onresize*
//onscroll*
//onseeked
//onseeking
//onselect
//onshow
//onstalled
//onsubmit
//onsuspend
//ontimeupdate
//ontoggle
//onvolumechange
//onwaiting

//accept-charset - Character encodings to use for form submission
//action - URL to use for form submission
//autocomplete - Default setting for autofill feature for controls in the form
//enctype - Form data set encoding type to use for form submission
//method - HTTP method to use for form submission
//name - Name of form to use in the document.forms API
//novalidate - Bypass form control validation for form submission
//target - Browsing context for form submission


/**
 * A generic class for form tags.
 *
 * The unique tag id is an integer to be used for internal purposes but may be exposed on output.
 * If the id attribute is defined, then on output the id attribute is used for the HTML tag.
 *
 * @see: https://www.w3.org/TR/html5/forms.html#forms
 */
class c_theme_form_tag extends c_base_markup_tag {
  private $attributes;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->attributes = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    parent::__destruct();

    unset($this->attributes);
  }

  /**
   * Assign the specified tag.
   *
   * @param int $attribute
   *   The attribute to assign.
   * @param $value
   *   The value of the attribute.
   *   The actual value type is specific to each attribute type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_attribute($attribute, $value) {
    if (!is_int($attribute)) {
      return c_base_return_error::s_false();
    }

    switch ($attribute) {
      case self::ATTRIBUTE_NONE:
        unset($this->attribute[$attribute]);
        return new c_base_return_true();

      case self::ATTRIBUTE_ACTION:
      case self::ATTRIBUTE_DIRECTION_NAME:
      case self::ATTRIBUTE_FOR:
      case self::ATTRIBUTE_FORM:
      case self::ATTRIBUTE_FORM_ACTION:
      case self::ATTRIBUTE_FORM_TARGET:
      case self::ATTRIBUTE_KEY_TYPE:
      case self::ATTRIBUTE_LABEL:
      case self::ATTRIBUTE_LIST:
      case self::ATTRIBUTE_NAME:
      case self::ATTRIBUTE_ON_ABORT:
      case self::ATTRIBUTE_ON_AFTER_PRINT:
      case self::ATTRIBUTE_ON_ANIMATION_END:
      case self::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case self::ATTRIBUTE_ON_ANIMATION_start:
      case self::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case self::ATTRIBUTE_ON_BEFORE_PRINT:
      case self::ATTRIBUTE_ON_BLUR:
      case self::ATTRIBUTE_ON_CLICK:
      case self::ATTRIBUTE_ON_CONTEXT_MENU:
      case self::ATTRIBUTE_ON_COPY:
      case self::ATTRIBUTE_ON_CUT:
      case self::ATTRIBUTE_ON_CAN_PLAY:
      case self::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case self::ATTRIBUTE_ON_CHANGE:
      case self::ATTRIBUTE_ON_DOUBLE_CLICK:
      case self::ATTRIBUTE_ON_DRAG:
      case self::ATTRIBUTE_ON_DRAG_END:
      case self::ATTRIBUTE_ON_DRAG_ENTER:
      case self::ATTRIBUTE_ON_DRAG_LEAVE:
      case self::ATTRIBUTE_ON_DRAG_OVER:
      case self::ATTRIBUTE_ON_DRAG_START:
      case self::ATTRIBUTE_ON_DROP:
      case self::ATTRIBUTE_ON_DURATION_CHANGE:
      case self::ATTRIBUTE_ON_ERROR:
      case self::ATTRIBUTE_ON_EMPTIED:
      case self::ATTRIBUTE_ON_ENDED:
      case self::ATTRIBUTE_ON_ERROR:
      case self::ATTRIBUTE_ON_FOCUS:
      case self::ATTRIBUTE_ON_FOCUS_IN:
      case self::ATTRIBUTE_ON_FOCUS_OUT:
      case self::ATTRIBUTE_ON_HASH_CHANGE:
      case self::ATTRIBUTE_ON_INPUT:
      case self::ATTRIBUTE_ON_INVALID:
      case self::ATTRIBUTE_ON_KEY_DOWN:
      case self::ATTRIBUTE_ON_KEY_PRESS:
      case self::ATTRIBUTE_ON_KEY_UP:
      case self::ATTRIBUTE_ON_LOAD:
      case self::ATTRIBUTE_ON_LOADED_DATA:
      case self::ATTRIBUTE_ON_LOADED_META_DATA:
      case self::ATTRIBUTE_ON_LOAD_START:
      case self::ATTRIBUTE_ON_MOUSE_DOWN:
      case self::ATTRIBUTE_ON_MOUSE_ENTER:
      case self::ATTRIBUTE_ON_MOUSE_LEAVE:
      case self::ATTRIBUTE_ON_MOUSE_MOVE:
      case self::ATTRIBUTE_ON_MOUSE_OVER:
      case self::ATTRIBUTE_ON_MOUSE_OUT:
      case self::ATTRIBUTE_ON_MOUSE_UP:
      case self::ATTRIBUTE_ON_MESSAGE:
      case self::ATTRIBUTE_ON_MOUSE_WHEEL:
      case self::ATTRIBUTE_ON_OPEN:
      case self::ATTRIBUTE_ON_ONLINE:
      case self::ATTRIBUTE_ON_OFFLINE:
      case self::ATTRIBUTE_ON_PAGE_SHOW:
      case self::ATTRIBUTE_ON_PAGE_HIDE:
      case self::ATTRIBUTE_ON_PASTE:
      case self::ATTRIBUTE_ON_PAUSE:
      case self::ATTRIBUTE_ON_PLAY:
      case self::ATTRIBUTE_ON_PLAYING:
      case self::ATTRIBUTE_ON_PROGRESS:
      case self::ATTRIBUTE_ON_POP_STATE:
      case self::ATTRIBUTE_ON_RESIZE:
      case self::ATTRIBUTE_ON_RESET:
      case self::ATTRIBUTE_ON_RATE_CHANGE:
      case self::ATTRIBUTE_ON_SCROLL:
      case self::ATTRIBUTE_ON_SEARCH:
      case self::ATTRIBUTE_ON_SELECT:
      case self::ATTRIBUTE_ON_SUBMIT:
      case self::ATTRIBUTE_ON_SEEKED:
      case self::ATTRIBUTE_ON_SEEKING:
      case self::ATTRIBUTE_ON_STALLED:
      case self::ATTRIBUTE_ON_SUSPEND:
      case self::ATTRIBUTE_ON_SHOW:
      case self::ATTRIBUTE_ON_STORAGE:
      case self::ATTRIBUTE_ON_TIME_UPDATE:
      case self::ATTRIBUTE_ON_TRANSITION_END:
      case self::ATTRIBUTE_ON_TOGGLE:
      case self::ATTRIBUTE_ON_TOUCH_CANCEL:
      case self::ATTRIBUTE_ON_TOUCH_END:
      case self::ATTRIBUTE_ON_TOUCH_MOVE:
      case self::ATTRIBUTE_ON_TOUCH_START:
      case self::ATTRIBUTE_ON_UNLOAD:
      case self::ATTRIBUTE_ON_VOLUME_CHANGE:
      case self::ATTRIBUTE_ON_WAITING:
      case self::ATTRIBUTE_ON_WHEEL:
      case self::ATTRIBUTE_PATTERN:
      case self::ATTRIBUTE_PLACE_HOLDER:
      case self::ATTRIBUTE_READONLY:
      case self::ATTRIBUTE_REQUIRED:
      case self::ATTRIBUTE_ROWS:
      case self::ATTRIBUTE_SELECTED:
      case self::ATTRIBUTE_SIZE:
      case self::ATTRIBUTE_SOURCE:
      case self::ATTRIBUTE_STEP:
      case self::ATTRIBUTE_TYPE:
      case self::ATTRIBUTE_WRAP:
      case self::ATTRIBUTE_VALUE:
        if (!is_string($value)) {
          return c_base_return_false();
        }
        break;

      case self::ATTRIBUTE_FORM_NO_VALIDATED:
      case self::ATTRIBUTE_AUTO_COMPLETE:
      case self::ATTRIBUTE_AUTO_FOCUS:
      case self::ATTRIBUTE_CHALLENGE:
      case self::ATTRIBUTE_CHECKED:
      case self::ATTRIBUTE_DISABLED:
      case self::ATTRIBUTE_MULTIPLE:
        if (!is_bool($value)) {
          return c_base_return_false();
        }
        break;

      case self::ATTRIBUTE_ACCEPT:
      case self::ATTRIBUTE_FORM_ENCODE_TYPE:
        if (!$this->pr_validate_value_mime_type($value)) {
          return c_base_return_false();
        }
        break;

      case self::ATTRIBUTE_COLUMNS:
      case self::ATTRIBUTE_MAXIMUM:
      case self::ATTRIBUTE_MAXIMUM_LENGTH:
      case self::ATTRIBUTE_MINIMUM:
        if (!is_int($value)) {
          return c_base_return_false();
        }
        break;

      case self::ATTRIBUTE_FORM_METHOD:
        if (!$this->pr_validate_value_http_method($value)) {
          return c_base_return_false();
        }
        break;

      default:
        return new c_base_return_false();
    }

    $this->attribute[$attribute] = $value;

    return new c_base_return_true();
  }

  /**
   * Get the attributes assigned to this object.
   *
   * @return c_base_return_array
   *   The attributes assigned to this class.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attributes() {
    if (!isset($this->attributes) && !is_array($this->attributes)) {
      $this->attributes = array();
    }

    return new c_base_return_array($this->attributes);
  }

  /**
   * Get the value of a single attribute assigned to this object.
   *
   * @param int $attribute
   *   The attribute to assign.
   *
   * @return c_base_return_int|c_base_return_string|c_base_return_bool|c_base_return_status
   *   The value assigned to the attribte (the data type is different per attribute).
   *   FALSE is returned if the element does not exist.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attribute($attribute) {
    if (!is_int($attribute)) {
      return c_base_return_error::s_false();
    }

    if (!isset($this->attributes) && !is_array($this->attributes)) {
      $this->attributes = array();
    }

    if (array_key_exists($attribute, $this->attributes)) {
      switch ($attribute) {
        case self::ATTRIBUTE_NONE:
          // should not be possible, so consider this an error (attributes set to NONE are actually unset from the array).
          return c_base_return_error::s_false();

        case self::ATTRIBUTE_ACTION:
        case self::ATTRIBUTE_DIRECTION_NAME:
        case self::ATTRIBUTE_FOR:
        case self::ATTRIBUTE_FORM:
        case self::ATTRIBUTE_FORM_ACTION:
        case self::ATTRIBUTE_FORM_TARGET:
        case self::ATTRIBUTE_KEY_TYPE:
        case self::ATTRIBUTE_LABEL:
        case self::ATTRIBUTE_LIST:
        case self::ATTRIBUTE_NAME:
        case self::ATTRIBUTE_ON_ABORT:
        case self::ATTRIBUTE_ON_AFTER_PRINT:
        case self::ATTRIBUTE_ON_ANIMATION_END:
        case self::ATTRIBUTE_ON_ANIMATION_ITERATION:
        case self::ATTRIBUTE_ON_ANIMATION_start:
        case self::ATTRIBUTE_ON_BEFORE_UNLOAD:
        case self::ATTRIBUTE_ON_BEFORE_PRINT:
        case self::ATTRIBUTE_ON_BLUR:
        case self::ATTRIBUTE_ON_CLICK:
        case self::ATTRIBUTE_ON_CONTEXT_MENU:
        case self::ATTRIBUTE_ON_COPY:
        case self::ATTRIBUTE_ON_CUT:
        case self::ATTRIBUTE_ON_CAN_PLAY:
        case self::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
        case self::ATTRIBUTE_ON_CHANGE:
        case self::ATTRIBUTE_ON_DOUBLE_CLICK:
        case self::ATTRIBUTE_ON_DRAG:
        case self::ATTRIBUTE_ON_DRAG_END:
        case self::ATTRIBUTE_ON_DRAG_ENTER:
        case self::ATTRIBUTE_ON_DRAG_LEAVE:
        case self::ATTRIBUTE_ON_DRAG_OVER:
        case self::ATTRIBUTE_ON_DRAG_START:
        case self::ATTRIBUTE_ON_DROP:
        case self::ATTRIBUTE_ON_DURATION_CHANGE:
        case self::ATTRIBUTE_ON_ERROR:
        case self::ATTRIBUTE_ON_EMPTIED:
        case self::ATTRIBUTE_ON_ENDED:
        case self::ATTRIBUTE_ON_ERROR:
        case self::ATTRIBUTE_ON_FOCUS:
        case self::ATTRIBUTE_ON_FOCUS_IN:
        case self::ATTRIBUTE_ON_FOCUS_OUT:
        case self::ATTRIBUTE_ON_HASH_CHANGE:
        case self::ATTRIBUTE_ON_INPUT:
        case self::ATTRIBUTE_ON_INVALID:
        case self::ATTRIBUTE_ON_KEY_DOWN:
        case self::ATTRIBUTE_ON_KEY_PRESS:
        case self::ATTRIBUTE_ON_KEY_UP:
        case self::ATTRIBUTE_ON_LOAD:
        case self::ATTRIBUTE_ON_LOADED_DATA:
        case self::ATTRIBUTE_ON_LOADED_META_DATA:
        case self::ATTRIBUTE_ON_LOAD_START:
        case self::ATTRIBUTE_ON_MOUSE_DOWN:
        case self::ATTRIBUTE_ON_MOUSE_ENTER:
        case self::ATTRIBUTE_ON_MOUSE_LEAVE:
        case self::ATTRIBUTE_ON_MOUSE_MOVE:
        case self::ATTRIBUTE_ON_MOUSE_OVER:
        case self::ATTRIBUTE_ON_MOUSE_OUT:
        case self::ATTRIBUTE_ON_MOUSE_UP:
        case self::ATTRIBUTE_ON_MESSAGE:
        case self::ATTRIBUTE_ON_MOUSE_WHEEL:
        case self::ATTRIBUTE_ON_OPEN:
        case self::ATTRIBUTE_ON_ONLINE:
        case self::ATTRIBUTE_ON_OFFLINE:
        case self::ATTRIBUTE_ON_PAGE_SHOW:
        case self::ATTRIBUTE_ON_PAGE_HIDE:
        case self::ATTRIBUTE_ON_PASTE:
        case self::ATTRIBUTE_ON_PAUSE:
        case self::ATTRIBUTE_ON_PLAY:
        case self::ATTRIBUTE_ON_PLAYING:
        case self::ATTRIBUTE_ON_PROGRESS:
        case self::ATTRIBUTE_ON_POP_STATE:
        case self::ATTRIBUTE_ON_RESIZE:
        case self::ATTRIBUTE_ON_RESET:
        case self::ATTRIBUTE_ON_RATE_CHANGE:
        case self::ATTRIBUTE_ON_SCROLL:
        case self::ATTRIBUTE_ON_SEARCH:
        case self::ATTRIBUTE_ON_SELECT:
        case self::ATTRIBUTE_ON_SUBMIT:
        case self::ATTRIBUTE_ON_SEEKED:
        case self::ATTRIBUTE_ON_SEEKING:
        case self::ATTRIBUTE_ON_STALLED:
        case self::ATTRIBUTE_ON_SUSPEND:
        case self::ATTRIBUTE_ON_SHOW:
        case self::ATTRIBUTE_ON_STORAGE:
        case self::ATTRIBUTE_ON_TIME_UPDATE:
        case self::ATTRIBUTE_ON_TRANSITION_END:
        case self::ATTRIBUTE_ON_TOGGLE:
        case self::ATTRIBUTE_ON_TOUCH_CANCEL:
        case self::ATTRIBUTE_ON_TOUCH_END:
        case self::ATTRIBUTE_ON_TOUCH_MOVE:
        case self::ATTRIBUTE_ON_TOUCH_START:
        case self::ATTRIBUTE_ON_UNLOAD:
        case self::ATTRIBUTE_ON_VOLUME_CHANGE:
        case self::ATTRIBUTE_ON_WAITING:
        case self::ATTRIBUTE_ON_WHEEL:
        case self::ATTRIBUTE_PATTERN:
        case self::ATTRIBUTE_READONLY:
        case self::ATTRIBUTE_REQUIRED:
        case self::ATTRIBUTE_ROWS:
        case self::ATTRIBUTE_SELECTED:
        case self::ATTRIBUTE_SIZE:
        case self::ATTRIBUTE_SOURCE:
        case self::ATTRIBUTE_STEP:
        case self::ATTRIBUTE_TYPE:
        case self::ATTRIBUTE_WRAP:
        case self::ATTRIBUTE_PLACE_HOLDER:
        case self::ATTRIBUTE_VALUE:
          return c_base_return_string::s_new($value);

        case self::ATTRIBUTE_FORM_NO_VALIDATED:
        case self::ATTRIBUTE_AUTO_COMPLETE:
        case self::ATTRIBUTE_AUTO_FOCUS:
        case self::ATTRIBUTE_CHALLENGE:
        case self::ATTRIBUTE_CHECKED:
        case self::ATTRIBUTE_DISABLED:
        case self::ATTRIBUTE_MULTIPLE:
          return c_base_return_bool::s_new($value);

        case self::ATTRIBUTE_ACCEPT:
        case self::ATTRIBUTE_FORM_ENCODE_TYPE:
          return c_base_return_int::s_new($value);

        case self::ATTRIBUTE_COLUMNS:
        case self::ATTRIBUTE_MAXIMUM:
        case self::ATTRIBUTE_MAXIMUM_LENGTH:
        case self::ATTRIBUTE_MINIMUM:
          return c_base_return_int::s_new($value);

        case self::ATTRIBUTE_FORM_METHOD:
          return c_base_return_int::s_new($value);

        default:
          return new c_base_return_false();
      }
    }

    $this->attribute[$attribute] = $value;

    return new c_base_return_false();
  }
}
