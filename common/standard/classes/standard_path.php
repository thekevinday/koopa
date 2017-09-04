<?php
/**
 * @file
 * Provides the standard path handling class.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_menu.php');
require_once('common/base/classes/base_markup.php');
require_once('common/base/classes/base_mime.php');

/**
 * Provides standard extensions to base paths.
 *
 * This is used primarily for generating HTML5 pages.
 */
class c_standard_path extends c_base_path {
  public const PATH_SELF = '';

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
  protected const CSS_AS_HEADER_TEXT            = 'as-header-text';
  protected const CSS_AS_HEADERS                = 'as-headers';
  protected const CSS_AS_FIELD_SET              = 'as-field_set';
  protected const CSS_AS_FIELD_SET_LEGEND       = 'as-field_set-legend';
  protected const CSS_AS_FIELD_SET_CONTENT      = 'as-field_set-content';
  protected const CSS_AS_FIELD_ROW              = 'as-field_row';
  protected const CSS_AS_FIELD_ROW_NAME         = 'as-field_row-name';
  protected const CSS_AS_FIELD_ROW_VALUE        = 'as-field_row-value';
  protected const CSS_AS_ROW                    = 'as-row';
  protected const CSS_AS_ROW_EVEN               = 'as-row-even';
  protected const CSS_AS_ROW_ODD                = 'as-row-odd';
  protected const CSS_AS_ROW_VALUE              = 'as-row-value';
  protected const CSS_AS_SPACER                 = 'as-spacer';
  protected const CSS_AS_TABLE                  = 'as-table';
  protected const CSS_AS_TABLE_CAPTION          = 'as-table-caption';
  protected const CSS_AS_TABLE_COLUMN           = 'as-table-column';
  protected const CSS_AS_TABLE_COLUMN_GROUP     = 'as-table-column_group';
  protected const CSS_AS_TABLE_HEADER           = 'as-table-header';
  protected const CSS_AS_TABLE_HEADER_CELL      = 'as-table-header-cell';
  protected const CSS_AS_TABLE_BODY             = 'as-table-body';
  protected const CSS_AS_TABLE_ROW              = 'as-table-row';
  protected const CSS_AS_TABLE_CELL             = 'as-table-cell';
  protected const CSS_AS_TABLE_FOOTER           = 'as-table-footer';

  protected const CSS_IS_JAVASCRIPT_ENABLED  = 'javascript-enabled';
  protected const CSS_IS_JAVASCRIPT_DISABLED = 'javascript-disabled';
  protected const CSS_IS_CONTENT_TYPE        = 'is-html_5';

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

  protected const PATH_MENU_HEADER      = 'common/standard/menus/';
  protected const PATH_MENU_UTILITY     = 'common/standard/menus/';
  protected const PATH_MENU_BREADCRUMBS = 'common/standard/menus/';
  protected const PATH_MENU_CONTENT     = 'common/standard/menus/';
  protected const PATH_MENU_FOOTER      = 'common/standard/menus/';

  protected const NAME_MENU_HEADER      = 'menu_header';
  protected const NAME_MENU_UTILITY     = 'menu_utility';
  protected const NAME_MENU_BREADCRUMBS = 'menu_breadcrumbs';
  protected const NAME_MENU_CONTENT     = 'menu_content';
  protected const NAME_MENU_FOOTER      = 'menu_footer';

  protected const HANDLER_MENU_HEADER      = 'c_standard_menu_header';
  protected const HANDLER_MENU_UTILITY     = 'c_standard_menu_utility';
  protected const HANDLER_MENU_BREADCRUMBS = 'c_standard_menu_breadcrumbs';
  protected const HANDLER_MENU_CONTENT     = 'c_standard_menu_content';
  protected const HANDLER_MENU_FOOTER      = 'c_standard_menu_footer';

  protected const SCRIPT_EXTENSION = '.php';

  protected $html;
  protected $http;
  protected $database;
  protected $session;
  protected $settings;

  protected $languages;
  protected $language_alias;

  protected $text_type;
  protected $request_uri;
  protected $breadcrumbs;
  protected $arguments;
  protected $output_format;


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

    $this->languages      = array();
    $this->language_alias = NULL;

    $this->text_type     = NULL;
    $this->request_uri   = NULL;
    $this->breadcrumbs   = NULL;
    $this->arguments     = array();
    $this->output_format = c_base_mime::TYPE_TEXT_HTML;
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
    unset($this->language_alias);

    unset($this->text_type);
    unset($this->request_uri);
    unset($this->breadcrumbs);
    unset($this->arguments);
    unset($this->output_format);

    parent::__destruct();
  }

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    // to avoid recursion, breadcrumbs are initialized here instead of in set_parameters().
    $this->pr_build_breadcrumbs();

    return $executed;
  }

  /**
   * Implements set_parameters().
   */
  public function set_parameters(&$http, &$database, &$session, $settings) {
    $result = parent::set_parameters($http, $database, $session, $settings);
    if (c_base_return::s_has_error($result)) {
      return $result;
    }
    unset($result);

    $this->text_type = c_base_markup_tag::TYPE_SPAN;
    if (isset($this->settings['standards_issue-use_p_tags']) && $this->settings['standards_issue-use_p_tags']) {
      $this->text_type = c_base_markup_tag::TYPE_PARAGRAPH;
    }

    $this->languages = $this->http->get_response_content_language()->get_value_exact();
    if (!is_array($this->languages)) {
      $this->languages = array();
    }

    $this->pr_get_language_alias();

    return new c_base_return_true();
  }

  /**
   * Get the breadcrumb for this path.
   *
   * The breadcrumb will be built by this function if it is not already built.
   *
   * @return c_base_menu_item|c_base_return_null
   *   The breadcrumb menu is returned on success.
   *   If not defined, then NULL is returned.
   *   NULL with the error bit set is returned on error.
   */
  public function get_breadcrumbs() {
    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $this->pr_build_breadcrumbs();
    }

    if ($this->breadcrumbs instanceof c_base_menu_item) {
      return clone($this->breadcrumbs);
    }

    return new c_base_return_null();
  }

  /**
   * Return the current path parts after the specified path.
   *
   * This is intended for handling the path parts as arguments.
   * Processed path arguments are stored on the classes arguments variable.
   *
   * No sanitization is performed on these arguments.
   *
   * @param string $path_after
   *   The string to parse.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *   On error, arguments is assigned to an empty array.
   */
  protected function pr_process_path_arguments($path_after) {
    $path = $this->http->get_request_uri_relative($this->settings['base_path'])->get_value_exact();
    $path = preg_replace('@^' . $path_after . '(/|$)@i', '', $path);

    if (mb_strlen($path) == 0) {
      unset($path);
      $this->arguments = array();
      return FALSE;
    }

    $this->arguments = explode('/', $path);
    unset($path);

    return TRUE;
  }

  /**
   * Build the breadcrumb.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_build_breadcrumbs() {
    if (!is_object($this->path_tree)) {
      return new c_base_return_false();
    }

    $handler_settings = $this->path_tree->get_item_reset();
    if ($handler_settings instanceof c_base_return_false) {
      unset($handler_settings);
      return new c_base_return_false();
    }

    $this->breadcrumbs = new c_base_menu_item();

    // render the breadcrumbs for all appropriate paths as the default behavior.
    // this does not include the last path tree item because that item should be this class.
    $count = 0;
    $total = $this->path_tree->get_items_count() - 1;
    for (; $count < $total; $count++) {
      if ($handler_settings instanceof c_base_return_false) {
        $handler_settings = $this->path_tree->get_item_next();
        continue;
      }

      $handler_settings = $handler_settings->get_value();

      if (!isset($handler_settings['include_name']) || !is_string($handler_settings['include_name'])) {
        $handler_settings = $this->path_tree->get_item_next();
        continue;
      }

      if (!isset($handler_settings['include_directory']) || !is_string($handler_settings['include_directory'])) {
        $handler_settings = $this->path_tree->get_item_next();
        continue;
      }

      if (!isset($handler_settings['handler']) || !is_string($handler_settings['handler'])) {
        $handler_settings = $this->path_tree->get_item_next();
        continue;
      }

      require_once($handler_settings['include_directory'] . $handler_settings['include_name'] . static::SCRIPT_EXTENSION);


      $handler = NULL;
      if (is_string($this->language_alias)) {
        @include_once($handler_settings['include_directory'] . $this->language_alias . '/' . $handler_settings['include_name'] . static::SCRIPT_EXTENSION);

        $handler_class = $handler_settings['handler'] . '_' . $this->language_alias;
        if (class_exists($handler_class)) {
          $handler = new $handler_class();
        }
        unset($handler_class);
      }

      if (is_null($handler)) {
        if (class_exists($handler_settings['handler'])) {
          $handler = new $handler_settings['handler']();
        }
        else {
          unset($handler);
          $handler_settings = $this->path_tree->get_item_next();
          continue;
        }
      }

      $handler->set_parameters($this->http, $this->database, $this->session, $this->settings);
      $breadcrumbs = $handler->get_breadcrumbs();
      if ($breadcrumbs instanceof c_base_menu_item) {
        $breadcrumbs = $breadcrumbs->get_items()->get_value_exact();
        foreach ($breadcrumbs as $breadcrumb) {
          $this->breadcrumbs->set_item($breadcrumb);
        }
        unset($breadcrumb);
      }
      unset($breadcrumbs);
      unset($handler);
      unset($handler_settings);

      $handler_settings = $this->path_tree->get_item_next();
    }
    unset($count);
    unset($total);

    return new c_base_return_true();
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
   * @param string|array||null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_section($headers = NULL, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_SECTION, static::CSS_AS_SECTION);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SECTION, $id, $classes);
    unset($classes);

    if (is_array($headers)) {
      $header = $this->pr_create_tag_headers($headers, $arguments, NULL, static::CSS_AS_SECTION_HEADERS);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_wrapper($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_WRAPPER, static::CSS_AS_WRAPPER);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_break($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_BREAK, static::CSS_AS_BREAK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   * @param c_base_markup_tag|null $prepend
   *   If not NULL, then a markup tag to prepend inside of the header tag block.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_header($text, $header, $arguments = array(), $id = NULL, $extra_class = NULL, $prepend = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_HEADER, static::CSS_AS_HEADER, static::CSS_AS_HEADER . '-' . $header);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

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
    else {
      $type = c_base_markup_tag::TYPE_HX;
    }

    $tag = c_theme_html::s_create_tag($type, $id, $classes);

    if ($prepend instanceof c_base_markup_tag) {
      $tag->set_tag($prepend);
    }

    $tag_text = $this->pr_create_tag_text($text, $arguments, $id, static::CSS_AS_HEADER_TEXT);
    $tag->set_tag($tag_text);
    unset($tag_text);

    if ($header > 6) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'text-text-heading_' . ((int) $header));
    }

    return $tag;
  }

  /**
   * Creates the standard headers block.
   *
   * This is the HTML <header> tag and not the html <head> tag.
   *
   * @param array|null $headers
   *   An array of headers, whose keys are the header number and values are the header names.
   *   Header values may also be an array with the following structure:
   *   - prepend-outside: A c_base_markup_tag to prepend outside of the header tag, but inside the headers tag.
   *                      May be NULL for undefined.
   *   - append-outside: A c_base_markup_tag to append outside of the header tag, but inside the headers tag.
   *                     May be NULL for undefined.
   *   - prepend-inside: A c_base_markup_tag to prepend inside of the header tag, but inside the headers tag.
   *                     May be NULL for undefined.
   *   - append-inside: A c_base_markup_tag to append inside of the header tag, but inside the headers tag.
   *                    May be NULL for undefined.
   *   - text: The header name value string or integer.
   *   If NULL, then the headers are not assigned.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_headers($headers, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_HEADERS, static::CSS_AS_HEADERS);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_HEADER, $id, $classes);
    unset($classes);

    if (is_array($headers)) {
      foreach ($headers as $header_id => $header_text) {
        if (is_array($header_text)) {
          if (!isset($header_text['text'])) {
            continue;
          }

          if (isset($header_text['prepend-outside']) && $header_text['prepend-outside'] instanceof c_base_markup_tag) {
            $tag->set_tag($header_text['prepend-outside']);
          }

          if (isset($header_text['prepend-inside'])) {
            $header = $this->pr_create_tag_header($header_text['text'], $header_id, $arguments, NULL, NULL, $header_text['prepend-inside']);
          }
          else {
            $header = $this->pr_create_tag_header($header_text['text'], $header_id, $arguments);
          }

          if (isset($header_text['append-inside'])) {
            $header->set_tag($header_text['append-inside']);
          }

          $tag->set_tag($header);
          unset($header);

          if (isset($header_text['append-outside']) && $header_text['append-outside'] instanceof c_base_markup_tag) {
            $tag->set_tag($header_text['append-outside']);
          }

          continue;
        }

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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_PARAGRAPH, static::CSS_AS_PARAGRAPH);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_text_block($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TEXT_BLOCK, static::CSS_AS_TEXT_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT), $text);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_paragraph_block($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_PARAGRAPH_BLOCK, static::CSS_AS_PARAGRAPH_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT), $text);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_link($text, $tooltip, $destination, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_LINK, static::CSS_AS_LINK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (is_int($text)) {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_A, NULL, array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT), $this->pr_get_text($text, $arguments));
    }
    else {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_A, NULL, array($this->settings['base_css'] . static::CSS_AS_TEXT, static::CSS_AS_TEXT), $text);
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
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_link_block($text, $tooltip, $destination, $description, $arguments = array(), $header = 0, $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_LINK_BLOCK, static::CSS_AS_LINK_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if ($header < 1) {
        $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, array(static::CSS_AS_TEXT, static::CSS_AS_LINK_BLOCK_NAME));
      }
      else {
        $header_classes = array($this->settings['base_css'] . static::CSS_AS_HEADER, static::CSS_AS_HEADER, static::CSS_AS_HEADER . '-' . $header, static::CSS_AS_LINK_BLOCK_NAME);

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
        else {
          $type = c_base_markup_tag::TYPE_HX;
        }

        $wrapper = c_theme_html::s_create_tag($type, NULL, $header_classes);
        unset($header_classes);
        unset($type);

        if ($header > 6) {
          $wrapper->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, 'text-text-heading_' . ((int) $header));
        }
      }

      if (!is_null($text)) {
        $tag = $this->pr_create_tag_link($text, $tooltip, $destination, $arguments, static::CSS_AS_LINK_BLOCK_LINK);
        $wrapper->set_tag($tag);
        unset($tag);
      }

      $block->set_tag($wrapper);
      unset($wrapper);
    }

    if (!is_null($description)) {
      $tag = $this->pr_create_tag_text_block($description, $arguments, static::CSS_AS_LINK_BLOCK_DESCRIPTION);
      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Creates the standard fieldset block, with an optional fieldset legend.
   *
   * This does not define the fieldset body, which is left to be defined by the caller.
   * The fieldset body is generally assumed to be defined wih static::CSS_AS_FIELD_SET_CONTENT.
   *
   * @param int|string|null $text
   *   The text or the text code to use as the fieldset legend.
   *   If NULL, then no legend is generated..
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_fieldset($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_PARAGRAPH_BLOCK, static::CSS_AS_PARAGRAPH_BLOCK);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $block = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_FIELD_SET, $id, $classes);
    unset($classes);

    if (!is_null($text)) {
      if (is_int($text)) {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array($this->settings['base_css'] . static::CSS_AS_FIELD_SET_LEGEND, static::CSS_AS_FIELD_SET_LEGEND), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag = c_theme_html::s_create_tag($this->text_type, NULL, array($this->settings['base_css'] . static::CSS_AS_FIELD_SET_LEGEND, static::CSS_AS_FIELD_SET_LEGEND), $text);
      }

      $block->set_tag($tag);
      unset($tag);
    }

    return $block;
  }

  /**
   * Creates the standard "row".
   *
   * A row only has a single tag within it, generally called the value.
   *
   * @param string|null $value
   *   If not NULL, then is text used to be displayed as the field value or description.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   * @param int|null $row
   *   (optional) If not NULL, then is a row number to append as an additional class.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_row($value = NULL, $arguments = array(), $id = NULL, $extra_class = NULL, $row = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_ROW, static::CSS_AS_ROW);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    if (is_int($row)) {
      $classes[] = static::CSS_AS_ROW . '-' . $row;
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    $tag_text = $this->pr_create_tag_text($value, $arguments, $id, static::CSS_AS_ROW_VALUE);
    $tag->set_tag($tag_text);
    unset($tag_text);

    return $tag;
  }

  /**
   * Creates the standard "field row".
   *
   * A field row has a field name tag and a field value tag.
   *
   * @param string|null $field_name
   *   If not NULL, then is text used to be displayed as the field name or label.
   * @param string|null $field_value
   *   If not NULL, then is text used to be displayed as the field value or description.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   * @param int|null $row
   *   (optional) If not NULL, then is a row number to append as an additional class.
   * @param bool $spacer
   *   (optional) If TRUE, then a special spacing class string is added between field name and value.
   *   This is intended to provide a way to have spacing if CSS is not used (unthemed/raw page).
   *   If a theme is then used, it can then set the spacer tab to be not displayed.
   *   If FALSE, this spacer tag is omitted.
   *   @fixme: spacer was added experimentally and may or may not be used in the future depending on how practical it is.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_field_row($field_name = NULL, $field_value = NULL, $arguments = array(), $id = NULL, $extra_class = NULL, $row = NULL, $spacer = FALSE) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_FIELD_ROW, static::CSS_AS_FIELD_ROW);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    if (is_int($row)) {
      $classes[] = static::CSS_AS_ROW . '-' . $row;
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, $id, $classes);
    unset($classes);

    $tag_text = $this->pr_create_tag_text($field_name, $arguments, NULL, static::CSS_AS_FIELD_ROW_NAME);
    $tag->set_tag($tag_text);
    unset($tag_text);

    if ($spacer) {
      $this->pr_create_tag_spacer($tag);
    }

    $tag_text = $this->pr_create_tag_text($field_value, $arguments, NULL, static::CSS_AS_FIELD_ROW_VALUE);
    $tag->set_tag($tag_text);
    unset($tag_text);

    return $tag;
  }

  /**
   * Creates the standard "table".
   *
   * This does not create/add the colgroup, thead, tbody, tr, or tfoot tags.
   *
   * @param int|string|null $text
   *   The text or the text code to use as the table caption.
   *   If NULL, then no caption is generated..
   *   If adding additional markup, caption should be added manually.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE, static::CSS_AS_TABLE);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE, $id, $classes);

    if (!is_null($text)) {
      if (is_int($text)) {
        $tag_text = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_CAPTION, NULL, array($this->settings['base_css'] . static::CSS_AS_TABLE_CAPTION,  static::CSS_AS_TABLE_CAPTION), $this->pr_get_text($text, $arguments));
      }
      else {
        $tag_text = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_CAPTION, NULL, array($this->settings['base_css'] . static::CSS_AS_TABLE_CAPTION,  static::CSS_AS_TABLE_CAPTION), $text);
      }

      $tag->set_tag($tag_text);
      unset($tag_text);
    }

    return $tag;
  }

  /**
   * Creates the standard "table caption".
   *
   * @param int|string|null $text
   *   The text or the text code to use as the table caption.
   *   If NULL, then no text is added..
   *   If adding additional markup, caption should be added manually.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_caption($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_CAPTION, static::CSS_AS_TABLE_CAPTION);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    if (!is_null($text)) {
      if (is_int($text)) {
        return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_CAPTION, $id, $classes, $this->pr_get_text($text, $arguments));
      }

      return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_CAPTION, $id, $classes, $text);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_CAPTION, $id, $classes);
  }

  /**
   * Creates the standard "table column".
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_column($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_COLUMN, static::CSS_AS_TABLE_COLUMN);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_COLUMN, $id, $classes);
  }

  /**
   * Creates the standard "table column group".
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_column_group($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_COLUMN_GROUP, static::CSS_AS_TABLE_COLUMN_GROUP);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_COLUMN_GROUP, $id, $classes);
  }

  /**
   * Creates the standard "table header".
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_header($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_HEADER, static::CSS_AS_TABLE_HEADER);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_HEADER, $id, $classes);
  }

  /**
   * Creates the standard "table header cell".
   *
   * @param int|string|null $text
   *   The text or the text code to use as the table caption.
   *   If NULL, then no text is added..
   *   If adding additional markup, text should be added manually.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_header_cell($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_HEADER_CELL, static::CSS_AS_TABLE_HEADER_CELL);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    if (!is_null($text)) {
      if (is_int($text)) {
        return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_HEADER_CELL, $id, $classes, $this->pr_get_text($text, $arguments));
      }

      return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_HEADER_CELL, $id, $classes, $text);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_HEADER_CELL, $id, $classes);
  }

  /**
   * Creates the standard "table body".
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_body($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_BODY, static::CSS_AS_TABLE_BODY);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_BODY, $id, $classes);
  }

  /**
   * Creates the standard "table row".
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_row($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_ROW, static::CSS_AS_TABLE_ROW);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_ROW, $id, $classes);
  }

  /**
   * Creates the standard "table body".
   *
   * @param int|string|null $text
   *   The text or the text code to use as the table caption.
   *   If NULL, then no text is added..
   *   If adding additional markup, text should be added manually.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_cell($text, $arguments = array(), $id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_CELL, static::CSS_AS_TABLE_CELL);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    if (!is_null($text)) {
      if (is_int($text)) {
        return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_CELL, $id, $classes, $this->pr_get_text($text, $arguments));
      }

      return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_CELL, $id, $classes, $text);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_CELL, $id, $classes);
  }

  /**
   * Creates the standard "table body".
   *
   * @param string|null $id
   *   (optional) An ID attribute to assign.
   *   If NULL, then this is not assigned.
   * @param string|null $extra_class
   *   (optional) An additional css class to append to the wrapping block.
   *   May be an array of classes to append.
   *   If NULL, then this is not assigned.
   *
   * @return c_base_markup_tag
   *   The generated markup tag.
   */
  protected function pr_create_tag_table_footer($id = NULL, $extra_class = NULL) {
    $classes = array($this->settings['base_css'] . static::CSS_AS_TABLE_FOOTER, static::CSS_AS_TABLE_FOOTER);
    if (is_string($extra_class)) {
      $classes[] = $extra_class;
    }
    elseif (is_array($extra_class)) {
      foreach ($extra_class as $class) {
        $classes[] = $class;
      }
      unset($class);
    }

    return c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TABLE_FOOTER, $id, $classes);
  }

  /**
   * Create a new HTML markup class with default settings populated.
   *
   * @param bool $real_page
   *   (optional) A real page is a page where content is being provided.
   *   Examples of non-real pages are 404 pages.
   *   Certain headers and settings are discluded on non-real pages, such as canonical urls.
   * @param array $arguments_title
   *   (optional) An array of arguments to convert into text, passed to the title handling functions.
   * @param array $arguments_script
   *   (optional) An array of arguments to convert into text, passed to the script handling functions.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE with error bit set is returned on error.
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
  protected function pr_create_html($real_page = TRUE, $arguments_title = array(), $arguments_script = array()) {
    $this->html = new c_base_html();

    $this->pr_create_html_add_primary_ids();
    $this->pr_create_html_add_primary_classes();
    $this->pr_create_html_add_lanaguages();
    $this->pr_create_html_add_title($arguments_title);
    $this->pr_create_html_add_header_base();
    $this->pr_create_html_add_header_meta();

    if ($real_page) {
      // @todo: redesign these to accept the $request_uri array instead of trying to build them directly here.
      $this->pr_create_html_add_header_link_canonical();
      $this->pr_create_html_add_header_link_shortlink();
    }

    $this->pr_create_html_add_header_script($arguments_script);

    return new c_base_return_true();
  }

  /**
   * Create an HTML primary id attributes.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_primary_ids() {
    $id = $this->html->sanitize_css(static::CSS_SYSTEM_PREFIX . $this->settings['system_name'])->get_value_exact();
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
    $class[] = static::CSS_DATE_YEAR . $this->html->sanitize_css(date('Y', $instance))->get_value_exact();
    $class[] = static::CSS_DATE_MONTH . $this->html->sanitize_css(strtolower(date('F', $instance)))->get_value_exact();
    $class[] = static::CSS_DATE_WEEK_DAY . $this->html->sanitize_css(strtolower(date('l', $instance)))->get_value_exact();
    $class[] = static::CSS_DATE_DAY . $this->html->sanitize_css(date('d', $instance))->get_value_exact();
    $class[] = static::CSS_DATE_HOUR . $this->html->sanitize_css(date('H', $instance))->get_value_exact();
    $class[] = static::CSS_DATE_MINUTE . $this->html->sanitize_css(date('m', $instance))->get_value_exact();
    $class[] = static::CSS_DATE_SECOND . $this->html->sanitize_css(date('s', $instance))->get_value_exact();
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

        $class[] = static::CSS_PATH_PART . $delta . '-' . $this->html->sanitize_css($sanitized_part)->get_value_exact();
        $delta++;
      }
      unset($path_part);
      unset($sanitized_part);

      $class[] = static::CSS_PATH_FULL . $this->html->sanitize_css(substr($sanitized, 1))->get_value_exact();
      unset($sanitized);
    }
    unset($path_parts);

    $class[] = static::CSS_IS_CONTENT_TYPE;
    $class[] = static::CSS_IS_JAVASCRIPT_DISABLED;

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
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_title($arguments = array()) {
    $title = $this->pr_get_text_title($arguments);

    if (is_string($title)) {
      $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_TITLE);
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

      $href .= $this->request_uri['path'];

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
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @see: self::pr_create_html()
   */
  protected function pr_create_html_add_header_script($arguments = array()) {
    // provide a custom javascript for detecting if javascript is enabled and storing in a css class name.
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SCRIPT, 'f_standard_paths_hmtl_javascript_detection');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_TYPE, c_base_mime::TYPE_TEXT_JS);

    $javascript = 'function f_standard_paths_hmtl_javascript_detection() {';
    $javascript .= 'document.body.removeAttribute(\'onLoad\');';
    $javascript .= 'document.body.className = document.body.className.replace(/\b' . static::CSS_IS_JAVASCRIPT_DISABLED . '\b/i, \'' . static::CSS_IS_JAVASCRIPT_ENABLED . '\');';
    $javascript .= '}';
    $tag->set_text($javascript);
    unset($javascript);

    $this->html->set_header($tag);
    $this->html->set_attribute_body(c_base_markup_attributes::ATTRIBUTE_ON_LOAD, 'f_standard_paths_hmtl_javascript_detection();');
    unset($tag);
  }

  /**
   * Appends a spacer to the specified tag.
   *
   * This is provided so that languages that do not use spacing may override this accordingly.
   *
   * @param c_base_markup_tag &$tag
   *   The markup tag to assign the spacer to.
   */
  protected function pr_create_tag_spacer(&$tag) {
    $tag_text = $this->pr_create_tag_text(' ', array(), NULL, static::CSS_AS_SPACER);
    $tag->set_tag($tag_text);
    unset($tag_text);
  }

  /**
   * Add all menus to the page.
   */
  protected function pr_add_menus() {
    $menu = $this->pr_build_menu_header($this->http, $this->database, $this->session, $this->settings);
    if ($menu instanceof c_base_markup_tag) {
      $this->html->set_tag($menu);
    }
    unset($menu);

    $menu = $this->pr_build_menu_utility($this->http, $this->database, $this->session, $this->settings);
    if ($menu instanceof c_base_markup_tag) {
      $this->html->set_tag($menu);
    }
    unset($menu);

    $menu = $this->pr_build_menu_breadcrumbs($this->http, $this->database, $this->session, $this->settings);
    if ($menu instanceof c_base_markup_tag) {
      $this->html->set_tag($menu);
    }
    unset($menu);

    $menu = $this->pr_build_menu_content($this->http, $this->database, $this->session, $this->settings);
    if ($menu instanceof c_base_markup_tag) {
      $this->html->set_tag($menu);
    }
    unset($menu);

    $menu = $this->pr_build_menu_footer($this->http, $this->database, $this->session, $this->settings);
    if ($menu instanceof c_base_markup_tag) {
      $this->html->set_tag($menu);
    }
    unset($menu);
  }

  /**
   * Load and return the header menu handler.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A built menu markup tag.
   *   FALSE without error bit set is returned if no menu was built.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_build_menu_header() {
    $menu = $this->pr_include_path(static::PATH_MENU_HEADER, static::NAME_MENU_HEADER, static::HANDLER_MENU_HEADER);
    return $menu->do_build($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Load and return the utility menu handler.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A built menu markup tag.
   *   FALSE without error bit set is returned if no menu was built.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_build_menu_utility(&$http, &$database, &$session, $settings) {
    $menu = $this->pr_include_path(static::PATH_MENU_UTILITY, static::NAME_MENU_UTILITY, static::HANDLER_MENU_UTILITY);
    return $menu->do_build($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Load and return the breadcrumbs menu handler.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A built menu markup tag.
   *   FALSE without error bit set is returned if no menu was built.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_build_menu_breadcrumbs(&$http, &$database, &$session, $settings) {
    $menu = $this->pr_include_path(static::PATH_MENU_BREADCRUMBS, static::NAME_MENU_BREADCRUMBS, static::HANDLER_MENU_BREADCRUMBS);
    return $menu->do_build($http, $database, $session, $settings, $this->breadcrumbs);
  }

  /**
   * Load and return the content menu handler.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A built menu markup tag.
   *   FALSE without error bit set is returned if no menu was built.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_build_menu_content(&$http, &$database, &$session, $settings) {
    $menu = $this->pr_include_path(static::PATH_MENU_CONTENT, static::NAME_MENU_CONTENT, static::HANDLER_MENU_CONTENT);
    return $menu->do_build($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Load and return the footer menu handler.
   *
   * @param c_base_http &$http
   *   The entire HTTP information to allow for the execution to access anything that is necessary.
   * @param c_base_database &$database
   *   The database object, which is usually used by form and ajax paths.
   * @param c_base_session &$session
   *   The current session.
   * @param array $settings
   *   An array of additional settings that are usually site-specific.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   A built menu markup tag.
   *   FALSE without error bit set is returned if no menu was built.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_build_menu_footer(&$http, &$database, &$session, $settings) {
    $menu = $this->pr_include_path(static::PATH_MENU_FOOTER, static::NAME_MENU_FOOTER, static::HANDLER_MENU_FOOTER);
    return $menu->do_build($this->http, $this->database, $this->session, $this->settings);
  }

  /**
   * Create a single breadcrumbs item.
   *
   * @param string $text
   *   The text assigned to the breadcrumbs item.
   * @param string|array|null $uri
   *   (optional) The URI string or array the breadcrumb points to.
   *   If NULL, then the uri is not assigned.
   *
   * @return c_base_menu_item
   *   The generated breadcrumb menu item.
   *   A generated menu item with the error bit set is returned on error.
   */
  protected function pr_create_breadcrumbs_item($text, $uri = NULL) {
    $item = new c_base_menu_item();
    $item->set_text($text);

    if (!is_null($uri)) {
      $item->set_uri($uri);
    }

    return $item;
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
  protected function pr_get_text_title($arguments = array()) {
    return NULL;
  }

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   *
   * @return string
   *   A string is returned as the custom text.
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }

  /**
   * Load and save the current preferred language alias.
   */
  protected function pr_get_language_alias() {
    $aliases = array();
    if (is_array($this->languages) && !empty($this->languages)) {
      $language = reset($this->languages);

      // us-english is the default, so do not attempt to include any external files.
      if ($language === i_base_languages::ENGLISH_US || $language === i_base_languages::ENGLISH) {
        unset($language);
        unset($aliases);

        $this->language_alias = NULL;
        return;
      }

      $aliases = c_base_defaults_global::s_get_languages()::s_get_aliases_by_id($language)->get_value_exact();
    }
    unset($language);

    // use default if no aliases are found.
    if (empty($aliases)) {
      unset($aliases);

      $this->language_alias = NULL;
      return;
    }

    $this->language_alias = end($aliases);
  }

  /**
   * Will include a custom language path if one exists.
   *
   * The default language files ends in "${path}${name}.php".
   * All other language files end in "${path}${language_alias}/${name}.php".
   *
   * The default class is the provided class name.
   * All other languages use the provided class name with '_${language_alias}' appended.
   *
   * For example (using path='my_file'), us english is the default, so that would load the file 'my_file.php'.
   *                                     japanese language load the file 'my_file-ja.php'.
   *
   * @param string $path
   *   The path to the include file, without the file name.
   * @param string $name
   *   The file name of the PHP file, without the '.php' extension.
   * @param string $class
   *   The name of the class, that is an instance of c_base_menu, to execute.
   *
   * @return c_base_meni
   *   The created c_base_meni object.
   */
  protected function pr_include_path($path, $name, $class) {
    require_once($path . $name . static::SCRIPT_EXTENSION);

    // use default if no aliases are found.
    if (is_null($this->language_alias)) {
      return new $class();
    }

    // use include_once instead of require_require to allow for failsafe behavior.
    @include_once($path . $this->language_alias . '/' . $name . static::SCRIPT_EXTENSION);

    $language_class = $class . '_' . $this->language_alias;
    if (class_exists($language_class)) {
      return new $language_class();
    }
    unset($language_class);

    // if unable to find, fallback to original class
    return new $class();
  }
}

