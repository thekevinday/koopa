<?php
/**
 * @file
 * Provides path handler for the user settings.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_path.php');
require_once('common/base/classes/base_database.php');

require_once('common/standard/classes/standard_path.php');
require_once('common/standard/classes/standard_path_user.php');
require_once('common/standard/paths/u/user_view.php');

require_once('common/theme/classes/theme_html.php');

/**
 * Provides a path handler for user viewing user settings.
 *
 * This listens on: /u/settings
 */
class c_standard_path_user_settings extends c_standard_path_user {
  public const PATH_SELF = 'u/settings';

  protected const NAME_MENU_CONTENT    = 'menu_content_user_view';
  protected const HANDLER_MENU_CONTENT = '\n_koopa\c_standard_menu_content_user_view';

  protected const CLASS_USER_SETTINGS_ACCOUNT  = 'user_settings-account';
  protected const CLASS_USER_SETTINGS_PERSONAL = 'user_settings-personal';
  protected const CLASS_USER_SETTINGS_ACCESS   = 'user_settings-access';
  protected const CLASS_USER_SETTINGS_HISTORY  = 'user_settings-history';

  /**
   * Implementation of pr_build_breadcrumbs().
   */
  protected function pr_build_breadcrumbs() {
    $path_user_view = new c_standard_path_user_view();
    $path_user_view->set_parameters($this->http, $this->database, $this->session, $this->settings);
    $path_user_view->set_path_tree($this->get_path_tree($this->path_tree));
    $this->breadcrumbs = $path_user_view->get_breadcrumbs();
    unset($path_user_view);

    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $result = parent::pr_build_breadcrumbs();
      if ($result instanceof c_base_return_false) {
        unset($result);
        return new c_base_return_false();
      }
      unset($result);
    }

    if (!($this->breadcrumbs instanceof c_base_menu_item)) {
      $this->breadcrumbs = new c_base_menu_item();
    }

    $item = $this->pr_create_breadcrumbs_item($this->pr_get_text(0), self::PATH_SELF);
    $this->breadcrumbs->set_item($item);
    unset($item);

    return new c_base_return_true();
  }

  /**
   * Implementation of pr_create_html_add_header_link_canonical().
   */
  protected function pr_create_html_add_header_link_canonical() {
    $tag = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_LINK);
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_REL, 'canonical');
    $tag->set_attribute(c_base_markup_attributes::ATTRIBUTE_HREF, $this->settings['base_scheme'] . '://' . $this->settings['base_host'] . $this->settings['base_port'] . $this->settings['base_path'] . self::PATH_SELF);
    $this->html->set_header($tag);

    unset($tag);
  }

  /**
   * Implements pr_get_text().
   */
  protected function pr_get_text($code, $arguments = []) {
    $string = '';
    switch ($code) {
      case 0:
        if (array_key_exists(':{user_name}', $arguments)) {
          $string = 'User Settings: :{user_name}';
        }
        else {
          $string = 'User Settings';
        }
        break;
      case 1:
        $string = 'Public';
        break;
      case 2:
        $string = 'User';
        break;
      case 3:
        $string = 'System';
        break;
      case 4:
        $string = 'Requester';
        break;
      case 5:
        $string = 'Drafter';
        break;
      case 6:
        $string = 'Editor';
        break;
      case 7:
        $string = 'Reviewer';
        break;
      case 8:
        $string = 'Financer';
        break;
      case 9:
        $string = 'Insurer';
        break;
      case 10:
        $string = 'Publisher';
        break;
      case 11:
        $string = 'Auditor';
        break;
      case 12:
        $string = 'Manager';
        break;
      case 13:
        $string = 'Administer';
        break;
      case 14:
        $string = 'Account Information';
        break;
      case 15:
        $string = 'Personal Information';
        break;
      case 16:
        $string = 'Access Information';
        break;
      case 17:
        $string = 'History Information';
        break;
      case 18:
        $string = 'ID';
        break;
      case 19:
        $string = 'External ID';
        break;
      case 20:
        $string = 'Name';
        break;
      case 21:
        $string = 'E-mail';
        break;
      case 22:
        $string = 'Roles';
        break;
      case 23:
        $string = 'Role Manager';
        break;
      case 24:
        $string = 'Is Locked';
        break;
      case 25:
        $string = 'Is Deleted';
        break;
      case 26:
        $string = 'Is Public';
        break;
      case 27:
        $string = 'Is Private';
        break;
      case 28:
        $string = 'Date Created';
        break;
      case 29:
        $string = 'Date Changed';
        break;
      case 30:
        $string = 'Date Synced';
        break;
      case 31:
        $string = 'Date Locked';
        break;
      case 32:
        $string = 'Date Deleted';
        break;
      case 33:
        $string = 'Yes';
        break;
      case 34:
        $string = 'No';
        break;
      case 35:
        $string = 'Enabled';
        break;
      case 36:
        $string = 'Disabled';
        break;
      case 37:
        $string = 'Prefix';
        break;
      case 38:
        $string = 'First';
        break;
      case 39:
        $string = 'Middle';
        break;
      case 40:
        $string = 'Last';
        break;
      case 41:
        $string = 'Suffix';
        break;
      case 42:
        $string = 'Full';
        break;
      case 43:
        $string = 'Undisclosed';
        break;
      case 44:
        $string = 'User ID';
        break;
      case 45:
        $string = 'Title';
        break;
      case 46:
        $string = 'Type';
        break;
      case 47:
        $string = 'Sub-Type';
        break;
      case 48:
        $string = 'Severity';
        break;
      case 49:
        $string = 'Facility';
        break;
      case 50:
        $string = 'Details';
        break;
      case 51:
        $string = 'Date';
        break;
      case 52:
        $string = 'Client';
        break;
      case 53:
        $string = 'Response Code';
        break;
      case 54:
        $string = 'Session User ID';
        break;
      case 55:
        $string = 'Request Path';
        break;
      case 56:
        $string = 'Request Arguments';
        break;
      case 57:
        $string = 'Request Client';
        break;
      case 58:
        $string = 'Request Date';
        break;
      case 59:
        $string = 'Request Headers';
        break;
      case 60:
        $string = 'Response Headers';
        break;
      case 61:
        $string = 'Response Code';
        break;
      case 62:
        $string = 'User History';
        break;
      case 63:
        $string = 'Access History';
        break;
    }

    if (!empty($arguments)) {
      $this->pr_process_replacements($string, $arguments);
    }

    return $string;
  }

  /**
   * Execution of the view path.
   *
   * @param c_base_path_executed &$executed
   *   The execution results to be returned.
   *
   * @return null|array
   *   NULL is returned if no errors are found.
   *   An array of errors are returned if found.
   */
  protected function pr_do_execute_build_content(&$executed) {
    $errors = NULL;

    $arguments = [];
    $arguments[':{user_name}'] = $this->path_user->get_name_human()->get_first()->get_value_exact() . ' ' . $this->path_user->get_name_human()->get_last()->get_value_exact();
    if (mb_strlen($arguments[':{user_name}']) == 0) {
      unset($arguments[':{user_name}']);
    }

    if (is_int($this->path_user_id)) {
      $text_id_user = $this->pr_create_tag_text('[id: ' . $this->path_user_id . ']', [], NULL, static::CLASS_ID_USER);
      $wrapper = $this->pr_create_tag_section([1 => ['text' => 0, 'append-inside' => $text_id_user]], $arguments);
      unset($text_id_user);
    }
    else {
      $wrapper = $this->pr_create_tag_section([1 => 0], $arguments);
    }

    $roles_current = $this->session->get_user_current()->get_roles()->get_value_exact();
    $roles = $this->path_user->get_roles()->get_value_exact();

    $full_view_access = FALSE;
    if ($this->path_user_id === $this->session->get_user_current()->get_id()->get_value_exact()) {
      $full_view_access = TRUE;
    }
    else if (isset($roles_current[c_base_roles::MANAGER]) || isset($roles_current[c_base_roles::ADMINISTER])) {
      $full_view_access = TRUE;
    }


    // initialize the content as HTML.
    $this->pr_create_html(TRUE, $this->arguments);
    $this->html->set_tag($wrapper);
    unset($wrapper);
    unset($arguments);


    // account information
    $fieldset = $this->pr_create_tag_fieldset(14, [], static::CLASS_USER_SETTINGS_ACCOUNT, static::CLASS_USER_SETTINGS_ACCOUNT);
    $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, [static::CSS_AS_FIELD_SET_CONTENT]);

    $content->set_tag($this->pr_create_tag_field_row(18, '' . $this->path_user_id, [], NULL, c_standard_path::CSS_AS_ROW_EVEN, 0, TRUE));

    if ($full_view_access || !$this->path_user->get_address_email()->is_private()->get_value()) {
      $count = 1;

      if ($full_view_access) {
        $content->set_tag($this->pr_create_tag_field_row(19, '' . $this->path_user->get_id_external()->get_value(), [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
        $count++;
      }

      $content->set_tag($this->pr_create_tag_field_row(20, '' . $this->path_user->get_name_machine()->get_value(), [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
      $count++;

      $content->set_tag($this->pr_create_tag_field_row(21, '' . $this->path_user->get_address_email()->get_address()->get_value(), [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
      $count++;

      if ($this->path_user->is_locked()->get_value_exact()) {
        $tag_text = $this->pr_get_text(33);
      }
      else {
        $tag_text = $this->pr_get_text(34);
      }
      $content->set_tag($this->pr_create_tag_field_row(24, $tag_text, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
      $count++;

      if ($this->path_user->is_private()->get_value_exact()) {
        $tag_text = $this->pr_get_text(33);
      }
      else {
        $tag_text = $this->pr_get_text(34);
      }
      $content->set_tag($this->pr_create_tag_field_row(27, $tag_text, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
      $count++;

      if ($this->path_user->is_roler()->get_value_exact()) {
        $tag_text = $this->pr_get_text(33);
      }
      else {
        $tag_text = $this->pr_get_text(34);
      }
      $content->set_tag($this->pr_create_tag_field_row(23, $tag_text, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
      $count++;

      if (isset($roles_current[c_base_roles::MANAGER]) || isset($roles_current[c_base_roles::ADMINISTER])) {
        if ($this->path_user->is_deleted()->get_value_exact()) {
          $tag_text = $this->pr_get_text(33);
        }
        else {
          $tag_text = $this->pr_get_text(34);
        }
        $content->set_tag($this->pr_create_tag_field_row(25, $tag_text, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));

        $count++;
      }

      if ($full_view_access) {

        // date created
        $date = NULL;
        if (!is_null($this->path_user->get_date_created()->get_value())) {
          $date = c_base_defaults_global::s_get_date(c_base_defaults_global::FORMAT_DATE_TIME_SECONDS_HUMAN, $this->path_user->get_date_created()->get_value())->get_value_exact();
        }

        $content->set_tag($this->pr_create_tag_field_row(28, $date, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
        $count++;


        // date changed
        $date = NULL;
        if (!is_null($this->path_user->get_date_changed()->get_value())) {
          $date = c_base_defaults_global::s_get_date(c_base_defaults_global::FORMAT_DATE_TIME_SECONDS_HUMAN, $this->path_user->get_date_changed()->get_value())->get_value_exact();
        }

        $content->set_tag($this->pr_create_tag_field_row(29, $date, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
        $count++;


        // date synced
        $date = NULL;
        if (!is_null($this->path_user->get_date_synced()->get_value())) {
          $date = c_base_defaults_global::s_get_date(c_base_defaults_global::FORMAT_DATE_TIME_SECONDS_HUMAN, $this->path_user->get_date_synced()->get_value())->get_value_exact();
        }

        $content->set_tag($this->pr_create_tag_field_row(30, $date, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
        $count++;


        // date locked
        $date = NULL;
        if (!is_null($this->path_user->get_date_locked()->get_value())) {
          $date = c_base_defaults_global::s_get_date(c_base_defaults_global::FORMAT_DATE_TIME_SECONDS_HUMAN, $this->path_user->get_date_locked()->get_value())->get_value_exact();
        }

        $content->set_tag($this->pr_create_tag_field_row(31, '' . $date, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
        $count++;


        // date deleted
        $date = NULL;
        if (!is_null($this->path_user->get_date_deleted()->get_value())) {
          $date = c_base_defaults_global::s_get_date(c_base_defaults_global::FORMAT_DATE_TIME_SECONDS_HUMAN, $this->path_user->get_date_deleted()->get_value())->get_value_exact();
        }

        $content->set_tag($this->pr_create_tag_field_row(32, '' . $date, [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));
        $count++;
      }

      unset($count);
      unset($date);
    }
    else {
      $content->set_tag($this->pr_create_tag_field_row(20, '' . $this->path_user->get_name_machine()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_ODD, 1, TRUE));
      $content->set_tag($this->pr_create_tag_field_row(21, $this->pr_get_text(43), [], NULL, c_standard_path::CSS_AS_ROW_EVEN, 2, TRUE));
    }

    $fieldset->set_tag($content);
    unset($content);

    $this->html->set_tag($fieldset);
    unset($fieldset);


    if ($full_view_access || !$this->path_user->is_private()->get_value()) {
      // personal information
      $fieldset = $this->pr_create_tag_fieldset(15, [], static::CLASS_USER_SETTINGS_PERSONAL, static::CLASS_USER_SETTINGS_PERSONAL);
      $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, [static::CSS_AS_FIELD_SET_CONTENT]);

      $content->set_tag($this->pr_create_tag_field_row(37, '' . $this->path_user->get_name_human()->get_prefix()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_EVEN, 0, TRUE));
      $content->set_tag($this->pr_create_tag_field_row(38, '' . $this->path_user->get_name_human()->get_first()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_ODD, 1, TRUE));
      $content->set_tag($this->pr_create_tag_field_row(39, '' . $this->path_user->get_name_human()->get_middle()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_EVEN, 2, TRUE));
      $content->set_tag($this->pr_create_tag_field_row(40, '' . $this->path_user->get_name_human()->get_last()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_ODD, 3, TRUE));
      $content->set_tag($this->pr_create_tag_field_row(41, '' . $this->path_user->get_name_human()->get_suffix()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_EVEN, 4, TRUE));
      $content->set_tag($this->pr_create_tag_field_row(42, '' . $this->path_user->get_name_human()->get_complete()->get_value(), [], NULL, c_standard_path::CSS_AS_ROW_ODD, 5, TRUE));

      $fieldset->set_tag($content);
      unset($content);

      $this->html->set_tag($fieldset);
      unset($fieldset);


      // access information
      $fieldset = $this->pr_create_tag_fieldset(16, [], static::CLASS_USER_SETTINGS_ACCESS, static::CLASS_USER_SETTINGS_ACCESS);
      $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, [static::CSS_AS_FIELD_SET_CONTENT]);

      $access_to_text_mapping = [
        c_base_roles::PUBLIC => 1,
        c_base_roles::SYSTEM => 2,
        c_base_roles::USER => 3,
        c_base_roles::REQUESTER => 4,
        c_base_roles::DRAFTER => 5,
        c_base_roles::EDITOR => 6,
        c_base_roles::REVIEWER => 7,
        c_base_roles::FINANCER => 8,
        c_base_roles::INSURER => 9,
        c_base_roles::PUBLISHER => 10,
        c_base_roles::AUDITOR => 11,
        c_base_roles::MANAGER => 12,
        c_base_roles::ADMINISTER => 13,
      ];

      $id_text = NULL;
      $count = 0;
      foreach ($roles as $role) {
        if (!isset($access_to_text_mapping[$role])) {
          continue;
        }

        $content->set_tag($this->pr_create_tag_field_row($access_to_text_mapping[$role], [], NULL, ($count % 2 == 0 ? c_standard_path::CSS_AS_ROW_EVEN : c_standard_path::CSS_AS_ROW_ODD), $count, TRUE));

        $count++;
      }
      unset($role);
      unset($id_text);
      unset($count);

      $fieldset->set_tag($content);
      unset($content);

      $this->html->set_tag($fieldset);
      unset($fieldset);
      unset($roles);


      // history information
      $fieldset = $this->pr_create_tag_fieldset(17, [], static::CLASS_USER_SETTINGS_HISTORY, static::CLASS_USER_SETTINGS_HISTORY);
      $content = c_theme_html::s_create_tag(c_base_markup_tag::TYPE_DIVIDER, static::CSS_AS_FIELD_SET_CONTENT, [static::CSS_AS_FIELD_SET_CONTENT]);

      // user history
      // @todo: implement code for processing and generating a table/list of history, with the ability to navigate additional entries.
      $query_result = $this->database->do_query('select id, log_title, log_type, log_type_sub, log_severity, log_facility, log_date, request_client, response_code from v_log_users_self order by id desc limit 10');

      if (c_base_return::s_has_error($query_result)) {
        if (is_null($errors)) {
          $errors = [];
        }

        c_base_return::s_copy_errors($query_result->get_error(), $errors);

        $last_error = $this->database->get_last_error()->get_value_exact();
        if (!empty($last_error)) {
          $errors[] = c_base_error::s_log(NULL, ['arguments' => [':{database_error_message}' => $last_error, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::POSTGRESQL_ERROR);
        }
        unset($last_error);
      }
      else {
        $tag_table = $this->pr_create_tag_table(62);

        $tag_table_header = $this->pr_create_tag_table_header();
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(18));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(45));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(46));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(47));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(48));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(49));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(51));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(52));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(53));

        $tag_table->set_tag($tag_table_header);
        unset($tag_table_header);

        $tag_table_body = $this->pr_create_tag_table_body();

        $columns = $query_result->fetch_row()->get_value();
        while (is_array($columns) && !empty($columns)) {
          $row_timestamp = c_base_defaults_global::s_get_timestamp($columns[6], c_base_database::STANDARD_TIMESTAMP_FORMAT)->get_value_exact();
          $row_date = c_base_defaults_global::s_get_date(c_base_defaults_global::FORMAT_DATE_TIME_SECONDS_HUMAN, $row_timestamp)->get_value_exact();
          unset($row_timestamp);

          $tag_table_row = $this->pr_create_tag_table_row();
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[0]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[1]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[2]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[3]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[4]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[5]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $row_date));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[7]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[8]));
          unset($row_date);

          $tag_table_body->set_tag($tag_table_row);
          unset($tag_table_row);

          $columns = $query_result->fetch_row()->get_value();
        }
        unset($columns);

        $tag_table->set_tag($tag_table_body);
        unset($tag_table_body);

        $content->set_tag($tag_table);
        unset($tag_table);
      }


      // access history
      // @todo: implement code for processing and generating a table/list of history, with the ability to navigate additional entries.
      $query_result = $this->database->do_query('select id, request_path, request_arguments, request_date, request_client, response_code from v_log_user_activity_self order by id desc limit 10');

      if (c_base_return::s_has_error($query_result)) {
        if (is_null($errors)) {
          $errors = [];
        }

        c_base_return::s_copy_errors($query_result->get_error(), $errors);

        $last_error = $this->database->get_last_error()->get_value_exact();
        if (!empty($last_error)) {
          $errors[] = c_base_error::s_log(NULL, ['arguments' => [':{database_error_message}' => $last_error, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::POSTGRESQL_ERROR);
        }
        unset($last_error);
      }
      else {
        $tag_table = $this->pr_create_tag_table(62);

        $tag_table_header = $this->pr_create_tag_table_header();
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(18));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(55));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(56));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(58));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(57));
        $tag_table_header->set_tag($this->pr_create_tag_table_header_cell(61));

        $tag_table->set_tag($tag_table_header);
        unset($tag_table_header);

        $tag_table_body = $this->pr_create_tag_table_body();

        // @fixme: below is just an example/test. Rewrite this, cleaning up the code and adding more appropriate sanitizers and structure.
        $columns = $query_result->fetch_row()->get_value();
        while (is_array($columns) && !empty($columns)) {
          $tag_table_row = $this->pr_create_tag_table_row();
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[0]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[1]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[2]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[3]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[4]));
          $tag_table_row->set_tag($this->pr_create_tag_table_cell((string) $columns[5]));

          $tag_table_body->set_tag($tag_table_row);
          unset($tag_table_row);

          $columns = $query_result->fetch_row()->get_value();
        }
        unset($columns);

        $tag_table->set_tag($tag_table_body);
        unset($tag_table_body);

        $content->set_tag($tag_table);
        unset($tag_table);
      }

      $fieldset->set_tag($content);
      unset($content);

      $this->html->set_tag($fieldset);
      unset($fieldset);
    }


    // @todo add edit, cancel, etc.. links.


    $this->pr_add_menus();

    $executed->set_output($this->html);
    unset($this->html);

    return $errors;
  }
}
