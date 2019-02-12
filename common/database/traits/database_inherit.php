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

/**
 * Provide the sql INHERIT functionality.
 */
trait t_database_inherit {
  protected $inherit;

  /**
   * Assign the settings.
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

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $this->inherit = [
      'name' => $placeholder,
      'inherit' => $inherit,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned inherit settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the inherit settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_inherit() {
    if (is_null($this->inherit)) {
      return new c_base_return_null();
    }

    if (is_array($this->inherit)) {
      return c_base_return_array::s_new($this->inherit);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'inherit', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Perform the common build process for this trait.
   *
   * As an internal trait method, the caller is expected to perform any appropriate validation.
   *
   * @return string|null
   *   A string is returned.
   *   NULL is returned if there is nothing to process or there is an error.
   */
  protected function p_do_build_inherit() {
    $value = $this->inherit['inherit'] ? c_database_string::INHERIT : c_database_string::NO . ' ' . c_database_string::INHERIT;
    $value .= ' ' . $this->inherit['name'];
    return $value;
  }
}
