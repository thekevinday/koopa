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

require_once('common/database/enumerations/database_handler.php');

/**
 * Provide the sql HANDLER/NO HANDLER functionality.
 */
trait t_database_handler {
  protected $handler;

  /**
   * Set the HANDLER settings.
   *
   * @param int|null $handler
   *   The integer representing handler/no-handler.
   *   Set to NULL to disable.
   * @param string|null $handler_function
   *   The handler function name or null when NO_HANDLER is specified.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_handler($handler, $handler_function) {
    if (is_null($handler)) {
      $this->handler = NULL;
      return new c_base_return_true();
    }

    if ($handler === e_database_handler::HANDLER) {
      if (is_string($handler_function)) {
        $placeholder = $this->add_placeholder($handler_function);
        if ($placeholder->has_error()) {
          unset($action);
          return c_base_return_error::s_false($placeholder->get_error());
        }

        $this->handler = [
          'type' => $handler,
          'name' => $placeholder,
        ];
        unset($placeholder);

        return new c_base_return_true();
      }

      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'handler_function', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    else if ($handler === e_database_handler::NO_HANDLER) {
      $this->handler = [
        'type' => $handler,
        'name' => null,
      ];

      return new c_base_return_true();
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'handler', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Get the currently assigned handler.
   *
   * @return c_base_return_array|c_base_return_null
   *   An array containing the handler data on success.
   *   NULL is returned if not set.
   *   NULL with the error bit set is returned on error.
   */
  public function get_handler() {
    if (is_null($this->handler)) {
      return new c_base_return_null();
    }

    if (is_array($this->handler)) {
      return c_base_return_array::s_new($this->handler);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'handler', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_handler() {
    $value = NULL;
    if ($this->handler['type'] === e_database_handler::HANDLER) {
      if (isset($this->handler['name'])) {
        $value = c_database_string::HANDLER . ' ' . $this->handler['name'];
      }
    }
    else if ($this->handler['type'] === e_database_handler::NO_HANDLER) {
      $value .= c_database_string::NO_HANDLER;
    }

    return $value;
  }
}
