<?php
/**
 * @file
 * Provides a class for managing HTML5 Markup.
 *
 * @see: https://www.w3.org/TR/html5/
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_markup.php');
require_once('common/base/classes/base_http.php');
require_once('common/base/classes/base_html.php');
require_once('common/base/classes/base_mime.php');

/**
 * An HTML theme page.
 *
 * This is intended to render the theme for an HTML page.
 *
 * @todo: add support for non-standard tag attributes, which will just be a string or NULL.
 *
 * There are amazing levels of inconsistency in HTML5, but for now this class implements HTML5 as closely as possible.
 * Future versions of this may violate the standard in favor of consistency and sanity.
 */
class c_theme_html extends c_base_return {
  const DEFAULT_MAX_RECURSION_DEPTH = 16384;

  private $html;
  private $markup;
  private $http;
  private $max_recursion_depth;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->html = NULL;
    $this->markup = NULL;
    $this->http = NULL;
    $this->max_recursion_depth = self::DEFAULT_MAX_RECURSION_DEPTH;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->html);
    unset($this->markup);
    unset($this->http);
    unset($this->max_recursion_depth);

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
   * Create a tag with commonly used properties.
   *
   * @param int $type
   *   A c_base_markup_tag type id.
   * @param string|null $id
   *   (optional) An id attribute to assign the tag.
   *   If null, this is variable ignored.
   * @param array|null $classes
   *   (optional) An array of strings representing additional classes to append.
   * @param string|null $text
   *   (optional) Text to assign the tag.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   */
  public static function s_create_tag($type, $id = NULL, $classes = NULL, $text = NULL) {
    if (!is_null($id) && !is_string($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($classes) && !is_array($classes)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'classes', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($text) && !is_string($text)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $tag = new c_base_markup_tag();

    $result = $tag->set_type($type);
    if ($result instanceof c_base_return_false) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_string($id)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_ID, $id);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, $id);
    }

    if (is_array($classes)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $classes);
    }

    self::s_p_populate_tag_class_names($tag, $type, $id);

    if (is_string($text)) {
      $tag->set_text($text);
    }

    return $tag;
  }

  /**
   * Create a tag with the supplied text.
   *
   * @param int $type
   *   A c_base_markup_tag type id.
   * @param string $text
   *   Text to assign the tag.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A newly created tag is returned on success.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: self::s_create_tag()
   */
  public static function s_create_tag_text($type, $text) {
    return self::s_create_tag($type, NULL, NULL, $text);
  }

  /**
   * Assign the markup html to be themed.
   *
   * @param c_base_html $html
   *   The markup html to apply the theme to.
   *   This object is cloned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_html($html) {
    if (!($html instanceof c_base_html)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'html', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->html = clone($html);
    return new c_base_return_true();
  }

  /**
   * Get the markup html assigned to this object.
   *
   * @return c_base_html|c_base_return_status
   *   The (cloned) markup html object.
   *   FALSE is returned if no id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_html() {
    if (!($this->html instanceof c_base_html)) {
      $this->html = new c_base_html();
    }

    return clone($this->html);
  }

  /**
   * Get any renderred markup.
   *
   * @return c_base_return_string|c_base_return_status
   *   The unique numeric id assigned to this object.
   *   FALSE is returned if no id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_markup() {
    if (!is_string($this->markup)) {
      $this->markup = '';
    }

    return c_base_return_string::s_new($this->markup);
  }

  /**
   * Process the markup tags and create HTML markup string.
   *
   * @return c_base_return_status
   *   TRUE is returned on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function render_markup() {
    if (!($this->html instanceof c_base_html)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{variable_name}' => 'this->html', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $this->markup = '<html' . $this->p_render_markup_attributes_global() . $this->p_render_markup_attributes_event_handler() . '>';
    $this->markup .= '<head>' . $this->p_render_markup_head_tags() . '</head>';
    $this->markup .= '<body' . $this->p_render_markup_attributes_body() .'>' . $this->p_render_markup_body_tags() . '</body>';
    $this->markup .= '</html>';

    return new c_base_return_true();
  }

  /**
   * Assign the HTTP information.
   *
   * @param c_base_http $http
   *   The markup tags to apply the theme to.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_http($http) {
    if (!($http instanceof c_base_http)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'http', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->http = clone($http);
    return new c_base_return_true();
  }

  /**
   * Get the HTTP information
   *
   * @return c_base_http|c_base_return_status
   *   The (cloned) markup tags object.
   *   FALSE is returned if no id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_http() {
    if (!($this->http instanceof c_base_http)) {
      $this->http = new c_base_http();
    }

    return clone($this->http);
  }

  /**
   * Assign the maximum recursion depth.
   *
   * @param int $max_recursion_depth
   *   How far to recurse when generating markup from body tags.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_max_recursion_depth($max_recursion_depth) {
    if (!is_int($max_recursion_depth)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'max_recursion_depth', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->max_recursion_depth = $max_recursion_depth;
    return new c_base_return_true();
  }

  /**
   * Get the maximum recursion depth integer assigned to this object.
   *
   * @return c_base_return_int|c_base_return_status
   *   The markup html object.
   *   FALSE is returned if no id is assigned.
   *   FALSE with error bit set is returned on error.
   */
  public function get_max_recursion_depth() {
    if (!is_int($this->max_recursion_depth)) {
      $this->max_recursion_depth = self::DEFAULT_MAX_RECURSION_DEPTH;
    }

    return c_base_return_int::s_new($this->max_recursion_depth);
  }

  /**
   * Generate a common list of class names for a given tag type.
   *
   * @param c_base_markup_tag &$tag
   *   The tag to operate on.
   * @param int $type
   *   A c_base_markup_tag type id.
   * @param string|null $id
   *   A unique ID field associated with the tag.
   *   If null, then this is ignored.
   */
  private static function s_p_populate_tag_class_names(&$tag, $type, $id) {
    $class_1 = NULL;
    $class_2 = NULL;
    $class_3 = NULL;

    if ($type === c_base_markup_tag::TYPE_A) {
      $class_1 = 'text';
      $class_2 = 'text-link';
    }
    elseif ($type === c_base_markup_tag::TYPE_ABBR) {
      $class_1 = 'text';
      $class_2 = 'text-abbreviation';
    }
    elseif ($type === c_base_markup_tag::TYPE_ADDRESS) {
      $class_1 = 'structure';
      $class_2 = 'structure-address';
    }
    elseif ($type === c_base_markup_tag::TYPE_AREA) {
      $class_1 = 'datum';
      $class_2 = 'datum-area';
    }
    elseif ($type === c_base_markup_tag::TYPE_ARTICLE) {
      $class_1 = 'structure';
      $class_2 = 'structure-article';
    }
    elseif ($type === c_base_markup_tag::TYPE_ASIDE) {
      $class_1 = 'structure';
      $class_2 = 'structure-aside';
    }
    elseif ($type === c_base_markup_tag::TYPE_AUDIO) {
      $class_1 = 'media';
      $class_2 = 'media-audio';
    }
    elseif ($type === c_base_markup_tag::TYPE_BOLD) {
      $class_1 = 'format';
      $class_2 = 'format-strong';
    }
    elseif ($type === c_base_markup_tag::TYPE_BDI) {
      $class_1 = 'text';
      $class_2 = 'text-bdi';
      $class_3 = 'text-direction';
    }
    elseif ($type === c_base_markup_tag::TYPE_BDO) {
      $class_1 = 'text';
      $class_2 = 'text-bdo';
      $class_3 = 'text-direction';
    }
    elseif ($type === c_base_markup_tag::TYPE_BLOCKQUOTE) {
      $class_1 = 'text';
      $class_2 = 'text-blockquote';
      $class_3 = 'text-quote';
    }
    elseif ($type === c_base_markup_tag::TYPE_BREAK) {
      $class_1 = 'structure';
      $class_2 = 'structure-break';
    }
    elseif ($type === c_base_markup_tag::TYPE_BUTTON) {
      $class_1 = 'field';
      $class_2 = 'field-button';
    }
    elseif ($type === c_base_markup_tag::TYPE_CANVAS) {
      $class_1 = 'structure';
      $class_2 = 'structure-canvas';
    }
    elseif ($type === c_base_markup_tag::TYPE_CHECKBOX) {
      $class_1 = 'field';
      $class_2 = 'field-checkbox';
    }
    elseif ($type === c_base_markup_tag::TYPE_CITE) {
      $class_1 = 'text';
      $class_2 = 'text-cite';
    }
    elseif ($type === c_base_markup_tag::TYPE_CODE) {
      $class_1 = 'text';
      $class_2 = 'text-code';
    }
    elseif ($type === c_base_markup_tag::TYPE_COL) {
      $class_1 = 'structure';
      $class_2 = 'structure-column';
    }
    elseif ($type === c_base_markup_tag::TYPE_COLOR) {
      $class_1 = 'field';
      $class_2 = 'field-color';
    }
    elseif ($type === c_base_markup_tag::TYPE_COL_GROUP) {
      $class_1 = 'structure';
      $class_2 = 'structure-column_group';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATA) {
      $class_1 = 'datum';
      $class_2 = 'datum-data';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATA) {
      $class_1 = 'datum';
      $class_2 = 'datum-data_list';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATE) {
      $class_1 = 'field';
      $class_2 = 'field-date';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATE_TIME_LOCAL) {
      $class_1 = 'field';
      $class_2 = 'field-date_local_time';
    }
    elseif ($type === c_base_markup_tag::TYPE_TERM_DESCRIPTION) {
      $class_1 = 'text';
      $class_2 = 'text-term_description';
    }
    elseif ($type === c_base_markup_tag::TYPE_DEL) {
      $class_1 = 'format';
      $class_2 = 'format-delete';
    }
    elseif ($type === c_base_markup_tag::TYPE_DETAILS) {
      $class_1 = 'structure';
      $class_2 = 'structure-details';
    }
    elseif ($type === c_base_markup_tag::TYPE_DFN) {
      $class_1 = 'text';
      $class_2 = 'text-definition';
    }
    elseif ($type === c_base_markup_tag::TYPE_DIALOG) {
      $class_1 = 'structure';
      $class_2 = 'structure-dialog';
    }
    elseif ($type === c_base_markup_tag::TYPE_DIVIDER) {
      $class_1 = 'structure';
      $class_2 = 'structure-divider';
    }
    elseif ($type === c_base_markup_tag::TYPE_DIVIDER) {
      $class_1 = 'structure';
      $class_2 = 'structure-divider';
    }
    elseif ($type === c_base_markup_tag::TYPE_DEFINITION_LIST) {
      $class_1 = 'structure';
      $class_2 = 'structure-definition_list';
    }
    elseif ($type === c_base_markup_tag::TYPE_TERM_NAME) {
      $class_1 = 'text';
      $class_2 = 'text-term_name';
    }
    elseif ($type === c_base_markup_tag::TYPE_EM) {
      $class_1 = 'format';
      $class_2 = 'format-emphasis';
    }
    elseif ($type === c_base_markup_tag::TYPE_EMAIL) {
      $class_1 = 'field';
      $class_2 = 'field-email';
    }
    elseif ($type === c_base_markup_tag::TYPE_EMBED) {
      $class_1 = 'media';
      $class_2 = 'media-embed';
    }
    elseif ($type === c_base_markup_tag::TYPE_FIELD_SET) {
      $class_1 = 'structure';
      $class_2 = 'structure-field_set';
    }
    elseif ($type === c_base_markup_tag::TYPE_FIGURE) {
      $class_1 = 'structure';
      $class_2 = 'structure-figure';
    }
    elseif ($type === c_base_markup_tag::TYPE_FIGURE_CAPTION) {
      $class_1 = 'text';
      $class_2 = 'text-figure_caption';
      $class_3 = 'text-caption';
    }
    elseif ($type === c_base_markup_tag::TYPE_FILE) {
      $class_1 = 'field';
      $class_2 = 'field-file';
    }
    elseif ($type === c_base_markup_tag::TYPE_FOOTER) {
      $class_1 = 'structure';
      $class_2 = 'structure-footer';
    }
    elseif ($type === c_base_markup_tag::TYPE_FORM) {
      $class_1 = 'structure';
      $class_2 = 'structure-form';
    }
    elseif ($type === c_base_markup_tag::TYPE_H1) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
      $class_3 = 'text-heading_1';
    }
    elseif ($type === c_base_markup_tag::TYPE_H2) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
      $class_3 = 'text-heading_2';
    }
    elseif ($type === c_base_markup_tag::TYPE_H3) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
      $class_3 = 'text-heading_3';
    }
    elseif ($type === c_base_markup_tag::TYPE_H4) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
      $class_3 = 'text-heading_4';
    }
    elseif ($type === c_base_markup_tag::TYPE_H5) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
      $class_3 = 'text-heading_5';
    }
    elseif ($type === c_base_markup_tag::TYPE_H6) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
      $class_3 = 'text-heading_6';
    }
    elseif ($type === c_base_markup_tag::TYPE_HX) {
      $class_1 = 'text';
      $class_2 = 'text-heading';
    }
    elseif ($type === c_base_markup_tag::TYPE_HEADER) {
      $class_1 = 'structure';
      $class_2 = 'structure-header';
    }
    elseif ($type === c_base_markup_tag::TYPE_HIDDEN) {
      $class_1 = 'field';
      $class_2 = 'field-hidden';
    }
    elseif ($type === c_base_markup_tag::TYPE_HORIZONTAL_RULER) {
      $class_1 = 'structure';
      $class_2 = 'structure-horizontal_ruler';
    }
    elseif ($type === c_base_markup_tag::TYPE_ITALICS) {
      $class_1 = 'format';
      $class_2 = 'format-emphasis';
    }
    elseif ($type === c_base_markup_tag::TYPE_HORIZONTAL_RULER) {
      $class_1 = 'structure';
      $class_2 = 'structure-horizontal_ruler';
    }
    elseif ($type === c_base_markup_tag::TYPE_INLINE_FRAME) {
      $class_1 = 'structure';
      $class_2 = 'structure-inline_frame';
    }
    elseif ($type === c_base_markup_tag::TYPE_IMAGE) {
      $class_1 = 'media';
      $class_2 = 'media-image';
    }
    elseif ($type === c_base_markup_tag::TYPE_INPUT) {
      $class_1 = 'field';
      $class_2 = 'field-input';
    }
    elseif ($type === c_base_markup_tag::TYPE_INS) {
      $class_1 = 'text';
      $class_2 = 'text-insert';
    }
    elseif ($type === c_base_markup_tag::TYPE_KEYBOARD) {
      $class_1 = 'text';
      $class_2 = 'text-keyboard';
    }
    elseif ($type === c_base_markup_tag::TYPE_KEY_GEN) {
      $class_1 = 'field';
      $class_2 = 'field-key_generator';
    }
    elseif ($type === c_base_markup_tag::TYPE_LABEL) {
      $class_1 = 'field';
      $class_2 = 'field-label';
    }
    elseif ($type === c_base_markup_tag::TYPE_LEGEND) {
      $class_1 = 'text';
      $class_2 = 'text-legend';
    }
    elseif ($type === c_base_markup_tag::TYPE_LIST_ITEM) {
      $class_1 = 'text';
      $class_2 = 'text-list_item';
    }
    elseif ($type === c_base_markup_tag::TYPE_MAIN) {
      $class_1 = 'structure';
      $class_2 = 'structure-main';
    }
    elseif ($type === c_base_markup_tag::TYPE_MAP) {
      $class_1 = 'structure';
      $class_2 = 'structure-image_map';
    }
    elseif ($type === c_base_markup_tag::TYPE_MARK) {
      $class_1 = 'format';
      $class_2 = 'format-mark';
    }
    elseif ($type === c_base_markup_tag::TYPE_MENU) {
      $class_1 = 'structure';
      $class_2 = 'structure-menu';
    }
    elseif ($type === c_base_markup_tag::TYPE_MENU_ITEM) {
      $class_1 = 'structure';
      $class_2 = 'structure-menu_item';
    }
    elseif ($type === c_base_markup_tag::TYPE_METER) {
      $class_1 = 'field';
      $class_2 = 'field-meter';
    }
    elseif ($type === c_base_markup_tag::TYPE_MONTH) {
      $class_1 = 'field';
      $class_2 = 'field-month';
    }
    elseif ($type === c_base_markup_tag::TYPE_NAVIGATION) {
      $class_1 = 'structure';
      $class_2 = 'structure-navigation';
    }
    elseif ($type === c_base_markup_tag::TYPE_NO_SCRIPT) {
      $class_1 = 'structure';
      $class_2 = 'structure-no_script';
    }
    elseif ($type === c_base_markup_tag::TYPE_NUMBER) {
      $class_1 = 'field';
      $class_2 = 'field-number';
    }
    elseif ($type === c_base_markup_tag::TYPE_OBJECT) {
      $class_1 = 'media';
      $class_2 = 'media-object';
    }
    elseif ($type === c_base_markup_tag::TYPE_ORDERED_LIST) {
      $class_1 = 'structure';
      $class_2 = 'structure-ordered_list';
    }
    elseif ($type === c_base_markup_tag::TYPE_OPTIONS_GROUP) {
      $class_1 = 'structure';
      $class_2 = 'structure-options_group';
    }
    elseif ($type === c_base_markup_tag::TYPE_OPTION) {
      $class_1 = 'datum';
      $class_2 = 'datum-option';
    }
    elseif ($type === c_base_markup_tag::TYPE_OUTPUT) {
      $class_1 = 'datum';
      $class_2 = 'datum-output';
    }
    elseif ($type === c_base_markup_tag::TYPE_PARAGRAPH) {
      $class_1 = 'text';
      $class_2 = 'text-paragraph';
    }
    elseif ($type === c_base_markup_tag::TYPE_PARAM) {
      $class_1 = 'datum';
      $class_2 = 'datum-parameter';
    }
    elseif ($type === c_base_markup_tag::TYPE_PASSWORD) {
      $class_1 = 'field';
      $class_2 = 'field-password';
    }
    elseif ($type === c_base_markup_tag::TYPE_PICTURE) {
      $class_1 = 'structure';
      $class_2 = 'structure-picture';
    }
    elseif ($type === c_base_markup_tag::TYPE_PREFORMATTED) {
      $class_1 = 'format';
      $class_2 = 'format-preformatted';
    }
    elseif ($type === c_base_markup_tag::TYPE_PROGRESS) {
      // note: 'media' is the closest thing I can think of for this tag (as in, it is like an image).
      $class_1 = 'media';
      $class_2 = 'media-progress';
    }
    elseif ($type === c_base_markup_tag::TYPE_PREFORMATTED) {
      $class_1 = 'format';
      $class_2 = 'format-preformatted';
    }
    elseif ($type === c_base_markup_tag::TYPE_Q) {
      $class_1 = 'text';
      $class_2 = 'text-quote';
    }
    elseif ($type === c_base_markup_tag::TYPE_RADIO) {
      $class_1 = 'field';
      $class_2 = 'field-radio';
    }
    elseif ($type === c_base_markup_tag::TYPE_RANGE) {
      $class_1 = 'field';
      $class_2 = 'field-range';
    }
    elseif ($type === c_base_markup_tag::TYPE_RESET) {
      $class_1 = 'field';
      $class_2 = 'field-reset';
    }
    elseif ($type === c_base_markup_tag::TYPE_RUBY_PARENTHESIS) {
      $class_1 = 'format';
      $class_2 = 'format-ruby_parenthesis';
    }
    elseif ($type === c_base_markup_tag::TYPE_RUBY_PRONUNCIATION) {
      $class_1 = 'format';
      $class_2 = 'format-ruby_pronunciation';
    }
    elseif ($type === c_base_markup_tag::TYPE_RUBY) {
      $class_1 = 'format';
      $class_2 = 'format-ruby';
    }
    elseif ($type === c_base_markup_tag::TYPE_STRIKE_THROUGH) {
      $class_1 = 'format';
      $class_2 = 'format-strike_through';
    }
    elseif ($type === c_base_markup_tag::TYPE_SAMPLE) {
      $class_1 = 'text';
      $class_2 = 'text-sample';
    }
    elseif ($type === c_base_markup_tag::TYPE_SEARCH) {
      $class_1 = 'field';
      $class_2 = 'field-search';
    }
    elseif ($type === c_base_markup_tag::TYPE_SECTION) {
      $class_1 = 'structure';
      $class_2 = 'structure-section';
    }
    elseif ($type === c_base_markup_tag::TYPE_SELECT) {
      $class_1 = 'field';
      $class_2 = 'field-select';
    }
    elseif ($type === c_base_markup_tag::TYPE_SMALL) {
      $class_1 = 'format';
      $class_2 = 'format-small';
    }
    elseif ($type === c_base_markup_tag::TYPE_SOURCE) {
      $class_1 = 'datum';
      $class_2 = 'datum-source';
    }
    elseif ($type === c_base_markup_tag::TYPE_SPAN) {
      $class_1 = 'structure';
      $class_2 = 'structure-span';
    }
    elseif ($type === c_base_markup_tag::TYPE_STRONG) {
      $class_1 = 'format';
      $class_2 = 'format-strong';
    }
    elseif ($type === c_base_markup_tag::TYPE_SUB_SCRIPT) {
      $class_1 = 'format';
      $class_2 = 'format-sub_script';
    }
    elseif ($type === c_base_markup_tag::TYPE_SUBMIT) {
      $class_1 = 'field';
      $class_2 = 'field-submit';
    }
    elseif ($type === c_base_markup_tag::TYPE_SUPER_SCRIPT) {
      $class_1 = 'format';
      $class_2 = 'format-super_script';
    }
    elseif ($type === c_base_markup_tag::TYPE_SVG) {
      $class_1 = 'media';
      $class_2 = 'media-svg';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE) {
      $class_1 = 'structure';
      $class_2 = 'structure-table';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_BODY) {
      $class_1 = 'structure';
      $class_2 = 'structure-table_body';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_CELL) {
      $class_1 = 'structure';
      $class_2 = 'structure-table_cell';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_FOOTER) {
      $class_1 = 'structure';
      $class_2 = 'structure-table_footer';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_HEADER) {
      $class_1 = 'structure';
      $class_2 = 'structure-table_header';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_HEADER_CELL) {
      $class_1 = 'structure';
      $class_2 = 'structure-table_header_cell';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_ROW) {
      $class_1 = 'structure';
      $class_2 = 'structure-table_row';
    }
    elseif ($type === c_base_markup_tag::TYPE_TELEPHONE) {
      $class_1 = 'field';
      $class_2 = 'field-telephone';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEMPLATE) {
      $class_1 = 'datum';
      $class_2 = 'datum-template';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT) {
      $class_1 = 'field';
      $class_2 = 'field-text';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT_AREA) {
      $class_1 = 'field';
      $class_2 = 'field-text_area';
    }
    elseif ($type === c_base_markup_tag::TYPE_TIME) {
      $class_1 = 'field';
      $class_2 = 'field-time';
    }
    elseif ($type === c_base_markup_tag::TYPE_TRACK) {
      $class_1 = 'datum';
      $class_2 = 'datum-track';
    }
    elseif ($type === c_base_markup_tag::TYPE_UNDERLINE) {
      $class_1 = 'format';
      $class_2 = 'format-underline';
    }
    elseif ($type === c_base_markup_tag::TYPE_UNORDERED_LIST) {
      $class_1 = 'structure';
      $class_2 = 'structure-unordered_list';
    }
    elseif ($type === c_base_markup_tag::TYPE_URL) {
      $class_1 = 'text';
      $class_2 = 'text-url';
    }
    elseif ($type === c_base_markup_tag::TYPE_VARIABLE) {
      $class_1 = 'text';
      $class_2 = 'text-variable';
    }
    elseif ($type === c_base_markup_tag::TYPE_VIDEO) {
      $class_1 = 'media';
      $class_2 = 'media-video';
    }
    elseif ($type === c_base_markup_tag::TYPE_WEEK) {
      $class_1 = 'field';
      $class_2 = 'field-week';
    }
    elseif ($type === c_base_markup_tag::TYPE_WIDE_BREAK) {
      $class_1 = 'structure';
      $class_2 = 'structure-wide_break';
    }

    if (!is_null($class_1)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $class_1);

      if (!is_null($id)) {
        $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $id . '-' . $class_1);
      }
    }
    unset($class_1);

    if (!is_null($class_2)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $class_2);

      if (!is_null($id)) {
        $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $id . '-' . $class_2);
      }
    }
    unset($class_2);

    if (!is_null($class_3)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $class_3);

      if (!is_null($id)) {
        $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $id . '-' . $class_3);
      }
    }
    unset($class_3);
  }

  /**
   * Generates the HTML tag global attributes.
   *
   * These are used on all HTML tags.
   *
   * @param c_base_markup_tag|null $tag
   *   (optional) When not NULL, represents the tag to get the attributes of.
   *   When NULL, the html attributes are used.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_global($tag = NULL) {
    $markup = '';


    // attribute: id
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_ID)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ID)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' id="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: lang
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE)->get_value_exact();
    }

    $ltr = TRUE;
    if (!empty($attribute)) {
      $language_array = c_base_defaults_global::s_get_languages()->s_get_aliases_by_id($attribute)->get_value_exact();

      // use the first language alias available.
      $language = array_pop($language_array);
      unset($language_array);

      if (!empty($language)) {
        $markup .= ' lang="' . $language . '"';
      }
      unset($language);
    }
    unset($attribute);


    // attribute: direction
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION)->get_value_exact();
    }

    if (is_int($attribute)) {
      $is_ltr = c_base_defaults_global::s_get_languages()->s_get_ltr_by_id($attribute)->get_value_exact();

      if ($is_ltr) {
        $markup .= ' dir="ltr"';
      }
      else {
        $markup .= ' dir="rtl"';
      }

      unset($is_ltr);
    }
    elseif (is_null($attribute)) {
      $markup .= ' dir="auto"';
    }
    unset($attribute);


    // attribute: title
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' title="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: access key
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' accesskey="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: contenteditable
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' contenteditable="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: hidden
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_HIDDEN)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HIDDEN)->get_value_exact();
    }

    if ($attribute) {
      $markup .= ' hidden';
    }
    unset($attribute);


    // attribute: role
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROLE)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROLE)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' role="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: spellcheck
    if (is_null($tag)) {
      $is_spellcheck = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_SPELLCHECK);
    }
    else {
      $is_spellcheck = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SPELLCHECK);
    }

    if (!($is_spellcheck instanceof c_base_return_false)) {
      $is_spellcheck = $is_spellcheck->get_value_exact();
      if ($is_spellcheck) {
        $markup .= ' spellcheck="true"';
      }
      else {
        $markup .= ' spellcheck="false"';
      }
    }


    // attribute: style
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_STYLE)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_STYLE)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' style="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: tabindex
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_TAB_INDEX)->get_value();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TAB_INDEX)->get_value();
    }

    if (is_int($attribute)) {
      $markup .= ' tabindex="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: translate
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_TRANSLATE)->get_value_exact();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TRANSLATE)->get_value_exact();
    }

    if (!empty($attribute)) {
      $markup .= ' translate="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: class
    if (is_null($tag)) {
      $attribute = $this->html->get_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS)->get_value();
    }
    else {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS)->get_value();
    }

    if (is_array($attribute) && !empty($attribute)) {
      $markup .= ' class="' . implode(' ', $attribute) . '"';
    }
    unset($attribute);


    // attribute: aria-tags
    $attributes = array(
      // global tags
      c_base_markup_attributes::ATTRIBUTE_ARIA_ATOMIC => 'aria-atomic',
      c_base_markup_attributes::ATTRIBUTE_ARIA_BUSY => 'aria-busy',
      c_base_markup_attributes::ATTRIBUTE_ARIA_CONTROLS => 'aria-controls',
      c_base_markup_attributes::ATTRIBUTE_ARIA_DESCRIBED_BY => 'aria-describedby',
      c_base_markup_attributes::ATTRIBUTE_ARIA_DISABLED => 'aria-disabled',
      c_base_markup_attributes::ATTRIBUTE_ARIA_DROP_EFFECT => 'aria-dropeffect',
      c_base_markup_attributes::ATTRIBUTE_ARIA_FLOW_TO => 'aria-flowto',
      c_base_markup_attributes::ATTRIBUTE_ARIA_GRABBED => 'aria-grabbed',
      c_base_markup_attributes::ATTRIBUTE_ARIA_HAS_POPUP => 'aria-haspopup',
      c_base_markup_attributes::ATTRIBUTE_ARIA_HIDDEN => 'aria-hiddem',
      c_base_markup_attributes::ATTRIBUTE_ARIA_INVALID => 'aria-invalid',
      c_base_markup_attributes::ATTRIBUTE_ARIA_LABEL => 'aria-label',
      c_base_markup_attributes::ATTRIBUTE_ARIA_LABELLED_BY => 'aria-labelledby',
      c_base_markup_attributes::ATTRIBUTE_ARIA_LIVE => 'aria-live',
      c_base_markup_attributes::ATTRIBUTE_ARIA_OWNS => 'aria-owns',
      c_base_markup_attributes::ATTRIBUTE_ARIA_RELEVANT => 'aria-relevant',

      // extra context-specifc tags
      c_base_markup_attributes::ATTRIBUTE_ARIA_AUTOCOMPLETE => 'aria-autocomplete',
      c_base_markup_attributes::ATTRIBUTE_ARIA_ACTIVE_DESCENDANT => 'aria-activedescendant',
      c_base_markup_attributes::ATTRIBUTE_ARIA_CHECKED => 'aria-checked',
      c_base_markup_attributes::ATTRIBUTE_ARIA_EXPANDED => 'aria-expanded',
      c_base_markup_attributes::ATTRIBUTE_ARIA_LEVEL => 'aria-level',
      c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_LINE => 'aria-muultiline',
      c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_SELECTABLE => 'aria-multiselectable',
      c_base_markup_attributes::ATTRIBUTE_ARIA_ORIENTATION => 'aria-orientation',
      c_base_markup_attributes::ATTRIBUTE_ARIA_POSITION_INSET => 'aria-positioninsert',
      c_base_markup_attributes::ATTRIBUTE_ARIA_PRESSED => 'aria-pressed',
      c_base_markup_attributes::ATTRIBUTE_ARIA_READONLY => 'aria-readonly',
      c_base_markup_attributes::ATTRIBUTE_ARIA_RELEVANT => 'aria-relevant',
      c_base_markup_attributes::ATTRIBUTE_ARIA_REQUIRED => 'aria-required',
      c_base_markup_attributes::ATTRIBUTE_ARIA_SELECTED => 'aria-selected',
      c_base_markup_attributes::ATTRIBUTE_ARIA_SET_SIZE => 'aria-setsize',
      c_base_markup_attributes::ATTRIBUTE_ARIA_SORT => 'aria-sort',
      c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MAXIMUM => 'aria-valuemax',
      c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MINIMIM => 'aria-valuemin',
      c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_NOW => 'aria-valuenow',
      c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_TEXT => 'aria-valuetext',
    );

    if (is_null($tag)) {
      foreach ($attributes as $attribute_id => $attribute_name) {
        $attribute = $this->html->get_attribute($attribute_id)->get_value_exact();
        if (!empty($attribute)) {
          $markup .= ' ' . $attribute_name . '="' . $attribute . '"';
        }
        unset($attribute);
      }
    }
    else {
      foreach ($attributes as $attribute_id => $attribute_name) {
        $attribute = $tag->get_attribute($attribute_id)->get_value_exact();
        if (!empty($attribute)) {
          $markup .= ' ' . $attribute_name . '="' . $attribute . '"';
        }
        unset($attribute);
      }
    }
    unset($attribute_id);
    unset($attribute_name);
    unset($attributes);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to event handlers.
   *
   * These may be specified on all HTML tags.
   *
   * @param c_base_markup_tag|null $tag
   *   (optional) When not NULL, represents the tag to get the attributes of.
   *   When NULL, the html attributes are used.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_event_handler($tag = NULL) {
    $markup = '';

    $attributes = array(
      c_base_markup_attributes::ATTRIBUTE_ON_ABORT => 'onabort',
      c_base_markup_attributes::ATTRIBUTE_ON_BLUR => 'onblur',
      c_base_markup_attributes::ATTRIBUTE_ON_CANCEL => 'oncancel',
      c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY => 'oncanplay',
      c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH => 'oncanplaythrough',
      c_base_markup_attributes::ATTRIBUTE_ON_CHANGE => 'onchange',
      c_base_markup_attributes::ATTRIBUTE_ON_CLICK => 'onclick',
      c_base_markup_attributes::ATTRIBUTE_ON_CUE_CHANGE => 'oncuechange',
      c_base_markup_attributes::ATTRIBUTE_ON_DOUBLE_CLICK => 'ondblclick',
      c_base_markup_attributes::ATTRIBUTE_ON_DURATION_CHANGE => 'ondurationchange',
      c_base_markup_attributes::ATTRIBUTE_ON_EMPTIED => 'onemptied',
      c_base_markup_attributes::ATTRIBUTE_ON_ENDED => 'onended',
      c_base_markup_attributes::ATTRIBUTE_ON_ERROR => 'onerror',
      c_base_markup_attributes::ATTRIBUTE_ON_FOCUS => 'onfocus',
      c_base_markup_attributes::ATTRIBUTE_ON_INPUT => 'oninput',
      c_base_markup_attributes::ATTRIBUTE_ON_INVALID => 'oninvalid',
      c_base_markup_attributes::ATTRIBUTE_ON_KEY_DOWN => 'onkeydown',
      c_base_markup_attributes::ATTRIBUTE_ON_KEY_PRESS => 'onkeypress',
      c_base_markup_attributes::ATTRIBUTE_ON_KEY_UP => 'onkeyup',
      c_base_markup_attributes::ATTRIBUTE_ON_LOAD => 'onload',
      c_base_markup_attributes::ATTRIBUTE_ON_LOADED_DATA => 'onloadeddata',
      c_base_markup_attributes::ATTRIBUTE_ON_LOADED_META_DATA => 'onloadedmetadata',
      c_base_markup_attributes::ATTRIBUTE_ON_LOAD_START => 'onloadstart',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_DOWN => 'onmousedown',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_ENTER => 'onmouseenter',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_LEAVE => 'onmouseleave',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_MOVE => 'onmousemove',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OUT => 'onmouseout',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OVER => 'onmouseover',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_UP => 'onmouseup',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_WHEEL => 'onmousewheel',
      c_base_markup_attributes::ATTRIBUTE_ON_PAUSE => 'onpause',
      c_base_markup_attributes::ATTRIBUTE_ON_PLAY => 'onplay',
      c_base_markup_attributes::ATTRIBUTE_ON_PLAYING => 'onplaying',
      c_base_markup_attributes::ATTRIBUTE_ON_PROGRESS => 'onprogress',
      c_base_markup_attributes::ATTRIBUTE_ON_RATED_CHANGE => 'onratechange',
      c_base_markup_attributes::ATTRIBUTE_ON_RESET => 'onreset',
      c_base_markup_attributes::ATTRIBUTE_ON_RESIZE => 'onresize',
      c_base_markup_attributes::ATTRIBUTE_ON_SCROLL => 'onscroll',
      c_base_markup_attributes::ATTRIBUTE_ON_SEEKED => 'onseeked',
      c_base_markup_attributes::ATTRIBUTE_ON_SEEKING => 'onseeking',
      c_base_markup_attributes::ATTRIBUTE_ON_SELECT => 'onselect',
      c_base_markup_attributes::ATTRIBUTE_ON_SHOW => 'onshow',
      c_base_markup_attributes::ATTRIBUTE_ON_INSTALLED => 'onstalled',
      c_base_markup_attributes::ATTRIBUTE_ON_SUBMIT => 'onsubmit',
      c_base_markup_attributes::ATTRIBUTE_ON_SUSPEND => 'onsuspend',
      c_base_markup_attributes::ATTRIBUTE_ON_TIME_UPDATE => 'ontimeupdate',
      c_base_markup_attributes::ATTRIBUTE_ON_TOGGLE => 'ontoggle',
      c_base_markup_attributes::ATTRIBUTE_ON_VOLUME_CHANGE => 'onvolumechange',
      c_base_markup_attributes::ATTRIBUTE_ON_WAITING => 'onwaiting',
    );

    if (is_null($tag)) {
      foreach ($attributes as $attribute_id => $attribute_name) {
        $attribute = $this->html->get_attribute($attribute_id)->get_value_exact();
        if (!empty($attribute)) {
          $markup .= ' ' . $attribute_name . '="' . $attribute . '"';
        }
        unset($attribute);
      }
    }
    else {
      foreach ($attributes as $attribute_id => $attribute_name) {
        $attribute = $tag->get_attribute($attribute_id)->get_value_exact();
        if (!empty($attribute)) {
          $markup .= ' ' . $attribute_name . '="' . $attribute . '"';
        }
        unset($attribute);
      }
    }
    unset($attribute_id);
    unset($attribute_name);
    unset($attributes);

    return $markup;
  }

  /**
   * Generates the HTML tag body attributes.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_body() {
    $markup = '';


    // attribute: id
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_ID)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' id="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: lang
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_LANGUAGE)->get_value_exact();
    if (!empty($attribute)) {
      $language_array = c_base_defaults_global::s_get_languages()->s_get_aliases_by_id($attribute)->get_value_exact();

      // use the first language alias available.
      $language = array_pop($language_array);
      unset($language_array);

      if (!empty($language)) {
        $markup .= ' lang="' . $language . '"';
      }
      unset($language);
    }
    unset($attribute);


    // attribute: direction
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_DIRECTION)->get_value_exact();

    if (is_int($attribute)) {
      $is_ltr = c_base_defaults_global::s_get_languages()->s_get_ltr_by_id($attribute)->get_value_exact();

      if ($is_ltr) {
        $markup .= ' dir="ltr"';
      }
      else {
        $markup .= ' dir="rtl"';
      }

      unset($is_ltr);
    }
    elseif (is_null($attribute)) {
      $markup .= ' dir="auto"';
    }
    unset($attribute);


    // attribute: title
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_TITLE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' title="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: accesskey
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' accesskey="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: contenteditable
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' contenteditable="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: hidden
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_HIDDEN)->get_value_exact();
    if ($attribute) {
      $markup .= ' hidden';
    }
    unset($attribute);


    // attribute: spellcheck
    $is_spellcheck = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_SPELLCHECK);
    if (!($is_spellcheck instanceof c_base_return_false)) {
      $is_spellcheck = $is_spellcheck->get_value_exact();
      if ($is_spellcheck) {
        $markup .= ' spellcheck="true"';
      }
      else {
        $markup .= ' spellcheck="false"';
      }
    }
    unset($is_spellcheck);


    // attribute: style
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_STYLE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' style="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: tabindex
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_TAB_INDEX)->get_value();
    if (is_int($attribute)) {
      $markup .= ' tabindex="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: translate
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_TRANSLATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' translate="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: class
    $attribute = $this->html->get_attribute_body(c_base_markup_attributes::ATTRIBUTE_CLASS)->get_value();
    if (is_array($attribute) && !empty($attribute)) {
      $markup .= ' class="' . implode(' ', $attribute) . '"';
    }
    unset($attribute);

    $attributes = array(
      c_base_markup_attributes::ATTRIBUTE_ON_ABORT => 'onabort',
      c_base_markup_attributes::ATTRIBUTE_ON_BLUR => 'onblur',
      c_base_markup_attributes::ATTRIBUTE_ON_CANCEL => 'oncancel',
      c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY => 'oncanplay',
      c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH => 'oncanplaythrough',
      c_base_markup_attributes::ATTRIBUTE_ON_CHANGE => 'onchange',
      c_base_markup_attributes::ATTRIBUTE_ON_CLICK => 'onclick',
      c_base_markup_attributes::ATTRIBUTE_ON_CUE_CHANGE => 'oncuechange',
      c_base_markup_attributes::ATTRIBUTE_ON_DOUBLE_CLICK => 'ondblclick',
      c_base_markup_attributes::ATTRIBUTE_ON_DURATION_CHANGE => 'ondurationchange',
      c_base_markup_attributes::ATTRIBUTE_ON_EMPTIED => 'onemptied',
      c_base_markup_attributes::ATTRIBUTE_ON_ENDED => 'onended',
      c_base_markup_attributes::ATTRIBUTE_ON_ERROR => 'onerror',
      c_base_markup_attributes::ATTRIBUTE_ON_FOCUS => 'onfocus',
      c_base_markup_attributes::ATTRIBUTE_ON_INPUT => 'oninput',
      c_base_markup_attributes::ATTRIBUTE_ON_INVALID => 'oninvalid',
      c_base_markup_attributes::ATTRIBUTE_ON_KEY_DOWN => 'onkeydown',
      c_base_markup_attributes::ATTRIBUTE_ON_KEY_PRESS => 'onkeypress',
      c_base_markup_attributes::ATTRIBUTE_ON_KEY_UP => 'onkeyup',
      c_base_markup_attributes::ATTRIBUTE_ON_LOAD => 'onload',
      c_base_markup_attributes::ATTRIBUTE_ON_LOADED_DATA => 'onloadeddata',
      c_base_markup_attributes::ATTRIBUTE_ON_LOADED_META_DATA => 'onloadedmetadata',
      c_base_markup_attributes::ATTRIBUTE_ON_LOAD_START => 'onloadstart',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_DOWN => 'onmousedown',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_ENTER => 'onmouseenter',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_LEAVE => 'onmouseleave',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_MOVE => 'onmousemove',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OUT => 'onmouseout',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OVER => 'onmouseover',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_UP => 'onmouseup',
      c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_WHEEL => 'onmousewheel',
      c_base_markup_attributes::ATTRIBUTE_ON_PAUSE => 'onpause',
      c_base_markup_attributes::ATTRIBUTE_ON_PLAY => 'onplay',
      c_base_markup_attributes::ATTRIBUTE_ON_PLAYING => 'onplaying',
      c_base_markup_attributes::ATTRIBUTE_ON_PROGRESS => 'onprogress',
      c_base_markup_attributes::ATTRIBUTE_ON_RATED_CHANGE => 'onratechange',
      c_base_markup_attributes::ATTRIBUTE_ON_RESET => 'onreset',
      c_base_markup_attributes::ATTRIBUTE_ON_RESIZE => 'onresize',
      c_base_markup_attributes::ATTRIBUTE_ON_SCROLL => 'onscroll',
      c_base_markup_attributes::ATTRIBUTE_ON_SEEKED => 'onseeked',
      c_base_markup_attributes::ATTRIBUTE_ON_SEEKING => 'onseeking',
      c_base_markup_attributes::ATTRIBUTE_ON_SELECT => 'onselect',
      c_base_markup_attributes::ATTRIBUTE_ON_SHOW => 'onshow',
      c_base_markup_attributes::ATTRIBUTE_ON_INSTALLED => 'onstalled',
      c_base_markup_attributes::ATTRIBUTE_ON_SUBMIT => 'onsubmit',
      c_base_markup_attributes::ATTRIBUTE_ON_SUSPEND => 'onsuspend',
      c_base_markup_attributes::ATTRIBUTE_ON_TIME_UPDATE => 'ontimeupdate',
      c_base_markup_attributes::ATTRIBUTE_ON_TOGGLE => 'ontoggle',
      c_base_markup_attributes::ATTRIBUTE_ON_VOLUME_CHANGE => 'onvolumechange',
      c_base_markup_attributes::ATTRIBUTE_ON_WAITING => 'onwaiting',
    );

    foreach ($attributes as $attribute_id => $attribute_name) {
      $attribute = $this->html->get_attribute_body($attribute_id)->get_value_exact();
      if (!empty($attribute)) {
        $markup .= ' ' . $attribute_name . '="' . $attribute . '"';
      }
      unset($attribute);
    }
    unset($attribute_id);
    unset($attribute_name);
    unset($attributes);

    return $markup;
  }

  /**
   * Generates the HTML head tags.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_head_tags() {
    $markup = '';

    $header_tags = $this->html->get_headers()->get_value();
    if (!is_array($header_tags)) {
      return $markup;
    }

    foreach ($header_tags as $header_tag) {
      $tag_type = $header_tag->get_type()->get_value_exact();

      if ($tag_type === c_base_markup_tag::TYPE_BASE) {
        $markup .= '<base' . $this->p_render_markup_attributes_global($header_tag) . $this->p_render_markup_attributes_base($header_tag) . '>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_LINK) {
        $markup .= '<link' . $this->p_render_markup_attributes_global($header_tag) . $this->p_render_markup_attributes_link($header_tag) . '>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_META) {
        $markup .= '<meta' . $this->p_render_markup_attributes_global($header_tag) . $this->p_render_markup_attributes_meta($header_tag) . '>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_NO_SCRIPT) {
        $markup .= '<noscript' . $this->p_render_markup_attributes_global($header_tag) . '>';
        $markup .= $header_tag->get_text()->get_value_exact();
        $markup .= '</noscript>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_SCRIPT) {
        $markup .= '<script' . $this->p_render_markup_attributes_global($header_tag) . $this->p_render_markup_attributes_script($header_tag) . '>';
        $markup .= $header_tag->get_text()->get_value_exact();
        $markup .= '</script>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_STYLE) {
        $markup .= '<style' . $this->p_render_markup_attributes_global($header_tag) . $this->p_render_markup_attributes_style($header_tag) . '>';
        $markup .= $header_tag->get_text()->get_value_exact();
        $markup .= '</style>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_TEMPLATE) {
        $markup .= '<template' . $this->p_render_markup_attributes_global($header_tag) . '>';
        $markup .= $header_tag->get_text()->get_value_exact();
        $markup .= '</template>';
      }
      elseif ($tag_type === c_base_markup_tag::TYPE_TITLE) {
        $markup .= '<title' . $this->p_render_markup_attributes_global($header_tag) . '>';
        $markup .= $header_tag->get_text()->get_value_exact();
        $markup .= '</title>';
      }
    }
    unset($header_tag);
    unset($header_tags);
    unset($tag_type);

    return $markup;
  }

  /**
   * Generates the HTML body tags.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_body_tags() {
    $markup = '';

    $body_tags = $this->html->get_body()->get_value();
    if (!is_array($body_tags)) {
      return $markup;
    }

    foreach ($body_tags as $body_tag) {
      $tag_type = $body_tag->get_type()->get_value_exact();

      $children = $body_tag->get_tags()->get_value();
      $child_markup = '';
      if (is_array($children)) {
        $this->p_render_markup_body_tags_recurse($child_markup, $children, 1);
      }
      unset($children);

      $markup .= $this->p_render_markup_body_tags_by_type($body_tag, $tag_type, $child_markup);
    }
    unset($body_tag);
    unset($body_tags);
    unset($tag_type);

    return $markup;
  }

  /**
   * Recursively generates the HTML body child tags.
   *
   * @param string &$markup
   *   The markup string.
   * @parm array $children
   *   An array of c_base_markup_tags.
   * @parm int $depth
   *   An integer representing the recursion depth.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_body_tags_recurse(&$markup, $children, $depth) {
    foreach ($children as $child_tag) {
      $tag_type = $child_tag->get_type()->get_value_exact();

      $child_markup = '';
      if ($depth < $this->max_recursion_depth) {
        $children = $child_tag->get_tags()->get_value();
        if (is_array($children)) {
          $this->p_render_markup_body_tags_recurse($child_markup, $children, $depth + 1);
        }
        unset($children);
      }

      $markup .= $this->p_render_markup_body_tags_by_type($child_tag, $tag_type, $child_markup);
    }
    unset($child_tag);
    unset($tag_type);
  }

  /**
   * Generates the HTML body tags for the given tag type.
   *
   * @todo: implement SVG body tag types.
   *
   * @param c_base_markup_tag $tag
   *   The tag to process.
   * @param int $type
   *   The tag type.
   * @param string $child_markup
   *   Renderred markup for child tags.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_body_tags_by_type($tag, $type, $child_markup) {
    $markup = '';

    if ($type === c_base_markup_tag::TYPE_A) {
      $markup .= '<a' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_a($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</a>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ABBR) {
      $markup .= '<abbr' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</abbr>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ADDRESS) {
      $markup .= '<address' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</address>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ALTERNATE_GLYPH) {
      $markup .= '<altglyph' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_alternate_glyph($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</altglyph>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ALTERNATE_GLYPH_DEFINITION) {
      $markup .= '<altglyphdef' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</altglyphdef>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ALTERNATE_GLYPH_ITEM) {
      $markup .= '<altglyphitem' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</altglyphitem>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ANIMATE) {
      $markup .= '<animate' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_animate($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</animate>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ANIMATE_MOTION) {
      $markup .= '<animatemotion' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_animate_motion($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</animatemotion>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ANIMATE_TRANSFORM) {
      $markup .= '<animatetransform' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_animate_transform($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</animatetransform>';
    }
    elseif ($type === c_base_markup_tag::TYPE_AREA) {
      $markup .= '<area' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_area($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</area>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ARTICLE) {
      $markup .= '<article' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</article>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ASIDE) {
      $markup .= '<aside' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</aside>';
    }
    elseif ($type === c_base_markup_tag::TYPE_AUDIO) {
      $markup .= '<audio' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_audio($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</audio>';
    }
    elseif ($type === c_base_markup_tag::TYPE_BOLD) {
      $markup .= '<b' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</b>';
    }
    elseif ($type === c_base_markup_tag::TYPE_BDI) {
      $markup .= '<bdi' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</bdi>';
    }
    elseif ($type === c_base_markup_tag::TYPE_BDO) {
      $markup .= '<bdo' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</bdo>';
    }
    elseif ($type === c_base_markup_tag::TYPE_BLOCKQUOTE) {
      $markup .= '<blockquote' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_blockquote($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</blockquote>';
    }
    elseif ($type === c_base_markup_tag::TYPE_BREAK) {
      $markup .= '<br' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
    }
    elseif ($type === c_base_markup_tag::TYPE_BUTTON) {
      $markup .= '<button' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_button($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</button>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CANVAS) {
      $markup .= '<canvas' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_canvas($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</canvas>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CHECKBOX) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'checkbox') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CIRCLE) {
      $markup .= '<circle' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_circle($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</circle>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CITE) {
      $markup .= '<cite' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</a>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CLIP_PATH) {
      $markup .= '<clippath' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_clip_path($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</clippath>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CODE) {
      $markup .= '<code' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</code>';
    }
    elseif ($type === c_base_markup_tag::TYPE_COL) {
      $markup .= '<col' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_col($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</col>';
    }
    elseif ($type === c_base_markup_tag::TYPE_COL_GROUP) {
      $markup .= '<colgroup' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_colgroup($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</colgroup>';
    }
    elseif ($type === c_base_markup_tag::TYPE_COLOR) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'color') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_COLOR_PROFILE) {
      $markup .= '<colorprofile' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_color_profile($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</colorprofile>';
    }
    elseif ($type === c_base_markup_tag::TYPE_CURSOR) {
      $markup .= '<cursor' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_cursor($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</cursor>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATA) {
      $markup .= '<data' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_data($tag)  . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</data>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATA_LIST) {
      $markup .= '<datalist' . $this->p_render_markup_attributes_global($tag)  . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</datalist>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATE) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'date') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DATE_TIME_LOCAL) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'datetime-local') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TERM_DESCRIPTION) {
      $markup .= '<dd' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</dd>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DEFS) {
      $markup .= '<defs' . $this->p_render_markup_attributes_global($tag)  . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</defs>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DEL) {
      $markup .= '<del' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_del($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</del>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DESCRIPTION) {
      $markup .= '<description' . $this->p_render_markup_attributes_global($tag)  . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</description>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DETAILS) {
      $markup .= '<details' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_details($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</details>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DFN) {
      $markup .= '<dfn' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</dfn>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DIALOG) {
      $markup .= '<dialog' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_dialog($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</dialog>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DIVIDER) {
      $markup .= '<div' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</div>';
    }
    elseif ($type === c_base_markup_tag::TYPE_DEFINITION_LIST) {
      $markup .= '<dl' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</dl>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TERM_NAME) {
      $markup .= '<dt' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</dt>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ELLIPSE) {
      $markup .= '<ellipse' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_ellipse($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</ellipse>';
    }
    elseif ($type === c_base_markup_tag::TYPE_EM) {
      $markup .= '<em' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</em>';
    }
    elseif ($type === c_base_markup_tag::TYPE_EMAIL) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'email') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_EMBED) {
      $markup .= '<embed' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_embed($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</embed>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FE_BLEND) {
      $markup .= '<feblend' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_fe_blend($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</feblend>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FIELD_SET) {
      $markup .= '<fieldset' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_fieldset($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</fieldset>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FIGURE) {
      $markup .= '<figure' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</figure>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FIGURE_CAPTION) {
      $markup .= '<figcaption' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</figcaption>';
    }
    elseif ($type === c_base_markup_tag::TYPE_GROUP) {
      $markup .= '<g' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_group($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</g>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FILE) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'file') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FOOTER) {
      $markup .= '<footer' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</footer>';
    }
    elseif ($type === c_base_markup_tag::TYPE_FORM) {
      $markup .= '<form' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_form($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</form>';
    }
    elseif ($type === c_base_markup_tag::TYPE_H1) {
      $markup .= '<h1' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</h1>';
    }
    elseif ($type === c_base_markup_tag::TYPE_H2) {
      $markup .= '<h2' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</h2>';
    }
    elseif ($type === c_base_markup_tag::TYPE_H3) {
      $markup .= '<h3' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</h3>';
    }
    elseif ($type === c_base_markup_tag::TYPE_H4) {
      $markup .= '<h4' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</h4>';
    }
    elseif ($type === c_base_markup_tag::TYPE_H5) {
      $markup .= '<h5' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag-get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</h5>';
    }
    elseif ($type === c_base_markup_tag::TYPE_H6) {
      $markup .= '<h6' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</h6>';
    }
    elseif ($type === c_base_markup_tag::TYPE_HX) {
      $markup .= '<div' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</div>';
    }
    elseif ($type === c_base_markup_tag::TYPE_HEADER) {
      $markup .= '<header' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</header>';
    }
    elseif ($type === c_base_markup_tag::TYPE_HIDDEN) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'hidden') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_HORIZONTAL_RULER) {
      $markup .= '<hr' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ITALICS) {
      $markup .= '<i' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</i>';
    }
    elseif ($type === c_base_markup_tag::TYPE_INLINE_FRAME) {
      $markup .= '<iframe' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_iframe($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</iframe>';
    }
    elseif ($type === c_base_markup_tag::TYPE_IMAGE) {
      $markup .= '<img' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_image($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</img>';
    }
    elseif ($type === c_base_markup_tag::TYPE_IMAGE_SVG) {
      $markup .= '<image' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_image_svg($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</image>';
    }
    elseif ($type === c_base_markup_tag::TYPE_INPUT) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_INS) {
      $markup .= '<ins' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_ins($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</ins>';
    }
    elseif ($type === c_base_markup_tag::TYPE_KEYBOARD) {
      $markup .= '<kbd' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</kbd>';
    }
    elseif ($type === c_base_markup_tag::TYPE_KEY_GEN) {
      $markup .= '<keygen' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_key_gen($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</keygen>';
    }
    elseif ($type === c_base_markup_tag::TYPE_LABEL) {
      $markup .= '<label' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_label($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</label>';
    }
    elseif ($type === c_base_markup_tag::TYPE_LEGEND) {
      $markup .= '<legend' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</legend>';
    }
    elseif ($type === c_base_markup_tag::TYPE_LIST_ITEM) {
      $markup .= '<li' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_li($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</li>';
    }
    elseif ($type === c_base_markup_tag::TYPE_LINE) {
      $markup .= '<line' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_line($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</line>';
    }
    elseif ($type === c_base_markup_tag::TYPE_LINEAR_GRADIENT) {
      $markup .= '<lineargradient' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_linear_gradient($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</lineargradient>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MAIN) {
      $markup .= '<main' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</main>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MAP) {
      $markup .= '<map' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_map($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</map>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MARK) {
      $markup .= '<mark' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</mark>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MARKER) {
      $markup .= '<marker' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_marker($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</marker>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MASK) {
      $markup .= '<mask' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_mask($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</mask>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MENU) {
      $markup .= '<menu' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_menu($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</menu>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MENU_ITEM) {
      $markup .= '<menuitem' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_menuitem($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</menuitem>';
    }
    elseif ($type === c_base_markup_tag::TYPE_METER) {
      $markup .= '<meter' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_meter($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</meter>';
    }
    elseif ($type === c_base_markup_tag::TYPE_MONTH) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'month') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_NAVIGATION) {
      $markup .= '<nav' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</nav>';
    }
    elseif ($type === c_base_markup_tag::TYPE_NUMBER) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'number') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_OBJECT) {
      $markup .= '<object' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_object($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</object>';
    }
    elseif ($type === c_base_markup_tag::TYPE_ORDERED_LIST) {
      $markup .= '<ol' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_ol($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</ol>';
    }
    elseif ($type === c_base_markup_tag::TYPE_OPTIONS_GROUP) {
      $markup .= '<opt_group' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_opt_group($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</opt_group>';
    }
    elseif ($type === c_base_markup_tag::TYPE_OPTION) {
      $markup .= '<option' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_option($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</option>';
    }
    elseif ($type === c_base_markup_tag::TYPE_OUTPUT) {
      $markup .= '<output' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_output($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</output>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PARAGRAPH) {
      $markup .= '<p' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</p>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PARAM) {
      $markup .= '<param' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_param($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</param>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PASSWORD) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'password') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PATH) {
      $markup .= '<path' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_path($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</path>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PATTERN) {
      $markup .= '<pattern' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_pattern($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</pattern>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PICTURE) {
      $markup .= '<picture' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</picture>';
    }
    elseif ($type === c_base_markup_tag::TYPE_POLYGON) {
      $markup .= '<polygon' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_polygon($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</polygon>';
    }
    elseif ($type === c_base_markup_tag::TYPE_POLYLINE) {
      $markup .= '<polyline' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_polyline($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</polyline>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PREFORMATTED) {
      $markup .= '<pre' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</pre>';
    }
    elseif ($type === c_base_markup_tag::TYPE_PROGRESS) {
      $markup .= '<progress' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_progress($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</progress>';
    }
    elseif ($type === c_base_markup_tag::TYPE_Q) {
      $markup .= '<q' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_q($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</q>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RADIAL_GRADIENT) {
      $markup .= '<radialgradient' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_radial_gradient($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</radialgradient>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RADIO) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'radio') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RANGE) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'range') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RECTANGLE) {
      $markup .= '<rect' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_rectangle($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</rect>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RESET) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'reset') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RUBY_PARENTHESIS) {
      $markup .= '<rp' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</rp>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RUBY_PRONUNCIATION) {
      $markup .= '<rt' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</rt>';
    }
    elseif ($type === c_base_markup_tag::TYPE_RUBY) {
      $markup .= '<ruby' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</ruby>';
    }
    elseif ($type === c_base_markup_tag::TYPE_STRIKE_THROUGH) {
      $markup .= '<s' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</s>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SAMPLE) {
      $markup .= '<samp' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</samp>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SEARCH) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'search') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SECTION) {
      $markup .= '<section' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</section>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SELECT) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'select') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SMALL) {
      $markup .= '<small' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</small>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SOURCE) {
      $markup .= '<source' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_source($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</source>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SPAN) {
      $markup .= '<span' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</span>';
    }
    elseif ($type === c_base_markup_tag::TYPE_STOP) {
      $markup .= '<stop' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_stop($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</stop>';
    }
    elseif ($type === c_base_markup_tag::TYPE_STRONG) {
      $markup .= '<strong' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</strong>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SUB_SCRIPT) {
      $markup .= '<sub' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</sub>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SUBMIT) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'submit') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_SVG) {
      $markup .= '<svg' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_svg($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</svg>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE) {
      $markup .= '<table' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_table($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</table>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_BODY) {
      $markup .= '<tbody' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</tbody>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_CELL) {
      $markup .= '<td' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</td>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_FOOTER) {
      $markup .= '<tfoot' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</tfoot>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_HEADER) {
      $markup .= '<thead' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</thead>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_HEADER_CELL) {
      $markup .= '<th' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_table_header_cell($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</th>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TABLE_ROW) {
      $markup .= '<tr' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</tr>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'text') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT_AREA) {
      $markup .= '<textarea' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_text_area($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</textarea>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT_REFERENCE) {
      $markup .= '<tref' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</tref>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT_SPAN) {
      $markup .= '<tspan' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</tspan>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TEXT_SVG) {
      $markup .= '<text' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_text_svg($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</text>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TIME) {
      $markup .= '<time' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_time($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</time>';
    }
    elseif ($type === c_base_markup_tag::TYPE_TRACK) {
      $markup .= '<track' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_track($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</track>';
    }
    elseif ($type === c_base_markup_tag::TYPE_UNDERLINE) {
      $markup .= '<u' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</u>';
    }
    elseif ($type === c_base_markup_tag::TYPE_UNORDERED_LIST) {
      $markup .= '<ul' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</ul>';
    }
    elseif ($type === c_base_markup_tag::TYPE_URL) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'url') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_USE) {
      $markup .= '<use' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_use($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</use>';
    }
    elseif ($type === c_base_markup_tag::TYPE_VARIABLE) {
      $markup .= '<var' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</var>';
    }
    elseif ($type === c_base_markup_tag::TYPE_VIDEO) {
      $markup .= '<video' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_video($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</video>';
    }
    elseif ($type === c_base_markup_tag::TYPE_WEEK) {
      $markup .= '<input' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_input($tag, 'week') . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</input>';
    }
    elseif ($type === c_base_markup_tag::TYPE_WIDE_BREAK) {
      $markup .= '<wbr' . $this->p_render_markup_attributes_global($tag) . $this->p_render_markup_attributes_event_handler($tag) . '>';
      $markup .= $tag->get_text()->get_value_exact();
      $markup .= $child_markup;
      $markup .= '</wbr>';
    }

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body a tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_a($tag) {
    $markup = '';


    // attribute: download
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DOWNLOAD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' download="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' href="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: hreflang
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE)->get_value_exact();

    if (!empty($attribute)) {
      $language_array = c_base_defaults_global::s_get_languages()->s_get_aliases_by_id($attribute)->get_value_exact();

      // use the first language alias available.
      $language = array_pop($language_array);
      unset($language_array);

      if (!empty($language)) {
        $markup .= ' hreflang="' . $language . '"';
      }
      unset($language);
    }
    unset($attribute);


    // attribute: media
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MEDIA)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' media="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rel
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rel="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: target
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' target="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: xlink:show
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_SHOW)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:show="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:actuate
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_ACTUATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:actuate="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);



    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body altglyph tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_alternate_glyph($tag) {
    $markup = '';


    // attribute: dx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_D_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' dx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: dy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_D_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' dy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: format
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORMAT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' format="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: glyphRef
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_GLYPH_REFERENCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' glyphref="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rotate
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROTATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rotate="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body animate tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_animate($tag) {
    $markup = '';


    // attribute: attributename
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' attributename="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: by
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_BY)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' by="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: dur
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DURATION)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' dur="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: from
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FROM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' from="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: repeatcount
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REPEAT_COUNT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' repeatcount="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: to
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TO)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' to="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body animate_motion tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_animate_motion($tag) {
    $markup = '';


    // attribute: calcmode
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CALCULATE_MODE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' calcmode="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: keypoints
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_KEY_POINTS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' keypoints="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: path
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PATH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' path="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rotate
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROTATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rotate="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body animate_transform tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_animate_transform($tag) {
    $markup = '';


    // attribute: by
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_BY)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' by="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: from
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FROM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' from="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: to
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TO)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' to="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE_SVG)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' type="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body area tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_area($tag) {
    $markup = '';


    // attribute: alt
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ALTERNATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' alt="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: coords
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_COORDINATES)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' coords="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: download
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DOWNLOAD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' download="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' href="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: hreflang
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE)->get_value_exact();

    if (!empty($attribute)) {
      $language_array = c_base_defaults_global::s_get_languages()->s_get_aliases_by_id($attribute)->get_value_exact();

      // use the first language alias available.
      $language = array_pop($language_array);
      unset($language_array);

      if (!empty($language)) {
        $markup .= ' hreflang="' . $language . '"';
      }
      unset($language);
    }
    unset($attribute);


    // attribute: media
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MEDIA)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' media="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: nohref
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF_NO)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' nohref="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rel
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rel="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: shape
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SHAPE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' shape="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: target
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' target="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body audio tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_audio($tag) {
    $markup = '';


    // attribute: autoplay
    $is_autoplay = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_PLAY)->get_value_exact();
    if ($is_autoplay) {
      $markup .= ' autoplay';
    }


    // attribute: controls
    $is_controls = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CONTROLS)->get_value_exact();
    if ($is_controls) {
      $markup .= ' controls';
    }


    // attribute: loop
    $is_loop = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LOOP)->get_value_exact();
    if ($is_loop) {
      $markup .= ' loop';
    }


    // attribute: muted
    $is_muted = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MUTED)->get_value_exact();
    if ($is_muted) {
      $markup .= ' muted';
    }


    // attribute: preload
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PRELOAD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' preload="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the head base tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_base($tag) {
    $markup = '';


    // attribute: href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' href="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: target
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' target="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body blockquote tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_blockquote($tag) {
    $markup = '';


    // attribute: cite
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CITE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' cite="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body button tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_button($tag) {
    $markup = '';


    // attribute: autofocus
    $is_autofocus = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS)->get_value_exact();
    if ($is_autofocus) {
      $markup .= ' autofocus';
    }


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: formaction
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_ACTION)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' formaction="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: formenctype
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_ENCODE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' formenctype="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: formmethod
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_METHOD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' formmethod="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: formnovalidate
    $is_form_no_validate = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_NO_VALIDATE)->get_value_exact();
    if ($is_form_no_validate) {
      $markup .= ' formnovalidate';
    }


    // attribute: formtarget
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' formtarget="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE_BUTTON)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' type="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body canvas tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_canvas($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body circle tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_circle($tag) {
    $markup = '';


    // attribute: cx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CENTER_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: cy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CENTER_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: r
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIUS)->get_value();
    if (is_int($attribute)) {
      $markup .= ' r="' . $attribute . '"';
    }
    unset($attribute);
  }

  /**
   * Generates the HTML tag attributes related to the body clippath tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_clip_path($tag) {
    $markup = '';


    // attribute: clip-path
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CLIP_PATH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' clip-path="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: clippathunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CLIP_PATH_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' clippathunits="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body col tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_col($tag) {
    $markup = '';


    // attribute: span
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SPAN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' span="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body colgroup tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_colgroup($tag) {
    $markup = '';


    // attribute: span
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SPAN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' span="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body colorprofile tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_color_profile($tag) {
    $markup = '';


    // attribute: local
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LOCAL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' local="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rendering-intent
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RENDERING_INTENT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rendering-intent="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body cursor tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_cursor($tag) {
    $markup = '';


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body data tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_data($tag) {
    $markup = '';


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);


    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body del tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_del($tag) {
    $markup = '';


    // attribute: cite
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CITE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' cite="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: datetime
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DATE_TIME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' datetime="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body details tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_details($tag) {
    $markup = '';


    // attribute: open
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_OPEN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' open="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body dialog tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_dialog($tag) {
    $markup = '';


    // attribute: open
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_OPEN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' open="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body ellipse tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_ellipse($tag) {
    $markup = '';


    // attribute: cx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CENTER_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: cy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CENTER_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIUS_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' rx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: ry
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIUS_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' ry="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body embed tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_embed($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body feblend tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_fe_blend($tag) {
    $markup = '';


    // attribute: mode
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MODE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' mode="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: in
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_IN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' in="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: in2
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_IN_2)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' in2="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body fieldset tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_fieldset($tag) {
    $markup = '';


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body form tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_form($tag) {
    $markup = '';


    // attribute: accept-charset
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET)->get_value_exact();
    if (!empty($attribute)) {
      $value = c_base_charset::s_to_string($attribute)->get_value();
      if (is_string($value)) {
        $markup .= ' accept-charset="' . $value . '"';
      }
      unset($value);
    }
    unset($attribute);


    // attribute: action
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ACTION)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' action="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: autocomplete
    $is_autocomplete = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_COMPLETE);
    if (!($is_autocomplete instanceof c_base_return_false)) {
      $is_autocomplete = $is_spellcheck->get_value_exact();
      if ($is_autocomplete) {
        $markup .= ' autocomplete="on"';
      }
      else {
        $markup .= ' autocomplete="off"';
      }
    }
    unset($is_autocomplete);


    // attribute: enctype
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ENCODING_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' enctype="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: method
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_METHOD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' method="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;


    // attribute: novalidate
    $is_novalidate = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NO_VALIDATE)->get_value_exact();
    if ($is_novalidate) {
      $markup .= ' novalidate';
    }


    // attribute: target
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' target="' . $attribute . '"';
    }
    unset($attribute);
  }

  /**
   * Generates the HTML tag attributes related to the body g tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_group($tag) {
    $markup = '';


    // attribute: fill
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FILL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' fill="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: opacity
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_OPACITY)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' opacity="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body iframe tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_iframe($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: sandbox
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SANDBOX)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' sandbox="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: srcdoc
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE_DOCUMENT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' srcdoc="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body img tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_image($tag) {
    $markup = '';


    // attribute: alt
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ALTERNATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' alt="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: ismap
    $is_map = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_IS_MAP)->get_value_exact();
    if ($is_map) {
      $markup .= ' ismap';
    }


    // attribute: longdesc
    $is_map = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LONG_DESCRIPTION)->get_value_exact();
    if ($is_map) {
      $markup .= ' longdesc';
    }


    // attribute: sizes
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SIZES)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' sizes="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: srcset
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE_SET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' srcset="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: usemap
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_USE_MAP)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' usemap="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body image tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_image_svg($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value();
    if (is_int($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body input tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   * @param string|null $type
   *   (optional) If not null, then is a string that overrides the type.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_input($tag, $type = NULL) {
    $markup = '';


    // attribute: accept
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ACCEPT)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' accept="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: alt
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ALTERNATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' alt="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: autocomplete
    $is_autocomplete = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_COMPLETE);
    if (!($is_autocomplete instanceof c_base_return_false)) {
      $is_autocomplete = $is_spellcheck->get_value_exact();
      if ($is_autocomplete) {
        $markup .= ' autocomplete="on"';
      }
      else {
        $markup .= ' autocomplete="off"';
      }
    }
    unset($is_autocomplete);


    // attribute: autofocus
    $is_autofocus = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS)->get_value_exact();
    if ($is_autofocus) {
      $markup .= ' autofocus';
    }


    // attribute: checked
    $is_checked = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CHECKED)->get_value_exact();
    if ($is_checked) {
      $markup .= ' checked';
    }


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: dirname
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' dirname="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: formaction
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_ACTION)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' formaction="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: formenctype
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_ENCODE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' formenctype="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: formmethod
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_METHOD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' formmethod="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: formnovalidate
    $is_form_no_validate = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_NO_VALIDATE)->get_value_exact();
    if ($is_form_no_validate) {
      $markup .= ' formnovalidate';
    }


    // attribute: formtarget
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' formtarget="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: list
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LIST)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' list="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: max
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MAXIMUM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' max="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: maxlength
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MAXIMUM_LENGTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' maxlength="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: multiple
    $is_multiple = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MULTIPLE)->get_value_exact();
    if ($is_multiple) {
      $markup .= ' multiple';
    }


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: pattern
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PATTERN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' pattern="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: placeholder
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PLACE_HOLDER)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' placeholder="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: readonly
    $is_readonly = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_READONLY)->get_value_exact();
    if ($is_readonly) {
      $markup .= ' readonly';
    }


    // attribute: required
    $is_required = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REQUIRED)->get_value_exact();
    if ($is_required) {
      $markup .= ' required';
    }


    // attribute: size
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SIZE)->get_value();
    if (is_int($attribute)) {
      $markup .= ' size="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: step
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_STEP)->get_value();
    if (is_int($attribute)) {
      $markup .= ' step="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    if (is_null($type)) {
      $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE_BUTTON)->get_value_exact();
      if (!empty($attribute)) {
        $markup .= ' type="' . $attribute . '"';
      }
      unset($attribute);
    }
    else {
      $markup .= ' type="' . $type . '"';
    }


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body ins tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_ins($tag) {
    $markup = '';


    // attribute: cite
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CITE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' cite="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: datetime
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DATE_TIME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' datetime="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body keygen tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_key_gen($tag) {
    $markup = '';


    // attribute: autofocus
    $is_autofocus = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS)->get_value_exact();
    if ($is_autofocus) {
      $markup .= ' autofocus';
    }


    // attribute: challenge
    $is_challenge = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CHALLENGE)->get_value_exact();
    if ($is_challenge) {
      $markup .= ' challenge';
    }


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: keytype
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_KEY_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' keytype="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body label tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_label($tag) {
    $markup = '';


    // attribute: for
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FOR)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' for="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body li tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_li($tag) {
    $markup = '';


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body line tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_line($tag) {
    $markup = '';


    // attribute: x1
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X_1)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x1="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x2
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X_2)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x2="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y1
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y_1)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y1="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y2
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y_2)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y2="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body lineargradient tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_linear_gradient($tag) {
    $markup = '';


    // attribute: gradientunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_GRADIANT_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' gradientunits="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: gradienttransform
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_GRADIANT_TRANSFORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' gradienttransform="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x1
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X_1)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x1="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x2
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X_2)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x2="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y1
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y_1)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y1="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y2
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y_2)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y2="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the head link tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_link($tag) {
    $markup = '';


    // attribute: crossorigin
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CROSS_ORIGIN)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' crossorigin="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' href="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: hreflang
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE)->get_value_exact();

    if (!empty($attribute)) {
      $language_array = c_base_defaults_global::s_get_languages()->s_get_aliases_by_id($attribute)->get_value_exact();

      // use the first language alias available.
      $language = array_pop($language_array);
      unset($language_array);

      if (!empty($language)) {
        $markup .= ' hreflang="' . $language . '"';
      }
      unset($language);
    }
    unset($attribute);


    // attribute: media
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MEDIA)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' media="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rel
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rel="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: sizes
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SIZES)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' sizes="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: target
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TARGET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' target="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body map tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_map($tag) {
    $markup = '';


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body marker tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_marker($tag) {
    $markup = '';


    // attribute: gradienttransform
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_GRADIANT_TRANSFORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' gradienttransform="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: markerheight
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MARKER_HEIGHT)->get_value();
    if (is_int($attribute)) {
      $markup .= ' markerheight="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: markerunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MARKER_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' markerunits="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: markerwidth
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MARKER_WIDTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' markerwidth="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: orient
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ORIENTATION)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' orient="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: refx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REFERENCE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' refx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: refy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REFERENCE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' refy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: viewbox
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VIEW_BOX)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' viewbox="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body mask tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_mask($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value();
    if (is_int($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: maskunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MASK_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' maskunits="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body menu tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_menu($tag) {
    $markup = '';


    // attribute: label
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' label="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' type="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body menuitem tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_menuitem($tag) {
    $markup = '';


    // attribute: checked
    $is_checked = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CHECKED)->get_value_exact();
    if ($is_checked) {
      $markup .= ' checked';
    }


    // attribute: default
    $is_default = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DEFAULT)->get_value_exact();
    if ($is_default) {
      $markup .= ' default';
    }


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: icon
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ICON)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' icon="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: label
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' label="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: radiogroup
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIO_GROUP)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' radiogroup="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' type="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body meter tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_meter($tag) {
    $markup = '';


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: high
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HIGH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' high="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: low
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LOW)->get_value();
    if (is_int($attribute)) {
      $markup .= ' low="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: max
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MAXIMUM_NUMBER)->get_value();
    if (is_int($attribute)) {
      $markup .= ' max="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: min
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MINIMUM_NUMBER)->get_value();
    if (is_int($attribute)) {
      $markup .= ' min="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: optimum
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_OPTIMUM)->get_value();
    if (is_int($attribute)) {
      $markup .= ' optimum="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE_NUMBER)->get_value();
    if (is_int($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the head meta tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_meta($tag) {
    $markup = '';


    // attribute: charset
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET)->get_value_exact();
    if (!empty($attribute)) {
      $value = c_base_charset::s_to_string($attribute)->get_value_exact();
      $markup .= ' charset="' . $value . '"';
      unset($value);
    }
    unset($attribute);


    // attribute: content
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' content="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: http-equiv
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' http-equiv="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body object tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_object($tag) {
    $markup = '';


    // attribute: data
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DATA)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' data="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);


    // attribute: usemap
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_USE_MAP)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' usemap="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body ol tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_ol($tag) {
    $markup = '';


    // attribute: reversed
    $is_reversed = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_REVERSED)->get_value_exact();
    if ($is_reversed) {
      $markup .= ' reversed';
    }


    // attribute: start
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_START)->get_value();
    if (is_int($attribute)) {
      $markup .= ' start="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE_LIST)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' type="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body optgroup tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_opt_group($tag) {
    $markup = '';


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: label
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' label="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body option tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_option($tag) {
    $markup = '';


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: label
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' label="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: disabled
    $is_selected = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SELECTED)->get_value_exact();
    if ($is_selected) {
      $markup .= ' selected';
    }


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body output tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_output($tag) {
    $markup = '';


    // attribute: for
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FOR)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' for="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body param tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_param($tag) {
    $markup = '';


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value();
    if (is_int($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE)->get_value();
    if (is_int($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body path tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_path($tag) {
    $markup = '';


    // attribute: d
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_D)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' d="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: path_length
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PATH_LENGTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' pathlength="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: transform
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TRANSFORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' transform="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body pattern tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_pattern($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value();
    if (is_int($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: patternunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PATTERN_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' patternunits="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: patterncontentunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PATTERN_CONTENT_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' patterncontentunits="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: patterntransform
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PATTERN_TRANSFORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' patterntransform="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: viewbox
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VIEW_BOX)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' viewbox="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body polygon tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_polygon($tag) {
    $markup = '';


    // attribute: points
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_POINTS)->get_value();
    if (is_int($attribute)) {
      $markup .= ' points="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: fill-rule
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FILL_RULE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' fill-rule="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body polyline tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_polyline($tag) {
    $markup = '';


    // attribute: points
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_POINTS)->get_value();
    if (is_int($attribute)) {
      $markup .= ' points="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body progress tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_progress($tag) {
    $markup = '';


    // attribute: max
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MAXIMUM_NUMBER)->get_value();
    if (is_int($attribute)) {
      $markup .= ' max="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: value
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VALUE_NUMBER)->get_value();
    if (is_int($attribute)) {
      $markup .= ' value="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body q tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_q($tag) {
    $markup = '';


    // attribute: cite
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CITE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' cite="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body radialgradient tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_radial_gradient($tag) {
    $markup = '';


    // attribute: cx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CENTER_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: cy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CENTER_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: fx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FOCUS_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' fx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: fy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FOCUS_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' fy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: gradientunits
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_GRADIANT_UNITS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' gradientunits="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: r
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIUS)->get_value();
    if (is_int($attribute)) {
      $markup .= ' r="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: spreadmethod
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SPREAD_METHOD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' spreadmethod="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body rect tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_rectangle($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value();
    if (is_int($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIUS_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' rx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: ry
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_RADIUS_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body stop tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_stop($tag) {
    $markup = '';


    // attribute: offset
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_OFFSET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' offset="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: stop-color
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_STOP_COLOR)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' stop-color="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: stop-opacity
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_STOP_OPACITY)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' stop-opacity="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the head script tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_script($tag) {
    $markup = '';


    // attribute: async
    $is_asynchronous = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ASYNCHRONOUS)->get_value_exact();
    if ($is_asynchronous) {
      $markup .= ' async';
    }


    // attribute: charset
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET)->get_value_exact();
    if (!empty($attribute)) {
      $value = c_base_charset::s_to_string($attribute)->get_value();
      if (is_string($value)) {
        $markup .= ' charset="' . $value . '"';
      }
      unset($value);
    }
    unset($attribute);


    // attribute: defer
    $is_deferred = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DEFER)->get_value_exact();
    if ($is_deferred) {
      $markup .= ' defer';
    }


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute)->get_value();

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body source tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_source($tag) {
    $markup = '';


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: srcset
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE_SET)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' srcset="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: media
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MEDIA)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' media="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: sizes
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SIZES)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' sizes="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the head style tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_style($tag) {
    $markup = '';


    // attribute: media
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MEDIA)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' media="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: scoped
    $is_scoped = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SCOPED)->get_value_exact();
    if ($is_scoped) {
      $markup .= ' scoped';
    }


    // attribute: type
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE)->get_value_exact();
    if (!empty($attribute)) {
      $mime_types = c_base_mime::s_get_names_by_id($attribute);

      if (is_array($mime_types)) {
        // use the first mime type available.
        $mime_type = array_pop($mime_types);

        $markup .= ' type="' . $mime_type . '"';
        unset($mime_type);
      }
      unset($mime_types);
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body svg tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_svg($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: viewbox
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_VIEW_BOX)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' viewbox="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xml
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XML)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xml="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xmlns
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XMLNS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xmlns="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xmlns:xlink
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XMLNS_XLINK)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xmlns:xlink="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xml:space
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XML_SPACE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xml:space="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body table tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_table($tag) {
    $markup = '';


    // attribute: sortable
    $is_sortable = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SORTABLE)->get_value_exact();
    if ($is_sortable) {
      $markup .= ' sortable';
    }

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body th tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_table_header_cell($tag) {
    $markup = '';


    // attribute: abbr
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ABBR)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' abbr="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: colspan
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_COLUMN_SPAN)->get_value();
    if (is_int($attribute)) {
      $markup .= ' colspan="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: headers
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEADERS)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' headers="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rowspan
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROW_SPAN)->get_value();
    if (is_int($attribute)) {
      $markup .= ' rowspan="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: scope
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SCOPE)->get_value();
    if (is_int($attribute)) {
      $markup .= ' scope="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: sorted
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SORTED)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' sorted="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body textarea tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_text_area($tag) {
    $markup = '';


    // attribute: autofocus
    $is_autofocus = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS)->get_value_exact();
    if ($is_autofocus) {
      $markup .= ' autofocus';
    }


    // attribute: cols
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_COLUMNS)->get_value();
    if (is_int($attribute)) {
      $markup .= ' cols="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: dirname
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' dirname="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: disabled
    $is_disabled = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DISABLED)->get_value_exact();
    if ($is_disabled) {
      $markup .= ' disabled';
    }


    // attribute: form
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_FORM)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' form="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: maxlength
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MAXIMUM_LENGTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' maxlength="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: name
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_NAME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' name="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: placeholder
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PLACE_HOLDER)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' placeholder="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: readonly
    $is_readonly = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_READONLY)->get_value_exact();
    if ($is_readonly) {
      $markup .= ' readonly';
    }


    // attribute: rows
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROWS)->get_value();
    if (is_int($attribute)) {
      $markup .= ' rows="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: wrap
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WRAP)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' wrap="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body text tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_text_svg($tag) {
    $markup = '';


    // attribute: dx
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_D_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' dx="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: dy
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_D_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' dy="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: lengthadjust
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LENGTH_ADJUST)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' lengthadjust="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: rotate
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_ROTATE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' rotate="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: textlength
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_TEXT_LENGTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' textlength="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body time tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_time($tag) {
    $markup = '';


    // attribute: datetime
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DATE_TIME)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' datetime="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body track tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_track($tag) {
    $markup = '';


    // attribute: default
    $is_default = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_DEFAULT)->get_value_exact();
    if ($is_default) {
      $markup .= ' default';
    }


    // attribute: kind
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_KIND)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' kind="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: label
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LABEL)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' label="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: srclang
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE_LANGUAGE)->get_value_exact();

    if (!empty($attribute)) {
      $language_array = c_base_defaults_global::s_get_languages()->s_get_aliases_by_id($attribute)->get_value_exact();

      // use the first language alias available.
      $language = array_pop($language_array);
      unset($language_array);

      if (!empty($language)) {
        $markup .= ' srclang="' . $language . '"';
      }
      unset($language);
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body use tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_use($tag) {
    $markup = '';


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value();
    if (is_int($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value();
    if (is_int($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: x
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_X)->get_value();
    if (is_int($attribute)) {
      $markup .= ' x="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: xlink:href
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_XLINK_HREF)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' xlink:href="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: y
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_Y)->get_value();
    if (is_int($attribute)) {
      $markup .= ' y="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }

  /**
   * Generates the HTML tag attributes related to the body video tag.
   *
   * @param c_base_markup_tag $tag
   *   The tag to get the attributes of.
   *
   * @return string
   *   The renderred string.
   */
  private function p_render_markup_attributes_video($tag) {
    $markup = '';


    // attribute: autoplay
    $is_autoplay = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_AUTO_PLAY)->get_value_exact();
    if ($is_autoplay) {
      $markup .= ' autoplay';
    }


    // attribute: controls
    $is_controls = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_CONTROLS)->get_value_exact();
    if ($is_controls) {
      $markup .= ' controls';
    }


    // attribute: height
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_HEIGHT)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' height="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: loop
    $is_loop = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_LOOP)->get_value_exact();
    if ($is_loop) {
      $markup .= ' loop';
    }


    // attribute: muted
    $is_muted = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_MUTED)->get_value_exact();
    if ($is_muted) {
      $markup .= ' muted';
    }


    // attribute: poster
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_POSTER)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' poster="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: preload
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_PRELOAD)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' preload="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: src
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_SOURCE)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' src="' . $attribute . '"';
    }
    unset($attribute);


    // attribute: width
    $attribute = $tag->get_attribute(c_base_markup_attributes::ATTRIBUTE_WIDTH)->get_value_exact();
    if (!empty($attribute)) {
      $markup .= ' width="' . $attribute . '"';
    }
    unset($attribute);

    return $markup;
  }
}
