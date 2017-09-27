<?php
/**
 * @file
 * Provides a class for managing HTML5 Markup.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 *
 * @see: https://www.w3.org/TR/html5/
 */
namespace n_koopa;

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

    $this->attributes = [];
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
      case static::ATTRIBUTE_NONE:
        unset($this->attribute[$attribute]);
        return new c_base_return_true();

      case static::ATTRIBUTE_ACTION:
      case static::ATTRIBUTE_DIRECTION_NAME:
      case static::ATTRIBUTE_FOR:
      case static::ATTRIBUTE_FORM:
      case static::ATTRIBUTE_FORM_ACTION:
      case static::ATTRIBUTE_FORM_TARGET:
      case static::ATTRIBUTE_KEY_TYPE:
      case static::ATTRIBUTE_LABEL:
      case static::ATTRIBUTE_LIST:
      case static::ATTRIBUTE_NAME:
      case static::ATTRIBUTE_ON_ABORT:
      case static::ATTRIBUTE_ON_AFTER_PRINT:
      case static::ATTRIBUTE_ON_ANIMATION_END:
      case static::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case static::ATTRIBUTE_ON_ANIMATION_start:
      case static::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case static::ATTRIBUTE_ON_BEFORE_PRINT:
      case static::ATTRIBUTE_ON_BLUR:
      case static::ATTRIBUTE_ON_CLICK:
      case static::ATTRIBUTE_ON_CONTEXT_MENU:
      case static::ATTRIBUTE_ON_COPY:
      case static::ATTRIBUTE_ON_CUT:
      case static::ATTRIBUTE_ON_CAN_PLAY:
      case static::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case static::ATTRIBUTE_ON_CHANGE:
      case static::ATTRIBUTE_ON_DOUBLE_CLICK:
      case static::ATTRIBUTE_ON_DRAG:
      case static::ATTRIBUTE_ON_DRAG_END:
      case static::ATTRIBUTE_ON_DRAG_ENTER:
      case static::ATTRIBUTE_ON_DRAG_LEAVE:
      case static::ATTRIBUTE_ON_DRAG_OVER:
      case static::ATTRIBUTE_ON_DRAG_START:
      case static::ATTRIBUTE_ON_DROP:
      case static::ATTRIBUTE_ON_DURATION_CHANGE:
      case static::ATTRIBUTE_ON_ERROR:
      case static::ATTRIBUTE_ON_EMPTIED:
      case static::ATTRIBUTE_ON_ENDED:
      case static::ATTRIBUTE_ON_ERROR:
      case static::ATTRIBUTE_ON_FOCUS:
      case static::ATTRIBUTE_ON_FOCUS_IN:
      case static::ATTRIBUTE_ON_FOCUS_OUT:
      case static::ATTRIBUTE_ON_HASH_CHANGE:
      case static::ATTRIBUTE_ON_INPUT:
      case static::ATTRIBUTE_ON_INVALID:
      case static::ATTRIBUTE_ON_KEY_DOWN:
      case static::ATTRIBUTE_ON_KEY_PRESS:
      case static::ATTRIBUTE_ON_KEY_UP:
      case static::ATTRIBUTE_ON_LOAD:
      case static::ATTRIBUTE_ON_LOADED_DATA:
      case static::ATTRIBUTE_ON_LOADED_META_DATA:
      case static::ATTRIBUTE_ON_LOAD_START:
      case static::ATTRIBUTE_ON_MOUSE_DOWN:
      case static::ATTRIBUTE_ON_MOUSE_ENTER:
      case static::ATTRIBUTE_ON_MOUSE_LEAVE:
      case static::ATTRIBUTE_ON_MOUSE_MOVE:
      case static::ATTRIBUTE_ON_MOUSE_OVER:
      case static::ATTRIBUTE_ON_MOUSE_OUT:
      case static::ATTRIBUTE_ON_MOUSE_UP:
      case static::ATTRIBUTE_ON_MESSAGE:
      case static::ATTRIBUTE_ON_MOUSE_WHEEL:
      case static::ATTRIBUTE_ON_OPEN:
      case static::ATTRIBUTE_ON_ONLINE:
      case static::ATTRIBUTE_ON_OFFLINE:
      case static::ATTRIBUTE_ON_PAGE_SHOW:
      case static::ATTRIBUTE_ON_PAGE_HIDE:
      case static::ATTRIBUTE_ON_PASTE:
      case static::ATTRIBUTE_ON_PAUSE:
      case static::ATTRIBUTE_ON_PLAY:
      case static::ATTRIBUTE_ON_PLAYING:
      case static::ATTRIBUTE_ON_PROGRESS:
      case static::ATTRIBUTE_ON_POP_STATE:
      case static::ATTRIBUTE_ON_RESIZE:
      case static::ATTRIBUTE_ON_RESET:
      case static::ATTRIBUTE_ON_RATE_CHANGE:
      case static::ATTRIBUTE_ON_SCROLL:
      case static::ATTRIBUTE_ON_SEARCH:
      case static::ATTRIBUTE_ON_SELECT:
      case static::ATTRIBUTE_ON_SUBMIT:
      case static::ATTRIBUTE_ON_SEEKED:
      case static::ATTRIBUTE_ON_SEEKING:
      case static::ATTRIBUTE_ON_STALLED:
      case static::ATTRIBUTE_ON_SUSPEND:
      case static::ATTRIBUTE_ON_SHOW:
      case static::ATTRIBUTE_ON_STORAGE:
      case static::ATTRIBUTE_ON_TIME_UPDATE:
      case static::ATTRIBUTE_ON_TRANSITION_END:
      case static::ATTRIBUTE_ON_TOGGLE:
      case static::ATTRIBUTE_ON_TOUCH_CANCEL:
      case static::ATTRIBUTE_ON_TOUCH_END:
      case static::ATTRIBUTE_ON_TOUCH_MOVE:
      case static::ATTRIBUTE_ON_TOUCH_START:
      case static::ATTRIBUTE_ON_UNLOAD:
      case static::ATTRIBUTE_ON_VOLUME_CHANGE:
      case static::ATTRIBUTE_ON_WAITING:
      case static::ATTRIBUTE_ON_WHEEL:
      case static::ATTRIBUTE_PATTERN:
      case static::ATTRIBUTE_PLACE_HOLDER:
      case static::ATTRIBUTE_READONLY:
      case static::ATTRIBUTE_REQUIRED:
      case static::ATTRIBUTE_ROWS:
      case static::ATTRIBUTE_SELECTED:
      case static::ATTRIBUTE_SIZE:
      case static::ATTRIBUTE_SOURCE:
      case static::ATTRIBUTE_STEP:
      case static::ATTRIBUTE_TYPE:
      case static::ATTRIBUTE_WRAP:
      case static::ATTRIBUTE_VALUE:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case static::ATTRIBUTE_FORM_NO_VALIDATED:
      case static::ATTRIBUTE_AUTO_COMPLETE:
      case static::ATTRIBUTE_AUTO_FOCUS:
      case static::ATTRIBUTE_CHALLENGE:
      case static::ATTRIBUTE_CHECKED:
      case static::ATTRIBUTE_DISABLED:
      case static::ATTRIBUTE_MULTIPLE:
        if (!is_bool($value)) {
          return new c_base_return_false();
        }
        break;

      case static::ATTRIBUTE_ACCEPT:
      case static::ATTRIBUTE_FORM_ENCODE_TYPE:
        if (!$this->pr_validate_value_mime_type($value)) {
          return new c_base_return_false();
        }
        break;

      case static::ATTRIBUTE_COLUMNS:
      case static::ATTRIBUTE_MAXIMUM:
      case static::ATTRIBUTE_MAXIMUM_LENGTH:
      case static::ATTRIBUTE_MINIMUM:
        if (!is_int($value)) {
          return new c_base_return_false();
        }
        break;

      case static::ATTRIBUTE_FORM_METHOD:
        if (!$this->pr_validate_value_http_method($value)) {
          return new c_base_return_false();
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
      $this->attributes = [];
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
      $this->attributes = [];
    }

    if (array_key_exists($attribute, $this->attributes)) {
      switch ($attribute) {
        case static::ATTRIBUTE_NONE:
          // should not be possible, so consider this an error (attributes set to NONE are actually unset from the array).
          return c_base_return_error::s_false();

        case static::ATTRIBUTE_ACTION:
        case static::ATTRIBUTE_DIRECTION_NAME:
        case static::ATTRIBUTE_FOR:
        case static::ATTRIBUTE_FORM:
        case static::ATTRIBUTE_FORM_ACTION:
        case static::ATTRIBUTE_FORM_TARGET:
        case static::ATTRIBUTE_KEY_TYPE:
        case static::ATTRIBUTE_LABEL:
        case static::ATTRIBUTE_LIST:
        case static::ATTRIBUTE_NAME:
        case static::ATTRIBUTE_ON_ABORT:
        case static::ATTRIBUTE_ON_AFTER_PRINT:
        case static::ATTRIBUTE_ON_ANIMATION_END:
        case static::ATTRIBUTE_ON_ANIMATION_ITERATION:
        case static::ATTRIBUTE_ON_ANIMATION_start:
        case static::ATTRIBUTE_ON_BEFORE_UNLOAD:
        case static::ATTRIBUTE_ON_BEFORE_PRINT:
        case static::ATTRIBUTE_ON_BLUR:
        case static::ATTRIBUTE_ON_CLICK:
        case static::ATTRIBUTE_ON_CONTEXT_MENU:
        case static::ATTRIBUTE_ON_COPY:
        case static::ATTRIBUTE_ON_CUT:
        case static::ATTRIBUTE_ON_CAN_PLAY:
        case static::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
        case static::ATTRIBUTE_ON_CHANGE:
        case static::ATTRIBUTE_ON_DOUBLE_CLICK:
        case static::ATTRIBUTE_ON_DRAG:
        case static::ATTRIBUTE_ON_DRAG_END:
        case static::ATTRIBUTE_ON_DRAG_ENTER:
        case static::ATTRIBUTE_ON_DRAG_LEAVE:
        case static::ATTRIBUTE_ON_DRAG_OVER:
        case static::ATTRIBUTE_ON_DRAG_START:
        case static::ATTRIBUTE_ON_DROP:
        case static::ATTRIBUTE_ON_DURATION_CHANGE:
        case static::ATTRIBUTE_ON_ERROR:
        case static::ATTRIBUTE_ON_EMPTIED:
        case static::ATTRIBUTE_ON_ENDED:
        case static::ATTRIBUTE_ON_ERROR:
        case static::ATTRIBUTE_ON_FOCUS:
        case static::ATTRIBUTE_ON_FOCUS_IN:
        case static::ATTRIBUTE_ON_FOCUS_OUT:
        case static::ATTRIBUTE_ON_HASH_CHANGE:
        case static::ATTRIBUTE_ON_INPUT:
        case static::ATTRIBUTE_ON_INVALID:
        case static::ATTRIBUTE_ON_KEY_DOWN:
        case static::ATTRIBUTE_ON_KEY_PRESS:
        case static::ATTRIBUTE_ON_KEY_UP:
        case static::ATTRIBUTE_ON_LOAD:
        case static::ATTRIBUTE_ON_LOADED_DATA:
        case static::ATTRIBUTE_ON_LOADED_META_DATA:
        case static::ATTRIBUTE_ON_LOAD_START:
        case static::ATTRIBUTE_ON_MOUSE_DOWN:
        case static::ATTRIBUTE_ON_MOUSE_ENTER:
        case static::ATTRIBUTE_ON_MOUSE_LEAVE:
        case static::ATTRIBUTE_ON_MOUSE_MOVE:
        case static::ATTRIBUTE_ON_MOUSE_OVER:
        case static::ATTRIBUTE_ON_MOUSE_OUT:
        case static::ATTRIBUTE_ON_MOUSE_UP:
        case static::ATTRIBUTE_ON_MESSAGE:
        case static::ATTRIBUTE_ON_MOUSE_WHEEL:
        case static::ATTRIBUTE_ON_OPEN:
        case static::ATTRIBUTE_ON_ONLINE:
        case static::ATTRIBUTE_ON_OFFLINE:
        case static::ATTRIBUTE_ON_PAGE_SHOW:
        case static::ATTRIBUTE_ON_PAGE_HIDE:
        case static::ATTRIBUTE_ON_PASTE:
        case static::ATTRIBUTE_ON_PAUSE:
        case static::ATTRIBUTE_ON_PLAY:
        case static::ATTRIBUTE_ON_PLAYING:
        case static::ATTRIBUTE_ON_PROGRESS:
        case static::ATTRIBUTE_ON_POP_STATE:
        case static::ATTRIBUTE_ON_RESIZE:
        case static::ATTRIBUTE_ON_RESET:
        case static::ATTRIBUTE_ON_RATE_CHANGE:
        case static::ATTRIBUTE_ON_SCROLL:
        case static::ATTRIBUTE_ON_SEARCH:
        case static::ATTRIBUTE_ON_SELECT:
        case static::ATTRIBUTE_ON_SUBMIT:
        case static::ATTRIBUTE_ON_SEEKED:
        case static::ATTRIBUTE_ON_SEEKING:
        case static::ATTRIBUTE_ON_STALLED:
        case static::ATTRIBUTE_ON_SUSPEND:
        case static::ATTRIBUTE_ON_SHOW:
        case static::ATTRIBUTE_ON_STORAGE:
        case static::ATTRIBUTE_ON_TIME_UPDATE:
        case static::ATTRIBUTE_ON_TRANSITION_END:
        case static::ATTRIBUTE_ON_TOGGLE:
        case static::ATTRIBUTE_ON_TOUCH_CANCEL:
        case static::ATTRIBUTE_ON_TOUCH_END:
        case static::ATTRIBUTE_ON_TOUCH_MOVE:
        case static::ATTRIBUTE_ON_TOUCH_START:
        case static::ATTRIBUTE_ON_UNLOAD:
        case static::ATTRIBUTE_ON_VOLUME_CHANGE:
        case static::ATTRIBUTE_ON_WAITING:
        case static::ATTRIBUTE_ON_WHEEL:
        case static::ATTRIBUTE_PATTERN:
        case static::ATTRIBUTE_READONLY:
        case static::ATTRIBUTE_REQUIRED:
        case static::ATTRIBUTE_ROWS:
        case static::ATTRIBUTE_SELECTED:
        case static::ATTRIBUTE_SIZE:
        case static::ATTRIBUTE_SOURCE:
        case static::ATTRIBUTE_STEP:
        case static::ATTRIBUTE_TYPE:
        case static::ATTRIBUTE_WRAP:
        case static::ATTRIBUTE_PLACE_HOLDER:
        case static::ATTRIBUTE_VALUE:
          return c_base_return_string::s_new($value);

        case static::ATTRIBUTE_FORM_NO_VALIDATED:
        case static::ATTRIBUTE_AUTO_COMPLETE:
        case static::ATTRIBUTE_AUTO_FOCUS:
        case static::ATTRIBUTE_CHALLENGE:
        case static::ATTRIBUTE_CHECKED:
        case static::ATTRIBUTE_DISABLED:
        case static::ATTRIBUTE_MULTIPLE:
          return c_base_return_bool::s_new($value);

        case static::ATTRIBUTE_ACCEPT:
        case static::ATTRIBUTE_FORM_ENCODE_TYPE:
          return c_base_return_int::s_new($value);

        case static::ATTRIBUTE_COLUMNS:
        case static::ATTRIBUTE_MAXIMUM:
        case static::ATTRIBUTE_MAXIMUM_LENGTH:
        case static::ATTRIBUTE_MINIMUM:
          return c_base_return_int::s_new($value);

        case static::ATTRIBUTE_FORM_METHOD:
          return c_base_return_int::s_new($value);

        default:
          return new c_base_return_false();
      }
    }

    $this->attribute[$attribute] = $value;

    return new c_base_return_false();
  }
}
