<?php
/**
 * @file
 * Provides a class for managing ldap connections.
 *
 * This is initially designed just to select/read from the ldap and not meant to modify or manage ldap databases.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A class for managing ldap connections.
 */
class c_base_ldap {
  private $ldap;
  private $name;

  private $bind_name;
  private $bind_password;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->ldap = NULL;
    $this->name = NULL;

    $this->bind_name = NULL;
    $this->bind_password = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->ldap);
    unset($this->name);

    unset($this->bind_name);
    unset($this->bind_password);
  }

  /**
   * Assigns the host name url of the server to connect to.
   *
   * @param string $name
   *   The host name url, such as: ldaps://ldap.example.com/ .
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_name($name) {
    if (!is_string($name) || empty($name)) {
      return c_base_return_error::s_false();
    }

    // sanitize the name string.
    $parsed = parse_url($name, PHP_URL_HOST);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    $this->name = preg_replace('/(^\s+)|(\s+$)/us', '', $parsed);
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Returns the stored ldap host name url.
   *
   * @return c_base_return_string
   *   The user name string.
   */
  public function get_name() {
    return c_base_return_string::s_new($this->name);
  }

  /**
   * Assigns the bind name used to connect to the ldap server.
   *
   * @param string $name
   *   The bind name. Often referred to as the bind_rdn.
   *   Set to NULL to disble bind username.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_bind_name($name) {
    if (!is_null($name) && (!is_string($name) || empty($name))) {
      return c_base_return_error::s_false();
    }

    $this->bind_name = $name;
    return new c_base_return_true();
  }

  /**
   * Returns the bind user name.
   *
   * @return c_base_return_string
   *   The bind name. Often referred to as the bind_rdn.
   */
  public function get_bind_name() {
    return c_base_return_string::s_new($this->bind_name);
  }

  /**
   * Assigns the bind password used to connect to the ldap server.
   *
   * @param string|null $password
   *   The bind password.
   *   Set to NULL to disble bind password.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_bind_password($password) {
    if (!is_null($password) && (!is_string($password) || empty($password))) {
      return c_base_return_error::s_false();
    }

    $this->bind_password = $password;
    return new c_base_return_true();
  }

  /**
   * Returns the bind password.
   *
   * @return c_base_return_string
   *   The password string.
   */
  public function get_bind_password() {
    return c_base_return_string::s_new($this->bind_password);
  }

  /**
   * Binds/Connects to the ldap server.
   *
   * This performs both ldap_connect() and ldap_bind().
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: ldap_connect()
   * @see: ldap_bind()
   * @see: self::do_prepare()
   */
  public function do_connect() {
    if (is_null($this->name)) {
      return c_base_return_error::s_false();
    }

    // already prepared, just return true.
    if (is_resource($this->ldap)) {
      return new c_base_return_true();
    }

    $this->ldap = ldap_connect($this->name);
    if (!is_resource($this->ldap)) {
      $this->ldap = NULL;
      return c_base_return_error::s_false();
    }

    $bound = ldap_bind($this->ldap, $this->bind_name, $this->bind_password);
    if ($bound) {
      unset($bound);
      return new c_base_return_true();
    }
    unset($bound);

    return c_base_return_error::s_false();
  }

  /**
   * Unbinds/Disconnects from the ldap server.
   *
   * The ldap connection must be prepared before this function can be used.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: ldap_unbind()
   * @see: self::do_connect()
   */
  public function do_disconnect() {
    if (!is_resource($this->ldap)) {
      return new c_base_return_false();
    }

    $unbound = ldap_unbind($this->ldap);
    if ($unbound) {
      unset($unbound);
      return new c_base_return_true();
    }
    unset($unbound);

    return c_base_return_error::s_false();
  }

  /**
   * Search the ldap database.
   *
   * @param string $directory_name
   *   The is the base directory name (DN).
   * @param string $filter
   *   A simple or advanced filter string.
   * @param array $attributes
   *   An array of required attributes.
   *   The PHP documentation recommends supplying this for efficiency reasons.
   * @param bool $attributes_only
   *   (optional) If TRUE, then only the attribute types.
   *   Otherwise, both attribute types and values are loaded.
   * @param int $entry_limit
   *   (optional) Limit the number of entries fetched by this limit.
   *   PHP calls this the size limit. 0 means no limit.
   * @param int $time_limit
   *   (optional) Limit the number of seconds the query is allowed to operate.
   *   0 means no limit.
   * @param int $dereference
   *   Specify how aliases should be handled during the search.
   *   One of: LDAP_DEREF_NEVER, LDAP_DEREF_SEARCHING, LDAP_DEREF_FINDING, LDAP_DEREF_ALWAYS.
   *
   * @return c_base_return_status|c_base_ldap_entries
   *   The search identifier is returned
   *
   * @see: ldap_search()
   */
  public function do_search($directory_name, $filter, $attributes, $attributes_only = FALSE, $entry_limit = 0, $time_limit = 0, $dereference = LDAP_DEREF_NEVER) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_string($directory_name) || !is_string($filter)) {
      return c_base_return_error::s_false();
    }

    if (!is_array($attributes)) {
      return c_base_return_error::s_false();
    }

    if (!is_int($entry_limit) || $entry_limit < 0) {
      return c_base_return_error::s_false();
    }

    if (!is_int($time_limit) || $time_limit < 0) {
      return c_base_return_error::s_false();
    }

    if (!is_int($dereference)) {
      return c_base_return_error::s_false();
    }

    // To prevent flooding the logs, prepend @ to prevent errors from being printed as described by the PHP documentation.
    // Any errors can still be obtained via the self::get_error_message() and self::get_error_number() functions.
    $found = ldap_search($this->ldap, $directory_name, $filter, $attributes, $attributes_only, $entry_limit, $time_limit, $dereference);
    if (is_resource($found)) {
      $result = c_base_ldap_result::s_new($found);
      $result->set_ldap($this->ldap);

      unset($found);
      return $result;
    }
    unset($found);

    return c_base_return_error::s_false();
  }

  /**
   * Search the ldap database, but only as far as the base directory name (single-depth).
   *
   * @param string $directory_name
   *   The is the base directory name (DN).
   * @param string $filter
   *   A simple or advanced filter string.
   * @param array $attributes
   *   An array of required attributes.
   *   The PHP documentation recommends supplying this for efficiency reasons.
   * @param bool $attributes_only
   *   (optional) If TRUE, then only the attribute types.
   *   Otherwise, both attribute types and values are loaded.
   * @param int $entry_limit
   *   (optional) Limit the number of entries fetched by this limit.
   *   PHP calls this the size limit. 0 means no limit.
   * @param int $time_limit
   *   (optional) Limit the number of seconds the query is allowed to operate.
   *   0 means no limit.
   * @param int $dereference
   *   Specify how aliases should be handled during the search.
   *   One of: LDAP_DEREF_NEVER, LDAP_DEREF_SEARCHING, LDAP_DEREF_FINDING, LDAP_DEREF_ALWAYS.
   *
   * @return c_base_return_status|c_base_ldap_entries
   *   The search identifier is returned
   *
   * @see: ldap_list()
   */
  public function do_list($directory_name, $filter, $attributes, $attributes_only = FALSE, $entry_limit = 0, $time_limit = 0, $dereference = LDAP_DEREF_NEVER) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_string($directory_name) || !is_string($filter)) {
      return c_base_return_error::s_false();
    }

    if (!is_array($attributes)) {
      return c_base_return_error::s_false();
    }

    if (!is_int($entry_limit) || $entry_limit < 0) {
      return c_base_return_error::s_false();
    }

    if (!is_int($time_limit) || $time_limit < 0) {
      return c_base_return_error::s_false();
    }

    if (!is_int($dereference)) {
      return c_base_return_error::s_false();
    }

    // To prevent flooding the logs, prepend @ to prevent errors from being printed as described by the PHP documentation.
    // Any errors can still be obtained via the self::get_error_message() and self::get_error_number() functions.
    $found = ldap_list($this->ldap, $directory_name, $filter, $attributes, $attributes_only, $entry_limit, $time_limit, $dereference);
    if (is_resource($found)) {
      $result = c_base_ldap_result::s_new($found);
      $result->set_ldap($this->ldap);

      unset($found);
      return $result;
    }
    unset($found);

    return c_base_return_error::s_false();
  }

  /**
   * Read the ldap database, but only for a single entry.
   *
   * @param string $directory_name
   *   The is the base directory name (DN).
   * @param string $filter
   *   A simple or advanced filter string.
   * @param array $attributes
   *   An array of required attributes.
   *   The PHP documentation recommends supplying this for efficiency reasons.
   * @param bool $attributes_only
   *   (optional) If TRUE, then only the attribute types.
   *   Otherwise, both attribute types and values are loaded.
   * @param int $entry_limit
   *   (optional) Limit the number of entries fetched by this limit.
   *   PHP calls this the size limit. 0 means no limit.
   * @param int $time_limit
   *   (optional) Limit the number of seconds the query is allowed to operate.
   *   0 means no limit.
   * @param int $dereference
   *   Specify how aliases should be handled during the search.
   *   One of: LDAP_DEREF_NEVER, LDAP_DEREF_SEARCHING, LDAP_DEREF_FINDING, LDAP_DEREF_ALWAYS.
   *
   * @return c_base_return_status|c_base_ldap_entries
   *   The search identifier is returned
   *
   * @see: ldap_read()
   */
  public function do_read($directory_name, $filter, $attributes, $attributes_only = FALSE, $entry_limit = 0, $time_limit = 0, $dereference = LDAP_DEREF_NEVER) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_string($directory_name) || !is_string($filter)) {
      return c_base_return_error::s_false();
    }

    if (!is_array($attributes)) {
      return c_base_return_error::s_false();
    }

    if (!is_int($entry_limit) || $entry_limit < 0) {
      return c_base_return_error::s_false();
    }

    if (!is_int($time_limit) || $time_limit < 0) {
      return c_base_return_error::s_false();
    }

    if (!is_int($dereference)) {
      return c_base_return_error::s_false();
    }

    // To prevent flooding the logs, prepend @ to prevent errors from being printed as described by the PHP documentation.
    // Any errors can still be obtained via the self::get_error_message() and self::get_error_number() functions.
    $found = ldap_read($this->ldap, $directory_name, $filter, $attributes, $attributes_only, $entry_limit, $time_limit, $dereference);
    if (is_resource($found)) {
      $result = c_base_ldap_result::s_new($found);
      $result->set_ldap($this->ldap);

      unset($found);
      return $result;
    }
    unset($found);

    return c_base_return_error::s_false();
  }

  /**
   * Compare value of an attribute specified within a directory.
   *
   * This does not recurse down to sub-entries.
   *
   * @param string $directory_name
   *   The diretory or dn.
   * @param string $attribute
   *   The attribute to compare.
   * @param string $value
   *   The value to compare against.
   *
   * @return c_return_status
   *   TRUE on match, FALSE on no-match (no error bit set), and FALSE with error bit set for error.
   *
   * @see: ldap_compare()
   */
  public function do_compare($directory_name, $attribute, $value) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_string($directory_name) || !is_string($attribute) || !is_string($value)) {
      return c_base_return_error::s_false();
    }

    $result = ldap_compare($this->ldap, $domain, $attribute, $value);
    if ($result === -1) {
      unset($result);
      return c_base_return_error::s_false();
    }

    if ($result === TRUE) {
      unset($result);
      return new c_base_return_true();
    }
    unset($result);

    return new c_base_return_false();
  }

  /**
   * Returns the ldap error message, if there is an error.
   *
   * @return c_base_return_status|c_base_return_string
   *   The error message.
   *
   * @see: ldap_error()
   */
  public function get_error_message() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new(ldap_error($this->ldap));
  }

  /**
   * Returns the ldap error number, if there is an error.
   *
   * Call ldap_err2str() with this number at any point to determine the message associated with it.
   *
   * @return c_base_return_status|c_base_return_int
   *   The error number.
   *
   * @see: ldap_errno()
   * @see: ldap_err2str()
   */
  public function get_error_number() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new(ldap_errno($this->ldap));
  }

  /**
   * Get the current value for a given option in the ldap connection.
   *
   * @param int $option
   *   A number representing the option.
   *
   * @return c_base_return_status|c_base_return_value
   *   FALSE with the error bit set is returned on failure.
   *   Anything else is returned on success.
   *   The return type is depending on the value for option, see:
   *   - LDAP_OPT_DEREF: c_base_return_int
   *   - LDAP_OPT_SIZELIMIT: c_base_return_int
   *   - LDAP_OPT_TIMELIMIT: c_base_return_int
   *   - LDAP_OPT_NETWORK_TIMEOUT: c_base_return_int
   *   - LDAP_OPT_PROTOCOL_VERSION: c_base_return_int
   *   - LDAP_OPT_ERROR_NUMBER: c_base_return_int
   *   - LDAP_OPT_REFERRALS: c_base_return_status
   *   - LDAP_OPT_RESTART: c_base_return_status
   *   - LDAP_OPT_HOST_NAME: c_base_return_string
   *   - LDAP_OPT_ERROR_STRING: c_base_return_string
   *   - LDAP_OPT_MATCHED_DN: c_base_return_string
   *   - LDAP_OPT_SERVER_CONTROLS: c_base_return_array
   *   - LDAP_OPT_CLIENT_CONTROLS: c_base_return_array
   *   - Anything else: c_base_return_value.
   *
   * @see: ldap_get_option()
   */
  public function get_option($option) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_int($option)) {
      return c_base_return_error::s_false();
    }

    $value = NULL;
    if (ldap_get_option($this->ldap, $option, $value) === FALSE) {
      unset($value);
      return c_base_return_error::s_false();
    }

    if ($option == LDAP_OPT_DEREF || $option == LDAP_OPT_SIZELIMIT || $option == LDAP_OPT_TIMELIMIT || $option == LDAP_OPT_NETWORK_TIMEOUT || $option == LDAP_OPT_PROTOCOL_VERSION || $option == LDAP_OPT_ERROR_NUMBER) {
      return c_base_return_int::s_new($value);
    }

    if ($option == LDAP_OPT_REFERRALS || $option == LDAP_OPT_RESTART) {
      if ($value === TRUE) {
        unset($value);
        return new c_base_return_true();
      }
      unset($value);

      return new c_base_return_false();
    }

    if ($option == LDAP_OPT_HOST_NAME || $option == LDAP_OPT_ERROR_STRING || $option == LDAP_OPT_MATCHED_DN) {
      return c_base_return_string::s_new($value);
    }

    if ($option == LDAP_OPT_SERVER_CONTROLS || $option == LDAP_OPT_CLIENT_CONTROLS) {
      return c_base_return_array::s_new($value);
    }

    return c_base_return_value::s_new($value);
  }
}

/**
 * A generic class for processing ldap search results.
 *
 * This should be returned by c_base_ldap::search().
 */
class c_base_ldap_result extends c_base_return_resource {
  private $ldap;
  private $entry;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->ldap = NULL;
    $this->entry = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    if (is_resource($this->value)) {
      ldap_free_result($this->value);
    }

    unset($this->ldap);
    unset($this->entry);

    parent::__destruct();
  }

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * Assigns the ldap resource to this class.
   *
   * @param resource $ldap
   *   The ldap resource to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_ldap($ldap) {
    if (!is_resource($ldap)) {
      return c_base_return_error::s_false();
    }

    $this->ldap = $ldap;
    return new c_base_return_true();
  }

  /**
   * Returns the total number of entries.
   *
   * This appears to be capped by the directory limit.
   * Therefore, this may not actually be the max number of entries.
   * Instead, consider this the total number of entries retrieved in a single request.
   *
   * @return c_base_return_status|c_base_return_int
   *
   * @see: ldap_count_entries()
   */
  public function get_count() {
    if (!is_resource($ldap)) {
      return c_base_return_error::s_false();
    }

    $total = ldap_count_entries($this->ldap, $this->value);
    if ($total === FALSE) {
      unset($total);
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($total);
  }

  /**
   * Loads the first entry.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: ldap_first_entry()
   */
  public function load_entry_first() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    $first = ldap_first_entry($this->ldap, $this->value);
    if ($first === FALSE) {
      unset($first);
      return c_base_return_error::s_false();
    }

    $this->entry = $first;
    unset($first);

    return new c_base_return_true();
  }

  /**
   * Returns the next ldap result entry.
   *
   * The first entry must be loaded before calling this function.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE on error.
   *   FALSE (without error bit set) is returned when there are no remaining entries.
   *
   * @see: ldap_first_entry()
   * @see: self::load_entry_first()
   */
  public function load_entry_next() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    // the entry is false when there are no entries remaining.
    if ($this->entry === FALSE) {
      return new c_base_return_false();
    }

    // the entry must first be loaded by self::load_entry_first().
    if (!is_null($this->entry)) {
      return c_base_return_error::s_false();
    }

    $this->entry = ldap_next_entry($this->ldap, $this->value);
    if ($this->entry === FALSE) {
      return new c_base_return_false();
    }

    return $entry;
  }

  /**
   * Returns all entries.
   *
   * This requires that the entries be loaded first.
   * Call self::load_entry_first() or the subsequent self::load_entry_next() before calling this.
   *
   * @return c_base_return_status|c_base_return_array
   *   The an array of attribute strings on success, FALSE otherwise.
   *
   * @see: ldap_get_entries()
   * @see: self::load_entry_first()
   * @see: self::load_entry_next()
   */
  public function get_entry_all() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    $entries = ldap_get_entries($this->ldap, $this->value);
    if ($entries === FALSE) {
      unset($entries);
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($entries);
  }

  /**
   * Returns the first attribute for a given entry.
   *
   * This requires that the entries be loaded first.
   * Call self::load_entry_first() or the subsequent self::load_entry_next() before calling this.
   *
   * @return c_base_return_status|c_base_return_string
   *   The attribute string on success, FALSE otherwise.
   *
   * @see: ldap_first_attribute()
   * @see: self::load_entry_first()
   * @see: self::load_entry_next()
   */
  public function get_attribute_first() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_resource($this->entry)) {
      return c_base_return_error::s_false();
    }

    $attribute = ldap_first_attribute($this->ldap, $this->entry);
    if ($attribute === FALSE) {
      unset($attribute);
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($attribute);
  }

  /**
   * Returns the next attribute for a given entry.
   *
   * This requires that the entries be loaded first.
   * Call self::load_entry_first() or the subsequent self::load_entry_next() before calling this.
   *
   * @return c_base_return_status|c_base_return_string
   *   The attribute string on success, FALSE otherwise.
   *
   * @see: ldap_next_attribute()
   * @see: self::load_attribute_first()
   * @see: self::load_entry_first()
   * @see: self::load_entry_next()
   */
  public function get_attribute_next() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_resource($this->entry)) {
      return c_base_return_error::s_false();
    }

    $attribute = ldap_next_attribute($this->ldap, $this->entry);
    if ($attribute === FALSE) {
      unset($attribute);
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($attribute);
  }

  /**
   * Loads the all attributes for a given entry.
   *
   * This requires that the entries be loaded first.
   * Call self::load_entry_first() or the subsequent self::load_entry_next() before calling this.
   *
   * @return c_base_return_status|c_base_return_array
   *   The an array of attribute strings on success, FALSE otherwise.
   *
   * @see: ldap_next_attribute()
   * @see: self::load_attribute_first()
   * @see: self::load_entry_first()
   * @see: self::load_entry_next()
   */
  public function get_attribute_all() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_resource($this->entry)) {
      return c_base_return_error::s_false();
    }

    $attributes = ldap_get_attributes($this->ldap, $this->entry);
    if ($attributes === FALSE) {
      unset($attributes);
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($attributes);
  }

  /**
   * Returns the directory name for a given entry.
   *
   * This requires that the entries be loaded first.
   * Call self::load_entry_first() or the subsequent self::load_entry_next() before calling this.
   *
   * @return c_base_return_status|c_base_return_string
   *   The attribute string on success, FALSE otherwise.
   *
   * @see: ldap_get_dn()
   * @see: self::load_attribute_first()
   * @see: self::load_entry_first()
   * @see: self::load_entry_next()
   */
  public function get_directory_name() {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_resource($this->entry)) {
      return c_base_return_error::s_false();
    }

    $directory_name = ldap_get_dn($this->ldap, $this->entry);
    if ($directory_name === FALSE) {
      unset($directory_name);
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($directory_name);
  }

  /**
   * Returns the values for a given attribute.
   *
   * @param string $attribute
   *   The attribute to get the values for.
   * @param bool $binary
   *   (optional) When TRUE, returns binary data. When FALSE, returns string data.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array on success, FALSE otherwise.
   *
   * @see: ldap_get_values()
   * @see: ldap_get_values_len()
   */
  public function get_values($attribute, $binary = FALSE) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_resource($this->entry)) {
      return c_base_return_error::s_false();
    }

    if (!is_string($attribute)) {
      return c_base_return_error::s_false();
    }

    if ($binary) {
      $values = ldap_get_values_len($this->ldap, $this->entry, $attribute);
    }
    else {
      $values = ldap_get_values($this->ldap, $this->entry, $attribute);
    }

    if (!is_array($values)) {
      unset($values);
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($values);
  }

  /**
   * Sorts all loaded entries by the given filter.
   *
   * @param string $attribute
   *   The attribute to perform the sort against.
   *
   * @see: ldap_sort()
   */
  public function do_sort($attribute) {
    if (!is_resource($this->ldap)) {
      return c_base_return_error::s_false();
    }

    if (!is_string($attribute)) {
      return c_base_return_error::s_false();
    }

    $status = ldap_sort($this->ldap, $this->value, $attribute);
    if ($status === FALSE) {
      unset($status);
      return c_base_return_error::s_false();
    }
    unset($status);

    return new c_base_return_true();
  }

  /**
   * Free's memory allocated to the class.
   *
   * @return c_base_return_status
   *   TRUE is returned on success, FALSE is returned if nothing to free.
   *   FALSE with the error flag set is returned on error.
   *
   * @see: ldap_free_result()
   */
  public function free_result() {
    if (!is_resource($this->value)) {
      return new c_base_return_false();
    }

    if (ldap_free_result($this->value)) {
      return new c_base_return_true();
    }

    return c_base_return_error::s_false();
  }
}
