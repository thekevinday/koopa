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

require_once('common/database/enumerations/database_attribute_action.php');
require_once('common/database/enumerations/database_attribute_cascade.php');

/**
 * Provide the sql type actions functionality.
 */
trait t_database_type_action {
  protected $type_action;

  /**
   * Assign the settings.
   *
   * @param int|null $attribute
   *   The attribute type to use from e_database_attribute_action.
   *   Set to NULL to disable.
   * @param string|null $name
   *   (optional) The attribute name.
   *   Required when $attribute is not NULL.
   * @param int|null $cascade
   *   (optional) The e_database_cascade property to use.
   * @param string|null $type
   *   (optional) The attribute data type.
   *   Ignored when not applicable.
   * @param string|null $collation
   *   (optional) The collation name.
   *   Ignored when not applicable.
   * @param bool|null $if_exists
   *   (optional) TRUE for IF EXISTS, FALSE to not specify.
   *   Ignored when not applicable.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public function set_type_action($attribute, $name = NULL, $cascade = NULL, $type = NULL, $collation = NULL, $if_exists = NULL) {
    if (is_null($attribute)) {
      $this->type_action = NULL;
      return new c_base_return_true();
    }

    $placeholder = $this->add_placeholder($name);
    if ($placeholder->has_error()) {
      return c_base_return_error::s_false($placeholder->get_error());
    }

    $type_action = [
      'attribute' => $attribute,
      'name' => $placeholder,
      'cascade' => NULL,
      'type' => NULL,
      'collation' => NULL,
      'if_exists' => NULL,
    ];
    unset($placeholder);

    if ($attribute === e_database_attribute_action::ADD) {
      if (is_null($collation)) {
        $placeholder = NULL;
      }
      else if (is_string($collation)) {
        $placeholder = $this->add_placeholder($collation);
        if ($placeholder->has_error()) {
          unset($type_action);
          return c_base_return_error::s_false($placeholder->get_error());
        }
      }
      else {
        unset($type_action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'collation', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $type_action['collation'] = $placeholder;
    }
    else if ($attribute === e_database_attribute_action::ALTER) {
      if (is_null($collation)) {
        $placeholder = NULL;
      }
      else if (is_string($collation)) {
        $placeholder = $this->add_placeholder($collation);
        if ($placeholder->has_error()) {
          unset($type_action);
          return c_base_return_error::s_false($placeholder->get_error());
        }
      }
      else {
        unset($type_action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'collation', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $type_action['collation'] = $placeholder;
    }
    else if ($attribute === e_database_attribute_action::DROP) {
      if (!is_null($if_exists) && !is_bool($if_exists)) {
        unset($type_action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'if_exists', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $type_action['if_exists'] = $if_exists;
    }
    else {
      unset($type_action);
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    switch($cascade) {
      case e_database_cascade::CASCADE:
      case e_database_cascade::RESTRICT:
        break;
      default:
        unset($type_action);
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'cascade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $this->type_action = $type_action;

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
  public function get_type_action() {
    if (is_null($this->type_action)) {
      return new c_base_return_null();
    }

    if (is_array($this->type_action)) {
      return c_base_return_array::s_new($this->type_action);
    }

    $error = c_base_error::s_log(NULL, ['arguments' => [':{variable_name}' => 'type_action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_VARIABLE);
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
  protected function p_do_build_type_action() {
    $value = NULL;

    if ($this->type_action['attribute'] === e_database_attribute_action::ADD) {
      $value = c_database_string::ADD . ' ' . c_database_string::ATTRIBUTE;
      $value .= ' ' . $this->type_action['name'];
      $value .= ' ' . $this->type_action['data_type'];

      if (isset($this->type_action['collation'])) {
        $value .= ' ' . c_database_string::COLLATE;
        $value .= ' ' . $this->type_action['collation'];
      }
    }
    else if ($this->type_action['attribute'] === e_database_attribute_action::ALTER) {
      $value = c_database_string::ALTER . ' ' . c_database_string::ATTRIBUTE;
      $value .= ' ' . $this->type_action['name'];
      $value .= ' ' . c_database_string::SET . ' ' . c_database_string::DATA . ' ' . c_database_string::TYPE;
      $value .= ' ' . $this->type_action['data_type'];

      if (isset($this->type_action['collation'])) {
        $value .= ' ' . c_database_string::COLLATE;
        $value .= ' ' . $this->type_action['collation'];
      }
    }
    else if ($this->type_action['attribute'] === e_database_attribute_action::DROP) {
      $value = c_database_string::DROP . ' ' . c_database_string::ATTRIBUTE;
      if ($this->type_action['if_exists']) {
        $value .= ' ' . c_database_string::IF . ' ' . c_database_string::EXISTS;
      }

      $value .= ' ' . $this->type_action['name'];
    }

    if ($this->type_action['cascade'] === c_database_string::CASCADE) {
      $value .= ' ' . c_database_string::CASCADE;
    }
    else if ($this->type_action['cascade'] === c_database_string::RESTRICT) {
      $value .= ' ' . c_database_string::RESTRICT;
    }

    return $value;
  }
}
