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
 */
class c_standard_path extends c_base_path {
  protected const CSS_NAME = 'content-wrapper';

  protected const CSS_AS_TITLE            = 'as-title';
  protected const CSS_AS_TEXT             = 'as-text';
  protected const CSS_AS_TEXT_BLOCK       = 'as-text-block';
  protected const CSS_AS_PARAGRAPH        = 'as-paragraph';
  protected const CSS_AS_PARAGRAPH_BLOCK  = 'as-paragraph-block';

  protected const CSS_IS_JAVASCRIPT_ENABLED  = 'javascript-enabled';
  protected const CSS_IS_JAVASCRIPT_DISABLED = 'javascript-disabled';
  protected const CSS_IS_CONTENT_TYPE        = 'is-html_5
  ';
  protected const CSS_DATE_YEAR     = 'date-year-';
  protected const CSS_DATE_MONTH    = 'date-month-';
  protected const CSS_DATE_WEEK_DAY = 'date-week_day-';
  protected const CSS_DATE_DAY      = 'date-day-';
  protected const CSS_DATE_HOUR     = 'date-hour-';
  protected const CSS_DATE_MINUTE   = 'date-minute-';
  protected const CSS_DATE_SECOND   = 'date-second-';

  protected const CSS_PATH_PART = 'path-part-';
  protected const CSS_PATH_FULL = 'path-full-';

  protected $http;
  protected $database;
  protected $session;
  protected $settings;


  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->http     = NULL;
    $this->database = NULL;
    $this->session  = NULL;
    $this->settings = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->http);
    unset($this->database);
    unset($this->session);
    unset($this->settings);

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
  }

  /**
   * Creates the standard wrapper.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_wrapper() {
    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, $this->settings['base_css'] . self::CSS_NAME, array($this->settings['base_css'] . self::CSS_NAME,  self::CSS_NAME));
  }

  /**
   * Creates the standard break tag.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_break() {
    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BREAK);
  }

  /**
   * Creates the standard title.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_title($text, $arguments = array()) {
    if (is_int($text)) {
      return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1, NULL, array(self::CSS_AS_TITLE), $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_H1, NULL, array(self::CSS_AS_TITLE), $text);
  }

  /**
   * Creates the standard text.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text($text, $arguments = array()) {
    $type = c_base_markup_tag::TYPE_SPAN;
    if (isset($this->settings['standards_issue-use_p_tags']) && $this->settings['standards_issue-use_p_tags']) {
      $type = c_base_markup_tag::TYPE_PARAGRAPH;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_TEXT), $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_TEXT), $text);
  }

  /**
   * Creates the standard paragraph.
   *
   * @param int|string $text
   *   The text or the text code to use.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph($text, $arguments = array()) {
    $type = c_base_markup_tag::TYPE_SPAN;
    if (isset($this->settings['standards_issue-use_p_tags']) && $this->settings['standards_issue-use_p_tags']) {
      $type = c_base_markup_tag::TYPE_PARAGRAPH;
    }

    if (is_int($text)) {
      return c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_PARAGRAPH), $this->pr_get_text($text, $arguments));
    }

    return c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_PARAGRAPH), $text);
  }

  /**
   * Creates the standard text, wrapped in a block.
   *
   * @param int|string|null $text
   *   The text or the text code to use.
   *   If NULL, only the block is created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text_block($text, $arguments = array()) {
    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, array(self::CSS_AS_TEXT_BLOCK));

    if (!is_null($text)) {
      $type = c_base_markup_tag::TYPE_SPAN;
      if (isset($this->settings['standards_issue-use_p_tags']) && $this->settings['standards_issue-use_p_tags']) {
        $type = c_base_markup_tag::TYPE_PARAGRAPH;
      }

      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_TEXT), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_TEXT), $text);
      }
      unset($type);

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Creates the standard text, wrapped in a block.
   *
   * @param int|string|null $text
   *   The text or the text code to use.
   *   If NULL, only the block is created.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph_block($text, $arguments = array()) {
    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, array(self::CSS_AS_PARAGRAPH_BLOCK));

    if (!is_null($text)) {
      $type = c_base_markup_tag::TYPE_SPAN;
      if (isset($this->settings['standards_issue-use_p_tags']) && $this->settings['standards_issue-use_p_tags']) {
        $type = c_base_markup_tag::TYPE_PARAGRAPH;
      }

      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_PARAGRAPH_BLOCK), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($type, NULL, array(self::CSS_AS_PARAGRAPH_BLOCK), $text);
      }
      unset($type);

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
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

  /**
   * Create a new HTML markup class with default settings populated.
   *
   * @return c_base_html
   *   The generated html is returned on success.
   *   The generated html with error bit set is returned on error.
   */
  protected function pr_create_html() {
    $title = $this->pr_get_title();

    $html = new c_base_html();

    $request_uri = $this->http->get_request(c_base_http::REQUEST_URI)->get_value_exact();
    if (isset($request_uri['data']) && is_string($request_uri['data'])) {
      $request_uri = $request_uri['data'];
      unset($request_uri['current']);
      unset($request_uri['invalid']);

      $request_path = $this->http->get_request_uri_relative($this->settings['base_path'])->get_value_exact();
    }
    else {
      $request_uri = array(
        'scheme' => $this->settings['base_scheme'],
        'authority' => $this->settings['base_host'],
        'path' => $this->settings['base_path'],
        'query' => NULL,
        'fragment' => NULL,
        'url' => TRUE,
      );

      $request_path = '/';
    }

    // add date/time classes.
    $instance = c_base_defaults_global::s_get_timestamp_session()->get_value_exact();
    $class[] = self::CSS_DATE_YEAR . $html->sanitize_css(date('Y', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_MONTH . $html->sanitize_css(strtolower(date('F', $instance)))->get_value_exact();
    $class[] = self::CSS_DATE_WEEK_DAY . $html->sanitize_css(strtolower(date('l', $instance)))->get_value_exact();
    $class[] = self::CSS_DATE_DAY . $html->sanitize_css(date('d', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_HOUR . $html->sanitize_css(date('H', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_MINUTE . $html->sanitize_css(date('m', $instance))->get_value_exact();
    $class[] = self::CSS_DATE_SECOND . $html->sanitize_css(date('s', $instance))->get_value_exact();
    unset($instance);

    // add path classes
    $path = $this->http->get_request_uri_relative($request_uri['path'])->get_value_exact();
    $path_parts = explode('/', $path);

    if (is_array($path_parts)) {
      $sanitized = NULL;
      $delta = 0;
      foreach ($path_parts as $path_part) {
        $sanitized_part = $html->sanitize_css($path_part, TRUE)->get_value_exact();
        $sanitized .= '-' . $sanitized_part;

        $class[] = self::CSS_PATH_PART . $delta . '-' . $html->sanitize_css($sanitized_part)->get_value_exact();
        $delta++;
      }
      unset($path_part);
      unset($sanitized_part);

      $class[] = self::CSS_PATH_FULL . $html->sanitize_css(substr($sanitized, 1))->get_value_exact();
      unset($sanitized);
    }
    unset($path_parts);

    $class[] = self::CSS_IS_CONTENT_TYPE;
    $class[] = self::CSS_IS_JAVASCRIPT_DISABLED;

    $html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_CLASS, $class);
    unset($class);


    // assign id attribute
    $id = $html->sanitize_css('system-' . $this->settings['session_system'])->get_value_exact();
    #$html->set_attribute(c_base_markup_attributes::ATTRIBUTE_ID, $id);
    $html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ID, $id);
    unset($id);


    // assign language attribute.
    $language = i_base_languages::ENGLISH_US;
    $languages = $this->http->get_response_content_language()->get_value_exact();
    if (is_array($languages) && !empty($languages)) {
      $language = reset($languages);
    }

    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, $language);
    unset($language);


    // assign default direction attribute (@todo: this needs to come from the language attribute (when possible)).
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION, 'ltr');


    // assign title header tag (setting title tag at delta 0 so that it can easily be overriden as needed).
    if (is_string($title)) {
      $tag = new c_base_markup_tag();
      $tag->set_type(c_base_markup_tag::TYPE_TITLE);
      $tag->set_text($title);
      $html->set_header($tag, 0);
      unset($tag);
    }


    // assign base header tag
    if (isset($request_uri['path']) && is_string($request_uri['path']) && mb_strlen($request_uri['scheme']) > 0) {
      $href = '';
      if (isset($request_uri['scheme']) && is_string($request_uri['scheme']) && mb_strlen($request_uri['scheme']) > 0) {
        if (isset($request_uri['authority']) && is_string($request_uri['authority']) && mb_strlen($request_uri['authority']) > 0) {
          $href .= $request_uri['scheme'] . '://' . $request_uri['authority'];
        }
      }

      $href .= $request_uri['path'];

      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_BASE);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $href);
      $html->set_header($tag);
      unset($tag);
      unset($href);
    }


    // assign http-equiv header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'Content-Type');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'text/html; charset=utf-8');
    $html->set_header($tag);
    unset($tag);


    // assign charset header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET, c_base_charset::UTF_8);
    $html->set_header($tag);
    unset($tag);


    // assign canonical header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $request_uri['scheme'] . '://' . $request_path);
    $html->set_header($tag);
    unset($tag);


    // assign shortlink header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'shortlink');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $request_path);
    $html->set_header($tag);
    unset($tag);

    unset($request_path);
    unset($request_uri);


    // assign distribution header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'distribution');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'web');
    $html->set_header($tag);
    unset($tag);


    // assign robots header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'robots');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'INDEX,FOLLOW');
    $html->set_header($tag);
    unset($tag);


    // assign viewport header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'viewport');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'width=device-width, initial-scale=1');
    $html->set_header($tag);
    unset($tag);


    // assign content http-equiv header tag
    $aliases = array();
    if (is_array($languages) && !empty($languages)) {
      // assign the primary language.
      $language_aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id(reset($languages))->get_value_exact();
      if (is_array($language_aliases) && !empty($language_aliases)) {
        $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, reset($language_aliases));
      }
      unset($language_aliases);

      foreach ($languages as $language) {
        $language_aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id($language)->get_value_exact();
        if (is_array($language_aliases) && !empty($language_aliases)) {
          $aliases[] = array_pop($language_aliases);
        }
        unset($language_aliases);
      }
      unset($language);
    }
    unset($languages);

    if (!empty($aliases)) {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'content-language');
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, implode(', ', $aliases));
      $html->set_header($tag);
      unset($tag);
    }
    unset($aliases);


    // provide a custom javascript for detecting if javascript is enabled and storing in a css class name.
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SCRIPT);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE, c_base_mime::TYPE_TEXT_JS);

    $javascript = 'function f_standard_paths_hmtl_javascript_detection() {';
    $javascript .= 'document.body.removeAttribute(\'onLoad\');';
    $javascript .= 'document.body.className = document.body.className.replace(/\b' . self::CSS_IS_JAVASCRIPT_DISABLED . '\b/i, \'' . self::CSS_IS_JAVASCRIPT_ENABLED . '\');';
    $javascript .= '}';
    $tag->set_text($javascript);
    unset($javascript);

    $html->set_header($tag);
    $html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ON_LOAD, 'f_standard_paths_hmtl_javascript_detection();');
    unset($tag);

    return $html;
  }
}
