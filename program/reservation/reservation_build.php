<?php
/**
 * @file
 * Provides reservation build functions beyond what is found in index.php.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_html.php');

require_once('common/theme/classes/theme_html.php');

class c_reservation_build {
  /**
   * Create a new HTML markup class with default settings populated.
   *
   * @param c_base_http &$http
   *   Http object.
   * @param c_base_database &$databbase
   *   The database object.
   * @param c_base_session &$session
   *   Session information.
   * @param array $settings
   *   System settings
   * @param string|null $title
   *   (optional) A string to be used as the page title header.
   *   Set to NULL to use default language.
   *
   * @return c_base_html
   *   The generated html is returned on success.
   *   The generated html with error bit set is returned on error.
   */
  public static function s_create_html(&$http, &$database, &$session, $settings, $title = NULL) {
    if (!is_string($title)) {
      $title = NULL;
    }

    $html = new c_base_html();

    // assign class attribute
    $class = array(
      'reservation',
      'no-script',
      'is-html5',
    );

    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, $class);
    unset($class);


    // assign id attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_ID, 'reservation-system');


    // assign default language attribute.
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_LANGUAGE, i_base_language::ENGLISH_US);


    // assign default direction attribute
    $html->set_attribute(c_base_markup_attributes::ATTRIBUTE_DIRECTION, 'ltr');


    // assign title header tag (setting title tag at delta 0 so that it can easily be overriden as needed).
    $tag = new c_base_markup_tag();
    $tag->set_type(c_base_markup_tag::TYPE_TITLE);

    if (is_string($title)) {
      $tag->set_text($title);
    }
    else {
      $tag->set_text('Reservation System');
    }

    $html->set_header($tag, 0);
    unset($tag);


    // assign base header tag
    if (isset($settings['base_path']) && is_string($settings['base_path']) && mb_strlen($settings['base_scheme']) > 0) {
      $href = '';
      if (isset($settings['base_scheme']) && is_string($settings['base_scheme']) && mb_strlen($settings['base_scheme']) > 0) {
        if (isset($settings['base_host']) && is_string($settings['base_host']) && mb_strlen($settings['base_host']) > 0) {
          $href .= $settings['base_scheme'] . '://' . $settings['base_host'];
        }
      }

      $href .= $settings['base_path'];

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
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, 'http://localhost/');
    #$html->set_header($tag);
    #unset($tag);


    // assign shortlink header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'shortlink');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, '/');
    #$html->set_header($tag);
    #unset($tag);


    // assign description header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'description');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'A reservation/scheduling system.');
    $html->set_header($tag);
    unset($tag);


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


    // assign expires header tag
    #$tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV, 'expires');
    #$tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, c_base_defaults_global::s_get_date('r', strtotime('+30 minutes'))->get_value_exact());
    #$html->set_header($tag);
    #unset($tag);


    // assign viewport header tag
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_META);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_NAME, 'viewport');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CONTENT, 'width=device-width, initial-scale=1');
    $html->set_header($tag);
    unset($tag);


    // assign content http-equiv header tag
    $aliases = array();
    $languages = $http->get_response_content_language()->get_value_exact();
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

    return $html;
  }
}
