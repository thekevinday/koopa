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

require_once('common/database/classes/database_string.php');

/**
 * Provide the sql INHERIT functionality.
 */
trait t_database_inherit {
  protected $inherit;

  /**
   * Set the INHERIT settings.
   *
   * @param string|null $name
   *   The table name to inherit from.
   *   Set to NULL to disable.
   * @param bool $inherit
   *   Set to TRUE for INHERIT an FALSE for NO INHERIT.
   *   This is ignored when $name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_inherit($name, $inherit = TRUE) {
    if (is_null($name)) {
      $this->name = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'inherit', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->inherit = [
      'name' => $name,
      'inherit' => $inherit,
    ];
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned inherit status.
   *
   * @return c_base_return_bool|c_base_return_null
   *   TRUE for INHERIT or FALSE for NO INHERIT on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_inherit() {
    if (is_null($this->inherit)) {
      return new c_base_return_null();
    }

    if (is_bool($this->inherit['inherit'])) {
      return c_base_return_bool::s_new($this->inherit['inherit']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'inherit[inherit]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned name to inherit from.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_inherit_name() {
    if (is_null($this->inherit)) {
      return new c_base_return_null();
    }

    if (is_string($this->inherit['name'])) {
      return c_base_return_string::s_new($this->inherit['name']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'inherit[name]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_inherit() {
    if (is_null($this->inherit)) {
      return NULL;
    }

    $value = $this->inherit['inherit'] ? c_database_string::INHERIT : c_database_string::NO_INHERIT;
    $value .= ' ' . $this->inherit['name'];
    return $value;
  }
}
