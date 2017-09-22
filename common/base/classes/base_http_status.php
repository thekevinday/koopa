<?php
/**
 * @file
 * Provides a class for managing the HTTP protocol status codes.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for managing the HTTP protocol status codes.
 *
 * @see: https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
class c_base_http_status {
  const UNDEFINED = 0;
  const INVALID   = 1;
  const UNKNOWN   = 2;

  const CONTINUE_REQUEST    = 100; // https://tools.ietf.org/html/rfc7231#section-6.2.1 (cannot use "CONTINUE" here because it is reserved by PHP.)
  const SWITCHING_PROTOCOLS = 101; // https://tools.ietf.org/html/rfc7231#section-6.2.2
  const PROCESSING          = 102;

  const OK                = 200; // https://tools.ietf.org/html/rfc7231#section-6.3.1
  const CREATED           = 201; // https://tools.ietf.org/html/rfc7231#section-6.3.2
  const ACCEPTED          = 202; // https://tools.ietf.org/html/rfc7231#section-6.3.3
  const NON_AUTHORATATIVE = 203; // https://tools.ietf.org/html/rfc7231#section-6.3.4
  const NO_CONTENT        = 204; // https://tools.ietf.org/html/rfc7231#section-6.3.5
  const RESET_CONTENT     = 205; // https://tools.ietf.org/html/rfc7231#section-6.3.6
  const PARTIAL_CONTENT   = 206; // https://tools.ietf.org/html/rfc7233#section-4.1
  const MULTI_STATUS      = 207;
  const ALREADY_REPORTED  = 208;
  const IM_USED           = 209;

  const MULTIPLE_CHOICES   = 300; // https://tools.ietf.org/html/rfc7231#section-6.4.1
  const MOVED_PERMANENTLY  = 301; // https://tools.ietf.org/html/rfc7231#section-6.4.2
  const FOUND              = 302; // https://tools.ietf.org/html/rfc7231#section-6.4.3
  const SEE_OTHER          = 303; // https://tools.ietf.org/html/rfc7231#section-6.4.4
  const NOT_MODIFIED       = 304; // https://tools.ietf.org/html/rfc7232#section-4.1
  const USE_PROXY          = 305; // https://tools.ietf.org/html/rfc7231#section-6.4.5
  const SWITCH_PROXY       = 306;
  const TEMPORARY_REDIRECT = 307; // https://tools.ietf.org/html/rfc7231#section-6.4.7
  const PERMANENT_REDIRECT = 308;

  const BAD_REQUEST                     = 400; // https://tools.ietf.org/html/rfc7231#section-6.5.1
  const UNAUTHORIZED                    = 401; // https://tools.ietf.org/html/rfc7235#section-3.1
  const PAYMENT_REQUIRED                = 402; // https://tools.ietf.org/html/rfc7231#section-6.5.2
  const FORBIDDEN                       = 403; // https://tools.ietf.org/html/rfc7231#section-6.5.3
  const NOT_FOUND                       = 404; // https://tools.ietf.org/html/rfc7231#section-6.5.4
  const METHOD_NOT_ALLOWED              = 405; // https://tools.ietf.org/html/rfc7231#section-6.5.5
  const NOT_ACCEPTABLE                  = 406; // https://tools.ietf.org/html/rfc7231#section-6.5.6
  const PROXY_AUTHENTICATION_REQUIRED   = 407; // https://tools.ietf.org/html/rfc7235#section-3.2
  const REQUEST_TIMEOUT                 = 408; // https://tools.ietf.org/html/rfc7231#section-6.5.7
  const CONFLICT                        = 409; // https://tools.ietf.org/html/rfc7231#section-6.5.8
  const GONE                            = 410; // https://tools.ietf.org/html/rfc7231#section-6.5.9
  const LENGTH_REQUIRED                 = 411; // https://tools.ietf.org/html/rfc7231#section-6.5.10
  const PRECONDITION_FAILED             = 412; // https://tools.ietf.org/html/rfc7232#section-4.2
  const PAYLOAD_TOO_LARGE               = 413; // https://tools.ietf.org/html/rfc7231#section-6.5.11
  const REQUEST_URI_TOO_LONG            = 414; // https://tools.ietf.org/html/rfc7231#section-6.5.12
  const UNSUPPORTED_MEDIA_TYPE          = 415; // https://tools.ietf.org/html/rfc7231#section-6.5.13
  const REQUESTED_RANGE_NOT_SATISFIABLE = 416; // https://tools.ietf.org/html/rfc7233#section-4.4
  const EXPECTATION_FAILED              = 417; // https://tools.ietf.org/html/rfc7231#section-6.5.14
  const MISDIRECTED_REQUEST             = 422;
  const LOCKED                          = 423;
  const FAILED_DEPENDENCY               = 424;
  const UPGRADE_REQUIRED                = 426; // https://tools.ietf.org/html/rfc7231#section-6.5.15
  const PRECONDITION_REQUIRED           = 428;
  const TOO_MANY_REQUESTS               = 429;
  const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
  const NO_RESPONSE                     = 444; // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#nginx
  const UNAVAILABLE_FOR_LEGAL_REASONS   = 451;
  const SSL_CERTIFICATE_ERROR           = 495; // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#nginx
  const HTTP_REQUEST_SENT_TO_HTTPS      = 497; // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#nginx
  const CLIENT_CLOSED_REQUEST           = 499; // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#nginx

  const INTERNAL_SERVER_ERROR           = 500; // https://tools.ietf.org/html/rfc7231#section-6.6.1
  const NOT_IMPLEMENTED                 = 501; // https://tools.ietf.org/html/rfc7231#section-6.6.2
  const BAD_GATEWAY                     = 502; // https://tools.ietf.org/html/rfc7231#section-6.6.3
  const SERVICE_UNAVAILABLE             = 503; // https://tools.ietf.org/html/rfc7231#section-6.6.4
  const GATEWAY_TIMEOUT                 = 504; // https://tools.ietf.org/html/rfc7231#section-6.6.5
  const HTTP_VERSION_NOT_SUPPORTED      = 505; // https://tools.ietf.org/html/rfc7231#section-6.6.6
  const VARIANT_ALSO_NEGOTIATES         = 506;
  const INSUFFICIENT_STORAGE            = 507;
  const LOOP_DETECTED                   = 508;
  const NOT_EXTENDED                    = 510;
  const NETWORK_AUTHENTICATION_REQUIRED = 511;


  /**
   * Convert the given status code into a text statement.
   *
   * @param int $status
   *   The code to convert
   *
   * @return c_base_return_string|c_base_return_status
   *   The status text string on success, FALSE otherwise.
   *   FALSE with the error bit set is returned on error.
   */
  public static function to_text($status) {
    if (!is_int($status)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':{argument_name}' => 'status', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $string = "";
    switch ($status) {
      case static::CONTINUE_REQUEST:
        $string = "Continue Request";
        break;

      case static::SWITCHING_PROTOCOLS:
        $string = "Switching Protocols";
        break;

      case static::PROCESSING:
        $string = "Processing";
        break;

      case static::OK:
        $string = "OK";
        break;

      case static::CREATED:
        $string = "Created";
        break;

      case static::ACCEPTED:
        $string = "Accepted";
        break;

      case static::NON_AUTHORATATIVE:
        $string = "Non-Authoratative";
        break;

      case static::NO_CONTENT:
        $string = "No Content";
        break;

      case static::RESET_CONTENT:
        $string = "Reset Content";
        break;

      case static::PARTIAL_CONTENT:
        $string = "Partial Content";
        break;

      case static::MULTI_STATUS:
        $string = "Multi-Status";
        break;

      case static::ALREADY_REPORTED:
        $string = "Already Reported";
        break;

      case static::IM_USED:
        $string = "IM Used";
        break;

      case static::MULTIPLE_CHOICES:
        $string = "Multiple Choices";
        break;

      case static::MOVED_PERMANENTLY:
        $string = "Moved Permanently";
        break;

      case static::FOUND:
        $string = "Found";
        break;

      case static::SEE_OTHER:
        $string = "See Other";
        break;

      case static::NOT_MODIFIED:
        $string = "Not Modified";
        break;

      case static::USE_PROXY:
        $string = "Use Proxy";
        break;

      case static::SWITCH_PROXY:
        $string = "Switch Proxy";
        break;

      case static::TEMPORARY_REDIRECT:
        $string = "Temporary Redirect";
        break;

      case static::PERMANENT_REDIRECT:
        $string = "Permanent Redirect";
        break;

      case static::BAD_REQUEST:
        $string = "Bad Request";
        break;

      case static::UNAUTHORIZED:
        $string = "Unauthorized";
        break;

      case static::PAYMENT_REQUIRED:
        $string = "Payment Required";
        break;

      case static::FORBIDDEN:
        $string = "Forbidden";
        break;

      case static::NOT_FOUND:
        $string = "Not Found";
        break;

      case static::METHOD_NOT_ALLOWED:
        $string = "Method Not Allowed";
        break;

      case static::NOT_ACCEPTABLE:
        $string = "Not Acceptable";
        break;

      case static::PROXY_AUTHENTICATION_REQUIRED:
        $string = "Proxy Authentication Required";
        break;

      case static::REQUEST_TIMEOUT:
        $string = "Request Timeout";
        break;

      case static::CONFLICT:
        $string = "Conflict";
        break;

      case static::GONE:
        $string = "Gone";
        break;

      case static::LENGTH_REQUIRED:
        $string = "Length Required";
        break;

      case static::PRECONDITION_FAILED:
        $string = "Pre-condition Failed";
        break;

      case static::PAYLOAD_TOO_LARGE:
        $string = "Payload Too Large";
        break;

      case static::REQUEST_URI_TOO_LONG:
        $string = "Request URI Too Long";
        break;

      case static::UNSUPPORTED_MEDIA_TYPE:
        $string = "Unsupported Media Type";
        break;

      case static::REQUESTED_RANGE_NOT_SATISFIABLE:
        $string = "Requested Range Not Satisfiable";
        break;

      case static::EXPECTATION_FAILED:
        $string = "Expectation Failed";
        break;

      case static::MISDIRECTED_REQUEST:
        $string = "Misdirected Request";
        break;

      case static::LOCKED:
        $string = "Locked";
        break;

      case static::FAILED_DEPENDENCY:
        $string = "Failed Dependency";
        break;

      case static::UPGRADE_REQUIRED:
        $string = "Upgrade Required";
        break;

      case static::PRECONDITION_REQUIRED:
        $string = "Pre-Condition Required";
        break;

      case static::TOO_MANY_REQUESTS:
        $string = "Too Many Requests";
        break;

      case static::REQUEST_HEADER_FIELDS_TOO_LARGE:
        $string = "Request Header Fields Too Large";
        break;

      case static::NO_RESPONSE:
        $string = "No Response";
        break;

      case static::UNAVAILABLE_FOR_LEGAL_REASONS:
        $string = "Unavailable for Legal Reasons";
        break;

      case static::SSL_CERTIFICATE_ERROR:
        $string = "SSL Certificate Error";
        break;

      case static::HTTP_REQUEST_SENT_TO_HTTPS:
        $string = "HTTP Request Sent to HTTPS Port";
        break;

      case static::CLIENT_CLOSED_REQUEST:
        $string = "Client Closed Request";
        break;

      case static::INTERNAL_SERVER_ERROR:
        $string = "Internal Server Error";
        break;

      case static::NOT_IMPLEMENTED:
        $string = "Not Implemented";
        break;

      case static::BAD_GATEWAY:
        $string = "Bad Gateway";
        break;

      case static::SERVICE_UNAVAILABLE:
        $string = "Service Unavailable";
        break;

      case static::GATEWAY_TIMEOUT:
        $string = "Gateway Timeout";
        break;

      case static::HTTP_VERSION_NOT_SUPPORTED:
        $string = "HTTP Version Not Supported";
        break;

      case static::VARIANT_ALSO_NEGOTIATES:
        $string = "Variant Also Negotiates";
        break;

      case static::INSUFFICIENT_STORAGE:
        $string = "Unsufficient Storage";
        break;

      case static::LOOP_DETECTED:
        $string = "Loop Detected";
        break;

      case static::NOT_EXTENDED:
        $string = "Not Extended";
        break;

      case static::NETWORK_AUTHENTICATION_REQUIRED:
        $string = "Network Authentication Required";
        break;

      case static::UNDEFINED:
      case static::UNKNOWN:
        $string = "";
        break;

      case static::INVALID:
        // invalid will not be processed because it is invalid.

      default:
        return new c_base_return_false();
    }

    return c_base_return_string::s_new($string);
  }
}
