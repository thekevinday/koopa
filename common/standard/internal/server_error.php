<?php
/**
 * @file
 * Provides path handler for the server rror pages.
 */
namespace n_koopa;

require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_http_status.php');

require_once('common/standard/classes/standard_path_exception.php');

require_once('common/theme/classes/theme_html.php');

class c_standard_path_server_error extends c_standard_path_exception {

  /**
   * Implements do_execute().
   */
  public function do_execute(&$http, &$database, &$session, $settings = []) {
    // the parent function performs validation on the parameters.
    $executed = parent::do_execute($http, $database, $session, $settings);
    if (c_base_return::s_has_error($executed)) {
      return $executed;
    }

    $wrapper = $this->pr_create_tag_section(array(1 => 0));
    $wrapper->set_tag($this->pr_create_tag_text_block(1));


    // append any error messages to the page.
    $errors = $this->session->get_error();
    if (is_array($errors) && !empty($errors)) {
      $error_wrapper = $this->pr_create_tag_wrapper('error-block');
      $error_wrapper->set_tag($this->pr_create_tag_header(2, 2, 'error-block-title'));

      $error_list = $this->pr_create_tag_list();
      foreach ($errors as $error) {
        $error_text = $this->pr_get_error_text($error);
        if (!($error_text instanceof c_base_return_string)) {
          unset($error_text);
          continue;
        }

        $text = $this->pr_create_tag_text($error_text->get_value_exact());
        unset($error_text);

        $list_item = $this->pr_create_tag_list_item();
        $list_item->set_tag($text);
        unset($text);

        $error_list->set_tag($list_item);
        unset($list_item);
      }
      unset($error);

      $error_wrapper->set_tag($error_list);
      unset($error_list);

      $wrapper->set_tag($error_wrapper);
      unset($error_wrapper);
    }


    // initialize the content as HTML.
    $this->pr_create_html(FALSE);
    $this->html->set_tag($wrapper);
    unset($wrapper);

    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);


    // assign HTTP response status.
    $http->set_response_status(c_base_http_status::INTERNAL_SERVER_ERROR);


    return $executed;
  }

  /**
   * Implements pr_get_text_title().
   */
  protected function pr_get_text_title($arguments = []) {
    return $this->pr_get_text(0, $arguments);
  }

  /**
   * Implements pr_get_text_breadcrumbs().
   */
  protected function pr_get_text_breadcrumbs($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = '';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        $string = 'Server Error';
        break;
      case 1:
        $string = 'Something went wrong while processing your request, please try again later.';
        break;
      case 2:
        $string = 'Error Messages';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }
}
