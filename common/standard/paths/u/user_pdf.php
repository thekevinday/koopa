<?php
/**
 * @file
 * Provides path handler for the user dashboard.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');
require_once('common/standard/classes/standard_path_user.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for user in pdf format.
 *
 * @fixme: this should probably be renamed to c_standard_path_user_view_pdf.
 *
 * This listens on: /u/pdf
 */
class c_standard_path_user_pdf extends c_standard_path_user_view {
  public const PATH_SELF = 'u/pdf'; // @fixme: make path 'u/pdf/view'.

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $wrapper = $this->pr_create_tag_section(array(1 => 0));

    // initialize the content as HTML.
    $this->pr_create_html();
    $this->html->set_tag($wrapper);
    unset($wrapper);

    $executed->set_output($this->html);
    unset($this->html);

    return $executed;
  }

  /**
   * Implementation of pr_create_html_add_header_link_canonical().
   */
  protected function pr_create_html_add_header_link_canonical() {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . self::PATH_SELF);
    $this->html->set_header($tag);

    unset($tag);
  }
}
