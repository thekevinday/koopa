<?php
/**
 * @file
 * Provides the standard site index class.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_paths.php');
require_once('common/base/classes/base_markup.php');

/**
 * Provides standard extensions to base paths.
 *
 * This is used primarily for generating HTML5 pages.
 */
class c_standard_path extends c_base_path {
  protected const CSS_AS_SECTION                = 'as-section';
  protected const CSS_AS_SECTION_HEADERS        = 'as-section-headers';
  protected const CSS_AS_WRAPPER                = 'as-wrapper';
  protected const CSS_AS_BREAK                  = 'as-break';
  protected const CSS_AS_TITLE                  = 'as-title';
  protected const CSS_AS_TEXT                   = 'as-text';
  protected const CSS_AS_TEXT_BLOCK             = 'as-text-block';
  protected const CSS_AS_PARAGRAPH              = 'as-paragraph';
  protected const CSS_AS_PARAGRAPH_BLOCK        = 'as-paragraph-block';
  protected const CSS_AS_LINK_BLOCK             = 'as-link_block';
  protected const CSS_AS_LINK_BLOCK_NAME        = 'as-link_block-name';
  protected const CSS_AS_LINK_BLOCK_LINK        = 'as-link_block-link';
  protected const CSS_AS_LINK_BLOCK_DESCRIPTION = 'as-link_block-description';
  protected const CSS_AS_HEADER                 = 'as-header';
  protected const CSS_AS_HEADERS                = 'as-headers';

  protected const CSS_IS_JAVASCRIPT_ENABLED     = 'javascript-enabled';
  protected const CSS_IS_JAVASCRIPT_DISABLED    = 'javascript-disabled';
  protected const CSS_IS_CONTENT_TYPE           = 'is-html_5';

  protected const CSS_SYSTEM_PREFIX = 'system-';

  protected const CSS_DATE_YEAR     = 'date-year-';
  protected const CSS_DATE_MONTH    = 'date-month-';
  protected const CSS_DATE_WEEK_DAY = 'date-week_day-';
  protected const CSS_DATE_DAY      = 'date-day-';
  protected const CSS_DATE_HOUR     = 'date-hour-';
  protected const CSS_DATE_MINUTE   = 'date-minute-';
  protected const CSS_DATE_SECOND   = 'date-second-';

  protected const CSS_PATH_PART = 'path-part-';
  protected const CSS_PATH_FULL = 'path-full-';

  protected $html;
  protected $http;
  protected $database;
  protected $session;
  protected $settings;

  protected $languages;
  protected $text_type;
  protected $request_uri;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->html     = NULL;
    $this->http     = NULL;
    $this->database = NULL;
    $this->session  = NULL;
    $this->settings = array();

    $this->languages   = array();
    $this->text_type   = NULL;
    $this->request_uri = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->html);
    unset($this->http);
    unset($this->database);
    unset($this->session);
    unset($this->settings);

    unset($this->languages);
    unset($this->text_type);
    unset($this->request_uri);

    parent::__destruct();
  }

  /**
   * Load any default settings.
   *
   * @param c_base_http $http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database $database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   (optional) An array of additional settings that are usually site-specific.
   */
  protected function pr_assign_defaults(&$http, &$database, &$session, &$settings) {
    $this->http = $http;
    $this->database = $database;
    $this->session = $session;
    $this->settings = $settings;

    $this->text_type = c_base_markup_tag::TYPE_SPAN;
    if (isset($this->settings['standards_issue-use_p_tags']) && $this->settings['standards_issue-use_p_tags']) {
      $this->text_type = c_base_markup_tag::TYPE_PARAGRAPH;
    }

    $request_uri = $this->http->get_request(c_base_http::REQUEST_URI)->get_value_exact();
    if (isset($request_uri['data']) && is_string($request_uri['data'])) {
      $request_uri = $request_uri['data'];
      unset($request_uri['current']);
      unset($request_uri['invalid']);

      $this->request_uri = $request_uri;
    }
    else {
      $this->request_uri = array(
        'scheme' => $this->settings['base_scheme'],
        'authority' => $this->settings['base_host'],
        'path' => $this->settings['base_path'],
        'query' => NULL,
        'fragment' => NULL,
        'url' => TRUE,
      );
    }
    unset($request_uri);

    $this->languages = $this->http->get_response_content_language()->get_value_exact();
    if (!is_array($this->languages)) {
      $this->languages = array();
    }
  }

  /**
   * Creates the standard section.
   *
   * @param array|null $headers
   *   An array of headers, whose keys are the header number and values are the header names.
   *   If NULL, then the headers are not assigned.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_section($headers = NULL, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_SECTION,  self::CSS_AS_SECTION);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, $id, $classes);
    unset($classes);

    if (is_array($headers)) {
      $header = $this->pr_create_tag_headers($headers, $arguments, NULL, self::CSS_AS_SECTION_HEADERS);
      $tag->set_tag($header);
      unset($header);
    }

    return $tag;
  }

  /**
   * Creates the standard wrapper.
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_wrapper($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_WRAPPER,  self::CSS_AS_WRAPPER);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
  }

  /**
   * Creates the standard break tag.
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_break($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_BREAK,  self::CSS_AS_BREAK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BREAK, $id, $classes);
  }

  /**
   * Creates the standard text.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_TEXT,  self::CSS_AS_TEXT);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($this->text_type, $id, $classes, $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($this->text_type, $id, $classes, $text);
  }

  /**
   * Creates the standard header text.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param int $header
   *   May be any number greater than 0, but only H1-H6 are used.
   *   All other cases a div tag is substituted as a simulated H7+.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_header($text, $header, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_HEADER,  self::CSS_AS_HEADER,  self::CSS_AS_HEADER . '-' . $header);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $type = c_base_markup_tag::TYPE_DIVIDER;
    if ($header == 1) {
      $type = c_base_markup_tag::TYPE_H1;
    }
    elseif ($header == 2) {
      $type = c_base_markup_tag::TYPE_H2;
    }
    elseif ($header == 3) {
      $type = c_base_markup_tag::TYPE_H3;
    }
    elseif ($header == 4) {
      $type = c_base_markup_tag::TYPE_H4;
    }
    elseif ($header == 5) {
      $type = c_base_markup_tag::TYPE_H5;
    }
    elseif ($header == 6) {
      $type = c_base_markup_tag::TYPE_H6;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($type, $id, $classes, $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($type, $id, $classes, $text);
  }

  /**
   * Creates the standard headers block.
   *
   * This is the HTML <header> tag and not the html <head> tag.
   *
   * @param array|null $headers
   *   An array of headers, whose keys are the header number and values are the header names.
   *   If NULL, then the headers are not assigned.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_headers($headers, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_HEADERS,  self::CSS_AS_HEADERS);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_HEADER, $id, $classes);
    unset($classes);

    if (is_array($headers)) {
      foreach ($headers as $header_id => $header_text) {
        $header = $this->pr_create_tag_header($header_text, $header_id, $arguments);
        $tag->set_tag($header);
        unset($header);
      }
      unset($header_id);
      unset($header_text);
    }

    return $tag;
  }

  /**
   * Creates the standard paragraph.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_PARAGRAPH,  self::CSS_AS_PARAGRAPH);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($this->text_type, $id, $classes, $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($this->text_type, $id, $classes, $text);
  }

  /**
   * Creates the standard text, wrapped in a block.
   *
   * @param int|string|null $text
   *   The text or the text code to use.
   *   If NULL, only the block is created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text_block($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_TEXT_BLOCK,  self::CSS_AS_TEXT_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array(self::CSS_AS_TEXT), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array(self::CSS_AS_TEXT), $text);
      }

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Creates the standard text, wrapped in a block.
   *
   * This is intended to be used as a paragraph (this is not the same as the <p> tag).
   *
   * @param int|string|null $text
   *   The text or the text code to use.
   *   If NULL, only the block is created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph_block($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_PARAGRAPH_BLOCK,  self::CSS_AS_PARAGRAPH_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array(self::CSS_AS_PARAGRAPH_BLOCK), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array(self::CSS_AS_PARAGRAPH_BLOCK), $text);
      }

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Creates the standard dashboard text block.
   *
   * @param int|string|null $text
   *   The text or the text code to use as the link text.
   *   If NULL, the link text is not created.
   * @param int|string|null $tooltip
   *   The text that describes the code to use as the link tooltip.
   *   If NULL, the description text is not created.
   * @param string|array|null $destination
   *   The destination url to send the link to.
   *   If an array, must be a url array.
   *   If NULL, the no destination is provided and the link is displayed instead as a label.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_link($text, $tooltip, $destination, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_LINK,  self::CSS_AS_LINK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (is_int($text)) {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_A, NULL, $classes, $this->pr_get_text($text, $arguments));
    }
    else {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_A, NULL, $classes, $text);
    }

    if (is_array($destination)) {
      $uri = $this->pr_rfc_string_combine_uri_array($destination);
      if (is_string($uri)) {
        $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $uri);
      }
      unset($uri);
    }
    else {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $destination);
    }

    // the HTML title attribute is a tooltip.
    if (is_int($tooltip)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE, $this->pr_get_text($tooltip, $arguments));
    }
    elseif (is_string($tooltip)) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE, $tooltip);
    }
  }

  /**
   * Creates the standard dashboard text block.
   *
   * @param int|string|null $text
   *   The text or the text code to use as the link text.
   *   If NULL, the link text is not created.
   * @param int|string|null $tooltip
   *   The text that describes the code to use as the link tooltip.
   *   If NULL, the description text is not created.
   * @param string|array|null $destination
   *   The destination url to send the link to.
   *   If an array, must be a url array.
   *   If NULL, the no destination is provided and the link is displayed instead as a label.
   * @param int|string|null $description
   *   The text that describes the code to use.
   *   If NULL, the description text is not created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param int $header
   *   (optional) A header id to use instead of a div to wrap the link.
   *   Set to 0 to disable.
   *   May be any number greater than or equal to 0, but only H1-H6 are used.
   *   All other cases a div tag is substituted as a simulated H7+.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_link_block($text, $tooltip, $destination, $description, $arguments = array(), $header = 0, $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . self::CSS_AS_LINK_BLOCK,  self::CSS_AS_LINK_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if ($header < 1) {
        $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, array(self::CSS_AS_TEXT, self::CSS_AS_LINK_BLOCK_NAME));
      }
      else {
        $header_classes = array($this->settings['base_css'] . self::CSS_AS_HEADER,  self::CSS_AS_HEADER,  self::CSS_AS_HEADER . '-' . $header, self::CSS_AS_LINK_BLOCK_NAME);

        $type = c_base_markup_tag::TYPE_DIVIDER;
        if ($header == 1) {
          $type = c_base_markup_tag::TYPE_H1;
        }
        elseif ($header == 2) {
          $type = c_base_markup_tag::TYPE_H2;
        }
        elseif ($header == 3) {
          $type = c_base_markup_tag::TYPE_H3;
        }
        elseif ($header == 4) {
          $type = c_base_markup_tag::TYPE_H4;
        }
        elseif ($header == 5) {
          $type = c_base_markup_tag::TYPE_H5;
        }
        elseif ($header == 6) {
          $type = c_base_markup_tag::TYPE_H6;
        }

        $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, $header_classes);
        unset($header_classes);
        unset($type);
      }

      if (!is_null($text)) {
        $tag = $this->pr_create_tag_link($text, $tooltip, $destination, $arguments, self::CSS_AS_LINK_BLOCK_LINK);
        $wrapper->set_tag($tag);
        unset($tag);
      }

      $block->set_tag($wrapper);
      unset($wrapper);
    }

    if (!is_null($description)) {
      $tag = $this->pr_create_tag_text_block($description, $arguments, self::CSS_AS_LINK_BLOCK_DESCRIPTION);
      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Create a new HTML markup class with default settings populated.
   *
   * @param bool $real_page
   *   (optional) A real page is a page where content is being provided.
   *   Examples of non-real pages are 404 pages.
   *   Certain headers and settings are discluded on non-real pages, such as canonical urls.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set on error.
   *
   * @see: self::pr_create_html_add_primary_ids()
   * @see: self::pr_create_html_add_primary_classes()
   * @see: self::pr_create_html_add_lanaguages()
   * @see: self::pr_create_html_add_title()
   * @see: self::pr_create_html_add_header_base()
   * @see: self::pr_create_html_add_header_meta()
   * @see: self::pr_create_html_add_header_link_canonical()
   * @see: self::pr_create_html_add_header_link_shortlink()
   * @see: self::pr_create_html_add_header_script()
   */
  protected function pr_create_html($real_page = TRUE) {
    $this->html = new c_base_html();

    $this->pr_create_html_add_primary_ids();
    $this->pr_create_html_add_primary_classes();
    $this->pr_create_html_add_lanaguages();
    $this->pr_create_html_add_title();
    $this->pr_create_html_add_header_base();
    $this->pr_create_html_add_header_meta();

    if ($real_page) {
      // @todo: redesign these to accept the $request_uri array instead of trying to build them directly here.
      $this->pr_create_html_add_header_link_canonical();
      $this->pr_create_html_add_header_link_shortlink();
    }

    $this->pr_create_html_add_header_script();

    return new c_base_return_true();
  }


  /**
   * Create an HTML primary id attributes.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_primary_ids() {
    $id = $this->html->sanitize_css(self::CSS_SYSTEM_PREFIX . $this->settings['session_system'])->get_value_exact();
    #$this->html->set_attribute(c_base_markup_attributes::ATTRIBUTE_ID, $id);
    $this->html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ID, $id);

    unset($id);
  }

  /**
   * Create an HTML primary classes.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_primary_classes() {

    // add date/time classes.
    $instance = c_base_defaults_global::s_get_timestamp_session()->get_value_exact();
    $class[] = self::CSS_DATE_YEAR . $this->html->sanitize_css(date('Y', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_MONTH . $this->html->sanitize_css(strtolower(date('F', $instance)))->get_value_exact();
    $class[] = self::CSS_DATE_WEEK_DAY . $this->html->sanitize_css(strtolower(date('l', $instance)))->get_value_exact();
    $class[] = self::CSS_DATE_DAY . $this->html->sanitize_css(date('d', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_HOUR . $this->html->sanitize_css(date('H', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_MINUTE . $this->html->sanitize_css(date('m', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_SECOND . $this->html->sanitize_css(date('s', $instance))->get_value_exact();
    unset($instance);

    // add path classes
    $path = $this->http->get_request_uri_relative($this->request_uri['path'])->get_value_exact();
    $path_parts = explode('/', $path);

    if (is_array($path_parts)) {
      $sanitized = NULL;
      $delta = 0;
      foreach ($path_parts as $path_part) {
        $sanitized_part = $this->html->sanitize_css($path_part, TRUE)->get_value_exact();
        $sanitized .= '-' . $sanitized_part;

        $class[] = self::CSS_PATH_PART . $delta . '-' . $this->html->sanitize_css($sanitized_part)->get_value_exact();
        $delta++;
      }
      unset($path_part);
      unset($sanitized_part);

      $class[] = self::CSS_PATH_FULL . $this->html->sanitize_css(substr($sanitized, 1))->get_value_exact();
      unset($sanitized);
    }
    unset($path_parts);

    $class[] = self::CSS_IS_CONTENT_TYPE;
    $class[] = self::CSS_IS_JAVASCRIPT_DISABLED;

    $this->html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_CLASS, $class);

    unset($class);
  }

  /**
   * Create an HTML primary classes.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_lanaguages() {
    // assign language attribute.
    $language = i_base_languages::ENGLISH_US;
    if (!empty($this->languages)) {
      $language = reset($this->languages);
    }

    $this->html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, $language);


    // assign default direction attribute.
    $this->html->set_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION, $language);

    unset($language);
  }

  /**
   * Create an HTML title tag.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_title() {
    $title = $this->pr_get_title();

    if (is_string($title)) {
      $tag = new c_base_markup_tag();
      $tag->set_type(c_base_markup_tag::TYPE_TITLE);
      $tag->set_text($title);
      $this->html->set_header($tag, 0);
      unset($tag);
    }

    unset($title);
  }

  /**
   * Create an HTML base header tag.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_header_base() {
    if (isset($this->request_uri['path']) && is_string($this->request_uri['path']) && mb_strlen($this->request_uri['scheme']) > 0) {
      $href = '';
      if (isset($this->request_uri['scheme']) && is_string($this->request_uri['scheme']) && mb_strlen($this->request_uri['scheme']) > 0) {
        if (isset($this->request_uri['authority']) && is_string($this->request_uri['authority']) && mb_strlen($this->request_uri['authority']) > 0) {
          $href .= $this->request_uri['scheme'] . '://' . $this->request_uri['authority'] . $this->settings['base_port'];
        }
      }

      $href .= $request_uri['path'];

      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BASE);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $href);
      $this->html->set_header($tag);

      unset($tag);
      unset($href);
    }
  }

  /**
   * Create an HTML header meta tags.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_header_meta() {
    // assign http-equiv header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'Content-Type');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'text/html; charset=utf-8');
    $this->html->set_header($tag);
    unset($tag);


    // assign charset header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET, c_base_charset::UTF_8);
    $this->html->set_header($tag);
    unset($tag);


    // assign distribution header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'distribution');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'web');
    $this->html->set_header($tag);
    unset($tag);


    // assign robots header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'robots');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'INDEX,FOLLOW');
    $this->html->set_header($tag);
    unset($tag);


    // assign viewport header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'viewport');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'width=device-width, initial-scale=1');
    $this->html->set_header($tag);
    unset($tag);


    // assign content http-equiv header tag
    $aliases = array();
    if (!empty($this->languages)) {
      // assign the primary language.
      $language_aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id(reset($this->languages))->get_value_exact();
      if (is_array($language_aliases) && !empty($language_aliases)) {
        $this->html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, reset($language_aliases));
      }
      unset($language_aliases);

      foreach ($this->languages as $language) {
        $language_aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id($language)->get_value_exact();
        if (is_array($language_aliases) && !empty($language_aliases)) {
          $aliases[] = array_pop($language_aliases);
        }
        unset($language_aliases);
      }
      unset($language);
    }

    if (!empty($aliases)) {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'content-language');
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, implode(', ', $aliases));
      $this->html->set_header($tag);
      unset($tag);
    }
    unset($aliases);
  }

  /**
   * Create an HTML canonical header link tag.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_header_link_canonical() {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $this->request_uri['scheme'] . '://' . $this->request_uri['authority'] . $this->request_uri['path']);
    $this->html->set_header($tag);

    unset($tag);
  }

  /**
   * Create an HTML shortlink header link tag.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_header_link_shortlink() {
    // shortlink is not provided by default, but below is an example implementation.
    #$request_path = $this->http->get_request_uri_relative($this->settings['base_path'])->get_value_exact();
    #if ($request_path == '') {
    #  $request_path = '/';
    #}

    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'shortlink');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $request_path);
    #$this->html->set_header($tag);

    #unset($request_path);
    #unset($tag);
  }

  /**
   * Create an HTML script header tags.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_header_script() {
    // provide a custom javascript for detecting if javascript is enabled and storing in a css class name.
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SCRIPT);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE, c_base_mime::TYPE_TEXT_JS);

    $javascript = 'function f_standard_paths_hmtl_javascript_detection() {';
    $javascript .= 'document.body.removeAttribute(\'onLoad\');';
    $javascript .= 'document.body.className = document.body.className.replace(/\b' . self::CSS_IS_JAVASCRIPT_DISABLED . '\b/i, \'' . self::CSS_IS_JAVASCRIPT_ENABLED . '\');';
    $javascript .= '}';
    $tag->set_text($javascript);
    unset($javascript);

    $this->html->set_header($tag);
    $this->html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ON_LOAD, 'f_standard_paths_hmtl_javascript_detection();');
    unset($tag);
  }

  /**
   * Load the title text associated with this page.
   *
   * This is provided here as a means for a language class to override with a custom language for the title.
   *
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return string|null
   *   A string is returned as the custom title.
   *   NULL is returned to enforce default title.
   */
  protected function pr_get_title($arguments = array()) {
    return NULL;
  }

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }
}
