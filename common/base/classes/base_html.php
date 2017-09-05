<?php
/**
 * @file
 * Provides a class for managing HTML5 Markup.
 *
 * This provides basic HTML5 support in a relatively straight-forward and rough way.
 *
 * @see: https://www.w3.org/TR/html5/
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_markup.php');

/**
 * A generic container for html tags.
 *
 * This uses a simple approach to store different html tags.
 *
 * @todo: add support for non-standard tag attributes, which will just be a string or NULL.
 */
class c_base_html extends c_base_return {
  protected $id;
  protected $attributes;
  protected $attributes_body;
  protected $headers;
  protected $body;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->id = NULL;

    $this->attributes      = array();
    $this->attributes_body = array();

    $this->headers = array();
    $this->body    = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);

    unset($this->attributes);
    unset($this->attributes_body);

    unset($this->headers);
    unset($this->body);

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
   * Sanitizes a string to ensure it can be used as a CSS class name (or an id attribute name).
   *
   * Full performs the complete sanitization, only allowing dash, a-z, A-Z, underscore, 0-9,and ISO characters.
   * Partial will replace all non-words with '_'.
   * - After calling partial, a full sanitization still must be performed.
   *
   * @param string $text
   *   The text to sanitize.
   * @param bool $partial
   *   (optional) When TRUE, the text is treated as a partial name.
   *   When FALSE, the text is treated as a full name.
   *
   * @return c_base_return_string
   *   A string is always returned.
   *   An empty string with the error bit set is returned on error.
   */
  public static function sanitize_css($text, $partial = FALSE) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (!is_bool($partial)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'partial', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if ($partial) {
      $sanitized = preg_replace('/(\W)+/i', '_', $text);
    }
    else {
      // (From drupal 7's drupal_clean_css_identifier() function.)
      // Allowed characters: the dash (U+002D),  a-z (U+0030 - U+0039), A-Z (U+0041 - U+005A), the underscore (U+005F), 0-9 (U+0061 - U+007A), and ISO 10646 characters U+00A1 and higher.
      $sanitized = preg_replace('/[^\x{002D}\x{0030}-\x{0039}\x{0041}-\x{005A}\x{005F}\x{0061}-\x{007A}\x{00A1}-\x{FFFF}]/u', '', $text);
    }

    if (is_string($sanitized)) {
      return c_base_return_string::s_new($sanitized);
    }
    unset($sanitized);

    $error = c_base_error::s_log(' ' . $response['error']['message'], array('arguments' => array(':{operation_name}' => 'preg_replace', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_value('', 'c_base_return_string', $error);
  }

  /**
   * Assign a unique numeric id to represent this HTML page.
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Assign the specified attribute.
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return $this->p_set_attribute($attribute, $value);
  }

  /**
   * Assign the specified attribute to the body tag.
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
  public function set_attribute_body($attribute, $value) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return $this->p_set_attribute($attribute, $value, TRUE);
  }

  /**
   * Assign a tag to the HTML.
   *
   * @param c_base_markup_tag $tag
   *   The html tag tp assign.
   * @param int|null $delta
   *   (optional) A position in the page to assign the tag.
   *   If NULL, then the tag is appended.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_tag($tag, $delta = NULL) {
    if (!($tag instanceof c_base_markup_tag)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'tag', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($delta) && !is_int($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'delta', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->body)) {
      $this->body = array();
    }

    if (is_null($delta)) {
      $this->body[] = $tag;
    }
    else {
      $this->body[$delta] = $tag;
    }
    return new c_base_return_true();
  }

  /**
   * Assign a header to the HTML.
   *
   * @param c_base_markup_tag $header
   *   The html header tp assign.
   * @param int|null $delta
   *   (optional) A position in the page to assign the header.
   *   If NULL, then the header is appended.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer representing the delta of where the header was added.
   *   FALSE with error bit set is returned on error.
   */
  public function set_header($header, $delta = NULL) {
    if (!($header instanceof c_base_markup_tag)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($delta) && !is_int($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'delta', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // only certain header types are allowed.
    $type = $header->get_type()->get_value_exact();
    switch ($type) {
      case c_base_markup_tag::TYPE_BASE:
      case c_base_markup_tag::TYPE_LINK:
      case c_base_markup_tag::TYPE_META:
      case c_base_markup_tag::TYPE_NO_SCRIPT:
      case c_base_markup_tag::TYPE_SCRIPT:
      case c_base_markup_tag::TYPE_STYLE:
      case c_base_markup_tag::TYPE_TITLE:
        break;
      default:
        return new c_base_retun_false();
    }

    if (!is_array($this->headers)) {
      $this->headers = array();
    }

    if (is_null($delta)) {
      $at = count($this->headers);
      $this->headers[] = $header;
    }
    else {
      $at = $delta;
      $this->headers[$delta] = $header;
    }

    return c_base_return_int::s_new($at);
  }

  /**
   * Get the unique id assigned to this HTML page.
   *
   * @return c_base_return_int|c_base_return_status
   *   The unique numeric id assigned to this object.
   *   FALSE is returned if no id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id() {
    if (!is_int($this->id)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->id);
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

    return c_base_return_array::s_new($this->attributes);
  }

  /**
   * Get the value of a single attribute assigned to this object.
   *
   * @param int $attribute
   *   The attribute to assign.
   *
   * @return c_base_return_int|c_base_return_string|c_base_return_bool|c_base_return_status
   *   The value assigned to the attribute (the data type is different per attribute).
   *   FALSE is returned if the element does not exist.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attribute($attribute) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return $this->p_get_attribute($attribute);
  }

  /**
   * Get the value of a single attribute assigned to this object.
   *
   * These attributes are assigned to the body tag.
   *
   * @param int $attribute
   *   The attribute to assign.
   *
   * @return c_base_return_int|c_base_return_string|c_base_return_bool|c_base_return_status
   *   The value assigned to the attribute (the data type is different per attribute).
   *   FALSE is returned if the element does not exist.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attribute_body($attribute) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    return $this->p_get_attribute($attribute, TRUE);
  }

  /**
   * Get tag from the HTML.
   *
   * @param int $delta
   *   The position in the array of the tag to get.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   The tag, if found at delta.
   *   FALSE is returned if no tag is at delta.
   *   FALSE with error bit set is returned on error.
   */
  public function get_tag($delta) {
    if (!is_int($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'delta', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!array_key_exists($delta, $this->body)) {
      return new c_base_return_false();
    }

    return $this->body[$delta];
  }

  /**
   * Get all body assigned to the HTML.
   *
   * @return c_base_return_array
   *   FALSE is returned if no tag is at delta.
   *   FALSE with error bit set is returned on error.
   */
  public function get_body() {
    if (!is_array($this->body)) {
      return c_base_return_array::s_new(array());
    }

    return c_base_return_array::s_new($this->body);
  }

  /**
   * Get header from the HTML.
   *
   * @param int $delta
   *   The position in the array of the header to get.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   The header, if found at delta.
   *   FALSE is returned if no header is at delta.
   *   FALSE with error bit set is returned on error.
   */
  public function get_header($delta) {
    if (!is_int($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'delta', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!array_key_exists($delta, $this->headers)) {
      return new c_base_return_false();
    }

    return $this->headers[$delta];
  }

  /**
   * Get all headers assigned to the HTML.
   *
   * @return c_base_return_array
   *   FALSE is returned if no header is at delta.
   *   FALSE with error bit set is returned on error.
   */
  public function get_headers() {
    if (!is_array($this->headers)) {
      return c_base_return_array::s_new(array());
    }

    return c_base_return_array::s_new($this->headers);
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
   * Protected function for character set values.
   *
   * @param int $value
   *   The value of the attribute populate from c_base_charset.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE otherwise.
   */
  protected function pr_validate_value_character_set($value) {
    if (!is_int($value)) {
      return FALSE;
    }

    switch ($value) {
      case c_base_charset::UNDEFINED:
      case c_base_charset::ASCII:
      case c_base_charset::UTF_8:
      case c_base_charset::UTF_16:
      case c_base_charset::UTF_32:
      case c_base_charset::ISO_8859_1:
      case c_base_charset::ISO_8859_2:
      case c_base_charset::ISO_8859_3:
      case c_base_charset::ISO_8859_4:
      case c_base_charset::ISO_8859_5:
      case c_base_charset::ISO_8859_6:
      case c_base_charset::ISO_8859_7:
      case c_base_charset::ISO_8859_8:
      case c_base_charset::ISO_8859_9:
      case c_base_charset::ISO_8859_10:
      case c_base_charset::ISO_8859_11:
      case c_base_charset::ISO_8859_12:
      case c_base_charset::ISO_8859_13:
      case c_base_charset::ISO_8859_14:
      case c_base_charset::ISO_8859_15:
      case c_base_charset::ISO_8859_16:
        break;
      default:
        return FALSE;
    }

    return TRUE;
  }

  /**
   * Assign the specified tag.
   *
   * @param int $attribute
   *   The attribute to assign.
   * @param $value
   *   The value of the attribute.
   *   The actual value type is specific to each attribute type.
   * @param bool $body
   *   (optional) When TRUE, the body attributes are assigned.
   *   When FALSE, the normal attributes are assigned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  private function p_set_attribute($attribute, $value, $body = FALSE) {
    switch ($attribute) {
      case c_base_markup_attributes::ATTRIBUTE_NONE:
        unset($this->attribute[$attribute]);
        return new c_base_return_true();

      case c_base_markup_attributes::ATTRIBUTE_ABBR:
      case c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY:
      case c_base_markup_attributes::ATTRIBUTE_ACTION:
      case c_base_markup_attributes::ATTRIBUTE_ALTERNATE:
      case c_base_markup_attributes::ATTRIBUTE_BY:
      case c_base_markup_attributes::ATTRIBUTE_CALCULATE_MODE:
      case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH:
      case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_CITE:
      case c_base_markup_attributes::ATTRIBUTE_COLOR:
      case c_base_markup_attributes::ATTRIBUTE_COORDINATES:
      case c_base_markup_attributes::ATTRIBUTE_CONTENT:
      case c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE:
      case c_base_markup_attributes::ATTRIBUTE_CROSS_ORIGIN:
      case c_base_markup_attributes::ATTRIBUTE_D:
      case c_base_markup_attributes::ATTRIBUTE_DATA:
      case c_base_markup_attributes::ATTRIBUTE_DATE_TIME:
      case c_base_markup_attributes::ATTRIBUTE_DIRECTION_NAME:
      case c_base_markup_attributes::ATTRIBUTE_DOWNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_DURATION:
      case c_base_markup_attributes::ATTRIBUTE_FILL:
      case c_base_markup_attributes::ATTRIBUTE_FILL_RULE:
      case c_base_markup_attributes::ATTRIBUTE_FILL_STROKE:
      case c_base_markup_attributes::ATTRIBUTE_FONT_SPECIFICATION:
      case c_base_markup_attributes::ATTRIBUTE_FOR:
      case c_base_markup_attributes::ATTRIBUTE_FORM:
      case c_base_markup_attributes::ATTRIBUTE_FORM_ACTION:
      case c_base_markup_attributes::ATTRIBUTE_FORM_TARGET:
      case c_base_markup_attributes::ATTRIBUTE_FORMAT:
      case c_base_markup_attributes::ATTRIBUTE_FROM:
      case c_base_markup_attributes::ATTRIBUTE_GLYPH_REFERENCE:
      case c_base_markup_attributes::ATTRIBUTE_GRADIANT_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_GRADIANT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_GRAPHICS:
      case c_base_markup_attributes::ATTRIBUTE_HEADERS:
      case c_base_markup_attributes::ATTRIBUTE_HEIGHT:
      case c_base_markup_attributes::ATTRIBUTE_HREF:
      case c_base_markup_attributes::ATTRIBUTE_HREF_NO:
      case c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV:
      case c_base_markup_attributes::ATTRIBUTE_ICON:
      case c_base_markup_attributes::ATTRIBUTE_ID:
      case c_base_markup_attributes::ATTRIBUTE_IN:
      case c_base_markup_attributes::ATTRIBUTE_IN_2:
      case c_base_markup_attributes::ATTRIBUTE_IS_MAP:
      case c_base_markup_attributes::ATTRIBUTE_KEY_POINTS:
      case c_base_markup_attributes::ATTRIBUTE_KEY_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_KIND:
      case c_base_markup_attributes::ATTRIBUTE_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_LENGTH_ADJUST:
      case c_base_markup_attributes::ATTRIBUTE_LIST:
      case c_base_markup_attributes::ATTRIBUTE_LOCAL:
      case c_base_markup_attributes::ATTRIBUTE_LONG_DESCRIPTION:
      case c_base_markup_attributes::ATTRIBUTE_MARKERS:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MASK_CONTENT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MASK_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM:
      case c_base_markup_attributes::ATTRIBUTE_MEDIA:
      case c_base_markup_attributes::ATTRIBUTE_METHOD:
      case c_base_markup_attributes::ATTRIBUTE_MODE:
      case c_base_markup_attributes::ATTRIBUTE_MINIMUM:
      case c_base_markup_attributes::ATTRIBUTE_NAME:
      case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_BLUR:
      case c_base_markup_attributes::ATTRIBUTE_ON_CANCEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_CLICK:
      case c_base_markup_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
      case c_base_markup_attributes::ATTRIBUTE_ON_COPY:
      case c_base_markup_attributes::ATTRIBUTE_ON_CUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case c_base_markup_attributes::ATTRIBUTE_ON_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_CUE_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_ENTER:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_OVER:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_DROP:
      case c_base_markup_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_EMPTIED:
      case c_base_markup_attributes::ATTRIBUTE_ON_ENDED:
      case c_base_markup_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_IN:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_OUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_HASH_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_INPUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_INSTALLED:
      case c_base_markup_attributes::ATTRIBUTE_ON_INVALID:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_DOWN:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_PRESS:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_UP:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_DATA:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOAD_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OVER:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_UP:
      case c_base_markup_attributes::ATTRIBUTE_ON_MESSAGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_OPEN:
      case c_base_markup_attributes::ATTRIBUTE_ON_ONLINE:
      case c_base_markup_attributes::ATTRIBUTE_ON_OFFLINE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_HIDE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PASTE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAUSE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_ON_PLAYING:
      case c_base_markup_attributes::ATTRIBUTE_ON_PROGRESS:
      case c_base_markup_attributes::ATTRIBUTE_ON_POP_STATE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RATED_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RESIZE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RESET:
      case c_base_markup_attributes::ATTRIBUTE_ON_RATE_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_SCROLL:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEARCH:
      case c_base_markup_attributes::ATTRIBUTE_ON_SELECT:
      case c_base_markup_attributes::ATTRIBUTE_ON_SUBMIT:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEEKED:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEEKING:
      case c_base_markup_attributes::ATTRIBUTE_ON_STALLED:
      case c_base_markup_attributes::ATTRIBUTE_ON_SUSPEND:
      case c_base_markup_attributes::ATTRIBUTE_ON_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_ON_STORAGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TIME_UPDATE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TRANSITION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOGGLE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_WAITING:
      case c_base_markup_attributes::ATTRIBUTE_ON_WHEEL:
      case c_base_markup_attributes::ATTRIBUTE_OFFSET:
      case c_base_markup_attributes::ATTRIBUTE_OPEN:
      case c_base_markup_attributes::ATTRIBUTE_ORIENTATION:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_CONTENT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_PATH:
      case c_base_markup_attributes::ATTRIBUTE_PATH_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_PLACE_HOLDER:
      case c_base_markup_attributes::ATTRIBUTE_POINTS:
      case c_base_markup_attributes::ATTRIBUTE_POSTER:
      case c_base_markup_attributes::ATTRIBUTE_PRELOAD:
      case c_base_markup_attributes::ATTRIBUTE_PRESERVE_ASPECT_RATIO:
      case c_base_markup_attributes::ATTRIBUTE_RADIO_GROUP:
      case c_base_markup_attributes::ATTRIBUTE_SANDBOX:
      case c_base_markup_attributes::ATTRIBUTE_SCOPE:
      case c_base_markup_attributes::ATTRIBUTE_SHAPE:
      case c_base_markup_attributes::ATTRIBUTE_REL:
      case c_base_markup_attributes::ATTRIBUTE_RENDERING_INTENT:
      case c_base_markup_attributes::ATTRIBUTE_REPEAT_COUNT:
      case c_base_markup_attributes::ATTRIBUTE_ROLE:
      case c_base_markup_attributes::ATTRIBUTE_ROTATE:
      case c_base_markup_attributes::ATTRIBUTE_SIZE:
      case c_base_markup_attributes::ATTRIBUTE_SIZES:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_DOCUMENT:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_SET:
      case c_base_markup_attributes::ATTRIBUTE_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_SPREAD_METHOD:
      case c_base_markup_attributes::ATTRIBUTE_STOP_COLOR:
      case c_base_markup_attributes::ATTRIBUTE_STOP_OPACITY:
      case c_base_markup_attributes::ATTRIBUTE_STYLE:
      case c_base_markup_attributes::ATTRIBUTE_TARGET:
      case c_base_markup_attributes::ATTRIBUTE_TEXT_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_TEXT_CONTENT_ELEMENTS:
      case c_base_markup_attributes::ATTRIBUTE_TITLE:
      case c_base_markup_attributes::ATTRIBUTE_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_TRANSLATE:
      case c_base_markup_attributes::ATTRIBUTE_TO:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_BUTTON:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_LIST:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_SVG:
      case c_base_markup_attributes::ATTRIBUTE_USE_MAP:
      case c_base_markup_attributes::ATTRIBUTE_VALUE:
      case c_base_markup_attributes::ATTRIBUTE_VIEW_BOX:
      case c_base_markup_attributes::ATTRIBUTE_WIDTH:
      case c_base_markup_attributes::ATTRIBUTE_WRAP:
      case c_base_markup_attributes::ATTRIBUTE_XML:
      case c_base_markup_attributes::ATTRIBUTE_XMLNS:
      case c_base_markup_attributes::ATTRIBUTE_XMLNS_XLINK:
      case c_base_markup_attributes::ATTRIBUTE_XML_SPACE:
      case c_base_markup_attributes::ATTRIBUTE_ZOOM_AND_PAN:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_XLINK_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_XLINK_ACTUATE:
      case c_base_markup_attributes::ATTRIBUTE_XLINK_HREF:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ARIA_ATOMIC:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_AUTOCOMPLETE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_ACTIVE_DESCENDANT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_BUSY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_CHECKED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_CONTROLS:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DESCRIBED_BY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DISABLED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DROP_EFFECT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_EXPANDED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_FLOW_TO:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_GRABBED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_HAS_POPUP:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_HIDDEN:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_INVALID:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LABELLED_BY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LEVEL:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LIVE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_LINE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_SELECTABLE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_ORIENTATION:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_OWNS:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_POSITION_INSET:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_PRESSED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_READONLY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_RELEVANT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_REQUIRED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SELECTED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SET_SIZE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SORT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MAXIMUM:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MINIMIM:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_NOW:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_TEXT:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ASYNCHRONOUS:
      case c_base_markup_attributes::ATTRIBUTE_ATTRIBUTE_NAME:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_COMPLETE:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_CHALLENGE:
      case c_base_markup_attributes::ATTRIBUTE_CONTROLS:
      case c_base_markup_attributes::ATTRIBUTE_CHECKED:
      case c_base_markup_attributes::ATTRIBUTE_DEFAULT:
      case c_base_markup_attributes::ATTRIBUTE_DEFER:
      case c_base_markup_attributes::ATTRIBUTE_DISABLED:
      case c_base_markup_attributes::ATTRIBUTE_FORM_NO_VALIDATE:
      case c_base_markup_attributes::ATTRIBUTE_HIDDEN:
      case c_base_markup_attributes::ATTRIBUTE_LOOP:
      case c_base_markup_attributes::ATTRIBUTE_MULTIPLE:
      case c_base_markup_attributes::ATTRIBUTE_MUTED:
      case c_base_markup_attributes::ATTRIBUTE_NO_VALIDATE:
      case c_base_markup_attributes::ATTRIBUTE_READONLY:
      case c_base_markup_attributes::ATTRIBUTE_REQUIRED:
      case c_base_markup_attributes::ATTRIBUTE_REVERSED:
      case c_base_markup_attributes::ATTRIBUTE_SCOPED:
      case c_base_markup_attributes::ATTRIBUTE_SELECTED:
      case c_base_markup_attributes::ATTRIBUTE_SORTABLE:
      case c_base_markup_attributes::ATTRIBUTE_SORTED:
      case c_base_markup_attributes::ATTRIBUTE_SPELLCHECK:
        if (!is_bool($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ACCEPT:
      case c_base_markup_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_ENCODING_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_TYPE:
        if (!$this->pr_validate_value_mime_type($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET:
      case c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET:
        if (!$this->pr_validate_value_character_set($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_DIRECTION:
        if (!is_null($value) && !is_int($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_CENTER_X:
      case c_base_markup_attributes::ATTRIBUTE_CENTER_Y:
      case c_base_markup_attributes::ATTRIBUTE_COLUMNS:
      case c_base_markup_attributes::ATTRIBUTE_COLUMN_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_D_X:
      case c_base_markup_attributes::ATTRIBUTE_D_Y:
      case c_base_markup_attributes::ATTRIBUTE_FOCUS_X:
      case c_base_markup_attributes::ATTRIBUTE_FOCUS_Y:
      case c_base_markup_attributes::ATTRIBUTE_HIGH:
      case c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_LOW:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_HEIGHT:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_WIDTH:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_MINIMUM_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_OPTIMUM:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS_X:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS_Y:
      case c_base_markup_attributes::ATTRIBUTE_REFERENCE_X:
      case c_base_markup_attributes::ATTRIBUTE_REFERENCE_Y:
      case c_base_markup_attributes::ATTRIBUTE_ROWS:
      case c_base_markup_attributes::ATTRIBUTE_ROW_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_START:
      case c_base_markup_attributes::ATTRIBUTE_STEP:
      case c_base_markup_attributes::ATTRIBUTE_TAB_INDEX:
      case c_base_markup_attributes::ATTRIBUTE_VALUE_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_X:
      case c_base_markup_attributes::ATTRIBUTE_X_1:
      case c_base_markup_attributes::ATTRIBUTE_X_2:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_ACTUATE:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_HREF:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_Y:
      case c_base_markup_attributes::ATTRIBUTE_Y_1:
      case c_base_markup_attributes::ATTRIBUTE_Y_2:
        if (!is_int($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_CLASS:
        if (!is_array($value)) {
          if (is_string($value)) {
            if ($body) {
              if (!isset($this->attributes_body[$attribute])) {
                $this->attributes_body[$attribute] = array();
              }

              $this->attributes_body[$attribute][] = $value;
            }
            else {
              if (!isset($this->attributes[$attribute])) {
                $this->attributes[$attribute] = array();
              }

              $this->attributes[$attribute][] = $value;
            }

            return new c_base_return_true();
          }

          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_FORM_METHOD:
        if (!$this->pr_validate_value_http_method($value)) {
          return new c_base_return_false();
        }
        break;

      default:
        return new c_base_return_false();
    }

    if ($body) {
      $this->attributes_body[$attribute] = $value;
    }
    else {
      $this->attributes[$attribute] = $value;
    }

    return new c_base_return_true();
  }

  /**
   * Get the value of a single attribute assigned to this object.
   *
   * @param int $attribute
   *   The attribute to assign.
   * @param bool $body
   *   (optional) When TRUE, the body attributes are returned.
   *   When FALSE, the normal attributes are returned.
   *
   * @return c_base_return_int|c_base_return_string|c_base_return_bool|c_base_return_status
   *   The value assigned to the attribute (the data type is different per attribute).
   *   FALSE is returned if the element does not exist.
   *   FALSE with error bit set is returned on error.
   */
  private function p_get_attribute($attribute, $body = FALSE) {
    if ($body) {
      if (!isset($this->attributes_body) && !is_array($this->attributes_body)) {
        $this->attributes_body = array();
      }
    }
    else {
      if (!isset($this->attributes) && !is_array($this->attributes)) {
        $this->attributes = array();
      }
    }

    if ($body) {
      if (!array_key_exists($attribute, $this->attributes_body)) {
        return new c_base_return_false();
      }
    }
    else {
      if (!array_key_exists($attribute, $this->attributes)) {
        return new c_base_return_false();
      }
    }

    switch ($attribute) {
      case c_base_markup_attributes::ATTRIBUTE_NONE:
        return new c_base_return_null();

      case c_base_markup_attributes::ATTRIBUTE_ABBR:
      case c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY:
      case c_base_markup_attributes::ATTRIBUTE_ACTION:
      case c_base_markup_attributes::ATTRIBUTE_ALTERNATE:
      case c_base_markup_attributes::ATTRIBUTE_BY:
      case c_base_markup_attributes::ATTRIBUTE_CALCULATE_MODE:
      case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH:
      case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_CITE:
      case c_base_markup_attributes::ATTRIBUTE_COLOR:
      case c_base_markup_attributes::ATTRIBUTE_COORDINATES:
      case c_base_markup_attributes::ATTRIBUTE_CONTENT:
      case c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE:
      case c_base_markup_attributes::ATTRIBUTE_CROSS_ORIGIN:
      case c_base_markup_attributes::ATTRIBUTE_D:
      case c_base_markup_attributes::ATTRIBUTE_DATA:
      case c_base_markup_attributes::ATTRIBUTE_DATE_TIME:
      case c_base_markup_attributes::ATTRIBUTE_DIRECTION_NAME:
      case c_base_markup_attributes::ATTRIBUTE_DOWNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_DURATION:
      case c_base_markup_attributes::ATTRIBUTE_FILL:
      case c_base_markup_attributes::ATTRIBUTE_FILL_RULE:
      case c_base_markup_attributes::ATTRIBUTE_FILL_STROKE:
      case c_base_markup_attributes::ATTRIBUTE_FONT_SPECIFICATION:
      case c_base_markup_attributes::ATTRIBUTE_FOR:
      case c_base_markup_attributes::ATTRIBUTE_FORM:
      case c_base_markup_attributes::ATTRIBUTE_FORM_ACTION:
      case c_base_markup_attributes::ATTRIBUTE_FORM_TARGET:
      case c_base_markup_attributes::ATTRIBUTE_FORMAT:
      case c_base_markup_attributes::ATTRIBUTE_FROM:
      case c_base_markup_attributes::ATTRIBUTE_GLYPH_REFERENCE:
      case c_base_markup_attributes::ATTRIBUTE_GRADIANT_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_GRADIANT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_GRAPHICS:
      case c_base_markup_attributes::ATTRIBUTE_HEADERS:
      case c_base_markup_attributes::ATTRIBUTE_HEIGHT:
      case c_base_markup_attributes::ATTRIBUTE_HREF:
      case c_base_markup_attributes::ATTRIBUTE_HREF_NO:
      case c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV:
      case c_base_markup_attributes::ATTRIBUTE_ICON:
      case c_base_markup_attributes::ATTRIBUTE_ID:
      case c_base_markup_attributes::ATTRIBUTE_IN:
      case c_base_markup_attributes::ATTRIBUTE_IN_2:
      case c_base_markup_attributes::ATTRIBUTE_IS_MAP:
      case c_base_markup_attributes::ATTRIBUTE_KEY_POINTS:
      case c_base_markup_attributes::ATTRIBUTE_KEY_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_KIND:
      case c_base_markup_attributes::ATTRIBUTE_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_LENGTH_ADJUST:
      case c_base_markup_attributes::ATTRIBUTE_LIST:
      case c_base_markup_attributes::ATTRIBUTE_LOCAL:
      case c_base_markup_attributes::ATTRIBUTE_LONG_DESCRIPTION:
      case c_base_markup_attributes::ATTRIBUTE_MARKERS:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MASK_CONTENT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MASK_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM:
      case c_base_markup_attributes::ATTRIBUTE_MEDIA:
      case c_base_markup_attributes::ATTRIBUTE_METHOD:
      case c_base_markup_attributes::ATTRIBUTE_MODE:
      case c_base_markup_attributes::ATTRIBUTE_MINIMUM:
      case c_base_markup_attributes::ATTRIBUTE_NAME:
      case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_BLUR:
      case c_base_markup_attributes::ATTRIBUTE_ON_CANCEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_CLICK:
      case c_base_markup_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
      case c_base_markup_attributes::ATTRIBUTE_ON_COPY:
      case c_base_markup_attributes::ATTRIBUTE_ON_CUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case c_base_markup_attributes::ATTRIBUTE_ON_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_CUE_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_ENTER:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_OVER:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_DROP:
      case c_base_markup_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_EMPTIED:
      case c_base_markup_attributes::ATTRIBUTE_ON_ENDED:
      case c_base_markup_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_IN:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_OUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_HASH_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_INPUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_INSTALLED:
      case c_base_markup_attributes::ATTRIBUTE_ON_INVALID:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_DOWN:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_PRESS:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_UP:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_DATA:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOAD_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OVER:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_UP:
      case c_base_markup_attributes::ATTRIBUTE_ON_MESSAGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_OPEN:
      case c_base_markup_attributes::ATTRIBUTE_ON_ONLINE:
      case c_base_markup_attributes::ATTRIBUTE_ON_OFFLINE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_HIDE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PASTE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAUSE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_ON_PLAYING:
      case c_base_markup_attributes::ATTRIBUTE_ON_PROGRESS:
      case c_base_markup_attributes::ATTRIBUTE_ON_POP_STATE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RATED_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RESIZE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RESET:
      case c_base_markup_attributes::ATTRIBUTE_ON_RATE_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_SCROLL:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEARCH:
      case c_base_markup_attributes::ATTRIBUTE_ON_SELECT:
      case c_base_markup_attributes::ATTRIBUTE_ON_SUBMIT:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEEKED:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEEKING:
      case c_base_markup_attributes::ATTRIBUTE_ON_STALLED:
      case c_base_markup_attributes::ATTRIBUTE_ON_SUSPEND:
      case c_base_markup_attributes::ATTRIBUTE_ON_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_ON_STORAGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TIME_UPDATE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TRANSITION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOGGLE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_WAITING:
      case c_base_markup_attributes::ATTRIBUTE_ON_WHEEL:
      case c_base_markup_attributes::ATTRIBUTE_OFFSET:
      case c_base_markup_attributes::ATTRIBUTE_OPEN:
      case c_base_markup_attributes::ATTRIBUTE_ORIENTATION:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_CONTENT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_PATH:
      case c_base_markup_attributes::ATTRIBUTE_PATH_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_PLACE_HOLDER:
      case c_base_markup_attributes::ATTRIBUTE_POINTS:
      case c_base_markup_attributes::ATTRIBUTE_POSTER:
      case c_base_markup_attributes::ATTRIBUTE_PRELOAD:
      case c_base_markup_attributes::ATTRIBUTE_PRESERVE_ASPECT_RATIO:
      case c_base_markup_attributes::ATTRIBUTE_RADIO_GROUP:
      case c_base_markup_attributes::ATTRIBUTE_SANDBOX:
      case c_base_markup_attributes::ATTRIBUTE_SCOPE:
      case c_base_markup_attributes::ATTRIBUTE_SHAPE:
      case c_base_markup_attributes::ATTRIBUTE_REL:
      case c_base_markup_attributes::ATTRIBUTE_RENDERING_INTENT:
      case c_base_markup_attributes::ATTRIBUTE_REPEAT_COUNT:
      case c_base_markup_attributes::ATTRIBUTE_ROLE:
      case c_base_markup_attributes::ATTRIBUTE_ROTATE:
      case c_base_markup_attributes::ATTRIBUTE_SIZE:
      case c_base_markup_attributes::ATTRIBUTE_SIZES:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_DOCUMENT:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_SET:
      case c_base_markup_attributes::ATTRIBUTE_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_SPREAD_METHOD:
      case c_base_markup_attributes::ATTRIBUTE_STOP_COLOR:
      case c_base_markup_attributes::ATTRIBUTE_STOP_OPACITY:
      case c_base_markup_attributes::ATTRIBUTE_STYLE:
      case c_base_markup_attributes::ATTRIBUTE_TARGET:
      case c_base_markup_attributes::ATTRIBUTE_TEXT_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_TEXT_CONTENT_ELEMENTS:
      case c_base_markup_attributes::ATTRIBUTE_TITLE:
      case c_base_markup_attributes::ATTRIBUTE_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_TRANSLATE:
      case c_base_markup_attributes::ATTRIBUTE_TO:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_BUTTON:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_LIST:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_SVG:
      case c_base_markup_attributes::ATTRIBUTE_USE_MAP:
      case c_base_markup_attributes::ATTRIBUTE_VALUE:
      case c_base_markup_attributes::ATTRIBUTE_VIEW_BOX:
      case c_base_markup_attributes::ATTRIBUTE_WIDTH:
      case c_base_markup_attributes::ATTRIBUTE_WRAP:
      case c_base_markup_attributes::ATTRIBUTE_XML:
      case c_base_markup_attributes::ATTRIBUTE_XMLNS:
      case c_base_markup_attributes::ATTRIBUTE_XMLNS_XLINK:
      case c_base_markup_attributes::ATTRIBUTE_XML_SPACE:
      case c_base_markup_attributes::ATTRIBUTE_ZOOM_AND_PAN:
        if ($body) {
          return c_base_return_string::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_string::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_ARIA_ATOMIC:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_AUTOCOMPLETE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_ACTIVE_DESCENDANT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_BUSY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_CHECKED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_CONTROLS:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DESCRIBED_BY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DISABLED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DROP_EFFECT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_EXPANDED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_FLOW_TO:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_GRABBED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_HAS_POPUP:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_HIDDEN:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_INVALID:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LABELLED_BY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LEVEL:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LIVE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_LINE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_SELECTABLE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_ORIENTATION:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_OWNS:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_POSITION_INSET:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_PRESSED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_READONLY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_RELEVANT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_REQUIRED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SELECTED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SET_SIZE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SORT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MAXIMUM:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MINIMIM:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_NOW:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_TEXT:
        if ($body) {
          return c_base_return_string::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_string::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_XLINK_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_XLINK_ACTUATE:
      case c_base_markup_attributes::ATTRIBUTE_XLINK_HREF:
        if ($body) {
          return c_base_return_string::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_string::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_ASYNCHRONOUS:
      case c_base_markup_attributes::ATTRIBUTE_ATTRIBUTE_NAME:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_COMPLETE:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_CHALLENGE:
      case c_base_markup_attributes::ATTRIBUTE_CONTROLS:
      case c_base_markup_attributes::ATTRIBUTE_CHECKED:
      case c_base_markup_attributes::ATTRIBUTE_DEFAULT:
      case c_base_markup_attributes::ATTRIBUTE_DEFER:
      case c_base_markup_attributes::ATTRIBUTE_DISABLED:
      case c_base_markup_attributes::ATTRIBUTE_FORM_NO_VALIDATE:
      case c_base_markup_attributes::ATTRIBUTE_HIDDEN:
      case c_base_markup_attributes::ATTRIBUTE_LOOP:
      case c_base_markup_attributes::ATTRIBUTE_MULTIPLE:
      case c_base_markup_attributes::ATTRIBUTE_MUTED:
      case c_base_markup_attributes::ATTRIBUTE_NO_VALIDATE:
      case c_base_markup_attributes::ATTRIBUTE_READONLY:
      case c_base_markup_attributes::ATTRIBUTE_REQUIRED:
      case c_base_markup_attributes::ATTRIBUTE_REVERSED:
      case c_base_markup_attributes::ATTRIBUTE_SCOPED:
      case c_base_markup_attributes::ATTRIBUTE_SELECTED:
      case c_base_markup_attributes::ATTRIBUTE_SORTABLE:
      case c_base_markup_attributes::ATTRIBUTE_SORTED:
      case c_base_markup_attributes::ATTRIBUTE_SPELLCHECK:
        if ($body) {
          return c_base_return_bool::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_bool::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_ACCEPT:
      case c_base_markup_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_ENCODING_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_TYPE:
        if ($body) {
          return c_base_return_int::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_int::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET:
      case c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET:
        if ($body) {
          return c_base_return_int::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_int::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_DIRECTION:
        if ($body) {
          if (is_int($this->attributes_body[$attribute])) {
            return c_base_return_int::s_new($this->attributes_body[$attribute]);
          }
          elseif (is_null($this->attributes_body[$attribute])) {
            return new c_base_return_null();
          }
        }
        else {
          if (is_int($this->attributes[$attribute])) {
            return c_base_return_int::s_new($this->attributes[$attribute]);
          }
          elseif (is_null($this->attributes[$attribute])) {
            return new c_base_return_null();
          }
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_CENTER_X:
      case c_base_markup_attributes::ATTRIBUTE_CENTER_Y:
      case c_base_markup_attributes::ATTRIBUTE_COLUMNS:
      case c_base_markup_attributes::ATTRIBUTE_COLUMN_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_D_X:
      case c_base_markup_attributes::ATTRIBUTE_D_Y:
      case c_base_markup_attributes::ATTRIBUTE_FOCUS_X:
      case c_base_markup_attributes::ATTRIBUTE_FOCUS_Y:
      case c_base_markup_attributes::ATTRIBUTE_HIGH:
      case c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_LOW:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_HEIGHT:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_WIDTH:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_MINIMUM_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_OPTIMUM:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS_X:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS_Y:
      case c_base_markup_attributes::ATTRIBUTE_REFERENCE_X:
      case c_base_markup_attributes::ATTRIBUTE_REFERENCE_Y:
      case c_base_markup_attributes::ATTRIBUTE_ROWS:
      case c_base_markup_attributes::ATTRIBUTE_ROW_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_START:
      case c_base_markup_attributes::ATTRIBUTE_STEP:
      case c_base_markup_attributes::ATTRIBUTE_TAB_INDEX:
      case c_base_markup_attributes::ATTRIBUTE_VALUE_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_X:
      case c_base_markup_attributes::ATTRIBUTE_X_1:
      case c_base_markup_attributes::ATTRIBUTE_X_2:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_ACTUATE:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_HREF:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_Y:
      case c_base_markup_attributes::ATTRIBUTE_Y_1:
      case c_base_markup_attributes::ATTRIBUTE_Y_2:
        if ($body) {
          return c_base_return_int::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_int::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_FORM_METHOD:
        if ($body) {
          return c_base_return_int::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_int::s_new($this->attributes[$attribute]);
        }

      case c_base_markup_attributes::ATTRIBUTE_CLASS:
        if ($body) {
          return c_base_return_array::s_new($this->attributes_body[$attribute]);
        }
        else {
          return c_base_return_array::s_new($this->attributes[$attribute]);
        }

      default:
        return new c_base_return_false();
    }

    return new c_base_return_false();
  }
}
