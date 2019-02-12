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

require_once('common/database/enumerations/database_user.php');

/**
 * Provide the sql user name functionality.
 */
trait t_database_user_name {
  protected $user_name;

  /**
   * Assign the settings.
   *
   * @param int|null $type
   *   The user name type to use, from e_database_user.
   *   Set to NULL to disable.
   *   When NULL, this will remove all values.
   * @param string|null $name
   *   (optional) The user name to use if needed by a given $type.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_user_name($type, $name = NULL) {
    if (is_null($type)) {
      $this->user_name = NULL;
      return new c_base_return_true();
    }

    switch ($type) {
      case e_database_user::CURRENT:
      case e_database_user::PUBLIC:
      case e_database_user::SESSION:
        $placeholder = NULL;
        break;
      case e_database_user::NAME:
        if (!is_string($name)) {
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }

        $placeholder = $this->add_placeholder($name);
        if ($placeholder->has_error()) {
          return c_base_return_error::s_false($placeholder->get_error());
        }
      default:
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->user_name = [
      'type' => $type,
      'name' => $placeholder,
    ];
    unset($placeholder);

    return new c_base_return_true();
  }

  /**
   * Get the currently assigned settings.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array of type settings.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_user_name() {
    if (is_null($this->user_name)) {
      return new c_base_return_null();
    }

    if (is_array($this->user_name)) {
      return c_base_return_array::s_new($this->user_name);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'user_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_user_name() {
    $value = NULL;

    if ($this->user_name['type'] === e_database_user::CURRENT) {
      $value = c_database_string::CURRENT . ' ' . c_database_string::USER;
    }
    else if ($this->user_name['type'] === e_database_user::PUBLIC) {
      $value = c_database_string::PUBLIC;
    }
    else if ($this->user_name['type'] === e_database_user::SESSION) {
      $value = c_database_string::SESSION . ' ' . c_database_string::USER;
    }
    else if ($this->user_name['type'] === e_database_user::NAME) {
      $value = $this->user_name['name'];
    }

    return $value;
  }
}
