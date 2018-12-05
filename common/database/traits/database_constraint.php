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

require_once('common/database/enumerations/database_cascade.php');
require_once('common/database/enumerations/database_constraint.php');

/**
 * Provide the sql ADD/VALIDATE/DROP CONSTAINT functionality.
 */
trait t_database_constraint {
  protected $constraint;

  /**
   * Set the ADD/VALIDATE/DROP CONSTRAINT settings.
   *
   * @param string|null $constraint_name
   *   The name to use.
   *   Set to NULL to disable.
   * @param int|null $type
   *   The type code representing the constaint operation.
   *   This can only be NULL when $constraint_name is NULL.
   * @param bool|null $exists_or_invalid
   *   When $type is ADD, then this is a boolean such that NOT VALID is added when TRUE.
   *   When $type is DROP, then this is a boolean such that IS EXISTS is added when TRUE.
   *   Otherwise this should be NULL.
   * @param int|null $cascade
   *   When $type is DROP, this must be an integer representing CASCADE or RESTRICT.
   *   Otherwise this should be NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_constraint($constraint_name, $type = NULL, $exists_or_invalid = NULL, $cascade = NULL) {
    if (is_null($constraint)) {
      $this->constraint = NULL;
      return new c_base_return_true();
    }

    if (!is_string($constraint_name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'constraint', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $constraint = [
      'name' => $constraint_name,
      'type' => $type,
      'exists_or_invalid' => NULL,
      'cascade' => NULL,
    ];

    if ($type === e_database_constraint::ADD) {
      if (!is_bool($exists_or_invalid)) {
        unset($constraint);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'exists_or_invalid', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $constraint['exists_or_invalid'] = $exists_or_invalid;
    }
    else if ($type === e_database_constraint::DROP) {
      if (!is_bool($exists_or_invalid)) {
        unset($constraint);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'exists_or_invalid', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $constraint['exists_or_invalid'] = $exists_or_invalid;
    }
    else if ($type === e_database_constraint::VALIDATE) {
      switch ($cascade) {
        case e_database_cascade::CASCADE:
        case e_database_cascade::RESTRICT:
          $constraint['cascade'] = $cascade;
          break;
        default:
          unset($constraint);
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
      }
    }
    else {
      unset($constraint);
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->constraint = $constraint;
    unset($constraint);
    return new c_base_return_true();
  }

  /**
   * Get the currently assigned name.
   *
   * @return c_base_return_string|c_base_return_null
   *   A name on success.
   *   NULL is returned if not set (constraint is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_constraint_name() {
    if (is_null($this->constraint)) {
      return new c_base_return_null();
    }

    if (is_string($this->constraint['name'])) {
      return c_base_return_string::s_new($this->constraint['name']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'constraint[name]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned type.
   *
   * @return c_base_return_int|c_base_return_null
   *   A type code on success.
   *   NULL is returned if not set (constraint is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_constraint_type() {
    if (is_null($this->constraint)) {
      return new c_base_return_null();
    }

    if (is_int($this->constraint['type'])) {
      return c_base_return_int::s_new($this->constraint['type']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'constraint[type]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned exists or invalid setting.
   *
   * @return c_base_return_bool|c_base_return_null
   *   A boolean on success.
   *   NULL is returned if not set (constraint is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_constraint_exists_or_invalid() {
    if (is_null($this->constraint) || is_null($this->constraint['exists_or_invalid'])) {
      return new c_base_return_null();
    }

    if (is_bool($this->constraint['exists_or_invalid'])) {
      return c_base_return_bool::s_new($this->constraint['exists_or_invalid']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'constraint[exists_or_invalid]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
    return c_base_return_error::s_null($error);
  }

  /**
   * Get the currently assigned cascade setting.
   *
   * @return c_base_return_int|c_base_return_null
   *   A cascade code on success.
   *   NULL is returned if not set (constraint is not to be used).
   *   NULL with the error bit set is returned on error.
   */
  public function get_constraint_cascade() {
    if (is_null($this->constraint) || is_null($this->constraint['cascade'])) {
      return new c_base_return_null();
    }

    if (is_int($this->constraint['cascade'])) {
      return c_base_return_bool::s_new($this->constraint['cascade']);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'constraint[cascade]', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_constraint() {
    if (is_null($this->constraint)) {
      return NULL;
    }

    $value = NULL;
    if ($this->constraint['type'] === e_database_constraint::ADD) {
      if (is_string($this->constraint['name'])) {
        $value = c_database_string::ADD . ' ' . $this->constraint['name'];

        if ($this->constraint['exists_or_invalid']) {
          $value .= ' ' . c_database_string::NOT_VALID;
        }
      }
    }
    else if ($this->constraint['type'] === e_database_constraint::DROP) {
      if (is_string($this->constraint['name'])) {
        $value = c_database_string::DROP_CONSTRAINT . ' ' . $this->constraint['name'];
      }
    }
    else if ($this->constraint['type'] === e_database_constraint::VALIDATE) {
      if (is_string($this->constraint['name'])) {
        $value = c_database_string::VALIDATE_CONSTAINT;

        if ($this->constraint['exists_or_invalid']) {
          $value .= ' ' . c_database_string::NOT_VALID;
        }

        $value .=' ' . $this->constraint['name'];

        if ($this->constraint['cascade'] === e_database_cascade::CASCADE) {
          $value .= ' ' . c_database_string::CASCADE;
        }
        else if ($this->constraint['cascade'] === e_database_cascade::RESTRICT) {
          $value .= ' ' . c_database_string::RESTRICT;
        }
      }
    }

    return $value;
  }
}
