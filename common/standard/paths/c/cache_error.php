<?php
/**
 * @file
 * Provides path handler for the cached error pages.
 *
 * This could be used to load, alter, and sign (such as via PGP/GPG) a static file before output.
 * Such a use is not truly static, but it can be used for some amount of optimization.
 */

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');

require_once('common/standard/classes/standard_path.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for logs administration.
 *
 * This listens on: /c/error
 */
class c_standard_path_cache_error extends c_standard_path {
  public const PATH_SELF = 'c/error';

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = array()) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    if (!$this->pr_process_arguments($executed)) {
      return $executed;
    }

    // only support HTML output unless otherwise needed.
    // @todo: eventually all HTML output will be expected to support at least print and PDF formats (with print being the string 'print').
    if ($this->output_format !== c_base_mime::TYPE_TEXT_HTML) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
      $executed->set_error($error);
      unset($error);

      return $executed;
    }

    // @todo: this function needs to check to see if the user has administer (or manager?) roles (c_base_roles::MANAGER, c_base_roles::ADMINISTER) and if they do, set administrative to TRUE when calling do_load().
    #$user = $this->session->get_user_current();
    #$roles_current = $user->get_roles()->get_value_exact();

    // @todo: this function is currently disabled, so return a path not found.
    $error = c_base_error::s_log(NULL, array('arguments' => array(':{path_name}' => static::PATH_SELF . '/' . implode('/', $this->arguments), ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_PATH);
    $executed->set_error($error);
    unset($error);

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
