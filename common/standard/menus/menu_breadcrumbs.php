<?php
/**
 * @file
 * Provides a class for a standard breadcrumbs menu.
 *
 * This is a menu that shows a navigation history on how to get to the current page via links.
 * This does not have to be a link to every path in the current url.
 */
require_once('common/base/classes/base_markup.php');

require_once('common/standard/classes/standard_menu.php');
require_once('common/standard/classes/standard_paths.php');

/**
 * A generic class for managing a breadcrumb menu.
 */
class c_standard_menu_breadcrumbs extends c_standard_menu {
  protected const CLASS_NAME = 'menu-breadcrumb';
  protected const CLASS_ITEM = 'as-breadcrumbs-item';

  /**
   * Implements do_build();
   */
  public function do_build(&$http, &$database, &$session, $settings, $items = NULL) {
    $built = parent::do_build($http, $database, $session, $settings);
    if (c_base_return::s_has_error($built)) {
      return $built;
    }
    unset($built);

    // if no items are available, then do nothing.
    if (!($items instanceof c_base_menu_item)) {
      return new c_base_return_false();
    }

    $menu = $this->pr_create_html_create_menu($settings['base_css'] . static::CLASS_NAME, $this->pr_get_text(0));
    foreach ($items->get_items()->get_value_exact() as $item) {
      if (!($item instanceof c_base_menu_item)) {
        continue;
      }

      $item_text = $item->get_text()->get_value_exact();
      $item_uri = $item->get_uri()->get_value();

      if (is_string($item_uri) || is_array($item_uri)) {
        // @fixme: handle if $item_uri is an array.
        $tag = $this->pr_create_html_add_menu_item_link($item_text, $settings['base_path'] . $item_uri);
      }
      else {
        $tag = $this->pr_create_html_add_menu_item_label($item_text);
      }
      unset($item_uri);
      unset($item_text);

      $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_CLASS, static::CLASS_ITEM);

      $item_attributes = $item->get_attributes()->get_value_exact();
      if (is_array($item_attributes) && !empty($item_attributes)) {
        foreach ($item_attributes as $attribute_key => $attribute_value) {
          $tag->set_attribute($attribute_key, $attribute_value);
        }
        unset($attribute_key);
        unset($attribute_value);
      }
      unset($item_attributes);

      $menu->set_tag($tag);
      unset($tag);
    }
    unset($item);

    return $menu;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Breadcrumb Menu';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
