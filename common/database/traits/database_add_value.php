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

require_once('common/database/enumerations/database_position.php');

/**
 * Provide the sql ADD VALUE functionality.
 */
trait t_database_add_value {
  protected $add_value;

  /**
   * Set the SET WITH OIDS value.
   *
   * @param string|null $new_enum_value
   *   The enum value to use.
   *   Set to NULL to disable.
   * @param bool|null $if_not_exists
   *   (optional) If TRUE, IF NO EXISTS is used.
   *   If FALSE, do nothing.
   *   If NULL, do nothing.
   * @param int|null $position
   *   (optional) Either BEFORE or AFTER from e_database_position.
   *   If NULL, do nothing.
   * @param string|null $neighbor_enum_value
   *   The neighbor enum value to use.
   *   When not NULL, $position must also be not NULL.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_add_value($new_enum_value, $if_not_exists = NULL, $position = NULL, $neighbor_enum_value = NULL) {
    if (is_null($add_value)) {
      $this->add_value = NULL;
      return new c_base_return_true();
    }

    if (!is_string($new_enum_value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'new_enum_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($if_not_exists)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'if_not_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch ($position) {
      case e_database_position::AFTER:
      case e_database_position::BEFORE:
      case NULL:
        break;
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'position', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if (!is_null($neighbor_enum_value) && !is_string($neighbor_enum_value)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'neighbor_enum_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $placeholder_new_enum_value = $this->add_placeholder($new_enum_value);
    if ($placeholder_new_enum_value->has_error()) {
      return c_base_return_error::s_false($placeholder_new_enum_value->get_error());
    }

    $placeholder_neighbor_enum_value = NULL;
    if (is_string($neighbor_enum_value)) {
      unset($placeholder_new_enum_value);
      $placeholder_neighbor_enum_value = $this->add_placeholder($neighbor_enum_value);
      if ($placeholder_neighbor_enum_value->has_error()) {
        return c_base_return_error::s_false($placeholder_neighbor_enum_value->get_error());
      }
    }

    $this->add_value = [
      'new_enum_value' => $placeholder_new_enum_value,
      'if_not_exists' =>  $if_not_exists,
      'position' => $position,
      'neighbor_enum_value' => $placeholder_neighbor_enum_value,
    ];
    unset($placeholder_new_enum_value);
    unset($placeholder_neighbor_enum_value);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_add_value() {
    if (is_null($this->add_value)) {
      return new c_base_return_null();
    }

    if (is_array($this->add_value)) {
      return c_base_return_array::s_new($this->add_value);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'add_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_add_value() {
    $value = c_database_string::ADD_VALUE;
    // @todo: confirm/deny whether or not the placeholder will be auto-quoted by PDO.
    $value .= ' \'' . $this->add_value['new_enum_value'] . '\'';

    if ($this->add_value['if_not_exists']) {
      $value .= ' ' . c_database_string::IF_NOT_EXISTS;
    }

    if (is_int($this->add_value['position'])) {
      if ($this->add_value['position'] === e_database_position::AFTER) {
        $value .= ' ' . c_database_string::AFTER;
      }
      else if ($this->add_value['position'] === e_database_position::BEFORE) {
        $value .= ' ' . c_database_string::BEFORE;
      }

      // @todo: confirm/deny whether or not the placeholder will be auto-quoted by PDO.
      $value .= ' \'' . $this->add_value['neighbor_enum_value'] . '\'';
    }

    return $value;
  }
}
