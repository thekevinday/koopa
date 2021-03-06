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
 * Provide the sql OWNED BY functionality.
 */
trait t_database_owned_by {
  protected $owned_by;

  /**
   * Assigns the SQL owned_by.
   *
   * @param int|string|null $owned_by
   *   Set a owned_by code or name.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_owned_by($owned_by) {
    if (is_null($owned_by)) {
      $this->owned_by = NULL;
      return new c_base_return_true();
    }

    if (is_int($owned_by)) {
      if ($owned_by === e_database_user::ALL || $owned_by === e_database_user::NONE) {
        $this->owned_by = e_database_user::ALL;

        return new c_base_return_true();
      }
    }
    else if (is_string($owned_by)) {
      if (!is_array($this->owned_by)) {
        $this->owned_by = [];
      }

      $placeholder = $this->add_placeholder($owned_by);
      if ($placeholder->has_error()) {
        return c_base_return_error::s_false($placeholder->get_error());
      }

      $this->owned_by[] = $placeholder;
      unset($placeholder);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'owned_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the owned by settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of owned_by or NULL if not defined.
   *   NULL with the error bit set is returned on error.
   */
  public function get_owned_by() {
    if (is_null($this->owned_by)) {
      return new c_base_return_null();
    }

    if ($this->owned_by === e_database_user::ALL || $this->owned_by === e_database_user::NONE) {
      return c_base_return_array::s_new([$this->owned_by]);
    }
    else if (is_array($this->owned_by)) {
      return c_base_return_array::s_new($this->owned_by);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'owned_by', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_owned_by() {
    $value = c_database_string::OWNED_BY . ' ';

    if ($this->owned_by === e_database_user::ALL) {
      $value .= c_database_string::ALL;
    }
    else if ($this->owned_by === e_database_user::NONE) {
      $value .= c_database_string::NONE;
    }
    else {
      $value .= implode(', ', $this->owned_by);
    }

    return $value;
  }
}
