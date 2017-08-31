<?php
/**
 * @file
 * Provides a class for managing the HTTP protocol.
 */
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_charset.php');
require_once('common/base/classes/base_rfc_string.php');
require_once('common/base/classes/base_utf8.php');
require_once('common/base/classes/base_languages.php');
require_once('common/base/classes/base_http_status.php');
require_once('common/base/classes/base_cookie.php');
require_once('common/base/classes/base_mime.php');

/**
 * A generic class for managing the HTTP protocol.
 *
 * @see: https://www.iana.org/assignments/message-headers/message-headers.xhtml
 * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
 *
 * @require class base_email
 * @require class base_rfc_string
 * @require class base_utf8
 */
class c_base_http extends c_base_rfc_string {
  // standard request headers
  const REQUEST_NONE                           = 0;
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
  const REQUEST_GET                            = 17;
  const REQUEST_HOST                           = 18;
  const REQUEST_IF_MATCH                       = 19;
  const REQUEST_IF_MODIFIED_SINCE              = 20;
  const REQUEST_IF_NONE_MATCH                  = 21;
  const REQUEST_IF_RANGE                       = 22;
  const REQUEST_IF_UNMODIFIED_SINCE            = 23;
  const REQUEST_MAX_FORWARDS                   = 24;
  const REQUEST_METHOD                         = 25;
  const REQUEST_ORIGIN                         = 26;
  const REQUEST_POST                           = 27;
  const REQUEST_PRAGMA                         = 28;
  const REQUEST_PROXY_AUTHORIZATION            = 29;
  const REQUEST_RANGE                          = 30;
  const REQUEST_REFERER                        = 31;
  const REQUEST_SCRIPT_NAME                    = 32;
  const REQUEST_TE                             = 33;
  const REQUEST_UPGRADE                        = 34;
  const REQUEST_URI                            = 35;
  const REQUEST_USER_AGENT                     = 36;
  const REQUEST_VIA                            = 37;
  const REQUEST_WARNING                        = 38;
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
  // @todo: should PHP's REMOTE_ADDRESS be added and handled here?

  // standard response headers
  const RESPONSE_NONE                             = 0;
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
  const RESPONSE_CHECKSUM_HEADER           = 1001;
  const RESPONSE_CHECKSUM_HEADERS          = 1002;
  const RESPONSE_CHECKSUM_CONTENT          = 1003;
  const RESPONSE_CONTENT_REVISION          = 1004;
  const RESPONSE_CONTENT_SECURITY_POLICY   = 1005;
  const RESPONSE_DATE_ACTUAL               = 1006;
  const RESPONSE_REFRESH                   = 1007;
  const RESPONSE_X_CONTENT_TYPE_OPTIONS    = 1008;
  const RESPONSE_X_UA_COMPATIBLE           = 1009;

  // delimiters (the syntax for the accept delimiters can be confusing and misleading)
  const DELIMITER_ACCEPT_SUP   = ',';
  const DELIMITER_ACCEPT_SUB   = ';';
  const DELIMITER_ACCEPT_SUB_0 = 'q';
  const DELIMITER_ACCEPT_SUB_1 = '=';

  // cache control options
  const CACHE_CONTROL_NONE             = 0;
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
  const CHECKSUM_NONE     = 0;
  const CHECKSUM_MD2      = 1;
  const CHECKSUM_MD4      = 2;
  const CHECKSUM_MD5      = 3;
  const CHECKSUM_SHA1     = 4;
  const CHECKSUM_SHA224   = 5;
  const CHECKSUM_SHA256   = 6;
  const CHECKSUM_SHA384   = 7;
  const CHECKSUM_SHA512   = 8;
  const CHECKSUM_CRC32    = 9;
  const CHECKSUM_CRC32B   = 10;
  const CHECKSUM_PG       = 11; // such as: GPG or PGP.

  // checksum actions
  const CHECKSUM_ACTION_NONE = 0;
  const CHECKSUM_ACTION_AUTO = 1;
  const CHECKSUM_ACTION_MANUAL = 2;

  // checksum whats
  const CHECKSUM_WHAT_NONE     = 0;
  const CHECKSUM_WHAT_FULL     = 1;
  const CHECKSUM_WHAT_PARTIAL  = 2;
  const CHECKSUM_WHAT_SIGNED   = 3;
  const CHECKSUM_WHAT_UNSIGNED = 4;

  // checksum lengths
  const CHECKSUM_LENGTH_SHORTSUM = 9;

  // uri path types
  const URI_PATH_NONE = 0;
  const URI_PATH_SITE = 1; // such as: '//example.com/main/index.html'
  const URI_PATH_BASE = 2; // such as: '/main/index.html'
  const URI_PATH_THIS = 3; // such as: 'index.html'

  // uri host ip addresses
  const URI_HOST_NONE = 0;
  const URI_HOST_IPV4 = 1;
  const URI_HOST_IPV6 = 2;
  const URI_HOST_IPVX = 3;
  const URI_HOST_NAME = 4;

  // transfer encoding choices
  const ENCODING_NONE     = 0;
  const ENCODING_CHUNKED  = 1;
  const ENCODING_COMPRESS = 2;
  const ENCODING_DEFLATE  = 3;
  const ENCODING_GZIP     = 4; // Compression Options: -1 -> 9.
  const ENCODING_BZIP     = 5; // Compression Options: 1 -> 9.
  const ENCODING_LZO      = 6; // Compression Options: LZO1_99, LZO1A_99, LZO1B_999, LZO1C_999, LZO1F_999, LZO1X_999, LZO1Y_999, LZO1Z_999, LZO2A_999 (and many more).
  const ENCODING_XZ       = 7; // currently unsupported due to available libraries being defunct.
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

  // http separators
  const SEPARATOR_HEADER_NAME = ': ';
  const SEPARATOR_HEADER_LINE = "\n";

  // fallbacks/failsafes
  const FALLBACK_PROTOCOL = 'HTTP/1.1';

  protected $headers;
  protected $headers_sent;
  protected $request;
  protected $request_time;
  protected $response;

  protected $request_uri_relative;
  protected $request_uri_query;

  protected $content;
  protected $content_is_file;
  protected $buffer_enabled;

  protected $languages;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->headers      = NULL;
    $this->headers_sent = FALSE;
    $this->request      = array();
    $this->request_time = NULL;
    $this->response     = array();

    $this->request_uri_relative = NULL;
    $this->request_uri_query    = NULL;

    $this->content         = NULL;
    $this->content_is_file = NULL;
    $this->buffer_enabled  = FALSE;

    $this->languages = NULL;
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

    unset($this->request_uri_relative);
    unset($this->request_uri_query);

    unset($this->content);
    unset($this->content_is_file);
    unset($this->buffer_enabled);

    unset($this->languages);

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
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Returns a list of HTTP headers that can be used as an HTML meta tag.
   *
   * The HTML language supports HTTP headers as HTML tags.
   *
   * The relationship between HTTP headers and HTML headers is not always one to one.
   * @todo: this list will need to be reviewed once I work on the HTML meta handling code.
   *
   * @return c_base_return_array
   *   An array of HTTP headers that can be used as meta tags.
   *
   * @see: https://html.spec.whatwg.org/multipage/semantics.html#standard-metadata-names
   * @see: https://www.w3.org/TR/html5/document-metadata.html#the-meta-element
   */
  public function get_response_headers_for_meta() {
    return c_base_return_array::s_new(array(
      static::RESPONSE_CACHE_CONTROL => static::RESPONSE_CACHE_CONTROL,
      static::RESPONSE_CONTENT_ENCODING => static::RESPONSE_CONTENT_ENCODING,
      static::RESPONSE_CONTENT_LANGUAGE => static::RESPONSE_CONTENT_LANGUAGE,
      static::RESPONSE_CONTENT_SECURITY_POLICY => static::RESPONSE_CONTENT_SECURITY_POLICY,
      static::RESPONSE_CONTENT_TYPE => static::RESPONSE_CONTENT_TYPE,
      static::RESPONSE_EXPIRES => static::RESPONSE_EXPIRES,
      static::RESPONSE_LINK => static::RESPONSE_LINK,
      static::RESPONSE_PRAGMA => static::RESPONSE_PRAGMA,
      static::RESPONSE_REFRESH => static::RESPONSE_REFRESH,
    ));
  }

  /**
   * Get the HTTP request array.
   *
   * Load the entire HTTP request array or a specific request field.
   *
   * @param int|null $header_name
   *   (optional) The numeric id of the request or NULL to load all requests.
   * @param int|string|null $delta
   *   (optional) For headers that have an array of data, this represents and index position within that array.
   *   For all other headers, this does nothing.
   *
   * @return c_base_return_array|c_base_return_value|c_base_return_status
   *   The HTTP request array or an array containing the request field information.
   *   FALSE without error bit set is returned when the requested header name is undefined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_request($header_name = NULL, $delta = NULL) {
    if (!is_null($header_name) && !is_int($header_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($delta) && !is_int($delta) && !is_string($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'delta', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($header_name)) {
      return c_base_return_array::s_new($this->request);
    }

    if (!array_key_exists($header_name, $this->request)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => $header_name, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($delta)) {
      if (isset($this->request[$header_name]['data']) && is_array($this->request[$header_name]['data']) && array_key_exists($delta, $this->request[$header_name]['data'])) {
        if ($this->request[$header_name]['data'][$delta] instanceof c_base_return) {
          return $this->request[$header_name]['data'][$delta];
        }

        return c_base_return_value::s_new($this->request[$header_name]['data'][$delta]);
      }

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => $delta, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_array::s_new($this->request[$header_name]);
  }

  /**
   * Get all provided HTTP request headers exactly as they were provided.
   *
   * @return c_base_return_array
   *   This always returns an array.
   *   An array with all request headers is returned.
   *   An empty array with the error bit set is returned on error.
   */
  public function get_request_headers() {
    if (!is_array($this->headers)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{data_name}' => 'request headers', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_DEFINED);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->headers);
  }

  /**
   * Return the relative part of the request URI that is also relative to the base path.
   *
   * @param string $base_path
   *   The base_path to remove from the request uri.
   * @param, bool $with_query
   *   (optional) If TRUE, the query is appended to the string.
   *   If FALSE, the query is ommitted.
   *
   * @return c_base_return_string
   *   A string is always returned.
   *   A string with error bit set is returned on error.
   */
  public function get_request_uri_relative($base_path, $with_query = FALSE) {
    if (!is_string($base_path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'base_path', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (!is_bool($with_query)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'with_query', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (is_string($this->request_uri_relative)) {
      if ($with_query) {
        if (is_string($this->request_uri_query)) {
          return c_base_return_string::s_new($this->request_uri_relative . '?' . $this->request_uri_query);
        }
      }

      return c_base_return_string::s_new($this->request_uri_relative);
    }

    $request_uri = $this->get_request(c_base_http::REQUEST_URI)->get_value_exact();
    if (!isset($request_uri['defined']) || !$request_uri['defined']) {
      unset($request_uri);
      return c_base_return_string::s_new('');
    }

    // strip the base path from the requested uri.
    if (strlen($base_path) > 0) {
      $request_uri['data']['path'] = preg_replace('@^' . preg_quote($base_path, '@') . '@i', '', $request_uri['data']['path']);
      $request_uri['data']['path'] = preg_replace('@/$@', '', $request_uri['data']['path']);
    }

    $this->request_uri_relative = $request_uri['data']['path'];
    $this->request_uri_query = NULL;

    if (is_array($request_uri['data']['query']) && !empty($request_uri['data']['query'])) {
      $this->request_uri_query = http_build_query($request_uri['data']['query']);
    }
    unset($request_uri);

    if ($with_query && is_string($this->request_uri_query)) {
      return c_base_return_string::s_new($this->request_uri_relative . '?' . $this->request_uri_query);
    }

    return c_base_return_string::s_new($this->request_uri_relative);
  }

  /**
   * Return the query part of the request URI that is also relative to the base path.
   *
   * This is functionally similar to get_request_uri_relative() except that it returns the request query arguments (if any) instead of the request path.
   *
   * @param string $base_path
   *   The base_path to remove from the request uri.
   *
   * @return c_base_return_string
   *   A string is always returned.
   *   A string with error bit set is returned on error.
   *
   * @see: self::get_request_uri_relative()
   */
  public function get_request_uri_query($base_path) {
    if (!is_string($base_path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'base_path', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    if (is_string($this->request_uri_query)) {
      return c_base_return_string::s_new($this->request_uri_query);
    }

    $request_uri = $this->get_request(c_base_http::REQUEST_URI)->get_value_exact();
    if (!isset($request_uri['defined']) || !$request_uri['defined']) {
      unset($request_uri);
      return c_base_return_string::s_new('');
    }

    // strip the base path from the requested uri.
    if (strlen($base_path) > 0) {
      $request_uri['data']['path'] = preg_replace('@^' . preg_quote($base_path, '@') . '@i', '', $request_uri['data']['path']);
      $request_uri['data']['path'] = preg_replace('@/$@', '', $request_uri['data']['path']);
    }

    $this->request_uri_relative = $request_uri['data']['path'];
    $this->request_uri_query = NULL;

    if (is_array($request_uri['data']['query']) && !empty($request_uri['data']['query'])) {
      $this->request_uri_query = http_build_query($request_uri['data']['query']);
    }
    unset($request_uri);

    return c_base_return_string::s_new($this->request_uri_query);
  }

  /**
   * Get the HTTP response array.
   *
   * Load the entire HTTP response array or a specific response field.
   *
   * @param int|null $header_name
   *   (optional) The numeric id of the response or NULL to load all responses.
   * @param int|string|null $delta
   *   (optional) For headers that have an array of data, this represents and index position within that array.
   *   For all other headers, this does nothing.
   *
   * @return c_base_return_array|c_base_return_string|c_base_return_value|c_base_return_status
   *   The HTTP response array or string for a given field or the entire HTTP response array.
   *   FALSE without error bit set is returned when the requested header name is undefined.
   *   FALSE with error bit set is returned on error.
   */
  public function get_response($header_name = NULL, $delta = NULL) {
    if (!is_null($header_name) && !is_int($header_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($delta) && !is_int($delta) && !is_string($delta)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'delta', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($header_name)) {
      return c_base_return_array::s_new($this->response);
    }

    if (!array_key_exists($header_name, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => $header_name, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    if (is_array($this->response[$header_name])) {
      if (is_null($delta)) {
        return c_base_return_array::s_new($this->response[$header_name]);
      }

      if (isset($this->response[$header_name]) && is_array($this->response[$header_name]) && array_key_exists($delta, $this->response[$header_name])) {
        if ($this->response[$header_name][$delta] instanceof c_base_return) {
          return $this->response[$header_name][$delta];
        }

        return c_base_return_value::s_new($this->response[$header_name][$delta]);
      }

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => $delta, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    return c_base_return_string::s_new($this->response[$header_name]);
  }

  /**
   * Assign the languages class.
   *
   * The languages class provides a list of supported languages.
   *
   * @param string|i_base_languages $class_name
   *   A string name representing an object that is a sub-class of i_base_languages.
   *   Or a language class object.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_languages($class_name) {
    if (!(is_string($class_name) && is_subclass_of('i_base_languages', $class_name)) || !(is_object($class_name) && $class_name instanceof i_base_languages)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'class_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_string($class_name)) {
      $this->languages = new $class_name();
    }
    else {
      $this->languages = $class_name;
    }

    return new c_base_return_true();
  }

  /**
   * Get the language class object currently assigned to this class.
   *
   * @return i_base_languages
   *   The language class object.
   */
  public function get_languages() {
    if (is_null($this->languages)) {
      // provide a failsafe/fallback.
      $this->languages = c_base_defaults_global::s_get_languages();
    }

    return $this->languages;
  }

  /**
   * Get the HTTP request timestamp..
   *
   * @return c_base_return_float|c_base_return_status
   *   The HTTP request time.
   *   FALSE without error bit is returned when the request timestamp has not yet been loaded
   *   FALSE with error bit set is returned on error.
   */
  public function get_request_time() {
    if (is_null($this->request_time)) {
      $timestamp = c_base_defaults_global::s_get_timestamp_session();

      if ($timestamp->has_error()) {
        unset($timestamp);
        return new c_base_return_false();
      }

      $this->request_time = $timestamp->get_value_exact();
      unset($timestamp);
    }

    return c_base_return_float::s_new($this->request_time);
  }

  /**
   * Load, process, and interpret all of the supported http request headers.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function do_load_request() {
    if (!is_array($this->headers)) {
      $this->p_get_all_headers();
    }

    // force the request array to be defined.
    $this->request = array();

    $initialize_keys = array(
      static::REQUEST_ACCEPT,
      static::REQUEST_ACCEPT_CHARSET,
      static::REQUEST_ACCEPT_ENCODING,
      static::REQUEST_ACCEPT_LANGUAGE,
      static::REQUEST_ACCEPT_DATETIME,
      static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD,
      static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS,
      static::REQUEST_AUTHORIZATION,
      static::REQUEST_CACHE_CONTROL,
      static::REQUEST_CONNECTION,
      static::REQUEST_COOKIE,
      static::REQUEST_CONTENT_LENGTH,
      static::REQUEST_CONTENT_TYPE,
      static::REQUEST_DATE,
      static::REQUEST_EXPECT,
      static::REQUEST_FROM,
      static::REQUEST_HOST,
      static::REQUEST_IF_MATCH,
      static::REQUEST_IF_MODIFIED_SINCE,
      static::REQUEST_IF_NONE_MATCH,
      static::REQUEST_IF_RANGE,
      static::REQUEST_IF_UNMODIFIED_SINCE,
      static::REQUEST_MAX_FORWARDS,
      static::REQUEST_METHOD,
      static::REQUEST_ORIGIN,
      static::REQUEST_PRAGMA,
      static::REQUEST_PROXY_AUTHORIZATION,
      static::REQUEST_RANGE,
      static::REQUEST_REFERER,
      static::REQUEST_SCRIPT_NAME,
      static::REQUEST_TE,
      static::REQUEST_UPGRADE,
      static::REQUEST_URI,
      static::REQUEST_USER_AGENT,
      static::REQUEST_VIA,
      static::REQUEST_WARNING,
      static::REQUEST_X_REQUESTED_WITH,
      static::REQUEST_X_FORWARDED_FOR,
      static::REQUEST_X_FORWARDED_HOST,
      static::REQUEST_X_FORWARDED_PROTO,
      static::REQUEST_CHECKSUM_HEADER,
      static::REQUEST_CHECKSUM_HEADERS,
      static::REQUEST_CHECKSUM_CONTENT,
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
    $this->request[static::REQUEST_ACCEPT]['types'] = array();


    if (array_key_exists('accept', $this->headers)) {
      $this->p_load_request_accept();
      unset($headers['accept']);
    }

    if (array_key_exists('accept-language', $this->headers)) {
      $this->p_load_request_accept_language();
      unset($headers['accept-language']);
    }

    if (array_key_exists('accept-encoding', $this->headers)) {
      $this->p_load_request_accept_encoding();
      unset($headers['accept-encoding']);
    }

    if (array_key_exists('accept-charset', $this->headers)) {
      $this->p_load_request_accept_charset();
      unset($headers['accept-charset']);
    }

    if (array_key_exists('accept-datetime', $this->headers)) {
      $this->p_load_request_accept_datetime();
      unset($headers['accept-datetime']);
    }

    if (array_key_exists('access-control-request-method', $this->headers)) {
      $this->p_load_request_access_control_request_method();
      unset($headers['access-control-request-method']);
    }

    if (array_key_exists('access-control-request-headers', $this->headers)) {
      $this->p_load_request_access_control_request_headers();
      unset($headers['access-control-request-headers']);
    }

    if (array_key_exists('authorization', $this->headers)) {
      $this->p_load_request_authorization();
      unset($headers['authorization']);
    }

    if (array_key_exists('cache-control', $this->headers)) {
      $this->p_load_request_cache_control();
      unset($headers['cache-control']);
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

    if (array_key_exists('content-length', $this->headers)) {
      $this->p_load_request_content_length();
      unset($headers['content-length']);
    }

    if (array_key_exists('content-md5', $this->headers)) {
      $this->p_load_request_content_md5();
      unset($headers['content-md5']);
    }

    if (array_key_exists('content-checksum', $this->headers)) {
      $this->p_load_request_content_checksum();
      unset($headers['content-checksum']);
    }

    if (array_key_exists('content-type', $this->headers)) {
      $this->p_load_request_content_type();
      unset($headers['content-type']);
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

    if (array_key_exists('if-match', $this->headers)) {
      $this->p_load_request_if_match();
      unset($headers['if-match']);
    }

    if (array_key_exists('if-none-match', $this->headers)) {
      $this->p_load_request_if_none_match();
      unset($headers['if-none-match']);
    }

    if (array_key_exists('if-modified-since', $this->headers)) {
      $this->p_load_request_if_modified_since();
      unset($headers['if-modified-since']);
    }

    if (array_key_exists('if-unmodified-since', $this->headers)) {
      $this->p_load_request_if_unmodified_since();
      unset($headers['if-unmodified-since']);
    }

    if (array_key_exists('if-range', $this->headers)) {
      $this->p_load_request_if_range();
      unset($headers['if-range']);
    }

    if (array_key_exists('range', $this->headers)) {
      $this->p_load_request_range();
      unset($headers['range']);
    }

    if (array_key_exists('max-forwards', $this->headers)) {
      $this->p_load_request_max_forwards();
      unset($headers['max-forwards']);
    }

    // request method is stored in $_SERVER['REQUEST_METHOD'] and should always be defined (by PHP) for valid HTTP requests.
    if (isset($_SERVER['REQUEST_METHOD'])) {
      $this->p_load_request_method();
    }

    if (array_key_exists('origin', $this->headers)) {
      $this->p_load_request_origin();
      unset($headers['origin']);
    }

    if (array_key_exists('proxy-authorization', $this->headers)) {
      $this->p_load_request_proxy_authorization();
      unset($headers['proxy-authorization']);
    }

    if (array_key_exists('referer', $this->headers)) {
      $this->p_load_request_referer();
      unset($headers['referer']);
    }

    if (array_key_exists('script-name', $this->headers)) {
      $this->p_load_request_script_name();
      unset($headers['script-name']);
    }

    if (array_key_exists('te', $this->headers)) {
      $this->p_load_request_te();
      unset($headers['te']);
    }

    if (array_key_exists('upgrade', $this->headers)) {
      $this->p_load_request_upgrade();
      unset($headers['upgrade']);
    }

    if (array_key_exists('uri', $this->headers)) {
      $this->p_load_request_uri();
      unset($headers['uri']);
    }

    if (array_key_exists('user-agent', $this->headers)) {
      $this->p_load_request_user_agent();
      unset($headers['user-agent']);
    }

    if (array_key_exists('via', $this->headers)) {
      $this->p_load_request_via();
      unset($headers['via']);
    }

    if (array_key_exists('warning', $this->headers)) {
      $this->p_load_request_warning();
      unset($headers['warning']);
    }

    if (array_key_exists('x-requested-with', $this->headers)) {
      $this->p_load_request_x_requested_with();
      unset($headers['x_requested_with']);
    }

    if (array_key_exists('x-forwarded-for', $this->headers)) {
      $this->p_load_request_x_requested_for();
      unset($headers['x-forwarded-for']);
    }

    if (array_key_exists('x_-orwarded-host', $this->headers)) {
      $this->p_load_request_x_requested_host();
      unset($headers['x-forwarded-host']);
    }

    if (array_key_exists('x-forwarded-proto', $this->headers)) {
      $this->p_load_request_x_requested_proto();
      unset($headers['x-forwarded-proto']);
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
        $this->p_load_request_unknown($header_name, static::REQUEST_UNKNOWN, 256);
      }
      unset($header_name);
      unset($header_value);
    }
    unset($headers);

    return new c_base_return_true();
  }

  /**
   * Chooses a language based on available languages and the requested languages.
   *
   * Because multiple languages may be returned, this does not explicitly define the langugae headers.
   *
   * @param array $supported_languages
   *   An array of supported languages as defined in i_base_languages.
   *
   * @return c_base_return_int
   *   An integer representing the language code as defined in i_base_languages.
   *   0 with the error bit set is returned on error.
   *   An integer representing the language code as defined in i_base_languages with the error bit set is returned on error.
   */
  public function select_language($supported_languages) {
    if (!is_array($supported_languages) || empty($supported_languages)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'supported_languages', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    // specify a failsafe in case decision making has trouble.
    $language_chosen = reset($supported_languages);

    if (isset($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']) && is_array($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data'])) {
      if (isset($this->request[static::REQUEST_ACCEPT_LANGUAGE]['invalid']) && $this->request[static::REQUEST_ACCEPT_LANGUAGE]['invalid']) {;
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'supported_languages', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_value($language_chosen, 'c_base_return_int', $error);
      }

      if (isset($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight']) && is_array($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight'])) {
        foreach ($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight'] as $weight => $aliases) {
          $alias = end($aliases);
          $language_code = c_base_defaults_global::s_get_languages()::s_get_id_by_alias($alias)->get_value_exact();
          unset($alias);

          if (array_key_exists($language_code, $supported_languages)) {
            $language_chosen = $language_code;
            break;
          }
        }
        unset($weight);
        unset($aliases);
        unset($language_code);
      }
    }

    return c_base_return_int::s_new($language_chosen);
  }

  /**
   * Assign HTTP response header: access-control-allow-origin.
   *
   * Note on multiple urls: The standard appears to only support one url.
   * Therefore, to have multiple urls, the clients ORIGIN should be checked against a known list.
   * Then, from that list either the default or the clients ORIGIN is sent.
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
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Origin
   */
  public function set_response_access_control_allow_origin($uri) {
    if (!is_string($uri)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'uri', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    if ($uri == c_base_ascii::ASTERISK) {
      $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN] = array('wildcard' => TRUE);
    }
    else {
      $text = $this->pr_rfc_string_prepare($uri);
      if ($text['invalid']) {
        unset($text);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      $parsed = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed['invalid']) {
        unset($parsed);
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'uri', ':{expected_format}' => NULL, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }
      unset($parsed['invalid']);
      unset($parsed['current']);

      $parsed['wildcard'] = FALSE;
      $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN] = $parsed;
      unset($parsed);
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-credentials.
   *
   * @param bool $allow_credentials
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Credentials
   */
  public function set_response_access_control_allow_credentials($allow_credentials) {
    if (!is_bool($allow_credentials)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'allowed_credentials', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS] = $allow_credentials;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-expose-headers.
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
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Expose-Headers
   */
  public function set_response_access_control_expose_headers($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_token = $this->p_prepare_token($header_name);
    if ($prepared_token === FALSE) {
      unset($prepared_token);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->p_prepare_token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS]) || !is_array($this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS])) {
        $this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS] = array();
      }

      $this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS][$prepared_token] = $prepared_token;
    }
    else {
      $this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS] = array($prepared_token => $prepared_token);
    }
    unset($prepared_token);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-max-age.
   *
   * @param int|float $seconds
   *   The seconds to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Max-Age
   */
  public function set_response_access_control_max_age($seconds) {
    if (!is_int($seconds) && !is_float($seconds)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'seconds', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_ACCESS_CONTROL_MAX_AGE] = $seconds;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-methods.
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
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Methods
   */
  public function set_response_access_control_allow_methods($method, $append = TRUE) {
    if (!is_int($method)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'method', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    // this method does nothing.
    if ($method === static::HTTP_METHOD_NONE) {
      return new c_base_return_true();
    }


    // require only valid/known methods.
    switch ($method) {
      case static::HTTP_METHOD_NONE:
      case static::HTTP_METHOD_GET:
      case static::HTTP_METHOD_HEAD:
      case static::HTTP_METHOD_POST:
      case static::HTTP_METHOD_PUT:
      case static::HTTP_METHOD_DELETE:
      case static::HTTP_METHOD_TRACE:
      case static::HTTP_METHOD_OPTIONS:
      case static::HTTP_METHOD_CONNECT:
      case static::HTTP_METHOD_PATCH:
      case static::HTTP_METHOD_TRACK:
      case static::HTTP_METHOD_DEBUG:
        break;

      default:
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'method', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }


    if ($append) {
      if (!isset($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS]) || !is_array($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS])) {
        $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] = array();
      }

      $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS][$method] = $method;
    }
    else {
      $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] = array($method => $method);
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: access-control-allow-headers.
   *
   * @param string $header_name
   *   The header name to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Headers
   */
  public function set_response_access_control_allow_headers($header_name, $append = TRUE) {
    if (!is_string($header_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_token = $this->p_prepare_token($header_name);
    if ($prepared_token === FALSE) {
      unset($prepared_token);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->p_prepare_token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS]) || !is_array($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS])) {
        $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS] = array();
      }

      $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS][$prepared_token] = $prepared_token;
    }
    else {
      $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS] = array($prepared_token => $prepared_token);
    }
    unset($prepared_token);

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
   * @see: https://tools.ietf.org/html/rfc2616#section-3.12
   */
  public function set_response_accept_patch($media_type, $append = TRUE) {
    if (!is_string($media_type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'media_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $text = $this->pr_rfc_string_prepare($media_type);
    if ($text['invalid']) {
      unset($text);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $parsed = $this->pr_rfc_string_is_media_type($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      unset($parsed);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'media type', ':{expected_format}' => '1*(tchar) "/" 1*(tchar) *(*(wsp) ";" *(wsp) 1*(1*(tchar) *(wsp) "=" *(wsp) 1*(tchar) / (quoted-string)))', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }
    unset($parsed['invalid']);
    unset($parsed['current']);

    if ($append) {
      if (!isset($this->response[static::RESPONSE_ACCEPT_PATCH]) || !is_array($this->response[static::RESPONSE_ACCEPT_PATCH])) {
        $this->response[static::RESPONSE_ACCEPT_PATCH] = array();
      }

      $this->response[static::RESPONSE_ACCEPT_PATCH][] = $parsed;
    }
    else {
      $this->response[static::RESPONSE_ACCEPT_PATCH] = array($parsed);
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'ranges', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_token = $this->p_prepare_token($ranges);
    if ($prepared_token === FALSE) {
      unset($prepared_token);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->p_prepare_token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_ACCEPT_RANGES] = $prepared_token;
    unset($prepared_token);

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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'seconds', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $this->response[static::RESPONSE_AGE] = $seconds;
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'allow', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    switch ($allow) {
      case static::HTTP_METHOD_NONE:
      case static::HTTP_METHOD_GET:
      case static::HTTP_METHOD_HEAD:
      case static::HTTP_METHOD_POST:
      case static::HTTP_METHOD_PUT:
      case static::HTTP_METHOD_DELETE:
      case static::HTTP_METHOD_TRACE:
      case static::HTTP_METHOD_OPTIONS:
      case static::HTTP_METHOD_CONNECT:
      case static::HTTP_METHOD_PATCH:
      case static::HTTP_METHOD_TRACK:
      case static::HTTP_METHOD_DEBUG:
        break;
      default:
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'allow', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if ($allow == static::HTTP_METHOD_NONE) {
      $this->response[static::RESPONSE_ALLOW] = array($allow => $allow);
      return new c_base_return_true();
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_ALLOW]) || !is_array($this->response[static::RESPONSE_ALLOW])) {
        $this->response[static::RESPONSE_ALLOW] = array();
      }

      $this->response[static::RESPONSE_ALLOW][$allow] = $allow;
    }
    else {
      $this->response[static::RESPONSE_ALLOW] = array($allow => $allow);
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'directive_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($directive_value) && !is_string($directive_value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'directive_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    switch($directive_name) {
      case static::CACHE_CONTROL_NO_CACHE:
      case static::CACHE_CONTROL_NO_STORE:
      case static::CACHE_CONTROL_NO_TRANSFORM:
      case static::CACHE_CONTROL_MAX_AGE:
      case static::CACHE_CONTROL_MAX_AGE_S:
      case static::CACHE_CONTROL_MAX_STALE:
      case static::CACHE_CONTROL_MIN_FRESH:
      case static::CACHE_CONTROL_ONLY_IF_CACHED:
      case static::CACHE_CONTROL_PUBLIC:
      case static::CACHE_CONTROL_PRIVATE:
      case static::CACHE_CONTROL_MUST_REVALIDATE:
      case static::CACHE_CONTROL_PROXY_REVALIDATE:
        break;

      default:
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'directive_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    $parsed_directive_value = NULL;
    if (!is_null($directive_value)) {
      $text = $this->pr_rfc_string_prepare($directive_value);
      if ($text['invalid']) {
        unset($text);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      $parsed = $this->pr_rfc_string_is_token_quoted($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed['invalid']) {
        unset($parsed);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'directive value', ':{expected_format}' => '1*(tchar) *("=" 1*(1*(tchar) / quoted-string) *(*(wsp) "," *(wsp) 1*(tchar) *("=" 1*(1*(tchar) / quoted-string))', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }
      unset($parsed);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_CACHE_CONTROL]) || !is_array($this->response[static::RESPONSE_CACHE_CONTROL])) {
        $this->response[static::RESPONSE_CACHE_CONTROL] = array();
      }

      $this->response[static::RESPONSE_CACHE_CONTROL][$directive_name] = $directive_value;
    }
    else {
      $this->response[static::RESPONSE_CACHE_CONTROL] = array($directive_name => $directive_value);
    }

    unset($parsed_directive_value);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: connection.
   *
   * @param string $connection_option
   *   The connection option to assign as a connection-specific field.
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
  public function set_response_connection($connection_option, $append = TRUE) {
    if (!is_string($connection_option)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'connection_option', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_token = $this->p_prepare_token($connection_option);
    if ($prepared_token === FALSE) {
      unset($prepared_token);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->p_prepare_token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_CONNECTION]) || !is_array($this->response[static::RESPONSE_CONNECTION])) {
        $this->response[static::RESPONSE_CONNECTION] = array();
      }

      $this->response[static::RESPONSE_CONNECTION][$prepared_token] = $prepared_token;
    }
    else {
      $this->response[static::RESPONSE_CONNECTION] = array($prepared_token => $prepared_token);
    }
    unset($prepared_token);

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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'content', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($is_file)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'is_file', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
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
   * - 1*(tchar) *(";" (wsp) 1*(tchar) "=" 1*(tchar) / 1*(quoted-string))
   *
   * The "type" is: 1*(tchar)
   * The "parameter_name" is: 1*(tchar)
   * The "parameter_value" is: 1*(tchar) / 1*(quoted-string)
   *
   * @param string|null $type
   *   The disposition type string to assign.
   *   May be NULL when append is TRUE.
   *   Both $type and $parameter_name may not be NULL.
   * @param string|null $parameter_name
   *   (optional) A single disposition parameter to be added.
   *   If NULL, then this is ignored.
   *   Must not be NULL when $parameter_value is not NULL.
   * @param string|null $parameter_value
   *   (optional) A single disposition value to be added.
   *   If NULL, then this is ignored.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *   FALSE without error bit is returned when $type is NULL but there is no type assigned or append is FALSE.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  public function set_response_content_disposition($type, $parameter_name = NULL, $parameter_value = NULL, $append = TRUE) {
    if (!is_null($type) && !is_string($type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($parameter_name) && !is_string($parameter_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($parameter_value) && !is_string($parameter_value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    // nothing to do!
    if (is_null($type) && (is_null($parameter_name) || $append === FALSE)) {
      return new c_base_return_false();
    }

    if (is_null($parameter_name) && !is_null($parameter_value)) {
      return new c_base_return_false();
    }


    $prepared_token = NULL;
    if (is_string($type)) {
      $prepared_token = $this->p_prepare_token($type);
      if ($prepared_token === FALSE) {
        unset($prepared_token);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'content disposition type', ':{expected_format}' => '1*(tchar)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }

      if (empty($prepared_token)) {
        unset($prepared_token);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    $prepared_parameter_name = NULL;
    if (is_string($parameter_name)) {
      $prepared_parameter_name = $this->p_prepare_token($parameter_name);
      if ($prepared_parameter_name === FALSE) {
        unset($prepared_parameter_name);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'content disposition parameter name', ':{expected_format}' => '1*(tchar)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }

      if (empty($prepared_parameter_name)) {
        unset($prepared_parameter_name);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    if (is_string($parameter_value)) {
      $text = $this->pr_rfc_string_prepare($parameter_value);
      if ($text['invalid']) {
        unset($text);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      $parsed_parameter_value = $this->pr_rfc_string_is_token_quoted($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed_parameter_value['invalid']) {
        unset($parsed_parameter_value);
        unset($prepared_token);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'disposition parameter value', ':{expected_format}' => '1*(tchar) / 1*(quoted-string)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }
      unset($parsed_parameter_value['invalid']);
      unset($parsed_parameter_value['current']);
    }
    else {
      $parsed_parameter_value = NULL;
    }

    if (!isset($this->response[static::RESPONSE_CONTENT_DISPOSITION])) {
      // type cannot be NULL if there is no type currently assigned.
      if (is_null($type)) {
        unset($prepared_token);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->response[static::RESPONSE_CONTENT_DISPOSITION] = array(
        'type' => NULL,
        'parameters' => array(),
      );
    }

    if (is_string($type)) {
      $this->response[static::RESPONSE_CONTENT_DISPOSITION]['type'] = $prepared_token;
    }
    unset($prepared_token);

    if (is_string($parameter_name)) {
      if ($append) {
        $this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters'][$prepared_parameter_name] = $parsed_parameter_value['text'];
      }
      else {
        $this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters'] = array($prepared_parameter_name => $parsed_parameter_value['text']);
      }
    }
    unset($prepared_parameter_name);
    unset($parsed_parameter_value);

    return new c_base_return_true();
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
   * - Defines the encoding applied to the content for the purpose of transmitting.
   * - This is completely unrelated to the mime-type such that the example.html.gz could still be gzipped (such as using a different compression level) but it would represent example.html.gz and not example.html.gz.gz.
   *
   * This becomes more convoluted because the standard for content encoding states that it does not refer to encodings "inherent" to the content type.
   * - If that is the case, then the file example.html.gz would have no content-encoding because the file being transmitted is example.html.gz.
   *
   * The standard uses "Content-Encoding: gzip" as an example, even though gzip is used as a transfer-encoding.
   * - This means that the encoding is not about the content, but instead of how the content is being transferred (which sounds an awful lot like the words "transfer" and "encoding").
   *
   * That said, many sites and services out there appear to use (and document) that content-encoding is used for compressing data on transfer.
   * - This directly conflicts with the standards requirements as the compression is done for the transfer (via transfer encoding).
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
   * @param int $encoding
   *   The encoding to assign to the specified header.
   * @param bool $append
   *   (optional) If TRUE, then append the header value.
   *   If FALSE, then assign the header value.
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'encoding', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    switch ($encoding) {
      case static::ENCODING_CHUNKED:
      case static::ENCODING_COMPRESS:
      case static::ENCODING_DEFLATE:
      case static::ENCODING_GZIP:
      case static::ENCODING_BZIP:
      case static::ENCODING_LZO:
      case static::ENCODING_XZ:
      case static::ENCODING_EXI:
      case static::ENCODING_IDENTITY:
      case static::ENCODING_SDCH:
      case static::ENCODING_PG:
        break;
      default:
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'encoding', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
    }

    if ($append) {
      $this->response[static::RESPONSE_CONTENT_ENCODING][] = $encoding;
    }
    else {
      $this->response[static::RESPONSE_CONTENT_ENCODING] = array($encoding);
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content-language.
   *
   * @param int|null $language
   *   The language code to assign to the specified header.
   *   If NULL, then the default language according the the given language class is used.
   *   If NULL and the default language is not set, then FALSE with error bit set is returned.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *   When $language is NULL, $append is treated as FALSE.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.3.2
   */
  public function set_response_content_language($language = NULL, $append = TRUE) {
    if (!is_null($language) && !is_int($language)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'language', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_object($this->languages)) {
      $this->languages = c_base_defaults_global::s_get_languages();
    }

    if (is_null($language)) {
      $default = $this->languages->s_get_default_id();
      if ($default instanceof c_base_return_false) {
        unset($default);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->languages->s_get_default_id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      $this->response[static::RESPONSE_CONTENT_LANGUAGE] = array($default->get_value_exact());
      unset($default);
    }
    else {
      if ($this->languages->s_get_names_by_id($language) instanceof c_base_return_false) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->languages->s_get_names_by_id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      if (!isset($this->response[static::RESPONSE_CONTENT_LANGUAGE])) {
        $this->response[static::RESPONSE_CONTENT_LANGUAGE] = array();
      }

      $this->response[static::RESPONSE_CONTENT_LANGUAGE][] = $language;
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
   * @param bool $force
   *   (optional) Set to TRUE, override the standard and enforce a content-length regardless of transfer-encoding.
   *   When FALSE adhere to the RFC in regards to transfer-encoding.
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
  public function set_response_content_length($length = NULL, $force = FALSE) {
    if (!is_null($length) && !is_int($length) || $length < 0) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'length', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($force)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'force', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    // From the RFC: "A sender MUST NOT send a Content-Length header field in any message that contains a Transfer-Encoding header field."
    if ($force === FALSE && array_key_exists(static::RESPONSE_TRANSFER_ENCODING, $this->response)) {
      return new c_base_return_false();
    }

    if (is_null($length)) {
      if (is_null($this->content)) {
        $this->response[static::RESPONSE_CONTENT_LENGTH] = 0;
      }
      else {
        if ($this->content_is_file) {
          $this->response[static::RESPONSE_CONTENT_LENGTH] = 0;

          foreach ($this->content as $filename) {
            if (!file_exists($filename)) {
              unset($filename);

              $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
              return c_base_return_error::s_false($error);
            }

            $this->response[static::RESPONSE_CONTENT_LENGTH] += filesize($filename);
          }
        }
        else {
          $this->response[static::RESPONSE_CONTENT_LENGTH] = $this->p_calculate_content_length($this->content);
        }
      }
    }
    else {
      $this->response[static::RESPONSE_CONTENT_LENGTH] = $length;
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content_range.
   *
   * Currently only byte ranges are supported.
   * Ranges used in this function represent bytes.
   * The ranges are inclusive and start at 0.
   *
   * @param int|bool $start
   *   The start range.
   *   Set to FALSE to represent a not satisfiable range.
   * @param int|bool $stop
   *   The stop range.
   *   Set to FALSE to represent a not satisfiable range.
   * @param int|bool $total
   *   An integer representing the total bytes.
   *   May be set to FALSE to designate that the total range is unkown.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-4.2
   */
  public function set_response_content_range($start, $stop, $total) {
    if (!is_int($start) && $start !== FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'start', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($stop) && $stop !== FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'stop', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($total) && $total !== FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'total', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    // unsatisfiable requires a total to be specified.
    if (($start === FALSE || $stop === FALSE) && $total === FALSE) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'total', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_CONTENT_RANGE] = array(
      'total' => $total,
      'type' => 'bytes',
      'range' => array(
        'start' => $start,
        'stop' => $stop,
      )
    );

    if ($start === FALSE || $stop === FALSE) {
      $this->response[static::RESPONSE_CONTENT_RANGE]['range'] = FALSE;
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content_type.
   *
   * @todo: implement a thorough sanity check, this currently uses a simple check.
   *
   * @param string|int $content_type
   *   The content type to assign to the specified header.
   *   May be an integer representing a mime type as defined in c_base_mime.
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
    if (is_int($content_type)) {
      $result = c_base_mime::s_get_names_by_id($content_type);
      if ($result instanceof c_base_return_false) {
        unset($result);
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'content_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $content_type_string = array_shift($result);
      unset($result);
    }
    elseif (!is_string($content_type)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'content_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }
    else {
      $result = c_base_mime::s_identify($content_type, TRUE);
      if ($result instanceof c_base_return_false) {
        unset($result);
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'content_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $content_type_string = $result->get_value_exact()['name_category'] . '/' . $result->get_value_exact()['name_type'];
      unset($result);
    }

    if (!c_base_charset::s_is_valid($charset)) {
      unset($content_type_string);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'charset', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $this->response[static::RESPONSE_CONTENT_TYPE] = array(
      'type' => $content_type_string,
      'charset' => $charset,
    );
    unset($content_type_string);

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
        // @todo: create a date managing class that auto-generates a date value for global use one time per php execution to avoid multiple calls.
        $timezone_old = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $this->request_time = microtime(TRUE);

        date_default_timezone_set($timezone_old);
        unset($timezone_old);
      }

      $this->response[static::RESPONSE_DATE] = $this->request_time;
      return new c_base_return_true();
    }

    if (!is_int($timestamp) && !is_float($timestamp)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'timestamp', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $this->response[static::RESPONSE_DATE] = $timestamp;
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

      $this->response[static::RESPONSE_DATE_ACTUAL] = $this->request_time;
      return new c_base_return_true();
    }

    if (!is_int($timestamp) && !is_float($timestamp)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'timestamp', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $this->response[static::RESPONSE_DATE_ACTUAL] = $timestamp;
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'entity_tag', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
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

            $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
            return c_base_return_error::s_false($error);
          }

          $success = hash_update_file($hash, $filename);
          if (!$success) {
            unset($success);
            unset($filename);
            unset($hash);
            unset($response);

            $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
            return c_base_return_error::s_false($error);
          }
        }
        unset($filename);
        unset($success);

        $response['tag'] = hash_final($hash, FALSE);
        unset($hash);

        if ($weak) {
          // Keep the first 15 characters for 'partial:sha256:' plus the first 9 characters of the checksum.
          $response['tag'] = mt_substr($response['tag'], 0, 24);
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

    $this->response[static::RESPONSE_ETAG] = $response;
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'timestamp', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_EXPIRES] = $timestamp;
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'timestamp', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_LAST_MODIFIED] = $timestamp;
    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: link.
   *
   * Use self::SCHEME_LOCAL for a local filesystem link.
   *
   * The standard defines this as:
   * - (uri) *(";" 1*(tchar) "=" 1*(1*(tchar) / 1*(quoted-string)))
   *
   * This standard likely supports multiple link headers.
   *
   * @param string $uri
   *   The URI to assign to the specified header.
   * @param string|null $parameter_name
   *   (optional) A single link parameter to be added.
   *   If NULL, then this is ignored.
   *   Must not be NULL when $parameter_value is not NULL.
   * @param string|null $parameter_value
   *   (optional) A single link value to be added.
   *   If NULL, then this is ignored.
   * @param bool $append
   *   (optional) If TRUE, then append the parameter name.
   *   If FALSE, then assign the parameter name.
   *   When $parameter_name is NULL and this is FALSE, then assign an empty array for parameters associated with $uri.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc5988#section-5
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function set_response_link($uri, $parameter_name = NULL, $parameter_value = NULL, $append = TRUE) {
    if (!is_string($uri)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'uri', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($parameter_name) && !is_string($parameter_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($parameter_value) && !is_string($parameter_value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    // nothing to do!
    if (is_null($uri) && (is_null($parameter_name) || $append === FALSE)) {
      return new c_base_return_false();
    }

    if (is_null($parameter_name) && !is_null($parameter_value)) {
      return new c_base_return_false();
    }


    $text = $this->pr_rfc_string_prepare($uri);
    if ($text['invalid']) {
      unset($text);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $parsed = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed_uri['invalid']) {
      unset($parsed_uri);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'link uri', ':{expected_format}' => '(uri)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }
    unset($parsed_uri['invalid']);


    // when append is FALSE and there is no parameter name, then assign url instead of appending url.
    if (!$append && is_null($parameter_name)) {
      if (!isset($this->response[static::RESPONSE_LINK])) {
        $this->response[static::RESPONSE_LINK] = array();
      }

      $this->response[static::RESPONSE_LINK][$uri] = array(
        'uri' => $parsed_uri,
        'parameters' => array(),
      );
      unset($parsed_uri);

      return new c_base_return_true();
    }


    $prepared_parameter_name = NULL;
    if (is_string($parameter_name)) {
      $prepared_parameter_name = $this->p_prepare_token($parameter_name);
      if ($prepared_parameter_name === FALSE) {
        unset($prepared_parameter_name);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'link parameter name', ':{expected_format}' => '1*(tchar)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }

      if (empty($prepared_parameter_name)) {
        unset($prepared_parameter_name);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }


    if (is_string($parameter_value)) {
      $text = $this->pr_rfc_string_prepare($parameter_value);
      if ($text['invalid']) {
        unset($text);
        unset($parsed_uri);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      $parsed_parameter_value = $this->pr_rfc_string_is_token_quoted($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed_parameter_value['invalid']) {
        unset($parsed_parameter_value);
        unset($prepared_token);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'content disposition parameter value', ':{expected_format}' => '1*(tchar) / 1*(quoted-string)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }
      unset($parsed_parameter_value['invalid']);
      unset($parsed_parameter_value['current']);
    }
    else {
      $parsed_parameter_value = NULL;
    }


    if (!isset($this->response[static::RESPONSE_LINK])) {
      $this->response[static::RESPONSE_LINK] = array();
    }

    if (!array_key_exists($uri, $this->response[static::RESPONSE_LINK])) {
      $this->response[static::RESPONSE_LINK][$uri] = array(
        'uri' => $parsed_uri,
        'parameters' => array()
      );
    }
    unset($parsed_uri);

    if (is_string($parameter_name)) {
      if ($append) {
        $this->response[static::RESPONSE_LINK][$uri]['parameters'][$prepared_parameter_name] = $parsed_parameter_value['text'];
      }
      else {
        $this->response[static::RESPONSE_LINK][$uri]['parameters'] = array($prepared_parameter_name => $parsed_parameter_value['text']);
      }
    }
    unset($prepared_parameter_name);
    unset($parsed_parameter_value);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: location.
   *
   * Use self::SCHEME_LOCAL for a local filesystem link.
   *
   * @param stringarray $uri
   *   When a string, the URI to assign to the specified header.
   *   When an array, an array of the destination url parts to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function set_response_location($uri) {
    if (!is_string($uri) && !is_array($uri)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'uri', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_array($uri)) {
      $uri_string = $this->pr_rfc_string_combine_uri_array($uri);
      if ($uri_string === FALSE) {
        unset($parts);
        unset($combined);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'URI or URL', ':{expected_format}' => 'URI or URL', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }
      unset($combined);
    }
    else {
      $uri_string = $uri;
    }

    $text = $this->pr_rfc_string_prepare($uri_string);
    if ($text['invalid']) {
      unset($text);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $parsed = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      unset($parsed);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'uri', ':{expected_format}' => NULL, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }
    unset($parsed['invalid']);

    $this->response[static::RESPONSE_LOCATION] = $parsed;
    unset($parsed);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: pragma.
   *
   * The standard defines this as:
   * - 1*(tchar) [ "=" ( 1*(tchar) / 1*(quoted-string) ] *("," (wsp) 1*(tchar) [ "=" ( 1*(tchar) / 1*(quoted-string) ])
   *
   * The "parameter_name" is: 1*(tchar)
   * The "parameter_value" is: 1*(tchar) / 1*(quoted-string)
   *
   * @param string $parameter_name
   *   A single pragma parameter to be added.
   * @param string|null $parameter_value
   *   (optional) A single pragma value to be added.
   *   If NULL, then this is ignored.
   * @param bool $append
   *   (optional) If TRUE, then append the header name.
   *   If FALSE, then assign the header name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.32
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  public function set_response_pragma($parameter_name, $parameter_value = NULL, $append = TRUE) {
    if (!is_string($parameter_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($parameter_value) && !is_string($parameter_value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_parameter_name = NULL;
    if (is_string($parameter_name)) {
      $prepared_parameter_name = $this->p_prepare_token($parameter_name);
      if ($prepared_parameter_name === FALSE) {
        unset($prepared_parameter_name);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'pragma parameter name', ':{expected_format}' => '1*(tchar)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }

      if (empty($prepared_parameter_name)) {
        unset($prepared_parameter_name);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }
    }

    if (is_string($parameter_value)) {
      $text = $this->pr_rfc_string_prepare($parameter_value);
      if ($text['invalid']) {
        unset($text);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
        return c_base_return_error::s_false($error);
      }

      $parsed_parameter_value = $this->pr_rfc_string_is_token_quoted($text['ordinals'], $text['characters']);
      unset($text);

      if ($parsed_parameter_value['invalid']) {
        unset($parsed_parameter_value);
        unset($prepared_token);

        $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'pragma parameter value', ':{expected_format}' => '1*(tchar) / 1*(quoted-string)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
        return c_base_return_error::s_false($error);
      }
      unset($parsed_parameter_value['invalid']);
      unset($parsed_parameter_value['current']);
    }
    else {
      $parsed_parameter_value = NULL;
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_PRAGMA])) {
        $this->response[static::RESPONSE_PRAGMA] = array();
      }

      $this->response[static::RESPONSE_PRAGMA][$prepared_parameter_name] = $parsed_parameter_value['text'];
    }
    else {
      $this->response[static::RESPONSE_PRAGMA] = array($prepared_parameter_name => $parsed_parameter_value['text']);
    }
    unset($prepared_parameter_name);
    unset($parsed_parameter_value);

    return new c_base_return_true();
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
    // @todo: self::RESPONSE_PROXY_AUTHENTICATE

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response proxy authenticate', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    // @todo: self::RESPONSE_PUBLIC_KEY_PINS

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response public key pins', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    // @todo: self::RESPONSE_REFRESH

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response refresh', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!is_int($date)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'date', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($seconds)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'seconds', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $this->response[static::RESPONSE_RETRY_AFTER] = array(
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
    // @todo: self::RESPONSE_SERVER

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response server', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assign HTTP response header: set-cookie.
   *
   * @param c_base_cookie $cookie
   *   The cookie object to assign as a cookie.
   *   This object is cloned.
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
   * @see: https://tools.ietf.org/html/rfc6265
   */
  public function set_response_set_cookie($cookie, $append = TRUE) {
    if (!($cookie instanceof c_base_cookie)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'cookie', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $cookie_name = $cookie->get_name()->get_value_exact();
    if ($append) {
      if (!isset($this->response[static::RESPONSE_SET_COOKIE]) || !is_array($this->response[static::RESPONSE_SET_COOKIE])) {
        $this->response[static::RESPONSE_SET_COOKIE] = array();
      }

      $this->response[static::RESPONSE_SET_COOKIE][$cookie_name] = clone($cookie);
    }
    else {
      $this->response[static::RESPONSE_SET_COOKIE] = array($cookie_name => clone($cookie));
    }
    unset($cookie_name);

    return new c_base_return_true();
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
   * // @fixme: there are more response status definitions, the see: needs to be updated.
   * @see: https://tools.ietf.org/html/rfc7232#section-4
   */
  public function set_response_status($code) {
    if (!is_int($code)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'code', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_STATUS] = $code;
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
    // @todo: self::RESPONSE_STRICT_SECURITY_TRANSPORT

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response strict transport security', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    // @todo: self::RESPONSE_TRAILER

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response trailer', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (array_key_exists(static::RESPONSE_CONTENT_LENGTH, $this->response)) {
      unset($this->response[static::RESPONSE_CONTENT_LENGTH]);
    }

    // @todo: self::RESPONSE_TRANSFER_ENCODING

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response transfer encoding', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    // @todo: self::RESPONSE_UPGRADE

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response upgrade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_token = $this->p_prepare_token($header_name, FALSE);
    if ($prepared_token === FALSE) {
      unset($prepared_token);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->p_prepare_token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_VARY]) || !is_array($this->response[static::RESPONSE_VARY])) {
        $this->response[static::RESPONSE_VARY] = array();
      }

      $this->response[static::RESPONSE_VARY][$prepared_token] = $prepared_token;
    }
    else {
      $this->response[static::RESPONSE_VARY] = array($prepared_token => $prepared_token);
    }
    unset($prepared_token);

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
    // @todo: self::RESPONSE_WARNING

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response warning', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    // @todo: self::RESPONSE_WWW_AUTHENTICATE

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response www authenticate', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Assign HTTP response header: HTTP Protocol.
   *
   * @param string $protocol
   *   A string representing the HTTP protocol, such as: "HTTP/1.1".
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_protocol($protocol) {
    if (!is_string($protocol)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'protocol', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_PROTOCOL] = $protocol;

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: content-security-policy.
   *
   * 1*((alpha) | (digit) | '-')  1*((wsp) 1*(vchar, except ';' and ',')).
   *
   * Policy Name: 1*((alpha) | (digit) | '-').
   * Policy Value: 1*(vchar, except ';' and ',').
   *
   * There may be multiple policy names.
   * Policy names may have multiple policy values.
   *
   * @param string $policy_name
   *   The name of the policy.
   * @param string $policy_value
   *   A value assigned to the policy.
   * @param bool $append
   *   (optional) Set to TRUE to append values instead of assigning.
   *   Set to FALSE to assign a new value.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://en.wikipedia.org/wiki/Content_Security_Policy
   * @see: https://www.html5rocks.com/en/tutorials/security/content-security-policy/
   */
  public function set_response_content_security_policy($policy_name, $policy_value, $append = TRUE) {
    if (!is_string($policy_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'policy_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_string($policy_value)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'policy_value', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $text = $this->pr_rfc_string_prepare($policy_name);
    if ($text['invalid']) {
      unset($text);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $parsed_policy_name = $this->pr_rfc_string_is_alpha_numeric_dash($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed_policy_name['invalid']) {
      unset($parsed_policy_name);
      unset($prepared_token);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'policy name', ':{expected_format}' => '1*((alpha) | (digit) | '-')', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }
    unset($parsed_policy_name['invalid']);
    unset($parsed_policy_name['current']);


    $text = $this->pr_rfc_string_prepare($policy_name);
    if ($text['invalid']) {
      unset($text);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->pr_rfc_string_prepare', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    $parsed_policy_value = $this->pr_rfc_string_is_directive_value($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed_policy_value['invalid']) {
      unset($parsed_policy_value);
      unset($prepared_token);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'policy value', ':{expected_format}' => '1*(vchar, except \';\' and \',\')', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }
    unset($parsed_policy_value['invalid']);
    unset($parsed_policy_value['current']);


    if ($append) {
      if (!isset($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY]) || !is_array($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY])) {
        $this->response[static::RESPONSE_CONTENT_SECURITY_POLICY] = array(
          $parsed_policy_name['text'] => array(),
        );
      }

      $this->response[static::RESPONSE_CONTENT_SECURITY_POLICY][$parsed_policy_name['text']][] = $parsed_policy_value['text'];
    }
    else {
      $this->response[static::RESPONSE_CONTENT_SECURITY_POLICY] = array($parsed_policy_name['text'] => array($parsed_policy_value['text']));
    }
    unset($parsed_policy_name);
    unset($parsed_policy_value);
  }

  /**
   * Assign HTTP response header: x-content-type-options.
   *
   * @param bool $no_sniff
   *   The value to assign to the specified header.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_x_content_type_options($no_sniff) {
    if (!is_bool($no_sniff)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'no_sniff', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_X_CONTENT_TYPE_OPTIONS] = $no_sniff;

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: x-ua-compatible.
   *
   * Treating both parameter name and parameter value as 1*(tchar).
   *
   * @param string $browser_name
   *   The (short) name of the browser.
   * @param string|null $compatible_version
   *   The version of the browser that will be the most compatible.
   *   Set to NULL to remove the $browser_name.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_x_ua_compatible($browser_name, $compatible_version) {
    if (!is_string($parameter_name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'parameter_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($compatible_version) && !is_string($compatible_version)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'compatible_version', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_browser_name = $this->p_prepare_token($browser_name);
    if ($prepared_browser_name === FALSE) {
      unset($prepared_browser_name);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'browser name', ':{expected_format}' => '1*(tchar)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    if (empty($prepared_browser_name)) {
      unset($prepared_browser_name);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'browser_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    // remove the browser name when compatible version is null.
    if (is_null($compatible_version)) {
      if (isset($this->response[static::RESPONSE_X_UA_COMPATIBLE][$prepared_browser_name])) {
        unset($this->response[static::RESPONSE_X_UA_COMPATIBLE][$prepared_browser_name]);
      }
      unset($prepared_browser_name);

      return new c_base_return_true();
    }


    $prepared_compatible_version = $this->p_prepare_token($compatible_version);
    if ($prepared_compatible_version === FALSE) {
      unset($prepared_compatible_version);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{format_name}' => 'browser name', ':{expected_format}' => '1*(tchar)', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    if (empty($prepared_compatible_version)) {
      unset($prepared_compatible_version);

      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'compatible_version', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_X_UA_COMPATIBLE])) {
        $this->response[static::RESPONSE_X_UA_COMPATIBLE] = array();
      }

      $this->response[static::RESPONSE_X_UA_COMPATIBLE][$prepared_browser_name] = $parsed_compatible_version['text'];
    }
    else {
      $this->response[static::RESPONSE_X_UA_COMPATIBLE] = array($prepared_browser_name => $parsed_compatible_version['text']);
    }
    unset($prepared_browser_name);
    unset($parsed_compatible_version);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: checksum_header.
   *
   * This is a field for applying a checksum to the headers.
   *
   * @param int|null $action
   *   (optional) Define how the checksum is to be processed.
   *   Can only be one of:
   *   - self::CHECKSUM_ACTION_NONE
   *   - self::CHECKSUM_ACTION_AUTO
   *   - self::CHECKSUM_ACTION_MANUAL
   *   When NULL, a default will be assigned.
   * @param int|null $what
   *   (optional) An integer representing the checksum what, can be one of:
   *   - self::CHECKSUM_WHAT_FULL
   *   - self::CHECKSUM_WHAT_PARTIAL
   *   This may be set to NULL.when $action is self::CHECKSUM_ACTION_AUTO.
   * @param int|null $type
   *   (optional) An integer representing the checksum algorithm type.
   *   This may be set to NULL.when $action is self::CHECKSUM_ACTION_AUTO.
   * @param string|null $checksum
   *   (optional) A checksum that represents the content.
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_checksum_header($action = NULL, $what = NULL, $type = NULL, $checksum = NULL) {
    if (is_null($action)) {
      // static:: cannot be used as a default function parameter because it may be used in compile-time constants (which is what default parameters are).
      $action = static::CHECKSUM_ACTION_AUTO;
    }
    elseif (!is_int($action)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($action != static::CHECKSUM_ACTION_NONE && $action != static::CHECKSUM_ACTION_AUTO && $action != static::CHECKSUM_ACTION_MANUAL) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($action == static::CHECKSUM_ACTION_MANUAL) {
      if (!is_int($what)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'what', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_int($type)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_string($checksum)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'checksum', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if ($what != static::CHECKSUM_WHAT_PARTIAL && $what != static::CHECKSUM_WHAT_FULL) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'what', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
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
        case CHECKSUM_CRC32B:
        case CHECKSUM_PG:
          break;
        default:
          $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
      }

      $this->response[static::RESPONSE_CHECKSUM_HEADER] = array(
        'checksum' => $checksum,
        'action' => $action,
        'what' => $what,
        'type' => $type,
      );
    }
    else {

      if (!is_null($what) && !is_int($what)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'what', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($type) && !is_int($type)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($action) && !is_int($action)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->response[static::RESPONSE_CHECKSUM_HEADER] = array(
        'checksum' => NULL,
        'action' => $action,
        'what' => static::CHECKSUM_WHAT_FULL,
        'type' => static::CHECKSUM_SHA256,
      );

      if (!is_null($what)) {
        $this->response[static::RESPONSE_CHECKSUM_HEADER]['what'] = $what;
      }

      if (!is_null($type)) {
        $this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] = $type;
      }
    }

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: checksum_headers.
   *
   * This is an array of header names in which the checksum is processed against.
   * This field is always included in the checksum_header field.
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'header_name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($append)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'append', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $prepared_token = $this->p_prepare_token($header_name);
    if ($prepared_token === FALSE) {
      unset($prepared_token);
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'this->p_prepare_token', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_FAILURE);
      return c_base_return_error::s_false($error);
    }

    if ($append) {
      if (!isset($this->response[static::RESPONSE_CHECKSUM_HEADERS]) || !is_array($this->response[static::RESPONSE_CHECKSUM_HEADERS])) {
        $this->response[static::RESPONSE_CHECKSUM_HEADERS] = array();
      }

      $this->response[static::RESPONSE_CHECKSUM_HEADERS][$prepared_token] = $prepared_token;
    }
    else {
      $this->response[static::RESPONSE_CHECKSUM_HEADERS] = array($prepared_token => $prepared_token);
    }
    unset($prepared_token);

    return new c_base_return_true();
  }

  /**
   * Assign HTTP response header: checksum_content.
   *
   * @param int|null $action
   *   (optional) Define how the checksum is to be processed.
   *   Can only be one of:
   *   - self::CHECKSUM_ACTION_NONE
   *   - self::CHECKSUM_ACTION_AUTO
   *   - self::CHECKSUM_ACTION_MANUAL
   *   When $action is self::CHECKSUM_ACTION_AUTO, the checksum will not be calculated at this point in time.
   *   When $action is NULL, a default is used.
   * @param int|null $what
   *   (optional) An integer representing the checksum what, can be one of:
   *   - self::CHECKSUM_WHAT_FULL
   *   - self::CHECKSUM_WHAT_PARTIAL
   *   This may be set to NULL.when $action is self::CHECKSUM_ACTION_AUTO.
   * @param int|null $type
   *   (optional) An integer representing the checksum algorithm type.
   *   This may be set to NULL.when $action is self::CHECKSUM_ACTION_AUTO.
   * @param string|null $checksum
   *   (optional) A checksum that represents the content.
   *   This is only processed when $action is set to CHECKSUM_ACTION_MANUAL.
   *   Otherwise, this must be NULL.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_response_checksum_content($action = NULL, $what = NULL, $type = NULL, $checksum = NULL) {
    if (is_null($action)) {
      // static:: cannot be used as a default function parameter because it may be used in compile-time constants (which is what default parameters are).
      $action = static::CHECKSUM_ACTION_AUTO;
    }
    else if (!is_int($action)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($action != static::CHECKSUM_ACTION_NONE && $action != static::CHECKSUM_ACTION_AUTO && $action != static::CHECKSUM_ACTION_MANUAL) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($action == static::CHECKSUM_ACTION_MANUAL) {
      if (!is_int($what)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'what', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_int($type)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_string($checksum)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'checksum', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if ($what != static::CHECKSUM_WHAT_PARTIAL && $what != static::CHECKSUM_WHAT_FULL) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'what', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
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
        case CHECKSUM_CRC32B:
        case CHECKSUM_PG:
          break;
        default:
          $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);
      }

      $this->response[static::RESPONSE_CHECKSUM_CONTENT] = array(
        'checksum' => $checksum,
        'action' => $action,
        'what' => $what,
        'type' => $type,
      );
    }
    else {
      if (!is_null($what) && !is_int($what)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'what', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_null($type) && !is_int($type)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      if (!is_int($action)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'action', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
        return c_base_return_error::s_false($error);
      }

      $this->response[static::RESPONSE_CHECKSUM_CONTENT] = array(
        'checksum' => NULL,
        'action' => $action,
        'what' => static::CHECKSUM_WHAT_FULL,
        'type' => static::CHECKSUM_SHA256,
      );

      if (!is_null($what)) {
        $this->response[static::RESPONSE_CHECKSUM_HEADER]['what'] = $what;
      }

      if (!is_null($type)) {
        $this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] = $type;
      }
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'revision', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->response[static::RESPONSE_CONTENT_REVISION] = $revision;

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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'response_id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($response_id, $this->response)) {
      unset($this->response[$response_id]);
    }

    return new c_base_return_true();
  }

  /**
   * Obtain HTTP response header: access-control-allow-origin.
   *
   * @return c_base_return_array
   *   A decoded uri split into its different parts inside an array.
   *   This array also contains a key called 'wildcard' which may be either TRUE or FALSE.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Origin
   */
  public function get_response_access_control_allow_origin() {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN]);
  }

  /**
   * Obtain HTTP response header: access-control-allow-credentials.
   *
   * @return c_base_return_bool
   *   A boolean representing whether or not to allow credentials.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Credentials
   *
   */
  public function get_response_access_control_allow_credentials() {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(FALSE, 'c_base_return_bool', $error);
    }

    return c_base_return_bool::s_new($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS]);
  }

  /**
   * Obtain HTTP response header: access-control-expose-headers.
   *
   * @return c_base_return_array
   *   An array of headers to expose.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Expose-Headers
   */
  public function get_response_access_control_expose_headers() {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS]);
  }

  /**
   * Obtain HTTP response header: access-control-max-age.
   *
   * @return c_base_return_int
   *   An Unix timestamp representing the specified header.
   *   0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Max-Age
   */
  public function get_response_access_control_max_age() {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_MAX_AGE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCESS_CONTROL_MAX_AGE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_ACCESS_CONTROL_MAX_AGE]);
  }

  /**
   * Obtain HTTP response header: access-control-allow-methods.
   *
   * @return c_base_return_array
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Methods
   */
  public function get_response_access_control_allow_methods() {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS]);
  }

  /**
   * Obtain HTTP response header: access-control-allow-headers.
   *
   * @return c_base_return_array
   *   An array of allowed headers is returned.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Headers
   */
  public function get_response_access_control_allow_headers() {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS]);
  }

  /**
   * Obtain HTTP response header: accept-patch.
   *
   * @return c_base_return_array
   *   An array containing the header values.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc5789#section-3.1
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   * @see: https://tools.ietf.org/html/rfc2616#section-3.12
   */
  public function get_response_accept_patch() {
    if (!array_key_exists(static::RESPONSE_ACCEPT_PATCH, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCEPT_PATCH, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ACCEPT_PATCH]);
  }

  /**
   * Obtain HTTP response header: accept_ranges.
   *
   * @return c_base_return_string
   *   A string representing the header value.
   *
   *   Common ranges are:
   *   - bytes
   *   - none
   *
   *   An empty string with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-2.3
   * @see: https://tools.ietf.org/html/rfc7233#section-3.1
   */
  public function get_response_accept_ranges() {
    if (!array_key_exists(static::RESPONSE_ACCEPT_RANGES, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ACCEPT_RANGES, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    return c_base_return_string::s_new($this->response[static::RESPONSE_ACCEPT_RANGES]);
  }

  /**
   * Obtain HTTP response header: age.
   *
   * @return c_base_return_int
   *   A Unix timestamp representing the header value.
   *   0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.1
   */
  public function get_response_age() {
    if (!array_key_exists(static::RESPONSE_AGE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_AGE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_AGE]);
  }

  /**
   * Obtain HTTP response header: allow.
   *
   * @return c_base_return_array
   *   An array of allow method codes.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.1
   */
  public function get_response_allow() {
    if (!array_key_exists(static::RESPONSE_ALLOW, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ALLOW, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ALLOW]);
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
   * @return c_base_return_array
   *   An array containing the cache-control directives.
   *   Each array key is a name and if that directive has no value, then the related directive name will have a NULL value.
   *   For example, a directive of "no-cache" will have the following array structure:
   *   - array("no-cache" => NULL)
   *   For example, a directive of "private, max-age=32" will have the following array structure:
   *   - array("private" => NULL, "max-age" => 32)
   *
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2.3
   */
  public function get_response_cache_control() {
    if (!array_key_exists(static::RESPONSE_CACHE_CONTROL, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CACHE_CONTROL, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CACHE_CONTROL]);
  }

  /**
   * Obtain HTTP response header: connection.
   *
   * @return c_base_return_array
   *   An array of header names assigned to the connection header.
   *   The header name format is:
   *   - 1*(tchar)
   *
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-6.1
   */
  public function get_response_connection() {
    if (!array_key_exists(static::RESPONSE_CONNECTION, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONNECTION, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONNECTION]);
  }

  /**
   * Obtain HTTP response header: content-disposition.
   *
   * @return c_base_return_array
   *   An an containing the decoded content disposition and its parameters.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  public function get_response_content_disposition() {
    if (!array_key_exists(static::RESPONSE_CONTENT_DISPOSITION, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_DISPOSITION, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONTENT_DISPOSITION]);
  }

  /**
   * Obtain HTTP response header: content-encoding.
   *
   * @return c_base_return_array
   *   An array of integers representing the content length value.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_encoding() {
    if (!array_key_exists(static::RESPONSE_CONTENT_ENCODING, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_ENCODING, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONTENT_ENCODING]);
  }

  /**
   * Obtain HTTP response header: content-language.
   *
   * @return c_base_return_array
   *   An array of integers representing the content language value.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.3.2
   */
  public function get_response_content_language() {
    if (!array_key_exists(static::RESPONSE_CONTENT_LANGUAGE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_LANGUAGE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONTENT_LANGUAGE]);
  }

  /**
   * Obtain HTTP response header: content-length.
   *
   * @return c_base_return_int
   *   An integer containing the response header value.
   *   0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.2
   */
  public function get_response_content_length() {
    if (!array_key_exists(static::RESPONSE_CONTENT_LENGTH, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_LENGTH, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_CONTENT_LENGTH]);
  }

  /**
   * Obtain HTTP response header: content_range.
   *
   * Ranges returned by this function represent bytes.
   * The ranges are inclusive and start at 0.
   *
   * @return c_base_return_array
   *   An array with the following keys:
   *   - 'total': The complete length integer or '*'.
   *   - 'type': A string representing the type of range, usually will be 'bytes'.
   *   - 'range': an array with the following keys (or may be FALSE for not satisfiable range):
   *     - 'start': The start range interger.
   *     - 'stop': The stop range integer.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-4.2
   */
  public function get_response_content_range() {
    if (!array_key_exists(static::RESPONSE_CONTENT_RANGE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_RANGE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONTENT_RANGE]);
  }

  /**
   * Obtain HTTP response header: content_type.
   *
   * @return c_base_return_array
   *   An array containing the following keys:
   *   - 'type': the content type string, such as 'text/html'.
   *   - 'charset': the character set integer, such as: c_base_charset::UTF_8.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  public function get_response_content_type() {
    if (!array_key_exists(static::RESPONSE_CONTENT_TYPE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_TYPE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONTENT_TYPE]);
  }

  /**
   * Obtain HTTP response header: date.
   *
   * @return c_base_return_int|c_base_return_float
   *   A unix timestamp integer.
   *   0.0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  public function get_response_date() {
    if (!array_key_exists(static::RESPONSE_DATE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_DATE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    if (is_float($this->response[static::RESPONSE_DATE])) {
      return c_base_return_float::s_new($this->response[static::RESPONSE_DATE]);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_DATE]);
  }

  /**
   * Obtain HTTP response header: date_actual.
   *
   * This is identical to the HTTP response header: date.
   * The purpose of this is to allow clients to still receive the correct/actual date when HTTP servers, such as apache, overwrite or alter the HTTP date response header.
   * This should therefore be used and calculated with when the date variable.
   *
   * @return c_base_return_int|c_base_return_float
   *   A unix timestamp integer.
   *   0.0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  public function get_response_date_actual() {
    if (!array_key_exists(static::RESPONSE_DATE_ACTUAL, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_DATE_ACTUAL, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    if (is_float($this->response[static::RESPONSE_DATE_ACTUAL])) {
      return c_base_return_float::s_new($this->response[static::RESPONSE_DATE_ACTUAL]);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_DATE_ACTUAL]);
  }

  /**
   * Obtain HTTP response header: etag.
   *
   * @return c_base_return_array
   *   An array containing the following:
   *   - tag: The entity tag string (without weakness).
   *   - weak: A boolean representing whether or not the entity tag is weak.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  public function get_response_etag() {
    if (!array_key_exists(static::RESPONSE_ETAG, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_ETAG, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_ETAG]);
  }

  /**
   * Obtain HTTP response header: expires.
   *
   * @return c_base_return_int|c_base_return_float
   *   A unix timestamp integer.
   *   0.0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.3
   */
  public function get_response_expires() {
    if (!array_key_exists(static::RESPONSE_EXPIRES, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_EXPIRES, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    if (is_float($this->response[static::RESPONSE_EXPIRES])) {
      return c_base_return_float::s_new($this->response[static::RESPONSE_EXPIRES]);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_EXPIRES]);
  }

  /**
   * Obtain HTTP response header: last-modified.
   *
   * @return c_base_return_int|c_base_return_float
   *   A unix timestamp integer.
   *   0.0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-2.2
   */
  public function get_response_last_modified() {
    if (!array_key_exists(static::RESPONSE_LAST_MODIFIED, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_LAST_MODIFIED, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0.0, 'c_base_return_float', $error);
    }

    if (is_float($this->response[static::RESPONSE_LAST_MODIFIED])) {
      return c_base_return_float::s_new($this->response[static::RESPONSE_LAST_MODIFIED]);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_LAST_MODIFIED]);
  }

  /**
   * Obtain HTTP response header: link.
   *
   * @todo: break this into an array of the differnt parts.
   *
   * @return c_base_return_array
   *   A decoded link and parameters split into an array.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc5988#section-5
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function get_response_link() {
    if (!array_key_exists(static::RESPONSE_LINK, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_LINK, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_LINK]);
  }

  /**
   * Obtain HTTP response header: location.
   *
   * @todo: consider changing this to an array containing the entire url parts broken into each key.
   *
   * @return c_base_return_array
   *   A decoded uri split into its different parts inside an array.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  public function get_response_location() {
    if (!array_key_exists(static::RESPONSE_LOCATION, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_LOCATION, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_LOCATION]);
  }

  /**
   * Obtain HTTP response header: pragma.
   *
   * @return c_base_return_array
   *   An array containing the processed pragma.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.32
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  public function get_response_pragma() {
    if (!array_key_exists(static::RESPONSE_PRAGMA, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_PRAGMA, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_PRAGMA]);
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
    if (!array_key_exists(static::RESPONSE_PROXY_AUTHENTICATE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_PROXY_AUTHENTICATE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response proxy authenticate', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!array_key_exists(static::RESPONSE_PUBLIC_KEY_PINS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_PUBLIC_KEY_PINS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response public key pins', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!array_key_exists(static::RESPONSE_REFRESH, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_REFRESH, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response refresh', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Obtain HTTP response header: retry-after.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the following:
   *   - value: When 'is_seconds' is FALSE, this is the unix timestamp representing when the page expires.
   *            When 'is_seconds' is FALSE, this is the relative number of seconds until the content expires.
   *   - is_seconds: A boolean that when true changes the interpretation of the 'value' key.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.3
   */
  public function get_response_retry_after() {
    if (!array_key_exists(static::RESPONSE_RETRY_AFTER, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_RETRY_AFTER, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_RETRY_AFTER]);
  }

  /**
   * Obtain HTTP response header: set-cookie.
   *
   * @return c_base_cookie
   *   An HTTP cookie.
   *   A cookie with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc6265
   */
  public function get_response_set_cookie() {
    if (!array_key_exists(static::RESPONSE_SET_COOKIE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_SET_COOKIE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_cookie', $error);
    }

    return $this->response[static::RESPONSE_SET_COOKIE];
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
    if (!array_key_exists(static::RESPONSE_SERVER, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_SERVER, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response server', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Obtain HTTP response header: status.
   *
   * @return c_base_return_int
   *   An integer representing the HTTP status code.
   *   0 with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-4
   */
  public function get_response_status() {
    if (!array_key_exists(static::RESPONSE_STATUS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_STATUS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_STATUS]);
  }

  /**
   * Obtain HTTP response header: strict-transport-security.
   *
   * @return c_base_return_string
   *   A string containing the response header value.
   *   An empty string with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc6797#section-6.1
   */
  public function get_response_strict_transport_security() {
    if (!array_key_exists(static::RESPONSE_STRICT_TRANSPORT_SECURITY, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_STRICT_TRANSPORT_SECURITY, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response strict transport security', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!array_key_exists(static::RESPONSE_TRAILER, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_TRAILER, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response trailer', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!array_key_exists(static::RESPONSE_TRANSFER_ENCODING, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_TRANSFER_ENCODING, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response transfer encoding', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!array_key_exists(static::RESPONSE_UPGRADE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_UPGRADE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response upgrade', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Obtain HTTP response header: vary.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array containing the response header values.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.4
   */
  public function get_response_vary() {
    if (!array_key_exists(static::RESPONSE_VARY, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_VARY, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_VARY]);
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
    if (!array_key_exists(static::RESPONSE_WARNING, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_WARNING, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response warning', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
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
    if (!array_key_exists(static::RESPONSE_WWW_AUTHENTICATE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_WWW_AUTHENTICATE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_false($error);
    }

    // @todo

    $error = c_base_error::s_log(NULL, array('arguments' => array(':{functionality_name}' => 'http response www authenticate', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NO_SUPPORT);
    return c_base_return_error::s_false($error);
  }

  /**
   * Obtain HTTP response header: HTTP Protocol.
   *
   * @return c_base_return_string
   *   A string containing the response header value.
   *   An empty string with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_protocol() {
    if (!array_key_exists(static::RESPONSE_PROTOCOL, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_PROTOCOL, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    return c_base_return_string::s_new($this->response[static::RESPONSE_PROTOCOL]);
  }

  /**
   * Obtain HTTP response header: content-security-policy.
   *
   * @return c_base_return_array
   *   A string containing the response header value.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   *
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://en.wikipedia.org/wiki/Content_Security_Policy
   * @see: https://www.html5rocks.com/en/tutorials/security/content-security-policy/
   */
  public function get_response_content_security_policy() {
    if (!array_key_exists(static::RESPONSE_CONTENT_SECURITY_POLICY, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_SECURITY_POLICY, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY]);
  }

  /**
   * Obtain HTTP response header: x-content-type-options.
   *
   * @return c_base_return_bool
   *   A boolean representing the presence of nosniff.
   *   FALSE with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_x_content_type_options() {
    if (!array_key_exists(static::RESPONSE_X_CONTENT_TYPE_OPTIONS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_X_CONTENT_TYPE_OPTIONS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(FALSE, 'c_base_return_bool', $error);
    }

    return c_base_return_bool::s_new($this->response[static::RESPONSE_X_CONTENT_TYPE_OPTIONS]);
  }

  /**
   * Obtain HTTP response header: x-ua-compatible.
   *
   * @return c_base_return_array
   *   An array containing the response header values.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_x_ua_compatible() {
    if (!array_key_exists(static::RESPONSE_X_UA_COMPATIBLE, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_X_UA_COMPATIBLE, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_X_UA_COMPATIBLE]);
  }

  /**
   * Obtain HTTP response header: checksum_header.
   *
   * @fixme: this should be auto-populated, so don
   *
   * @return c_base_return_array
   *   An array containing:
   *   - 'what': A specific way in which to interpret the checksum.
   *   - 'type': The algorithm type of the checksum.
   *   - 'checksum': The checksum value after it has been base64 decoded.
   *   - 'action': An integer representing how this checksum is processed when generating the HTTP response.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_checksum_header() {
    if (!array_key_exists(static::RESPONSE_CHECKSUM_HEADER, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CHECKSUM_HEADER, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CHECKSUM_HEADER]);
  }

  /**
   * Obtain HTTP response header: checksum_headers.
   *
   * @return c_base_return_array
   *   An array containing a list of header field names.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_checksum_headers() {
    if (!array_key_exists(static::RESPONSE_CHECKSUM_HEADERS, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CHECKSUM_HEADERS, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CHECKSUM_HEADERS]);
  }

  /**
   * Obtain HTTP response header: checksum_content.
   *
   * @return c_base_return_array
   *   An array containing:
   *   - 'what': A specific way in which to interpret the checksum.
   *   - 'type': The algorithm type of the checksum.
   *   - 'checksum': The checksum value after it has been base64 decoded.
   *   - 'action': An integer representing how this checksum is processed when generating the HTTP response.
   *   An empty array with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_checksum_content() {
    if (!array_key_exists(static::RESPONSE_CHECKSUM_CONTENT, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CHECKSUM_CONTENT, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(array(), 'c_base_return_array', $error);
    }

    return c_base_return_array::s_new($this->response[static::RESPONSE_CHECKSUM_CONTENT]);
  }

  /**
   * Obtain HTTP response header: content_revision.
   *
   * @return c_base_return_int
   *   An integer representing a revision number.
   *   0 with error bit set is returned on error, including when the key is not defined.
   */
  public function get_response_content_revision() {
    if (!array_key_exists(static::RESPONSE_CONTENT_REVISION, $this->response)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{index_name}' => static::RESPONSE_CONTENT_REVISION, ':{array_name}' => 'this->response', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_ARRAY_INDEX);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    return c_base_return_int::s_new($this->response[static::RESPONSE_CONTENT_REVISION]);
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
   *    Some web-servers, namely Apache, will alter the headers, causing the order to not be completely shuffled.
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'shuffle', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($this->headers_sent || headers_sent()) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{operation_name}' => 'headers_sent()', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::OPERATION_UNECESSARY);
      return c_base_return_error::s_false($error);
    }


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

    // this is used to create header and still perform checksums against.
    $header_output = array();

    $header_id_to_names = $this->p_get_header_response_mapping(TRUE);


    // response status, this must always be first.
    unset($headers[static::RESPONSE_STATUS]);
    $status_string = NULL;
    if (array_key_exists(static::RESPONSE_STATUS, $this->response)) {
      if (array_key_exists(static::RESPONSE_PROTOCOL, $this->response)) {
        $status_string = $this->response[static::RESPONSE_PROTOCOL] . ' ';
      }
      else {
        $status_string = static::FALLBACK_PROTOCOL . ' ';
      }

      $status_text = c_base_http_status::to_text($this->response[static::RESPONSE_STATUS]);
      if ($status_text instanceof c_base_return_false) {
        $status_string .= $this->response[static::RESPONSE_STATUS];
      }
      else {
        $status_string .= $this->response[static::RESPONSE_STATUS] . ' ' . $status_text->get_value_exact();
      }
      unset($status_text);

      header($status_string, TRUE, $this->response[static::RESPONSE_STATUS]);
    }

    $this->p_prepare_header_response_access_control_allow_origin($header_id_to_names[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN], $header_output);
    $this->p_prepare_header_response_boolean_value($header_id_to_names[static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS], $header_output, static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS);
    $this->p_prepare_header_response_access_control_expose_headers($header_id_to_names[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS], $header_output);
    $this->p_prepare_header_response_simple_value($header_id_to_names[static::RESPONSE_ACCESS_CONTROL_MAX_AGE], $header_output, static::RESPONSE_ACCESS_CONTROL_MAX_AGE);
    $this->p_prepare_header_response_access_control_allow_methods($header_id_to_names[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS], $header_output);
    $this->p_prepare_header_response_access_control_allow_headers($header_id_to_names[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS], $header_output);
    $this->p_prepare_header_response_accept_patch($header_id_to_names[static::RESPONSE_ACCEPT_PATCH], $header_output);
    $this->p_prepare_header_response_simple_value($header_id_to_names[static::RESPONSE_ACCEPT_RANGES], $header_output, static::RESPONSE_ACCEPT_RANGES);
    $this->p_prepare_header_response_simple_value($header_id_to_names[static::RESPONSE_AGE], $header_output, static::RESPONSE_AGE);
    $this->p_prepare_header_response_allow($header_id_to_names[static::RESPONSE_ALLOW], $header_output);
    $this->p_prepare_header_response_cache_control($header_id_to_names[static::RESPONSE_CACHE_CONTROL], $header_output);
    $this->p_prepare_header_response_connection($header_id_to_names[static::RESPONSE_CONNECTION], $header_output);
    $this->p_prepare_header_response_content_disposition($header_id_to_names[static::RESPONSE_CONTENT_DISPOSITION], $header_output);
    $this->p_prepare_header_response_content_encoding($header_id_to_names[static::RESPONSE_CONTENT_ENCODING], $header_output);
    $this->p_prepare_header_response_content_language($header_id_to_names[static::RESPONSE_CONTENT_LANGUAGE], $header_output);
    $this->p_prepare_header_response_simple_value($header_id_to_names[static::RESPONSE_CONTENT_LENGTH], $header_output, static::RESPONSE_CONTENT_LENGTH);
    $this->p_prepare_header_response_content_range($header_id_to_names[static::RESPONSE_CONTENT_RANGE], $header_output, static::RESPONSE_CONTENT_RANGE);
    $this->p_prepare_header_response_content_type($header_id_to_names[static::RESPONSE_CONTENT_TYPE], $header_output);
    $this->p_prepare_header_response_timestamp_value($header_id_to_names[static::RESPONSE_DATE], $header_output, $headers[static::RESPONSE_DATE], static::RESPONSE_DATE);
    $this->p_prepare_header_response_timestamp_value($header_id_to_names[static::RESPONSE_DATE_ACTUAL], $header_output, $headers[static::RESPONSE_DATE_ACTUAL], static::RESPONSE_DATE_ACTUAL);
    $this->p_prepare_header_response_etag($header_id_to_names[static::RESPONSE_ETAG], $header_output);
    $this->p_prepare_header_response_timestamp_value($header_id_to_names[static::RESPONSE_EXPIRES], $header_output, $headers[static::RESPONSE_EXPIRES], static::RESPONSE_EXPIRES);
    $this->p_prepare_header_response_timestamp_value($header_id_to_names[static::RESPONSE_LAST_MODIFIED], $header_output, $headers[static::RESPONSE_LAST_MODIFIED], static::RESPONSE_LAST_MODIFIED);
    $this->p_prepare_header_response_link($header_id_to_names[static::RESPONSE_LINK], $header_output);
    $this->p_prepare_header_response_location($header_id_to_names[static::RESPONSE_LOCATION], $header_output);
    $this->p_prepare_header_response_pragma($header_id_to_names[static::RESPONSE_PRAGMA], $header_output);
    $this->p_prepare_header_response_proxy_authenticate($header_id_to_names[static::RESPONSE_PROXY_AUTHENTICATE], $header_output);
    $this->p_prepare_header_response_public_key_pins($header_id_to_names[static::RESPONSE_PUBLIC_KEY_PINS], $header_output);
    $this->p_prepare_header_response_refresh($header_id_to_names[static::RESPONSE_REFRESH], $header_output);
    $this->p_prepare_header_response_retry_after($header_id_to_names[static::RESPONSE_RETRY_AFTER], $header_output);
    $this->p_prepare_header_response_server($header_id_to_names[static::RESPONSE_SERVER], $header_output);
    $this->p_prepare_header_response_set_cookie($header_id_to_names[static::RESPONSE_SET_COOKIE], $header_output);
    $this->p_prepare_header_response_strict_transport_security($header_id_to_names[static::RESPONSE_STRICT_TRANSPORT_SECURITY], $header_output);
    $this->p_prepare_header_response_trailer($header_id_to_names[static::RESPONSE_TRAILER], $header_output);
    $this->p_prepare_header_response_transfer_encoding($header_id_to_names[static::RESPONSE_TRANSFER_ENCODING], $header_output);
    $this->p_prepare_header_response_upgrade($header_id_to_names[static::RESPONSE_UPGRADE], $header_output);
    $this->p_prepare_header_response_vary($header_id_to_names[static::RESPONSE_VARY], $header_output);
    $this->p_prepare_header_response_warning($header_id_to_names[static::RESPONSE_WARNING], $header_output);
    $this->p_prepare_header_response_www_authenticate($header_id_to_names[static::RESPONSE_WWW_AUTHENTICATE], $header_output);
    $this->p_prepare_header_response_content_security_policy($header_id_to_names[static::RESPONSE_CONTENT_SECURITY_POLICY], $header_output);
    $this->p_prepare_header_response_x_content_type_options($header_id_to_names[static::RESPONSE_X_CONTENT_TYPE_OPTIONS], $header_output);
    $this->p_prepare_header_response_x_ua_compatible($header_id_to_names[static::RESPONSE_X_UA_COMPATIBLE], $header_output);
    $this->p_prepare_header_response_simple_value($header_id_to_names[static::RESPONSE_CONTENT_LENGTH], $header_output, static::RESPONSE_CONTENT_LENGTH);
    $this->p_prepare_header_response_simple_value($header_id_to_names[static::RESPONSE_CONTENT_REVISION], $header_output, static::RESPONSE_CONTENT_REVISION);
    $this->p_prepare_header_response_checksum_content($header_id_to_names[static::RESPONSE_CHECKSUM_CONTENT], $header_output);
    $this->p_prepare_header_response_checksum_header($header_id_to_names[static::RESPONSE_CHECKSUM_HEADERS], $header_id_to_names[static::RESPONSE_CHECKSUM_HEADER], $header_output, $status_string);
    unset($status_string);
    unset($header_id_to_names);


    // send header output.
    foreach ($headers as $header_id => $header_name) {
      if (array_key_exists($header_id, $header_output)) {
        if (is_array($header_output[$header_id])) {
          // the very first header should be a replacement, all others should be an appendment.
          $headers_copy = $header_output[$header_id];
          reset($headers_copy);
          $key = key($headers_copy);
          $sub_header = $headers_copy[$key];

          header($sub_header);
          unset($headers_copy[$key]);
          unset($key);

          foreach ($headers_copy as $sub_header) {
            header($sub_header, FALSE);
          }
          unset($sub_header);
          unset($headers_copy);
        }
        else {
          header($header_output[$header_id]);
        }
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
   * Returns whether or not response content is defined.
   *
   * @return c_base_return_bool
   *   TRUE if content is defined.
   *   FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function has_response_content() {
    if (empty($this->content)) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }

  /**
   * Returns whether or not response content is a file.
   *
   * @return c_base_return_bool
   *   TRUE if content is a file.
   *   FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function is_response_content_file() {
    if ($this->content_is_file) {
      return c_base_return_bool::s_new(TRUE);
    }

    return c_base_return_bool::s_new(FALSE);
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'compression', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($max_filesize) && !(is_int($max_filesize) && $max_filesize > 0)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'max_filesize', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }


    $encoding = $this->p_determine_response_encoding();

    if ($this->content_is_file) {
      if (empty($this->content)) {
        unset($encoding);
        return new c_base_return_false();
      }

      if (is_null($max_filesize)) {
        $content = '';
        foreach ($this->content as $filename) {
          if (!file_exists($filename)) {
            unset($encoding);
            unset($filename);
            unset($content);

            $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
            return c_base_return_error::s_false($error);
          }

          $content .= file_get_contents($filename);
        }
        unset($filename);

        if (empty($content)) {
          unset($encoding);
          unset($content);
          return new c_base_return_false();
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

            $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
            return c_base_return_error::s_false($error);
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
          return new c_base_return_false();
        }
        unset($content_length);

        $this->p_encode_content($content, $encoding, $compression, $compression, FALSE);
        unset($content);
        unset($encoding);

        // the content-length cannot be specified in this case.
        unset($this->response[static::RESPONSE_CONTENT_LENGTH]);
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

          $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
          return c_base_return_error::s_false($error);
        }

        $opened_file = fopen($filename, 'rb');
        if ($opened_file === FALSE) {
          unset($filename);

          $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
          return c_base_return_error::s_false($error);
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
   * Sanitizes a given path to ensure certain combinations of characters are not allowed.
   *
   * This removes any '../' in the path.
   * This removes multiple consecutive '/'.
   * This removes any '/' prefix.
   * This removes any '/' suffix.
   *
   * @param string $path
   *   The path to sanitize
   *
   * @return c_base_return_string
   *   The sanitized string on success.
   *   An empty string with the error bit set is returned on error.
   */
  public function sanitize_path($path) {
    if (!is_string($path)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'path', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value('', 'c_base_return_string', $error);
    }

    // do not support '../' in the url paths (primarily for security reasons).
    $sanitized = preg_replace('@^\.\./(\.\./)*@', '', $path);
    $sanitized = preg_replace('@/(\.\./)+@', '/', $sanitized);

    // remove redundant path parts, such as replacing '//////' with '/'.
    $sanitized = preg_replace('@/(/)+@', '/', $sanitized);

    // remove leading and trailing slashes.
    $sanitized = preg_replace('@(^/|/$)@', '', $sanitized);

    return c_base_return_string::s_new($sanitized);
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
      $this->request[static::REQUEST_ACCEPT]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_ACCEPT]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_ACCEPT]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_ACCEPT]['data']['invalid']) {
      $this->request[static::REQUEST_ACCEPT]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_ACCEPT]['defined'] = TRUE;
      $this->request[static::REQUEST_ACCEPT]['data']['accept'] = NULL;
      $this->request[static::REQUEST_ACCEPT]['data']['category'] = NULL;
      $this->request[static::REQUEST_ACCEPT]['data']['weight'] = array();
      $this->request[static::REQUEST_ACCEPT]['data']['types'] = array();
      #$this->request[static::REQUEST_ACCEPT]['data']['categories'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[static::REQUEST_ACCEPT]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $result = c_base_mime::s_identify($c['choice'], TRUE);
          if ($result instanceof c_base_return_false) {
            unset($result);

            // there is no valid value to process.
            continue;
          }

          $identified = $result->get_value_exact();
          unset($result);

          $c['accept'] = $identified['id_type'];
          $c['category'] = $identified['id_category'];
          $this->request[static::REQUEST_ACCEPT]['data']['types'][$weight][$identified['id_type']] = $identified['id_type'];
          #$this->request[static::REQUEST_ACCEPT]['data']['categories'][$weight][$identified['id_category']] = $identified['id_category'];
          $this->request[static::REQUEST_ACCEPT]['data']['weight'][$weight][$identified['id_type']] = $identified['name_category'] . '/' . $identified['name_type'];

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
      krsort($this->request[static::REQUEST_ACCEPT]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->pr_prepend_array_value(NULL, $this->request[static::REQUEST_ACCEPT]['data']['weight']);

      // rename 'choices' array key to 'accept'.
      $this->request[static::REQUEST_ACCEPT]['data']['accept'] = $this->request[static::REQUEST_ACCEPT]['data']['choices'];
      unset($this->request[static::REQUEST_ACCEPT]['data']['choices']);
    }
    unset($this->request[static::REQUEST_ACCEPT]['data']['invalid']);
    unset($this->request[static::REQUEST_ACCEPT]['data']['current']);

    $this->request[static::REQUEST_ACCEPT]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-language.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.5
   */
  private function p_load_request_accept_language() {
    if (empty($this->headers['accept-language'])) {
      $this->request[static::REQUEST_ACCEPT_LANGUAGE]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept-language']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_ACCEPT_LANGUAGE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_ACCEPT_LANGUAGE]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['invalid']) {
      $this->request[static::REQUEST_ACCEPT_LANGUAGE]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_ACCEPT_LANGUAGE]['defined'] = TRUE;
      $this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $id = c_base_defaults_global::s_get_languages()->s_get_id_by_name($c['choice']);
          if ($id instanceof c_base_return_false) {
            $c['language'] = NULL;
          }
          else {
            $c['language'] = $id->get_value_exact();
            $this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight'][$weight][$c['language']] = c_base_utf8::s_lowercase($c['choice'])->get_value_exact();
            unset($c['choice']);
          }
        }
      }

      // sort the weight array.
      krsort($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->pr_prepend_array_value(NULL, $this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['weight']);

      // rename 'choices' array key to 'language'.
      $this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['language'] = $this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['choices'];
      unset($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['choices']);
    }
    unset($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['invalid']);
    unset($this->request[static::REQUEST_ACCEPT_LANGUAGE]['data']['current']);

    $this->request[static::REQUEST_ACCEPT_LANGUAGE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-encoding.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.4
   */
  private function p_load_request_accept_encoding() {
    if (empty($this->headers['accept-encoding'])) {
      $this->request[static::REQUEST_ACCEPT_ENCODING]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept-encoding']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_ACCEPT_ENCODING]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_ACCEPT_ENCODING]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['invalid']) {
      $this->request[static::REQUEST_ACCEPT_ENCODING]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_ACCEPT_ENCODING]['defined'] = TRUE;
      $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $lowercase = c_base_utf8::s_lowercase($c['choice'])->get_value_exact();
          if ($lowercase == 'chunked') {
            $c['encoding'] = static::ENCODING_CHUNKED;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_CHUNKED] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'compress') {
            $c['encoding'] = static::ENCODING_COMPRESS;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_COMPRESS] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'deflate') {
            $c['encoding'] = static::ENCODING_DEFLATE;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_DEFLATE] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'gzip') {
            $c['encoding'] = static::ENCODING_GZIP;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_GZIP] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'bzip') {
            $c['encoding'] = static::ENCODING_BZIP;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_BZIP] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'lzo') {
            $c['encoding'] = static::ENCODING_LZO;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_LZO] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'xz') {
            $c['encoding'] = static::ENCODING_XZ;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_XZ] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'exit') {
            $c['encoding'] = static::ENCODING_EXI;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_EXI] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'identity') {
            $c['encoding'] = static::ENCODING_IDENTITY;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_IDENTITY] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'sdch') {
            $c['encoding'] = static::ENCODING_SDCH;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_SDCH] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'pg') {
            $c['encoding'] = static::ENCODING_PG;
            $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'][$weight][static::ENCODING_PG] = $lowercase;
            unset($c['choice']);
          }
          else {
            $c['encoding'] = NULL;
          }
        }
      }
      unset($lowercase);

      // sort the weight array.
      krsort($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->pr_prepend_array_value(NULL, $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight']);

      // rename 'choices' array key to 'encoding'.
      $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['encoding'] = $this->request[static::REQUEST_ACCEPT_ENCODING]['data']['choices'];
      unset($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['choices']);
    }
    unset($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['invalid']);
    unset($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['current']);

    $this->request[static::REQUEST_ACCEPT_ENCODING]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-charset.
   *
   * @see: self::pr_rfc_string_is_negotiation()
   * @see: https://tools.ietf.org/html/rfc7231#section-5.3.3
   */
  private function p_load_request_accept_charset() {
    if (empty($this->headers['accept-charset'])) {
      $this->request[static::REQUEST_ACCEPT_CHARSET]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['accept-charset']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_ACCEPT_CHARSET]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_ACCEPT_CHARSET]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_ACCEPT_CHARSET]['data']['invalid']) {
      $this->request[static::REQUEST_ACCEPT_CHARSET]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_ACCEPT_CHARSET]['defined'] = TRUE;
      $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'] = array();

      // convert the known values into integers for improved processing.
      foreach ($this->request[static::REQUEST_ACCEPT_CHARSET]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $lowercase = c_base_utf8::s_lowercase($c['choice'])->get_value_exact();
          if ($lowercase == 'ascii') {
            $c['charset'] = c_base_charset::ASCII;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ASCII] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'utf-8') {
            $c['charset'] = c_base_charset::UTF_8;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::UTF_8] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'utf-16') {
            $c['charset'] = c_base_charset::UTF_16;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::UTF_16] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'utf-32') {
            $c['charset'] = c_base_charset::UTF_32;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::UTF_32] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-1') {
            $c['charset'] = c_base_charset::ISO_8859_1;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_1] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-2') {
            $c['charset'] = c_base_charset::ISO_8859_2;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_2] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-3') {
            $c['charset'] = c_base_charset::ISO_8859_3;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_3] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-4') {
            $c['charset'] = c_base_charset::ISO_8859_4;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_4] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-5') {
            $c['charset'] = c_base_charset::ISO_8859_5;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_5] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-6') {
            $c['charset'] = c_base_charset::ISO_8859_6;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_6] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-7') {
            $c['charset'] = c_base_charset::ISO_8859_7;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_7] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-8') {
            $c['charset'] = c_base_charset::ISO_8859_8;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_8] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-9') {
            $c['charset'] = c_base_charset::ISO_8859_9;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_9] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-10') {
            $c['charset'] = c_base_charset::ISO_8859_10;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_10] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-11') {
            $c['charset'] = c_base_charset::ISO_8859_11;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_11] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-12') {
            $c['charset'] = c_base_charset::ISO_8859_12;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_12] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-13') {
            $c['charset'] = c_base_charset::ISO_8859_13;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_13] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-14') {
            $c['charset'] = c_base_charset::ISO_8859_14;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_14] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-15') {
            $c['charset'] = c_base_charset::ISO_8859_15;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_15] = $lowercase;
            unset($c['choice']);
          }
          elseif ($lowercase == 'iso-8859-16') {
            $c['charset'] = c_base_charset::ISO_8859_16;
            $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight'][$weight][c_base_charset::ISO_8859_16] = $lowercase;
            unset($c['choice']);
          }
          else {
            $c['charset'] = NULL;
          }
        }
      }
      unset($lowercase);

      // sort the weight array.
      krsort($this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight']);

      // The NULL key should be the first key in the weight.
      $this->pr_prepend_array_value(NULL, $this->request[static::REQUEST_ACCEPT_CHARSET]['data']['weight']);
    }
    unset($this->request[static::REQUEST_ACCEPT_CHARSET]['data']['invalid']);

    $this->request[static::REQUEST_ACCEPT_CHARSET]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-datetime.
   *
   * This is not part of an official standard, it is provided to support a separate web archive standard.
   *
   * @see: http://www.mementoweb.org/guide/rfc/ID/#Accept-Memento-Datetime
   */
  private function p_load_request_accept_datetime() {
    if (p_validate_date_is_valid_rfc($this->headers['accept-datetime']) === FALSE) {
      $this->request[static::REQUEST_ACCEPT_DATETIME]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['accept-datetime']);
    if ($timestamp === FALSE) {
      $this->request[static::REQUEST_ACCEPT_DATETIME]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[static::REQUEST_ACCEPT_DATETIME]['defined'] = TRUE;
    $this->request[static::REQUEST_ACCEPT_DATETIME]['data'] = $timestamp;
    $this->request[static::REQUEST_ACCEPT_DATETIME]['invalid'] = FALSE;

    unset($timestamp);
  }

  /**
   * Load and process the HTTP request parameter: accept-datetime.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Request-Method
   */
  private function p_load_request_access_control_request_method() {
    $method_string = c_base_utf8::s_lowercase($this->headers['access-control-request-method'])->get_value_exact();
    $method_string = str_replace(' ', '', $method_string);

    $methods = explode(',', $method_string);
    unset($method_string);

    if (empty($methods)) {
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'] = array();

    foreach ($methods as $method) {
      switch ($method) {
        case 'get':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_GET] = static::HTTP_METHOD_GET;
          break;

        case 'head':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_HEAD] = static::HTTP_METHOD_HEAD;
          break;

        case 'post':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_POST] = static::HTTP_METHOD_POST;
          break;

        case 'put':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_PUT] = static::HTTP_METHOD_PUT;
          break;

        case 'delete':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_DELETE] = static::HTTP_METHOD_DELETE;
          break;

        case 'trace':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_TRACE] = static::HTTP_METHOD_TRACE;
          break;

        case 'options':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_OPTIONS] = static::HTTP_METHOD_OPTIONS;
          break;

        case 'connect':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_CONNECT] = static::HTTP_METHOD_CONNECT;
          break;

        case 'patch':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_PATCH] = static::HTTP_METHOD_PATCH;
          break;

        case 'track':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_TRACK] = static::HTTP_METHOD_TRACK;
          break;

        case 'debug':
          $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'][static::HTTP_METHOD_DEBUG] = static::HTTP_METHOD_DEBUG;
          break;

        default:
          // skip unknown methods instead of failing so that any discovered known methods are still loaded.
          break;
      }
    }
    unset($method);

    if (!empty($methods) && empty($this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['data'])) {
      unset($methods);

      // no valid methods were found, now the error can be reported.
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['invalid'] = TRUE;
      return;
    }
    unset($methods);

    $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['defined'] = TRUE;
    $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: accept-datetime.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Request-Headers
   */
  private function p_load_request_access_control_request_headers() {
    if (empty($this->headers['access-control-request-headers'])) {
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['access-control-request-headers']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data'] = $this->pr_rfc_string_is_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data']['invalid']) {
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['defined'] = TRUE;

      // rename 'tokens' array key to 'headers'.
      $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data']['headers'] = $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data']['tokens'];
      unset($this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data']['tokens']);
    }
    unset($this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data']['invalid']);
    unset($this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['data']['current']);

    $this->request[static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: authorization.
   *
   * 1*(tchar) 1*(wsp) 1*(tchar) *(wsp) "=" *(wsp) ( 1*(tchar) / quoted-string ) *( *(wsp) "," *(wsp) 1*(tchar) *(wsp) "=" *(wsp) ( 1*(tchar) / quoted-string ) ).
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.2
   */
  private function p_load_request_authorization() {
    if (empty($this->headers['authorization'])) {
      $this->request[static::REQUEST_AUTHORIZATION]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['authorization']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_AUTHORIZATION]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_AUTHORIZATION]['data'] = $this->pr_rfc_string_is_credentials($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_AUTHORIZATION]['data']['invalid']) {
      $this->request[static::REQUEST_AUTHORIZATION]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_AUTHORIZATION]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_AUTHORIZATION]['data']['invalid']);
    unset($this->request[static::REQUEST_AUTHORIZATION]['data']['current']);

    $this->request[static::REQUEST_AUTHORIZATION]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: cache-control.
   *
   * Only 'no-cache' is supported at this time for requests.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   */
  private function p_load_request_cache_control() {
    $cache_control = $this->headers['cache-control'];
    if (empty($cache_control)) {
      $this->request[static::REQUEST_CACHE_CONTROL]['invalid'] = TRUE;
      unset($cache_control);
      return;
    }

    $this->request[static::REQUEST_CACHE_CONTROL]['data'] = array(
      'methods' => array(),
    );

    $integer_types = array(
      SELF::CACHE_CONTROL_MAX_AGE => 'max-age=',
      SELF::CACHE_CONTROL_MAX_STALE => 'max-stale=',
      SELF::CACHE_CONTROL_MIN_FRESH => 'min-fresh=',
    );

    $parts = mb_split(', ', $cache_control);
    foreach ($parts as $part) {
      $cleaned_up = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $part))->get_value_exact();
      if ($cleaned_up == 'no-cache') {
        $this->request[static::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[static::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_NO_CACHE] = SELF::CACHE_CONTROL_NO_CACHE;
      }
      elseif ($cleaned_up == 'no-store') {
        $this->request[static::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[static::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_NO_STORE] = SELF::CACHE_CONTROL_NO_STORE;
      }
      elseif ($cleaned_up == 'no-transform') {
        $this->request[static::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[static::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_NO_TRANSFORM] = SELF::CACHE_CONTROL_NO_TRANSFORM;
      }
      elseif ($cleaned_up == 'only-if-cached') {
        $this->request[static::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
        $this->request[static::REQUEST_CACHE_CONTROL]['data']['methods'][SELF::CACHE_CONTROL_ONLY_IF_CACHED] = SELF::CACHE_CONTROL_ONLY_IF_CACHED;
      }
      else {
        foreach ($integer_types as $type_id => $type_string) {
          if (mb_strpos($cleaned_up, $type_string) === FALSE) {
            continue;
          }

          $pieces = mb_split('=', $cleaned_up);
          if (!isset($pieces[1]) || !is_numeric($pieces[1]) || count($pieces) > 2) {
            $this->request[static::REQUEST_CACHE_CONTROL]['invalid'] = TRUE;
            unset($pieces);
            continue;
          }

          $this->request[static::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
          $this->request[static::REQUEST_CACHE_CONTROL]['data']['methods'][$type_id] = (int) $pieces[1];

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

    $this->request[static::REQUEST_CACHE_CONTROL]['invalid'] = FALSE;
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
      $this->request[static::REQUEST_CONNECTION]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['connection']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_CONNECTION]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_CONNECTION]['data'] = $this->pr_rfc_string_is_commad_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_CONNECTION]['data']['invalid']) {
      $this->request[static::REQUEST_CONNECTION]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_CONNECTION]['defined'] = TRUE;

      // rename 'tokens' array key to 'connection'.
      $this->request[static::REQUEST_CONNECTION]['data']['connection'] = $this->request[static::REQUEST_CONNECTION]['data']['tokens'];
      unset($this->request[static::REQUEST_CONNECTION]['data']['text']);
    }
    unset($this->request[static::REQUEST_CONNECTION]['data']['invalid']);
    unset($this->request[static::REQUEST_CONNECTION]['data']['current']);

    $this->request[static::REQUEST_CONNECTION]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: pragma.
   *
   * This is an older version of cache_control that supports 'no-cache'.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.32
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  private function p_load_request_pragma() {
    if ($this->request[static::REQUEST_CACHE_CONTROL]['defined']) {
      // this is a conflict, favor 'cache-control' over 'pragma'.
      return;
    }

    $pragma = $this->headers['pragma'];
    if (empty($pragma)) {
      // silently fail on invalid pragma.
      unset($pragma);
      return;
    }

    $cleaned_up = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $pragma))->get_value_exact();
    if ($cleaned_up == 'no-cache') {
      $this->request[static::REQUEST_CACHE_CONTROL]['defined'] = TRUE;
      $this->request[static::REQUEST_CACHE_CONTROL]['data'][SELF::CACHE_CONTROL_NO_CACHE] = SELF::CACHE_CONTROL_NO_CACHE;
    }
    unset($cleaned_up);
    unset($pragma);

    $this->request[static::REQUEST_CACHE_CONTROL]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: cookie.
   *
   * @see: https://tools.ietf.org/html/rfc6265
   */
  private function p_load_request_cookies() {
    $this->request[static::REQUEST_COOKIE]['data'] = array();

    foreach ($_COOKIE as $cookie_name => $cookie_values) {
      $cookie = new c_base_cookie();
      $result = $cookie->set_name($cookie_name);

      if ($result instanceof c_base_return_false) {
        unset($cookie);
        unset($result);
        continue;
      }

      $cookie->do_pull();
      $this->request[static::REQUEST_COOKIE]['data'][$cookie_name] = $cookie;
      $this->request[static::REQUEST_COOKIE]['defined'] = TRUE;
      unset($cookie);
    }
    unset($cookie_name);
    unset($cookie_values);

    $this->request[static::REQUEST_COOKIE]['invalid'] = FALSE;
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
    if (is_int($this->headers['content-length'])) {
      $this->request[static::REQUEST_CONTENT_LENGTH]['defined'] = TRUE;
      $this->request[static::REQUEST_CONTENT_LENGTH]['data'] = (int) $this->headers['content-length'];
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['content-length']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_CONTENT_LENGTH]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $parsed = $this->pr_rfc_string_is_digit($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      $this->request[static::REQUEST_CONTENT_LENGTH]['invalid'] = TRUE;
      unset($parsed);
      return;
    }

    $this->request[static::REQUEST_CONTENT_LENGTH]['defined'] = TRUE;
    $this->request[static::REQUEST_CONTENT_LENGTH]['data'] = intval($parsed['text']);
    unset($parsed);

    $this->request[static::REQUEST_CONTENT_LENGTH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: content-type.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  private function p_load_request_content_type() {
    $content_type = $this->headers['content-type'];
    if (empty($content_type)) {
      $this->request[static::REQUEST_CONTENT_TYPE]['invalid'] = TRUE;
      unset($content_type);
      return;
    }

    $content_type_parts = mb_split(';', $content_type);
    $content_type_part = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $content_type_parts[0]))->get_value_exact();

    $this->request[static::REQUEST_CONTENT_TYPE]['defined'] = TRUE;
    $this->request[static::REQUEST_CONTENT_TYPE]['data'] = $content_type_part;

    unset($content_type_part);
    unset($content_type_parts);
    unset($content_type);

    $this->request[static::REQUEST_CONTENT_TYPE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: date.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.1.2
   */
  private function p_load_request_date() {
    if (p_validate_date_is_valid_rfc($this->headers['date']) === FALSE) {
      $this->request[static::REQUEST_DATE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['date']);
    if ($timestamp === FALSE) {
      $this->request[static::REQUEST_DATE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[static::REQUEST_DATE]['defined'] = TRUE;
    $this->request[static::REQUEST_DATE]['data'] = $timestamp;

    unset($timestamp);

    $this->request[static::REQUEST_DATE]['invalid'] = FALSE;
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
      $this->request[static::REQUEST_EXPECT]['invalid'] = TRUE;
      unset($expect);
      return;
    }

    $this->request[static::REQUEST_EXPECT]['defined'] = TRUE;
    $this->request[static::REQUEST_EXPECT]['data'] = $expect;

    unset($expect);

    $this->request[static::REQUEST_EXPECT]['invalid'] = FALSE;
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
      $this->request[static::REQUEST_FROM]['invalid'] = TRUE;
      return;
    }

    // @todo: write a custom validation to ensure that the from email address is valid.
    $this->request[static::REQUEST_FROM]['defined'] = TRUE;
    $this->request[static::REQUEST_FROM]['data'] = $this->headers['from'];

    unset($from);

    $this->request[static::REQUEST_FROM]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: host.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.1
   * @see: https://tools.ietf.org/html/rfc5322#section-3.4
   */
  private function p_load_request_host() {
    if (empty($this->headers['host'])) {
      $this->request[static::REQUEST_HOST]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['host']);
    if ($text['invalid']) {
      unset($text);

      $this->request[static::REQUEST_HOST]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_HOST]['data'] = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_HOST]['data']['invalid']) {
      $this->request[static::REQUEST_HOST]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_HOST]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_HOST]['data']['invalid']);

    $this->request[static::REQUEST_HOST]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-match.
   *
   * @see: https://tools.ietf.org/html/rfc7234
   * @see: https://tools.ietf.org/html/rfc7232#section-3.1
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  private function p_load_request_if_match() {
    if (empty($this->headers['if-match'])) {
      $this->request[static::REQUEST_IF_MATCH]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_IF_MATCH]['data'] = $this->p_parse_if_entity_tag($this->headers['if-match']);

    if ($this->request[static::REQUEST_IF_MATCH]['data']['invalid']) {
      $this->request[static::REQUEST_IF_MATCH]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_IF_MATCH]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_IF_MATCH]['data']['current']);
    unset($this->request[static::REQUEST_IF_MATCH]['data']['invalid']);

    $this->request[static::REQUEST_IF_MATCH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-none-match.
   *
   * @see: https://tools.ietf.org/html/rfc7234
   * @see: https://tools.ietf.org/html/rfc7232#section-3.2
   * @see: https://tools.ietf.org/html/rfc7232#section-2.3
   */
  private function p_load_request_if_none_match() {
    if (empty($this->headers['if-none-match'])) {
      $this->request[static::REQUEST_IF_NONE_MATCH]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_IF_NONE_MATCH]['data'] = $this->p_parse_if_entity_tag_and_weak($this->headers['if-none-match']);

    if ($this->request[static::REQUEST_IF_NONE_MATCH]['data']['invalid']) {
      $this->request[static::REQUEST_IF_NONE_MATCH]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_IF_NONE_MATCH]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_IF_NONE_MATCH]['data']['invalid']);

    $this->request[static::REQUEST_IF_NONE_MATCH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-modified-since.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-3.3
   */
  private function p_load_request_if_modified_since() {
    if ($this->p_validate_date_is_valid_rfc($this->headers['if-modified-since']) === FALSE) {
      $this->request[static::REQUEST_IF_MODIFIED_SINCE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['if-modified-since']);
    if ($timestamp === FALSE) {
      $this->request[static::REQUEST_IF_MODIFIED_SINCE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[static::REQUEST_IF_MODIFIED_SINCE]['defined'] = TRUE;
    $this->request[static::REQUEST_IF_MODIFIED_SINCE]['data'] = $timestamp;

    unset($timestamp);

    $this->request[static::REQUEST_IF_MODIFIED_SINCE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: if-unmodified-since.
   *
   * @see: https://tools.ietf.org/html/rfc7232#section-3.4
   */
  private function p_load_request_if_unmodified_since() {
    if (p_validate_date_is_valid_rfc($this->headers['if-unmodified-since']) === FALSE) {
      $this->request[static::REQUEST_IF_UNMODIFIED_SINCE]['invalid'] = TRUE;
      return;
    }

    $timestamp = strtotime($this->headers['if-unmodified-since']);
    if ($timestamp === FALSE) {
      $this->request[static::REQUEST_IF_UNMODIFIED_SINCE]['invalid'] = TRUE;
      unset($timestamp);
      return;
    }

    $this->request[static::REQUEST_IF_UNMODIFIED_SINCE]['defined'] = TRUE;
    $this->request[static::REQUEST_IF_UNMODIFIED_SINCE]['data'] = $timestamp;

    unset($timestamp);

    $this->request[static::REQUEST_IF_UNMODIFIED_SINCE]['invalid'] = FALSE;
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
    if (p_validate_date_is_valid_rfc($this->headers['if-range'])) {
      $timestamp = strtotime($this->headers['if-range']);
      if ($timestamp === FALSE) {
        $this->request[static::REQUEST_IF_RANGE]['invalid'] = TRUE;
        $this->request[static::REQUEST_IF_RANGE]['data'] = array(
          'is_date' => TRUE,
        );
        unset($timestamp);
        return;
      }

      $this->request[static::REQUEST_IF_RANGE]['defined'] = TRUE;
      $this->request[static::REQUEST_IF_RANGE]['data'] = array(
        'range' => $timestamp,
        'is_date' => TRUE,
      );

      unset($timestamp);
      return;
    }

    // at this point, assume the if-range is an entity tag.
    $if_range = $this->headers['if-range'];
    if (empty($if_range)) {
      $this->request[static::REQUEST_IF_RANGE]['if-range'] = TRUE;
      $this->request[static::REQUEST_IF_RANGE]['data']['is_date'] = FALSE;
      unset($if_range);
      return;
    }

    $this->request[static::REQUEST_IF_RANGE]['data'] = $this->p_parse_if_entity_tag_and_weak($if_range);
    $this->request[static::REQUEST_IF_RANGE]['data']['is_date'] = FALSE;

    if ($this->request[static::REQUEST_IF_RANGE]['data']['invalid']) {
      $this->request[static::REQUEST_IF_RANGE]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_IF_RANGE]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_IF_RANGE]['data']['invalid']);

    unset($if_range);

    $this->request[static::REQUEST_IF_RANGE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: range.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-3.1
   */
  private function p_load_request_range() {
    if (empty($this->headers['range'])) {
      $this->request[static::REQUEST_RANGE]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['range']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_RANGE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_RANGE]['data'] = $this->pr_rfc_string_is_range($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_RANGE]['data']['invalid']) {
      $this->request[static::REQUEST_RANGE]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_RANGE]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_RANGE]['data']['invalid']);

    $this->request[static::REQUEST_RANGE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: max-forwards.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.1.2
   */
  private function p_load_request_max_forwards() {
    if (is_int($this->headers['max-forwards'])) {
      $this->request[static::REQUEST_MAX_FORWARDS]['defined'] = TRUE;
      $this->request[static::REQUEST_MAX_FORWARDS]['data'] = (int) $this->headers['max-forwards'];
    }

    $text = $this->pr_rfc_string_prepare($this->headers['max-forwards']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_MAX_FORWARDS]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $parsed = $this->pr_rfc_string_is_digit($text['ordinals'], $text['characters']);
    unset($text);

    if ($parsed['invalid']) {
      $this->request[static::REQUEST_MAX_FORWARDS]['invalid'] = TRUE;
      unset($parsed);
      return;
    }

    $this->request[static::REQUEST_MAX_FORWARDS]['defined'] = TRUE;
    $this->request[static::REQUEST_MAX_FORWARDS]['data'] = intval($parsed['text']);

    unset($parsed);

    $this->request[static::REQUEST_MAX_FORWARDS]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: request method.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-5.1.1
   */
  private function p_load_request_method() {
    $this->request[static::REQUEST_METHOD]['defined'] = TRUE;

    $method_string = c_base_utf8::s_lowercase($_SERVER['REQUEST_METHOD'])->get_value_exact();
    if ($method_string == 'get') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_GET;
    }
    elseif ($method_string == 'head') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_HEAD;
    }
    elseif ($method_string == 'post') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_POST;
    }
    elseif ($method_string == 'put') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_PUT;
    }
    elseif ($method_string == 'delete') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_DELETE;
    }
    elseif ($method_string == 'trace') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_TRACE;
    }
    elseif ($method_string == 'options') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_OPTIONS;
    }
    elseif ($method_string == 'connect') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_CONNECT;
    }
    elseif ($method_string == 'patch') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_PATCH;
    }
    elseif ($method_string == 'track') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_TRACK;
    }
    elseif ($method_string == 'debug') {
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_DEBUG;
    }
    else {
      // use 'none' to represent all unknown methods.
      $this->request[static::REQUEST_METHOD]['data'] = static::HTTP_METHOD_NONE;
    }
    unset($method_string);

    $this->request[static::REQUEST_METHOD]['invalid'] = FALSE;
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
   * @see: https://www.w3.org/TR/CSP2/
   */
  private function p_load_request_origin() {
    if (empty($this->headers['origin'])) {
      $this->request[static::REQUEST_ORIGIN]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['origin']);
    if ($text['invalid']) {
      unset($text);

      $this->request[static::REQUEST_ORIGIN]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_ORIGIN]['data'] = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_ORIGIN]['data']['invalid']) {
      $this->request[static::REQUEST_ORIGIN]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_ORIGIN]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_ORIGIN]['data']['invalid']);

    $this->request[static::REQUEST_ORIGIN]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: proxy-authorization.
   *
   * 1*(tchar) 1*(wsp) 1*(tchar) *(wsp) "=" *(wsp) ( 1*(tchar) / quoted-string ) *( *(wsp) "," *(wsp) 1*(tchar) *(wsp) "=" *(wsp) ( 1*(tchar) / quoted-string ) ).
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.4
   */
  private function p_load_request_proxy_authorization() {
    if (empty($this->headers['proxy-authorization'])) {
      $this->request[static::REQUEST_PROXY_AUTHORIZATION]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['proxy-authorization']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_PROXY_AUTHORIZATION]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_PROXY_AUTHORIZATION]['data'] = $this->pr_rfc_string_is_credentials($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_PROXY_AUTHORIZATION]['data']['invalid']) {
      $this->request[static::REQUEST_PROXY_AUTHORIZATION]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_PROXY_AUTHORIZATION]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_PROXY_AUTHORIZATION]['data']['invalid']);
    unset($this->request[static::REQUEST_PROXY_AUTHORIZATION]['data']['current']);

    $this->request[static::REQUEST_PROXY_AUTHORIZATION]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: referer.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.2
   */
  private function p_load_request_referer() {
    if (empty($this->headers['referer'])) {
      $this->request[static::REQUEST_REFERER]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['referer']);
    if ($text['invalid']) {
      unset($text);

      $this->request[static::REQUEST_REFERER]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_REFERER]['data'] = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_REFERER]['data']['invalid']) {
      $this->request[static::REQUEST_REFERER]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_REFERER]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_REFERER]['data']['invalid']);

    $this->request[static::REQUEST_REFERER]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: te.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-4.3
   */
  private function p_load_request_te() {
    if (empty($this->headers['te'])) {
      $this->request[static::REQUEST_TE]['te'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['te']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_TE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_TE]['data'] = $this->pr_rfc_string_is_negotiation($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_TE]['data']['invalid']) {
      $this->request[static::REQUEST_TE]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_TE]['defined'] = TRUE;

      // convert the known values into integers for improved processing.
      foreach ($this->request[static::REQUEST_TE]['data']['choices'] as $weight => &$choice) {
        foreach ($choice as $key => &$c) {
          $lowercase = c_base_utf8::s_lowercase($c['choice'])->get_value_exact();
          if ($c['choice'] == 'compress') {
            $c['encoding'] = static::ENCODING_COMPRESS;
          }
          elseif ($c['choice'] == 'deflate') {
            $c['encoding'] = static::ENCODING_DEFLATE;
          }
          elseif ($c['choice'] == 'gzip') {
            $c['encoding'] = static::ENCODING_GZIP;
          }
          elseif ($c['choice'] == 'bzip') {
            $c['encoding'] = static::ENCODING_BZIP;
          }
          elseif ($c['choice'] == 'lzo') {
            $c['encoding'] = static::ENCODING_LZO;
          }
          elseif ($c['choice'] == 'xz') {
            $c['encoding'] = static::ENCODING_XZ;
          }
          elseif ($c['choice'] == 'exit') {
            $c['encoding'] = static::ENCODING_EXI;
          }
          elseif ($c['choice'] == 'identity') {
            $c['encoding'] = static::ENCODING_IDENTITY;
          }
          elseif ($c['choice'] == 'sdch') {
            $c['encoding'] = static::ENCODING_SDCH;
          }
          elseif ($c['choice'] == 'pg') {
            $c['encoding'] = static::ENCODING_PG;
          }
          else {
            $c['encoding'] = NULL;
          }
        }
      }
      unset($lowercase);
    }
    unset($this->request[static::REQUEST_TE]['data']['invalid']);

    $this->request[static::REQUEST_TE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: user-agent.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_user_agent() {
    if (empty($this->headers['user-agent'])) {
      $this->request[static::REQUEST_USER_AGENT]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['user-agent']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_USER_AGENT]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // make sure agent is valid text.
    $agent = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($agent['invalid']) {
      $this->request[static::REQUEST_USER_AGENT]['invalid'] = TRUE;
      unset($agent);
      return;
    }

    $this->request[static::REQUEST_USER_AGENT]['data'] = $this->p_parse_user_agent($agent['text']);
    unset($agent);

    if ($this->request[static::REQUEST_USER_AGENT]['data']['invalid']) {
      $this->request[static::REQUEST_USER_AGENT]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_USER_AGENT]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_USER_AGENT]['data']['invalid']);

    $this->request[static::REQUEST_USER_AGENT]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: upgrade.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_upgrade() {
    if (empty($this->headers['upgrade'])) {
      $this->request[static::REQUEST_UPGRADE]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['upgrade']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_UPGRADE]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // @todo: future versions may do something with this, until then just check for text.
    $this->request[static::REQUEST_UPGRADE]['data'] = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_UPGRADE]['data']['invalid']) {
      $this->request[static::REQUEST_UPGRADE]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_UPGRADE]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_UPGRADE]['data']['invalid']);

    $this->request[static::REQUEST_UPGRADE]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: uri.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_uri() {
    if (empty($this->headers['uri'])) {
      $this->request[static::REQUEST_URI]['invalid'] = TRUE;
      return;
    }

    // attempt to reconstruct the uri as a full url, if possible.
    $uri = $this->headers['uri'];

    if (isset($_SERVER['SERVER_NAME']) && is_string($_SERVER['SERVER_NAME']) && mb_strlen($_SERVER['SERVER_NAME']) > 0) {
      if (isset($_SERVER['HTTPS'])) {
        $uri = 'https://' . $_SERVER['SERVER_NAME'] . $uri;
      }
      else {
        $uri = 'http://' . $_SERVER['SERVER_NAME'] . $uri;
      }
    }

    $text = $this->pr_rfc_string_prepare($uri);
    unset($uri);

    if ($text['invalid']) {
      $this->request[static::REQUEST_URI]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_URI]['data'] = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_URI]['data']['invalid']) {
      $this->request[static::REQUEST_URI]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_URI]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_URI]['data']['invalid']);
    unset($this->request[static::REQUEST_URI]['data']['current']);

    $this->request[static::REQUEST_URI]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: via.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_load_request_via() {
    if (empty($this->headers['via'])) {
      $this->request[static::REQUEST_VIA]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['via']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_VIA]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // @todo: future versions may do something with this, until then just check for text.
    $this->request[static::REQUEST_VIA]['data'] = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_VIA]['data']['invalid']) {
      $this->request[static::REQUEST_VIA]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_VIA]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_VIA]['data']['invalid']);

    $this->request[static::REQUEST_VIA]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: warning.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.5
   */
  private function p_load_request_warning() {
    if (empty($this->headers['warning'])) {
      $this->request[static::REQUEST_WARNING]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['warning']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_WARNING]['invalid'] = TRUE;
      unset($text);
      return;
    }

    // @todo: future versions may do something with this, until then just check for text.
    $this->request[static::REQUEST_WARNING]['data'] = $this->pr_rfc_string_is_basic($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_WARNING]['data']['invalid']) {
      $this->request[static::REQUEST_WARNING]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_WARNING]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_WARNING]['data']['invalid']);

    $this->request[static::REQUEST_WARNING]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: x-requested-with.
   *
   * I am just assuming the syntax to be 1*(tchar).
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_load_request_x_requested_with() {
    if (empty($this->headers['x-requested-with'])) {
      $this->request[static::REQUEST_X_REQUESTED_WITH]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['x-requested-with']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_WITH]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_X_REQUESTED_WITH]['data'] = $this->pr_rfc_string_is_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_X_REQUESTED_WITH]['data']['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_WITH]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_X_REQUESTED_WITH]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_X_REQUESTED_WITH]['data']['invalid']);

    $this->request[static::REQUEST_X_REQUESTED_WITH]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: x-requested-for.
   *
   * This could be a name or an ip address.
   * I am just using 1*(tchar) because that also allows ip addresses.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_load_request_x_requested_for() {
    if (empty($this->headers['x-requested-for'])) {
      $this->request[static::REQUEST_X_REQUESTED_FOR]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['x-requested-for']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_FOR]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_X_REQUESTED_HOST]['data'] = $this->pr_rfc_string_is_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_X_REQUESTED_HOST]['data']['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_HOST]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_X_REQUESTED_HOST]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_X_REQUESTED_HOST]['data']['invalid']);
    unset($this->request[static::REQUEST_X_REQUESTED_HOST]['data']['uri']);

    $this->request[static::REQUEST_X_REQUESTED_HOST]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: x-requested-host.
   *
   * I am just assuming the syntax to be (url).
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_load_request_x_requested_host() {
    if (empty($this->headers['x-requested-host'])) {
      $this->request[static::REQUEST_X_REQUESTED_HOST]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['x-requested-host']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_HOST]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_X_REQUESTED_HOST]['data'] = $this->pr_rfc_string_is_uri($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_X_REQUESTED_HOST]['data']['invalid'] || $this->request[static::REQUEST_X_REQUESTED_HOST]['data']['uri'] === FALSE) {
      $this->request[static::REQUEST_X_REQUESTED_HOST]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_X_REQUESTED_HOST]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_X_REQUESTED_HOST]['data']['invalid']);
    unset($this->request[static::REQUEST_X_REQUESTED_HOST]['data']['uri']);

    $this->request[static::REQUEST_X_REQUESTED_HOST]['invalid'] = FALSE;
  }

  /**
   * Load and process the HTTP request parameter: x-requested-proto.
   *
   * I am just assuming the syntax to be 1*(tchar).
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_load_request_x_requested_proto() {
    if (empty($this->headers['x-requested-proto'])) {
      $this->request[static::REQUEST_X_REQUESTED_PROTO]['invalid'] = TRUE;
      return;
    }

    $text = $this->pr_rfc_string_prepare($this->headers['x-requested-proto']);
    if ($text['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_PROTO]['invalid'] = TRUE;
      unset($text);
      return;
    }

    $this->request[static::REQUEST_X_REQUESTED_PROTO]['data'] = $this->pr_rfc_string_is_token($text['ordinals'], $text['characters']);
    unset($text);

    if ($this->request[static::REQUEST_X_REQUESTED_PROTO]['data']['invalid']) {
      $this->request[static::REQUEST_X_REQUESTED_PROTO]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_X_REQUESTED_PROTO]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_X_REQUESTED_PROTO]['data']['invalid']);

    $this->request[static::REQUEST_X_REQUESTED_PROTO]['invalid'] = FALSE;
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
    // this requires checksum_headers to be defined.
    if (empty($this->headers['checksum_header'])) {
      $this->request[static::REQUEST_CHECKSUM_HEADER]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_CHECKSUM_HEADER]['data'] = $this->p_parse_checksum($this->headers['checksum_header']);

    if ($this->request[static::REQUEST_CHECKSUM_HEADER]['data']['invalid']) {
      $this->request[static::REQUEST_CHECKSUM_HEADER]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_CHECKSUM_HEADER]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_CHECKSUM_HEADER]['data']['invalid']);

    $this->request[static::REQUEST_CHECKSUM_HEADER]['invalid'] = FALSE;
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
    // this requires checksum_header to be defined.
    if (empty($this->headers['checksum_headers'])) {
      $this->request[static::REQUEST_CHECKSUM_HEADERS]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_CHECKSUM_HEADERS]['data'] = $this->p_parse_checksum_headers($this->headers['checksum_headers']);

    if ($this->request[static::REQUEST_CHECKSUM_HEADERS]['data']['invalid']) {
      $this->request[static::REQUEST_CHECKSUM_HEADERS]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_CHECKSUM_HEADERS]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_CHECKSUM_HEADERS]['data']['invalid']);

    $this->request[static::REQUEST_CHECKSUM_HEADERS]['invalid'] = FALSE;
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
      $this->request[static::REQUEST_CHECKSUM_CONTENT]['invalid'] = TRUE;
      return;
    }

    $this->request[static::REQUEST_CHECKSUM_CONTENT]['data'] = $this->p_parse_checksum($this->headers['checksum_content']);

    if ($this->request[static::REQUEST_CHECKSUM_CONTENT]['data']['invalid']) {
      $this->request[static::REQUEST_CHECKSUM_CONTENT]['invalid'] = TRUE;
    }
    else {
      $this->request[static::REQUEST_CHECKSUM_CONTENT]['defined'] = TRUE;
    }
    unset($this->request[static::REQUEST_CHECKSUM_CONTENT]['data']['invalid']);

    $this->request[static::REQUEST_CHECKSUM_CONTENT]['invalid'] = FALSE;
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

    $this->request[$key]['data'] = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $raw))->get_value_exact();
    if (!is_null($max_length)) {
      $this->request[$key]['data'] = c_base_utf8::s_substring($this->request[$key]['data'], 0, $max_length)->get_value_exact();
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


    $this->request[$key]['data'][$field] = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $raw))->get_value_exact();
    if (!is_null($max_length)) {
      $this->request[$key]['data'][$field] = c_base_utf8::s_substring($this->request[$key]['data'][$field], 0, $max_length)->get_value_exact();
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

    $raw = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $original))->get_value_exact();

    // rfc5322 is the preferred/recommended format.
    $rfc5322 = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', c_base_defaults_global::s_get_date(static::TIMESTAMP_RFC_5322, $timestamp)->get_value_exact()))->get_value_exact();
    if ($raw == $rfc5322) {
      unset($raw);
      unset($timestamp);
      unset($rfc5322);
      return TRUE;
    }
    unset($rfc5322);

    $rfc1123 = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', c_base_defaults_global::s_get_date(static::TIMESTAMP_RFC_1123, $timestamp)->get_value_exact()))->get_value_exact();
    if ($raw == $rfc1123) {
      unset($raw);
      unset($timestamp);
      unset($rfc1123);
      return TRUE;
    }
    unset($rfc1123);

    $rfc850 = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', c_base_defaults_global::s_get_date(static::TIMESTAMP_RFC_850, $timestamp)->get_value_exact()))->get_value_exact();
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
    $parts_sub = mb_split(static::DELIMITER_ACCEPT_SUB, $super);

    $part_sub_value = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $parts_sub[0]))->get_value_exact();
    $part_sub_priority = NULL;
    if (count($parts_sub) > 1) {
      $part_sub_priority = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $parts_sub[1]))->get_value_exact();
    }

    if (self::p_length_string($part_sub_value) > 2) {
      if ($part_sub_value[0] == static::DELIMITER_ACCEPT_SUB_0 && $part_sub_value[1] == static::DELIMITER_ACCEPT_SUB_1) {
        $value = $part_sub_priority;
        $parts_sub_priority = $part_sub_value;
      }
    }
    else {
      $value = $part_sub_value;
    }
    unset($part_sub_value);

    if (!is_null($part_sub_priority) && self::p_length_string($part_sub_priority) > 2 && $part_sub_priority == 'q' && $part_sub_priority == '=') {
      $part = preg_replace('/(^\s+)|(\s+$)/us', '', str_replace(static::DELIMITER_ACCEPT_SUB_0 . static::DELIMITER_ACCEPT_SUB_1, '', $part_sub_priority));

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
      $parsed = $this->pr_rfc_string_is_quoted_string($text['ordinals'], $text['characters'], $current, static::STOP_AT_CLOSING_CHARACTER);
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

    $stop = self::p_length_string($match);
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
      if ($text['ordinals'][$current] == c_base_ascii::UPPER_W) {
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
        $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', c_base_utf8::s_lowercase($pieces[0])->get_value_exact());
        $lower_piece_2 = preg_replace('/(^\s+)|(\s+$)/us', '', c_base_utf8::s_lowercase($pieces[1])->get_value_exact());

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
        $lower_cased = c_base_utf8::s_lowercase($pieces[0])->get_value_exact();
        $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', $lower_cased);
        unset($lower_cased);

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
            $lower_cased = c_base_utf8::s_lowercase($pieces[0])->get_value_exact();
            $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', $lower_cased);
            unset($lower_cased);

            $lower_cased = c_base_utf8::s_lowercase($pieces[1])->get_value_exact();
            $lower_piece_2 = preg_replace('/(^\s+)|(\s+$)/us', '', $lower_cased);
            unset($lower_cased);

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
            $lower_cased = c_base_utf8::s_lowercase($pieces[0])->get_value_exact();
            $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', $lower_cased);
            unset($lower_cased);

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
          $lower_piece_1 = preg_replace('/(^\s+)|(\s+$)/us', '', c_base_utf8::s_lowercase($pieces[0])->get_value_exact());
          $lower_piece_2 = preg_replace('/(^\s+)|(\s+$)/us', '', c_base_utf8::s_lowercase($pieces[1])->get_value_exact());

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
          $lower_piece = preg_replace('/(^\s+)|(\s+$)/us', '', c_base_utf8::s_lowercase($pieces[0])->get_value_exact());

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

    $fixed_checksum = c_base_utf8::s_lowercase(preg_replace('/(^\s+)|(\s+$)/us', '', $checksum))->get_value_exact();
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
      $result['what'] = static::CHECKSUM_WHAT_FULL;
    }
    elseif ($parsed['text'] == 'complete') {
      $result['what'] = static::CHECKSUM_WHAT_COMPLETE;
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
      $result['type'] = static::CHECKSUM_MD2;
    }
    elseif ($parsed['text'] == 'md4') {
      $result['type'] = static::CHECKSUM_MD4;
    }
    elseif ($parsed['text'] == 'md5') {
      $result['type'] = static::CHECKSUM_MD5;
    }
    elseif ($parsed['text'] == 'sha1') {
      $result['type'] = static::CHECKSUM_SHA1;
    }
    elseif ($parsed['text'] == 'sha224') {
      $result['type'] = static::CHECKSUM_SHA224;
    }
    elseif ($parsed['text'] == 'sha256') {
      $result['type'] = static::CHECKSUM_SHA256;
    }
    elseif ($parsed['text'] == 'sha384') {
      $result['type'] = static::CHECKSUM_SHA384;
    }
    elseif ($parsed['text'] == 'sha512') {
      $result['type'] = static::CHECKSUM_SHA512;
    }
    elseif ($parsed['text'] == 'crc32') {
      $result['type'] = static::CHECKSUM_CRC32;
    }
    elseif ($parsed['text'] == 'crc32b') {
      $result['type'] = static::CHECKSUM_CRC32B;
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

    $fixed_checksum = c_base_utf8::s_lowercase(preg_replace("/(^( |\t)+)|(( |\t)+$)/us", '', $checksum_headers))->get_value_exact();
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
    return c_base_utf8::s_length_string($string)->get_value_exact();
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
   * For nginx, be sure to set 'underscores_in_headers' to 'on'.
   *
   * @fixme: do something about converting '_' to '-'.
   * look under sapi/cgi/cgi_main.c, for function add_request_header().
   * - remove the underscore conversions!
   * - there is also an apache_request_headers in this file, apply same fixes!
   *
   * @fixme: $_SERVER does not contain all headers (only well-known)!
   *
   * Custom headers may have to be handled outside of PHP such as via apache or nginx.
   * - With apache, the following might work: RewriteRule .* - [E=HTTP_EXAMPLE_HEADER:%{HTTP:Example_Header}]
   *   - This will then appear in $_SERVER.
   */
  private function p_get_all_headers() {
    $this->headers = array();

    // this works with apache.
    if (function_exists('getallheaders')) {
      $all_headers = getallheaders();

      foreach ($all_headers as $key => $value) {
        $lowered = c_base_utf8::s_lowercase($key)->get_value_exact();

        $this->headers[$lowered] = $value;
        unset($lowered);
      }
      unset($key);
      unset($value);
    }
    else {
      // non-apache, or calling php from command line.
      if (isset($_SERVER) && is_array($_SERVER) && !empty($_SERVER)) {
        foreach ($_SERVER as $key => $value) {
          $part = c_base_utf8::s_lowercase(c_base_utf8::s_substring($key, 0, 5))->get_value_exact();

          if ($part != 'http_') {
            continue;
          }

          $part = c_base_utf8::s_lowercase(c_base_utf8::s_substring($key, 5)->get_value_exact())->get_value_exact();
          $this->headers[$part] = $value;
        }
        unset($part);
        unset($key);
        unset($value);
      }

      if (isset($_SERVER['CONTENT_TYPE'])) {
        $this->headers['content-type'] = $_SERVER['CONTENT_TYPE'];
      }

      if (isset($_SERVER['CONTENT_LENGTH'])) {
        $this->headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
      }
    }

    if (!array_key_exists('uri', $this->headers)) {
      $this->headers['uri'] = '';

      if (isset($_SERVER['REQUEST_URI'])) {
        $this->headers['uri'] = $_SERVER['REQUEST_URI'];
      }
    }

    $timestamp = c_base_defaults_global::s_get_timestamp_session();
    if (!$timestamp->has_error()) {
      $this->request_time = $timestamp->get_value_exact();
    }
    unset($timestamp);
  }

  /**
   * Return an array for mapping HTTP request header strings to header ids.
   *
   * @return array
   *   An array for mapping HTTP request header strings to header ids.
   */
  private function p_get_header_request_mapping() {
    return array(
      'accept' => static::REQUEST_ACCEPT,
      'accept-charset' => static::REQUEST_ACCEPT_CHARSET,
      'accept-encoding' => static::REQUEST_ACCEPT_ENCODING,
      'accept-language' => static::REQUEST_ACCEPT_LANGUAGE,
      'accept-datetime' => static::REQUEST_ACCEPT_DATETIME,
      'access-control-request-method' => static::REQUEST_ACCESS_CONTROL_REQUEST_METHOD,
      'access-control-request-headers' => static::REQUEST_ACCESS_CONTROL_REQUEST_HEADERS,
      'authorization' => static::REQUEST_AUTHORIZATION,
      'cache-control' => static::REQUEST_CACHE_CONTROL,
      'connection' => static::REQUEST_CONNECTION,
      'cookie' => static::REQUEST_COOKIE,
      'content-length' => static::REQUEST_CONTENT_LENGTH,
      'content-type' => static::REQUEST_CONTENT_TYPE,
      'date' => static::REQUEST_DATE,
      'expect' => static::REQUEST_EXPECT,
      'from' => static::REQUEST_FROM,
      'host' => static::REQUEST_HOST,
      'if-match' => static::REQUEST_IF_MATCH,
      'if-modified-since' => static::REQUEST_IF_MODIFIED_SINCE,
      'if-none-match' => static::REQUEST_IF_NONE_MATCH,
      'if-range' => static::REQUEST_IF_RANGE,
      'if-unmodified-since' => static::REQUEST_IF_UNMODIFIED_SINCE,
      'max-forwards' => static::REQUEST_MAX_FORWARDS,
      'origin' => static::REQUEST_ORIGIN,
      'pragma' => static::REQUEST_PRAGMA,
      'proxy-authorization' => static::REQUEST_PROXY_AUTHORIZATION,
      'request-range' => static::REQUEST_RANGE,
      'referer' => static::REQUEST_REFERER,
      'te' => static::REQUEST_TE,
      'user-agent' => static::REQUEST_USER_AGENT,
      'upgrade' => static::REQUEST_UPGRADE,
      'via' => static::REQUEST_VIA,
      'warning' => static::REQUEST_WARNING,
      'x-requested-with' => static::REQUEST_X_REQUESTED_WITH,
      'x-forwarded-for' => static::REQUEST_X_FORWARDED_FOR,
      'x-forwarded-host' => static::REQUEST_X_FORWARDED_HOST,
      'x-forwarded-proto' => static::REQUEST_X_FORWARDED_PROTO,
      'checksum_header' => static::REQUEST_CHECKSUM_HEADER,
      'checksum_headers' => static::REQUEST_CHECKSUM_HEADERS,
      'checksum_content' => static::REQUEST_CHECKSUM_CONTENT,
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
        static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN => 'Access-Control-Allow-Origin',
        static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS => 'Access-Control-Allow-Credentials',
        static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS => 'Access-Control-Expose-Headers',
        static::RESPONSE_ACCESS_CONTROL_MAX_AGE => 'Access-Control-Max-Age',
        static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS => 'Access-Control-Allow-Methods',
        static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS => 'Access-Control-Allow-Headers',
        static::RESPONSE_ACCEPT_PATCH => 'Accept-Patch',
        static::RESPONSE_ACCEPT_RANGES => 'Accept-Ranges',
        static::RESPONSE_AGE => 'Age',
        static::RESPONSE_ALLOW => 'Allow',
        static::RESPONSE_CACHE_CONTROL => 'Cache-Control',
        static::RESPONSE_CHECKSUM_CONTENT => 'Checksum_Content',
        static::RESPONSE_CHECKSUM_HEADER => 'Checksum_Header',
        static::RESPONSE_CHECKSUM_HEADERS => 'Checksum_Headers',
        static::RESPONSE_CONNECTION => 'Connection',
        static::RESPONSE_CONTENT_DISPOSITION => 'Content-Disposition',
        static::RESPONSE_CONTENT_ENCODING => 'Content-Encoding',
        static::RESPONSE_CONTENT_LANGUAGE => 'Content-Language',
        static::RESPONSE_CONTENT_LENGTH => 'Content-Length',
        static::RESPONSE_CONTENT_LOCATION => 'Content-Location',
        static::RESPONSE_CONTENT_RANGE => 'Content-Range',
        static::RESPONSE_CONTENT_REVISION => 'Content_Revision',
        static::RESPONSE_CONTENT_SECURITY_POLICY => 'Content-Security-Policy',
        static::RESPONSE_CONTENT_TYPE => 'Content-Type',
        static::RESPONSE_DATE => 'Date',
        static::RESPONSE_DATE_ACTUAL => 'Date_Actual',
        static::RESPONSE_ETAG => 'Etag',
        static::RESPONSE_EXPIRES => 'Expires',
        static::RESPONSE_LAST_MODIFIED => 'Last-Modified',
        static::RESPONSE_LINK => 'Link',
        static::RESPONSE_LOCATION => 'Location',
        static::RESPONSE_PRAGMA => 'Pragma',
        static::RESPONSE_PROXY_AUTHENTICATE => 'Proxy-Authenticate',
        static::RESPONSE_PUBLIC_KEY_PINS => 'Public-Key-Pins',
        static::RESPONSE_REFRESH => 'Refresh',
        static::RESPONSE_RETRY_AFTER => 'Retry-After',
        static::RESPONSE_SERVER => 'Server',
        static::RESPONSE_SET_COOKIE => 'Set-Cookie',
        static::RESPONSE_STATUS => 'Status',
        static::RESPONSE_STRICT_TRANSPORT_SECURITY => 'Strict-Transport-Security',
        static::RESPONSE_TRAILER => 'Trailer',
        static::RESPONSE_TRANSFER_ENCODING => 'Transfer-Encoding',
        static::RESPONSE_UPGRADE => 'Upgrade',
        static::RESPONSE_VARY => 'Vary',
        static::RESPONSE_WARNING => 'Warning',
        static::RESPONSE_WWW_AUTHENTICATE => 'Www-Authenticate',
        static::RESPONSE_X_CONTENT_TYPE_OPTIONS => 'X-Content-Type-Options',
        static::RESPONSE_X_UA_COMPATIBLE => 'X-UA-Compatible',
      );
    }

    return array(
      static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN => 'access-control-allow-origin',
      static::RESPONSE_ACCESS_CONTROL_ALLOW_CREDENTIALS => 'access-control-allow-credentials',
      static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS => 'access-control-expose-headers',
      static::RESPONSE_ACCESS_CONTROL_MAX_AGE => 'access-control-max-age',
      static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS => 'access-control-allow-methods',
      static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS => 'access-control-allow-headers',
      static::RESPONSE_ACCEPT_PATCH => 'accept-patch',
      static::RESPONSE_ACCEPT_RANGES => 'accept-ranges',
      static::RESPONSE_AGE => 'age',
      static::RESPONSE_ALLOW => 'allow',
      static::RESPONSE_CACHE_CONTROL => 'cache-control',
      static::RESPONSE_CHECKSUM_CONTENT => 'checksum_content',
      static::RESPONSE_CHECKSUM_HEADER => 'checksum_header',
      static::RESPONSE_CHECKSUM_HEADERS => 'checksum_headers',
      static::RESPONSE_CONNECTION => 'connection',
      static::RESPONSE_CONTENT_DISPOSITION => 'content-disposition',
      static::RESPONSE_CONTENT_ENCODING => 'content-encoding',
      static::RESPONSE_CONTENT_LANGUAGE => 'content-language',
      static::RESPONSE_CONTENT_LENGTH => 'content-length',
      static::RESPONSE_CONTENT_LOCATION => 'content-location',
      static::RESPONSE_CONTENT_RANGE => 'content-range',
      static::RESPONSE_CONTENT_REVISION => 'content_revision',
      static::RESPONSE_CONTENT_SECURITY_POLICY => 'x-content-security-policy',
      static::RESPONSE_CONTENT_TYPE => 'content-type',
      static::RESPONSE_DATE => 'date',
      static::RESPONSE_DATE_ACTUAL => 'date_actual',
      static::RESPONSE_ETAG => 'etag',
      static::RESPONSE_EXPIRES => 'expires',
      static::RESPONSE_LAST_MODIFIED => 'last-modified',
      static::RESPONSE_LINK => 'link',
      static::RESPONSE_LOCATION => 'location',
      static::RESPONSE_PRAGMA => 'pragma',
      static::RESPONSE_PROXY_AUTHENTICATE => 'proxy-authenticate',
      static::RESPONSE_PUBLIC_KEY_PINS => 'public-key-pins',
      static::RESPONSE_REFRESH => 'refresh',
      static::RESPONSE_RETRY_AFTER => 'retry-after',
      static::RESPONSE_SERVER => 'server',
      static::RESPONSE_SET_COOKIE => 'set-cookie',
      static::RESPONSE_STATUS => 'status',
      static::RESPONSE_STRICT_TRANSPORT_SECURITY => 'strict-transport-security',
      static::RESPONSE_TRAILER => 'trailer',
      static::RESPONSE_TRANSFER_ENCODING => 'transfer-encoding',
      static::RESPONSE_UPGRADE => 'upgrade',
      static::RESPONSE_VARY => 'vary',
      static::RESPONSE_WARNING => 'warning',
      static::RESPONSE_WWW_AUTHENTICATE => 'www-authenticate',
      static::RESPONSE_X_CONTENT_TYPE_OPTIONS => 'x-content-type-options',
      static::RESPONSE_X_UA_COMPATIBLE => 'x-ua-compatible',
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
      'get' => static::HTTP_METHOD_GET,
      'head' => static::HTTP_METHOD_HEAD,
      'post' => static::HTTP_METHOD_POST,
      'put' => static::HTTP_METHOD_PUT,
      'delete' => static::HTTP_METHOD_DELETE,
      'trace' => static::HTTP_METHOD_TRACE,
      'options' => static::HTTP_METHOD_OPTIONS,
      'connect' => static::HTTP_METHOD_CONNECT,
      'patch' => static::HTTP_METHOD_PATCH,
      'track' => static::HTTP_METHOD_TRACK,
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
   * @param bool $lower_case
   *   (optional) Force token to be lower-case.
   *   There are some cases where the token case may need to remain untouched.
   *   In such cases, set this to FALSE.
   *
   * @return string|bool
   *   A sanitized string is return on success.
   *   FALSE is returned on error or if the header name is invalid.
   */
  private function p_prepare_token($token_name, $lower_case = TRUE) {
    if ($lower_case) {
      $lower_cased = c_base_utf8::s_lowercase($token_name)->get_value_exact();
      $trimmed = preg_replace('/(^\s+)|(\s+$)/us', '', $lower_cased);
      unset($lower_cased);

      if ($trimmed === FALSE) {
        unset($trimmed);
        return FALSE;
      }
    }
    else {
      $trimmed = preg_replace('/(^\s+)|(\s+$)/us', '', $token_name);
      if ($trimmed === FALSE) {
        unset($trimmed);
        return FALSE;
      }
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
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Origin
   */
  private function p_prepare_header_response_access_control_allow_origin($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN, $this->response)) {
      return;
    }

    if ($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN]['wildcard']) {
      $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN] = $header_name . static::SEPARATOR_HEADER_NAME . '*';
      return;
    }

    $combined = pr_rfc_string_combine_uri_array($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN]);
    if (is_string($combined)) {
      $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_ORIGIN] = $header_name . static::SEPARATOR_HEADER_NAME . $combined;
    }
    unset($combined);
  }

  /**
   * Prepare HTTP response header: access-control-expose-headers.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Expose-Headers
   */
  private function p_prepare_header_response_access_control_expose_headers($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS] = $header_name . static::SEPARATOR_HEADER_NAME;

    if (!empty($this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS])) {
      $exposed_headers_array = $this->response[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS];

      reset($exposed_headers_array);
      $exposed_header_name = array_shift($exposed_headers_array);
      $header_output[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS] .= $exposed_header_name;

      foreach ($exposed_headers_array as $exposed_header_name) {
        $header_output[static::RESPONSE_ACCESS_CONTROL_EXPOSE_HEADERS] .= ', ' . $exposed_header_name;
      }
      unset($exposed_headers_array);
      unset($exposed_header_name);
    }
  }

  /**
   * Prepare HTTP response header: access-control-allow-methods.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Methods
   */
  private function p_prepare_header_response_access_control_allow_methods($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] = $header_name . static::SEPARATOR_HEADER_NAME;

    $allow_methods_array = $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS];

    reset($allow_methods_array);
    $methods = array_shift($allow_methods_array);
    $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= $methods;

    foreach ($allow_methods_array as $methods) {
      $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= ', ' ;

      switch ($methods) {
        case static::HTTP_METHOD_HEAD:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'HEAD';
          break;

        case static::HTTP_METHOD_POST:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'POST';
          break;

        case static::HTTP_METHOD_PUT:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'PUT';
          break;

        case static::HTTP_METHOD_DELETE:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'DELETE';
          break;

        case static::HTTP_METHOD_TRACE:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'TRACE';
          break;

        case static::HTTP_METHOD_OPTIONS:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'OPTIONS';
          break;

        case static::HTTP_METHOD_CONNECT:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'CONNECT';
          break;

        case static::HTTP_METHOD_PATCH:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'PATCH';
          break;

        case static::HTTP_METHOD_TRACK:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'TRACK';
          break;

        case static::HTTP_METHOD_DEBUG:
          $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= 'DEBUG';
          break;

      }
      $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_METHODS] .= ', ' . $methods;
    }
    unset($allow_methods_array);
    unset($methods);

  }

  /**
   * Prepare HTTP response header: access-control-allow-headers.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Headers
   */
  private function p_prepare_header_response_access_control_allow_headers($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS] = $header_name . static::SEPARATOR_HEADER_NAME;

    if (empty($this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS])) {
      return;
    }

    $allowed_headers_array = $this->response[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS];

    reset($allowed_headers_array);
    $allowed_header_name = array_shift($allowed_headers_array);
    $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS] .= $allowed_header_name;

    foreach ($allowed_headers_array as $allowed_header_name) {
      $header_output[static::RESPONSE_ACCESS_CONTROL_ALLOW_HEADERS] .= ', ' . $allowed_header_name;
    }
    unset($allowed_headers_array);
    unset($allowed_header_name);
  }

  /**
   * Prepare HTTP response header: accept-patch.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: self::pr_rfc_string_is_media_type()
   * @see: https://tools.ietf.org/html/rfc5789#section-3.1
   * @see: https://tools.ietf.org/html/rfc2616#section-3.7
   * @see: https://tools.ietf.org/html/rfc2616#section-3.12
   */
  private function p_prepare_header_response_accept_patch($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ACCEPT_PATCH, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_ACCEPT_PATCH] = $header_name . static::SEPARATOR_HEADER_NAME;

    if (!empty($this->response[static::RESPONSE_ACCEPT_PATCH])) {
      foreach ($this->response[static::RESPONSE_ACCEPT_PATCH] as $media_type) {
        $header_output[static::RESPONSE_ACCEPT_PATCH] .= $media_type['media'];

        if (!empty($media_type['parameters'])) {
          $parameter_value = reset($media_type['parameters']);
          $parameter_name = key($media_type['parameters']);
          unset($media_type['parameters'][$parameter_name]);

          $media_parameters = $parameter_name . '=' . $parameter_value;
          foreach ($media_type['parameters'] as $parameter_name => $parameter_value) {
            $media_parameters .= '; ' . $parameter_name . '=' . $parameter_value;
          }
          unset($parameter_name);
          unset($parameter_value);

          $header_output[static::RESPONSE_ACCEPT_PATCH] .= ' ' . $media_parameters;
          unset($media_parameters);
        }
      }
      unset($media_type);
    }
  }

  /**
   * Prepare HTTP response header: allow.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.1
   */
  private function p_prepare_header_response_allow($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ALLOW, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_ALLOW] = $header_name . static::SEPARATOR_HEADER_NAME;

    if (array_key_exists(static::HTTP_METHOD_NONE, $this->response[static::RESPONSE_ALLOW])) {
      // An empty Allow field value indicates that the resource allows no methods, which might occur in a 405 response if the resource has been temporarily disabled by configuration.
      return;
    }

    $mapping = array_flip($this->p_get_http_method_mapping());

    $allow = reset($this->response[static::RESPONSE_ALLOW]);
    $allow_key = key($this->response[static::RESPONSE_ALLOW]);
    unset($this->response[static::RESPONSE_ALLOW][$allow_key]);
    unset($allow_key);

    $header_output[static::RESPONSE_ALLOW] .= $mapping[$allow];
    foreach ($this->response[static::RESPONSE_ALLOW] as $allow) {
      $header_output[static::RESPONSE_ALLOW] .= ', ' . $mapping[$allow];
    }
    unset($allow);
    unset($mapping);
  }

  /**
   * Prepare HTTP response header: cache-control.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2
   * @see: https://tools.ietf.org/html/rfc7234#section-5.2.3
   */
  private function p_prepare_header_response_cache_control($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CACHE_CONTROL, $this->response) || empty($this->response[static::RESPONSE_CACHE_CONTROL])) {
      return;
    }

    $header_output[static::RESPONSE_CACHE_CONTROL] = NULL;
    foreach ($this->response[static::RESPONSE_CACHE_CONTROL] as $cache_control_directive => $cache_control_value) {
      if (is_null($header_output[static::RESPONSE_CACHE_CONTROL])) {
        $header_output[static::RESPONSE_CACHE_CONTROL] = $header_name . static::SEPARATOR_HEADER_NAME;
      }
      else {
        $header_output[static::RESPONSE_CACHE_CONTROL] .= ', ';
      }

      switch ($cache_control_directive) {
        case static::CACHE_CONTROL_NO_CACHE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'no-cache';
          break;

        case static::CACHE_CONTROL_NO_STORE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'no-store';
          break;

        case static::CACHE_CONTROL_NO_TRANSFORM:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'no-transform';
          break;

        case static::CACHE_CONTROL_MAX_AGE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'max-age';
          break;

        case static::CACHE_CONTROL_MAX_AGE_S:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 's-max-age';
          break;

        case static::CACHE_CONTROL_MAX_STALE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'max-statle';
          break;

        case static::CACHE_CONTROL_MIN_FRESH:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'min-fresh';
          break;

        case static::CACHE_CONTROL_ONLY_IF_CACHED:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'only-if-cached';
          break;

        case static::CACHE_CONTROL_PUBLIC:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'public';
          break;

        case static::CACHE_CONTROL_PRIVATE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'private';
          break;

        case static::CACHE_CONTROL_MUST_REVALIDATE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'must-revalidate';
          break;

        case static::CACHE_CONTROL_PROXY_REVALIDATE:
          $header_output[static::RESPONSE_CACHE_CONTROL] .= 'proxy-revalidate';
          break;

        default:
          break;
      }

      if (!is_null($cache_control_value)) {
        $header_output[static::RESPONSE_CACHE_CONTROL] .= '=' . $cache_control_value;
      }
    }
    unset($cache_control_directive);
    unset($cache_control_value);
  }

  /**
   * Prepare HTTP response header: connection.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-6.1
   */
  private function p_prepare_header_response_connection($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONNECTION, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_CONNECTION] = $header_name . static::SEPARATOR_HEADER_NAME;

    $connection = reset($this->response[static::RESPONSE_CONNECTION]);
    $connection_key = key($this->response[static::RESPONSE_CONNECTION]);
    unset($this->response[static::RESPONSE_CONNECTION][$connection_key]);
    unset($connection_key);

    $header_output[static::RESPONSE_CONNECTION] .= $connection;
    foreach ($this->response[static::RESPONSE_CONNECTION] as $connection) {
      $header_output[static::RESPONSE_CONNECTION] .= ', ' . $connection;
    }
    unset($connection);
  }

  /**
   * Prepare HTTP response header: content-disposition.
   *
   * The standard defines this as:
   * - 1*(tchar) *(";" (wsp) 1*(tchar) "=" 1*(tchar) / 1*(quoted-string))
   *
   * The "type" is: 1*(tchar)
   * The "parameter_name" is: 1*(tchar)
   * The "parameter_value" is: 1*(tchar) / 1*(quoted-string)
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  private function p_prepare_header_response_content_disposition($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONTENT_DISPOSITION, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_CONTENT_DISPOSITION] = $header_name . static::SEPARATOR_HEADER_NAME;
    $header_output[static::RESPONSE_CONTENT_DISPOSITION] .= $this->response[static::RESPONSE_CONTENT_DISPOSITION]['type'];

    if (empty($this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters'])) {
      return;
    }

    $parameter_value = reset($this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters']);
    $parameter_name = key($this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters']);
    unset($this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters'][$parameter_name]);
    if (is_null($parameter_value)) {
      $parameters_string = $parameter_name;
    }
    else {
      $parameters_string = $parameter_name . '=' . $parameter_value;
    }

    foreach($this->response[static::RESPONSE_CONTENT_DISPOSITION]['parameters'] as $parameter_name => $parameter_value) {
      $header_output[static::RESPONSE_CONTENT_DISPOSITION] .= '; ';

      if (is_null($parameter_value)) {
        $header_output[static::RESPONSE_CONTENT_DISPOSITION] .= $parameter_name;
      }
      else {
        $header_output[static::RESPONSE_CONTENT_DISPOSITION] .= $parameter_name . '=' . $parameter_value;
      }
    }
    unset($parameter_name);
    unset($parameter_value);

    $header_output[static::RESPONSE_CONTENT_DISPOSITION] .= $parameters_string;
    unset($parameters_string);
  }

  /**
   * Prepare HTTP response header: content-encoding.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.2.2
   */
  private function p_prepare_header_response_content_encoding($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONTENT_ENCODING, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_CONTENT_ENCODING] = $header_name . static::SEPARATOR_HEADER_NAME;

    $output = NULL;

    foreach ($this->response[static::RESPONSE_CONTENT_ENCODING] as $encoding) {
      switch ($encoding) {
        case static::ENCODING_CHUNKED:
          $output .= ',chunked';
          break;
        case static::ENCODING_COMPRESS:
          $output .= ',compress';
          break;
        case static::ENCODING_DEFLATE:
          $output .= ',deflate';
          break;
        case static::ENCODING_GZIP:
          $output .= ',gzip';
          break;
        case static::ENCODING_BZIP:
          $output .= ',bzip';
          break;
        case static::ENCODING_LZO:
          $output .= ',lzo';
          break;
        case static::ENCODING_XZ:
          $output .= ',xz';
          break;
        case static::ENCODING_EXI:
          $output .= ',exi';
          break;
        case static::ENCODING_IDENTITY:
          $output .= ',identity';
          break;
        case static::ENCODING_SDCH:
          $output .= ',sdch';
          break;
        case static::ENCODING_PG:
          $output .= ',pg';
          break;
      }
    }

    if (is_null($output)) {
      unset($header_output[static::RESPONSE_CONTENT_ENCODING]);
    }
    else {
      $header_output[static::RESPONSE_CONTENT_ENCODING] .= c_base_utf8::s_substring($output, 1)->get_value_exact();
    }
  }

  /**
   * Prepare HTTP response header: content-language.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.3.2
   */
  private function p_prepare_header_response_content_language($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONTENT_LANGUAGE, $this->response)) {
      return;
    }

    $output = NULL;
    foreach ($this->response[static::RESPONSE_CONTENT_LANGUAGE] as $language) {
      $language_array = $this->languages->s_get_aliases_by_id($language);
      if ($language_array instanceof c_base_return_array) {
        $language_array = $language_array->get_value_exact();
        $alias = end($language_array);
        if (!empty($alias)) {
          $output .= ', ' . $alias;
        }
        unset($alias);
      }
      unset($language_array);
    }
    unset($language);

    if (!is_null($output)) {
      $header_output[static::RESPONSE_CONTENT_LANGUAGE] = $header_name . ': ' . c_base_utf8::s_substring($output, 2)->get_value_exact();
    }
    unset($output);
  }

  /**
   * Prepare HTTP response header: content-range.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7233#section-4.2
   */
  private function p_prepare_header_response_content_range($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONTENT_RANGE, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_CONTENT_RANGE] = $header_name . ': ' . $this->response[static::RESPONSE_CONTENT_RANGE]['type'] . ' ';

    if ($this->response[static::RESPONSE_CONTENT_RANGE]['range'] === FALSE) {
      $header_output[static::RESPONSE_CONTENT_RANGE] .= '*/' . $this->response[static::RESPONSE_CONTENT_RANGE]['total'];
      return;
    }

    if (!is_null($this->response[static::RESPONSE_CONTENT_RANGE]['range']['start'])) {
      $header_output[static::RESPONSE_CONTENT_RANGE] .= $this->response[static::RESPONSE_CONTENT_RANGE]['range']['start'];
    }

    $header_output[static::RESPONSE_CONTENT_RANGE] .= '-';

    if (!is_null($this->response[static::RESPONSE_CONTENT_RANGE]['range']['stop'])) {
      $header_output[static::RESPONSE_CONTENT_RANGE] .= $this->response[static::RESPONSE_CONTENT_RANGE]['range']['stop'];
    }

    $header_output[static::RESPONSE_CONTENT_RANGE] .= '/';

    if ($this->response[static::RESPONSE_CONTENT_RANGE]['total'] === FALSE) {
      $header_output[static::RESPONSE_CONTENT_RANGE] .= '*';
    }
    else {
      $header_output[static::RESPONSE_CONTENT_RANGE] .= $this->response[static::RESPONSE_CONTENT_RANGE]['total'];
    }
  }

  /**
   * Prepare HTTP response header: content-type.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-3.1.1.5
   */
  private function p_prepare_header_response_content_type($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONTENT_TYPE, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_CONTENT_TYPE] = $header_name . ': ' . $this->response[static::RESPONSE_CONTENT_TYPE]['type'];

    $encoding_string = c_base_charset::s_to_string($this->response[static::RESPONSE_CONTENT_TYPE]['charset']);
    if ($encoding_string instanceof c_base_return_string) {
      $header_output[static::RESPONSE_CONTENT_TYPE] .=  '; charset=' . $encoding_string->get_value_exact();
    }

    unset($encoding_string);
  }

  /**
   * Prepare HTTP response header: etag.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6266#section-4
   */
  private function p_prepare_header_response_etag($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_ETAG, $this->response)) {
      return;
    }

    if ($this->response[static::RESPONSE_ETAG]['weak']) {
      $header_output[static::RESPONSE_ETAG] = $header_name . ': W/"' . $this->response[static::RESPONSE_ETAG]['tag'] . '"';
    }
    else {
      $header_output[static::RESPONSE_ETAG] = $header_name . ': "' . $this->response[static::RESPONSE_ETAG]['tag'] . '"';
    }
  }

  /**
   * Prepare HTTP response header: link.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc5988#section-5
   * @see: https://tools.ietf.org/html/rfc3986
   */
  private function p_prepare_header_response_link($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_LINK, $this->response)) {
      return;
    }

    foreach ($this->response[static::RESPONSE_LINK] as $uris) {
      $uri = $this->pr_rfc_string_combine_uri_array($uris['uri']);
      if ($uri === FALSE) {
        unset($uri);
        unset($uris);
        return;
      }

      if (!isset($header_output[static::RESPONSE_LINK])) {
        $header_output[static::RESPONSE_LINK] = '';
      }

      $header_output[static::RESPONSE_LINK] .= $header_name . static::SEPARATOR_HEADER_NAME;
      $header_output[static::RESPONSE_LINK] .= '<' . $uri . '>';
      unset($uri);

      if (!empty($uris['parameters'])) {
        $parameter_value = reset($uris['parameters']);
        $parameter_name = key($uris['parameters']);
        unset($uris['parameters'][$parameter_name]);

        $parameters_string = '; ';
        if (is_null($parameter_value)) {
          $parameters_string .= $parameter_name;
        }
        else {
          $parameters_string .= $parameter_name . '=' . $parameter_value;
        }

        foreach($uris['parameters'] as $parameter_name => $parameter_value) {
          $parameters_string .= '; ';

          if (is_null($parameter_value)) {
            $parameters_string .= $parameter_name;
          }
          else {
            $parameters_string .= $parameter_name . '=' . $parameter_value;
          }
        }
        unset($parameter_name);
        unset($parameter_value);

        $header_output[static::RESPONSE_LINK] .= $parameters_string;
        unset($parameters_string);
      }
    }
    unset($uris);
  }

  /**
   * Prepare HTTP response header: location.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc3986
   */
  private function p_prepare_header_response_location($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_LOCATION, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_LOCATION] = $header_name . static::SEPARATOR_HEADER_NAME;

    $combined = self::pr_rfc_string_combine_uri_array($this->response[static::RESPONSE_LOCATION]);
    if (is_string($combined)) {
      $header_output[static::RESPONSE_LOCATION] .= $combined;
    }
    unset($combined);
  }

  /**
   * Prepare HTTP response header: pragma.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.32
   * @see: https://tools.ietf.org/html/rfc7234#section-5.4
   */
  private function p_prepare_header_response_pragma($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_PRAGMA, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_PRAGMA] = $header_name . static::SEPARATOR_HEADER_NAME;

    $parameter_value = reset($this->response[static::RESPONSE_PRAGMA]);
    $parameter_name = key($this->response[static::RESPONSE_PRAGMA]);
    unset($this->response[static::RESPONSE_PRAGMA][$parameter_name]);

    if (is_null($parameter_value)) {
      $parameters_string = $parameter_name;
    }
    else {
      $parameters_string = $parameter_name . '=' . $parameter_value;
    }

    foreach($this->response[static::RESPONSE_PRAGMA] as $parameter_name => $parameter_value) {
      $parameters_string .= ', ';

      if (is_null($parameter_value)) {
        $parameters_string .= $parameter_name;
      }
      else {
        $parameters_string .= $parameter_name . '=' . $parameter_value;
      }
    }
    unset($parameter_name);
    unset($parameter_value);

    $header_output[static::RESPONSE_PRAGMA] .= $parameters_string;
    unset($parameters_string);
  }

  /**
   * Prepare HTTP response header: proxy-authenticate.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.3
   */
  private function p_prepare_header_response_proxy_authenticate($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_PROXY_AUTHENTICATE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: public-key-pins.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7469
   */
  private function p_prepare_header_response_public_key_pins($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_PUBLIC_KEY_PINS, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: refresh.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/Meta_refresh
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_refresh($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_REFRESH, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: retry-after.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.3
   */
  private function p_prepare_header_response_retry_after($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_RETRY_AFTER, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_RETRY_AFTER] = $header_name . static::SEPARATOR_HEADER_NAME;

    if ($this->response[static::RESPONSE_RETRY_AFTER]['is_seconds']) {
      $header_output[static::RESPONSE_RETRY_AFTER] .= $this->response[static::RESPONSE_RETRY_AFTER]['is_seconds'];
    }
    else {
      $timezone = date_default_timezone_get();
      date_default_timezone_set('GMT');

      $header_output[static::RESPONSE_RETRY_AFTER] .= c_base_defaults_global::s_get_date(static::TIMESTAMP_RFC_5322, $this->response[static::RESPONSE_RETRY_AFTER]['value'])->get_value_exact();

      date_default_timezone_set($timezone);
      unset($timezone);
    }
  }

  /**
   * Prepare HTTP response header: set-cookie.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6265
   */
  private function p_prepare_header_response_set_cookie($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_SET_COOKIE, $this->response) || !is_array($this->response[static::RESPONSE_SET_COOKIE])) {
      return;
    }

    $header_output[static::RESPONSE_SET_COOKIE] = array();
    foreach ($this->response[static::RESPONSE_SET_COOKIE] as $cookie) {
      if (!($cookie instanceof c_base_cookie)) {
        continue;
      }

      $cookie_string = $cookie->get_cookie();
      if ($cookie_string instanceof c_base_return_string) {
        $header_output[static::RESPONSE_SET_COOKIE][] = $cookie_string->get_value_exact();
      }
      unset($cookie_string);
    }
    unset($cookie);
  }

  /**
   * Prepare HTTP response header: server.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.4.2
   */
  private function p_prepare_header_response_server($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_SERVER, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: strict-transport-security.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc6797#section-6.1
   */
  private function p_prepare_header_response_strict_transport_security($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_STRICT_TRANSPORT_SECURITY, $this->response)) {
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
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc2616#section-14.40
   * @see: https://tools.ietf.org/html/rfc7230#section-4.1.2
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  private function p_prepare_header_response_trailer($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_TRAILER, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: transfer-encoding.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7230#section-3.3.1
   */
  private function p_prepare_header_response_transfer_encoding($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_TRANSFER_ENCODING, $this->response)) {
      return;
    }

    // according to the standard, content-length cannot be specified when transfer-encoding is defined.
    if (array_key_exists(static::RESPONSE_CONTENT_LENGTH, $header_output)) {
      unset($header_output[static::RESPONSE_CONTENT_LENGTH]);
    }

    // @todo
    // @fixme:  transfer-encoding is now an array of values.
  }

  /**
   * Prepare HTTP response header: upgrade.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-5.5.3
   */
  private function p_prepare_header_response_upgrade($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_UPGRADE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: vary.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7231#section-7.1.4
   */
  private function p_prepare_header_response_vary($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_VARY, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_VARY] = $header_name . static::SEPARATOR_HEADER_NAME;

    $vary = reset($this->response[static::RESPONSE_VARY]);
    $vary_key = key($this->response[static::RESPONSE_VARY]);
    unset($this->response[static::RESPONSE_VARY][$vary_key]);
    unset($vary_key);

    $header_output[static::RESPONSE_VARY] .= $vary;

    foreach ($this->response[static::RESPONSE_VARY] as $vary) {
      $header_output[static::RESPONSE_VARY] .= ', ' . $vary;
    }
    unset($vary);
  }

  /**
   * Prepare HTTP response header: warning.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7234#section-5.5
   */
  private function p_prepare_header_response_warning($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_WARNING, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: www-authenticate.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://tools.ietf.org/html/rfc7235#section-4.1
   */
  private function p_prepare_header_response_www_authenticate($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_WWW_AUTHENTICATE, $this->response)) {
      return;
    }

    // @todo
  }

  /**
   * Prepare HTTP response header: x-content-security-policy
   *
   * 1*((alpha) | (digit) | '-')  1*((wsp) 1*(vchar, except ';' and ',')).
   *
   * Policy Name: 1*((alpha) | (digit) | '-').
   * Policy Value: 1*(vchar, except ';' and ',').
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://www.w3.org/TR/CSP2/
   * @see: https://en.wikipedia.org/wiki/Content_Security_Policy
   * @see: https://www.html5rocks.com/en/tutorials/security/content-security-policy/
   */
  private function p_prepare_header_response_content_security_policy($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_CONTENT_SECURITY_POLICY, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_CONTENT_SECURITY_POLICY] = $header_name . static::SEPARATOR_HEADER_NAME;

    $policy_value = reset($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY]);
    $policy_name = key($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY]);
    unset($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY][$policy_name]);

    $policy_string = $policy_name;
    foreach ($policy_values as $policy_value) {
      $policy_string .= ' ' . $policy_value;
    }
    unset($policy_value);

    foreach ($this->response[static::RESPONSE_CONTENT_SECURITY_POLICY] as $policy_name => $policy_values) {
      $policy_string .= '; ' . $policy_name;
      foreach ($policy_values as $policy_value) {
        $policy_string .= ' ' . $policy_value;
      }
      unset($policy_value);
    }
    unset($policy_name);
    unset($policy_values);

    $header_output[static::RESPONSE_CONTENT_SECURITY_POLICY] .= $policy_string;
    unset($policy_string);
  }

  /**
   * Prepare HTTP response header: x-content-type-options
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_x_content_type_options($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_X_CONTENT_TYPE_OPTIONS, $this->response)) {
      return;
    }

    $header_output[static::RESPONSE_X_CONTENT_TYPE_OPTIONS] = $header_name . static::SEPARATOR_HEADER_NAME;

    if ($this->response[static::RESPONSE_X_CONTENT_TYPE_OPTIONS]) {
      $header_output[static::RESPONSE_X_CONTENT_TYPE_OPTIONS] = 'nosniff';
    }
    else {
      $header_output[static::RESPONSE_X_CONTENT_TYPE_OPTIONS] = 'sniff';
    }
  }

  /**
   * Prepare HTTP response header: x-ua-compatible
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   *
   * @see: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
   */
  private function p_prepare_header_response_x_ua_compatible($header_name, &$header_output) {
    if (!array_key_exists(static::RESPONSE_X_UA_COMPATIBLE, $this->response)) {
      return;
    }

    // in this case, a new header is created for every single entry.
    $header_output[static::RESPONSE_X_UA_COMPATIBLE] = array();
    foreach($this->response[static::RESPONSE_X_UA_COMPATIBLE] as $browser_name => $compatible_version) {
        $header_output[static::RESPONSE_X_UA_COMPATIBLE][] = $browser_name . '=' . $compatible_version;
    }
    unset($browser_name);
    unset($compatible_version);
  }

  /**
   * Prepare HTTP response headers for the content checksum fields.
   *
   * This will perform a checksum against the content.
   * Be sure to perform this check before changing the content-encoding.
   *
   * @fixme: both this function and the one for checksum_header do the same thing but for different parts of the HTTP packet.
   *         the problem is they use two completely different approaches for generating the hash.
   *         these need to be reviewed and made consistent where possible.
   *
   * This handles the following header fields:
   * - checksum_content
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   */
  private function p_prepare_header_response_checksum_content($header_name, &$header_output) {
    // this field is generally auto-populated, so enforce a default.
    if (!isset($this->response[static::RESPONSE_CHECKSUM_CONTENT])) {
      $this->response[static::RESPONSE_CHECKSUM_CONTENT] = array(
        'checksum' => NULL,
        'action' => static::CHECKSUM_ACTION_AUTO,
        'what' => static::CHECKSUM_WHAT_FULL,
        'type' => static::CHECKSUM_SHA256,
      );
    }

    // setting this to none manually disables checksum generation.
    if ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['action'] == static::CHECKSUM_ACTION_NONE) {
      return;
    }

    $what = NULL;
    if ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['what'] == static::CHECKSUM_WHAT_FULL) {
      $what = 'full';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['what'] == static::CHECKSUM_WHAT_PARTIAL) {
      $what = 'partial';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['what'] == static::CHECKSUM_WHAT_SIGNED) {
      $what = 'signed';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['what'] == static::CHECKSUM_WHAT_UNSIGNED) {
      $what = 'unsigned';
    }

    if (is_null($what)) {
      unset($what);
      return;
    }

    $algorithm = NULL;
    $use_hash = FALSE;
    switch ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['type']) {
      case static::CHECKSUM_MD2:
        $algorithm = 'md2';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_MD4:
        $algorithm = 'md4';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_MD5:
        $algorithm = 'md5';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_SHA1:
        $algorithm = 'sha1';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_SHA224:
        $algorithm = 'sha224';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_SHA256:
        $algorithm = 'sha256';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_SHA384:
        $algorithm = 'sha384';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_SHA512:
        $algorithm = 'sha512';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_CRC32:
        $algorithm = 'crc32';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_CRC32B:
        $algorithm = 'crc32b';
        $use_hash = TRUE;
        break;
      case static::CHECKSUM_PG:
        $algorithm = 'pg';
        break;
    }

    // @todo: handle support for other algorithms.
    if ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['action'] == static::CHECKSUM_ACTION_AUTO) {
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

              $error = c_base_error::s_log(NULL, array('arguments' => array(':{file_name}' => $filename, ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::NOT_FOUND_FILE);
              return c_base_return_error::s_false($error);
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

          $header_output[static::RESPONSE_CHECKSUM_CONTENT] = 'Checksum_Content: ' . $what . ':' . $algorithm . ':' . hash_final($hash, FALSE);
          unset($hash);
        }
        else {
          // @todo: handle CHECKSUM_PG in this case.
          // if ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['type'] == static::CHECKSUM_PG) {
          // }
        }
      }
      else {
        if ($use_hash) {
          $header_output[static::RESPONSE_CHECKSUM_CONTENT] = 'Checksum_Content: ' . $what . ':' . $algorithm . ':' . hash($algorithm, $this->content);
        }
        else {
          // @todo: handle CHECKSUM_PG in this case.
          // if ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['type'] == static::CHECKSUM_PG) {
          // }
        }
      }
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_CONTENT]['action'] == static::CHECKSUM_ACTION_MANUAL) {
      if (!is_null($this->response[static::RESPONSE_CHECKSUM_CONTENT]['checksum'])) {
        $header_output[static::RESPONSE_CHECKSUM_CONTENT] = 'Checksum_Content: ' . $what . ':' . $algorithm . ':' . $this->response[static::RESPONSE_CHECKSUM_CONTENT]['checksum'];
      }
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
   * - checksum_header: used to store the checksum.
   * - checksum_headers: used to store a list of all header fields the checksum is processed against.
   *
   * This must be performed after all other header fields have been prepared to be accurate.
   *
   * @param string $headers_name
   *   The HTTP checksum headers name, such as: 'Checksum_Headers'.
   * @param string $header_name
   *   The HTTP checksum header name, such as: 'Checksum_Header'.
   * @param array $header_output
   *   The header output array to make changes to.
   * @param string|null $status_string
   *   When not NULL, this is prepended to the start of the header checksum string before the checksum is calculated.
   *   When NULL, this value is ignored.
   */
  private function p_prepare_header_response_checksum_header($headers_name, $header_name, &$header_output, $status_string) {
    // this field is generally auto-populated, so enforce a default.
    if (!isset($this->response[static::RESPONSE_CHECKSUM_HEADER])) {
      $this->response[static::RESPONSE_CHECKSUM_HEADER] = array(
        'checksum' => NULL,
        'action' => static::CHECKSUM_ACTION_AUTO,
        'what' => static::CHECKSUM_WHAT_FULL,
        'type' => static::CHECKSUM_SHA256,
      );
    }

    // setting this to none manually disables checksum generation.
    if ($this->response[static::RESPONSE_CHECKSUM_HEADER]['action'] == static::CHECKSUM_ACTION_NONE) {
      return;
    }

    // allow for the list of headers to be customized, but if it is not defined, use all available (allowed) headers.
    if (array_key_exists(static::RESPONSE_CHECKSUM_HEADERS, $this->response)) {
      $header_output_copy = array();
      $header_output[static::RESPONSE_CHECKSUM_HEADERS] = array();
      foreach ($this->response[static::RESPONSE_CHECKSUM_HEADERS] as $header_response_id => $header_response_value) {
        $header_output_copy[$header_response_id] = $header_response_id;
        $header_output[static::RESPONSE_CHECKSUM_HEADERS][$header_response_id] = $header_response_id;
      }
      unset($header_response_id);
      unset($header_response_value);
    }
    else {
      $header_output_copy = $header_output;
    }

    if (array_key_exists('date_actual', $header_output_copy)) {
      // When date_actual is specified, the date parameter will not be processed, prevent the 'date' parameter from being used to calculate the header checksum.
      unset($header_output_copy['date']);
    }


    if (empty($header_output_copy)) {
      // if there are no headers to perform a checksum against, then provide no checksum.
      unset($header_output_copy);
      unset($header_output[static::RESPONSE_CHECKSUM_HEADER]);
      unset($header_output[static::RESPONSE_CHECKSUM_HEADERS]);

      return;
    }

    reset($header_output_copy);
    $header_output_id = key($header_output_copy);
    unset($header_output_copy[$header_output_id]);

    $header_string = '';
    $header_mappings = $this->p_get_header_response_mapping();
    if (array_key_exists($header_output_id, $header_mappings)) {
      $header_string .= $header_mappings[$header_output_id];
    }

    foreach ($header_output_copy as $header_output_id => $header_output_value) {
      if (array_key_exists($header_output_id, $header_mappings)) {
        $header_string .= ', ' . $header_mappings[$header_output_id];
      }
    }
    unset($header_output_id);
    unset($header_output_value);
    unset($header_mappings);

    $header_output[static::RESPONSE_CHECKSUM_HEADERS] = $headers_name . static::SEPARATOR_HEADER_NAME . $header_string;
    unset($header_string);
    unset($header_output_copy);


    // checksum cannot include its own field.
    $header_output_copy = $header_output;
    unset($header_output_copy['checkum_header']);

    // the header keys must be in alphabetic order to ensure a consistent order for the checksum generation and validation.
    ksort($header_output_copy);

    $header_string = '';
    if (!is_null($status_string)) {
      $header_string .= $status_string . static::SEPARATOR_HEADER_LINE;
    }

    foreach ($header_output_copy as $header_output_id => $header_output_value) {
      if (array_key_exists($header_output_id, $header_output_copy)) {
        if (is_array($header_output_value)) {
          foreach ($header_output_value as $sub_header) {
            $header_string .= $sub_header . static::SEPARATOR_HEADER_LINE;
          }
          unset($sub_header);
        }
        else {
          $header_string .= $header_output_value . static::SEPARATOR_HEADER_LINE;
        }
      }
    }
    unset($header_output_copy);
    unset($header_output_id);
    unset($header_output_value);


    // generate the checksum header based on given parameters when no pre-calculated checksum is given.
    if ($this->response[static::RESPONSE_CHECKSUM_HEADER]['action'] == static::CHECKSUM_ACTION_AUTO) {
      if ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_MD2) {
        $checkum_header = hash('md2', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_MD4) {
        $checkum_header = hash('md4', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_MD5) {
        $checkum_header = hash('md5', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA1) {
        $checkum_header = hash('sha1', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA224) {
        $checkum_header = hash('sha224', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA256) {
        $checkum_header = hash('sha256', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA384) {
        $checkum_header = hash('sha384', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA512) {
        $checkum_header = hash('sha512', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_CRC32) {
        $checkum_header = hash('crc32', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_CRC32B) {
        $checkum_header = hash('crc32b', $header_string);
      }
      elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_PG) {
        // @todo:
        #$checkum_header = ;
      }
      unset($header_string);
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['action'] == static::CHECKSUM_ACTION_MANUAL) {
      $checkum_header = $this->response[static::RESPONSE_CHECKSUM_HEADER]['checksum'];
    }
    else {
      return;
    }


    $header_output[static::RESPONSE_CHECKSUM_HEADER] = $header_name . static::SEPARATOR_HEADER_NAME;

    if ($this->response[static::RESPONSE_CHECKSUM_HEADER]['what'] === static::CHECKSUM_WHAT_FULL) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'full:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['what'] === static::CHECKSUM_WHAT_PARTIAL) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'partial:';
      $checkum_header = mt_substr($checkum_header, 0, static::CHECKSUM_LENGTH_SHORTSUM);
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['what'] === static::CHECKSUM_WHAT_SIGNED) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'signed:';
      // @todo
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['what'] === static::CHECKSUM_WHAT_UNSIGNED) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'unsigned:';
      // @todo
    }

    if ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_MD2) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'md2:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_MD4) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'md4:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_MD5) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'md5:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA1) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'sha1:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA224) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'sha224:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA256) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'sha256:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA384) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'sha384:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_SHA512) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'sha512:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_CRC32) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'crc32:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_CRC32B) {
      $header_output[static::RESPONSE_CHECKSUM_HEADER] .= 'crc32b:';
    }
    elseif ($this->response[static::RESPONSE_CHECKSUM_HEADER]['type'] === static::CHECKSUM_PG) {
      // @todo:
      #$checkum_header = ;
    }

    $header_output[static::RESPONSE_CHECKSUM_HEADER] .= $checkum_header;
    unset($checkum_header);
  }

  /**
   * Prepare HTTP response headers that are simple values.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   * @param int $code
   *   The HTTP header code, such as:  self::RESPONSE_AGE.
   */
  private function p_prepare_header_response_simple_value($header_name, &$header_output, $code) {
    if (!array_key_exists($code, $this->response)) {
      return;
    }

    $header_output[$code] = $header_name . static::SEPARATOR_HEADER_NAME . $this->response[$code];
  }

  /**
   * Prepare HTTP response headers that are boolean values represented by the words true/false.
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   * @param int $code
   *   The HTTP header code, such as:  self::RESPONSE_AGE.
   */
  private function p_prepare_header_response_boolean_value($header_name, &$header_output, $code) {
    if (!array_key_exists($code, $this->response)) {
      return;
    }

    $header_output[$code] = $header_name . static::SEPARATOR_HEADER_NAME;

    if ($this->response[$code]) {
      $header_output[$code] .= 'true';
    }
    else {
      $header_output[$code] .= 'false';
    }
  }

  /**
   * Prepare HTTP response header: date
   *
   * @param string $header_name
   *   The HTTP header name, such as: 'Age'.
   * @param array $header_output
   *   The header output array to make changes to.
   * @param string $name_lower
   *   The HTTP header name (all lowercase), such as: 'age'.
   * @param int $code
   *   The HTTP header code, such as:  self::RESPONSE_AGE.
   */
  private function p_prepare_header_response_timestamp_value($header_name, &$header_output, $name_lower, $code) {
    if (!array_key_exists($code, $this->response)) {
      return;
    }

    $timezone = date_default_timezone_get();
    date_default_timezone_set('GMT');

    $header_output[$code] = $header_name . static::SEPARATOR_HEADER_NAME . c_base_defaults_global::s_get_date(static::TIMESTAMP_RFC_5322, $this->response[$code])->get_value_exact();

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
    if (!$this->request[static::REQUEST_ACCEPT_ENCODING]['defined'] || $this->request[static::REQUEST_ACCEPT_ENCODING]['invalid']) {
      return static::ENCODING_CHUNKED;
    }

    $encoding = static::ENCODING_CHUNKED;
    foreach ($this->request[static::REQUEST_ACCEPT_ENCODING]['data']['weight'] as $weight => $choices) {
      foreach ($choices as $key => $choice) {
        if ($key == c_base_http::ENCODING_GZIP) {
          $encoding = $key;
          break 2;
        }

        if ($key == static::ENCODING_DEFLATE) {
          $encoding = $key;
          break 2;
        }

        if ($key == static::ENCODING_BZIP) {
          $encoding = $key;
          break 2;
        }

        if ($key == static::ENCODING_LZO) {
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
   *
   * @return bool
   *   TRUE if content was encoded.
   *   FALSE otherwise.
   *
   * @see: https://github.com/adsr/php-lzo
   * @see: http://www.oberhumer.com/opensource/lzo/
   * @see: http://content.gpwiki.org/index.php/LZO
   * @see: https://github.com/payden/php-xz
   * @see: https://github.com/choden/php-xz
   */
  private function p_encode_content($content, $encoding, $compression = NULL, $calculate_content_length = TRUE) {
    if ($encoding == static::ENCODING_GZIP) {
      if (is_null($compression) || $compression < -1) {
        $compression = -1;
      }
      elseif ($compression > 9) {
        $compression = 9;
      }

      $this->content = gzencode($this->content, $compression, FORCE_GZIP);
      $this->content_is_file = FALSE;
      $this->response[static::RESPONSE_CONTENT_ENCODING] = array($encoding);

      if ($calculate_content_length) {
        $this->response[static::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }

      return TRUE;
    }
    elseif ($encoding == static::ENCODING_DEFLATE) {
      if (is_null($compression) || $compression < -1) {
        $compression = -1;
      }
      elseif ($compression > 9) {
        $compression = 9;
      }

      $this->content = gzencode($content, $compression, FORCE_DEFLATE);
      $this->content_is_file = FALSE;
      $this->response[static::RESPONSE_CONTENT_ENCODING] = array($encoding);

      if ($calculate_content_length) {
        $this->response[static::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }

      return TRUE;
    }
    elseif ($encoding == static::ENCODING_BZIP) {
      if (is_null($compression) || $compression < -1) {
        $compression = 4;
      }
      elseif ($compression > 9) {
        $compression = 9;
      }

      $this->content = bzcompress($content, $compression);
      $this->content_is_file = FALSE;
      $this->response[static::RESPONSE_CONTENT_ENCODING] = array($encoding);

      if ($calculate_content_length) {
        $this->response[static::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }

      return TRUE;
    }
    elseif ($encoding == static::ENCODING_LZO) {
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
      $this->response[static::RESPONSE_CONTENT_ENCODING] = array($encoding);

      if ($calculate_content_length) {
        $this->response[static::RESPONSE_CONTENT_LENGTH] = strlen($this->content);
      }

      return TRUE;
    }
    elseif ($encoding == static::ENCODING_XZ) {
      // @fixme: php-xz module is currently not working.
    }
    elseif ($encoding == static::ENCODING_EXI) {
      // @todo, maybe? (cannot seem to find a php library at this time)
    }
    elseif ($encoding == static::ENCODING_IDENTITY) {
      // @todo, maybe? (cannot seem to find a php library at this time)
    }
    elseif ($encoding == static::ENCODING_SDCH) {
      // @todo, maybe? (cannot seem to find a php library at this time)
    }
    elseif ($encoding == static::ENCODING_PG) {
      // @todo, using ascii armor on entire body.
      //        should be a header field containing the public key.
    }

    return FALSE;
  }
}
