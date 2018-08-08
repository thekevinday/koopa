<?php
/**
 * @file
 * Provides traits for specific Postgesql Queries.
 *
 * These traits are associated with actions.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * Provide option support for an SQL query.
 */
trait t_database_option {
  protected $query_option;

  /**
   * Assigns this query option.
   *
   * @param int|null $option
   *   Whether or not to use a query option, such as .
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_query_option($option) {
    if (is_null($grant)) {
      $this->query_option = NULL;
      return new c_base_return_true();
    }

    if (is_int($option)) {
      $this->query_option = $option;
      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned option.
   *
   * @return c_base_return_int|c_base_return_null
   *   Integer representing the option is returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_option() {
    if (is_null($this->query_option)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->query_option);
  }
}
