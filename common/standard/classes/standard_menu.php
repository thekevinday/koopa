<?php
/**
 * @file
 * Provides a class for managing the menus.
 */
namespace n_koopa;

require_once('common/base/classes/base_menu.php');

/**
 * A generic class for managing a menu.
 *
 * This can be converted to HTML <nav>, <menu>, or even breadcrumbs.
 */
class c_standard_menu extends c_base_menu {
  const CSS_MENU              = 'menu';
  const CSS_MENU_HEADER       = 'menu-header';
  const CSS_MENU_HEADER_TEXT  = 'menu-header-text';
  const CSS_MENU_ITEM         = 'menu_item';
  const CSS_MENU_ITEM_CONTENT = 'menu_item-content';

  const CSS_ITEM_LABEL  = 'item_type-label';
  const CSS_ITEM_LINK   = 'item_type-link';
  const CSS_ITEM_LOCAL  = 'item_type-local';
  const CSS_ITEM_REMOTE = 'item_type-remote';
  const CSS_ITEM_MENU   = 'item_type-menu';

  const CSS_TEXT_HEADING  = 'text-heading_';
  const CSS_TEXT_HEADINGS = 'text-headings';

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
   * Create a new menu HTML tag.
   *
   * @param string|null $name_machine
   *   (optional) The id of the navigation menu.
   *   Set to NULL to not create an id tag.
   * @param string|null $name_human
   *   (optional) The header name to use.
   *   Set to NULL to not create a header.
   * @param int $depth
   *   (optional) The number representing the header tag depth.
   *   This starts at 1.
   *   Anything beyond 6 is a simulated header.
   *   This is ignored when $name is NULL.
   *
   * @return c_base_markup_tag
   *   The created markup tag.
   *   The created markup tag with error bit set on error.
   */
  protected function pr_create_html_create_menu($name_machine = NULL, $name_human = NULL, $depth = 1) {
    $menu = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_NAVIGATION, $name_machine, [static::CSS_MENU]);

    if (is_string($name_human)) {
      $wrapper = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, [static::CSS_MENU_HEADER, static::CSS_MENU_HEADER . '-' . $depth]);

      if ($depth == 1) {
        $type = c_base_markup_tag::TYPE_H1;
      }
      elseif ($depth == 2) {
        $type = c_base_markup_tag::TYPE_H2;
      }
      elseif ($depth == 3) {
        $type = c_base_markup_tag::TYPE_H3;
      }
      elseif ($depth == 4) {
        $type = c_base_markup_tag::TYPE_H4;
      }
      elseif ($depth == 5) {
        $type = c_base_markup_tag::TYPE_H5;
      }
      elseif ($depth == 6) {
        $type = c_base_markup_tag::TYPE_H6;
      }
      else {
        $type = c_base_markup_tag::TYPE_HX;
      }

      $header = c_theme_html::s_create_tag($type, NULL, [static::CSS_MENU_HEADER_TEXT, static::CSS_MENU_HEADER_TEXT . '-' . $depth]);
      unset($type);

      if ($depth > 6) {
        $header->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CSS_TEXT_HEADING . ((int) $depth));
      }

      $header->set_text($name_human);

      $wrapper->set_tag($header);
      $wrapper->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CSS_TEXT_HEADINGS);
      unset($header);

      $menu->set_tag($wrapper);
      unset($wrapper);
    }

    return $menu;
  }

  /**
   * Create an HTML menu tag.
   *
   * @param string $label
   *   A string to display as the menu item name.
   * @param string|null $tooltip
   *   (optional) An tooltip string to assign to the label.
   *   Set to NULL to not assign a tooltip.
   *
   * @return c_base_markup_tag
   *   Markup tag on success.
   *   Markup tag with error bit set on error.
   */
  protected function pr_create_html_add_menu_item_label($label, $tooltip = NULL) {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, [static::CSS_MENU_ITEM, static::CSS_ITEM_LABEL]);

    $tag_content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SPAN, NULL, [static::CSS_MENU_ITEM_CONTENT]);
    $tag_content->set_text($label);

    if (is_string($tooltip)) {
      $tag_content->set_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE, $tooltip);
    }

    $tag->set_tag($tag_content);
    unset($tag_content);

    return $tag;
  }

  /**
   * Create an HTML menu tag.
   *
   * @param string $label
   *   A string to display as the menu item name.
   * @param string $uri
   *   A fully processed uri string.
   * @param string|null $tooltip
   *   (optional) An tooltip string to assign to the link.
   *   Set to NULL to not assign a tooltip.
   * @param bool $local
   *   (optional) If TRUE, then uri is a local uri.
   *   If FALSE, then uri is a remote uri.
   *
   * @return c_base_markup_tag
   *   Markup tag on success.
   *   Markup tag with error bit set on error.
   */
  protected function pr_create_html_add_menu_item_link($label, $uri, $tooltip = NULL, $local = TRUE) {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, [static::CSS_MENU_ITEM, static::CSS_ITEM_LINK]);

    if ($local) {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CSS_ITEM_LOCAL);
    }
    else {
      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CSS_ITEM_REMOTE);
    }

    $tag_content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_A, NULL, [static::CSS_MENU_ITEM_CONTENT]);

    $tag_content->set_text($label);
    $tag_content->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $uri);

    if (is_string($tooltip)) {
      $tag_content->set_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE, $tooltip);
    }

    $tag->set_tag($tag_content);
    unset($tag_content);

    return $tag;
  }

  /**
   * Create an HTML menu tag.
   *
   * @param string $label
   *   A string to display as the menu item name.
   * @param c_base_markup_tag $menu
   *   Another pre-build menu.
   * @param string|null $tooltip
   *   (optional) An tooltip string to assign to the link.
   *   Set to NULL to not assign a tooltip.
   *
   * @return c_base_markup_tag
   *   Markup tag on success.
   *   Markup tag with error bit set on error.
   */
  protected function pr_create_html_add_menu_item_menu($label, $menu, $tooltip = NULL) {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, NULL, [static::CSS_MENU_ITEM, static::CSS_ITEM_MENU]);

    $tag->set_text($label);

    $tag_content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_SPAN, NULL, [static::CSS_MENU_ITEM_CONTENT]);
    $tag_content->set_tag($menu);

    if (is_string($tooltip)) {
      $tag_content->set_attribute(c_base_markup_attributes::ATTRIBUTE_TITLE, $tooltip);
    }

    $tag->set_tag($tag_content);
    unset($tag_content);

    return $tag;
  }

  /**
   * Load text for a supported language.
   *
   * @param int $index
   *   A number representing which block of text to return.
   * @param array $arguments
   *   (optional) An array of arguments to convert into text.
   */
  protected function pr_get_text($code, $arguments = []) {
    return '';
  }

  /**
   * Replace all occurences of arguments within string.
   *
   * @todo: should this be moved into some string class as a static function?
   *
   * Perform sanitization based on the first character.
   * If first character is ':', do not perform sanitization.
   * If first character is '@', santize as HTML text.
   *
   * I recommend wrapping placeholders in '{' and '}' to help enforce uniqueness.
   * - For example the string ':words' could be confused with two different placeholders: ':word' and ':words'.
   * - By using ':{words}' and ':{word}', there there should be fewer chances of mixups.
   *
   * @param string &$string
   *   The string to perform replacements on.
   * @param array $arguments
   *   An array of replacement arguments.
   *
   * @see: htmlspecialchars()
   * @see: str_replace()
   */
  protected function pr_process_replacements(&$string, $arguments) {
    foreach ($arguments as $place_holder => $replacement) {
      $type = mb_substr($place_holder, 0, 1);

      if ($type == ':') {
        $sanitized = $replacement;
      }
      elseif ($type == '@') {
        $sanitized = htmlspecialchars($replacement, $this->sanitize_html['flags'], $this->sanitize_html['encoding']);
      }
      else {
        unset($type);

        // do not perform replacements on unknown placeholders.
        continue;
      }
      unset($type);

      $string = str_replace($place_holder, $sanitized, $string);
    }
    unset($place_holder);
    unset($replacement);
  }
}
