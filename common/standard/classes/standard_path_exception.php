<?php
/**
 * @file
 * Provides the standard path handling for exceptional cases.
 */
namespace n_koopa;

require_once('common/standard/classes/standard_path.php');

/**
 * Provides standard extensions to base paths specific to exceptional cases, such as errors.
 */
class c_standard_path_exception extends c_standard_path {

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    if (!is_object($this->path_tree)) {
      return new c_base_return_false();
    }

    $this->breadcrumbs = new c_base_menu_item();

    // exceptional pages do not exist at any path, so call the last item at the specified path and use its breadcrumb.
    $handler_settings = $this->path_tree->get_item_end();
    $count = 0;
    $total = $this->path_tree->get_items_count();
    for (; $count < $total; $count++) {
      if ($handler_settings instanceof c_base_return_false) {
        unset($handler_settings);
        $handler_settings = $this->path_tree->get_item_prev();
        continue;
      }

      $handler_settings = $handler_settings->get_value();

      if (!isset($handler_settings['include_name']) || !is_string($handler_settings['include_name'])) {
        unset($handler_settings);
        $handler_settings = $this->path_tree->get_item_prev();
        continue;
      }

      if (!isset($handler_settings['include_directory']) || !is_string($handler_settings['include_directory'])) {
        unset($handler_settings);
        $handler_settings = $this->path_tree->get_item_prev();
        continue;
      }

      if (!isset($handler_settings['handler']) || !is_string($handler_settings['handler'])) {
        unset($handler_settings);
        $handler_settings = $this->path_tree->get_item_prev();
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
          unset($handler_settings);
          $handler_settings = $this->path_tree->get_item_prev();
          continue;
        }
      }

      $handler->set_parameters($this->http, $this->database, $this->session, $this->settings);
      $handler->set_path_tree($this->path_tree);
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

      break;
    }

    return new c_base_return_true();
  }
}
