<?php
/**
 * @file
 * Provides a class for managing the HTTP protocol.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_charset.php');
require_once('common/base/classes/base_rfc_string.php');
require_once('common/base/classes/base_utf8.php');
require_once('common/base/classes/base_email.php');
require_once('common/base/classes/base_languages.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_mime.php');

/**
 * A generic class for managing the HTTP protocol.
 *
 * @see: http://www.iana.org/assignments/message-headers/message-headers.xhtml
 *
 * @require class base_email
 * @require class base_rfc_string
 * @require class base_utf8
 */
class c_base_http extends c_base_rfc_string {
  // standard request headers
  const REQUEST_ACCEPT                         = 1;
  const REQUEST_ACCEPT_CHARSET                 = 2;
  const REQUEST_ACCEPT_ENCODING                = 3;
  const REQUEST_ACCEPT_LANGUAGE                = 4;
  const REQUEST_ACCEPT_DATETIME                = 5;
  const REQUEST_ACCESS_CONTROL_REQUEST_METHOD  = 6;
  const REQUEST_ACCESS_CONTROL_REQUEST_HEADERS = 7;
  const REQUEST_AUTHORIZATION                  = 8;
  const REQUEST_CACHE_CONTROL                  = 9;
  const REQUEST_CONNECTION                     = 10;
  const REQUEST_COOKIE                         = 11;
  const REQUEST_CONTENT_LENGTH                 = 12;
  const REQUEST_CONTENT_TYPE                   = 13;
  const REQUEST_DATE                           = 14;
  const REQUEST_EXPECT                         = 15;
  const REQUEST_FROM                           = 16;
  const REQUEST_HOST                           = 17;
  const REQUEST_IF_MATCH                       = 18;
  const REQUEST_IF_MODIFIED_SINCE              = 19;
  const REQUEST_IF_NONE_MATCH                  = 20;
  const REQUEST_IF_RANGE                       = 21;
  const REQUEST_IF_UNMODIFIED_SINCE            = 22;
  const REQUEST_MAX_FORWARDS                   = 23;
  const REQUEST_ORIGIN                         = 24;
  const REQUEST_PRAGMA                         = 25;
  const REQUEST_PROXY_AUTHORIZATION            = 26;
  const REQUEST_RANGE                          = 27;
  const REQUEST_REFERER                        = 28;
  const REQUEST_TE                             = 29;
  const REQUEST_USER_AGENT                     = 30;
  const REQUEST_UPGRADE                        = 31;
  const REQUEST_VIA                            = 32;
  const REQUEST_WARNING                        = 33;
  const REQUEST_UNKNOWN                        = 999;

  // non-standard, but supported, request headers
  const REQUEST_X_REQUESTED_WITH        = 1001;
  const REQUEST_X_FORWARDED_FOR         = 1002;
  const REQUEST_X_FORWARDED_HOST        = 1003;
  const REQUEST_X_FORWARDED_PROTO       = 1004;
  const REQUEST_CHECKSUM_HEADER         = 1005;
  const REQUEST_CHECKSUM_HEADERS        = 1006;
  const REQUEST_CHECKSUM_CONTENT        = 1007;
  const REQUEST_CONTENT_ENCODING        = 1008;
  const REQUEST_SIGNATURE_PG            = 1009;

  // standard response headers
  const RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN      = 1;
  const RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS = 2;
  const RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS    = 3;
  const RESPONSE_ACCESS_CONTROL_MAX_AGE           = 4;
  const RESPONSE_ACCESS_CONTROL_ALLOW_METHODS     = 5;
  const RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS     = 6;
  const RESPONSE_ACCEPT_PATCH                     = 7;
  const RESPONSE_ACCEPT_RANGES                    = 8;
  const RESPONSE_AGE                              = 9;
  const RESPONSE_ALLOW                            = 10;
  const RESPONSE_CACHE_CONTROL                    = 11;
  const RESPONSE_CONNECTION                       = 12;
  const RESPONSE_CONTENT_DISPOSITION              = 13;
  const RESPONSE_CONTENT_ENCODING                 = 14;
  const RESPONSE_CONTENT_LANGUAGE                 = 15;
  const RESPONSE_CONTENT_LENGTH                   = 16;
  const RESPONSE_CONTENT_LOCATION                 = 17;
  const RESPONSE_CONTENT_RANGE                    = 18;
  const RESPONSE_CONTENT_TYPE                     = 19;
  const RESPONSE_DATE                             = 20;
  const RESPONSE_ETAG                             = 21;
  const RESPONSE_EXPIRES                          = 22;
  const RESPONSE_LAST_MODIFIED                    = 23;
  const RESPONSE_LINK                             = 24;
  const RESPONSE_LOCATION                         = 25;
  const RESPONSE_PRAGMA                           = 26;
  const RESPONSE_PROXY_AUTHENTICATE               = 27;
  const RESPONSE_PUBLIC_KEY_PINS                  = 28;
  const RESPONSE_RETRY_AFTER                      = 29;
  const RESPONSE_SERVER                           = 30;
  const RESPONSE_SET_COOKIE                       = 31;
  const RESPONSE_STATUS                           = 32;
  const RESPONSE_STRICT_TRANSPORT_SECURITY        = 33;
  const RESPONSE_TRAILER                          = 34;
  const RESPONSE_TRANSFER_ENCODING                = 35;
  const RESPONSE_UPGRADE                          = 36;
  const RESPONSE_VARY                             = 37;
  const RESPONSE_WARNING                          = 38;
  const RESPONSE_WWW_AUTHENTICATE                 = 39;
  const RESPONSE_PROTOCOL                         = 40;

  // non-standard, but supported, response headers.
  const RESPONSE_REFRESH                   = 1001;
  const RESPONSE_X_CONTENT_SECURITY_POLICY = 1002;
  const RESPONSE_X_CONTENT_TYPE_OPTIONS    = 1003;
  const RESPONSE_X_UA_COMPATIBLE           = 1004;
  const RESPONSE_CHECKSUM_HEADER           = 1005;
  const RESPONSE_CHECKSUM_HEADERS          = 1006;
  const RESPONSE_CHECKSUM_CONTENT          = 1007;
  const RESPONSE_CONTENT_REVISION          = 1008;
  const RESPONSE_DATE_ACTUAL               = 1009;

  // accept delimiters (the syntax for the separators can be confusing and misleading)
  const DELIMITER_ACCEPT_SUP   = ',';
  const DELIMITER_ACCEPT_SUB   = ';';
  const DELIMITER_ACCEPT_SUB_0 = 'q';
  const DELIMITER_ACCEPT_SUB_1 = '=';

  const ACCEPT_LANGUAGE_CLASS_DEFAULT = 'c_base_language_us_limited';

  // cache control options
  const CACHE_CONTROL_NO_CACHE         = 1;
  const CACHE_CONTROL_NO_STORE         = 2;
  const CACHE_CONTROL_NO_TRANSFORM     = 3;
  const CACHE_CONTROL_MAX_AGE          = 4;
  const CACHE_CONTROL_MAX_AGE_S        = 5;
  const CACHE_CONTROL_MAX_STALE        = 6;
  const CACHE_CONTROL_MIN_FRESH        = 7;
  const CACHE_CONTROL_ONLY_IF_CACHED   = 8;
  const CACHE_CONTROL_PUBLIC           = 9;
  const CACHE_CONTROL_PRIVATE          = 10;
  const CACHE_CONTROL_MUST_REVALIDATE  = 11;
  const CACHE_CONTROL_PROXY_REVALIDATE = 12;

  // supported checksums
  const CHECKSUM_MD2      = 1;
  const CHECKSUM_MD4      = 2;
  const CHECKSUM_MD5      = 3;
  const CHECKSUM_SHA1     = 4;
  const CHECKSUM_SHA224   = 5;
  const CHECKSUM_SHA256   = 6;
  const CHECKSUM_SHA384   = 7;
  const CHECKSUM_SHA512   = 8;
  const CHECKSUM_CRC32    = 9;
  const CHECKSUM_PG       = 10; // such as: GPG or PGP.

  // checksum actions
  const CHECKSUM_ACTION_NONE = 0;
  const CHECKSUM_ACTION_AUTO = 1;
  const CHECKSUM_ACTION_MANUAL = 2;

  // checksum whats
  const CHECKSUM_WHAT_FULL     = 1;
  const CHECKSUM_WHAT_PARTIAL  = 2;
  const CHECKSUM_WHAT_SIGNED   = 3;
  const CHECKSUM_WHAT_UNSIGNED = 4;

  // uri path types
  const URI_PATH_SITE = 1; // such as: '//example.com/main/index.html'
  const URI_PATH_BASE = 2; // such as: '/main/index.html'
  const URI_PATH_THIS = 3; // such as: 'index.html'

  // uri host ip addresses
  const URI_HOST_IPV4 = 1;
  const URI_HOST_IPV6 = 2;
  const URI_HOST_IPVX = 3;
  const URI_HOST_NAME = 4;

  // transfer encoding choices
  const ENCODING_CHUNKED  = 1;
  const ENCODING_COMPRESS = 2;
  const ENCODING_DEFLATE  = 3;
  const ENCODING_GZIP     = 4; // Compression Options: -1 -> 9.
  const ENCODING_BZIP     = 5; // Compression Options: 1 -> 9.
  const ENCODING_LZO      = 6; // Compression Options: LZO1_99, LZO1A_99, LZO1B_999, LZO1C_999, LZO1F_999, LZO1X_999, LZO1Y_999, LZO1Z_999, LZO2A_999 (and many more).
  const ENCODING_XZ       = 7;
  const ENCODING_EXI      = 8;
  const ENCODING_IDENTITY = 9;
  const ENCODING_SDCH     = 10;
  const ENCODING_PG       = 11;

  // timestamps
  const TIMESTAMP_RFC_5322 = 'D, d M Y H:i:s T'; // rfc5322 is the preferred/recommended format.
  const TIMESTAMP_RFC_1123 = 'D, d M Y H:i:s O';
  const TIMESTAMP_RFC_850  = 'l, d-M-y H:i:s T';

  // http methods
  const HTTP_METHOD_NONE    = 0;
  const HTTP_METHOD_GET     = 1; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_HEAD    = 2; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_POST    = 3; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_PUT     = 4; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_DELETE  = 5; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_TRACE   = 6; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Security
  const HTTP_METHOD_OPTIONS = 7; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_CONNECT = 8;
  const HTTP_METHOD_PATCH   = 9; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Safe_methods
  const HTTP_METHOD_TRACK   = 10; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Security
  const HTTP_METHOD_DEBUG   = 11; // https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Security

  private $headers;
  private $headers_sent;
  private $request;
  private $request_time;
  private $response;

  private $content;
  private $content_is_file;
  private $buffer_enabled;

  private $language_class;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->headers = NULL;
    $this->headers_sent = FALSE;
    $this->request = array();
    $this->request_time = NULL;
    $this->response = array();

    $this->content = NULL;
    $this->content_is_file = NULL;
    $this->buffer_enabled = FALSE;

    $this->language_class = NULL;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->headers);
    unset($this->headers_sent);
    unset($this->request);
    unset($this->request_time);
    unset($this->response);

    unset($this->content);
    unset($this->content_is_file);
    unset($this->buffer_enabled);

    unset($this->language_class);
  }

  /**
   * Get the HTTP request array.
   *
   * Load the entire HTTP request array or a specific request field.
   *
   * @param int|null $header_name
   *   (optional) The numeric id of the request or NULL to load all requests.
   *
   * @return c_base_return_array|c_base_return_status
   *   The HTTP request array or an array containing the request field information.
   *   FALSE with error bit set is returned on error.
   */
  public function get_request($header_name = NULL) {
    if (is_null($header_name)) {
      return c_base_return_array::s_new($this->request);
    }

    if (!is_int($header_name) || !array_key_exists($header_name, $this->request)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->request[$header_name]);
  }

  /**
   * Get the HTTP response array.
   *
   * Load the entire HTTP response array.
   *
   * @return c_base_return_array
   *   The HTTP response array.
   */
  public function get_response() {
    return c_base_return_array::s_new($this->response);
  }

  /**
   * Assign the class name as the language class string.
   *
   * @param string $class_name
   *   A string name representing an object that is a sub-class of i_base_language.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   */
  public function set_language_class($class_name) {
    if (!is_string($class_name) || !is_subclass_of('i_base_language', $class_name) ) {
      return c_base_return_error::s_false();
    }

    $this->language_class = $class_name;
    return new c_base_return_true();
  }

  /**
   * Get the language class string currently assigned to this class.
   *
   * @return c_base_return_string
   *   The language class string.
   */
  public function get_language_class() {
    return c_return_status_string::s_new($this->language_class);
  }

  /**
   * Get the HTTP request timestamp..
   *
   * @return c_base_return_float|c_base_return_status
   *   The HTTP request time.
   *   FALSE without error bit is returned when the request timestamp has not yet been loaded
   */
  public function get_request_time() {
    if (is_null($this->request_time)) {
      return new c_base_return_false();
    }

    return c_base_return_float::s_new($this->request_time);
  }

  /**
   * Load, process, and interpret all of the supported http request headers.
   */
  public function do_load_request() {
    if (!is_array($this->headers)) {
      $this->p_get_all_headers();
    }

    // force the request array to be defined.
    $this->request = array();

    $initialize_keys = array(
      self::REQUEST_ACCEPT,
      self::REQUEST_ACCEPT_CHARSET,
      self::REQUEST_ACCEPT_ENCODING,
      self::REQUEST_ACCEPT_LANGUAGE,
      self::REQUEST_ACCEPT_DATETIME,
      self::REQUEST_ACCESS_CONTROL_REQUEST_METHOD,
      self::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS,
      self::REQUEST_AUTHORIZATION,
      self::REQUEST_CACHE_CONTROL,
      self::REQUEST_CONNECTION,
      self::REQUEST_COOKIE,
      self::REQUEST_CONTENT_LENGTH,
      self::REQUEST_CONTENT_TYPE,
      self::REQUEST_DATE,
      self::REQUEST_EXPECT,
      self::REQUEST_FROM,
      self::REQUEST_HOST,
      self::REQUEST_IF_MATCH,
      self::REQUEST_IF_MODIFIED_SINCE,
      self::REQUEST_IF_NONE_MATCH,
      self::REQUEST_IF_RANGE,
      self::REQUEST_IF_UNMODIFIED_SINCE,
      self::REQUEST_MAX_FORWARDS,
      self::REQUEST_ORIGIN,
      self::REQUEST_PRAGMA,
      self::REQUEST_PROXY_AUTHORIZATION,
      self::REQUEST_RANGE,
      self::REQUEST_REFERER,
      self::REQUEST_TE,
      self::REQUEST_USER_AGENT,
      self::REQUEST_UPGRADE,
      self::REQUEST_VIA,
      self::REQUEST_WARNING,
      self::REQUEST_X_REQUESTED_WITH,
      self::REQUEST_X_FORWARDED_FOR,
      self::REQUEST_X_FORWARDED_HOST,
      self::REQUEST_X_FORWARDED_PROTO,
      self::REQUEST_CHECKSUM_HEADER,
      self::REQUEST_CHECKSUM_HEADERS,
      self::REQUEST_CHECKSUM_CONTENT,
    );

    foreach ($initialize_keys as $initialize_key) {
      $this->request[$initialize_key] = array(
        'defined' => FALSE,
        'invalid' => FALSE,
        'data' => array(
        ),
      );
    }
    unset($initialize_key);
    unset($initialize_keys);

    // build an array of headers so that unknown/unsupported headers can still be processed.
    $headers = array_flip(array_keys($this->headers));

    // additional keys for specific cases.
    $this->request[self::REQUEST_ACCEPT]['types'] = array();


    if (array_key_exists('accept', $this->headers)) {
      $this->p_load_request_accept();
      unset($headers['accept']);
    }

    if (array_key_exists('accept_language', $this->headers)) {
      $this->p_load_request_accept_language();
      unset($headers['accept_language']);
    }

    if (array_key_exists('accept_encoding', $this->headers)) {
      $this->p_load_request_accept_encoding();
      unset($headers['accept_encoding']);
    }

    if (array_key_exists('accept_charset', $this->headers)) {
      $this->p_load_request_accept_charset();
      unset($headers['accept_charset']);
    }

    if (array_key_exists('accept_datetime', $this->headers)) {
      $this->p_load_request_accept_datetime();
      unset($headers['accept_datetime']);
    }

    // @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
    if (array_key_exists('access_control_request_method', $this->headers)) {
      $this->p_load_request_rawish('access_control_request_method', $this->REQUEST_ACCESS_CONTROL_REQUEST_METHOD, 256);
      unset($headers['access_control_request_method']);
    }

    // @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
    if (array_key_exists('access_control_request_headers', $this->headers)) {
      $this->p_load_request_rawish('access_request_allow_headers', $this->REQUEST_ACCESS_CONTROL_REQUEST_HEADERS, 256);
      unset($headers['access_control_request_headers']);
    }

    // @see: https://tools.ietf.org/html/rfc7235#section-4.2
    if (array_key_exists('authorization', $this->headers)) {
      $this->p_load_request_rawish('authorization', $this->REQUEST_AUTHORIZATION, 4096);
      unset($headers['authorization']);
    }

    if (array_key_exists('cache_control', $this->headers)) {
      $this->p_load_request_cache_control();
      unset($headers['cache_control']);
    }

    if (array_key_exists('connection', $this->headers)) {
      $this->p_load_request_connection();
      unset($headers['connection']);
    }

    if (array_key_exists('pragma', $this->headers)) {
      $this->p_load_request_pragma();
      unset($headers['pragma']);
    }

    if (is_array($_COOKIE) && !empty($_COOKIE)) {
      $this->p_load_request_cookies();
      unset($headers['cookie']);
    }

    if (array_key_exists('content_length', $this->headers)) {
      $this->p_load_request_content_length();
      unset($headers['content_length']);
    }

    if (array_key_exists('content_md5', $this->headers)) {
      $this->p_load_request_content_md5();
      unset($headers['content_md5']);
    }

    if (array_key_exists('content_checksum', $this->headers)) {
      $this->p_load_request_content_checksum();
      unset($headers['content_checksum']);
    }

    if (array_key_exists('content_type', $this->headers)) {
      $this->p_load_request_content_type();
      unset($headers['content_type']);
    }

    if (array_key_exists('date', $this->headers)) {
      $this->p_load_request_date();
      unset($headers['date']);
    }

    if (array_key_exists('expect', $this->headers)) {
      $this->p_load_request_expect();
      unset($headers['expect']);
    }

    if (array_key_exists('from', $this->headers)) {
      $this->p_load_request_from();
      unset($headers['from']);
    }

    if (array_key_exists('host', $this->headers)) {
      $this->p_load_request_host();
      unset($headers['host']);
    }

    if (array_key_exists('if_match', $this->headers)) {
      $this->p_load_request_if_match();
      unset($headers['if_match']);
    }

    if (array_key_exists('if_none_match', $this->headers)) {
      $this->p_load_request_if_none_match();
      unset($headers['if_none_match']);
    }

    if (array_key_exists('if_modified_since', $this->headers)) {
      $this->p_load_request_if_modified_since();
      unset($headers['if_modified_since']);
    }

    if (array_key_exists('if_unmodified_since', $this->headers)) {
      $this->p_load_request_if_unmodified_since();
      unset($headers['if_unmodified_since']);
    }

    if (array_key_exists('if_range', $this->headers)) {
      $this->p_load_request_if_range();
      unset($headers['if_range']);
    }

    if (array_key_exists('range', $this->headers)) {
      $this->p_load_request_range();
      unset($headers['range']);
    }

    if (array_key_exists('max_forwards', $this->headers)) {
      $this->p_load_request_max_forwards();
      unset($headers['max_forwards']);
    }

    if (array_key_exists('origin', $this->headers)) {
      $this->p_load_request_origin();
      unset($headers['origin']);
    }

    // @see: https://tools.ietf.org/html/rfc7235#section-4.4
    if (array_key_exists('proxy_authorization', $this->headers)) {
      $this->p_load_request_rawish('proxy_authorization', $this->REQUEST_PROXY_AUTHORIZATION, 4096);
      unset($headers['proxy_authorization']);
    }

    if (array_key_exists('referer', $this->headers)) {
      $this->p_load_request_referer();
      unset($headers['referer']);
    }

    if (array_key_exists('te', $this->headers)) {
      $this->p_load_request_te();
      unset($headers['te']);
    }

    if (array_key_exists('user_agent', $this->headers)) {
      $this->p_load_request_user_agent();
      unset($headers['user_agent']);
    }

    if (array_key_exists('upgrade', $this->headers)) {
      $this->p_load_request_upgrade();
      unset($headers['upgrade']);
    }

    if (array_key_exists('via', $this->headers)) {
      $this->p_load_request_via();
      unset($headers['via']);
    }

    if (array_key_exists('warning', $this->headers)) {
      $this->p_load_request_warning();
      unset($headers['warning']);
    }

    if (array_key_exists('x_requested_with', $this->headers)) {
      $this->p_load_request_rawish('x_requested_with', $this->REQUEST_X_REQUESTED_WITH, 64);
      unset($headers['x_requested_with']);
    }

    if (array_key_exists('x_forwarded_for', $this->headers)) {
      $this->p_load_request_rawish('x_forwarded_for', $this->REQUEST_X_FORWARDED_for, 512);
      unset($headers['x_forwarded_for']);
    }

    if (array_key_exists('x_forwarded_host', $this->headers)) {
      $this->p_load_request_rawish('x_forwarded_host', $this->REQUEST_X_FORWARDED_HOST, 512);
      unset($headers['x_forwarded_host']);
    }

    if (array_key_exists('x_forwarded_proto', $this->headers)) {
      $this->p_load_request_rawish('x_forwarded_proto', $this->REQUEST_X_FORWARDED_PROTO, 64);
      unset($headers['x_forwarded_proto']);
    }

    if (array_key_exists('checksum_header', $this->headers)) {
      $this->p_load_request_checksum_header();
      unset($headers['checksum_header']);
    }

    if (array_key_exists('checksum_headers', $this->headers)) {
      $this->p_load_request_checksum_headers();
      unset($headers['checksum_headers']);
    }

    if (array_key_exists('checksum_content', $this->headers)) {
      $this->p_load_request_checksum_content();
      unset($headers['checksum_content']);
    }


    // process all unknown headers and store them in the unknown key, with a max size of 256.
    if (!empty($headers)) {
      foreach ($headers as $header_name => $header_value) {
        $this->p_load_request_unknown($header_name, self::REQUEST_UNKNOWN, 256);
      }
      unset($header_name);
      unset($header_value);
    }
    unset($headers);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-origin.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @param string $uri
   *   The uri to assign to the specified header.
   *   The wildcard character '*' is also allowed.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function set_response_access_control_allow_origin($uri) {
    if (!is_string($uri)) {
      return c_base_return_error::s_false();
    }

    if ($uri == c_base_ascii::ASTERISK) {
      $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN] = array('wildcard' => TRUE);
    }
    else {
      $parsed = $this->p_parse_uri($uri);
      if ($parsed['invalid']) {
        unset($parsed);
        return c_base_return_error::s_false();
      }
      unset($parsed['invalid']);

      $parsed['wildcard'] = FALSE;
      $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN] = $parsed;
      unset($parsed);
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-credentials.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @param bool $allow_credentials
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function set_response_access_control_allow_credentials($allow_credentials) {
    if (!is_bool($allow_credentials)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS] = $allow_credentials;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-expose-headers.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   * @todo: should this be written in the same way as set_response_allow()?
   *
   * @param string $header_name
   *   The header name to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function set_response_access_control_expose_headers($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_prepare_token($header_name);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    if ($append) {
      $this->response[self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS][$parsed] = $parsed;
    }
    else {
      $this->response[self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS] = array($parsed => $parsed);
    }
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-max-age.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @param int|float $timestamp
   *   The timestamp to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function set_response_access_control_max_age($timestamp) {
    if (!is_int($timestamp) && !is_float($timestamp)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_ACCESS_CONTROL_MAX_AGE] = $timestamp;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-methods.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @param int $method
   *   The code representing the method to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function set_response_access_control_allow_methods($method, $append = TRUE) {
    if (!is_int($uri)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    if ($append) {
      $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS][$method] = $method;
    }
    else {
      $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] = array($method => $method);
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-headers.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @param string $header_name
   *   The header name to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function set_response_access_control_allow_headers($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_prepare_token($header_name);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    if ($append) {
      $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS][$parsed] = $parsed;
    }
    else {
      $this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS] = array($parsed => $parsed);
    }
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: accept-patch.
   *
   * @param string $media_type
   *   The media type to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::pr_rfc_string_is_media_type()
   * @see: https://tools.ietf.org/html/rfc5789#section-3.1
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  public function set_response_accept_patch($media_type, $append = TRUE) {
    if (!is_string($media_type)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    $text = $this->pr_rfc_string_prepare($media_type);
    if ($text['invalid']) {
      unset($text);

      return c_base_return_error::s_false();
    }

    $parsed = $this->pr_rfc_string_is_media_type($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      unset($parsed);

      return c_base_return_error::s_false();
    }
    unset($parsed['invalid']);
    unset($parsed['current']);

    if ($append) {
      $this->response[self::RESPONSE_ACCEPT_PATCH][] = $parsed;
    }
    else {
      $this->response[self::RESPONSE_ACCEPT_PATCH] = array($parsed);
    }
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: accept_ranges.
   *
   * @param string $ranges
   *   An string representing ranges to assign to the specified header with the following structure:
   *   - 1*(tchar)
   *
   *   Common ranges are:
   *   - bytes
   *   - none
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-2.3
   * @see: https://tools.ietf.org/html/rfc7233#section-3.1
   */
  public function set_response_accept_ranges($ranges) {
    if (!is_string($ranges)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_prepare_token($ranges);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_ACCEPT_RANGES] = $parsed;
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: age.
   *
   * @param int $seconds
   *   The number of seconds to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.1
   */
  public function set_response_age($seconds) {
    if (!is_int($seconds)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_AGE] = $seconds;
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: allow.
   *
   * When HTTP_METHOD_NONE is passed, this forces all existing values to be removed ($append will be ignored).
   *
   * @param int $allow
   *   A code representing the specific allow method.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.1
   */
  public function set_response_allow($allow, $append = TRUE) {
    if (!is_int($allow)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    switch ($allow) {
      case self::HTTP_METHOD_NONE:
      case self::HTTP_METHOD_GET:
      case self::HTTP_METHOD_HEAD:
      case self::HTTP_METHOD_POST:
      case self::HTTP_METHOD_PUT:
      case self::HTTP_METHOD_DELETE:
      case self::HTTP_METHOD_TRACE:
      case self::HTTP_METHOD_OPTIONS:
      case self::HTTP_METHOD_CONNECT:
      case self::HTTP_METHOD_PATCH:
      case self::HTTP_METHOD_TRACK:
      case self::HTTP_METHOD_DEBUG:
        break;
      default:
        return c_base_return_error::s_false();
    }

    if ($allow == self::HTTP_METHOD_NONE) {
      $this->response[self::RESPONSE_ALLOW] = array($allow => $allow);
      return new c_base_return_true();
    }

    if ($append) {
      $this->response[self::RESPONSE_ALLOW][$allow] = $allow;
    }
    else {
      $this->response[self::RESPONSE_ALLOW] = array($allow => $allow);
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: cache-control.
   *
   * According to the standard, the cache-control directive has the following structure:
   * -  1*(tchar) *("=" (1*(tchar) / quoted-string))
   *
   * It then later describes the use of multiple-cache control directives using commas to separate.
   * This is very misleading and so the standards own definition of "cache-directive" is inconsistent with itself.
   *
   * Based on what I have seen in practice, the cache-control directive should instead be treated as:
   * 1*(tchar) *("=" 1*(1*(tchar) / quoted-string) *(*(wsp) "," *(wsp) 1*(tchar) *("=" 1*(1*(tchar) / quoted-string))
   *
   * @param int $directive_name
   *   May be an integer id code representing the name or a literal string name of the directive.
   * @param string $directive_value
   *   (optional) The value of the directive, if one exists, which has the following structure:
   *   - 1*(1*(tchar) / quoted-string)
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2.3
   */
  public function set_response_cache_control($directive_name, $directive_value = NULL, $append = TRUE) {
    if (!is_int($directive_name)) {
      return c_base_return_error::s_false();
    }

    if (!is_null($directive_value) && !is_string($directive_value)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    switch($directive_name) {
      case self::CACHE_CONTROL_NO_CACHE:
      case self::CACHE_CONTROL_NO_STORE:
      case self::CACHE_CONTROL_NO_TRANSFORM:
      case self::CACHE_CONTROL_MAX_AGE:
      case self::CACHE_CONTROL_MAX_AGE_S:
      case self::CACHE_CONTROL_MAX_STALE:
      case self::CACHE_CONTROL_MIN_FRESH:
      case self::CACHE_CONTROL_ONLY_IF_CACHED:
      case self::CACHE_CONTROL_PUBLIC:
      case self::CACHE_CONTROL_PRIVATE:
      case self::CACHE_CONTROL_MUST_REVALIDATE:
      case self::CACHE_CONTROL_PROXY_REVALIDATE:
        break;

      default:
        return c_base_return_error::s_false();
    }

    $parsed_directive_value = NULL;
    if (!is_null($directive_value)) {
      $text = $this->pr_rfc_string_prepare($directive_value);
      if ($text['invalid']) {
        unset($text);

        return c_base_return_error::s_false();
      }

      $parsed = $this->pr_rfc_string_is_token_quoted($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed['invalid']) {
        unset($parsed);

        return c_base_return_error::s_false();
      }
      unset($parsed);
    }

    if ($append) {
      $this->response[self::RESPONSE_CACHE_CONTROL][$directive_name] = $directive_value;
    }
    else {
      $this->response[self::RESPONSE_CACHE_CONTROL] = array($directive_name => $directive_value);
    }

    unset($parsed_directive_value);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: connection.
   *
   * @param string $header_name
   *   The header name to assign as a connection-specific field.
   *   These header fields apply only to the immediate client.
   *   The header name format is:
   *   - 1*(tchar)
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-6.1
   */
  public function set_response_connection($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_prepare_token($header_name);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    if ($append) {
      $this->response[self::RESPONSE_CONNECTION][$parsed] = $parsed;
    }
    else {
      $this->response[self::RESPONSE_CONNECTION] = array($parsed => $parsed);
    }
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response content (the data/body).
   *
   * The term 'content' and 'body' seem to be used interchangably.
   * The header fields often refer to this as 'content', therefore this will be called 'content' and neither 'body' nor 'data'.
   *
   * This can either be a representation of one or more files (by their filename) or a string (binary or otherwise).
   * - Appending a file after a string is assigned or vice-versa will result in the previous data being deleted.
   *
   * @param string $content
   *   The content to assign.
   *   This could be either a string or binary a binary string.
   *   If $is_file is TRUE, then this is the local filename to the content.
   * @param bool $append
   *   (optional) If TRUE, then append the content or filename.
   *   If FALSE, then assign the content or filename.
   * @param bool $is_file
   *   (optional) If TRUE, then $content is treated as a file name.
   *   If FALSE, then is_file will be assigned to FALSE.
   *   If $append is TRUE, then this argument is ignored.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_content($content, $append = TRUE, $is_file = FALSE) {
    if (!is_string($content)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($is_file)) {
      return c_base_return_error::s_false();
    }

    if ($append) {
      if ($this->content_is_file) {
        if (is_array($this->content)) {
          $this->content[] = $content;
        }
        else {
          $this->content = array($content);
        }


      }
      else {
        if (is_array($this->content)) {
          $this->content = $content;
        }
        else {
          $this->content .= $content;
        }
      }
    }
    else {
      unset($this->content);

      if ($is_file) {
        $this->content_is_file = TRUE;
        $this->content = array($content);
      }
      else {
        $this->content_is_file = FALSE;
        $this->content = $content;
      }
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content-disposition.
   *
   * The standard defines this as:
   * - 1*(tchar) *(";" 1*(token) "=" 1*(1*(tchar) / 1*(quoted_string)))
   *
   * @param ?? $disposition
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  public function set_response_content_disposition($disposition) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: content-encoding.
   *
   * The standard is horrible at describing the differences between content-encoding and transfer-encoding.
   * Here is my understanding of the RFCs:
   *
   * Content Encoding:
   * - Defines how the content is encoding as a complete entire content structure.
   * - This is directly related to the mime-type, such that if there was a tarball like: example.html.gz, then content-encoding could be set to gzip to represent the mimetype for example.html (which might be: text/html).
   *
   * Transfer Encoding:
   * - Defines the encoding applied to the content for the purpos of transmitting.
   * - This is completely unrelated to the mime-type such that the example.html.gz could still be gzipped (such as using a different compression level) but it would represent example.html.gz and not example.html.gz.gz.
   *
   * This becomes more convoluted because the standard for content encoding states that it does not refer to encodings "inherent" to the content type.
   * - If that is the case, then the file example.html.gz would have no content-encoding because the file being transmitted is example.html.gz.
   *
   * The standard uses "Content-Encoding: gzip" as an example, even though gzip is used as a transfer-encoding.
   * - This means that the encoding is not about the content, but instead of how the content is being transferred (which sounds an awful lot like the words "transfer" and "encoding").
   *
   * That said, many sites and services out there appear to use (and document) that content-encoding is used for compressing data on transfer.
   * - This directly conflicts with the standards requirements as the compression is done for the transfer.
   *
   * Now, with the transfer-encoding header, there is a major issue in which the standard states (under 3.3.2. Content-Length):
   * - "A sender MUST NOT send a Content-Length header field in any message that contains a Transfer-Encoding header field."
   * - This ruins and breaks some of the functionality and benefits (including security implications) provided by Content-Length.
   * - This also contradicts its purpose of representing the message and the content.
   * - Given that the content-length represents the content and not the transport of the data, requiring content-length to be removed is outright idiotic.
   *
   * Chunked transfer was originally designed with the idea that the size of the content is unknown.
   * - In practice, many servers always send chunked data for performance reasons (such as progressive load jpeg).
   * - In many of these cases where chunked is used, the content size is known.
   *
   * PHP (or possibly the web server) appears to almost always send chunked data.
   * - This effectively forces a requirement of transfer-encoding: chunked, but never sets that and browsers still accept chunked transfer without the transfer encoding specifying it.
   * - Clients still support receiving data in chunks despite a complete lack of the transfer-encoding header (by which the standard states an error must be thrown).
   *
   * Content-Length is used to tell clients how large the content is.
   * - This has performance, integrity, and even security implications and is generally a good idea.
   *
   * Multiple content-encodings should also be avoided because multiple values in content-length does not relate to the multiple content-encodings.
   * - The content-length is supposed to represent the content and not the transmittion of the content and not allowing a content-length to match every content-encoding is yet another contradiction.
   * - Effectively, content-length, when used with multiple values is treated as a transfer-encoding length (which is the contradiction!):
   *   - "If a message is received that has multiple Content-Length header fields with field-values consisting of the same decimal value, or a single Content-Length header field with a field value containing a list of identical decimal values (e.g., "Content-Length: 42, 42"), indicating that duplicate Content-Length header fields have been generated or combined by an upstream message processor, then the recipient MUST either reject the message as invalid or replace the duplicated field-values with a single valid Content-Length field containing that decimal value prior to determining the message body length or forwarding the message.".
   * - This leaves content-length with three possible (contradictory) interpretations:
   *   1) content-length represents the original content (doing this causes invalid message-length errors on web browsers).
   *   2) content-length represents the length of only the first content encoding (doing this probably causes errors on the web browsers).
   *   3) content-length represents the length of the last encoding (there is no way to get or confirm the length based on the headers for each individual encoding).
   *   - The problem is, using #3 (which is the only one that seems to work), content-length must be interepreted as a transfer-encoding content-length (again, there is that annoying contradiction).
   *
   * In summary, I recommend the following:
   * - Never use transfer-encoding, it contradicts itself and content-encoding.
   * - Avoid multiple content-encodings.
   * - The standard is poorly written and many clients do not follow the standard (probably due to its poor design).
   *
   * @todo: this should be an array of values in the order in which the encoding is applied.
   *
   * @param int $encoding
   *   The encoding to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_response_transfer_encoding()
   * @see: self::set_response_content_length()
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.2.2
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  public function set_response_content_encoding($encoding, $append = TRUE) {
    if (!is_int($encoding)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    switch ($encoding) {
      case self::ENCODING_CHUNKED:
      case self::ENCODING_COMPRESS:
      case self::ENCODING_DEFLATE:
      case self::ENCODING_GZIP:
      case self::ENCODING_BZIP:
      case self::ENCODING_LZO:
      case self::ENCODING_XZ:
      case self::ENCODING_EXI:
      case self::ENCODING_IDENTITY:
      case self::ENCODING_SDCH:
      case self::ENCODING_PG:
        break;
      default:
        return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_CONTENT_ENCODING] = $encoding;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content-language.
   *
   * @param int|null $language
   *   The language code to assign to the specified header.
   *   If NULL, then the default language according the the given language class is used.
   *   If NULL and the default language is not set, then FALSE with error bit set is returned.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.3.2
   */
  public function set_response_content_language($language = NULL) {
    if (!is_null($language) && !is_int($language)) {
      return c_base_return_error::s_false();
    }

    if (is_null($language)) {
      if (!is_object($this->language_class) || !($this->language_class instanceof i_base_language)) {
        return c_base_return_error::s_false();
      }

      $default = $this->language_class->s_get_default_id();
      if ($default instanceof c_base_return_false) {
        unset($default);
        return c_base_return_error::s_false();
      }

      $this->response[self::RESPONSE_CONTENT_LANGUAGE] = $default;
      unset($default);
    }
    else {
      if ($language_class->s_get_names_by_id($language) instanceof c_base_return_false) {
        return c_base_return_error::s_false();
      }

      $this->response[self::RESPONSE_CONTENT_LANGUAGE] = $language;
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content-length.
   *
   * Read the function comments for self::set_response_transfer_encoding().
   *
   * @param int|null $length
   *   (optional) The content-length, representing the total number of octals (8-bits).
   *   Set to NULL for auto-calculation from already assigned data.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE without error bit set is returned when the Transfer-Encoding header field is assigned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_response_content_encoding()
   * @see: self::set_response_transfer_encoding()
   * @see: self::p_calculate_content_length()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.2
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.3
   */
  public function set_response_content_length($length = NULL) {
    if (!is_null($length) && !is_int($length) || $length < 0) {
      return c_base_return_error::s_false();
    }

    // From the RFC: "A sender MUST NOT send a Content-Length header field in any message that contains a Transfer-Encoding header field."
    if (array_key_exists(self::RESPONSE_TRANSFER_ENCODING, $this->response)) {
      return new c_base_return_false();
    }

    if (is_null($length)) {
      if (is_null($this->content)) {
        $this->response[self::RESPONSE_CONTENT_LENGTH] = 0;
      }
      else {
        if ($this->content_is_file) {
          $this->response[self::RESPONSE_CONTENT_LENGTH] = 0;

          foreach ($this->content as $filename) {
            if (!file_exists($filename)) {
              unset($filename);
              // @todo: provide a file not found error.
              return c_base_return_error::s_false();
            }

            $this->response[self::RESPONSE_CONTENT_LENGTH] += filesize($filename);
          }
        }
        else {
          $this->response[self::RESPONSE_CONTENT_LENGTH] = $this->p_calculate_content_length($this->content);
        }
      }
    }
    else {
      $this->response[self::RESPONSE_CONTENT_LENGTH] = $length;
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content_range.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_content_range($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: content_type.
   *
   * @todo: implement a thorough sanity check, this currently uses a simple check.
   *
   * @param string $content_type
   *   The content type to assign to the specified header.
   * @param int $charset
   *   (optional) The character set to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_response_encoding()
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  public function set_response_content_type($content_type, $charset = c_base_charset::UTF_8) {
    if (!is_string($content_type)) {
      return c_base_return_error::s_false();
    }

    if (!c_base_charset::s_is_valid($charset)) {
      return c_base_return_error::s_false();
    }

    // perform a very basic syntax check.
    if (strpos($content_type, ';')) {
      return c_base_return_error::s_false();
    }

    $content_type_part = mb_split('/', $content_type);

    if (count($content_type_part) != 2) {
      unset($content_type_part);
      return c_base_return_error::s_false();
    }
    unset($content_type_part);

    $this->response[self::RESPONSE_CONTENT_TYPE] = array(
      'type' => mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $content_type)),
      'charset' => $charset,
    );

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: date.
   *
   * @param int|float|null $timestamp
   *   (optional) The timestamp to assign to the specified header.
   *   When NULL, the $request_time timestamp defined by this class is used.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  public function set_response_date($timestamp = NULL) {
    if (is_null($timestamp)) {
      if (is_null($this->request_time)) {
        $this->request_time = microtime(TRUE);
      }

      $this->response[self::RESPONSE_DATE_ACTUAL] = $this->request_time;
      return new c_base_return_true();
    }

    if (!is_int($timestamp) && !is_float($timestamp)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_DATE] = $timestamp;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: date_actual.
   *
   * This is identical to the HTTP response header: date.
   * The purpose of this is to allow clients to still receive the correct/actual date when HTTP servers, such as apache, overwrite or alter the HTTP date response header.
   * This should therefore be used and calculated with when the date variable.
   *
   * @param int|float|null $timestamp
   *   (optional) The timestamp to assign to the specified header.
   *   When NULL, the $request_time timestamp defined by this class is used.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  public function set_response_date_actual($timestamp = NULL) {
    if (is_null($timestamp)) {
      if (is_null($this->request_time)) {
        $this->request_time = microtime(TRUE);
      }

      $this->response[self::RESPONSE_DATE_ACTUAL] = $this->request_time;
      return new c_base_return_true();
    }

    if (!is_int($timestamp) && !is_float($timestamp)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_DATE_ACTUAL] = $timestamp;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: etag.
   *
   * The auto-generated e-tag will be a checksum.
   * - The checksum will be against the assigned content and should be called before any content encoding is performed.
   *
   * @param string|null|bool $entity_tag
   *   (optional) The entity tag to assign to the specified header.
   *   If NULL, then the entity tag will be auto-calculated from the content using a full sha256 checksum.
   *   When NULL, the content must be fully defined or the checksum will be invalid.
   *   If FALSE, the entity tag will not be changed, only the weak setting will be changed.
   *   TRUE is not used and is considered invalid.
   * @param bool $weak
   *   (optional) Set to TRUE to enable a weak entity-tag.
   *   When $entity_tag is NULL, the first 9 characters of the generated entity-tag will be used for the 'weak' entity-tag.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  public function set_response_etag($entity_tag = NULL, $weak = FALSE) {
    if (!is_null($entity_tag) && !is_string($entity_tag) && $entity_tag !== FALSE) {
      return c_base_return_error::s_false();
    }

    $response = array(
      'tag' => '',
      'weak' => FALSE,
    );

    if (is_null($entity_tag)) {

      if ($weak) {
        $response['tag'] = 'partial:sha256:';
      }
      else {
        $response['tag'] = 'full:sha256:';
      }

      if ($this->content_is_file) {
        // @todo: determine how to handle multiple files and the checksum!
        // this might be inefficient in that the files will likely need to be buffered and then have the string checksumed.
        // look into: hash_update_file()
        if (!is_array($this->content)) {
          unset($response);

          // @todo: report warning about no content file specified.
          return new c_base_return_false();
        }

        $hash = hash_init('sha256');
        foreach ($this->content as $filename) {
          if (!file_exists($filename)) {
            unset($filename);
            unset($hash);
            unset($response);

            // @todo: report file not found or other related errors.
            return c_base_return_error::s_false();
          }

          $success = hash_update_file($hash, $filename);
          if (!$success) {
            unset($success);
            unset($filename);
            unset($hash);
            unset($response);

            // @todo: report failure to hash file.
            return c_base_return_error::s_false();
          }
        }
        unset($filename);
        unset($success);

        $response['tag'] = hash_final($hash, FALSE);
        unset($hash);

        if ($weak) {
          // Keep the first 15 characters for 'partial:sha256:' plus the first 9 characters of the checksum.
          $response['tag'] = substr($response['tag'], 0, 24);
        }
      }
      else {
        if (!is_string($this->content)) {
          unset($response);

          // @todo: report warning about no content specified.
          return new c_base_return_false();
        }

        $response['tag'] = hash('sha256', $this->content, FALSE);
      }
    }
    elseif ($entity_tag !== FALSE) {
      $response['tag'] = $entity_tag;
    }

    $response['weak'] = $weak;

    $this->response[self::RESPONSE_ETAG] = $response;
    unset($response);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: expires.
   *
   * From the RFC: "The value in Expires is only intended for recipients that have not yet implemented the Cache-Control field.".
   *
   * @param int|float $timestamp
   *   The unix timestamp to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.3
   */
  public function set_response_expires($timestamp) {
    if (!is_int($timestamp) && !is_float($timestamp)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_EXPIRES] = $timestamp;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: last-modified.
   *
   * @param int|float $timestamp
   *   The Unix timestamp to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-2.2
   */
  public function set_response_last_modified($timestamp) {
    if (!is_int($timestamp) && !is_float($timestamp)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_LAST_MODIFIED] = $timestamp;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: link.
   *
   * Use self::SCHEME_LOCAL for a local filesystem link.
   *
   * @param string $uri
   *   The URI to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc5988#section-5
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function set_response_link($uri) {
    if (!is_string($uri)) {
      return c_base_return_error::s_false();
    }

    #$parsed = $this->p_parse_uri($uri);

    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: location.
   *
   * Use self::SCHEME_LOCAL for a local filesystem link.
   *
   * @param string $uri
   *   The URI to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function set_response_location($uri) {
    if (!is_string($uri)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_parse_uri($uri);
    if ($parsed['invalid']) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    unset($parsed['invalid']);

    $this->response[self::RESPONSE_LOCATION] = $parsed;
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: pragma.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  public function set_response_pragma($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: proxy-authenticate.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.3
   */
  public function set_response_proxy_authenticate($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: public-key-pins.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7469
   */
  public function set_response_public_key_pins($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: refresh.
   *
   * This is not defined in any RFC but is supported by many browser.
   * It is recommended to never use this.
   * Instead, try using the HTTP header: location.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: set_response_location()
   * @see: https://en.wikipedia.org/wiki/Meta_refresh
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  public function set_response_refresh($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: retry-after.
   *
   * @param int|float $date
   *   When $seconds is FALSE, a unix timestamp representing the retry date.
   *   When $seconds is TRUE, the number of seconds to retry after.
   *
   * @param bool $seconds
   *   (optional) Used to change the interpretation of the $data parameter.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.3
   */
  public function set_response_retry_after($date, $seconds = FALSE) {
    if (!is_int($date) || !is_float($timestamp)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($seconds)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_RETRY_AFTER] = array(
      'value' => $date,
      'is_seconds' => $seconds,
    );

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: server.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.2
   */
  public function set_response_server($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: set_cookie.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_set_cookie($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: status.
   *
   * @param int $code
   *   The status code to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-4
   */
  public function set_response_status($code) {
    if (!is_int($code)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_STATUS] = $code;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: strict-transport-security.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_strict_transport_security($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: trailer.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_trailer($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: transfer-encoding.
   *
   * Read the function comments for self::set_response_transfer_encoding().
   *
   * @fixme: this should be an array of options that are essentially identical to content-encoding.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE without error bit set is returned when the Content-Length header field is assigned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_response_content_encoding()
   * @see: self::set_response_content_length()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.2
   */
  public function set_response_transfer_encoding($value) {
    // From the RFC: "A sender MUST NOT send a Content-Length header field in any message that contains a Transfer-Encoding header field."
    if (array_key_exists(self::RESPONSE_CONTENT_LENGTH, $this->response)) {
      unset($this->response[self::RESPONSE_CONTENT_LENGTH]);
    }

    // RESPONSE_TRANSFER_ENCODING

    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: upgrade.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_upgrade($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: vary.
   *
   * @param string $header_name
   *   The name of a header field to assign to the specified header.
   * @param bool $append
   *   (optional) Set to TRUE to append values instead of assigning.
   *   Set to FALSE to assign a new value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.4
   */
  public function set_response_vary($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_prepare_token($header_name);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    if ($append) {
      $this->response[self::RESPONSE_VARY][$parsed] = $parsed;
    }
    else {
      $this->response[self::RESPONSE_VARY] = array($parsed => $parsed);
    }
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: warning.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.5
   */
  public function set_response_warning($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: www_authenticate.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.1
   */
  public function set_response_www_authenticate($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: HTTP Protocol.
   *
   * @param string $protocol
   *   A string representing the HTTP protocol, such as: "HTTP 1.1".
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_protocol($protocol) {
    if (!is_string($protocol)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_PROTOCOL] = $protocol;

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content-security-policy.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_content_security_policy($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: x-content-type-options.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_x_content_type_options($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: x-ua-compatible.
   *
   * @param ?? $value
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_x_ua_compatible($value) {
    // @todo
    return new c_base_return_false();
  }

  /**
   * Assign HTTP response header: checksum_header.
   *
   * @param int $action
   *   (optional) Define how the checksum is to be processed.
   *   Can only be one of:
   *   - self::CHECKSUM_ACTION_NONE
   *   - self::CHECKSUM_ACTION_AUTO
   *   - self::CHECKSUM_ACTION_MANUAL
   * @param int|null $what
   *   (optional) An integer representing the checksum what, can be one of:
   *   - self::CHECKSUM_WHAT_FULL
   *   - self::CHECKSUM_WHAT_PARTIAL
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   * @param int|null $type
   *   (optional) An integer representing the checksum algorithm type.
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   * @param string|null $checksum
   *   (optional) A checksum that represents the content.
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_checksum_header($action = self::CHECKSUM_ACTION_AUTO, $what = NULL, $type = NULL, $checksum = NULL) {
    if (!is_int($action)) {
      return c_base_return_error::s_false();
    }

    if ($action != self::CHECKSUM_ACTION_NONE && $action != self::CHECKSUM_ACTION_AUTO && $action != self::CHECKSUM_ACTION_MANUAL) {
      return c_base_return_error::s_false();
    }

    if ($action == self::CHECKSUM_ACTION_MANUAL) {
      if (!is_int($what) || !is_int($type) || !is_string($checksum)) {
        return c_base_return_error::s_false();
      }

      if ($what != self::CHECKSUM_WHAT_PARTIAL && $what != self::CHECKSUM_WHAT_FULL) {
        return c_base_return_error::s_false();
      }

      switch ($type) {
        case CHECKSUM_MD2:
        case CHECKSUM_MD4:
        case CHECKSUM_MD5:
        case CHECKSUM_SHA1:
        case CHECKSUM_SHA224:
        case CHECKSUM_SHA256:
        case CHECKSUM_SHA384:
        case CHECKSUM_SHA512:
        case CHECKSUM_CRC32:
        case CHECKSUM_PG:
          break;
        default:
          return c_base_return_error::s_false();
      }

      $this->response[self::RESPONSE_CHECKSUM_HEADER] = array(
        'checksum' => $checksum,
        'action' => $action,
        'what' => $what,
        'type' => $type,
      );
    }
    else {
      if (!is_null($what) || !is_null($type) || !is_null($checksum)) {
        return c_base_return_error::s_false();
      }

      $this->response[self::RESPONSE_CHECKSUM_HEADER] = array(
        'checksum' => NULL,
        'action' => $action,
        'what' => self::CHECKSUM_WHAT_FULL,
        'type' => self::CHECKSUM_SHA256,
      );
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: checksum_headers.
   *
   * @param string $header_name
   *   The header name to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_checksum_headers($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      return c_base_return_error::s_false();
    }

    if (!is_bool($append)) {
      return c_base_return_error::s_false();
    }

    $parsed = $this->p_prepare_token($header_name);
    if ($parsed === FALSE) {
      unset($parsed);
      return c_base_return_error::s_false();
    }

    if ($append) {
      $this->response[self::RESPONSE_CHECKSUM_HEADERS][$parsed] = $parsed;
    }
    else {
      $this->response[self::RESPONSE_CHECKSUM_HEADERS] = array($parsed => $parsed);
    }
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: checksum_content.
   *
   * @param int $action
   *   (optional) Define how the checksum is to be processed.
   *   Can only be one of:
   *   - self::CHECKSUM_ACTION_NONE
   *   - self::CHECKSUM_ACTION_AUTO
   *   - self::CHECKSUM_ACTION_MANUAL
   *   When $action is self::CHECKSUM_ACTION_AUTO, the checksum will not be calculated at this point in time.
   * @param int|null $what
   *   (optional) An integer representing the checksum what, can be one of:
   *   - self::CHECKSUM_WHAT_FULL
   *   - self::CHECKSUM_WHAT_PARTIAL
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   * @param int|null $type
   *   (optional) An integer representing the checksum algorithm type.
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   * @param string|null $checksum
   *   (optional) A checksum that represents the content.
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_checksum_content($action = self::CHECKSUM_ACTION_AUTO, $what = NULL, $type = NULL, $checksum = NULL) {
    if (!is_int($action)) {
      return c_base_return_error::s_false();
    }

    if ($action != self::CHECKSUM_ACTION_NONE && $action != self::CHECKSUM_ACTION_AUTO && $action != self::CHECKSUM_ACTION_MANUAL) {
      return c_base_return_error::s_false();
    }

    if ($action == self::CHECKSUM_ACTION_MANUAL) {
      if (!is_int($what) || !is_int($type) || !is_string($checksum)) {
        return c_base_return_error::s_false();
      }

      if ($what != self::CHECKSUM_WHAT_PARTIAL && $what != self::CHECKSUM_WHAT_FULL) {
        return c_base_return_error::s_false();
      }

      switch ($type) {
        case CHECKSUM_MD2:
        case CHECKSUM_MD4:
        case CHECKSUM_MD5:
        case CHECKSUM_SHA1:
        case CHECKSUM_SHA224:
        case CHECKSUM_SHA256:
        case CHECKSUM_SHA384:
        case CHECKSUM_SHA512:
        case CHECKSUM_CRC32:
        case CHECKSUM_PG:
          break;
        default:
          return c_base_return_error::s_false();
      }

      $this->response[self::RESPONSE_CHECKSUM_CONTENT] = array(
        'checksum' => $checksum,
        'action' => $action,
        'what' => $what,
        'type' => $type,
      );
    }
    else {
      if (!is_null($what) || !is_null($type) || !is_null($checksum)) {
        return c_base_return_error::s_false();
      }

      $this->response[self::RESPONSE_CHECKSUM_CONTENT] = array(
        'checksum' => NULL,
        'action' => $action,
        'what' => self::CHECKSUM_WHAT_FULL,
        'type' => self::CHECKSUM_SHA256,
      );
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content_revision.
   *
   * The content-revision is used to tell clients what revision this document or file is.
   *
   * @param int $revision
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_content_revision($revision) {
    if (!is_int($revision)) {
      return c_base_return_error::s_false();
    }

    $this->response[self::RESPONSE_CONTENT_REVISION] = $revision;

    return new c_base_return_true();
  }

  /**
   * Ensures that a given response value is not assigned.
   *
   * @param int $response_id
   *   The ID representing a given response to unassign
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function unset_response_value($response_id) {
    if (!is_int($response_id)) {
      return c_base_return_error::s_false();
    }

    if (array_key_exists($response_id, $this->response)) {
      unset($this->response[$response_id]);
    }

    return new c_base_return_true();
  }

  /**
   * Obtain HTTP response header: access-control-allow-origin.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @return c_base_return_array|bool
   *   A decoded uri split into its different parts inside an array.
   *   This array also contains a key called 'wildcard' which may be either TRUE or FALSE.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function get_response_access_control_allow_origin() {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN]);
  }

  /**
   * Obtain HTTP response header: access-control-allow-credentials.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @return c_base_return_bool|c_base_return_status
   *   A boolean representing whether or not to allow credentials.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function get_response_access_control_allow_credentials() {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_bool::s_new($this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS]);
  }

  /**
   * Obtain HTTP response header: access-control-expose-headers.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of headers to expose.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function get_response_access_control_expose_headers() {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS]);
  }

  /**
   * Obtain HTTP response header: access-control-max-age.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @return c_base_return_int|c_base_return_status
   *   An Unix timestamp representing the specified header.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function get_response_access_control_max_age() {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_MAX_AGE, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_ACCESS_CONTROL_MAX_AGE]);
  }

  /**
   * Obtain HTTP response header: access-control-allow-methods.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @return c_base_return_array|bool
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  public function get_response_access_control_allow_methods() {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS]);
  }

  /**
   * Obtain HTTP response header: access-control-allow-headers.
   *
   * @todo: I only glanced at the documentation (which was incomplete on wikipedia).
   *        More work is likely needed to be done with this and its structure is subject to change.
   *
   * @return c_base_return_arrayl|c_base_return_status
   *   An array of allowed headers is returned.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_access_control_allow_headers() {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS]);
  }

  /**
   * Obtain HTTP response header: accept-patch.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the header values.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc5789#section-3.1
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  public function get_response_accept_patch() {
    if (!array_key_exists(self::RESPONSE_ACCEPT_PATCH, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ACCEPT_PATCH]);
  }

  /**
   * Obtain HTTP response header: accept_ranges.
   *
   * @return c_base_return_string|c_base_return_status
   *   A string representing the header value.
   *
   *   Common ranges are:
   *   - bytes
   *   - none
   *
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-2.3
   * @see: https://tools.ietf.org/html/rfc7233#section-3.1
   */
  public function get_response_accept_ranges() {
    if (!array_key_exists(self::RESPONSE_ACCEPT_RANGES, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($this->response[self::RESPONSE_ACCEPT_RANGES]);
  }

  /**
   * Obtain HTTP response header: age.
   *
   * @return c_base_return_int|c_base_return_status
   *   A Unix timestamp representing the header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.1
   */
  public function get_response_age() {
    if (!array_key_exists(self::RESPONSE_AGE, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_AGE]);
  }

  /**
   * Obtain HTTP response header: allow.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of allow method codes.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.1
   */
  public function get_response_allow() {
    if (!array_key_exists(self::RESPONSE_ALLOW, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ALLOW]);
  }

  /**
   * Obtain HTTP response header: cache-control.
   *
   * According to the standard, the cache-control directive has the following structure:
   * -  1*(tchar) *("=" (1*(tchar) / quoted-string))
   *
   * It then later describes the use of multiple-cache control directives using commas to separate.
   * This is very misleading and so the standards own definition of "cache-directive" is inconsistent with itself.
   *
   * Based on what I have seen in practice, the cache-control directive should instead be treated as:
   * 1*(tchar) *("=" 1*(1*(tchar) / quoted-string) *(*(wsp) "," *(wsp) 1*(tchar) *("=" 1*(1*(tchar) / quoted-string))
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the cache-control directives.
   *   Each array key is a name and if that directive has no value, then the related directive name will have a NULL value.
   *   For example, a directive of "no-cache" will have the following array structure:
   *   - array("no-cache" => NULL)
   *   For example, a directive of "private, max-age=32" will have the following array structure:
   *   - array("private" => NULL, "max-age" => 32)
   *
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2.3
   */
  public function get_response_cache_control() {
    if (!array_key_exists(self::RESPONSE_CACHE_CONTROL, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_CACHE_CONTROL]);
  }

  /**
   * Obtain HTTP response header: connection.
   *
   * @return c_base_return_array|bool
   *   An array of header names assigned to the connection header.
   *   The header name format is:
   *   - 1*(tchar)
   *
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-6.1
   */
  public function get_response_connection() {
    if (!array_key_exists(self::RESPONSE_CONNECTION, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_CONNECTION]);
  }

  /**
   * Obtain HTTP response header: content-disposition.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  public function get_response_content_disposition() {
    if (!array_key_exists(self::RESPONSE_CONTENT_DISPOSITION, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: content-encoding.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer representing the content length value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_encoding() {
    if (!array_key_exists(self::RESPONSE_CONTENT_ENCODING, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_CONTENT_ENCODING]);
  }

  /**
   * Obtain HTTP response header: content-language.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer representing the content length value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.3.2
   */
  public function get_response_content_language() {
    if (!array_key_exists(self::RESPONSE_CONTENT_LANGUAGE, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_CONTENT_LANGUAGE]);
  }

  /**
   * Obtain HTTP response header: content-length.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_length() {
    if (!array_key_exists(self::RESPONSE_CONTENT_LENGTH, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_CONTENT_LENGTH]);
  }

  /**
   * Obtain HTTP response header: content_range.
   *
   * @todo: probably an array.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_range() {
    if (!array_key_exists(self::RESPONSE_CONTENT_RANGE, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: content_type.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the following keys:
   *   - 'type': the content type string, such as 'text/html'.
   *   - 'charset': the character set integer, such as: c_base_charset::UTF_8.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  public function get_response_content_type() {
    if (!array_key_exists(self::RESPONSE_CONTENT_TYPE, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_CONTENT_TYPE]);
  }

  /**
   * Obtain HTTP response header: date.
   *
   * @return c_base_return_int|c_base_return_float|c_base_return_status
   *   A unix timestamp integer.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  public function get_response_date() {
    if (!array_key_exists(self::RESPONSE_DATE, $this->response)) {
      return c_base_return_error::s_false();
    }

    if (is_float($this->response[self::RESPONSE_DATE])) {
      return c_base_return_float::s_new($this->response[self::RESPONSE_DATE]);
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_DATE]);
  }

  /**
   * Obtain HTTP response header: date_actual.
   *
   * This is identical to the HTTP response header: date.
   * The purpose of this is to allow clients to still receive the correct/actual date when HTTP servers, such as apache, overwrite or alter the HTTP date response header.
   * This should therefore be used and calculated with when the date variable.
   *
   * @return c_base_return_int|c_base_return_float|c_base_return_status
   *   A unix timestamp integer.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  public function get_response_date_actual() {
    if (!array_key_exists(self::RESPONSE_DATE_ACTUAL, $this->response)) {
      return c_base_return_error::s_false();
    }

    if (is_float($this->response[self::RESPONSE_DATE_ACTUAL])) {
      return c_base_return_float::s_new($this->response[self::RESPONSE_DATE_ACTUAL]);
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_DATE_ACTUAL]);
  }

  /**
   * Obtain HTTP response header: etag.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the following:
   *   - tag: The entity tag string (without weakness).
   *   - weak: A boolean representing whether or not the entity tag is weak.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  public function get_response_etag() {
    if (!array_key_exists(self::RESPONSE_ETAG, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_ETAG]);
  }

  /**
   * Obtain HTTP response header: expires.
   *
   * @return c_base_return_int|c_base_return_float|c_base_return_status
   *   A unix timestamp integer.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.3
   */
  public function get_response_expires() {
    if (!array_key_exists(self::RESPONSE_EXPIRES, $this->response)) {
      return c_base_return_error::s_false();
    }

    if (is_float($this->response[self::RESPONSE_EXPIRES])) {
      return c_base_return_float::s_new($this->response[self::RESPONSE_EXPIRES]);
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_EXPIRES]);
  }

  /**
   * Obtain HTTP response header: last-modified.
   *
   * @return c_base_return_int|c_base_return_float|c_base_return_status
   *   A unix timestamp integer.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-2.2
   */
  public function get_response_last_modified() {
    if (!array_key_exists(self::RESPONSE_LAST_MODIFIED, $this->response)) {
      return c_base_return_error::s_false();
    }

    if (is_float($this->response[self::RESPONSE_LAST_MODIFIED])) {
      return c_base_return_float::s_new($this->response[self::RESPONSE_LAST_MODIFIED]);
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_LAST_MODIFIED]);
  }

  /**
   * Obtain HTTP response header: link.
   *
   * @todo: break this into an array of the differnt parts.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc5988#section-5
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function get_response_link() {
    if (!array_key_exists(self::RESPONSE_LINK, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: location.
   *
   * @todo: consider changing this to an array containing the entire url parts broken into each key.
   *
   * @return c_base_return_array|c_base_return_status
   *   A decoded uri split into its different parts inside an array.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function get_response_location() {
    if (!array_key_exists(self::RESPONSE_LOCATION, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($this->response[self::RESPONSE_LOCATION]);
  }

  /**
   * Obtain HTTP response header: pragma.
   *
   * @todo: the cache specific options, probably an array.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  public function get_response_pragma() {
    if (!array_key_exists(self::RESPONSE_PRAGMA, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: proxy-authenticate.
   *
   * No specific content structure is defined for this response header.
   * Should a specific structure be defined in the future, then the return value is subject to change.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.3
   */
  public function get_response_proxy_authenticate() {
    if (!array_key_exists(self::RESPONSE_PROXY_AUTHENTICATE, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: public-key-pins.
   *
   * No specific content structure is defined for this response header.
   * Should a specific structure be defined in the future, then the return value is subject to change.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7469
   */
  public function get_response_public_key_pins() {
    if (!array_key_exists(self::RESPONSE_PUBLIC_KEY_PINS, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: refresh.
   *
   * This is not defined in any RFC but is supported by many browser.
   * It is recommended to never use this.
   * Instead, try using the HTTP header: location.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: get_response_location()
   * @see: https://en.wikipedia.org/wiki/Meta_refresh
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  public function get_response_refresh() {
    if (!array_key_exists(self::RESPONSE_REFRESH, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: retry-after.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the following:
   *   - value: When 'is_seconds' is FALSE, this is the unix timestamp representing when the page expires.
   *            When 'is_seconds' is FALSE, this is the relative number of seconds until the content expires.
   *   - is_seconds: A boolean that when true changes the interpretation of the 'value' key.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.3
   */
  public function get_response_retry_after() {
    if (!array_key_exists(self::RESPONSE_RETRY_AFTER, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_RETRY_AFTER]);
  }

  /**
   * Obtain HTTP response header: server.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.2
   */
  public function get_response_server() {
    if (!array_key_exists(self::RESPONSE_SERVER, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: get_cookie.
   *
   * @todo: cookie might needs special handling because PHP handles it in a different way from other headers.
   *
   * @return ??|c_base_return_status
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_cookie() {
    if (!array_key_exists(self::RESPONSE_COOKIE, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: status.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer representing the HTTP status code.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-4
   */
  public function get_response_status() {
    if (!array_key_exists(self::RESPONSE_STATUS, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_STATUS]);
  }

  /**
   * Obtain HTTP response header: strict-transport-security.
   *
   * @return c_base_return_string|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc6797#section-6.1
   */
  public function get_response_strict_transport_security() {
    if (!array_key_exists(self::RESPONSE_STRICT_TRANSPORT_SECURITY, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: trailer.
   *
   * @todo: this appears to no longer be directly specified in the headers.
   *        There is a 'trailer-part' mentioned along with the transfer encoding information.
   *
   * @return ???|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.40
   * @see: https://tools.ietf.org/html/rfc7230#section-4.1.2
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  public function get_response_trailer() {
    if (!array_key_exists(self::RESPONSE_TRAILER, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: transfer-encoding.
   *
   * @return ???|c_base_return_status
   *   An integer representing the encoding used to transfer this content.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  public function get_response_transfer_encoding() {
    if (!array_key_exists(self::RESPONSE_TRANSFER_ENCODING, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: upgrade.
   *
   *
   * @return ???|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_upgrade() {
    if (!array_key_exists(self::RESPONSE_UPGRADE, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: vary.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the response header values.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.4
   */
  public function get_response_vary() {
    if (!array_key_exists(self::RESPONSE_VARY, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_VARY]);
  }

  /**
   * Obtain HTTP response header: warning.
   *
   * @return ???|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.5
   */
  public function get_response_warning() {
    if (!array_key_exists(self::RESPONSE_WARNING, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: www-authenticate.
   *
   * @return ???|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.1
   */
  public function get_response_www_authenticate() {
    if (!array_key_exists(self::RESPONSE_WWW_AUTHENTICATE, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: HTTP Protocol.
   *
   * @return c_base_return_string|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_protocol() {
    if (!array_key_exists(self::RESPONSE_PROTOCOL, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_string::s_new($this->response[self::RESPONSE_PROTOCOL]);
  }

  /**
   * Obtain HTTP response header: content-security-policy.
   *
   * @return ???|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_security_policy() {
    if (!array_key_exists(self::RESPONSE_X_CONTENT_SECURITY_POLICY, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: x-content-type-options.
   *
   * @return ???|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_x_content_type_options() {
    if (!array_key_exists(self::RESPONSE_X_CONTENT_TYPE_OPTIONS, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: x-ua-compatible.
   *
   * @return c_base_return_string|c_base_return_status
   *   A string containing the response header value.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_x_ua_compatible() {
    if (!array_key_exists(self::RESPONSE_X_UA_COMPATIBLE, $this->response)) {
      return c_base_return_error::s_false();
    }

    // @todo
    return c_base_return_error::s_false();
  }

  /**
   * Obtain HTTP response header: checksum_header.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing:
   *   - 'what': A specific way in which to interpret the checksum.
   *   - 'type': The algorithm type of the checksum.
   *   - 'checksum': The checksum value after it has been base64 decoded.
   *   - 'action': An integer representing how this checksum is processed when generating the HTTP response.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_checksum_header() {
    if (!array_key_exists(self::RESPONSE_CHECKSUM_HEADER, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_CHECKSUM_HEADERS]);
  }

  /**
   * Obtain HTTP response header: checksum_headers.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing a list of header field names.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_checksum_headers() {
    if (!array_key_exists(self::RESPONSE_CHECKSUM_HEADERS, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_CHECKSUM_HEADERS]);
  }

  /**
   * Obtain HTTP response header: checksum_content.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing:
   *   - 'what': A specific way in which to interpret the checksum.
   *   - 'type': The algorithm type of the checksum.
   *   - 'checksum': The checksum value after it has been base64 decoded.
   *   - 'action': An integer representing how this checksum is processed when generating the HTTP response.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_checksum_content() {
    if (!array_key_exists(self::RESPONSE_CHECKSUM_CONTENT, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_array::s_new($this->response[self::RESPONSE_CHECKSUM_CONTENT]);
  }

  /**
   * Obtain HTTP response header: content_revision.
   *
   * @return c_base_return_int|c_base_return_status
   *   An integer representing a revision number.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_revision() {
    if (!array_key_exists(self::RESPONSE_CONTENT_REVISION, $this->response)) {
      return c_base_return_error::s_false();
    }

    return c_base_return_int::s_new($this->response[self::RESPONSE_CONTENT_REVISION]);
  }

  /**
   * Return an array for mapping HTTP request header strings to header ids.
   *
   * @return c_base_return_array
   *   An array for mapping HTTP request header strings to header ids.
   *
   * @see: p_get_header_request_mapping()
   */
  public function get_header_request_mapping() {
    return c_base_return_array::s_new($this->p_get_header_request_mapping());
  }

  /**
   * Return an array for mapping HTTP response header strings to header ids.
   *
   * @return c_base_return_array
   *   An array for mapping HTTP response header strings to header ids.
   *
   * @see: p_get_header_response_mapping()
   */
  public function get_header_response_mapping() {
    return c_base_return_array::s_new($this->p_get_header_response_mapping());
  }

  /**
   * Return an array for mapping HTTP method strings to ids.
   *
   * @return c_base_return_array
   *   An array for mapping HTTP response header method strings to header method ids.
   *
   * @see: p_get_http_method_mapping()
   */
  public function get_http_method_mapping() {
    return c_base_return_array::s_new($this->p_get_http_method_mapping());
  }

  /**
   * Send the response HTTP headers.
   *
   * This function will not correct incorrect behavior.
   * The idea is to provide the caller with as much control as possible to handle different situations.
   *
   * This sends checksum header fields and any change in the headers or content may result in request checksum failing.
   *
   * @param bool $shuffle
   *   (optional) When TRUE, this will randomize the order in which header fields are defined, except for the status header (which is always first).
   *    This helps resist fingerprinting techniques thereby helping increase security.
   *
   * @return c_base_return_status
   *   TRUE is returned when headers are sent.
   *   FALSE is returned when headers have already been sent.
   *   FALSE with error bit set is returned on error.
   *
   *   If headers were already sent, but not by this class, then FALSE with the error bit set is returned.
   *
   * @see: self::set_response_checksum_header()
   * @see: self::set_response_checksum_headers()
   * @see: self::set_response_checksum_content()
   * @see: header()
   * @see: headers_sent()
   */
  public function send_response_headers($shuffle = TRUE) {
    if (!is_bool($shuffle)) {
      return c_base_return_error::s_false();
    }

    if ($this->headers_sent) {
      return c_base_return_false;
    }

    if (headers_sent()) {
      return c_base_return_error::s_false();
    }

    $header_id_to_names = $this->p_get_header_response_mapping(TRUE);

    if ($shuffle) {
      $headers = array();

      // shuffle() alters the array keys, so some additional processing must be done to protect the array keys.
      $shuffled = array_flip($this->p_get_header_response_mapping());
      shuffle($shuffled);

      $unshuffled = $this->p_get_header_response_mapping();
      foreach ($shuffled as $header_code) {
        $headers[$header_code] = $unshuffled[$header_code];
      }
      unset($header_code);
      unset($unshuffled);
      unset($shuffled);
    }
    else {
      $headers = $this->p_get_header_response_mapping();
    }

    // this is used to perform checksums.
    $header_output = array();


    // response status, this must always be first.
    unset($headers[self::RESPONSE_STATUS]);
    $status_string = NULL;
    if (array_key_exists(self::RESPONSE_PROTOCOL, $this->response) && array_key_exists(self::RESPONSE_STATUS, $this->response)) {
      $status_string = $this->response[self::RESPONSE_PROTOCOL] . ' ';

      $status_text = c_base_http_status::to_text($this->response[self::RESPONSE_STATUS]);
      if ($status_text instanceof c_base_return_false) {
        $status_string .= $this->response[self::RESPONSE_STATUS];
      }
      else {
        $status_string .= $this->response[self::RESPONSE_STATUS] . ' ' . $status_text->get_value_exact();
      }
      unset($status_text);

      header($status_string, TRUE, $this->response[self::RESPONSE_STATUS]);
    }

    $this->p_prepare_header_response_access_control_allow_origin($header_output);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS], $header_id_to_names[self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS], self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS);
    $this->p_prepare_header_response_access_control_expose_headers($header_output);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_ACCESS_CONTROL_MAX_AGE], $header_id_to_names[self::RESPONSE_ACCESS_CONTROL_MAX_AGE], self::RESPONSE_ACCESS_CONTROL_MAX_AGE);
    $this->p_prepare_header_response_access_control_allow_methods($header_output);
    $this->p_prepare_header_response_access_control_allow_headers($header_output);
    $this->p_prepare_header_response_accept_patch($header_output);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_ACCEPT_RANGES], $header_id_to_names[self::RESPONSE_ACCEPT_RANGES], self::RESPONSE_ACCEPT_RANGES);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_AGE], $header_id_to_names[self::RESPONSE_AGE], self::RESPONSE_AGE);
    $this->p_prepare_header_response_allow($header_output);
    $this->p_prepare_header_response_cache_control($header_output);
    $this->p_prepare_header_response_connection($header_output);
    $this->p_prepare_header_response_content_disposition($header_output);
    $this->p_prepare_header_response_content_encoding($header_output);
    $this->p_prepare_header_response_content_language($header_output);
    // @todo: this is now an array of values.
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_CONTENT_LENGTH], $header_id_to_names[self::RESPONSE_CONTENT_LENGTH], self::RESPONSE_CONTENT_LENGTH);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_CONTENT_RANGE], $header_id_to_names[self::RESPONSE_CONTENT_RANGE], self::RESPONSE_CONTENT_RANGE);
    $this->p_prepare_header_response_content_type($header_output);
    $this->p_prepare_header_response_timestamp_value($header_output, $headers[self::RESPONSE_DATE], $header_id_to_names[self::RESPONSE_DATE], self::RESPONSE_DATE);
    $this->p_prepare_header_response_timestamp_value($header_output, $headers[self::RESPONSE_DATE_ACTUAL], $header_id_to_names[self::RESPONSE_DATE_ACTUAL], self::RESPONSE_DATE_ACTUAL);
    $this->p_prepare_header_response_etag($header_output);
    $this->p_prepare_header_response_timestamp_value($header_output, $header_id_to_names[self::RESPONSE_EXPIRES], $header_id_to_names[self::RESPONSE_EXPIRES], self::RESPONSE_EXPIRES);
    $this->p_prepare_header_response_timestamp_value($header_output, $header_id_to_names[self::RESPONSE_LAST_MODIFIED], $header_id_to_names[self::RESPONSE_LAST_MODIFIED], self::RESPONSE_LAST_MODIFIED);
    $this->p_prepare_header_response_link($header_output);
    $this->p_prepare_header_response_location($header_output);
    $this->p_prepare_header_response_pragma($header_output);
    $this->p_prepare_header_response_proxy_authenticate($header_output);
    $this->p_prepare_header_response_public_key_pins($header_output);
    $this->p_prepare_header_response_refresh($header_output);
    $this->p_prepare_header_response_retry_after($header_output);
    $this->p_prepare_header_response_server($header_output);

    // @todo: how is cookie going to be handled?

    $this->p_prepare_header_response_strict_transport_security($header_output);
    $this->p_prepare_header_response_trailer($header_output);
    $this->p_prepare_header_response_transfer_encoding($header_output);
    $this->p_prepare_header_response_upgrade($header_output);
    $this->p_prepare_header_response_vary($header_output);
    $this->p_prepare_header_response_warning($header_output);
    $this->p_prepare_header_response_www_authenticate($header_output);
    $this->p_prepare_header_response_x_content_security_policy($header_output);
    $this->p_prepare_header_response_x_content_type_options($header_output);
    $this->p_prepare_header_response_x_ua_compatible($header_output);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_CONTENT_LENGTH], $header_id_to_names[self::RESPONSE_CONTENT_LENGTH], self::RESPONSE_CONTENT_LENGTH);
    $this->p_prepare_header_response_simple_value($header_output, $headers[self::RESPONSE_CONTENT_REVISION], $header_id_to_names[self::RESPONSE_CONTENT_REVISION], self::RESPONSE_CONTENT_REVISION);
    $this->p_prepare_header_response_checksum_content($header_output);
    $this->p_prepare_header_response_checksum_headers($header_output, $status_string);
    unset($status_string);
    unset($header_id_to_names);


    // send header output.
    foreach ($headers as $header_id => $header_name) {
      if (array_key_exists($header_id, $header_output)) {
        header($header_output[$header_id]);
      }
    }
    unset($output);
    unset($header_output);


    $this->headers_sent = TRUE;
    return new c_base_return_true();
  }

  /**
   * Get the HTTP response content.
   *
   * The term 'content' and 'body' seem to be used interchangably.
   * The header fields often refer to this as 'content', therefore this will be called 'content' and neither 'body' nor 'data'.
   *
   * @return c_base_return_string|c_base_return_array
   *   A string representing the response content.
   *   If the content is represented via one or more files, then an array of filenames is returned.
   */
  public function get_response_content() {
    if ($this->content_is_file) {
      return c_base_return_array::s_new($this->content);
    }

    return c_base_return_string::s_new($this->content);
  }

  /**
   * Encode the HTTP response content.
   *
   * This must be performed after all content has been buffered and before the HTTP headers are sent.
   * This must be called before sending the response content.
   *
   * This will alter the 'content-encoding' header.
   * This will alter the 'content-length' header only if the encoding was altered.
   * This will alter the 'transfer-encoding' header (if not already defined) and set it to 'chunked', when 'content-length' is not specified.
   *
   * If called when the content is a file, then that file will be compressed into memory.
   * - This allows for the compressed file length to be calculated, but at the cost of additional memory consumption.
   * - Do not call this when handling large files.
   *
   * @fixme: this needs to properly handle content-length when transfer-encoding is specified.
   *
   * @param int|null $compression
   *   (optional) The compression integer.
   *   This is specific to the algorithm used, but in general could be considered as follows:
   *   - NULL = use default.
   *   - -1 = use library default if possible or default if not.
   *   - 0 = no compression.
   *   - 1 -> 9 = compress level with 1 being weakest and 9 being strongest.
   *   Some algorithms, such as lzo, have their own predefined constants that can be specified here, such as LZO1X_999.
   * @param int|null $max_filesize
   *   If content is a file and this is some value greater than 0, then files whose filesize is less than this will be loaded and compressed into memory.
   *   Otherwise, the content-length header will be unset, preventing an incorrect filesize from being transmitted.
   *   This represents filesizes in number of bytes.
   *   When NULL, the file size will not be calculated.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE without error bit set is returned if there is no assigned content to send.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::send_response_content()
   * @see: self::set_response_content_encoding()
   * @see: self::set_response_content_length()
   */
  public function encode_response_content($compression = NULL, $max_filesize = NULL) {
    if (!is_null($compression) && !is_int($compression)) {
      return c_base_return_error::s_false();
    }

    if (!is_null($max_filesize) && !(is_int($max_filesize) && $max_filesize > 0)) {
      return c_base_return_error::s_false();
    }

    $encoding = $this->p_determine_response_encoding();

    if ($this->content_is_file) {
      if (empty($this->content)) {
        unset($encoding);
        return c_base_return_false();
      }

      if (is_null($max_filesize)) {
        $content = '';
        foreach ($this->content as $filename) {
          if (!file_exists($filename)) {
            unset($encoding);
            unset($filename);
            unset($content);
            // @todo: provide a warning about missing files.
            return c_base_return_error::s_false();
          }

          $content .= file_get_contents($filename);
        }
        unset($filename);

        if (empty($content)) {
          unset($encoding);
          unset($content);
          return c_base_return_false();
        }

        $this->p_encode_content($content, $encoding, $compression, TRUE);
        unset($content);
        unset($encoding);
      }
      else {
        $content = '';
        $content_length = 0;
        foreach ($this->content as $filename) {
          if (!file_exists($filename)) {
            unset($encoding);
            unset($filename);
            unset($content);
            // @todo: provide a warning about missing files.
            return c_base_return_error::s_false();
          }

          $content_length += filesize($filename);
          if ($content_length >= $max_filesize) {
            break;
          }

          $content .= file_get_contents($filename);
        }
        unset($filename);

        if (empty($content) || $content_length >= $max_filesize) {
          unset($encoding);
          unset($content);
          return c_base_return_false();
        }
        unset($content_length);

        $this->p_encode_content($content, $encoding, $compression, $compression, FALSE);
        unset($content);
        unset($encoding);

        // the content-length cannot be specified in this case.
        unset($this->response[self::RESPONSE_CONTENT_LENGTH]);
      }
    }
    else {
      $this->p_encode_content($this->content, $encoding, $compression, TRUE);
      unset($encoding);
    }

    return new c_base_return_true();
  }

  /**
   * Send the HTTP response content.
   *
   * The term 'content' and 'body' seem to be used interchangably.
   * The header fields often refer to this as 'content', therefore this will be called 'content' and neither 'body' nor 'data'.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   *
   * @see: self::encode_response_content()
   */
  public function send_response_content() {
    if ($this->content_is_file) {
      // @todo: the $this->content may be an array of filenames instead of string data, handle that here.
      foreach ($this->content as $filename) {
        if (!file_exists($filename)) {
          unset($filename);
          // @todo: provide a warning about missing files.
          return c_base_return_error::s_false();
        }

        $opened_file = fopen($filename, 'rb');
        if ($opened_file === FALSE) {
          unset($filename);
          // @todo: provide a warning about unable to open file.
          return c_base_return_error::s_false();
        }

        fpassthru($opened_file);
        fclose($opened_file);
      }
      unset($opened_file);
      unset($filename);
    }
    else {
      print($this->content);
    }

    return new c_base_return_true();
  }

  /**
   * Calculate the 8-bit length (octet) of the content/body.
   *
   * RFC 7230 describes the content-length as referring to the count based on every 8-bits, which is a single octet.
   * Using strlen() would be incorrect because characters are 7-bit.
   * Using mb_strlen() would be incorrect because it contains mixed lengths.
   * Using any string test would be incorrect because the content may already be binary data.
   *
   * The solution is to break apart the string into 8-bit chunks and then calculate the length.
   *
   * Errata: It turns out this solution is expensive, and so is disabled for now.
   * - Because this only uses strlen(), there is no way to protect against strlen() becoming mb_strlen() via the mbstring.func_overload setting.
   * - Therefore, mbstring.func_overload must never be enabled or risk causing security issues.
   *
   * @return int
   *   Total number of octals on success.
   *
   * @see: set_response_content_length()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.2
   */
  private function p_calculate_content_length($text) {
    return strlen($text);
  }

  /**
   * Load and process the HTTP request parameter: accept.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.2
   */
  private function p_load_request_accept() {
    if (empty($this->headers['accept'])) {
      $this->request[self::REQUEST_ACCEPT]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_ACCEPT]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_ACCEPT]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_ACCEPT]['data']['invalid']) {
      $this->request[self::REQUEST_ACCEPT]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_ACCEPT]['defined'] = TRUE;
      $this->request[self::REQUEST_ACCEPT]['data']['accept'] = NULL;
      $this->request[self::REQUEST_ACCEPT]['data']['category'] = NULL;
      $this->request[self::REQUEST_ACCEPT]['data']['weight'] = array();
      $this->request[self::REQUEST_ACCEPT]['data']['types'] = array();
      #$this->request[self::REQUEST_ACCEPT]['data']['categories'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[self::REQUEST_ACCEPT]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $result = c_base_mime::s_identify($c['choice'], TRUE);
          if ($result instanceof c_base_return_false) {
            // there is no valid value to process.
            continue;
          }

          $identified = $result->get_value_exact();
          unset($result);

          $c['accept'] = $identified['id_type'];
          $c['category'] = $identified['id_category'];
          $this->request[self::REQUEST_ACCEPT]['data']['types'][$weight][$identified['id_type']] = $identified['id_type'];
          #$this->request[self::REQUEST_ACCEPT]['data']['categories'][$weight][$identified['id_category']] = $identified['id_category'];
          $this->request[self::REQUEST_ACCEPT]['data']['weight'][$weight][$identified['id_type']] = $identified['name_category'] . '/' . $identified['name_type'];

          // @todo: should this be used or not?
          #if ($identified['id_category'] == c_base_mime::CATEGORY_UNKNOWN || $identified['id_type'] == c_base_mime::TYPE_UNKNOWN) {
          #  $c['accept'] = NULL;
          #}

          unset($identified);
        }
      }
      unset($choice);
      unset($weight);
      unset($c);
      unset($key);

      // sort the weight array.
      krsort($this->request[self::REQUEST_ACCEPT]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->p_prepend_array_value(NULL, $this->request[self::REQUEST_ACCEPT]['data']['weight']);

      // rename 'choices' array key to 'accept'.
      $this->request[self::REQUEST_ACCEPT]['data']['accept'] = $this->request[self::REQUEST_ACCEPT]['data']['choices'];
      unset($this->request[self::REQUEST_ACCEPT]['data']['choices']);
    }
    unset($this->request[self::REQUEST_ACCEPT]['data']['invalid']);
    unset($this->request[self::REQUEST_ACCEPT]['data']['current']);

    $this->request[self::REQUEST_ACCEPT]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-language.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.5
   */
  private function p_load_request_accept_language() {
    if (empty($this->headers['accept_language'])) {
      $this->request[self::REQUEST_ACCEPT_LANGUAGE]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept_language']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_ACCEPT_LANGUAGE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_ACCEPT_LANGUAGE]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['invalid']) {
      $this->request[self::REQUEST_ACCEPT_LANGUAGE]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_ACCEPT_LANGUAGE]['defined'] = TRUE;
      $this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['weight'] = array();

      if (is_null($this->language_class) || !class_exists($this->language_class)) {
        // PHP does not allow "new self::ACCEPT_LANGUAGE_CLASS_DEFAULT()", but using a variable is allowed.
        $class = self::ACCEPT_LANGUAGE_CLASS_DEFAULT;
        $languages = new $class();
        unset($class);
      }
      else {
        $languages = new $this->language_class();
      }

      // convert the known values into integers for improved processing.
      foreach ($this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $id = $languages->s_get_id_by_name($c['choice']);

          if ($id instanceof c_base_return_false) {
            $c['language'] = NULL;
          }
          else {
            $c['language'] = $id->get_value_exact();
            $this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['weight'][$weight][$c['language']] = mb_strtolower($c['choice']);
            unset($c['choice']);
          }
        }
      }
      unset($languages);

      // sort the weight array.
      krsort($this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->p_prepend_array_value(NULL, $this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['weight']);

      // rename 'choices' array key to 'language'.
      $this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['language'] = $this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['choices'];
      unset($this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['choices']);
    }
    unset($this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['invalid']);
    unset($this->request[self::REQUEST_ACCEPT_LANGUAGE]['data']['current']);

    $this->request[self::REQUEST_ACCEPT_LANGUAGE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-encoding.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.4
   */
  private function p_load_request_accept_encoding() {
    if (empty($this->headers['accept_encoding'])) {
      $this->request[self::REQUEST_ACCEPT_ENCODING]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept_encoding']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_ACCEPT_ENCODING]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_ACCEPT_ENCODING]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['invalid']) {
      $this->request[self::REQUEST_ACCEPT_ENCODING]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_ACCEPT_ENCODING]['defined'] = TRUE;
      $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $lowercase = mb_strtolower($c['choice']);
          if ($lowercase == 'chunked') {
            $c['encoding'] = self::ENCODING_CHUNKED;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_CHUNKED] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'compress') {
            $c['encoding'] = self::ENCODING_COMPRESS;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_COMPRESS] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'deflate') {
            $c['encoding'] = self::ENCODING_DEFLATE;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_DEFLATE] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'gzip') {
            $c['encoding'] = self::ENCODING_GZIP;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_GZIP] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'bzip') {
            $c['encoding'] = self::ENCODING_BZIP;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_BZIP] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'lzo') {
            $c['encoding'] = self::ENCODING_LZO;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_LZO] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'xz') {
            $c['encoding'] = self::ENCODING_XZ;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_XZ] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'exit') {
            $c['encoding'] = self::ENCODING_EXI;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_EXI] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'identity') {
            $c['encoding'] = self::ENCODING_IDENTITY;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_IDENTITY] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'sdch') {
            $c['encoding'] = self::ENCODING_SDCH;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_SDCH] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'pg') {
            $c['encoding'] = self::ENCODING_PG;
            $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][self::ENCODING_PG] = $lowercase;
            unset($c['choice']);
          }
          else {
            $c['encoding'] = NULL;
          }
        }
      }
      unset($lowercase);

      // sort the weight array.
      krsort($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->p_prepend_array_value(NULL, $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight']);

      // rename 'choices' array key to 'encoding'.
      $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['encoding'] = $this->request[self::REQUEST_ACCEPT_ENCODING]['data']['choices'];
      unset($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['choices']);
    }
    unset($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['invalid']);
    unset($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['current']);

    $this->request[self::REQUEST_ACCEPT_ENCODING]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-charset.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.3
   */
  private function p_load_request_accept_charset() {
    if (empty($this->headers['accept_charset'])) {
      $this->request[self::REQUEST_ACCEPT_CHARSET]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept_charset']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_ACCEPT_CHARSET]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_ACCEPT_CHARSET]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_ACCEPT_CHARSET]['data']['invalid']) {
      $this->request[self::REQUEST_ACCEPT_CHARSET]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_ACCEPT_CHARSET]['defined'] = TRUE;
      $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[self::REQUEST_ACCEPT_CHARSET]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $lowercase = mb_strtolower($c['choice']);
          if ($lowercase == 'ascii') {
            $c['charset'] = c_base_charset::ASCII;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ASCII] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'utf-8') {
            $c['charset'] = c_base_charset::UTF_8;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::UTF_8] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'utf-16') {
            $c['charset'] = c_base_charset::UTF_16;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::UTF_16] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'utf-32') {
            $c['charset'] = c_base_charset::UTF_32;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::UTF_32] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-1') {
            $c['charset'] = c_base_charset::ISO_8859_1;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_1] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-2') {
            $c['charset'] = c_base_charset::ISO_8859_2;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_2] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-3') {
            $c['charset'] = c_base_charset::ISO_8859_3;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_3] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-4') {
            $c['charset'] = c_base_charset::ISO_8859_4;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_4] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-5') {
            $c['charset'] = c_base_charset::ISO_8859_5;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_5] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-6') {
            $c['charset'] = c_base_charset::ISO_8859_6;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_6] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-7') {
            $c['charset'] = c_base_charset::ISO_8859_7;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_7] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-8') {
            $c['charset'] = c_base_charset::ISO_8859_8;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_8] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-9') {
            $c['charset'] = c_base_charset::ISO_8859_9;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_9] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-10') {
            $c['charset'] = c_base_charset::ISO_8859_10;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_10] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-11') {
            $c['charset'] = c_base_charset::ISO_8859_11;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_11] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-12') {
            $c['charset'] = c_base_charset::ISO_8859_12;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_12] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-13') {
            $c['charset'] = c_base_charset::ISO_8859_13;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_13] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-14') {
            $c['charset'] = c_base_charset::ISO_8859_14;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_14] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-15') {
            $c['charset'] = c_base_charset::ISO_8859_15;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_15] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-16') {
            $c['charset'] = c_base_charset::ISO_8859_16;
            $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_16] = $lowercase;
            unset($c['choice']);
          }
          else {
            $c['charset'] = NULL;
          }
        }
      }
      unset($lowercase);

      // sort the weight array.
      krsort($this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->p_prepend_array_value(NULL, $this->request[self::REQUEST_ACCEPT_CHARSET]['data']['weight']);
    }
    unset($this->request[self::REQUEST_ACCEPT_CHARSET]['data']['invalid']);

    $this->request[self::REQUEST_ACCEPT_CHARSET]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-datetime.
   *
   * This is not part of an official standard, it is provided to support a separate web archive standard.
   *
   * @see: http://www.mementoweb.org/guide/rfc/ID/#Accept-Memento-Datetime
   */
  private function p_load_request_accept_datetime() {
    if (p_validate_date_is_valid_rfc($this->headers['accept_datetime']) === FALSE) {
      $this->request[self::REQUEST_DATE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['accept_datetime']);
    if ($timestamp === FALSE) {
      $this->request[self::REQUEST_DATE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[self::REQUEST_ACCEPT_DATETIME]['defined'] = TRUE;
    $this->request[self::REQUEST_ACCEPT_DATETIME]['data'] = $timestamp;
    $this->request[self::REQUEST_ACCEPT_DATETIME]['invalid'] = FALSE;

    unset($timestamp);
  }

  /**
   * Load and process the HTTP request parameter: cache-control.
   *
   * Only 'no-cache' is supported at this time for requests.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   */
  private function p_load_request_cache_control() {
    $cache_control = $this->headers['cache_control'];
    if (empty($cache_control)) {
      $this->request[self::REQUEST_CACHE_CONTROL]['invalid'] = TRUE;
      unset($cache_control);
      return;
    }

    $this->request[self::REQUEST_CACHE_CONTROL]['data'] = array(
      'methods' => array(),
    );

    $integer_types = array(
      SELF::CACHE_CONTROL_MAX_AGE => 'max-age=',
      SELF::CACHE_CONTROL_MAX_STALE => 'max-stale=',
      SELF::CACHE_CONTROL_MIN_FRESH => 'min-fresh=',
    );

    $parts = mb_split(', ', $cache_control);
    foreach ($parts as $part) {
      $cleaned_up = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $part));
      if ($cleaned_up == 'no-cache') {
        $this->request[self::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[self::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_NO_CACHE] = SELF::CACHE_CONTROL_NO_CACHE;
      }
      elseif ($cleaned_up == 'no-store') {
        $this->request[self::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[self::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_NO_STORE] = SELF::CACHE_CONTROL_NO_STORE;
      }
      elseif ($cleaned_up == 'no-transform') {
        $this->request[self::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[self::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_NO_TRANSFORM] = SELF::CACHE_CONTROL_NO_TRANSFORM;
      }
      elseif ($cleaned_up == 'only-if-cached') {
        $this->request[self::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[self::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_ONLY_IF_CACHED] = SELF::CACHE_CONTROL_ONLY_IF_CACHED;
      }
      else {
        foreach ($integer_types as $type_id => $type_string) {
          if (mb_strpos($cleaned_up, $type_string) === FALSE) {
            continue;
          }

          $pieces = mb_split('=', $cleaned_up);
          if (!isset($pieces[1]) || !is_numeric($pieces[1]) || count($pieces) > 2) {
            $this->request[self::REQUEST_CACHE_CONTROL]['invalid'] = TRUE;
            unset($pieces);
            continue;
          }

          $this->request[self::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
          $this->request[self::REQUEST_CACHE_CONTROL]['data']['methods'][$type_id] = (int) $pieces[1];

          unset($pieces);
        }

        unset($type_id);
        unset($type_string);
      }

      unset($cleaned_up);
    }
    unset($part);
    unset($parts);
    unset($cache_control);

    $this->request[self::REQUEST_CACHE_CONTROL]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: connection.
   *
   * @see: self::pr_rfc_string_is_commad_token()
   * @see: https://tools.ietf.org/html/rfc7230
   * @see: https://tools.ietf.org/html/rfc7230#appendix-B
   */
  private function p_load_request_connection() {
    if (empty($this->headers['connection'])) {
      $this->request[self::REQUEST_CONNECTION]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['connection']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_CONNECTION]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_CONNECTION]['data'] = $this->pr_rfc_string_is_commad_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_CONNECTION]['data']['invalid']) {
      $this->request[self::REQUEST_CONNECTION]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_CONNECTION]['defined'] = TRUE;

      // rename 'tokens' array key to 'connection'.
      $this->request[self::REQUEST_CONNECTION]['data']['connection'] = $this->request[self::REQUEST_CONNECTION]['data']['tokens'];
      unset($this->request[self::REQUEST_CONNECTION]['data']['tokens']);
    }
    unset($this->request[self::REQUEST_CONNECTION]['data']['invalid']);
    unset($this->request[self::REQUEST_CONNECTION]['data']['current']);

    $this->request[self::REQUEST_CONNECTION]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: pragma.
   *
   * This is an older version of cache_control that supports 'no-cache'.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  private function p_load_request_pragma() {
    if ($this->request[self::REQUEST_CACHE_CONTROL]['defined']) {
      // this is a conflict, favor 'cache-control' over 'pragma'.
      return;
    }

    $pragma = $this->headers['pragma'];
    if (empty($pragma)) {
      // silently fail on invalid pragma.
      unset($pragma);
      return;
    }

    $cleaned_up = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $pragma));
    if ($cleaned_up == 'no-cache') {
      $this->request[self::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
      $this->request[self::REQUEST_CACHE_CONTROL]['data'][SELF::CACHE_CONTROL_NO_CACHE] = SELF::CACHE_CONTROL_NO_CACHE;
    }
    unset($cleaned_up);
    unset($pragma);

    $this->request[self::REQUEST_CACHE_CONTROL]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: cookie.
   *
   * @see: https://tools.ietf.org/html/rfc6265
   */
  private function p_load_request_cookies() {
    $this->request[self::REQUEST_COOKIE]['data'] = array();

    foreach ($_COOKIE as $cookie_name => $cookie_values) {
      $cookie = new c_base_cookie();
      $result = $cookie->set_name($cookie_name);

      if ($result instanceof c_base_return_false) {
        unset($cookie);
        unset($result);
        continue;
      }

      $cookie->do_pull();
      $this->request[self::REQUEST_COOKIE]['data'][$cookie_name] = $cookie;
      $this->request[self::REQUEST_COOKIE]['defined'] = TRUE;
      unset($cookie);
    }
    unset($cookie_name);
    unset($cookie_values);

    $this->request[self::REQUEST_COOKIE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: content-length.
   *
   * This value is represented in octets (8-bit bytes).
   *
   * @see: self::pr_rfc_string_is_digit()
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.2
   */
  private function p_load_request_content_length() {
    if (is_int($this->headers['content_length'])) {
      $this->request[self::REQUEST_CONTENT_LENGTH]['defined'] = TRUE;
      $this->request[self::REQUEST_CONTENT_LENGTH]['data'] = (int) $this->headers['content_length'];
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['content_length']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_CONTENT_LENGTH]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $parsed = $this->pr_rfc_string_is_digit($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      $this->request[self::REQUEST_CONTENT_LENGTH]['invalid'] = TRUE;
      unset($parsed);
      return;
    }

    $this->request[self::REQUEST_CONTENT_LENGTH]['defined'] = TRUE;
    $this->request[self::REQUEST_CONTENT_LENGTH]['data'] = intval($parsed['text']);
    unset($parsed);

    $this->request[self::REQUEST_CONTENT_LENGTH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: content-type.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  private function p_load_request_content_type() {
    $content_type = $this->headers['content_type'];
    if (empty($content_type)) {
      $this->request[self::REQUEST_CONTENT_TYPE]['invalid'] = TRUE;
      unset($content_type);
      return;
    }

    $content_type_parts = mb_split(';', $content_type);
    $content_type_part = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $content_type_parts[0]));

    $this->request[self::REQUEST_CONTENT_TYPE]['defined'] = TRUE;
    $this->request[self::REQUEST_CONTENT_TYPE]['data'] = $content_type_part;

    unset($content_type_part);
    unset($content_type_parts);
    unset($content_type);

    $this->request[self::REQUEST_CONTENT_TYPE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: date.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  private function p_load_request_date() {
    if (p_validate_date_is_valid_rfc($this->headers['date']) === FALSE) {
      $this->request[self::REQUEST_DATE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['date']);
    if ($timestamp === FALSE) {
      $this->request[self::REQUEST_DATE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[self::REQUEST_DATE]['defined'] = TRUE;
    $this->request[self::REQUEST_DATE]['data'] = $timestamp;

    unset($timestamp);

    $this->request[self::REQUEST_DATE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: expect.
   *
   * Only '100-continue' is defined by the standard and the server may respond with a 416 (Expectation Failed) as the status code.
   *
   * This is essentially a kind way to inform the server that 'hey, I've got a packet coming your way, will you accept it?'.
   * The server may also respond with 401 (Unauthorized) or 405 (Methid Not Allowed).
   *
   * The server should expect header fields like content-type, content-length, and even the operation, such as PUT /some/path.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.1.1
   */
  private function p_load_request_expect() {
    $expect = $this->headers['expect'];
    if (empty($expect)) {
      $this->request[self::REQUEST_EXPECT]['invalid'] = TRUE;
      unset($expect);
      return;
    }

    $this->request[self::REQUEST_EXPECT]['defined'] = TRUE;
    $this->request[self::REQUEST_EXPECT]['data'] = $expect;

    unset($expect);

    $this->request[self::REQUEST_EXPECT]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: from.
   *
   * Warning: never use PHP's filter_var('', FILTER_VALIDATE_EMAIL), it is non-compliant and fails to properly validate.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.1
   * @see: https://tools.ietf.org/html/rfc5322#section-3.4
   */
  private function p_load_request_from() {
    if (empty($this->headers['from'])) {
      $this->request[self::REQUEST_FROM]['invalid'] = TRUE;
      return;
    }

    // @todo: write a custom validation to ensure that the from email address is valid.
    $this->request[self::REQUEST_FROM]['defined'] = TRUE;
    $this->request[self::REQUEST_FROM]['data'] = $this->headers['from'];

    unset($from);

    $this->request[self::REQUEST_FROM]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: host.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.1
   * @see: https://tools.ietf.org/html/rfc5322#section-3.4
   */
  private function p_load_request_host() {
    if (empty($this->headers['host'])) {
      $this->request[self::REQUEST_HOST]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_HOST]['data'] = $this->p_parse_uri($this->headers['host']);

    if ($this->request[self::REQUEST_HOST]['data']['invalid']) {
      $this->request[self::REQUEST_HOST]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_HOST]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_HOST]['data']['invalid']);

    $this->request[self::REQUEST_HOST]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-match.
   *
   * @see: https://tools.ietf.org/html/rfc7234
   * @see: https://tools.ietf.org/html/rfc7232#section-3.1
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  private function p_load_request_if_match() {
    if (empty($this->headers['if_match'])) {
      $this->request[self::REQUEST_IF_MATCH]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_IF_MATCH]['data'] = $this->p_parse_if_entity_tag($this->headers['if_match']);

    if ($this->request[self::REQUEST_IF_MATCH]['data']['invalid']) {
      $this->request[self::REQUEST_IF_MATCH]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_IF_MATCH]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_IF_MATCH]['data']['current']);
    unset($this->request[self::REQUEST_IF_MATCH]['data']['invalid']);

    $this->request[self::REQUEST_IF_MATCH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-none-match.
   *
   * @see: https://tools.ietf.org/html/rfc7234
   * @see: https://tools.ietf.org/html/rfc7232#section-3.2
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  private function p_load_request_if_none_match() {
    if (empty($this->headers['if_none_match'])) {
      $this->request[self::REQUEST_IF_NONE_MATCH]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_IF_NONE_MATCH]['data'] = $this->p_parse_if_entity_tag_and_weak($this->headers['if_none_match']);

    if ($this->request[self::REQUEST_IF_NONE_MATCH]['data']['invalid']) {
      $this->request[self::REQUEST_IF_NONE_MATCH]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_IF_NONE_MATCH]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_IF_NONE_MATCH]['data']['invalid']);

    $this->request[self::REQUEST_IF_NONE_MATCH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-modified-since.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-3.3
   */
  private function p_load_request_if_modified_since() {
    if ($this->p_validate_date_is_valid_rfc($this->headers['if_modified_since']) === FALSE) {
      $this->request[self::REQUEST_IF_MODIFIED_SINCE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['if_modified_since']);
    if ($timestamp === FALSE) {
      $this->request[self::REQUEST_IF_MODIFIED_SINCE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[self::REQUEST_IF_MODIFIED_SINCE]['defined'] = TRUE;
    $this->request[self::REQUEST_IF_MODIFIED_SINCE]['data'] = $timestamp;

    unset($timestamp);

    $this->request[self::REQUEST_IF_MODIFIED_SINCE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-unmodified-since.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-3.4
   */
  private function p_load_request_if_unmodified_since() {
    if (p_validate_date_is_valid_rfc($this->headers['if_unmodified_since']) === FALSE) {
      $this->request[self::REQUEST_IF_UNMODIFIED_SINCE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['if_unmodified_since']);
    if ($timestamp === FALSE) {
      $this->request[self::REQUEST_IF_UNMODIFIED_SINCE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[self::REQUEST_IF_UNMODIFIED_SINCE]['defined'] = TRUE;
    $this->request[self::REQUEST_IF_UNMODIFIED_SINCE]['data'] = $timestamp;

    unset($timestamp);

    $this->request[self::REQUEST_IF_UNMODIFIED_SINCE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-range.
   *
   * The range can be either a date or an entity-tag.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-3.5
   * @see: https://tools.ietf.org/html/rfc7233#section-3.2
   */
  private function p_load_request_if_range() {
    if (p_validate_date_is_valid_rfc($this->headers['if_range'])) {
      $timestamp = strtotime($this->headers['if_range']);
      if ($timestamp === FALSE) {
        $this->request[self::REQUEST_IF_RANGE]['invalid'] = TRUE;
        $this->request[self::REQUEST_IF_RANGE]['data'] = array(
          'is_date' => TRUE,
        );
        unset($timestamp);
        return;
      }

      $this->request[self::REQUEST_IF_RANGE]['defined'] = TRUE;
      $this->request[self::REQUEST_IF_RANGE]['data'] = array(
        'range' => $timestamp,
        'is_date' => TRUE,
      );

      unset($timestamp);
      return;
    }

    // at this point, assume the if-range is an entity tag.
    $if_range = $this->headers['if_range'];
    if (empty($if_range)) {
      $this->request[self::REQUEST_IF_RANGE]['if_range'] = TRUE;
      $this->request[self::REQUEST_IF_RANGE]['data']['is_date'] = FALSE;
      unset($if_range);
      return;
    }

    $this->request[self::REQUEST_IF_RANGE]['data'] = $this->p_parse_if_entity_tag_and_weak($if_range);
    $this->request[self::REQUEST_IF_RANGE]['data']['is_date'] = FALSE;

    if ($this->request[self::REQUEST_IF_RANGE]['data']['invalid']) {
      $this->request[self::REQUEST_IF_RANGE]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_IF_RANGE]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_IF_RANGE]['data']['invalid']);

    unset($if_range);

    $this->request[self::REQUEST_IF_RANGE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: range.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-3.1
   */
  private function p_load_request_range() {
    if (empty($this->headers['range'])) {
      $this->request[self::REQUEST_RANGE]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['range']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_RANGE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_RANGE]['data'] = $this->pr_rfc_string_is_range($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_RANGE]['data']['invalid']) {
      $this->request[self::REQUEST_RANGE]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_RANGE]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_RANGE]['data']['invalid']);

    $this->request[self::REQUEST_RANGE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: max-forwards.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.1.2
   */
  private function p_load_request_max_forwards() {
    if (is_int($this->headers['max_forwards'])) {
      $this->request[self::REQUEST_MAX_FORWARDS]['defined'] = TRUE;
      $this->request[self::REQUEST_MAX_FORWARDS]['data'] = (int) $this->headers['max_forwards'];
    }

    $text = $this->pr_rfc_string_prepare($this->headers['max_forwards']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_MAX_FORWARDS]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $parsed = pr_rfc_string_is_digit($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      $this->request[self::REQUEST_MAX_FORWARDS]['invalid'] = TRUE;
      unset($parsed);
      return;
    }

    $this->request[self::REQUEST_MAX_FORWARDS]['defined'] = TRUE;
    $this->request[self::REQUEST_MAX_FORWARDS]['data'] = intval($parsed['text']);

    unset($parsed);

    $this->request[self::REQUEST_MAX_FORWARDS]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: origin.
   *
   * Errata: I cannot find Origin specified in the RFC's that I looked at.
   * - Either I am completely overlooking it or its defined in some other standard.
   * - I will use wikipedia's notes to define and utilize this field.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  private function p_load_request_origin() {
    if (empty($this->headers['origin'])) {
      $this->request[self::REQUEST_ORIGIN]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_ORIGIN]['data'] = $this->p_parse_uri($this->headers['origin']);

    if ($this->request[self::REQUEST_ORIGIN]['data']['invalid']) {
      $this->request[self::REQUEST_ORIGIN]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_ORIGIN]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_ORIGIN]['data']['invalid']);

    $this->request[self::REQUEST_ORIGIN]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: referer.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.2
   */
  private function p_load_request_referer() {
    if (empty($this->headers['referer'])) {
      $this->request[self::REQUEST_REFERER]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_REFERER]['data'] = $this->p_parse_uri($this->headers['referer']);

    if ($this->request[self::REQUEST_REFERER]['data']['invalid']) {
      $this->request[self::REQUEST_REFERER]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_REFERER]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_REFERER]['data']['invalid']);

    $this->request[self::REQUEST_REFERER]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: te.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-4.3
   */
  private function p_load_request_te() {
    if (empty($this->headers['te'])) {
      $this->request[self::REQUEST_TE]['te'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['te']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_TE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[self::REQUEST_TE]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_TE]['data']['invalid']) {
      $this->request[self::REQUEST_TE]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_TE]['defined'] = TRUE;

      // convert the known values into integers for improved processing.
      foreach ($this->request[self::REQUEST_TE]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $lowercase = mb_strtolower($c['choice']);
          if ($c['choice'] == 'compress') {
            $c['encoding'] = self::ENCODING_COMPRESS;
          }
          elseif ($c['choice'] == 'deflate') {
            $c['encoding'] = self::ENCODING_DEFLATE;
          }
          elseif ($c['choice'] == 'gzip') {
            $c['encoding'] = self::ENCODING_GZIP;
          }
          elseif ($c['choice'] == 'bzip') {
            $c['encoding'] = self::ENCODING_BZIP;
          }
          elseif ($c['choice'] == 'lzo') {
            $c['encoding'] = self::ENCODING_LZO;
          }
          elseif ($c['choice'] == 'xz') {
            $c['encoding'] = self::ENCODING_XZ;
          }
          elseif ($c['choice'] == 'exit') {
            $c['encoding'] = self::ENCODING_EXI;
          }
          elseif ($c['choice'] == 'identity') {
            $c['encoding'] = self::ENCODING_IDENTITY;
          }
          elseif ($c['choice'] == 'sdch') {
            $c['encoding'] = self::ENCODING_SDCH;
          }
          elseif ($c['choice'] == 'pg') {
            $c['encoding'] = self::ENCODING_PG;
          }
          else {
            $c['encoding'] = NULL;
          }
        }
      }
      unset($lowercase);
    }
    unset($this->request[self::REQUEST_TE]['data']['invalid']);

    $this->request[self::REQUEST_TE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: user-agent.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_user_agent() {
    if (empty($this->headers['user_agent'])) {
      $this->request[self::REQUEST_USER_AGENT]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['user_agent']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_USER_AGENT]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // make sure agent is valid text.
    $agent = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($agent['invalid']) {
      $this->request[self::REQUEST_USER_AGENT]['invalid'] = TRUE;
      unset($agent);
      return;
    }

    $this->request[self::REQUEST_USER_AGENT]['data'] = $this->p_parse_user_agent($agent['text']);
    unset($agent);

    if ($this->request[self::REQUEST_USER_AGENT]['data']['invalid']) {
      $this->request[self::REQUEST_USER_AGENT]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_USER_AGENT]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_USER_AGENT]['data']['invalid']);

    $this->request[self::REQUEST_USER_AGENT]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: upgrade.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_upgrade() {
    if (empty($this->headers['upgrade'])) {
      $this->request[self::REQUEST_UPGRADE]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['upgrade']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_UPGRADE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // @todo: future versions may do something with this, until then just check for text.
    $this->request[self::REQUEST_UPGRADE]['data'] = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_UPGRADE]['data']['invalid']) {
      $this->request[self::REQUEST_UPGRADE]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_UPGRADE]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_UPGRADE]['data']['invalid']);

    $this->request[self::REQUEST_UPGRADE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: via.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_via() {
    if (empty($this->headers['via'])) {
      $this->request[self::REQUEST_VIA]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['via']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_VIA]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // @todo: future versions may do something with this, until then just check for text.
    $this->request[self::REQUEST_VIA]['data'] = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_VIA]['data']['invalid']) {
      $this->request[self::REQUEST_VIA]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_VIA]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_VIA]['data']['invalid']);

    $this->request[self::REQUEST_VIA]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: warning.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.5
   */
  private function p_load_request_warning() {
    if (empty($this->headers['warning'])) {
      $this->request[self::REQUEST_WARNING]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['warning']);
    if ($text['invalid']) {
      $this->request[self::REQUEST_WARNING]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // @todo: future versions may do something with this, until then just check for text.
    $this->request[self::REQUEST_WARNING]['data'] = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[self::REQUEST_WARNING]['data']['invalid']) {
      $this->request[self::REQUEST_WARNING]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_WARNING]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_WARNING]['data']['invalid']);

    $this->request[self::REQUEST_WARNING]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: checksum_header.
   *
   * The following format is expected:
   * - 1*(atext):1*(atext):1*(atext)
   *
   * The header should have either 'partial:checksum_type:checksum_value' or 'complete:checksum_type:checksum_value'.
   * The checksum_value is stored in base64.
   *
   * The content checksum represents the checksum of the HTTP content (HTTP packet body).
   * This should already be defined if and when a checksum_header is used.
   *
   * The header checksum represents the checksum of the HTTP header when only the checksum_header field is missing.
   * The checksum_content header should not be removed when creating or validating the checksum_header.
   *
   * @see: self::p_parse_checksum()
   */
  private function p_load_request_checksum_header() {
    if (empty($this->headers['checksum_header'])) {
      $this->request[self::REQUEST_CHECKSUM_HEADER]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_CHECKSUM_HEADER]['data'] = $this->p_parse_checksum($this->headers['checksum_header']);

    if ($this->request[self::REQUEST_CHECKSUM_HEADER]['data']['invalid']) {
      $this->request[self::REQUEST_CHECKSUM_HEADER]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_CHECKSUM_HEADER]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_CHECKSUM_HEADER]['data']['invalid']);

    $this->request[self::REQUEST_CHECKSUM_HEADER]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: checksum_headers.
   *
   * The following format is expected:
   * - 1*(atext)*(*(wsp) "," *(wsp)1*(atext))
   *
   * The headers is a comma separated list of http headers present at the time the header checksum was generated.
   * This is necessary because anything en-route may add or alter headers and the checksum needs to still validate for the checksum provided.
   * This also gives the client or server and idea on what was added and what was not.
   *
   * @see: self::p_parse_checksum_headers()
   */
  private function p_load_request_checksum_headers() {
    if (empty($this->headers['checksum_header'])) {
      $this->request[self::REQUEST_CHECKSUM_HEADERS]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_CHECKSUM_HEADERS]['data'] = $this->p_parse_checksum_headers($this->headers['checksum_headers']);

    if ($this->request[self::REQUEST_CHECKSUM_HEADERS]['data']['invalid']) {
      $this->request[self::REQUEST_CHECKSUM_HEADERS]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_CHECKSUM_HEADERS]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_CHECKSUM_HEADERS]['data']['invalid']);

    $this->request[self::REQUEST_CHECKSUM_HEADERS]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: checksum_content.
   *
   * The following format is expected:
   * - 1*(atext):1*(atext):1*(atext)
   *
   * The header should have either 'partial:checksum_type:checksum_value' or 'complete:checksum_type:checksum_value'.
   * The checksum_value is stored in base64.
   *
   * The content checksum represents the checksum of the HTTP content (HTTP packet body).
   * This should already be defined if and when a checksum_header is used.
   *
   * This is not part of any official standard, but is added as an additional feature of this project.
   * Unlike content_md5, this does not require additional headers.
   *
   * @see: self::p_parse_checksum()
   */
  private function p_load_request_checksum_content() {
    if (empty($this->headers['checksum_content'])) {
      $this->request[self::REQUEST_CHECKSUM_CONTENT]['invalid'] = TRUE;
      return;
    }

    $this->request[self::REQUEST_CHECKSUM_CONTENT]['data'] = $this->p_parse_checksum($this->headers['checksum_content']);

    if ($this->request[self::REQUEST_CHECKSUM_CONTENT]['data']['invalid']) {
      $this->request[self::REQUEST_CHECKSUM_CONTENT]['invalid'] = TRUE;
    }
    else {
      $this->request[self::REQUEST_CHECKSUM_CONTENT]['defined'] = TRUE;
    }
    unset($this->request[self::REQUEST_CHECKSUM_CONTENT]['data']['invalid']);

    $this->request[self::REQUEST_CHECKSUM_CONTENT]['invalid'] = FALSE;
  }

  /**
   * Store raw values for fields that will not have specific parsing done to them.
   *
   * The 'raw' value will be made lower case and then trimmed.
   *
   * @param string $field
   *   The name of the field as it is defined in $this->headers.
   * @param int $key
   *   The array key in which to store the data in.
   * @param int $max_length
   *   The maximum length that should be allowed in the data.
   */
  private function p_load_request_rawish($field, $key, $max_length = NULL) {
    $raw = $this->headers[$field];
    if (empty($raw)) {
      unset($raw);
      return;
    }

    $this->request[$key]['defined'] = TRUE;
    $this->request[$key]['invalid'] = FALSE;

    if (is_null($max_length)) {
      $this->request[$key]['data'] = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $raw));
    }
    else {
      $this->request[$key]['data'] = mb_substr(mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $raw)), 0, $max_length);
    }
    unset($raw);
  }

  /**
   * Store raw values for unknown fields
   *
   * This is identical to self::p_load_request_rawish() except the results are stored in an array.
   *
   * @param string $field
   *   The name of the field as it is defined in $this->headers.
   * @param int $key
   *   The array key in which to store the data in.
   * @param int $max_length
   *   The maximum length that should be allowed in the data.
   */
  private function p_load_request_unknown($field, $key, $max_length = NULL) {
    $raw = $this->headers[$field];
    if (empty($raw)) {
      unset($raw);
      return;
    }

    $this->request[$key]['defined'] = TRUE;
    $this->request[$key]['invalid'] = FALSE;

    if (is_null($max_length)) {
      $this->request[$key]['data'][$field] = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $raw));
    }
    else {
      $this->request[$key]['data'][$field] = mb_substr(mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $raw)), 0, $max_length);
    }
    unset($raw);
  }

  /**
   * Validate that the given date string is in one of the supported rfc formats.
   *
   * This function is necessary because simply converting the date string to a timestamp via strtotime() allows to many other possibilities.
   * To prevent unwanted dates, such as 'now', convert the passed timestamp into an rfd1123 into a date string.
   * The converted timestamp and the original datestring must match exactly.
   *
   * @todo: review this function to see if any utf8 support needs to be added.
   *
   * @param string $original
   *   Tne original, unaltered, date string.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.1
   * @see: https://tools.ietf.org/html/rfc5322#section-3.3
   */
  private function p_validate_date_is_valid_rfc($original) {
    $timestamp = strtotime($original);
    if ($timestamp === FALSE) {
      unset($timestamp);
      return FALSE;
    }

    $raw = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $original));

    // rfc5322 is the preferred/recommended format.
    $rfc5322 = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', date(self::TIMESTAMP_RFC_5322, $timestamp)));
    if ($raw == $rfc5322) {
      unset($raw);
      unset($timestamp);
      unset($rfc5322);
      return TRUE;
    }
    unset($rfc5322);

    $rfc1123 = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', date(self::TIMESTAMP_RFC_1123, $timestamp)));
    if ($raw == $rfc1123) {
      unset($raw);
      unset($timestamp);
      unset($rfc1123);
      return TRUE;
    }
    unset($rfc1123);

    $rfc850 = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', date(self::TIMESTAMP_RFC_850, $timestamp)));
    if ($raw == $rfc850) {
      unset($raw);
      unset($timestamp);
      unset($rfc850);
      return TRUE;
    }
    unset($rfc850);

    unset($raw);
    unset($timestamp);
    return FALSE;
  }

  /**
   * Process the accept header value sub parts.
   *
   * @param string $super
   *   The string that contains the value and the priority.
   * @param string $value
   *   The value to be returned.
   * @param string $priority
   *   The priority to be returned.
   */
  private function p_process_accept_parts_sub($super, &$value, &$priority) {
    $parts_sub = mb_split(self::DELIMITER_ACCEPT_SUB, $super);

    $part_sub_value = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $parts_sub[0]));
    $part_sub_priority = NULL;
    if (count($parts_sub) > 1) {
      $part_sub_priority = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $parts_sub[1]));
    }

    if (self::p_length_string($part_sub_value) > 2) {
      if ($part_sub_value[0] == self::DELIMITER_ACCEPT_SUB_0 && $part_sub_value[1] == self::DELIMITER_ACCEPT_SUB_1) {
        $value = $part_sub_priority;
        $parts_sub_priority = $part_sub_value;
      }
    }
    else {
      $value = $part_sub_value;
    }
    unset($part_sub_value);

    if (!is_null($part_sub_priority) && self::p_length_string($part_sub_priority) > 2 && $part_sub_priority == 'q' && $part_sub_priority == '=') {
      $part = preg_replace('/(^\s+)|(\s+$)/us', '', str_replace(self::DELIMITER_ACCEPT_SUB_0 . self::DELIMITER_ACCEPT_SUB_1, '', $part_sub_priority));

      if (is_numeric($part)) {
        $priority = sprintf('%.1f', (float) $part);
      }
      else {
        $priority = '1.0';
      }

      unset($part);
    }
    else {
      $priority = '1.0';
    }

    unset($part_sub_priority);
    unset($parts_sub);
  }

  /**
   * Decode and check that the given uri is valid.
   *
   * This does not decode the uri, it separates it into its individual parts.
   *
   * Validation is done according to rfc3986.
   *
   *   foo://example.com:8042/over/there?name=ferret#nose
   *   \_/   \______________/\_________/ \_________/ \__/
   *    |           |            |            |        |
   *  scheme    authority       path        query   fragment
   *    |   _____________________|__
   *   / \ /                        \
   *   urn:example:animal:ferret:nose
   *
   *
   * @param string $uri
   *   The url to validate and decode.
   *
   * @return array
   *   A decoded uri split into its different parts inside an array.
   *   An array key called 'invalid' exists to designate that the uri is invalid.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  private function p_parse_uri($uri) {
    $result = array(
      'scheme' => array(),
      'authority' => array(),
      'path' => array(),
      'query' => array(),
      'fragment' => array(),
      'invalid' => FALSE,
    );

    // @todo: completely rewrite below.
    $result['invalid'] = TRUE;
/*
    $matches = array();
    $matched = preg_match('!^((\w[=|\w|\d|\s|\.|-|_|~|%]*)*:)*([^#|?]*)(\?([^#]+))*(#(.+))*$!iu', $uri, $matches);

    if ($matched == FALSE || !array_key_exists(3, $matches)) {
      unset($address);
      unset($matches);
      unset($matched);
      $result['invalid'] = TRUE;
      return $result;
    }
    unset($matched);


    // process scheme.
    if (array_key_exists(2, $matches) && self::p_length_string($matches[2]) > 0) {
      $combined = $matches[3];
      if (array_key_exists(4, $matches)) {
        $combined .= $matches[4];
      }
      if (array_key_exists(6, $matches)) {
        $combined .= $matches[6];
      }

      $scheme_string = preg_replace('!:' . $combined . '$!iu', '', $matches[0]);
      $result['scheme'] = mb_split(':', $scheme_string);
      unset($scheme_string);
      unset($combined);

      foreach ($result['scheme'] as &$s) {
        $s = urldecode($s);
      }
      unset($s);
    }


    // process authority.
    if (self::p_length_string($matches[3]) > 0) {
      // rfc6854 designates multiple uris, separated by commas.
      $authority = mb_split(',', $matches[3]);
      foreach ($authority as $a) {
        $sub_matches = array();
        $sub_matched = preg_match('!^(//|/|)(([^@]+)@)*(.*)$!iu', $a, $sub_matches);

        if ($sub_matched === FALSE || !isset($sub_matches[4])) {
          $result['invalid'] = TRUE;

          unset($sub_matches);
          unset($sub_matched);
          continue;
        }


        // process user information.
        $information_matches = array();
        if (preg_match('@^([=|!|$|&|\'|(|)|\*|\+|,|;|\w|\d|-|\.|_|~|%|\s]*)(:|)$@iu', $sub_matches[3], $information_matches) === FALSE || !isset($information_matches[1])) {
          $result['invalid'] = TRUE;

          unset($information_matches);
          unset($sub_matches);
          unset($sub_matched);
          continue;
        }

        $authority_setting = array(
          'type_path' => self::URI_PATH_THIS,
          'type_host' => 0,
          'user' => urldecode($information_matches[1]),
          'host' => NULL,
          'port' => NULL,
        );
        unset($information_matches);


        // process host information.
        if ($sub_matches[1] == '//') {
          $authority_setting['type_path'] = self::URI_PATH_SITE;
        }
        elseif ($sub_matches[1] == '/') {
          $authority_setting['type_path'] = self::URI_PATH_BASE;
        }

        $ipv6_matches = array();
        if (preg_match('@^\[([^\]]+)\](:\d+$|$)@iu', $sub_matches[4], $ipv6_matches) !== FALSE && isset($ipv6_matches[1])) {
          $authority_setting['type_host'] = self::URI_HOST_IPV6;

          $ip = inet_pton($ipv6_matches[1]);
          if ($ip === FALSE) {
            $result['invalid'] = TRUE;

            unset($ip);
            unset($ipv6_matches);
            continue;
          }

          $authority_setting['host'] = inet_ntop($ip);
          unset($ip);

          if (isset($ipv6_matches[2]) && self::p_length_string($ipv6_matches[2]) > 0) {
            $authority_setting['port'] = (int) $ipv6_matches[2];
          }

          // @todo: ipvfuture is actually embedded inside of the the double brackets used by ipv6.
          //        to support this, the ipv6 regex must be modified to check for the ipvfuture parameters.
          // $authority_setting['type_host'] = self::URI_HOST_IPVX;
          // '@v[\d|a|b|c|d|e|f]\.([=|!|$|&|\'|(|)|\*|\+|,|;|\w|\d|-|\.|_|~|%|:]*)@i'
        }
        unset($ipv6_matches);

        $ipv4_matches = array();
        if (is_null($authority_setting['host']) && preg_match('@(\d+\.\d+\.d+\.d+)(:(\d+)|)$@iu', $sub_matches[4], $ipv4_matches) !== FALSE && isset($ipv4_matches[1])) {
          $authority_setting['type_host'] = self::URI_HOST_IPV4;

          $ip = inet_pton($ipv4_matches[1]);
          if ($ip === FALSE) {
            $result['invalid'] = TRUE;

            unset($ip);
            unset($ipv4_matches);
            continue;
          }

          $authority_setting['host'] = inet_ntop($ip);
          unset($ip);

          if (isset($ipv4_matches[3]) && self::p_length_string($ipv4_matches[3]) > 0) {
            $authority_setting['port'] = (int) $ipv4_matches[3];
          }
        }
        unset($ipv4_matches);

        $ipv4_matches = array();
        if (is_null($authority_setting['host']) && preg_match('@(\d+\.\d+\.d+\.d+)(:(\d+)|)$@iu', $sub_matches[4], $ipv4_matches) !== FALSE && isset($ipv4_matches[1])) {
          $authority_setting['type_host'] = self::URI_HOST_IPV4;

          $ip = inet_pton($ipv4_matches[1]);
          if ($ip === FALSE) {
            $result['invalid'] = TRUE;

            unset($ip);
            unset($ipv4_matches);
            continue;
          }

          $authority_setting['host'] = inet_ntop($ip);
          unset($ip);

          if (isset($ipv4_matches[3]) && self::p_length_string($ipv4_matches[3]) > 0) {
            $authority_setting['port'] = (int) $ipv4_matches[3];
          }
        }
        unset($ipv4_matches);

        $name_matches = array();
        if (is_null($authority_setting['host']) && preg_match('@((=|\w|\d|-|\.|_|~|\!|$|&|\'|(|)|\*|\+|,|;)+)(:(\d+)|)$@iu', $sub_matches[4], $name_matches) !== FALSE && isset($name_matches[1])) {
          $authority_setting['type_host'] = self::URI_HOST_NAME;
          $authority_setting['host'] = $name_matches[2];

          if (isset($name_matches[4]) && self::p_length_string($name_matches[4]) > 0) {
            $authority_setting['port'] = (int) $name_matches[4];
          }
        }
        unset($name_matches);

        $result['authority'][] = $authority_setting;

        unset($authority_setting);
        unset($sub_matches);
        unset($sub_matched);
      }

      unset($a);
      unset($authority);
    }


    // process query.
    if (array_key_exists(5, $matches) && self::p_length_string($matches[5]) > 0) {
      $query_parts = mb_split(',', $matches[5]);

      foreach ($query_parts as $qp) {
        $qp_parts = mb_split('=', $qp, 2);

        if (is_array($qp_parts) && isset($qp_parts[0])) {
          $decoded = urldecode($qp_parts[0]);
          if (isset($qp_parts[1])) {
            $result['query'][$decoded] = urldecode($qp_parts[1]);
          }
          else {
            $result['query'][$decoded] = NULL;
          }
          unset($decoded);
        }
      }
      unset($qp);
      unset($query_parts);
    }


    // process fragment.
    if (array_key_exists(7, $matches) && self::p_length_string($matches[7]) > 0) {
      $result['fragment'][] = urldecode($matches[7]);
    }

    unset($matches);
*/
    return $result;
  }

  /**
   * Decode and check that the given string is a valid entity tag such as with if-match (but do not test for weakness).
   *
   * Validation is done according to rfc7232.
   *
   * @param string $match
   *   The string to validate and decode.
   *
   * @return array
   *   The processed information:
   *   - 'matches': An array of processed entity tags.
   *   - 'any': A boolean that when TRUE means any matches are allowed and the 'matches' key will be an empty array.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc7232
   */
  private function p_parse_if_entity_tag($match) {
    $result = array(
      'matches' => array(),
      'any' => FALSE,
      'invalid' => FALSE,
    );

    $stop = self::p_length_string($match) + 1;
    if ($stop == 0) {
      unset($stop);

      $result['invalid'] = TRUE;
      return $result;
    }

    $text = $this->pr_rfc_string_prepare($match);
    if ($text['invalid']) {
      unset($stop);
      unset($text);

      $result['invalid'] = TRUE;
      return $result;
    }

    $current = 0;

    // The standard specifies the use of a wildcard '*', but only accept wildcard if it is the only character (with no whitespace).
    if ($stop == 1 && $text['ordinals'][$current] == c_base_ascii::ASTERISK) {
      unset($stop);
      unset($text);
      unset($current);

      $result['any'] = TRUE;
      return $result;
    }

    while ($current < $stop) {
      $parsed = $this->pr_rfc_string_is_quoted_string($text['ordinals'], $text['characters'], $current, self::STOP_AT_CLOSING_CHARACTER);
      $current = $parsed['current'];

      if ($parsed['invalid']) {
        $result['invalid'] = TRUE;
        unset($parsed);
        break;
      }

      $result['matches'][] = $parsed['text'];

      // The standard does not immediately define what the rules are for a list of entity tags, but they do imply comma separated.
      // therefore, until I learn the specifics, I am assuming that only FWS and comma are allowed.
      $current++;
      while ($current < $stop) {
        if ($text['ordinals'][$current] == c_base_ascii::COMMA) {
          $current++;
          break;
        }

        if (!$this->pr_rfc_char_is_fws($text['ordinals'][$current])) {
          $result['invalid'] = TRUE;
          break;
        }

        $current++;
      }

      if ($result['invalid']) {
        break;
      }
    }
    unset($current);
    unset($stop);
    unset($text);

    return $result;
  }

  /**
   * Decode and check for weak or strong entity tag matches, such as with: if-none-match or if-range.
   *
   * Validation is done according to rfc7232.
   *
   * @param string $match
   *   The string to validate and decode.
   *
   * @return array
   *   The processed information:
   *   - 'matches': An array of processed entity tags.
   *   - 'weak': An array of booleans defining whether the corrosponding match in the matches array is weak or not.
   *   - 'any': A boolean that when TRUE means any matches are allowed and the 'matches' key will be an empty array.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc7232
   */
  private function p_parse_if_entity_tag_and_weak($match) {
    $result = array(
      'matches' => array(),
      'weak' => array(),
      'any' => FALSE,
      'invalid' => FALSE,
    );

    $stop = self::p_length_string($match) + 1;
    if ($stop == 0) {
      unset($stop);

      $result['invalid'] = TRUE;
      return $result;
    }

    $text = $this->pr_rfc_string_prepare($match);
    if ($text['invalid']) {
      unset($stop);
      unset($text);

      $result['invalid'] = TRUE;
      return $result;
    }

    $current = 0;

    // The standard specifies the use of a wildcard '*', but only accept wildcard if it is the only character (with no whitespace).
    if ($stop == 1 && $text['ordinals'][$current] == c_base_ascii::ASTERISK) {
      unset($stop);
      unset($text);
      unset($current);

      $result['any'] = TRUE;
      return $result;
    }

    while ($current < $stop) {
      $weak = FALSE;
      if ($text['ordinals'][$current] == c_base_ascii::W) {
        $current++;
        if ($current >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }

        if ($text['ordinals'][$current] == c_base_ascii::SLASH_FORWARD) {
          $weak = TRUE;
          $current++;
          continue;
        }

        $result['invalid'] = TRUE;
        break;
      }
      elseif ($text['ordinals'][$current] == c_base_ascii::QUOTE_DOUBLE) {
        $current++;
        if ($current >= $stop) {
          $result['invalid'] = TRUE;
          break;
        }

        $parsed = $this->pr_rfc_string_is_entity_tag($text['ordinals'], $text['characters'], $current, $stop);
        $current = $parsed['current'];

        if ($parsed['invalid']) {
          $result['invalid'] = TRUE;
          unset($parsed);
          break;
        }

        $result['matches'][] = $parsed['text'];
        $result['weak'][] = $weak;

        // handle comma separated values.
        $current++;
        while ($current < $stop) {
          if ($text['ordinals'][$current] == c_base_ascii::COMMA) {
            $current++;

            // seek past all WS.
            while ($current < $stop) {
              if (!$this->pr_rfc_char_is_wsp($text['ordinals'][$current])) {
                break;
              }

              $current++;
            }

            break;
          }

          if (!$this->pr_rfc_char_is_wsp($text['ordinals'][$current])) {
            $result['invalid'] = TRUE;
            break;
          }

          $current++;
        }

        if ($result['invalid']) {
          break;
        }
      }
      else {
        $result['invalid'] = TRUE;
        break;
      }
    }
    unset($current);
    unset($stop);
    unset($text);
    unset($weak);

    return $result;
  }

  /**
   * Decode and check for weak or strong entity tag matches, such as with: user-agent.
   *
   * The user agent structure very poorly designed and provides no straight-forward and consistent way to present information.
   * The approach used here is a quick and simply approach that is intended to be good enough.
   * Do not expect it to be completely accuarete.
   *
   * Being to detailed on agents can cause significant performance penalties that has in the past forced me to implement this particular design.
   *
   * @param string $match
   *   The string to validate and decode.
   *
   * @return array
   *   The processed information:
   *   - 'full': The entire agent string.
   *   - 'name_machine': a machine-friendly name for the client, or null if undefined or unknown.
   *   - 'name_human': a human-friendly name for the client, or null if undefined or unknown.
   *   - 'engine_name_machine': a machine-friendly name for the client's engine, or null if undefined or unknown.
   *   - 'engine_name_human': a human-friendly name for the client's engine, or null if undefined or unknown.
   *   - 'version_major': A major version number of the client, or null if undefined or unknown.
   *   - 'version_engine': A major version number of the client's engine, or null if undefined or unknown.
   *   - 'is_ie_edge': a boolean representing whether or not this is IE Edge.
   *   - 'is_ie_compatibility': a boolean representing whether or not this is IE in "compatibility mode".
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   *
   * @see: https://tools.ietf.org/html/rfc7232
   */
  private function p_parse_user_agent($agent) {
    $result = array(
      'full' => $agent,
      'name_machine' => NULL,
      'name_human' => NULL,
      'engine_name_machine' => NULL,
      'engine_name_human' => NULL,
      'version_major' => NULL,
      'version_engine' => NULL,
      'is_ie_edge' => FALSE,
      'is_ie_compatibility' => FALSE,
      'invalid' => FALSE,
    );

    $agent_matches = array();
    $agent_matched = preg_match('/^[^(]*\(([^)]*)\)(.*)$/iu', $agent, $agent_matches);

    if (!$agent_matched) {
      $result['invalid'] = TRUE;
      unset($agent_matches);
      unset($agent_matched);
      return $result;
    }
    unset($agent_matched);

    if (!isset($agent_matches[1])) {
      unset($agent_matches);
      return $result;
    }


    // preprocess the agent in an attempt to determine the engine and therefore basic information.
    $agent_pieces = mb_split(';', $agent_matches[1]);

    if (empty($agent_pieces)) {
      unset($agent_pieces);
      unset($agent_matches);
      return $result;
    }

    foreach ($agent_pieces as $agent_piece) {
      $pieces = mb_split('/', $agent_piece);

      // ignore unknown structure.
      if (count($pieces) > 2) {
        continue;
      }

      if (isset($pieces[1])) {
        $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[0]));
        $lower_piece_2 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[1]));

        if ($lower_piece_1 == 'trident') {
          $result['engine_name_machine'] = 'trident';
          $result['engine_name_human'] = 'Trident';
          $result['version_engine'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);

          $result['name_machine'] = 'ie';
          $result['name_human'] = 'Internet Explorer';
        }
        elseif ($lower_piece_1 == 'gecko') {
          $result['engine_name_machine'] = 'gecko';
          $result['engine_name_human'] = 'Gecko';
          $result['version_engine'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
        }
        elseif ($lower_piece_1 == 'presto') {
          $result['engine_name_machine'] = 'presto';
          $result['engine_name_human'] = 'Presto';
          $result['version_engine'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
        }

        unset($lower_piece_1);
        unset($lower_piece_2);
      }
      elseif (isset($pieces[0])) {
        $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[0]));

        if (!empty($lower_piece_1)) {
          if (preg_match('/^msie \d/iu', $lower_piece_1)) {
            $lower_piece_2 = preg_replace('/^msie /iu', '', $lower_piece_1);

            $result['name_machine'] = 'ie';
            $result['name_human'] = 'Internet Explorer';

            if (is_null($result['version_major'])) {
              $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
            }
          }
          elseif (strpos($lower_piece_1, 'midori')) {
            $result['name_machine'] = 'midori';
            $result['name_human'] = 'Midori';
            $result['engine_name_machine'] = 'webkit';
          }
          else {
            // Browsers, such as Internet Explorer, use 'rv:number', such as: 'rv:11'.
            $revision_parts = explode(':', $lower_piece_1);
            if (count($revision_parts) == 2 && $revision_parts[0] == 'rv' && is_numeric($revision_parts[1])) {
              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) $revision_parts[1];
              }
            }
            unset($revision_parts);
          }
        }

        unset($lower_piece_1);
      }
    }
    unset($pieces);
    unset($agent_pieces);


    // determine the client agent information.
    if (isset($agent_matches[2])) {
      $agent_pieces = mb_split('\s', $agent_matches[2]);

      if (!empty($agent_pieces)) {
        foreach ($agent_pieces as $agent_piece) {
          $pieces = mb_split('/', $agent_piece);

          // ignore unknown structure.
          if (count($pieces) > 3) {
            continue;
          }

          if (isset($pieces[1])) {
            $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[0]));
            $lower_piece_2 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[1]));

            if ($lower_piece_1 == 'applewebkit') {
              $result['engine_name_machine'] = 'webkit';
              $result['engine_name_human'] = 'Webkit';
              $result['version_engine'] = (int) $lower_piece_2;

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }
            }
            elseif ($lower_piece_1 == 'safari') {
              // safari is used in a lot of places that is not safari, so use safari only if it is the only agent detected.
              if (is_null($result['name_machine'])) {
                $result['name_machine'] = 'safari';
                $result['name_human'] = 'Safari';

                if (is_null($result['version_major'])) {
                  $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
                }

                if (is_null($result['engine_name_machine'])) {
                  $result['engine_name_machine'] = 'webkit';
                  $result['engine_name_human'] = 'Webkit';
                }
              }
            }
            elseif ($lower_piece_1 == 'firefox') {
              $result['name_machine'] = 'firefox';
              $result['name_human'] = 'Firefox';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }

              $result['engine_name_machine'] = 'gecko';
              $result['engine_name_human'] = 'Gecko';
            }
            elseif ($lower_piece_1 == 'seamonkey') {
              $result['name_machine'] = 'seamonkey';
              $result['name_human'] = 'Seamonkey';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }

              $result['engine_name_machine'] = 'gecko';
              $result['engine_name_human'] = 'Gecko';
            }
            elseif ($lower_piece_1 == 'gecko') {
              if (is_null($result['version_engine']) && (is_null($result['engine_name_machine']) || $result['engine_name_machine'] == 'gecko')) {
                $result['version_engine'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
                $result['engine_name_machine'] = 'gecko';
                $result['engine_name_human'] = 'Gecko';
              }
            }
            elseif ($lower_piece_1 == 'chrome') {
              // the newer internet explorer uses safari/webkit based agent names, assign chrome conditionally.
              if (is_null($result['name_machine']) || $result['name_machine'] == 'safari') {
                $result['name_machine'] = 'chrome';
                $result['name_human'] = 'Google Chrome';

                if (is_null($result['version_major'])) {
                  $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
                }
              }
            }
            elseif ($lower_piece_1 == 'chromium') {
              $result['name_machine'] = 'chrome';
              $result['name_human'] = 'Google Chrome';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }
            }
            elseif ($lower_piece_1 == 'epiphany') {
              $result['name_machine'] = 'epiphany';
              $result['name_human'] = 'Ephiphany';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }

              if (is_null($result['engine_name_machine'])) {
                $result['engine_name_machine'] = 'gecko';
                $result['engine_name_human'] = 'Gecko';
              }
            }
            elseif ($lower_piece_1 == 'konqueror') {
              $result['name_machine'] = 'konqueror';
              $result['name_human'] = 'Konqueror';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }

              if (is_null($result['engine_name_machine'])) {
                $result['engine_name_machine'] = 'gecko';
                $result['engine_name_human'] = 'Gecko';
              }
            }
            elseif ($lower_piece_1 == 'khtml') {
              $result['name_machine'] = 'konqueror';
              $result['name_human'] = 'Konqueror';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }
            }
            elseif ($lower_piece_1 == 'opr') {
              $result['name_machine'] = 'opera';
              $result['name_human'] = 'Opera';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }

              if (is_null($result['engine_name_machine'])) {
                $result['engine_name_machine'] = 'presto';
                $result['engine_name_human'] = 'Presto';
              }
            }
            elseif ($lower_piece_1 == 'edge') {
              $result['name_machine'] = 'ie';
              $result['name_human'] = 'Internet Explorer';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }

              $result['is_ie_edge'] = TRUE;
            }
            elseif ($lower_piece_1 == 'midori') {
              $result['name_machine'] = 'midori';
              $result['name_human'] = 'Midori';

              if (is_null($result['version_major'])) {
                $result['version_major'] = (int) preg_replace('/\..*$/iu', '', $lower_piece_2);
              }
            }

            unset($lower_piece_1);
            unset($lower_piece_2);
          }
          elseif (isset($pieces[0])) {
            $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[0]));

            if ($lower_piece_1 == 'opera') {
              $result['name_machine'] = 'opera';
              $result['name_human'] = 'Opera';

              if (is_null($result['engine_name_machine'])) {
                $result['engine_name_machine'] = 'presto';
                $result['engine_name_human'] = 'Presto';
              }
            }
            elseif ($lower_piece_1 == '(khtml,') {
              // khtml is used in a lot of places that is not safari, so use only when necessary.
              if (is_null($result['engine_name_machine']) || $result['name_machine'] == 'epiphany' || $result['name_machine'] == 'konqueror') {
                $result['engine_name_machine'] = 'webkit';
                $result['engine_name_human'] = 'Webkit';
              }

              if (is_null($result['name_machine'])) {
                $result['name_machine'] = 'safari';
                $result['name_human'] = 'Safari';
              }
            }
            unset($lower_piece_1);
          }
        }
        unset($pieces);
      }
      unset($agent_pieces);
    }
    unset($agent_matches);


    // attempt to determine internet explorer versions if not already found.
    if ($result['engine_name_machine'] == 'trident' && (is_null($result['name_machine']) || ($result['name_machine'] == 'ie' && is_null($result['version_major'])))) {
      $result['name_machine'] = 'ie';
      $result['human_name'] = 'Internet Explorer';

      if (isset($result['is_ie_edge'])) {
        $result['version_major'] = 12;
      }
      elseif ($result['version_engine'] == 7) {
        $result['version_major'] = 11;
      }
      elseif ($result['version_engine'] == 6) {
        $result['version_major'] = 10;
      }
      elseif ($result['version_engine'] == 5) {
        $result['version_major'] = 9;
      }
      elseif ($result['version_engine'] == 4) {
        $result['version_major'] = 8;
      }
    }


    // detect internet explorers compatibility mode (for old versions) where possible to allow clients to better handle.
    if ($result['name_machine'] == 'ie') {
      if ($result['version_major'] <= 8) {
        if ($result['version_major'] == 7) {
          if ($result['engine_name_machine'] == 'trident') {
            $result['is_ie_compatibility'] = TRUE;
          }
        }
      }

      // alter the (faked) agent version to properly reflect the current browser.
      if ($result['is_ie_compatibility'] && isset($result['version_engine'])) {
        if (isset($result['is_ie_edge'])) {
          $result['version_major'] = 12;
        }
        elseif ($result['version_engine'] == 7) {
          $result['version_major'] = 11;
        }
        elseif ($result['version_engine'] == 6) {
          $result['version_major'] = 10;
        }
        elseif ($result['version_engine'] == 5) {
          $result['version_major'] = 9;
        }
        elseif ($result['version_engine'] == 4) {
          $result['version_major'] = 8;
        }
        elseif (preg_match("/; EIE10;/iu", $agent) > 0) {
          $result['version_major'] = 10;
        }
      }

      // added later on to allow for compatibility mode tests to be properly processed.
      $result['engine_name_machine'] = 'trident';
      $result['engine_name_human'] = 'Trident';
    }


    // if the agent wasn't identified, check to see if this is a bot or a known command line tool.
    if (is_null($result['engine_name_machine'])) {
      $agent_matches = array();
      preg_match('/^([^(]+)/iu', $agent, $agent_matches);

      if (isset($agent_matches[0])) {
        $pieces = mb_split('/', $agent_matches[0]);
        $total_pieces = count($pieces);
        if ($total_pieces == 2) {
          $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[0]));
          $lower_piece_2 = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[1]));

          if ($lower_piece_1 == 'curl') {
            $result['engine_name_machine'] = 'curl';
            $result['engine_name_human'] = 'Curl';

            if (preg_match('/^(\d|\.)+$/iu', $lower_piece_2) > 0) {
              $result['version_engine'] = $lower_piece_2;
            }
            else {
              $result['version_engine'] = 0;
            }

            $result['name_machine'] = 'curl';
            $result['human_name'] = 'Curl';
            $result['version_major'] = (int) $lower_piece_2;
          }
          elseif ($lower_piece_1 == 'wget') {
            $result['engine_name_machine'] = 'wget';
            $result['engine_name_human'] = 'WGet';

            if (preg_match('/^(\d|\.)+$/iu', $lower_piece_2) > 0) {
              $result['version_engine'] = $lower_piece_2;
            }
            else {
              $result['version_engine'] = 0;
            }

            $result['name_machine'] = 'wget';
            $result['human_name'] = 'WGet';
            $result['version_major'] = (int) $lower_piece_2;
          }
          elseif ($lower_piece_1 == 'elinks') {
            $result['engine_name_machine'] = 'elinks';
            $result['engine_name_human'] = 'Elimks';

            if (preg_match('/^(\d|\.)+$/iu', $lower_piece_2) > 0) {
              $result['version_engine'] = $lower_piece_2;
            }
            else {
              $result['version_engine'] = 0;
            }

            $result['name_machine'] = 'elinks';
            $result['human_name'] = 'Elimks';
            $result['version_major'] = (int) $lower_piece_2;
          }
          elseif ($lower_piece_1 == 'lynx') {
            $result['engine_name_machine'] = 'lynx';
            $result['engine_name_human'] = 'Lynx';

            if (preg_match('/^(\d|\.)+$/iu', $lower_piece_2) > 0) {
              $result['version_engine'] = $lower_piece_2;
            }
            else {
              $result['version_engine'] = 0;
            }

            $result['name_machine'] = 'lynx';
            $result['human_name'] = 'Lynx';
            $result['version_major'] = (int) $lower_piece_2;
          }

          unset($lower_piece_1);
          unset($lower_piece_2);
        }
        elseif ($total_pieces == 1) {
          $lower_piece = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($pieces[0]));

          if ($lower_piece == 'links') {
            $result['engine_name_machine'] = 'links';
            $result['engine_name_human'] = 'Links';
            $result['name_machine'] = 'links';
            $result['human_name'] = 'Links';
          }

          unset($lower_piece);
        }
        unset($pieces);
        unset($total_pieces);
      }
      unset($agent_matches);
    }

    return $result;
  }

  /**
   * Load and process the HTTP request parameter: checksum_content.
   *
   * The following format is expected:
   * - 1*(atext):1*(atext):1*(atext)
   *
   * The header should have either 'partial:checksum_type:checksum_value' or 'complete:checksum_type:checksum_value'.
   * The checksum_value is stored in base64 and will be decoded by this function.
   *
   * This is not part of any official standard, but is added as an additional feature of this project.
   * Unlike content_md5, this does not require additional headers.
   *
   * @param string $checksum
   *   The checksum string to validate and decode.
   *
   * @return array
   *   The processed information:
   *   - 'what': A specific way in which to interpret the checksum, currently either: 'partial' or 'full'.
   *   - 'type': The type of the checksum, such as 'sha256'.
   *   - 'checksum': The checksum value after it has been base64 decoded.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   */
  private function p_parse_checksum($checksum) {
    $result = array(
      'what' => NULL,
      'type' => NULL,
      'checksum' => NULL,
      'invalid' => FALSE,
    );

    $fixed_checksum = mb_strtolower(preg_replace('/(^\s+)|(\s+$)/us', '', $checksum));
    if (empty($fixed_checksum)) {
      $result['invalid'] = TRUE;
      unset($fixed_checksum);
      return $result;
    }

    $parts = mb_split(':', $fixed_checksum);
    unset($fixed_checksum);
    if (count($parts) != 3) {
      $result['invalid'] = TRUE;
      unset($parts);
      return $result;
    }


    // process the partial/complete option.
    $text = $this->pr_rfc_string_prepare($parts[0]);
    if ($text['invalid']) {
      $result['invalid'] = TRUE;
      unset($text);
      unset($parts);
      return $result;
    }

    $parsed = $this->pr_rfc_string_is_atext($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      $result['invalid'] = TRUE;
      unset($parsed);
      unset($parts);
      return $result;
    }

    if ($parsed['text'] == 'partial') {
      $result['what'] = self::CHECKSUM_WHAT_FULL;
    }
    elseif ($parsed['text'] == 'complete') {
      $result['what'] = self::CHECKSUM_WHAT_COMPLETE;
    }
    else {
      $result['invalid'] = TRUE;
      unset($parsed);
      unset($parts);
      return $result;
    }
    unset($parsed);


    // process the checksum option.
    $text = $this->pr_rfc_string_prepare($parts[1]);
    if ($text['invalid']) {
      $result['invalid'] = TRUE;
      unset($text);
      unset($parts);
      return $result;
    }

    $parsed = $this->pr_rfc_string_is_atext($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      $result['invalid'] = TRUE;
      unset($parsed);
      unset($parts);
      return $result;
    }

    if ($parsed['text'] == 'md2') {
      $result['type'] = self::CHECKSUM_MD2;
    }
    elseif ($parsed['text'] == 'md4') {
      $result['type'] = self::CHECKSUM_MD4;
    }
    elseif ($parsed['text'] == 'md5') {
      $result['type'] = self::CHECKSUM_MD5;
    }
    elseif ($parsed['text'] == 'sha1') {
      $result['type'] = self::CHECKSUM_SHA1;
    }
    elseif ($parsed['text'] == 'sha224') {
      $result['type'] = self::CHECKSUM_SHA224;
    }
    elseif ($parsed['text'] == 'sha256') {
      $result['type'] = self::CHECKSUM_SHA256;
    }
    elseif ($parsed['text'] == 'sha384') {
      $result['type'] = self::CHECKSUM_SHA384;
    }
    elseif ($parsed['text'] == 'sha512') {
      $result['type'] = self::CHECKSUM_SHA512;
    }
    elseif ($parsed['text'] == 'crc32') {
      $result['type'] = self::CHECKSUM_CRC32;
    }
    else {
      $result['invalid'] = TRUE;
      unset($parsed);
      unset($parts);
      return $result;
    }
    unset($parsed);


    // process the checksum value.
    $text = $this->pr_rfc_string_prepare($parts[1]);
    unset($parts);
    if ($text['invalid']) {
      $result['invalid'] = TRUE;
      unset($text);
      return $result;
    }

    $parsed = $this->pr_rfc_string_is_atext($text['ordinals'], $text['characters']);
    unset($text);

    $result['checksum'] = base64_decode($parsed['text']);
    unset($parsed);
    if ($result['checksum'] === FALSE || empty($result['checksum'])) {
      $result['invalid'] = TRUE;
      return $result;
    }

    return $result;
  }

  /**
   * Load and process the HTTP request parameter: checksum_headers.
   *
   * The following format is expected:
   * - 1*(atext)*(*(wsp) "," *(wsp)1*(atext))
   *
   * The headers is a comma separated list of http headers present at the time the header checksum was generated.
   * This is necessary because anything en-route may add or alter headers and the checksum needs to still validate for the checksum provided.
   * This also gives the client or server and idea on what was added and what was not.
   *
   * @param string $checksum_headers
   *   The checksum headers string to validate and decode.
   *
   * @return array
   *   The processed information:
   *   - 'headers': An array containing (all checksum header names are forced to become lower case):
   *     - 'known': An array of checksum header strings whose keys are the header ids.
   *     - 'unknown': An array of checksum header strings whose keys are numerically sorted.
   *   - 'invalid': a boolean representing whether or not this string is valid or if an error occurred.
   */
  private function p_parse_checksum_headers($checksum_headers) {
    $result = array(
      'headers' => array(
        'known' => array(),
        'unknown' => array(),
      ),
      'invalid' => FALSE,
    );

    $fixed_checksum = mb_strtolower(preg_replace("/(^( |\t)+)|(( |\t)+$)/us", '', $checksum_headers));
    if (empty($fixed_checksum)) {
      $result['invalid'] = TRUE;
      unset($fixed_checksum);
      return $result;
    }

    $parts = mb_split(',', $fixed_checksum);
    unset($fixed_checksum);

    if (empty($parts)) {
      // this is not an error, it simply means that no headers are associated with the header checksum (effectively making the header checksum pointless).
      return $result;
    }

    $mapping_headers = $this->p_get_header_request_mapping();
    foreach ($parts as $part) {
      // strip out leading or trailing whitespace.
      $sanitized = preg_replace("/(^( |\t)+)|(( |\t)+$)/us", '', $part);

      $text = $this->pr_rfc_string_prepare($sanitized);
      unset($sanitized);
      if ($text['invalid']) {
        $result['invalid'] = TRUE;
        unset($text);
        unset($parts);
        unset($part);
        unset($mapping_headers);
        return $result;
      }

      $parsed = $this->pr_rfc_string_is_atext($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed['invalid']) {
        $result['invalid'] = TRUE;
        unset($parsed);
        unset($parts);
        unset($part);
        unset($mapping_headers);
        return $result;
      }

      if (array_key_exists($parsed['text'], $mapping_headers)) {
        $result['headers']['known'][$mapping_headers[$parsed['text']]] = $parsed['text'];
      }
      else {
        $result['headers']['unknown'][] = $parsed['text'];
      }
      unset($parsed);
    }
    unset($parts);
    unset($part);
    unset($mapping_headers);

    return $result;
  }

  /**
   * Simplify handling of the multibyte string length processing.
   *
   * @param string $string
   *   The string to get the length of.
   *
   * @return int
   *   The string length.
   *   0 is returned on any error.
   */
  private function p_length_string($string) {
    $length = c_base_utf8::s_length_string($string);
    if ($length instanceof c_base_return_false) {
      unset($length);
      return 0;
    }

    return $length->get_value_exact();
  }

  /**
   * Obtain all headers whether or not apache is used.
   *
   * One of the problems is that PHP will clobber the request headers in the following ways:
   * - make them all uppercase.
   * - Replace '-' with '_'.
   *
   * The uppercase situation is not a problem, they just need to be all lowercased.
   * The '-' to '_' is a problem because the '_' may be the intended functionality.
   *
   * There are also some values stored in $_SERVER that may be useful, so additional fields may be created and used.
   * - These will be prefixed with 'environment-'.
   *
   * @fixme: do something about converting '_' to '-'.
   */
  private function p_get_all_headers() {
    $this->headers = array();

    // this works with apache.
    if (function_exists('getallheaders')) {
      $all_headers = getallheaders();

      foreach ($all_headers as $key => $value) {
        // break the header name so that it is consistent until such time that PHP stops clobbering the header names.
        $broken = preg_replace('/-/u', '_', $key);
        $this->headers[mb_strtolower($broken)] = $value;
        unset($broken);
      }
      unset($broken);
      unset($key);
      unset($value);
    }
    else {
      // non-apache, or calling php from command line.
      if (isset($_SERVER) && is_array($_SERVER) && !empty($_SERVER)) {
        foreach ($_SERVER as $key => $value) {
          $part = mb_strtolower(mb_substr($key, 0, 5));

          if ($part != 'http_') {
            continue;
          }

          $part = mb_strtolower(mb_substr($key, 5));
          $this->headers[$part] = $value;
        }
        unset($part);
        unset($key);
        unset($value);
      }
    }

    if (isset($_SERVER) && is_array($_SERVER) && !empty($_SERVER)) {
      // find and process potentially useful additional environment variables.
      if (array_key_exists('REQUEST_TIME_FLOAT', $_SERVER)) {
        $this->request_time = $_SERVER['REQUEST_TIME_FLOAT'];
      }
      elseif (array_key_exists('REQUEST_TIME', $_SERVER)) {
        $this->request_time = $_SERVER['REQUEST_TIME'];
      }
    }

    if (is_null($this->request_time)) {
      $this->request_time = microtime(TRUE);
    }
  }

  /**
   * Return an array for mapping HTTP request header strings to header ids.
   *
   * @return array
   *   An array for mapping HTTP request header strings to header ids.
   */
  private function p_get_header_request_mapping() {
    return array(
      'accept' => self::REQUEST_ACCEPT,
      'accept-charset' => self::REQUEST_ACCEPT_CHARSET,
      'accept-encoding' => self::REQUEST_ACCEPT_ENCODING,
      'accept-language' => self::REQUEST_ACCEPT_LANGUAGE,
      'accept-datetime' => self::REQUEST_ACCEPT_DATETIME,
      'access-control-request-method' => self::REQUEST_ACCESS_CONTROL_REQUEST_METHOD,
      'access-control-request-headers' => self::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS,
      'authorization' => self::REQUEST_AUTHORIZATION,
      'cache-control' => self::REQUEST_CACHE_CONTROL,
      'connection' => self::REQUEST_CONNECTION,
      'cookie' => self::REQUEST_COOKIE,
      'content-length' => self::REQUEST_CONTENT_LENGTH,
      'content-type' => self::REQUEST_CONTENT_TYPE,
      'date' => self::REQUEST_DATE,
      'expect' => self::REQUEST_EXPECT,
      'from' => self::REQUEST_FROM,
      'host' => self::REQUEST_HOST,
      'if-match' => self::REQUEST_IF_MATCH,
      'if-modified-since' => self::REQUEST_IF_MODIFIED_SINCE,
      'if-none-match' => self::REQUEST_IF_NONE_MATCH,
      'if-range' => self::REQUEST_IF_RANGE,
      'if-unmodified-since' => self::REQUEST_IF_UNMODIFIED_SINCE,
      'max-forwards' => self::REQUEST_MAX_FORWARDS,
      'origin' => self::REQUEST_ORIGIN,
      'pragma' => self::REQUEST_PRAGMA,
      'proxy-authorization' => self::REQUEST_PROXY_AUTHORIZATION,
      'request-range' => self::REQUEST_RANGE,
      'referer' => self::REQUEST_REFERER,
      'te' => self::REQUEST_TE,
      'user-agent' => self::REQUEST_USER_AGENT,
      'upgrade' => self::REQUEST_UPGRADE,
      'via' => self::REQUEST_VIA,
      'warning' => self::REQUEST_WARNING,
      'x-requested-with' => self::REQUEST_X_REQUESTED_WITH,
      'x-forwarded-for' => self::REQUEST_X_FORWARDED_FOR,
      'x-forwarded-host' => self::REQUEST_X_FORWARDED_HOST,
      'x-forwarded-proto' => self::REQUEST_X_FORWARDED_PROTO,
      'checksum_header' => self::REQUEST_CHECKSUM_HEADER,
      'checksum_headers' => self::REQUEST_CHECKSUM_HEADERS,
      'checksum_content' => self::REQUEST_CHECKSUM_CONTENT,
    );
  }

  /**
   * Return an array for mapping HTTP response header ids to header strings.
   *
   * Note: self::RESPONSE_PROTOCOL is not provided here because it is included in self::RESPONSE_STATUS.
   *
   * @param bool $case_first
   *   (optional) When TRUE, the first character of each word will be capitalized.
   *   The internal code uses lower case for processing, so in those cases, this should be set to FALSE.
   *   Many clients do this with HTTP headers, so to reduce the ability to fingerprint this project, one should set this to TRUE for HTTP responses.
   *
   * @return array
   *   An array for mapping HTTP response header strings to header ids.
   */
  private function p_get_header_response_mapping($case_first = FALSE) {
    if ($case_first) {
      return array(
        self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN => 'Access-Control-Allow-Origin',
        self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS => 'Access-Control-Allow-Credentials',
        self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS => 'Access-Control-Expose-Headers',
        self::RESPONSE_ACCESS_CONTROL_MAX_AGE => 'Access-Control-Max-Age',
        self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS => 'Access-Control-Allow-Methods',
        self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS => 'Access-Control-Allow-Headers',
        self::RESPONSE_ACCEPT_PATCH => 'Accept-Patch',
        self::RESPONSE_ACCEPT_RANGES => 'Accept-Ranges',
        self::RESPONSE_AGE => 'Age',
        self::RESPONSE_ALLOW => 'Allow',
        self::RESPONSE_CACHE_CONTROL => 'Cache-Control',
        self::RESPONSE_CONNECTION => 'Connection',
        self::RESPONSE_CONTENT_DISPOSITION => 'Content-Disposition',
        self::RESPONSE_CONTENT_ENCODING => 'Content-Encoding',
        self::RESPONSE_CONTENT_LANGUAGE => 'Content-Language',
        self::RESPONSE_CONTENT_LENGTH => 'Content-Length',
        self::RESPONSE_CONTENT_LOCATION => 'Content-Location',
        self::RESPONSE_CONTENT_RANGE => 'Content-Range',
        self::RESPONSE_CONTENT_TYPE => 'Content-Type',
        self::RESPONSE_DATE => 'Date',
        self::RESPONSE_DATE_ACTUAL => 'Date_Actual',
        self::RESPONSE_ETAG => 'Etag',
        self::RESPONSE_EXPIRES => 'Expires',
        self::RESPONSE_LAST_MODIFIED => 'Last-Modified',
        self::RESPONSE_LINK => 'Link',
        self::RESPONSE_LOCATION => 'Location',
        self::RESPONSE_PRAGMA => 'Pragma',
        self::RESPONSE_PROXY_AUTHENTICATE => 'Proxy-Authenticate',
        self::RESPONSE_PUBLIC_KEY_PINS => 'Public-Key-Pins',
        self::RESPONSE_REFRESH => 'Refresh',
        self::RESPONSE_RETRY_AFTER => 'Retry-After',
        self::RESPONSE_SERVER => 'Server',
        self::RESPONSE_SET_COOKIE => 'Set-Cookie',
        self::RESPONSE_STATUS => 'Status',
        self::RESPONSE_STRICT_TRANSPORT_SECURITY => 'Strict-Transport-Security',
        self::RESPONSE_TRAILER => 'Trailer',
        self::RESPONSE_TRANSFER_ENCODING => 'Transfer-Encoding',
        self::RESPONSE_UPGRADE => 'Upgrade',
        self::RESPONSE_VARY => 'Vary',
        self::RESPONSE_WARNING => 'Warning',
        self::RESPONSE_WWW_AUTHENTICATE => 'Www-Authenticate',
        self::RESPONSE_X_CONTENT_SECURITY_POLICY => 'X-Content-Security-Policy',
        self::RESPONSE_X_CONTENT_TYPE_OPTIONS => 'X-Content-Type-Options',
        self::RESPONSE_X_UA_COMPATIBLE => 'X-UA-Compatible',
        self::RESPONSE_CHECKSUM_HEADER => 'Checksum_Header',
        self::RESPONSE_CHECKSUM_HEADERS => 'Checksum_Headers',
        self::RESPONSE_CHECKSUM_CONTENT => 'Checksum_Content',
        self::RESPONSE_CONTENT_REVISION => 'Content_Revision',
      );
    }

    return array(
      self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN => 'access-control-allow-origin',
      self::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS => 'access-control-allow-credentials',
      self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS => 'access-control-expose-headers',
      self::RESPONSE_ACCESS_CONTROL_MAX_AGE => 'access-control-max-age',
      self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS => 'access-control-allow-methods',
      self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS => 'access-control-allow-headers',
      self::RESPONSE_ACCEPT_PATCH => 'accept-patch',
      self::RESPONSE_ACCEPT_RANGES => 'accept-ranges',
      self::RESPONSE_AGE => 'age',
      self::RESPONSE_ALLOW => 'allow',
      self::RESPONSE_CACHE_CONTROL => 'cache-control',
      self::RESPONSE_CONNECTION => 'connection',
      self::RESPONSE_CONTENT_DISPOSITION => 'content-disposition',
      self::RESPONSE_CONTENT_ENCODING => 'content-encoding',
      self::RESPONSE_CONTENT_LANGUAGE => 'content-language',
      self::RESPONSE_CONTENT_LENGTH => 'content-length',
      self::RESPONSE_CONTENT_LOCATION => 'content-location',
      self::RESPONSE_CONTENT_RANGE => 'content-range',
      self::RESPONSE_CONTENT_TYPE => 'content-type',
      self::RESPONSE_DATE => 'date',
      self::RESPONSE_DATE_ACTUAL => 'date_actual',
      self::RESPONSE_ETAG => 'etag',
      self::RESPONSE_EXPIRES => 'expires',
      self::RESPONSE_LAST_MODIFIED => 'last-modified',
      self::RESPONSE_LINK => 'link',
      self::RESPONSE_LOCATION => 'location',
      self::RESPONSE_PRAGMA => 'pragma',
      self::RESPONSE_PROXY_AUTHENTICATE => 'proxy-authenticate',
      self::RESPONSE_PUBLIC_KEY_PINS => 'public-key-pins',
      self::RESPONSE_REFRESH => 'refresh',
      self::RESPONSE_RETRY_AFTER => 'retry-after',
      self::RESPONSE_SERVER => 'server',
      self::RESPONSE_SET_COOKIE => 'set-cookie',
      self::RESPONSE_STATUS => 'status',
      self::RESPONSE_STRICT_TRANSPORT_SECURITY => 'strict-transport-security',
      self::RESPONSE_TRAILER => 'trailer',
      self::RESPONSE_TRANSFER_ENCODING => 'transfer-encoding',
      self::RESPONSE_UPGRADE => 'upgrade',
      self::RESPONSE_VARY => 'vary',
      self::RESPONSE_WARNING => 'warning',
      self::RESPONSE_WWW_AUTHENTICATE => 'www-authenticate',
      self::RESPONSE_X_CONTENT_SECURITY_POLICY => 'x-content-security-policy',
      self::RESPONSE_X_CONTENT_TYPE_OPTIONS => 'x-content-type-options',
      self::RESPONSE_X_UA_COMPATIBLE => 'x-ua-compatible',
      self::RESPONSE_CHECKSUM_HEADER => 'checksum_header',
      self::RESPONSE_CHECKSUM_HEADERS => 'checksum_headers',
      self::RESPONSE_CHECKSUM_CONTENT => 'checksum_content',
      self::RESPONSE_CONTENT_REVISION => 'content_revision',
    );
  }

  /**
   * Return an array for mapping HTTP method strings to numeric ids.
   *
   * @return array
   *   An array for mapping HTTP response header strings to header ids.
   */
  private function p_get_http_method_mapping() {
    return array(
      'get' => self::HTTP_METHOD_GET,
      'head' => self::HTTP_METHOD_HEAD,
      'post' => self::HTTP_METHOD_POST,
      'put' => self::HTTP_METHOD_PUT,
      'delete' => self::HTTP_METHOD_DELETE,
      'trace' => self::HTTP_METHOD_TRACE,
      'options' => self::HTTP_METHOD_OPTIONS,
      'connect' => self::HTTP_METHOD_CONNECT,
      'patch' => self::HTTP_METHOD_PATCH,
      'track' => self::HTTP_METHOD_TRACK,
    );
  }

  /**
   * Sanitize a token field, such as a header name, and make sure it is valid.
   *
   * A valid header name has the following structure:
   * - 1*(tchar)
   *
   * @param string $token_name
   *   The string to sanitize as a token name.
   *
   * @return string|bool
   *   A sanitized string is return on success.
   *   FALSE is returned on error or if the header name is invalid.
   */
  private function p_prepare_token($token_name) {
    $trimmed = preg_replace('/(^\s+)|(\s+$)/us', '', mb_strtolower($token_name));
    if ($trimmed === FALSE) {
      unset($trimmed);
      return FALSE;
    }

    $text = $this->pr_rfc_string_prepare($trimmed);
    unset($trimmed);

    if ($text['invalid']) {
      unset($text);
      return FALSE;
    }

    $sanitized = $this->pr_rfc_string_is_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($sanitized['invalid']) {
      unset($sanitized);
      return FALSE;
    }

    return $sanitized['text'];
  }

  /**
   * Prepare HTTP response header: access-control-allow-origin.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  private function p_prepare_header_response_access_control_allow_origin(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: access-control-expose-headers.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  private function p_prepare_header_response_access_control_expose_headers(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: access-control-allow-methods.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  private function p_prepare_header_response_access_control_allow_methods(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: access-control-allow-headers.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   */
  private function p_prepare_header_response_access_control_allow_headers(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: accept-patch.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: self::pr_rfc_string_is_media_type()
   * @see: https://tools.ietf.org/html/rfc5789#section-3.1
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   */
  private function p_prepare_header_response_accept_patch(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ACCEPT_PATCH, $this->response)) {
      return;
    }

    $header_output[self::RESPONSE_ACCEPT_PATCH] = 'Accept-Patch: ' . array_shift($this->response[self::RESPONSE_ACCEPT_PATCH]);

    if (!empty($this->response[self::RESPONSE_ACCEPT_PATCH])) {
      foreach ($this->response[self::RESPONSE_ACCEPT_PATCH] as $media_type) {
        $header_output[self::RESPONSE_ACCEPT_PATCH] .= ', ' . $media_type;
      }
      unset($media_type);
    }
  }

  /**
   * Prepare HTTP response header: allow.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.1
   */
  private function p_prepare_header_response_allow(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ALLOW, $this->response)) {
      return;
    }

    if (array_key_exists(self::HTTP_METHOD_NONE, $this->response[self::RESPONSE_ALLOW])) {
      // An empty Allow field value indicates that the resource allows no methods, which might occur in a 405 response if the resource has been temporarily disabled by configuration.
      $header_output[self::RESPONSE_ALLOW] = 'Allow: ';
      return;
    }

    $mapping = array_flip($this->p_get_http_method_mapping());

    $header_output[self::RESPONSE_ALLOW] = 'Allow: ';

    $allow = array_shift($this->response[self::RESPONSE_ALLOW]);
    $header_output[self::RESPONSE_ALLOW] .= $mapping[$allow];

    if (!empty($this->response[self::RESPONSE_ALLOW])) {
      foreach ($this->response[self::RESPONSE_ALLOW] as $allow) {
        $header_output[self::RESPONSE_ALLOW] .= ', ' . $mapping[$allow];
      }
    }
    unset($allow);
    unset($mapping);
  }

  /**
   * Prepare HTTP response header: cache-control.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2.3
   */
  private function p_prepare_header_response_cache_control(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CACHE_CONTROL, $this->response) || empty($this->response[self::RESPONSE_CACHE_CONTROL])) {
      return;
    }

    $header_output[self::RESPONSE_CACHE_CONTROL] = NULL;
    foreach ($this->response[self::RESPONSE_CACHE_CONTROL] as $cache_control_directive => $cache_control_value) {
      if (is_null($header_output[self::RESPONSE_CACHE_CONTROL])) {
        $header_output[self::RESPONSE_CACHE_CONTROL] = 'Cache-Control: ';
      }
      else {
        $header_output[self::RESPONSE_CACHE_CONTROL] .= ', ';
      }

      switch ($cache_control_directive) {
        case self::CACHE_CONTROL_NO_CACHE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'no-cache';
          break;

        case self::CACHE_CONTROL_NO_STORE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'no-store';
          break;

        case self::CACHE_CONTROL_NO_TRANSFORM:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'no-transform';
          break;

        case self::CACHE_CONTROL_MAX_AGE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'max-age';
          break;

        case self::CACHE_CONTROL_MAX_AGE_S:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 's-max-age';
          break;

        case self::CACHE_CONTROL_MAX_STALE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'max-statle';
          break;

        case self::CACHE_CONTROL_MIN_FRESH:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'min-fresh';
          break;

        case self::CACHE_CONTROL_ONLY_IF_CACHED:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'only-if-cached';
          break;

        case self::CACHE_CONTROL_PUBLIC:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'public';
          break;

        case self::CACHE_CONTROL_PRIVATE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'private';
          break;

        case self::CACHE_CONTROL_MUST_REVALIDATE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'must-revalidate';
          break;

        case self::CACHE_CONTROL_PROXY_REVALIDATE:
          $header_output[self::RESPONSE_CACHE_CONTROL] .= 'proxy-revalidate';
          break;

        default:
          break;
      }

      if (!is_null($cache_control_value)) {
        $header_output[self::RESPONSE_CACHE_CONTROL] .= '=' . $cache_control_value;
      }
    }
    unset($cache_control_directive);
    unset($cache_control_value);
  }

  /**
   * Prepare HTTP response header: connection.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-6.1
   */
  private function p_prepare_header_response_connection(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CONNECTION, $this->response)) {
      return;
    }

    $header_output[self::RESPONSE_CONNECTION] = 'Connection: ';

    $connection = array_shift($this->response[self::RESPONSE_CONNECTION]);
    $header_output[self::RESPONSE_CONNECTION] .= $connection;

    if (!empty($this->response[self::RESPONSE_CONNECTION])) {
      foreach ($this->response[self::RESPONSE_CONNECTION] as $connection) {
        $header_output[self::RESPONSE_CONNECTION] .= ', ' . $connection;
      }
    }
    unset($connection);
  }

  /**
   * Prepare HTTP response header: content-disposition.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  private function p_prepare_header_response_content_disposition(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CONTENT_DISPOSITION, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: content-encoding.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.2.2
   */
  private function p_prepare_header_response_content_encoding(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CONTENT_ENCODING, $this->response)) {
      return;
    }

    $header_output[self::RESPONSE_CONTENT_ENCODING] = 'Content-Encoding: ';

    switch ($this->response[self::RESPONSE_CONTENT_ENCODING]) {
      case self::ENCODING_CHUNKED:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'chubked';
        break;
      case self::ENCODING_COMPRESS:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'compress';
        break;
      case self::ENCODING_DEFLATE:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'deflate';
        break;
      case self::ENCODING_GZIP:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'gzip';
        break;
      case self::ENCODING_BZIP:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'bzip';
        break;
      case self::ENCODING_LZO:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'lzo';
        break;
      case self::ENCODING_XZ:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'xz';
        break;
      case self::ENCODING_EXI:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'exi';
        break;
      case self::ENCODING_IDENTITY:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'identity';
        break;
      case self::ENCODING_SDCH:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'sdch';
        break;
      case self::ENCODING_PG:
        $header_output[self::RESPONSE_CONTENT_ENCODING] .= 'pg';
        break;
      default:
        unset($header_output[self::RESPONSE_CONTENT_ENCODING]);
    }
  }

  /**
   * Prepare HTTP response header: content-language.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.3.2
   */
  private function p_prepare_header_response_content_language(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CONTENT_LANGUAGE, $this->response)) {
      return;
    }

    $language_array = $this->language_class->s_get_aliases_by_id($this->response[self::RESPONSE_CONTENT_LANGUAGE]);
    if ($language_array instanceof c_base_return_array) {
      $language_array = $language_array->get_value_exact();

      if (!empty($language_array[0])) {
        $header_output[self::RESPONSE_CONTENT_LANGUAGE] = 'Content-Language: ' . $language_array[0];
      }
    }
    unset($language_array);
  }

  /**
   * Prepare HTTP response header: content-type.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  private function p_prepare_header_response_content_type(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CONTENT_TYPE, $this->response)) {
      return;
    }

    $header_output[self::RESPONSE_CONTENT_TYPE] = 'Content-Type: ' . $this->response[self::RESPONSE_CONTENT_TYPE]['type'] . '; ';

    $encoding_string = c_base_charset::s_to_string($this->response[self::RESPONSE_CONTENT_TYPE]['charset']);
    if ($encoding_string instanceof c_base_return_string) {
      $header_output[self::RESPONSE_CONTENT_TYPE] .= $encoding_string->get_value_exact();
    }
    unset($encoding_string);
  }

  /**
   * Prepare HTTP response header: etag.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  private function p_prepare_header_response_etag(&$header_output) {
    if (!array_key_exists(self::RESPONSE_ETAG, $this->response)) {
      return;
    }

    if ($this->response[self::RESPONSE_ETAG]['weak']) {
      $header_output[self::RESPONSE_ETAG] = 'Etag: W/"' . $this->response[self::RESPONSE_ETAG]['tag'] . '"';
    }
    else {
      $header_output[self::RESPONSE_ETAG] = 'Etag: "' . $this->response[self::RESPONSE_ETAG]['tag'] . '"';
    }
  }

  /**
   * Prepare HTTP response header: link.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc5988#section-5
   * @see: https://tools.ietf.org/html/rfc3986
   */
  private function p_prepare_header_response_link(&$header_output) {
    if (!array_key_exists(self::RESPONSE_LINK, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: location.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  private function p_prepare_header_response_location(&$header_output) {
    if (!array_key_exists(self::RESPONSE_LOCATION, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: pragma.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  private function p_prepare_header_response_pragma(&$header_output) {
    if (!array_key_exists(self::RESPONSE_PRAGMA, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: proxy-authenticate.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.3
   */
  private function p_prepare_header_response_proxy_authenticate(&$header_output) {
    if (!array_key_exists(self::RESPONSE_PROXY_AUTHENTICATE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: public-key-pins.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7469
   */
  private function p_prepare_header_response_public_key_pins(&$header_output) {
    if (!array_key_exists(self::RESPONSE_PUBLIC_KEY_PINS, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: refresh.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Meta_refresh
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_refresh(&$header_output) {
    if (!array_key_exists(self::RESPONSE_REFRESH, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: retry-after.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.3
   */
  private function p_prepare_header_response_retry_after(&$header_output) {
    if (!array_key_exists(self::RESPONSE_RETRY_AFTER, $this->response)) {
      return;
    }

    $header_output[self::RESPONSE_RETRY_AFTER] = 'Retry-After: ';

    if ($this->response[self::RESPONSE_RETRY_AFTER]['is_seconds']) {
      $header_output[self::RESPONSE_RETRY_AFTER] .= $this->response[self::RESPONSE_RETRY_AFTER]['is_seconds'];
    }
    else {
      $timezone = date_default_timezone_get();
      date_default_timezone_set('GMT');

      $header_output[self::RESPONSE_RETRY_AFTER] .= date(self::TIMESTAMP_RFC_5322, $this->response[self::RESPONSE_RETRY_AFTER]['value']);

      date_default_timezone_set($timezone);
      unset($timezone);
    }
  }

  /**
   * Prepare HTTP response header: server.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.2
   */
  private function p_prepare_header_response_server(&$header_output) {
    if (!array_key_exists(self::RESPONSE_SERVER, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: strict-transport-security.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6797#section-6.1
   */
  private function p_prepare_header_response_strict_transport_security(&$header_output) {
    if (!array_key_exists(self::RESPONSE_STRICT_TRANSPORT_SECURITY, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: trailer.
   *
   * @todo: this appears to no longer be directly specified in the headers.
   *        There is a 'trailer-part' mentioned along with the transfer encoding information.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.40
   * @see: https://tools.ietf.org/html/rfc7230#section-4.1.2
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  private function p_prepare_header_response_trailer(&$header_output) {
    if (!array_key_exists(self::RESPONSE_TRAILER, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: transfer-encoding.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  private function p_prepare_header_response_transfer_encoding(&$header_output) {
    if (!array_key_exists(self::RESPONSE_TRANSFER_ENCODING, $this->response)) {
      return;
    }

    // according to the standard, content-length cannot be specified when transfer-encoding is defined.
    if (array_key_exists(self::RESPONSE_CONTENT_LENGTH, $header_output)) {
      unset($header_output[self::RESPONSE_CONTENT_LENGTH]);
    }

    // @todo
    // @fixme:  transfer-encoding is now an array of values.
  }

  /**
   * Prepare HTTP response header: upgrade.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_prepare_header_response_upgrade(&$header_output) {
    if (!array_key_exists(self::RESPONSE_UPGRADE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: vary.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.4
   */
  private function p_prepare_header_response_vary(&$header_output) {
    if (!array_key_exists(self::RESPONSE_VARY, $this->response)) {
      return;
    }

    $header_output[self::RESPONSE_VARY] = 'Vary: ';

    $vary = array_shift($this->response[self::RESPONSE_VARY]);
    $header_output[self::RESPONSE_VARY] .= $vary;

    if (!empty($this->response[self::RESPONSE_VARY])) {
      foreach ($this->response[self::RESPONSE_VARY] as $vary) {
        $header_output[self::RESPONSE_VARY] .= ', ' . $vary;
      }
    }
    unset($vary);
  }

  /**
   * Prepare HTTP response header: warning.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.5
   */
  private function p_prepare_header_response_warning(&$header_output) {
    if (!array_key_exists(self::RESPONSE_WARNING, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: www-authenticate.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.1
   */
  private function p_prepare_header_response_www_authenticate(&$header_output) {
    if (!array_key_exists(self::RESPONSE_WWW_AUTHENTICATE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: x-content-security-policy
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_x_content_security_policy(&$header_output) {
    if (!array_key_exists(self::RESPONSE_X_CONTENT_SECURITY_POLICY, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: x-content-type-options
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_x_content_type_options(&$header_output) {
    if (!array_key_exists(self::RESPONSE_X_CONTENT_TYPE_OPTIONS, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: x-ua-compatible
   *
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_x_ua_compatible(&$header_output) {
    if (!array_key_exists(self::RESPONSE_X_UA_COMPATIBLE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response headers for the content checksum fields.
   *
   * This will perform a checksum against the content.
   * Be sure to perform this check before changing the content-encoding.
   *
   * This handles the following header fields:
   * - checksum_content
   *
   * @param array $header_output
   *   The header output array to make changes to.
   */
  private function p_prepare_header_response_checksum_content(&$header_output) {
    if (!array_key_exists(self::RESPONSE_CHECKSUM_CONTENT, $this->response)) {
      return;
    }

    $what = NULL;
    if ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['what'] == self::CHECKSUM_WHAT_FULL) {
      $what = 'full';
    }
    elseif ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['what'] == self::CHECKSUM_WHAT_PARTIAL) {
      $what = 'partial';
    }
    elseif ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['what'] == self::CHECKSUM_WHAT_SIGNED) {
      $what = 'signed';
    }
    elseif ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['what'] == self::CHECKSUM_WHAT_UNSIGNED) {
      $what = 'unsigned';
    }

    if (is_null($what)) {
      unset($what);
      return;
    }

    $algorithm = NULL;
    $use_hash = FALSE;
    switch ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['type']) {
      case self::CHECKSUM_MD2:
        $algorithm = 'md2';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_MD4:
        $algorithm = 'md4';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_MD5:
        $algorithm = 'md5';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_SHA1:
        $algorithm = 'sha1';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_SHA224:
        $algorithm = 'sha224';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_SHA256:
        $algorithm = 'sha256';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_SHA384:
        $algorithm = 'sha384';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_SHA512:
        $algorithm = 'sha512';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_CRC32:
        $algorithm = 'crc32';
        $use_hash = TRUE;
        break;
      case self::CHECKSUM_PG:
        $algorithm = 'pg';
        break;
    }

    // @todo: handle support for other algorithms.
    if ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['action'] == self::CHECKSUM_ACTION_AUTO) {
      if ($this->content_is_file) {
        if ($use_hash) {
          $hash = hash_init($algorithm);
          foreach ($this->content as $filename) {
            if (!file_exists($filename)) {
              unset($filename);
              unset($hash);
              unset($what);
              unset($algorithm);
              unset($use_hash);

              // @todo: report file not found or other related errors.
              return c_base_return_error::s_false();
            }

            $success = hash_update_file($hash, $filename);
            if (!$success) {
              unset($success);
              unset($filename);
              unset($hash);
              unset($what);
              unset($algorithm);
              unset($use_hash);

              // failure
              return;
            }
          }
          unset($filename);
          unset($success);

          $header_output[self::RESPONSE_CHECKSUM_CONTENT] = 'Checksum_Content: ' . $what . ':' . $algorithm . ':' . hash_final($hash, FALSE);
          unset($hash);
        }
        else {
          // @todo: handle CHECKSUM_PG in this case.
          // if ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['type'] == self::CHECKSUM_PG) {
          // }
        }
      }
      else {
        if ($use_hash) {
          $header_output[self::RESPONSE_CHECKSUM_CONTENT] = 'Checksum_Content: ' . $what . ':' . $algorithm . ':' . hash($algorithm, $this->content);
        }
        else {
          // @todo: handle CHECKSUM_PG in this case.
          // if ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['type'] == self::CHECKSUM_PG) {
          // }
        }
      }
    }
    elseif ($this->response[self::RESPONSE_CHECKSUM_CONTENT]['action'] == self::CHECKSUM_ACTION_MANUAL) {
      $header_output[self::RESPONSE_CHECKSUM_CONTENT] = 'Checksum_Content: ' . $what . ':' . $algorithm . ':' . $this->response[self::RESPONSE_CHECKSUM_CONTENT]['checksum'];
    }
    unset($use_hash);
    unset($what);
    unset($algorithm);
  }

  /**
   * Prepare HTTP response headers for both the header checksum fields.
   *
   * @todo: implement custom functions for setting algorithms, checksum, and even enabled/disabling auto-checksuming for all checksum header fields.
   *
   * This handles the following header fields:
   * - checksum_header
   * - checksum_headers
   *
   * This must be performed after all other header fields have been prepared.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   * @param string|null $status_string
   *   When not NULL, this is prepended to the start of the header checksum string before the checksum is calculated.
   *   When NULL, this value is ignored.
   */
  private function p_prepare_header_response_checksum_headers(&$header_output, $status_string) {
    if (!array_key_exists(self::RESPONSE_CHECKSUM_HEADER, $this->response) || !array_key_exists(self::RESPONSE_CHECKSUM_HEADERS, $this->response)) {
      return;
    }

    $header_output_copy = $header_output;

    if (array_key_exists('date_actual', $header_output_copy)) {
      // When date_actual is specified, the date parameter will not be processed, prevent the 'date' parameter from being used to calculate the header checksum.
      unset($header_output_copy['date']);
    }

    $header_output_keys = array_keys($header_output_copy);
    unset($header_output_copy);

    if (!empty($header_output_keys)) {
      $header_value = array_shift($header_output_keys);
      if (!empty($header_output_keys)) {
        foreach ($header_output_keys as $header_name) {
          $header_value .= ', ' . $header_name;
        }
      }
    }
    unset($header_output_keys);

    $header_output[self::RESPONSE_CHECKSUM_HEADER] = $header_value;
    unset($header_value);

    $header_output_copy = $header_output;
    unset($header_output_copy['checkum_header']);

    $header_string = '';
    if (!is_null($status_string)) {
      $header_string .= $status_string . "\n";
    }

    $header_string .= implode("\n", $header_output_copy);
    unset($header_output_copy);

    // @todo: allow caller to specifiy which hash code and which settings.
    $checkum_header = hash('sha256', $header_string);
    unset($header_string);

    // @todo: handle support for other algorithms.
    $header_output[self::RESPONSE_CHECKSUM_HEADER] = 'Checksum_Header: full:sha256:' . $checkum_header;
    unset($checkum_header);
  }

  /**
   * Prepare HTTP response headers that are simple values.
   *
   * @param array $header_output
   *   The header output array to make changes to.
   * @param string $name_lower
   *   The HTTP header name (all lowercase), such as: 'age'.
   * @param string $name
   *   The HTTP header name, such as: 'Age'.
   * @param int $code
   *   The HTTP header code, such as:  self::RESPONSE_AGE.
   */
  private function p_prepare_header_response_simple_value(&$header_output, $name_lower, $name, $code) {
    if (!array_key_exists($code, $this->response)) {
      return;
    }

    $header_output[$code] = $name . ': ' . $this->response[$code];
  }

  /**
   * Prepare HTTP response header: date
   *
   * @param array $header_output
   *   The header output array to make changes to.
   * @param string $name_lower
   *   The HTTP header name (all lowercase), such as: 'age'.
   * @param string $name
   *   The HTTP header name, such as: 'Age'.
   * @param int $code
   *   The HTTP header code, such as:  self::RESPONSE_AGE.
   */
  private function p_prepare_header_response_timestamp_value(&$header_output, $name_lower, $name, $code) {
    if (!array_key_exists($code, $this->response)) {
      return;
    }

    $timezone = date_default_timezone_get();
    date_default_timezone_set('GMT');

    $header_output[$code] = $name . ': ' . date(self::TIMESTAMP_RFC_5322, $this->response[$code]);

    date_default_timezone_set($timezone);
    unset($timezone);
  }

  /**
   * Process the accept-encoding HTTP header to determine which content-encoding to use.
   *
   * @return int
   *   The encoding to use.
   */
  private function p_determine_response_encoding() {
    if (!$this->request[self::REQUEST_ACCEPT_ENCODING]['defined'] || $this->request[self::REQUEST_ACCEPT_ENCODING]['invalid']) {
      return self::ENCODING_CHUNKED;
    }

    $encoding = self::ENCODING_CHUNKED;
    foreach ($this->request[self::REQUEST_ACCEPT_ENCODING]['data']['weight'] as $weight => $choices) {
      foreach ($choices as $key => $choice) {
        if ($key == c_base_http::ENCODING_GZIP) {
          $encoding = $key;
          break 2;
        }

        if ($key == self::ENCODING_DEFLATE) {
          $encoding = $key;
          break 2;
        }
      }
      unset($key);
      unset($choice);
    }
    unset($weight);
    unset($choices);

    return $encoding;
  }

  /**
   * Encode and store the given content string.
   *
   * Don't pass $this->content directly to this function unless you know it is not an array of filenames.
   * - For an array of filenames, load the files into memory and then pass the loaded content.
   *
   * @param string $content
   *   The content string to encode and store.
   * @param int $encoding
   *   The encoding algorithm to perform.
   * @param bool $calculate_content_length
   *   (optional) Determine how the content-length HTTP header is to be calculated:
   *   - FALSE = do not calculate the content length.
   *   - TRUE = calculate and store the content length.
   */
  private function p_encode_content($content, $encoding, $compression = NULL, $calculate_content_length = TRUE) {
    if ($encoding == self::ENCODING_GZIP) {
      if (is_null($compression) || $compression < -1) {
        $compression = -1;
      }
      elseif ($compression > 9) {
        $compression = 9;
      }

      $this->content = gzencode($this->content, $compression, FORCE_GZIP);
      $this->content_is_file = FALSE;
      $this->response[self::RESPONSE_CONTENT_ENCODING] = $encoding;

      if ($calculate_content_length) {
        $this->response[self::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }

    }
    elseif ($encoding == self::ENCODING_DEFLATE) {
      if (is_null($compression) || $compression < -1) {
        $compression = -1;
      }
      elseif ($compression > 9) {
        $compression = 9;
      }

      $this->content = gzencode($content, $compression, FORCE_DEFLATE);
      $this->content_is_file = FALSE;
      $this->response[self::RESPONSE_CONTENT_ENCODING] = $encoding;

      if ($calculate_content_length) {
        $this->response[self::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }
    }
    elseif ($encoding == self::ENCODING_BZIP) {
      if (is_null($compression) || $compression < -1) {
        $compression = 4;
      }
      elseif ($compression > 9) {
        $compression = 9;
      }

      $this->content = bzcompress($content, $compression);
      $this->content_is_file = FALSE;
      $this->response[self::RESPONSE_CONTENT_ENCODING] = $encoding;

      if ($calculate_content_length) {
        $this->response[self::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }
    }
    elseif ($encoding == self::ENCODING_LZO) {
      switch ($compression) {
        case LZO1X_1:
        case LZO1_1:
        case LZO1_99:
        case LZO1A_1:
        case LZO1A_99:
        case LZO1B_1:
        case LZO1B_2:
        case LZO1B_3:
        case LZO1B_4:
        case LZO1B_5:
        case LZO1B_6:
        case LZO1B_7:
        case LZO1B_8:
        case LZO1B_9:
        case LZO1B_99:
        case LZO1B_999:
        case LZO1C_1:
        case LZO1C_2:
        case LZO1C_3:
        case LZO1C_4:
        case LZO1C_5:
        case LZO1C_6:
        case LZO1C_7:
        case LZO1C_8:
        case LZO1C_9:
        case LZO1C_99:
        case LZO1C_999:
        case LZO1F_1:
        case LZO1F_999:
        case LZO1X_1_11:
        case LZO1X_1_12:
        case LZO1X_1_15:
        case LZO1X_999:
        case LZO1Y_1:
        case LZO1Y_999:
        case LZO1Z_999:
        case LZO2A_999:
          break;
        default:
          $compression = LZO2A_999;
          // Note: according to (http://content.gpwiki.org/index.php/LZO) LZO1X tends to be the best (in general) consider that for the default.
      }

      $this->content = lzo_compress($content, $compression);
      $this->content_is_file = FALSE;
      $this->response[self::RESPONSE_CONTENT_ENCODING] = $encoding;

      if ($calculate_content_length) {
        $this->response[self::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }
    }
    elseif ($encoding == self::ENCODING_XZ) {
      // @todo, see: https://github.com/payden/php-xz
    }
    elseif ($encoding == self::ENCODING_EXI) {
      // @todo, maybe? (cannot seem to find a php library at this time)
    }
    elseif ($encoding == self::ENCODING_IDENTITY) {
      // @todo, maybe? (cannot seem to find a php library at this time)
    }
    elseif ($encoding == self::ENCODING_SDCH) {
      // @todo, maybe? (cannot seem to find a php library at this time)
    }
    elseif ($encoding == self::ENCODING_PG) {
      // @todo, using ascii armor on entire body.
      //        should be a header field containing the public key.
    }
  }
}
