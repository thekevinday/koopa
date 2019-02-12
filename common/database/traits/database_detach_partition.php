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
 * Provide the sql ATTACH PARTITION functionality.
 */
trait t_database_detach_partition {
  protected $detach_partition;

  /**
   * Assign the settings.
   *
   * @param string|null $name
   *   The partition name to use.
   *   Set to NULL to disable.
   * @param string|null $bound_spec
   *   The bound spec to use.
   *   Required when $name is not NULL.
   *   Ignored when $name is NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_detach_partition($name, $bound_spec = NULL) {
    if (is_null($name)) {
      $this->detach_partition = NULL;
      return new c_base_return_true();
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($bound_spec)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'bound_spec', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder_name = $this->add_placeholder($name);
    if ($placeholder_name->has_error()) {
      return c_base_return_error::s_false($placeholder_name->get_error());
    }

    $placeholder_bound_spec = $this->add_placeholder($bound_spec);
    if ($placeholder_bound_spec->has_error()) {
      unset($placeholder_name);
      return c_base_return_error::s_false($placeholder_bound_spec->get_error());
    }

    $this->detach_partition = [
      'name' => $placeholder_name,
      'bound_spec' => $placeholder_bound_spec,
    ];
    unset($placeholder_name);
    unset($placeholder_bound_spec);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned rename from settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing detach partition settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_detach_partition() {
    if (is_null($this->detach_partition)) {
      return new c_base_return_null();
    }

    if (is_array($this->detach_partition)) {
      return c_base_return_array::s_new($this->detach_partition);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'detach_partition', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_detach_partition() {
    return c_database_string::ATTACH . ' ' . c_database_string::PARTITION . ' ' . $this->detach_partition['name'] . ' ' . c_database_string::FOR . ' ' . c_database_string::VALUES . ' ' . $this->detach_partition['bound_spec'];
  }
}
