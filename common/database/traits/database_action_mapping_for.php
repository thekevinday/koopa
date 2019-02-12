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

require_once('common/database/enumerations/database_mapping_for.php');

/**
 * Provide the sql MAPPING FOR functionality.
 */
trait t_database_mapping_for {
  protected $mapping_for;

  /**
   * Assign the settings.
   *
   * Set all parameters to NULL to reset data to NULL.
   *
   * @param int|null $type
   *   The mapping type from e_database_mapping_for.
   * @param string|null $token
   *   (optional) The token type.
   * @param bool|null $if_exists
   *   (optional) Boolean for adding IF EXISTS when $type is DROP.
   *   Set to TRUE to use IF EXISTS.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_mapping_for($type, $token = NULL, $if_exists = NULL) {
    if (is_null($name) && is_null($token) && is_null($if_exists)) {
      $this->mapping_for = NULL;
      return new c_base_return_true();
    }

    switch ($type) {
      case e_database_mapping_for::ADD:
      case e_database_mapping_for::ALTER:
      case e_database_mapping_for::DROP:
      case e_database_mapping_for::REPLACE:
        $this->mapping_for['type'] = $type;
        break;
      case NULL:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_null($token) && !is_string($token)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->mapping_for)) {
      $this->mapping_for = [
        'type' => NULL,
        'values' => [],
        'if_exists' => NULL,
      ];
    }

    if (is_int($type)) {
      $this->mapping_for['type'] = $type;
    }

    if (is_string($token)) {
      $placeholder = $this->add_placeholder($token);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->mapping_for['values'][] = $placeholder;
      unset($placeholder);
    }

    if (is_bool($if_exists)) {
      $this->mapping_for['if_exists'] = $if_exists;
    }

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the add table settings.
   *   NULL is returned if not set (add table not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_mapping_for() {
    if (is_null($this->mapping_for)) {
      return new c_base_return_null();
    }

    if (isset($this->mapping_for)) {
      return c_base_return_array::s_new($this->mapping_for);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'mapping_for', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_mapping_for() {
    $value = NULL;

    if ($this->mapping_for['type'] === e_database_mapping_for::ADD) {
      $value = c_database_string::ADD . ' ' . c_database_string::MAPPING . ' ' . c_database_string::FOR;
    }
    else if ($this->mapping_for['type'] === e_database_mapping_for::ALTER) {
      $value = c_database_string::ALTER . ' ' . c_database_string::MAPPING . ' ' . c_database_string::FOR;
    }
    else if ($this->mapping_for['type'] === e_database_mapping_for::DROP) {
      $value = c_database_string::DROP . ' ' . c_database_string::MAPPING;

      if ($this->mapping_for['if_exists']) {
        $value .= ' ' . c_database_string::IF . ' ' . c_database_string::EXISTS;
      }

      $value .= ' ' . c_database_string::FOR;
    }
    else if ($this->mapping_for['type'] === e_database_mapping_for::REPLACE) {
      $value = c_database_string::ALTER . ' ' . c_database_string::MAPPING;

      if (isset($this->mapping_for['values'])) {
        $value .= ' ' . c_database_string::FOR;
      }
    }

    if (isset($this->mapping_for['values'])) {
      $value .= ' ' . implode(', ', $this->mapping_for['values']);
    }

    if ($this->mapping_for['type'] === e_database_mapping_for::REPLACE) {
      $value .= ' ' . c_database_string::REPLACE;
    }

    return $value;
  }
}
