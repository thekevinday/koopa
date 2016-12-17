<?php
/**
 * @file
 * Provides a class for managing system forms.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_mime.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_charset.php');
require_once('common/base/classes/base_language.php');

// use 'mutable' and have it set to FALSE for server-side only data that cannot be changed client side.
// - drupal uses 'readonly' and 'disabled', but those are presentation specific, so they must not be used or related to 'mutable' (or in this case, unmutable).

// associated form (sessions) with timestamp, ip address, and agent?
// generate unique key and/or checksum for each form (and in http header).

/**
 * A generic class for form attribute types.
 */
class c_base_form_attributes {
  const ATTRIBUTE_NONE                   = 0;
  const ATTRIBUTE_ACCESS_KEY             = 1; // single letter
  const ATTRIBUTE_ACCEPT                 = 2; // c_base_mime integer
  const ATTRIBUTE_ACTION                 = 3; // string, URL
  const ATTRIBUTE_ACTION                 = 4; // text
  const ATTRIBUTE_AUTO_COMPLETE          = 5; // on or off, use TRUE/FALSE
  const ATTRIBUTE_AUTO_FOCUS             = 6; // autofocus, use TRUE/FALSE
  const ATTRIBUTE_CLASS                  = 7; // array of strings
  const ATTRIBUTE_CHALLENGE              = 8; // challenge, use TRUE/FALSE
  const ATTRIBUTE_CHARACTER_SET          = 9; // c_base_charset integer
  const ATTRIBUTE_CHECKED                = 10; // checked, use TRUE/FALSE
  const ATTRIBUTE_COLUMNS                = 11; // number
  const ATTRIBUTE_CONTENT_EDITABLE       = 12; // TRUE, FALSE, INHERIT
  const ATTRIBUTE_DIRECTION              = 13; // ltr, rtl, auto
  const ATTRIBUTE_DIRECTION_NAME         = 14; // text, inputname.dir
  const ATTRIBUTE_DISABLED               = 15; // disabled, use TRUE/FALSE
  const ATTRIBUTE_ENCODING_TYPE          = 16; // c_base_mime integer
  const ATTRIBUTE_FOR                    = 17; // text, element id
  const ATTRIBUTE_FORM                   = 18; // text, form id
  const ATTRIBUTE_FORM_ACTION            = 19; // text, url
  const ATTRIBUTE_FORM_ENCODE_TYPE       = 20; // c_base_mime integer
  const ATTRIBUTE_FORM_METHOD            = 21; // get or post, use HTTP_METHOD_GET and HTTP_METHOD_POST
  const ATTRIBUTE_FORM_NO_VALIDATED      = 22; // formnovalidate, use TRUE/FALSE
  const ATTRIBUTE_FORM_TARGET            = 23; // text, _blank, _self, _parent, _top, URL
  const ATTRIBUTE_HIDDEN                 = 24; // TRUE/FALSE
  const ATTRIBUTE_KEY_TYPE               = 25; // text, rsa, dsa, ec
  const ATTRIBUTE_LABEL                  = 26; // text
  const ATTRIBUTE_LANG                   = 27; // i_base_language, int
  const ATTRIBUTE_LIST                   = 28; // text, datalist_id
  const ATTRIBUTE_MAXIMUM                = 29; // number, date
  const ATTRIBUTE_MAXIMUM_LENGTH         = 30; // number
  const ATTRIBUTE_MINIMUM                = 31; // number, date
  const ATTRIBUTE_MULTIPLE               = 32; // multiple, use TRUE/FALSE
  const ATTRIBUTE_NAME                   = 33; // TRUE/FALSE
  const ATTRIBUTE_NO_VALIDATE            = 34; // text
  const ATTRIBUTE_ON_ABORT               = 35; // text
  const ATTRIBUTE_ON_AFTER_PRINT         = 36; // text
  const ATTRIBUTE_ON_ANIMATION_END       = 37; // text
  const ATTRIBUTE_ON_ANIMATION_ITERATION = 38; // text
  const ATTRIBUTE_ON_ANIMATION_start     = 39; // text
  const ATTRIBUTE_ON_BEFORE_UNLOAD       = 40; // text
  const ATTRIBUTE_ON_BEFORE_PRINT        = 41; // text
  const ATTRIBUTE_ON_BLUR                = 42; // text
  const ATTRIBUTE_ON_CLICK               = 43; // text
  const ATTRIBUTE_ON_CONTEXT_MENU        = 44; // text
  const ATTRIBUTE_ON_COPY                = 45; // text
  const ATTRIBUTE_ON_CUT                 = 46; // text
  const ATTRIBUTE_ON_CAN_PLAY            = 47; // text
  const ATTRIBUTE_ON_CAN_PLAY_THROUGH    = 48; // text
  const ATTRIBUTE_ON_CHANGE              = 49; // text
  const ATTRIBUTE_ON_DOUBLE_CLICK        = 50; // text
  const ATTRIBUTE_ON_DRAG                = 51; // text
  const ATTRIBUTE_ON_DRAG_END            = 52; // text
  const ATTRIBUTE_ON_DRAG_ENTER          = 53; // text
  const ATTRIBUTE_ON_DRAG_LEAVE          = 54; // text
  const ATTRIBUTE_ON_DRAG_OVER           = 55; // text
  const ATTRIBUTE_ON_DRAG_START          = 56; // text
  const ATTRIBUTE_ON_DROP                = 57; // text
  const ATTRIBUTE_ON_DURATION_CHANGE     = 58; // text
  const ATTRIBUTE_ON_ERROR               = 59; // text
  const ATTRIBUTE_ON_EMPTIED             = 60; // text
  const ATTRIBUTE_ON_ENDED               = 61; // text
  const ATTRIBUTE_ON_ERROR               = 62; // text
  const ATTRIBUTE_ON_FOCUS               = 63; // text
  const ATTRIBUTE_ON_FOCUS_IN            = 64; // text
  const ATTRIBUTE_ON_FOCUS_OUT           = 65; // text
  const ATTRIBUTE_ON_HASH_CHANGE         = 66; // text
  const ATTRIBUTE_ON_INPUT               = 67; // text
  const ATTRIBUTE_ON_INVALID             = 68; // text
  const ATTRIBUTE_ON_KEY_DOWN            = 69; // text
  const ATTRIBUTE_ON_KEY_PRESS           = 70; // text
  const ATTRIBUTE_ON_KEY_UP              = 71; // text
  const ATTRIBUTE_ON_LOAD                = 72; // text
  const ATTRIBUTE_ON_LOADED_DATA         = 73; // text
  const ATTRIBUTE_ON_LOADED_META_DATA    = 74; // text
  const ATTRIBUTE_ON_LOAD_START          = 75; // text
  const ATTRIBUTE_ON_MOUSE_DOWN          = 76; // text
  const ATTRIBUTE_ON_MOUSE_ENTER         = 77; // text
  const ATTRIBUTE_ON_MOUSE_LEAVE         = 78; // text
  const ATTRIBUTE_ON_MOUSE_MOVE          = 79; // text
  const ATTRIBUTE_ON_MOUSE_OVER          = 80; // text
  const ATTRIBUTE_ON_MOUSE_OUT           = 81; // text
  const ATTRIBUTE_ON_MOUSE_UP            = 82; // text
  const ATTRIBUTE_ON_MESSAGE             = 83; // text
  const ATTRIBUTE_ON_MOUSE_WHEEL         = 84; // text
  const ATTRIBUTE_ON_OPEN                = 85; // text
  const ATTRIBUTE_ON_ONLINE              = 86; // text
  const ATTRIBUTE_ON_OFFLINE             = 87; // text
  const ATTRIBUTE_ON_PAGE_SHOW           = 88; // text
  const ATTRIBUTE_ON_PAGE_HIDE           = 89; // text
  const ATTRIBUTE_ON_PASTE               = 90; // text
  const ATTRIBUTE_ON_PAUSE               = 91; // text
  const ATTRIBUTE_ON_PLAY                = 92; // text
  const ATTRIBUTE_ON_PLAYING             = 93; // text
  const ATTRIBUTE_ON_PROGRESS            = 94; // text
  const ATTRIBUTE_ON_POP_STATE           = 95; // text
  const ATTRIBUTE_ON_RESIZE              = 96; // text
  const ATTRIBUTE_ON_RESET               = 97; // text
  const ATTRIBUTE_ON_RATE_CHANGE         = 98; // text
  const ATTRIBUTE_ON_SCROLL              = 99; // text
  const ATTRIBUTE_ON_SEARCH              = 100; // text
  const ATTRIBUTE_ON_SELECT              = 101; // text
  const ATTRIBUTE_ON_SUBMIT              = 102; // text
  const ATTRIBUTE_ON_SEEKED              = 103; // text
  const ATTRIBUTE_ON_SEEKING             = 104; // text
  const ATTRIBUTE_ON_STALLED             = 105; // text
  const ATTRIBUTE_ON_SUSPEND             = 106; // text
  const ATTRIBUTE_ON_SHOW                = 107; // text
  const ATTRIBUTE_ON_STORAGE             = 108; // text
  const ATTRIBUTE_ON_TIME_UPDATE         = 109; // text
  const ATTRIBUTE_ON_TRANSITION_END      = 110; // text
  const ATTRIBUTE_ON_TOGGLE              = 111; // text
  const ATTRIBUTE_ON_TOUCH_CANCEL        = 112; // text
  const ATTRIBUTE_ON_TOUCH_END           = 113; // text
  const ATTRIBUTE_ON_TOUCH_MOVE          = 114; // text
  const ATTRIBUTE_ON_TOUCH_START         = 115; // text
  const ATTRIBUTE_ON_UNLOAD              = 116; // text
  const ATTRIBUTE_ON_VOLUME_CHANGE       = 117; // text
  const ATTRIBUTE_ON_WAITING             = 118; // text
  const ATTRIBUTE_ON_WHEEL               = 119; // text
  const ATTRIBUTE_PATTERN                = 120; // text, regular expression
  const ATTRIBUTE_PLACE_HOLDER           = 121; // text
  const ATTRIBUTE_READONLY               = 122; // readonly, use TRUE/FALSE
  const ATTRIBUTE_REQUIRED               = 123; // required, use TRUE/FALSE
  const ATTRIBUTE_ROWS                   = 124; // number
  const ATTRIBUTE_SELECTED               = 125; // selected, use TRUE/FALSE
  const ATTRIBUTE_SIZE                   = 126; // number
  const ATTRIBUTE_SOURCE                 = 127; // url
  const ATTRIBUTE_SPELLCHECK             = 125; // TRUE/FALSE
  const ATTRIBUTE_STEP                   = 128; // number
  const ATTRIBUTE_STYLE                  = 129; // text
  const ATTRIBUTE_TAB_INDEX              = 130; // number
  const ATTRIBUTE_TARGET                 = 131; // text, _blank, _self, _parent, _top, URL
  const ATTRIBUTE_TITLE                  = 132; // text
  const ATTRIBUTE_TRANSLATE              = 133; // text
  const ATTRIBUTE_TYPE                   = 134; // see TYPE_ constanst below.
  const ATTRIBUTE_VALUE                  = 135; // text
  const ATTRIBUTE_WRAP                   = 136; // hard, soft
}

/**
 * A generic class for storing a single item of form data.
 *
 * Each tag is expected to have a unique id.
 * The internal id is an integer, primarily intended to be used in databases for performance reasons.
 * The form id is a string that can be identical to the internal tag id.
 * The form id is intended to represent the form tag id or form tag name as used on HTML form id tags.
 *
 */
class c_base_form_data_item {


/**
 * A generic class for form tag types.
 *
 * @see: https://www.w3.org/TR/html5/forms.html#forms
 */
class c_base_form_tags {
  const TYPE_NONE    = 0;
  const TYPE_TEXT    = 1;
  const TYPE_BOOLEAN = 2;
  const TYPE_INTEGER = 3;
  const TYPE_FLOAT   = 4;

  private $id_internal = NULL;
  private $id_form = NULL;
  private $type = self::TYPE_NONE;
  private $data = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->id_internal = NULL;
    $this->id_form = NULL;
    $this->type = self::TYPE_NONE;
    $this->data = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id_internal);
    unset($this->id_form);
    unset($this->type);
    unset($this->data);
  }

  /**
   * Assign the unique id which is intended to uniquely identify the form.
   *
   * @param int $id_internal
   *   The internal numeric id to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_internal($id_internal) {
    if (!is_int($id_internal)) {
      return c_base_return_error::s_false();
    }

    $this->id_internal = $id_internal;
    return new c_base_return_true();
  }

  /**
   * Get the unique id which is intended to uniquely identify the form.
   *
   * @return c_base_return_string|c_base_return_false
   *   The unique internal id assigned to this object.
   *   FALSE is returned if the unique numeric tag id is not set.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id_internal() {
    if (!isset($this->id_internal)) {
      return new c_base_return_false();
    }

    return new c_base_return_string($this->id_internal);
  }

  /**
   * Assign the unique id which is intended to represent the form id string.
   *
   * @param string $id_form
   *   The form id string to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id_form($id_form) {
    if (!is_string($id)) {
      return c_base_return_error::s_false();
    }

    $this->id_form = $id_form;
    return new c_base_return_true();
  }

  /**
   * Get the unique id which is intended to represent the form id string.
   *
   * @return c_base_return_string|c_base_return_false
   *   The form id string assigned to this object.
   *   FALSE is returned if the unique numeric tag id is not set.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id_form() {
    if (!isset($this->id_form)) {
      return new c_base_return_false();
    }

    return new c_base_return_string($this->id_form);
  }

  /**
   * Assign the data.
   *
   * @param int|string|float|bool|null $data
   *   The data whose type is directly dependend on $this->type.
   *   Failure to assign the proper type will result in an error.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_data($data) {
    if ($this->type === self::TYPE_NONE) {
      if (!is_null($data)) {
        return c_base_return_error::s_false();
      }
    }
    elseif ($this->type === self::TYPE_TEXT) {
      if (!is_string($data)) {
        return c_base_return_error::s_false();
      }
    }
    elseif ($this->type === self::TYPE_BOOLEAN) {
      if (!is_bool($data)) {
        return c_base_return_error::s_false();
      }
    }
    elseif ($this->type === self::TYPE_NUMBER) {
      if (!is_int($data)) {
        return c_base_return_error::s_false();
      }
    }
    elseif ($this->type === self::TYPE_FLOAT) {
      if (!is_float($data)) {
        return c_base_return_error::s_false();
      }
    }
    else {
      return c_base_return_error::s_false();
    }

    $this->data = $data;
    return new c_base_return_true();
  }

  /**
   * Get the assigned data.
   *
   * @return c_base_return_status|c_base_return_int|c_base_return_float|c_base_return_string|c_base_return_bool|c_base_return_null
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function get_data() {
    if ($this->type === self::TYPE_NONE) {
      return new c_base_return_null();
    }
    elseif ($this->type === self::TYPE_TEXT) {
      return new c_base_return_string($this->data);
    }
    elseif ($this->type === self::TYPE_BOOLEAN) {
      if ($this->data) {
        return new c_base_return_true();
      }

      return new c_base_return_false();
    }
    elseif ($this->type === self::TYPE_NUMBER) {
      return new c_base_return_int($this->data);
    }
    elseif ($this->type === self::TYPE_FLOAT) {
      return new c_base_return_float($this->data);
    }

    return c_base_return_error::s_false();
  }

  /**
   * Assign the type of data to be stored in this object.
   *
   * Assigning this will reset/clear the data.
   *
   * @param int $type
   */
  public function set_type($type) {
    if ($type === self::TYPE_NONE) {
      $this->type = $type;
      $this->data = NULL;
    }
    elseif ($type === self::TYPE_TEXT) {
      $this->type = $type;
      $this->data = '';
    }
    elseif ($type === self::TYPE_BOOLEAN) {
      $this->type = $type;
      $this->data = FALSE;
    }
    elseif ($type === self::TYPE_NUMBER) {
      $this->type = $type;
      $this->data = 0;
    }
    elseif ($type === self::TYPE_FLOAT) {
      $this->type = $type;
      $this->data = 0.0;
    }
    else {
      return c_base_return_error::s_false();
    }

    return new c_base_return_true();
  }

  /**
   * Get the assigned type.
   *
   * @return c_base_return_status|c_base_return_int
   */
  public function get_type() {
    return new c_base_return_int($this->type);
  }
}

/**
 * A generic class for storing form data.
 *
 * Each tag is expected to have a unique id.
 * The internal id is an integer, primarily intended to be used in databases for performance reasons.
 * The form id is a string that can be identical to the internal id.
 * The form id is intended to represent the form id or form name as used on HTML form id tags.
 *
 */
class c_base_form_data extends c_base_form_data_item {
  private $data = array();

  /**
   * Class constructor.
   */
  public function __construct() {
    super.__construct();

    $this->data = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    super.__destruct();

    unset($this->data);
  }

}

// below will be moved into output/theme areas.
/**
 * A generic class for form tags.
 *
 * The unique tag id is an integer to be used for internal purposes but may be exposed on output.
 * If the id attribute is defined, then on output the id attribute is used for the HTML tag.
 *
 * @see: https://www.w3.org/TR/html5/forms.html#forms
 */
class c_base_form_tag {
  const TAG_NONE      = 0;
  const TAG_INPUT     = 1;
  const TAG_RADIO     = 2;
  const TAG_SUBMIT    = 3;
  const TAG_TEXT_AREA = 4;
  const TAG_FIELD_SET = 5;
  const TAG_SELECT    = 6;
  const TAG_OPTION    = 7;
  const TAG_DATA_LIST = 8;
  const TAG_KEY_GEN   = 9;
  const TAG_OUTPUT    = 10;

  const TYPE_NONE           = 0;
  const TYPE_TEXT           = 1;
  const TYPE_BUTTON         = 2;
  const TYPE_CHECKBOX       = 3;
  const TYPE_COLOR          = 4;
  const TYPE_DATE           = 5;
  const TYPE_DATETIME       = 6;
  const TYPE_DATETIME_LOCAL = 7;
  const TYPE_EMAIL          = 8;
  const TYPE_FILE           = 9;
  const TYPE_HIDDEN         = 10;
  const TYPE_IMAGE          = 11;
  const TYPE_MONTH          = 12;
  const TYPE_NUMBER         = 13;
  const TYPE_PASSWORD       = 14;
  const TYPE_RADIO          = 15;
  const TYPE_RANGE          = 16;
  const TYPE_RESET          = 17;
  const TYPE_SEARCH         = 18;
  const TYPE_SUBMIT         = 19;
  const TYPE_TELEPHONE      = 20;
  const TYPE_TEXT           = 21;
  const TYPE_TIME           = 22;
  const TYPE_URL            = 23;
  const TYPE_WEEK           = 24;

  private $id;
  private $type;
  private $attributes;
  private $parent;
  private $children;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->id = NULL;
    $this->type = self::TAG_NONE;
    $this->attributes = array();
    $this->parent = NULL;
    $this->children = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->type);
    unset($this->attributes);
    unset($this->parent);
    unset($this->children);
  }

  /**
   * Assign the internal unique numeric tag id.
   *
   * @param int $id
   *   The internal numeric id to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id($id) {
    if (!is_int($id)) {
      return c_base_return_error::s_false();
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Get the unique numeric tag id assigned to this object.
   *
   * @return c_base_return_int|c_base_return_false
   *   The tag type assigned to this class.
   *   FALSE is returned if the unique numeric tag id is not set.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id() {
    if (!isset($this->id)) {
      return new c_base_return_false();
    }

    return new c_base_return_int($this->id);
  }

  /**
   * Assign the specified tag type.
   *
   * @param int $type
   *   The tag type to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type($type) {
    if (!is_int($type)) {
      return c_base_return_error::s_false();
    }

    switch ($type) {
      case self::TAG_NONE:
      case self::TAG_INPUT:
      case self::TAG_RADIO:
      case self::TAG_SUBMIT:
      case self::TAG_TEXT_AREA:
      case self::TAG_FIELD_SET:
      case self::TAG_SELECT:
      case self::TAG_OPTION:
      case self::TAG_DATA_LIST:
      case self::TAG_KEY_GEN:
      case self::TAG_OUTPUT:
        break;
      default:
        return new c_base_return_false();
    }

    $this->type = $type;
    return new c_base_return_true();
  }

  /**
   * Get the tag type assigned to this object.
   *
   * @return c_base_return_int
   *   The tag type assigned to this class.
   *   FALSE with error bit set is returned on error.
   */
  public function get_type() {
    if (!isset($this->type)) {
      $this->type = self::TAG_NONE;
    }

    return new c_base_return_int($this->type);
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
      case c_base_form_attributes::ATTRIBUTE_NONE:
        unset($this->attribute[$attribute]);
        return new c_base_return_true();

      case c_base_form_attributes::ATTRIBUTE_ACTION:
      case c_base_form_attributes::ATTRIBUTE_DIRECTION_NAME:
      case c_base_form_attributes::ATTRIBUTE_FOR:
      case c_base_form_attributes::ATTRIBUTE_FORM:
      case c_base_form_attributes::ATTRIBUTE_FORM_ACTION:
      case c_base_form_attributes::ATTRIBUTE_FORM_TARGET:
      case c_base_form_attributes::ATTRIBUTE_KEY_TYPE:
      case c_base_form_attributes::ATTRIBUTE_LABEL:
      case c_base_form_attributes::ATTRIBUTE_LIST:
      case c_base_form_attributes::ATTRIBUTE_NAME:
      case c_base_form_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_form_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_form_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_form_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_form_attributes::ATTRIBUTE_ON_BLUR:
      case c_base_form_attributes::ATTRIBUTE_ON_CLICK:
      case c_base_form_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
      case c_base_form_attributes::ATTRIBUTE_ON_COPY:
      case c_base_form_attributes::ATTRIBUTE_ON_CUT:
      case c_base_form_attributes::ATTRIBUTE_ON_CAN_PLAY:
      case c_base_form_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case c_base_form_attributes::ATTRIBUTE_ON_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_END:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_ENTER:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_OVER:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_START:
      case c_base_form_attributes::ATTRIBUTE_ON_DROP:
      case c_base_form_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_form_attributes::ATTRIBUTE_ON_EMPTIED:
      case c_base_form_attributes::ATTRIBUTE_ON_ENDED:
      case c_base_form_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_form_attributes::ATTRIBUTE_ON_FOCUS:
      case c_base_form_attributes::ATTRIBUTE_ON_FOCUS_IN:
      case c_base_form_attributes::ATTRIBUTE_ON_FOCUS_OUT:
      case c_base_form_attributes::ATTRIBUTE_ON_HASH_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_INPUT:
      case c_base_form_attributes::ATTRIBUTE_ON_INVALID:
      case c_base_form_attributes::ATTRIBUTE_ON_KEY_DOWN:
      case c_base_form_attributes::ATTRIBUTE_ON_KEY_PRESS:
      case c_base_form_attributes::ATTRIBUTE_ON_KEY_UP:
      case c_base_form_attributes::ATTRIBUTE_ON_LOAD:
      case c_base_form_attributes::ATTRIBUTE_ON_LOADED_DATA:
      case c_base_form_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
      case c_base_form_attributes::ATTRIBUTE_ON_LOAD_START:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_OVER:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_OUT:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_UP:
      case c_base_form_attributes::ATTRIBUTE_ON_MESSAGE:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
      case c_base_form_attributes::ATTRIBUTE_ON_OPEN:
      case c_base_form_attributes::ATTRIBUTE_ON_ONLINE:
      case c_base_form_attributes::ATTRIBUTE_ON_OFFLINE:
      case c_base_form_attributes::ATTRIBUTE_ON_PAGE_SHOW:
      case c_base_form_attributes::ATTRIBUTE_ON_PAGE_HIDE:
      case c_base_form_attributes::ATTRIBUTE_ON_PASTE:
      case c_base_form_attributes::ATTRIBUTE_ON_PAUSE:
      case c_base_form_attributes::ATTRIBUTE_ON_PLAY:
      case c_base_form_attributes::ATTRIBUTE_ON_PLAYING:
      case c_base_form_attributes::ATTRIBUTE_ON_PROGRESS:
      case c_base_form_attributes::ATTRIBUTE_ON_POP_STATE:
      case c_base_form_attributes::ATTRIBUTE_ON_RESIZE:
      case c_base_form_attributes::ATTRIBUTE_ON_RESET:
      case c_base_form_attributes::ATTRIBUTE_ON_RATE_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_SCROLL:
      case c_base_form_attributes::ATTRIBUTE_ON_SEARCH:
      case c_base_form_attributes::ATTRIBUTE_ON_SELECT:
      case c_base_form_attributes::ATTRIBUTE_ON_SUBMIT:
      case c_base_form_attributes::ATTRIBUTE_ON_SEEKED:
      case c_base_form_attributes::ATTRIBUTE_ON_SEEKING:
      case c_base_form_attributes::ATTRIBUTE_ON_STALLED:
      case c_base_form_attributes::ATTRIBUTE_ON_SUSPEND:
      case c_base_form_attributes::ATTRIBUTE_ON_SHOW:
      case c_base_form_attributes::ATTRIBUTE_ON_STORAGE:
      case c_base_form_attributes::ATTRIBUTE_ON_TIME_UPDATE:
      case c_base_form_attributes::ATTRIBUTE_ON_TRANSITION_END:
      case c_base_form_attributes::ATTRIBUTE_ON_TOGGLE:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_END:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_START:
      case c_base_form_attributes::ATTRIBUTE_ON_UNLOAD:
      case c_base_form_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_WAITING:
      case c_base_form_attributes::ATTRIBUTE_ON_WHEEL:
      case c_base_form_attributes::ATTRIBUTE_PATTERN:
      case c_base_form_attributes::ATTRIBUTE_PLACE_HOLDER:
      case c_base_form_attributes::ATTRIBUTE_READONLY:
      case c_base_form_attributes::ATTRIBUTE_REQUIRED:
      case c_base_form_attributes::ATTRIBUTE_ROWS:
      case c_base_form_attributes::ATTRIBUTE_SELECTED:
      case c_base_form_attributes::ATTRIBUTE_SIZE:
      case c_base_form_attributes::ATTRIBUTE_SOURCE:
      case c_base_form_attributes::ATTRIBUTE_STEP:
      case c_base_form_attributes::ATTRIBUTE_TYPE:
      case c_base_form_attributes::ATTRIBUTE_WRAP:
      case c_base_form_attributes::ATTRIBUTE_VALUE:
        if (!is_string($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_FORM_NO_VALIDATED:
      case c_base_form_attributes::ATTRIBUTE_AUTO_COMPLETE:
      case c_base_form_attributes::ATTRIBUTE_AUTO_FOCUS:
      case c_base_form_attributes::ATTRIBUTE_CHALLENGE:
      case c_base_form_attributes::ATTRIBUTE_CHECKED:
      case c_base_form_attributes::ATTRIBUTE_DISABLED:
      case c_base_form_attributes::ATTRIBUTE_MULTIPLE:
        if (!is_bool($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_ACCEPT:
      case c_base_form_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
        if (!this->pr_validate_value_mime_type($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_COLUMNS:
      case c_base_form_attributes::ATTRIBUTE_MAXIMUM:
      case c_base_form_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
      case c_base_form_attributes::ATTRIBUTE_MINIMUM:
        if (!is_int($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_FORM_METHOD:
        if (!this->pr_validate_value_http_method($value)) {
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
   * @return c_base_return_int|c_base_return_string|c_base_return_bool|c_base_return_false
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
        case c_base_form_attributes::ATTRIBUTE_NONE:
          // should not be possible, so consider this an error (attributes set to NONE are actually unset from the array).
          return c_base_return_error::s_false();

        case c_base_form_attributes::ATTRIBUTE_ACTION:
        case c_base_form_attributes::ATTRIBUTE_DIRECTION_NAME:
        case c_base_form_attributes::ATTRIBUTE_FOR:
        case c_base_form_attributes::ATTRIBUTE_FORM:
        case c_base_form_attributes::ATTRIBUTE_FORM_ACTION:
        case c_base_form_attributes::ATTRIBUTE_FORM_TARGET:
        case c_base_form_attributes::ATTRIBUTE_KEY_TYPE:
        case c_base_form_attributes::ATTRIBUTE_LABEL:
        case c_base_form_attributes::ATTRIBUTE_LIST:
        case c_base_form_attributes::ATTRIBUTE_NAME:
        case c_base_form_attributes::ATTRIBUTE_ON_ABORT:
        case c_base_form_attributes::ATTRIBUTE_ON_AFTER_PRINT:
        case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_END:
        case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
        case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_start:
        case c_base_form_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
        case c_base_form_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
        case c_base_form_attributes::ATTRIBUTE_ON_BLUR:
        case c_base_form_attributes::ATTRIBUTE_ON_CLICK:
        case c_base_form_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
        case c_base_form_attributes::ATTRIBUTE_ON_COPY:
        case c_base_form_attributes::ATTRIBUTE_ON_CUT:
        case c_base_form_attributes::ATTRIBUTE_ON_CAN_PLAY:
        case c_base_form_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
        case c_base_form_attributes::ATTRIBUTE_ON_CHANGE:
        case c_base_form_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
        case c_base_form_attributes::ATTRIBUTE_ON_DRAG:
        case c_base_form_attributes::ATTRIBUTE_ON_DRAG_END:
        case c_base_form_attributes::ATTRIBUTE_ON_DRAG_ENTER:
        case c_base_form_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
        case c_base_form_attributes::ATTRIBUTE_ON_DRAG_OVER:
        case c_base_form_attributes::ATTRIBUTE_ON_DRAG_START:
        case c_base_form_attributes::ATTRIBUTE_ON_DROP:
        case c_base_form_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
        case c_base_form_attributes::ATTRIBUTE_ON_ERROR:
        case c_base_form_attributes::ATTRIBUTE_ON_EMPTIED:
        case c_base_form_attributes::ATTRIBUTE_ON_ENDED:
        case c_base_form_attributes::ATTRIBUTE_ON_ERROR:
        case c_base_form_attributes::ATTRIBUTE_ON_FOCUS:
        case c_base_form_attributes::ATTRIBUTE_ON_FOCUS_IN:
        case c_base_form_attributes::ATTRIBUTE_ON_FOCUS_OUT:
        case c_base_form_attributes::ATTRIBUTE_ON_HASH_CHANGE:
        case c_base_form_attributes::ATTRIBUTE_ON_INPUT:
        case c_base_form_attributes::ATTRIBUTE_ON_INVALID:
        case c_base_form_attributes::ATTRIBUTE_ON_KEY_DOWN:
        case c_base_form_attributes::ATTRIBUTE_ON_KEY_PRESS:
        case c_base_form_attributes::ATTRIBUTE_ON_KEY_UP:
        case c_base_form_attributes::ATTRIBUTE_ON_LOAD:
        case c_base_form_attributes::ATTRIBUTE_ON_LOADED_DATA:
        case c_base_form_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
        case c_base_form_attributes::ATTRIBUTE_ON_LOAD_START:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_OVER:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_OUT:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_UP:
        case c_base_form_attributes::ATTRIBUTE_ON_MESSAGE:
        case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
        case c_base_form_attributes::ATTRIBUTE_ON_OPEN:
        case c_base_form_attributes::ATTRIBUTE_ON_ONLINE:
        case c_base_form_attributes::ATTRIBUTE_ON_OFFLINE:
        case c_base_form_attributes::ATTRIBUTE_ON_PAGE_SHOW:
        case c_base_form_attributes::ATTRIBUTE_ON_PAGE_HIDE:
        case c_base_form_attributes::ATTRIBUTE_ON_PASTE:
        case c_base_form_attributes::ATTRIBUTE_ON_PAUSE:
        case c_base_form_attributes::ATTRIBUTE_ON_PLAY:
        case c_base_form_attributes::ATTRIBUTE_ON_PLAYING:
        case c_base_form_attributes::ATTRIBUTE_ON_PROGRESS:
        case c_base_form_attributes::ATTRIBUTE_ON_POP_STATE:
        case c_base_form_attributes::ATTRIBUTE_ON_RESIZE:
        case c_base_form_attributes::ATTRIBUTE_ON_RESET:
        case c_base_form_attributes::ATTRIBUTE_ON_RATE_CHANGE:
        case c_base_form_attributes::ATTRIBUTE_ON_SCROLL:
        case c_base_form_attributes::ATTRIBUTE_ON_SEARCH:
        case c_base_form_attributes::ATTRIBUTE_ON_SELECT:
        case c_base_form_attributes::ATTRIBUTE_ON_SUBMIT:
        case c_base_form_attributes::ATTRIBUTE_ON_SEEKED:
        case c_base_form_attributes::ATTRIBUTE_ON_SEEKING:
        case c_base_form_attributes::ATTRIBUTE_ON_STALLED:
        case c_base_form_attributes::ATTRIBUTE_ON_SUSPEND:
        case c_base_form_attributes::ATTRIBUTE_ON_SHOW:
        case c_base_form_attributes::ATTRIBUTE_ON_STORAGE:
        case c_base_form_attributes::ATTRIBUTE_ON_TIME_UPDATE:
        case c_base_form_attributes::ATTRIBUTE_ON_TRANSITION_END:
        case c_base_form_attributes::ATTRIBUTE_ON_TOGGLE:
        case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
        case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_END:
        case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
        case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_START:
        case c_base_form_attributes::ATTRIBUTE_ON_UNLOAD:
        case c_base_form_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
        case c_base_form_attributes::ATTRIBUTE_ON_WAITING:
        case c_base_form_attributes::ATTRIBUTE_ON_WHEEL:
        case c_base_form_attributes::ATTRIBUTE_PATTERN:
        case c_base_form_attributes::ATTRIBUTE_READONLY:
        case c_base_form_attributes::ATTRIBUTE_REQUIRED:
        case c_base_form_attributes::ATTRIBUTE_ROWS:
        case c_base_form_attributes::ATTRIBUTE_SELECTED:
        case c_base_form_attributes::ATTRIBUTE_SIZE:
        case c_base_form_attributes::ATTRIBUTE_SOURCE:
        case c_base_form_attributes::ATTRIBUTE_STEP:
        case c_base_form_attributes::ATTRIBUTE_TYPE:
        case c_base_form_attributes::ATTRIBUTE_WRAP:
        case c_base_form_attributes::ATTRIBUTE_PLACE_HOLDER:
        case c_base_form_attributes::ATTRIBUTE_VALUE:
          return c_base_return_string::s_new($value);

        case c_base_form_attributes::ATTRIBUTE_FORM_NO_VALIDATED:
        case c_base_form_attributes::ATTRIBUTE_AUTO_COMPLETE:
        case c_base_form_attributes::ATTRIBUTE_AUTO_FOCUS:
        case c_base_form_attributes::ATTRIBUTE_CHALLENGE:
        case c_base_form_attributes::ATTRIBUTE_CHECKED:
        case c_base_form_attributes::ATTRIBUTE_DISABLED:
        case c_base_form_attributes::ATTRIBUTE_MULTIPLE:
          return c_base_return_bool::s_new($value);

        case c_base_form_attributes::ATTRIBUTE_ACCEPT:
        case c_base_form_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
          return c_base_return_int::s_new($value);

        case c_base_form_attributes::ATTRIBUTE_COLUMNS:
        case c_base_form_attributes::ATTRIBUTE_MAXIMUM:
        case c_base_form_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
        case c_base_form_attributes::ATTRIBUTE_MINIMUM:
          return c_base_return_int::s_new($value);

        case c_base_form_attributes::ATTRIBUTE_FORM_METHOD:
          return c_base_return_int::s_new($value);

        default:
          return new c_base_return_false();
      }
    }

    $this->attribute[$attribute] = $value;

    return new c_base_return_false();
  }

  /**
   * Assign the specified numeric id as the parent tag.
   *
   * @param int $parent_id
   *   The numeric id to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_parent($parent_id) {
    if (!is_int($parent_id)) {
      return c_base_return_error::s_false();
    }

    $this->parent = $parent_id;
    return new c_base_return_true();
  }

  /**
   * Get the parent tag numeric id associated with this.
   *
   * @return c_base_return_int|c_base_return_false
   *   The tag type assigned to this class.
   *   FALSE without error bit is set when no parent is defined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_parent() {
    if (!isset($this->parent)) {
      return new c_base_return_false();
    }

    return new c_base_return_int($this->parent);
  }

  /**
   * Add a child tag.
   *
   * @param c_base_form_tag $child
   *   The numeric id to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_child($child_tag) {
    if (!($child_tag instanceof c_base_form_tag)) {
      return c_base_return_error::s_false();
    }

    // this requires the class to have a tag id assigned.
    if (!is_int($this->id)) {
      return c_base_return_error::s_false();
    }

    $child_id = $child_tag->get_id();
    if (!($child_id instanceof c_base_return_int)) {
      unset($child_id);
      return c_base_return_error::s_false();
    }
    $child_id = $child_id->get_value_exact();

    $child_tag->set_parent($this->id);
    $this->children[$child_id] = $child_tag;
    unset($child_id);

    return new c_base_return_true();
  }

  /**
   * Get the specified child by its unique numeric tag id.
   *
   * @param int $child_id
   *   The unique numeric tag id of the child.
   *
   * @return c_base_form_return_tag|c_base_return_false
   *   The tag type assigned to this class.
   *   FALSE without error bit is set when the child is not defined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_child($child_id) {
    if (!is_array($this->children) || !array_key_exists($child_id, $this->children)) {
      return new c_base_return_false();
    }

    if (!($this->children[$child_id] instanceof c_base_form_tag)) {
      return c_base_return_error::s_false();
    }

    return new c_base_form_return_tag($this->children[$child_id]);
  }

  /**
   * Assign an array of child tags.
   *
   * @param array $children
   *   An array of children to assign.
   *   The array key must be an integer or it is ignore.
   *   The array value must be a c_base_form_tag instance or it is ignored.
   * @param bool $append
   *   (optional) When TRUE, child elements are appended.
   *   When FALSE, any existing children are removed.
   *
   * @return c_base_return_int|c_base_return_false
   *   An integer representing the number of children successfully added.
   *   FALSE with error bit set is returned on error.
   */
  public function set_children($children, $append = FALSE) {
    if (!is_array($children)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    if (!$append) {
      $this->children = array();
    }

    $added = 0;
    foreach ($children as $child_id => $child_tag) {
      if (!is_numeric($child_id) || !($child_tag instanceof c_base_form_tag)) {
        continue;
      }

      $child_tag_id = $child_tag->get_id();
      if (!($child_tag_id instanceof c_base_return_int) || $child_id != $child_tag_id->get_value_exact()) {
        unset($child_tag_id);
        continue;
      }
      unset($child_tag_id);

      $child_tag->set_parent($this->id);
      $this->children[(int) $child_id] = $child_tag;
      $added++;
    }
    unset($child_id);
    unset($child_tag);

    return new c_base_return_int($added);
  }

  /**
   * Get all assigned child tags.
   *
   * @return c_base_return_array|c_base_return_false
   *   The array of child tags.
   *   FALSE with error bit set is returned on error.
   */
  public function get_children() {
    if (!is_array($this->children)) {
      $this->children = array();
    }

    return new c_base_return_array($this->children);
  }

  /**
   * Protected function for mime values.
   *
   * @param int $value
   *   The value of the attribute populate from c_base_mime.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE otherwise.
   */
  protected function pr_validate_value_mime_type($value) {
    if (!is_int($value)) {
      return FALSE;
    }

    switch ($value) {
      case c_base_mime::CATEGORY_UNKNOWN:
      case c_base_mime::CATEGORY_PROVIDED:
      case c_base_mime::CATEGORY_STREAM:
      case c_base_mime::CATEGORY_TEXT:
      case c_base_mime::CATEGORY_IMAGE:
      case c_base_mime::CATEGORY_AUDIO:
      case c_base_mime::CATEGORY_VIDEO:
      case c_base_mime::CATEGORY_DOCUMENT:
      case c_base_mime::CATEGORY_CONTAINER:
      case c_base_mime::CATEGORY_APPLICATION:
      case c_base_mime::TYPE_UNKNOWN:
      case c_base_mime::TYPE_PROVIDED:
      case c_base_mime::TYPE_STREAM:
      case c_base_mime::TYPE_TEXT_PLAIN:
      case c_base_mime::TYPE_TEXT_HTML:
      case c_base_mime::TYPE_TEXT_RSS:
      case c_base_mime::TYPE_TEXT_ICAL:
      case c_base_mime::TYPE_TEXT_CSV:
      case c_base_mime::TYPE_TEXT_XML:
      case c_base_mime::TYPE_TEXT_CSS:
      case c_base_mime::TYPE_TEXT_JS:
      case c_base_mime::TYPE_TEXT_JSON:
      case c_base_mime::TYPE_TEXT_RICH:
      case c_base_mime::TYPE_TEXT_XHTML:
      case c_base_mime::TYPE_TEXT_PS:
      case c_base_mime::TYPE_IMAGE_PNG:
      case c_base_mime::TYPE_IMAGE_GIF:
      case c_base_mime::TYPE_IMAGE_JPEG:
      case c_base_mime::TYPE_IMAGE_BMP:
      case c_base_mime::TYPE_IMAGE_SVG:
      case c_base_mime::TYPE_IMAGE_TIFF:
      case c_base_mime::TYPE_AUDIO_WAV:
      case c_base_mime::TYPE_AUDIO_OGG:
      case c_base_mime::TYPE_AUDIO_MP3:
      case c_base_mime::TYPE_AUDIO_MP4:
      case c_base_mime::TYPE_AUDIO_MIDI:
      case c_base_mime::TYPE_VIDEO_MPEG:
      case c_base_mime::TYPE_VIDEO_OGG:
      case c_base_mime::TYPE_VIDEO_H264:
      case c_base_mime::TYPE_VIDEO_QUICKTIME:
      case c_base_mime::TYPE_VIDEO_DV:
      case c_base_mime::TYPE_VIDEO_JPEG:
      case c_base_mime::TYPE_VIDEO_WEBM:
      case c_base_mime::TYPE_DOCUMENT_LIBRECHART:
      case c_base_mime::TYPE_DOCUMENT_LIBREFORMULA:
      case c_base_mime::TYPE_DOCUMENT_LIBREGRAPHIC:
      case c_base_mime::TYPE_DOCUMENT_LIBREPRESENTATION:
      case c_base_mime::TYPE_DOCUMENT_LIBRESPREADSHEET:
      case c_base_mime::TYPE_DOCUMENT_LIBRETEXT:
      case c_base_mime::TYPE_DOCUMENT_LIBREHTML:
      case c_base_mime::TYPE_DOCUMENT_PDF:
      case c_base_mime::TYPE_DOCUMENT_ABIWORD:
      case c_base_mime::TYPE_DOCUMENT_MSWORD:
      case c_base_mime::TYPE_DOCUMENT_MSEXCEL:
      case c_base_mime::TYPE_DOCUMENT_MSPOWERPOINT:
      case c_base_mime::TYPE_CONTAINER_TAR:
      case c_base_mime::TYPE_CONTAINER_CPIO:
      case c_base_mime::TYPE_CONTAINER_JAVA:
        break;
      default:
        return FALSE;
    }

    return TRUE;
  }

  /**
   * Protected function for http method values.
   *
   * Only GET and POST methods are supported.
   *
   * @param int $value
   *   The value of the attribute populate from http method.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE otherwise.
   */
  protected function pr_validate_value_http_method($value) {
    if (!is_int($value)) {
      return FALSE;
    }

    switch ($value) {
      case c_base_http::HTTP_METHOD_NONE:
      case c_base_http::HTTP_METHOD_GET:
      case c_base_http::HTTP_METHOD_POST:
        break;
      default:
        return FALSE;
    }

    return TRUE;
  }
}

/**
 * A generic class for form fields.
 *
 * The unique tag id is an integer to be used for internal purposes but may be exposed on output.
 * If the id attribute is defined, then on output the id attribute is used for the HTML tag.
 *
 * This uses a simple approach to store different form inputs.
 * Each input has a single depth group (with NULL being the default group).
 * The input fields do not relate directly to the form input.
 *
 * @see: https://www.w3.org/TR/html5/forms.html#forms
 */
class c_base_form {
  private $id;
  private $inputs;
  private $attributes;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->id = NULL;
    $this->tags = array();
    $this->attributes = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->tags);
    unset($this->attributes);
  }

  /**
   * Assign a unique numeric id to represent this form.
   *
   * @param int $id
   *   The unique form tag id to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id($id) {
    if (!is_int($id)) {
      return c_base_return_error::s_false();
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Get the unique id assigned to this object.
   *
   * @return c_base_return_int|c_base_return_false
   *   The unique numeric id assigned to this object.
   *   FALSE is returned if no id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      return c_base_return_false();
    }

    return new c_base_return_int($this->id);
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
      case c_base_form_attributes::ATTRIBUTE_NONE:
        unset($this->attribute[$attribute]);
        return new c_base_return_true();

      case c_base_form_attributes::ATTRIBUTE_ACTION:
      case c_base_form_attributes::ATTRIBUTE_DIRECTION_NAME:
      case c_base_form_attributes::ATTRIBUTE_FOR:
      case c_base_form_attributes::ATTRIBUTE_FORM:
      case c_base_form_attributes::ATTRIBUTE_FORM_ACTION:
      case c_base_form_attributes::ATTRIBUTE_FORM_TARGET:
      case c_base_form_attributes::ATTRIBUTE_KEY_TYPE:
      case c_base_form_attributes::ATTRIBUTE_LABEL:
      case c_base_form_attributes::ATTRIBUTE_LIST:
      case c_base_form_attributes::ATTRIBUTE_NAME:
      case c_base_form_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_form_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_form_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_form_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_form_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_form_attributes::ATTRIBUTE_ON_BLUR:
      case c_base_form_attributes::ATTRIBUTE_ON_CLICK:
      case c_base_form_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
      case c_base_form_attributes::ATTRIBUTE_ON_COPY:
      case c_base_form_attributes::ATTRIBUTE_ON_CUT:
      case c_base_form_attributes::ATTRIBUTE_ON_CAN_PLAY:
      case c_base_form_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case c_base_form_attributes::ATTRIBUTE_ON_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_END:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_ENTER:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_OVER:
      case c_base_form_attributes::ATTRIBUTE_ON_DRAG_START:
      case c_base_form_attributes::ATTRIBUTE_ON_DROP:
      case c_base_form_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_form_attributes::ATTRIBUTE_ON_EMPTIED:
      case c_base_form_attributes::ATTRIBUTE_ON_ENDED:
      case c_base_form_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_form_attributes::ATTRIBUTE_ON_FOCUS:
      case c_base_form_attributes::ATTRIBUTE_ON_FOCUS_IN:
      case c_base_form_attributes::ATTRIBUTE_ON_FOCUS_OUT:
      case c_base_form_attributes::ATTRIBUTE_ON_HASH_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_INPUT:
      case c_base_form_attributes::ATTRIBUTE_ON_INVALID:
      case c_base_form_attributes::ATTRIBUTE_ON_KEY_DOWN:
      case c_base_form_attributes::ATTRIBUTE_ON_KEY_PRESS:
      case c_base_form_attributes::ATTRIBUTE_ON_KEY_UP:
      case c_base_form_attributes::ATTRIBUTE_ON_LOAD:
      case c_base_form_attributes::ATTRIBUTE_ON_LOADED_DATA:
      case c_base_form_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
      case c_base_form_attributes::ATTRIBUTE_ON_LOAD_START:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_OVER:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_OUT:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_UP:
      case c_base_form_attributes::ATTRIBUTE_ON_MESSAGE:
      case c_base_form_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
      case c_base_form_attributes::ATTRIBUTE_ON_OPEN:
      case c_base_form_attributes::ATTRIBUTE_ON_ONLINE:
      case c_base_form_attributes::ATTRIBUTE_ON_OFFLINE:
      case c_base_form_attributes::ATTRIBUTE_ON_PAGE_SHOW:
      case c_base_form_attributes::ATTRIBUTE_ON_PAGE_HIDE:
      case c_base_form_attributes::ATTRIBUTE_ON_PASTE:
      case c_base_form_attributes::ATTRIBUTE_ON_PAUSE:
      case c_base_form_attributes::ATTRIBUTE_ON_PLAY:
      case c_base_form_attributes::ATTRIBUTE_ON_PLAYING:
      case c_base_form_attributes::ATTRIBUTE_ON_PROGRESS:
      case c_base_form_attributes::ATTRIBUTE_ON_POP_STATE:
      case c_base_form_attributes::ATTRIBUTE_ON_RESIZE:
      case c_base_form_attributes::ATTRIBUTE_ON_RESET:
      case c_base_form_attributes::ATTRIBUTE_ON_RATE_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_SCROLL:
      case c_base_form_attributes::ATTRIBUTE_ON_SEARCH:
      case c_base_form_attributes::ATTRIBUTE_ON_SELECT:
      case c_base_form_attributes::ATTRIBUTE_ON_SUBMIT:
      case c_base_form_attributes::ATTRIBUTE_ON_SEEKED:
      case c_base_form_attributes::ATTRIBUTE_ON_SEEKING:
      case c_base_form_attributes::ATTRIBUTE_ON_STALLED:
      case c_base_form_attributes::ATTRIBUTE_ON_SUSPEND:
      case c_base_form_attributes::ATTRIBUTE_ON_SHOW:
      case c_base_form_attributes::ATTRIBUTE_ON_STORAGE:
      case c_base_form_attributes::ATTRIBUTE_ON_TIME_UPDATE:
      case c_base_form_attributes::ATTRIBUTE_ON_TRANSITION_END:
      case c_base_form_attributes::ATTRIBUTE_ON_TOGGLE:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_END:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
      case c_base_form_attributes::ATTRIBUTE_ON_TOUCH_START:
      case c_base_form_attributes::ATTRIBUTE_ON_UNLOAD:
      case c_base_form_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
      case c_base_form_attributes::ATTRIBUTE_ON_WAITING:
      case c_base_form_attributes::ATTRIBUTE_ON_WHEEL:
      case c_base_form_attributes::ATTRIBUTE_PATTERN:
      case c_base_form_attributes::ATTRIBUTE_PLACE_HOLDER:
      case c_base_form_attributes::ATTRIBUTE_READONLY:
      case c_base_form_attributes::ATTRIBUTE_REQUIRED:
      case c_base_form_attributes::ATTRIBUTE_ROWS:
      case c_base_form_attributes::ATTRIBUTE_SELECTED:
      case c_base_form_attributes::ATTRIBUTE_SIZE:
      case c_base_form_attributes::ATTRIBUTE_SOURCE:
      case c_base_form_attributes::ATTRIBUTE_STEP:
      case c_base_form_attributes::ATTRIBUTE_TYPE:
      case c_base_form_attributes::ATTRIBUTE_WRAP:
      case c_base_form_attributes::ATTRIBUTE_VALUE:
        if (!is_string($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_FORM_NO_VALIDATED:
      case c_base_form_attributes::ATTRIBUTE_AUTO_COMPLETE:
      case c_base_form_attributes::ATTRIBUTE_AUTO_FOCUS:
      case c_base_form_attributes::ATTRIBUTE_CHALLENGE:
      case c_base_form_attributes::ATTRIBUTE_CHECKED:
      case c_base_form_attributes::ATTRIBUTE_DISABLED:
      case c_base_form_attributes::ATTRIBUTE_MULTIPLE:
        if (!is_bool($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_ACCEPT:
      case c_base_form_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
        if (!this->pr_validate_value_mime_type($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_COLUMNS:
      case c_base_form_attributes::ATTRIBUTE_MAXIMUM:
      case c_base_form_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
      case c_base_form_attributes::ATTRIBUTE_MINIMUM:
        if (!is_int($value)) {
          return c_base_return_false();
        }
        break;

      case c_base_form_attributes::ATTRIBUTE_FORM_METHOD:
        if (!this->pr_validate_value_http_method($value)) {
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
}

