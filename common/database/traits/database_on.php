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

require_once('common/database/classes/database_string.php');

require_once('common/database/enumerations/database_on.php');

require_once('common/database/enumerations/database_on.php');

/**
 * Provide the sql ON functionality.
 */
trait t_database_on {
  protected $on;

  /**
   * Assigns the SQL ON operation.
   *
   * @param int|null $on
   *   Whether or not to use the ON operation in the query.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_on($on) {
    if (is_null($on)) {
      $this->on = NULL;
      return new c_base_return_true();
    }

    switch ($on) {
      case e_database_on::TABLES_TO:
      case e_database_on::SEQUENCES:
      case e_database_on::FUNCTIONS:
      case e_database_on::TYPES:
      case e_database_on::SCHEMAS:
        $this->on = $on;
        return new c_base_return_true();
      default:
        break;
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'on', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the ON operation status.
   *
   * @return c_base_return_int|c_base_return_null
   *   Integer representing the on operation is returned on success.
   *   NULL is returned if undefined.
   *   FALSE with error bit set is returned on error.
   */
  protected function get_on() {
    if (is_null($this->on)) {
      return new c_base_return_null();
    }

    return c_base_return_int::s_new($this->on);
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
  protected function p_do_build_on() {
    $value = NULL;
    switch($this->on) {
      case e_database_on::TABLES_TO:
        $value .= c_database_string::ON_TABLES_TO;
        break;
      case e_database_on::SEQUENCES:
        $value .= c_database_string::ON_SEQUENCES;
        break;
      case e_database_on::FUNCTIONS:
        $value .= c_database_string::ON_FUNCTIONS;
        break;
      case e_database_on::TYPES:
        $value .= c_database_string::ON_TYPES;
        break;
      case e_database_on::SCHEMAS:
        $value .= c_database_string::ON_SCHEMAS;
        break;
    }

    return $value;
  }
}
