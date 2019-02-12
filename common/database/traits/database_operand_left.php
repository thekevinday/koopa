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

require_once('common/database/enumerations/database_operand.php');

/**
 * Provide the sql (left) operand functionality.
 */
trait t_database_operand_left {
  protected $operand_left;

  /**
   * Assign the settings.
   *
   * @param int|string|null $type
   *   The operand to assign.
   *   Can be set to e_database_operand::NONE.
   *   Set to NULL to disable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_operand_left($type) {
    if (is_null($type)) {
      $this->operand_left = NULL;
      return new c_base_return_true();
    }

    if (is_int($type)) {
      if ($type !== e_database_operand::NONE) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->operand_left = e_database_operand::NONE;
      return new c_base_return_true();
    }
    else if (is_string($type)) {
      $placeholder = $this->add_placeholder($type);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->operand_left = $placeholder;
      unset($placeholder);

      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Get the currently assigned operand_left settings.
   *
   * @return i_database_query_placeholder|c_base_return_int|c_base_return_null
   *   The operand int or query placeholder on success.
   *   NULL is returned if not set (operand left is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_operand_left() {
    if (is_null($this->operand_left)) {
      return new c_base_return_null();
    }

    if (is_int($this->operand_left)) {
      return c_base_return_int::s_new($this->operand_left);
    }
    else if (isset($this->operand_left)) {
      return clone($this->operand_left);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'operand_left', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_operand_left() {
    if ($this->operand_left === e_database_operand::NONE) {
      return c_database_string::NONE;
    }

    return strval($this->operand_left);
  }
}
