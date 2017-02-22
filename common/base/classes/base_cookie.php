<?php
/**
 * @file
 * Provides a class for managing cookies.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic cookie management class.
 *
 * The cookie stored via this class will store the data as an array encoded in json format.
 *
 * This class overrides c_base_return_array() such that some of its return values are in a different form than expected.
 * This will utilize c_base_return_* as return values.
 *
 * @todo: review this class, for some reason I decided to use c_base_return_array as this class supertype.
 *        Is that a good idea, because it feels a bit abusive?
 *
 * @see: http://us.php.net/manual/en/features.cookies.php
 * @see: setcookie()
 */
class c_base_cookie extends c_base_return_array {
  const DEFAULT_LIFETIME = 172800; // 48 hours
  const DEFAULT_PATH = '/';
  const DEFAULT_JSON_ENCODE_DEPTH = 512;
  const CHECKSUM_ALGORITHM = 'sha256';

  private $name;
  private $secure;
  private $max_age;
  private $expires;
  private $path;
  private $domain;
  private $http_only;
  private $first_only;
  private $data;
  private $json_encode_depth;


  /**
   * Class constructor.
   */
  public function __construct() {
    $this->name = NULL;
    $this->secure = TRUE;
    $this->max_age = NULL;
    $this->expires = NULL;
    $this->path = self::DEFAULT_PATH;
    $this->domain = NULL;
    $this->http_only = FALSE;
    $this->first_only = TRUE;
    $this->data = array();
    $this->json_encode_depth = self::DEFAULT_JSON_ENCODE_DEPTH;

    $this->p_set_lifetime_default();

    parent::__construct();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->name);
    unset($this->secure);
    unset($this->max_age);
    unset($this->expires);
    unset($this->path);
    unset($this->domain);
    unset($this->http_only);
    unset($this->first_only);
    unset($this->data);
    unset($this->json_encode_depth);

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
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, array());
  }

  /**
   * Assigns the cookie name.
   *
   * @param string $name
   *   The cookie name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_name($name) {
    if (!is_string($name) || empty($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (mb_strlen($name) == 0 || preg_match('/^(\w|-)+$/iu', $name) != 1) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':format_name' => 'name', ':expected_format' => '. Alphanumeric and dash characters only', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    $this->name = preg_replace('/(^\s+)|(\s+$)/us', '', rawurlencode($name));
    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie name.
   *
   * @return c_base_return_string
   *   The cookie name string or NULL if undefined.
   */
  public function get_name() {
    return c_base_return_string::s_new($this->name);
  }

  /**
   * Assigns the cookie secure flag.
   *
   * This tells the browser that the cookie should only be used in SSL/HTTPS communications.
   * This is best left enabled for security reasons.
   *
   * @param bool $secure
   *   The security flag for the cookie.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_secure($secure) {
    if (!is_bool($secure)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'secure', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->secure = $secure;
    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie secure flag.
   *
   * @return c_base_return_bool
   *   The cookie secure flag setting.
   */
  public function get_secure() {
    // this flag should never be undefined, if it is NULL, then force the default.
    if (is_null($this->secure)) {
      $this->secure = TRUE;
    }

    return c_base_return_bool::s_new($this->secure);
  }

  /**
   * Assigns the expires timestamp of the cookie.
   *
   * This tells the browser until what date the cookie will be valid for.
   * This cannot be higher than php's 'session.cookie_lifetime' value.
   * If not specified, then the default is to use PHP's 'session.cookie_lifetime' based on when this class was created.
   *
   * A value of 0 in both max age and expires will designate a session cookie.
   *
   * @param int|null $expires
   *   A unix timestamp representing the date in which this cookie expires.
   *   A value of NULL disabled the usage of expires.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: self::set_max_age()
   */
  public function set_expires($expires) {
    if (!is_null($expires) && (!is_int($expires) || $this->expires < 0)) {
      if (is_string($this->max_age) && is_numeric($expires)) {
        $expires = (int) $expires;

        if ($expires < 0) {
          $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'expires', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
      }
      else {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'expires', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    $this->expires = $expires;
    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie expires timestamp.
   *
   * @return c_base_return_int
   *   The expiration unix timestamp for the cookie.
   *
   * @see: self::get_max_age()
   */
  public function get_expires() {
    if (is_null($this->expires) && is_null($this->max_age)) {
      $this->p_set_lifetime_default();
    }

    return c_base_return_int::s_new($this->expires);
  }

  /**
   * Assigns the max age of the cookie.
   *
   * This tells the browser how long the cookie will be valid for.
   * This cannot be higher than php's 'session.cookie_lifetime' value.
   * If not specified, then the default is to use PHP's 'session.cookie_lifetime' based on when this class was created.
   *
   * If max age is specified, but expires is not, then expires will be auto-calculated and provided for compatibility with old clients.
   *
   * A value of 0 in both max age and expires will designate a session cookie.
   *
   * @param int|null $max_age
   *   Number of seconds (max-age cookie flag).
   *   A value of NULL disabled the usage of max age.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: self::set_expires()
   */
  public function set_max_age($max_age) {
    if (!is_null($max_age) && (!is_int($max_age) || $this->max_age < 0)) {
      if (is_string($max_age) && is_numeric($max_age)) {
        $max_age = (int) $max_age;

        if ($max_age < 0) {
          $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'max_age', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
        }
      }
      else {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'max_age', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    $this->max_age = $max_age;

    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie max age value.
   *
   * @return c_base_return_int
   *   The expiration unix timestamp for the cookie.
   *   FALSE (without error bit) is returned if there is no value as per $timestamp parameter.
   *
   * @see: self::get_expires()
   */
  public function get_max_age() {
    if (is_null($this->expires) && is_null($this->max_age)) {
      $this->p_set_lifetime_default();
    }

    return c_base_return_int::s_new($this->max_age);
  }

  /**
   * Assigns the cookie path.
   *
   * @param string $path
   *   The path for the cookie.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_path($path) {
    if (!is_string($path) || empty($path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'path', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // sanitize the path string, only allowing the path portion of the url.
    $parsed = parse_url($path, PHP_URL_PATH);
    if ($parsed === FALSE) {
      unset($parsed);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'parse_url'), i_base_error_messages::OPERATION_FAILURE));
      return c_base_return_error::s_false($error);
    }

    $this->path = preg_replace('/(^\s+)|(\s+$)/us', '', $parsed);
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie path.
   *
   * @return c_base_return_string
   *   The cookie path.
   */
  public function get_path() {
    // this flag should never be undefined, if it is NULL, then force the default.
    if (is_null($this->path)) {
      $this->path = self::DEFAULT_PATH;
    }

    return c_base_return_string::s_new($this->path);
  }

  /**
   * Assigns the cookie domain.
   *
   * @param string $domain
   *   The domain for the cookie.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_domain($domain) {
    if (!is_string($domain) || empty($domain)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'domain', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // sanitize the domain string, only allowing the host portion of the url.
    $parsed = parse_url('stub://' . $domain, PHP_URL_HOST);
    if ($parsed === FALSE) {
      unset($parsed);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'parse_url', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->domain = preg_replace('/(^\s+)|(\s+$)/us', '', $parsed);
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie domain.
   *
   * @return c_base_return_string
   *   The cookie domain or null if undefined.
   */
  public function get_domain() {
    return c_base_return_string::s_new($this->domain);
  }

  /**
   * Assigns the cookie http only flag.
   *
   * Set this to TRUE to only allow http protocol to utilize this cookie.
   * According to the PHP documentation, this prohibits javascript from accessing the cookie.
   *
   * @param bool $http_only
   *   The http-only status for the cookie.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_http_only($http_only) {
    if (!is_bool($http_only)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'http_only', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->http_only = $http_only;
    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie http only flag.
   *
   * @return c_base_return_bool
   *   The cookie http only flag.
   */
  public function get_http_only() {
    // this flag should never be undefined, if it is NULL, then force the default.
    if (is_null($this->http_only)) {
      $this->http_only = FALSE;
    }

    return c_base_return_bool::s_new($this->http_only);
  }

  /**
   * Assigns the cookie http firsty-party flag.
   *
   * Set this to TRUE to only allow the cookie to be used as first party only.
   * According to the PHP documentation, this tells browsers to never allow this to be used as a thirdy-party cookie.
   *
   * @param bool $first_only
   *   The first-only status for the cookie.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_first_only($first_only) {
    if (!is_bool($first_only)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'first_only', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->first_only = $first_only;
    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie http only flag.
   *
   * @return c_base_return_bool
   *   The cookie http only flag.
   */
  public function get_first_only() {
    // this flag should never be undefined, if it is NULL, then force the default.
    if (is_null($this->http_only)) {
      $this->http_only = FALSE;
    }

    return c_base_return_bool::s_new($this->http_only);
  }

  /**
   * Assign the data.
   *
   * Cookies values associated with this class are only stored as an array.
   * Be sure to wrap your values in an array.
   * The array key 'checksum' will be created if one does not already exist when building the cookie.
   *
   * @param array $data
   *   Any value so long as it is an array.
   *   NULL is not allowed.
   *   FALSE with the error bit set is returned on error.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_data($data) {
    if (!is_array($data)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'data', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->data = $data;
    return new c_base_return_true();
  }

  /**
   * Return the data.
   *
   * @return c_base_return_array
   *   The value array stored within this class.
   *   NULL may be returned if there is no defined valid array.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_data() {
    if (!is_null($this->data) && !is_array($this->data)) {
      $this->data = array();
    }

    return c_base_return_array::s_new($this->data);
  }

  /**
   * Generate and return a cookie string prepared for HTTP header usage.
   *
   * @param bool $checksum
   *   (optional) When set to TRUE, the array will be converted to a json string and have a checksum created for it.
   *   This checksum value will then be placed inside the array and a final json string will be submitted.
   *
   * @return c_base_return_string
   *   The value array stored within this class.
   *   NULL may be returned if there is no defined valid array.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_cookie($checksum = TRUE) {
    if (!is_bool($checksum)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'checksum', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->data)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->data', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $cookie = $this->p_build_cookie($checksum);
    if ($cookie instanceof c_base_return_false) {
      return c_base_return_error::s_false($cookie->get_error());
    }

    return $cookie;
  }

  /**
   * Assigns the default json encode depth to be used.
   *
   * Sets the maximum json encode depth used when processing cookie data via json_encode().
   *
   * @param int $json_encode_depth
   *   The json encode max depth.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: json_encode()
   */
  public function set_json_encode_depth($json_encode_depth) {
    if (!is_int($json_encode_depth) || $json_encode_depth < 1) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'json_encode_depth', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->json_encode_depth = $json_encode_depth;
    return new c_base_return_true();
  }

  /**
   * Returns the stored cookie json_encode_depth.
   *
   * @return c_base_return_int
   *   The cookie json_encode_depth string or NULL if undefined.
   *   FALSE with the error bit set is returned on error.
   */
  public function get_json_encode_depth() {
    return c_base_return_int::s_new($this->json_encode_depth);
  }

  /**
   * Save the cookie to the HTTP headers for sending to the client.
   *
   * This function sends an HTTP header and therefore should only be used when ready to send headers.
   *
   * @param bool $checksum
   *   (optional) When set to TRUE, the array will be converted to a json string and have a checksum created for it.
   *   This checksum value will then be placed inside the array and a final json string will be submitted.
   *
   *   Warning: any top-level key in the array with the name of 'checksum' will be lost when using this.
   * @param bool $force
   *   (optional) If TRUE, will send cookie header even if header_sent() returns TRUE.
   *   If FALSE, cookie headers are not sent when headers_sent() returns TRUE.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit is returned on error.
   *   FALSE without error bit set is returned when the php headers have already been sent according to headers_sent().
   *
   * @see: header()
   * @see: headers_sent().
   */
  public function do_push($checksum = TRUE, $force = FALSE) {
    if (!is_bool($checksum)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'checksum', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($force)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'force', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (is_null($this->data)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->data', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    $cookie = $this->p_build_cookie($checksum);
    if ($cookie instanceof c_base_return_false) {
      return c_base_return_error::s_false($cookie->get_error());
    }

    if (headers_sent() && !$force) {
      return new c_base_return_false();
    }

    header($cookie->get_value_exact(), FALSE);

    unset($cookie);
    unset($data);

    return new c_base_return_true();
  }

  /**
   * Retrieve the cookie from the HTTP headers sent by the client.
   *
   * This class object will be populated with the cookies settings.
   * The cookie data will be cleared if the cookie exists.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function do_pull() {
    if (!isset($_COOKIE) || !array_key_exists($this->name, $_COOKIE)) {
      // This is not an error, but there is no cookie to pull.
      // simply return false without the error flag set.
      return new c_base_return_false();
    }

    $json = rawurldecode($_COOKIE[$this->name]);
    $data = json_decode($json, TRUE);
    unset($json);

    if ($data === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'json_decode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->data = $data;
    unset($data);

    return new c_base_return_true();
  }

  /**
   * Validate a checksum key.
   *
   * This is only meaningful when called after self::do_pull() is used.
   *
   * If a checksum key exists, will validate that the contents of the data are consistent with the checksum.
   * This is useful to protect data from alterations, be it defect or accident.
   * This does not protect against malicious activities because the malicious user could simply regenerate the checksum after their changes.
   *
   * @return c_base_return_status
   *   TRUE when the checksum validates, FALSE when the checksum fails or there is no checksum.
   *   On error FALSE is returned with the error bit set.
   */
  public function validate() {
    if (!is_array($this->data)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':variable_name' => 'this->data')), i_base_error_messages::INVALID_VARIABLE);
      return c_base_return_error::s_false($error);
    }

    if (!array_key_exists('checksum', $this->data)) {
      return new c_base_return_false();
    }

    $checksum = $this->p_build_checksum();
    if ($this->data['checksum'] == $checksum) {
      unset($checksum);
      return new c_base_return_true();
    }
    unset($checksum);

    return new c_base_return_false();
  }

  /**
   * Deletes the cookie by setting both the expires and max-age to -1.
   *
   * This does not need to be called when updating the cookie.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *
   * @see: self::push()
   */
  public function delete() {
    $original_max_age = $this->max_age;
    $original_expires = $this->expires;

    $this->max_age = -1;
    $this->expires = -1;

    $result = $this->push(FALSE);

    $this->max_age = $original_max_age;
    $this->expires = $original_expires;

    unset($original_max_age);
    unset($original_expires);

    return $result;
  }

  /**
   * Builds a checksum of the data array.
   *
   * This does not assign the checksum to the array.
   * The checksum is only assigned by the do_push() or do_pull() functions.
   *
   * If the values are changed after this call, then this checksum will be invalid.
   *
   * @see: self::do_pull()
   * @see: self::do_push()
   */
  public function build_checksum() {
    $checksum = $this->p_build_checksum();
    if (is_string($checksum)) {
      return c_base_return_string::s_new($checksum);
    }
    unset($checksum);

    $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_build_checksum', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assigns a default value for the expiration based on php's session.cookie_lifetime.
   */
  private function p_set_lifetime_default() {
    $lifetime = ini_get('session.cookie_lifetime');
    if ($lifetime <= 0) {
      $lifetime = self::DEFAULT_LIFETIME;
    }

    $this->max_age = $lifetime;
    unset($lifetime);
  }

  /**
   * Generates a checksum of the data array.
   *
   * Any existing checksum key is preserved.
   *
   * @return string
   *   A generated checksum.
   *
   * @see: hash()
   */
  private function p_build_checksum() {
    if (!is_array($this->data)) {
      $this->data = array();
    }

    $has_checksum = array_key_exists('checksum', $this->data);
    $checksum = NULL;
    if ($has_checksum) {
      $checksum = $this->data['checksum'];
      unset($this->data['checksum']);
    }

    $json = json_encode($this->data, 0, $this->json_encode_depth);
    if ($json === FALSE) {
      if ($has_checksum) {
        $this->data['checksum'] = $checksum;
      }

      unset($has_checksum);
      unset($checksum);
      unset($json);

      return NULL;
    }

    $generated = hash(c_base_cookie::CHECKSUM_ALGORITHM, $json);
    if ($has_checksum) {
      $this->data['checksum'] = $checksum;
    }

    unset($has_checksum);
    unset($checksum);
    unset($json);

    return $generated;
  }

  /**
   * Build the cookie HTTP headers for sending to the client.
   *
   * This function sends an HTTP header and therefore should only be used when ready to send headers.
   *
   * Both name and value are required to be set before calling this function.
   *
   * The functions setcookie() and setrawcookie() do not provide advanced customization.
   * Instead of using those functions, use header() to directly generate the cookie.
   *
   * @param bool $checksum
   *   (optional) When set to TRUE, the array will be converted to a json string and have a checksum created for it.
   *   This checksum value will then be placed inside the array and a final json string will be submitted.
   *
   *   Warning: any top-level key in the array with the name of 'checksum' will be lost when using this.
   *
   * @return c_base_return_string|c_base_return_status
   *   A generated cookie string is returned on success.
   *   FALSE with error bit is returned on error.
   *   FALSE without error bit set is returned when the php headers have already been sent according to headers_sent().
   *
   * @see: self::validate()
   * @see: setcookie()
   * @see: setrawcookie()
   * @see: header()
   * @see: headers_sent().
   */
  private function p_build_cookie($checksum = TRUE) {
    if ($checksum) {
      unset($this->data['checksum']);
      $this->data['checksum'] = $this->p_build_checksum();

      if (is_null($this->data['checksum'])) {
        unset($this->data['checksum']);
        $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'this->p_build_checksum', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }
    }

    $json = json_encode($this->data, 0, $this->json_encode_depth);
    if ($json === FALSE) {
      unset($json);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':operation_name' => 'json_encode', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $data = rawurlencode(preg_replace('/(^\s+)|(\s+$)/us', '', $json));
    unset($json);

    $cookie = 'Set-Cookie: ' . rawurlencode($this->name) . '=' . $data . ';';

    if (!is_null($this->domain)) {
      $cookie .= ' domain=' . $this->domain . ';';
    }

    if (!is_null($this->path)) {
      $cookie .= ' path=' . $this->path . ';';
    }

    if (!is_null($this->max_age)) {
      $cookie .= ' max-age=' . $this->max_age . ';';

      // provide an expires for compatibility purposes if one is not specified.
      if (is_null($this->expires)) {
        $cookie .= ' expires=' . gmdate('D, d-M-Y H:i:s T', strtotime('+' . $this->max_age . ' seconds')) . ';';
      }
    }

    if (!is_null($this->expires)) {
      $cookie .= ' expires=' . gmdate('D, d-M-Y H:i:s T', $this->expires) . ';';
    }

    if ($this->secure) {
      $cookie .= ' secure;';
    }

    if ($this->http_only) {
      $cookie .= ' httponly;';
    }

    if ($this->first_only) {
      $cookie .= ' first-party;';
    }

    return c_base_return_string::s_new($cookie);
  }
}
