<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * Provide the sql USING functionality.
 */
trait t_database_using {
  protected $using;

  /**
   * Set the USING settings.
   *
   * @param string|null $using
   *   The using to use.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_using($using) {
    if (is_null($using)) {
      $this->using = NULL;
      return new c_base_return_true();
    }

    if (is_string($using)) {
      $placeholder = $this->add_placeholder($using);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->using = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'using', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned using.
   *
   * @return i_database_query_placeholder|c_base_return_null
   *   A using query placeholder on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_using() {
    if (is_null($this->using)) {
      return new c_base_return_null();
    }

    if (isset($this->using)) {
      return clone($this->using);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'using', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned on success.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_using() {
    return strval($this->using);
  }
}
