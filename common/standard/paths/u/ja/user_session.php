<?php
/**
 * @file
 * Provides path handler for the user session actions.
 */
namespace n_koopa;

/**
 * Implements c_standard_path_user_session().
 */
class c_standard_path_user_session_ja extends c_standard_path_user_session {

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = array()) {
    return '';
  }
}
