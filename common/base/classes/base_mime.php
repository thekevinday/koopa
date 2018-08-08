<?php
/**
 * @file
 * Provides a class for managing mime-type information.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for managing mime-type information.
 */
class c_base_mime {
  const CATEGORY_NONE        = 0;
  const CATEGORY_UNKNOWN     = 1;
  const CATEGORY_PROVIDED    = 2;
  const CATEGORY_STREAM      = 3;
  const CATEGORY_MULTIPART   = 4;
  const CATEGORY_TEXT        = 5;
  const CATEGORY_IMAGE       = 6;
  const CATEGORY_AUDIO       = 7;
  const CATEGORY_VIDEO       = 8;
  const CATEGORY_DOCUMENT    = 9;
  const CATEGORY_CONTAINER   = 10;
  const CATEGORY_APPLICATION = 11; // only for application/* that values not covered by any other category.

  const TYPE_NONE      = 0;
  const TYPE_UNKNOWN   = 1;
  const TYPE_PROVIDED  = 2;
  const TYPE_STREAM    = 3;
  const TYPE_DATA_FORM = 4;
  const TYPE_DATA_URL  = 5;

  const TYPE_TEXT       = 1000;
  const TYPE_TEXT_PLAIN = 1001;
  const TYPE_TEXT_HTML  = 1002;
  const TYPE_TEXT_RSS   = 1003;
  const TYPE_TEXT_ICAL  = 1004;
  const TYPE_TEXT_CSV   = 1005;
  const TYPE_TEXT_XML   = 1006;
  const TYPE_TEXT_CSS   = 1007;
  const TYPE_TEXT_JS    = 1008;
  const TYPE_TEXT_JSON  = 1009;
  const TYPE_TEXT_RICH  = 1010;
  const TYPE_TEXT_XHTML = 1011;
  const TYPE_TEXT_PS    = 1012;
  const TYPE_TEXT_FSS   = 1013;

  const TYPE_IMAGE      = 2000;
  const TYPE_IMAGE_PNG  = 2001;
  const TYPE_IMAGE_GIF  = 2002;
  const TYPE_IMAGE_JPEG = 2003;
  const TYPE_IMAGE_BMP  = 2004;
  const TYPE_IMAGE_SVG  = 2005;
  const TYPE_IMAGE_TIFF = 2006;

  const TYPE_AUDIO       = 3000;
  const TYPE_AUDIO_WAV   = 3001;
  const TYPE_AUDIO_OGG   = 3002;
  const TYPE_AUDIO_OPUS  = 3003;
  const TYPE_AUDIO_SPEEX = 3004;
  const TYPE_AUDIO_FLAC  = 3005;
  const TYPE_AUDIO_MP3   = 3006;
  const TYPE_AUDIO_MP4   = 3007;
  const TYPE_AUDIO_MIDI  = 3008;
  const TYPE_AUDIO_BASIC = 3009;

  const TYPE_VIDEO           = 4000;
  const TYPE_VIDEO_MPEG      = 4001;
  const TYPE_VIDEO_OGG       = 4002;
  const TYPE_VIDEO_H264      = 4003;
  const TYPE_VIDEO_QUICKTIME = 4004;
  const TYPE_VIDEO_DV        = 4005;
  const TYPE_VIDEO_JPEG      = 4006;
  const TYPE_VIDEO_WEBM      = 4007;

  const TYPE_DOCUMENT                   = 5000;
  const TYPE_DOCUMENT_PDF               = 5001;
  const TYPE_DOCUMENT_LIBRECHART        = 5002;
  const TYPE_DOCUMENT_LIBREFORMULA      = 5003;
  const TYPE_DOCUMENT_LIBREGRAPHIC      = 5004;
  const TYPE_DOCUMENT_LIBREPRESENTATION = 5005;
  const TYPE_DOCUMENT_LIBRESPREADSHEET  = 5006;
  const TYPE_DOCUMENT_LIBRETEXT         = 5007;
  const TYPE_DOCUMENT_LIBREHTML         = 5008;
  const TYPE_DOCUMENT_ABIWORD           = 5009;
  const TYPE_DOCUMENT_MSWORD            = 5010;
  const TYPE_DOCUMENT_MSEXCEL           = 5011;
  const TYPE_DOCUMENT_MSPOWERPOINT      = 5012;

  const TYPE_CONTAINER      = 6000;
  const TYPE_CONTAINER_TAR  = 6001;
  const TYPE_CONTAINER_CPIO = 6002;
  const TYPE_CONTAINER_JAVA = 6003;

  const TYPE_APPLICATION               = 7000;
  const TYPE_APPLICATION_OCSP_REQUEST  = 7001;
  const TYPE_APPLICATION_OCSP_RESPONSE = 7002;


  private static $s_names_provided = [
    self::TYPE_PROVIDED  => ['*/*', 'text/*', 'image/*', 'audio/*', 'video/*', 'application/*'],
    self::TYPE_STREAM    => ['application/octet-stream'],
    self::TYPE_DATA_FORM => ['multipart/form-data'],
    self::TYPE_DATA_URL => ['application/x-www-form-urlencoded'],
  ];

  private static $s_names_text = [
    self::TYPE_TEXT         => ['text/*'],
    self::TYPE_TEXT_PLAIN   => ['text/plain'],
    self::TYPE_TEXT_HTML    => ['text/html'],
    self::TYPE_TEXT_RSS     => ['application/rss', 'application/rss+xml', 'application/rdf+xml', 'application/atom+xml'],
    self::TYPE_TEXT_ICAL    => ['text/calendar'],
    self::TYPE_TEXT_CSV     => ['text/csv'],
    self::TYPE_TEXT_XML     => ['application/xml'],
    self::TYPE_TEXT_CSS     => ['text/css'],
    self::TYPE_TEXT_JS      => ['text/javascript', 'application/javascript'],
    self::TYPE_TEXT_JSON    => ['text/json', 'application/json'],
    self::TYPE_TEXT_RICH    => ['text/rtf'],
    self::TYPE_TEXT_XHTML   => ['application/xhtml', 'application/xhtml+xml'],
    self::TYPE_TEXT_PS      => ['text/ps'],
    self::TYPE_TEXT_FSS     => ['text/fss'],
  ];

  private static $s_names_image = [
    self::TYPE_IMAGE      => ['image/*'],
    self::TYPE_IMAGE_PNG  => ['image/png'],
    self::TYPE_IMAGE_GIF  => ['image/gif'],
    self::TYPE_IMAGE_JPEG => ['image/jpeg', 'image/jpg', 'image/jpx'],
    self::TYPE_IMAGE_BMP  => ['image/bmp'],
    self::TYPE_IMAGE_SVG  => ['image/svg'],
    self::TYPE_IMAGE_TIFF => ['image/tiff', 'image/tiff-fx'],
  ];

  private static $s_names_audio = [
    self::TYPE_AUDIO       => ['audio/*'],
    self::TYPE_AUDIO_WAV   => ['audio/wav'],
    self::TYPE_AUDIO_OGG   => ['audio/ogg'],
    self::TYPE_AUDIO_OPUS  => ['audio/opus'],
    self::TYPE_AUDIO_SPEEX => ['audio/speex'],
    self::TYPE_AUDIO_FLAC  => ['audio/flac'],
    self::TYPE_AUDIO_MP3   => ['audio/mpeg'],
    self::TYPE_AUDIO_MP4   => ['audio/mp4'],
    self::TYPE_AUDIO_MIDI  => ['audio/midi'],
    self::TYPE_AUDIO_BASIC  => ['audio/au', 'audio/snd'],
  ];

  private static $s_names_video = [
    self::TYPE_VIDEO           => ['video/*'],
    self::TYPE_VIDEO_MPEG      => ['video/mp4', 'video/mpeg'],
    self::TYPE_VIDEO_OGG       => ['video/ogv'],
    self::TYPE_VIDEO_H264      => ['video/h264', 'video/x264'],
    self::TYPE_VIDEO_QUICKTIME => ['video/qt'],
    self::TYPE_VIDEO_DV        => ['video/dv'],
    self::TYPE_VIDEO_JPEG      => ['video/jpeg', 'video/jpeg2000'],
    self::TYPE_VIDEO_WEBM      => ['video/webm'],
  ];

  private static $s_names_document = [
    self::TYPE_DOCUMENT                   => ['application/*'],
    self::TYPE_DOCUMENT_PDF               => ['application/pdf'],
    self::TYPE_DOCUMENT_LIBRECHART        => ['application/vnd.oasis.opendocument.chart'],
    self::TYPE_DOCUMENT_LIBREFORMULA      => ['application/vnd.oasis.opendocument.formula'],
    self::TYPE_DOCUMENT_LIBREGRAPHIC      => ['application/vnd.oasis.opendocument.graphics'],
    self::TYPE_DOCUMENT_LIBREPRESENTATION => ['application/vnd.oasis.opendocument.presentation'],
    self::TYPE_DOCUMENT_LIBRESPREADSHEET  => ['application/vnd.oasis.opendocument.spreadsheet'],
    self::TYPE_DOCUMENT_LIBRETEXT         => ['application/vnd.oasis.opendocument.text'],
    self::TYPE_DOCUMENT_LIBREHTML         => ['application/vnd.oasis.opendocument.text-web'],
    self::TYPE_DOCUMENT_ABIWORD           => ['application/abiword', 'application/abiword-compressed'],
    self::TYPE_DOCUMENT_MSWORD            => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'],
    self::TYPE_DOCUMENT_MSEXCEL           => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/ms-excel'],
    self::TYPE_DOCUMENT_MSPOWERPOINT      => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/ms-powerpoint'],
  ];

  private static $s_names_container = [
    self::TYPE_CONTAINER      => ['application/*'],
    self::TYPE_CONTAINER_TAR  => ['application/tar'],
    self::TYPE_CONTAINER_CPIO => ['application/cpio'],
    self::TYPE_CONTAINER_JAVA => ['application/java'],
  ];

  private static $s_names_application = [
    self::TYPE_APPLICATION => ['application/*'],
    self::TYPE_APPLICATION_OCSP_REQUEST  => ['application/ocsp-request'],
    self::TYPE_APPLICATION_OCSP_RESPONSE => ['application/ocsp-response'],
  ];

  private static $s_extensions_text = [
    self::TYPE_TEXT         => [],
    self::TYPE_TEXT_PLAIN   => ['txt'],
    self::TYPE_TEXT_HTML    => ['html'],
    self::TYPE_TEXT_RSS     => ['rss', 'rdf'],
    self::TYPE_TEXT_ICAL    => ['ics'],
    self::TYPE_TEXT_CSV     => ['csv'],
    self::TYPE_TEXT_XML     => ['xml'],
    self::TYPE_TEXT_CSS     => ['css'],
    self::TYPE_TEXT_JS      => ['js'],
    self::TYPE_TEXT_JSON    => ['json'],
    self::TYPE_TEXT_RICH    => ['rtf'],
    self::TYPE_TEXT_XHTML   => ['xhtml'],
    self::TYPE_TEXT_PS      => ['ps'],
    self::TYPE_TEXT_FSS     => ['setting'],
  ];

  private static $s_extensions_image = [
    self::TYPE_IMAGE      => [],
    self::TYPE_IMAGE_PNG  => ['png'],
    self::TYPE_IMAGE_GIF  => ['gif'],
    self::TYPE_IMAGE_JPEG => ['jpg', 'jpeg'],
    self::TYPE_IMAGE_BMP  => ['bmp'],
    self::TYPE_IMAGE_SVG  => ['svg'],
    self::TYPE_IMAGE_TIFF => ['tiff'],
  ];

  private static $s_extensions_audio = [
    self::TYPE_AUDIO       => [],
    self::TYPE_AUDIO_WAV   => ['wav'],
    self::TYPE_AUDIO_OGG   => ['ogg'],
    self::TYPE_AUDIO_OPUS  => ['opus', 'ogg'],
    self::TYPE_AUDIO_SPEEX => ['spx'],
    self::TYPE_AUDIO_FLAC  => ['flac'],
    self::TYPE_AUDIO_MP3   => ['mp3', 'mp2', 'mp1'],
    self::TYPE_AUDIO_MP4   => ['mp4', 'mpeg'],
    self::TYPE_AUDIO_MIDI  => ['midi'],
    self::TYPE_AUDIO_BASIC => ['au', 'snd'],
  ];

  private static $s_extensions_video = [
    self::TYPE_VIDEO           => [],
    self::TYPE_VIDEO_MPEG      => ['mp4', 'video/mpeg'],
    self::TYPE_VIDEO_OGG       => ['video/ogv'],
    self::TYPE_VIDEO_H264      => ['video/h264'],
    self::TYPE_VIDEO_QUICKTIME => ['video/qt'],
    self::TYPE_VIDEO_DV        => ['video/dv'],
    self::TYPE_VIDEO_JPEG      => ['video/jpeg', 'video/jpeg2000'],
    self::TYPE_VIDEO_WEBM      => ['video/webm'],
  ];

  private static $s_extensions_document = [
    self::TYPE_DOCUMENT                   => ['application/*'],
    self::TYPE_DOCUMENT_PDF               => ['application/pdf'],
    self::TYPE_DOCUMENT_LIBRECHART        => ['application/vnd.oasis.opendocument.chart'],
    self::TYPE_DOCUMENT_LIBREFORMULA      => ['application/vnd.oasis.opendocument.formula'],
    self::TYPE_DOCUMENT_LIBREGRAPHIC      => ['application/vnd.oasis.opendocument.graphics'],
    self::TYPE_DOCUMENT_LIBREPRESENTATION => ['application/vnd.oasis.opendocument.presentation'],
    self::TYPE_DOCUMENT_LIBRESPREADSHEET  => ['application/vnd.oasis.opendocument.spreadsheet'],
    self::TYPE_DOCUMENT_LIBRETEXT         => ['application/vnd.oasis.opendocument.text'],
    self::TYPE_DOCUMENT_LIBREHTML         => ['application/vnd.oasis.opendocument.text-web'],
    self::TYPE_DOCUMENT_ABIWORD           => ['application/abiword', 'application/abiword-compressed'],
    self::TYPE_DOCUMENT_MSWORD            => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'],
    self::TYPE_DOCUMENT_MSEXCEL           => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/ms-excel'],
    self::TYPE_DOCUMENT_MSPOWERPOINT      => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/ms-powerpoint'],
  ];

  private static $s_extensions_container = [
    self::TYPE_CONTAINER      => ['application/*'],
    self::TYPE_CONTAINER_TAR  => ['application/tar'],
    self::TYPE_CONTAINER_CPIO => ['application/cpio'],
    self::TYPE_CONTAINER_JAVA => ['application/java'],
  ];

  private static $s_extensions_application = [
    self::TYPE_APPLICATION => ['application/*'],
    self::TYPE_APPLICATION_OCSP_REQUEST  => ['application/ocsp-request'],
    self::TYPE_APPLICATION_OCSP_RESPONSE => ['application/ocsp-response'],
  ];


  /**
   * Get the mime-types associated with the id.
   *
   * @param int $id
   *   The id of the mime type to return.
   * @param int|null $category
   *   (optional) search a limited sub-set of ids based on the category.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of mime-types or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given id.
   */
  static function s_get_names_by_id($id, $category = NULL) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($category)) {
      $result = NULL;

      if (array_key_exists($id, static::$s_names_provided)) {
        return c_base_return_array::s_new(static::$s_names_provided[$id]);
      }

      if (array_key_exists($id, static::$s_names_text)) {
        return c_base_return_array::s_new(static::$s_names_text[$id]);
      }

      if (array_key_exists($id, static::$s_names_audio)) {
        return c_base_return_array::s_new(static::$s_names_audio[$id]);
      }

      if (array_key_exists($id, static::$s_names_video)) {
        return c_base_return_array::s_new(static::$s_names_video[$id]);
      }

      if (array_key_exists($id, static::$s_names_document)) {
        return c_base_return_array::s_new(static::$s_names_document[$id]);
      }

      if (array_key_exists($id, static::$s_names_container)) {
        return c_base_return_array::s_new(static::$s_names_container[$id]);
      }

      if (array_key_exists($id, static::$s_names_application)) {
        return c_base_return_array::s_new(static::$s_names_application[$id]);
      }
    }
    else {
      if (!is_int($category)) {
        $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'category', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
      }

      if ($category == static::CATEGORY_PROVIDED) {
        if (array_key_exists($id, static::$s_names_provided)) {
          return c_base_return_array::s_new(static::$s_names_provided[$id]);
        }
      }
      else if ($category == static::CATEGORY_TEXT) {
        if (array_key_exists($id, static::$s_names_text)) {
          return c_base_return_array::s_new(static::$s_names_text[$id]);
        }
      }
      else if ($category == static::CATEGORY_IMAGE) {
        if (array_key_exists($id, static::$s_names_text)) {
          return c_base_return_array::s_new(static::$s_names_text[$id]);
        }
      }
      else if ($category == static::CATEGORY_AUDIO) {
        if (array_key_exists($id, static::$s_names_audio)) {
          return c_base_return_array::s_new(static::$s_names_audio[$id]);
        }
      }
      else if ($category == static::CATEGORY_VIDEO) {
        if (array_key_exists($id, static::$s_names_video)) {
          return c_base_return_array::s_new(static::$s_names_video[$id]);
        }
      }
      else if ($category == static::CATEGORY_DOCUMENT) {
        if (array_key_exists($id, static::$s_names_document)) {
          return c_base_return_array::s_new(static::$s_names_document[$id]);
        }
      }
      else if ($category == static::CATEGORY_CONTAINER) {
        if (array_key_exists($id, static::$s_names_container)) {
          return c_base_return_array::s_new(static::$s_names_container[$id]);
        }
      }
      else if ($category == static::CATEGORY_APPLICATION) {
        if (array_key_exists($id, static::$s_names_application)) {
          return c_base_return_array::s_new(static::$s_names_application[$id]);
        }
      }
    }

    return new c_base_return_false();
  }

  /**
   * Identifies the mime-type and returns relevant information.
   *
   * @param string $mime
   *   The mime-type string to identify.
   * @param bool $lowercase
   *   (optional) When TRUE, the passed string will be auto-forced to be lower-case.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of containing the following:
   *   - 'id_category': An integer representing the mime-type category.
   *   - 'id_type': An integer representing the mime-type.
   *   - 'name_category': A (lowercase) string representing the category part of the mime-type.
   *   - 'name_type': A (lowercase) string representing the type part of the mime-type.
   *   Both category and type ids may have appropriate UNKNOWN values to for valid mime-type formats of unknown mime-type strings.
   *   FALSE with the error bit set is returned on error.
   *   FALSE without the error flag means for an invalid mime-type string.
   *
   * @see: c_base_utf8::s_lowercase()
   */
  static function s_identify($mime, $lowercase = FALSE) {
    if (!is_string($mime)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'mime', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($lowercase)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'lowercase', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($lowercase) {
      $lower_mime = (string) c_base_utf8::s_lowercase($mime)->get_value();
    }
    else {
      $lower_mime = $mime;
    }

    $information = [
      'id_category' => static::CATEGORY_PROVIDED,
      'id_type' => static::TYPE_PROVIDED,
      'name_category' => '*',
      'name_type' => '*',
    ];

    if ($mime == '*/*') {
      return c_base_return_array::s_new($information);
    }

    $parts = mb_split('/', $lower_mime);

    if (count($parts) != 2) {
      // there is no valid value to process.
      unset($parts);
      unset($lower_mime);
      unset($information);
      return new c_base_return_false();
    }

    $information['name_category'] = $parts[0];
    $information['name_type'] = $parts[1];

    if ($parts[0] == 'text') {
      $information['id_category'] = static::CATEGORY_TEXT;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      else if ($parts[1] == 'html') {
        $information['id_type'] = static::TYPE_TEXT_HTML;
      }
      else if ($parts[1] == 'plain') {
        $information['id_type'] = static::TYPE_TEXT_PLAIN;
      }
      else if ($parts[1] == 'calendar') {
        $information['id_type'] = static::TYPE_TEXT_ICAL;
      }
      else if ($parts[1] == 'csv') {
        $information['id_type'] = static::TYPE_TEXT_CSV;
      }
      else if ($parts[1] == 'xml') {
        $information['id_type'] = static::TYPE_TEXT_XML;
      }
      else if ($parts[1] == 'css') {
        $information['id_type'] = static::TYPE_TEXT_CSS;
      }
      else if ($parts[1] == 'rtf') {
        $information['id_type'] = static::TYPE_TEXT_RICH;
      }
      else if ($parts[1] == 'javascript') {
        $information['id_type'] = static::TYPE_TEXT_JS;
      }
      else {
        $information['id_type'] = static::TYPE_UNKNOWN;
      }
    }
    else if ($parts[0] == 'application') {
      $information['id_category'] = static::CATEGORY_APPLICATION;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      else if ($parts[1] == 'octet-stream') {
        $information['id_category'] = static::CATEGORY_STREAM;
        $information['id_type'] = static::TYPE_STREAM;
      }
      else if ($parts[1] == 'pdf') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_APPLICATION_PDF;
      }
      else if ($parts[1] == 'rss' || $parts[1] == 'rss+xml' || $parts[1] == 'rdf+xml' || $parts[1] == 'atom+xml') {
        $information['id_category'] = static::CATEGORY_TEXT;
        $information['id_type'] = static::TYPE_TEXT_RSS;
      }
      else if ($parts[1] == 'xml') {
        $information['id_category'] = static::CATEGORY_TEXT;
        $information['id_type'] = static::TYPE_TEXT_XML;
      }
      else if ($parts[1] == 'javascript') {
        $information['id_category'] = static::CATEGORY_TEXT;
        $information['id_type'] = static::TYPE_TEXT_JS;
      }
      else if ($parts[1] == 'json') {
        $information['id_category'] = static::CATEGORY_TEXT;
        $information['id_type'] = static::TYPE_TEXT_JSON;
      }
      else if ($parts[1] == 'xhtml' || $parts[1] == 'xhtml+xml') {
        $information['id_category'] = static::CATEGORY_TEXT;
        $information['id_type'] = static::TYPE_TEXT_XHTML;
      }
      else if ($parts[1] == 'ps') {
        $information['id_category'] = static::CATEGORY_TEXT;
        $information['id_type'] = static::TYPE_TEXT_PS;
      }
      else if ($parts[1] == 'tar') {
        $information['id_category'] = static::CATEGORY_CONTAINER;
        $information['id_type'] = static::TYPE_CONTAINER_TAR;
      }
      else if ($parts[1] == 'cpio') {
        $information['id_category'] = static::CATEGORY_CONTAINER;
        $information['id_type'] = static::TYPE_CONTAINER_CPIO;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.chart') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBRECHART;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.formula') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBREFORMULA;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.graphics') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBREGRAPHIC;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.presentation') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBREPRESENTATION;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.spreadsheet') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBRESPREADSHEET;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.text') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBRETEXT;
      }
      else if ($parts[1] == 'vnd.oasis.opendocument.text-web') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_LIBREHTML;
      }
      else if ($parts[1] == 'abiword' || $parts[1] == 'abiword-compressed') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_ABIWORD;
      }
      else if ($parts[1] == 'msword' || $parts[1] == 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_MSWORD;
      }
      else if ($parts[1] == 'ms-excel' || $parts[1] == 'vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_MSEXCEL;
      }
      else if ($parts[1] == 'ms-powerpoint' || $parts[1] == 'vnd.openxmlformats-officedocument.presentationml.presentation') {
        $information['id_category'] = static::CATEGORY_DOCUMENT;
        $information['id_type'] = static::TYPE_DOCUMENT_MSPOWERPOINT;
      }
      else if ($parts[1] == 'java') {
        $information['id_category'] = static::CATEGORY_CONTAINER;
        $information['id_type'] = static::TYPE_CONTAINER_JAVA;
      }
      else if ($parts[1] == 'ocsp-request') {
        $information['id_category'] = static::CATEGORY_APPLICATION;
        $information['id_type'] = static::TYPE_PACKET_OCSP_REQUEST;
      }
      else if ($parts[1] == 'ocsp-response') {
        $information['id_category'] = static::CATEGORY_APPLICATION;
        $information['id_type'] = static::TYPE_PACKET_OCSP_RESPONSE;
      }
      else {
        $information['id_type'] = static::TYPE_UNKNOWN;
      }
    }
    else if ($parts[0] == 'image') {
      $information['id_category'] = static::CATEGORY_IMAGE;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      else if ($parts[1] == 'png') {
        $information['id_type'] = static::TYPE_IMAGE_PNG;
      }
      else if ($parts[1] == 'jpeg' || $parts[1] == 'jpg' || $parts[1] == 'jpx') {
        $information['id_type'] = static::TYPE_IMAGE_JPEG;
      }
      else if ($parts[1] == 'gif') {
        $information['id_type'] = static::TYPE_IMAGE_GIF;
      }
      else if ($parts[1] == 'bmp') {
        $information['id_type'] = static::TYPE_IMAGE_BMP;
      }
      else if ($parts[1] == 'svg') {
        $information['id_type'] = static::TYPE_IMAGE_SVG;
      }
      else if ($parts[1] == 'tiff' || $parts[1] == 'tiff-fx') {
        $information['id_type'] = static::TYPE_IMAGE_TIFF;
      }
      else {
        $information['id_type'] = static::TYPE_UNKNOWN;
      }
    }
    else if ($parts[0] == 'audio') {
      $information['id_category'] = static::CATEGORY_AUDIO;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      else if ($parts[1] == 'ogg') {
        $information['id_type'] = static::TYPE_AUDIO_OGG;
      }
      else if ($parts[1] == 'mpeg') {
        $information['id_type'] = static::TYPE_AUDIO_MP3;
      }
      else if ($parts[1] == 'mp4') {
        $information['id_type'] = static::TYPE_AUDIO_MP4;
      }
      else if ($parts[1] == 'wav') {
        $information['id_type'] = static::TYPE_AUDIO_WAV;
      }
      else if ($parts[1] == 'midi') {
        $information['id_type'] = static::TYPE_AUDIO_MIDI;
      }
      else {
        $information['id_type'] = static::TYPE_UNKNOWN;
      }
    }
    else if ($parts[0] == 'video') {
      $information['id_category'] = static::CATEGORY_VIDEO;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      else if ($parts[1] == 'mp4' || $parts[1] == 'mpeg') {
        $information['id_type'] = static::TYPE_VIDEO_MPEG;
      }
      else if ($parts[1] == 'ogg') {
        $information['id_type'] = static::TYPE_VIDEO_OGG;
      }
      else if ($parts[1] == 'h264') {
        $information['id_type'] = static::TYPE_VIDEO_H264;
      }
      else if ($parts[1] == 'quicktime') {
        $information['id_type'] = static::TYPE_VIDEO_QUICKTIME;
      }
      else if ($parts[1] == 'dv') {
        $information['id_type'] = static::TYPE_VIDEO_DV;
      }
      else if ($parts[1] == 'jpeg' || $parts[1] == 'jpeg2000') {
        $information['id_type'] = static::TYPE_VIDEO_JPEG;
      }
      else if ($parts[1] == 'webm') {
        $information['id_type'] = static::TYPE_VIDEO_WEBM;
      }
      else {
        $information['id_type'] = static::TYPE_UNKNOWN;
      }
    }
    else {
      $information['id_category'] = static::CATEGORY_UNKNOWN;
      $information['id_type'] = static::TYPE_UNKNOWN;
    }
    unset($parts);

    unset($lower_mime);
    return c_base_return_array::s_new($information);
  }
}
