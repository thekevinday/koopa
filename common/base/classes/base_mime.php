<?php
/**
 * @file
 * Provides a class for managing mime-type information.
 */

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


  private static $s_names_provided = array(
    self::TYPE_PROVIDED  => array('*/*', 'text/*', 'image/*', 'audio/*', 'video/*', 'application/*'),
    self::TYPE_STREAM    => array('application/octet-stream'),
    self::TYPE_DATA_FORM => array('multipart/form-data'),
    self::TYPE_DATA_URL => array('application/x-www-form-urlencoded'),
  );

  private static $s_names_text = array(
    self::TYPE_TEXT         => array('text/*'),
    self::TYPE_TEXT_PLAIN   => array('text/plain'),
    self::TYPE_TEXT_HTML    => array('text/html'),
    self::TYPE_TEXT_RSS     => array('application/rss', 'application/rss+xml', 'application/rdf+xml', 'application/atom+xml'),
    self::TYPE_TEXT_ICAL    => array('text/calendar'),
    self::TYPE_TEXT_CSV     => array('text/csv'),
    self::TYPE_TEXT_XML     => array('application/xml'),
    self::TYPE_TEXT_CSS     => array('text/css'),
    self::TYPE_TEXT_JS      => array('text/javascript', 'application/javascript'),
    self::TYPE_TEXT_JSON    => array('text/json', 'application/json'),
    self::TYPE_TEXT_RICH    => array('text/rtf'),
    self::TYPE_TEXT_XHTML   => array('application/xhtml', 'application/xhtml+xml'),
    self::TYPE_TEXT_PS      => array('text/ps'),
    self::TYPE_TEXT_FSS     => array('text/fss'),
  );

  private static $s_names_image = array(
    self::TYPE_IMAGE      => array('image/*'),
    self::TYPE_IMAGE_PNG  => array('image/png'),
    self::TYPE_IMAGE_GIF  => array('image/gif'),
    self::TYPE_IMAGE_JPEG => array('image/jpeg', 'image/jpg', 'image/jpx'),
    self::TYPE_IMAGE_BMP  => array('image/bmp'),
    self::TYPE_IMAGE_SVG  => array('image/svg'),
    self::TYPE_IMAGE_TIFF => array('image/tiff', 'image/tiff-fx'),
  );

  private static $s_names_audio = array(
    self::TYPE_AUDIO       => array('audio/*'),
    self::TYPE_AUDIO_WAV   => array('audio/wav'),
    self::TYPE_AUDIO_OGG   => array('audio/ogg'),
    self::TYPE_AUDIO_OPUS  => array('audio/opus'),
    self::TYPE_AUDIO_SPEEX => array('audio/speex'),
    self::TYPE_AUDIO_FLAC  => array('audio/flac'),
    self::TYPE_AUDIO_MP3   => array('audio/mpeg'),
    self::TYPE_AUDIO_MP4   => array('audio/mp4'),
    self::TYPE_AUDIO_MIDI  => array('audio/midi'),
    self::TYPE_AUDIO_BASIC  => array('audio/au', 'audio/snd'),
  );

  private static $s_names_video = array(
    self::TYPE_VIDEO           => array('video/*'),
    self::TYPE_VIDEO_MPEG      => array('video/mp4', 'video/mpeg'),
    self::TYPE_VIDEO_OGG       => array('video/ogv'),
    self::TYPE_VIDEO_H264      => array('video/h264', 'video/x264'),
    self::TYPE_VIDEO_QUICKTIME => array('video/qt'),
    self::TYPE_VIDEO_DV        => array('video/dv'),
    self::TYPE_VIDEO_JPEG      => array('video/jpeg', 'video/jpeg2000'),
    self::TYPE_VIDEO_WEBM      => array('video/webm'),
  );

  private static $s_names_document = array(
    self::TYPE_DOCUMENT                   => array('application/*'),
    self::TYPE_DOCUMENT_PDF               => array('application/pdf'),
    self::TYPE_DOCUMENT_LIBRECHART        => array('application/vnd.oasis.opendocument.chart'),
    self::TYPE_DOCUMENT_LIBREFORMULA      => array('application/vnd.oasis.opendocument.formula'),
    self::TYPE_DOCUMENT_LIBREGRAPHIC      => array('application/vnd.oasis.opendocument.graphics'),
    self::TYPE_DOCUMENT_LIBREPRESENTATION => array('application/vnd.oasis.opendocument.presentation'),
    self::TYPE_DOCUMENT_LIBRESPREADSHEET  => array('application/vnd.oasis.opendocument.spreadsheet'),
    self::TYPE_DOCUMENT_LIBRETEXT         => array('application/vnd.oasis.opendocument.text'),
    self::TYPE_DOCUMENT_LIBREHTML         => array('application/vnd.oasis.opendocument.text-web'),
    self::TYPE_DOCUMENT_ABIWORD           => array('application/abiword', 'application/abiword-compressed'),
    self::TYPE_DOCUMENT_MSWORD            => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'),
    self::TYPE_DOCUMENT_MSEXCEL           => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/ms-excel'),
    self::TYPE_DOCUMENT_MSPOWERPOINT      => array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/ms-powerpoint'),
  );

  private static $s_names_container = array(
    self::TYPE_CONTAINER      => array('application/*'),
    self::TYPE_CONTAINER_TAR  => array('application/tar'),
    self::TYPE_CONTAINER_CPIO => array('application/cpio'),
    self::TYPE_CONTAINER_JAVA => array('application/java'),
  );

  private static $s_names_application = array(
    self::TYPE_APPLICATION => array('application/*'),
    self::TYPE_APPLICATION_OCSP_REQUEST  => array('application/ocsp-request'),
    self::TYPE_APPLICATION_OCSP_RESPONSE => array('application/ocsp-response'),
  );

  private static $s_extensions_text = array(
    self::TYPE_TEXT         => array(),
    self::TYPE_TEXT_PLAIN   => array('txt'),
    self::TYPE_TEXT_HTML    => array('html'),
    self::TYPE_TEXT_RSS     => array('rss', 'rdf'),
    self::TYPE_TEXT_ICAL    => array('ics'),
    self::TYPE_TEXT_CSV     => array('csv'),
    self::TYPE_TEXT_XML     => array('xml'),
    self::TYPE_TEXT_CSS     => array('css'),
    self::TYPE_TEXT_JS      => array('js'),
    self::TYPE_TEXT_JSON    => array('json'),
    self::TYPE_TEXT_RICH    => array('rtf'),
    self::TYPE_TEXT_XHTML   => array('xhtml'),
    self::TYPE_TEXT_PS      => array('ps'),
    self::TYPE_TEXT_FSS     => array('setting'),
  );

  private static $s_extensions_image = array(
    self::TYPE_IMAGE      => array(),
    self::TYPE_IMAGE_PNG  => array('png'),
    self::TYPE_IMAGE_GIF  => array('gif'),
    self::TYPE_IMAGE_JPEG => array('jpg', 'jpeg'),
    self::TYPE_IMAGE_BMP  => array('bmp'),
    self::TYPE_IMAGE_SVG  => array('svg'),
    self::TYPE_IMAGE_TIFF => array('tiff'),
  );

  private static $s_extensions_audio = array(
    self::TYPE_AUDIO       => array(),
    self::TYPE_AUDIO_WAV   => array('wav'),
    self::TYPE_AUDIO_OGG   => array('ogg'),
    self::TYPE_AUDIO_OPUS  => array('opus', 'ogg'),
    self::TYPE_AUDIO_SPEEX => array('spx'),
    self::TYPE_AUDIO_FLAC  => array('flac'),
    self::TYPE_AUDIO_MP3   => array('mp3', 'mp2', 'mp1'),
    self::TYPE_AUDIO_MP4   => array('mp4', 'mpeg'),
    self::TYPE_AUDIO_MIDI  => array('midi'),
    self::TYPE_AUDIO_BASIC => array('au', 'snd'),
  );

  private static $s_extensions_video = array(
    self::TYPE_VIDEO           => array(),
    self::TYPE_VIDEO_MPEG      => array('mp4', 'video/mpeg'),
    self::TYPE_VIDEO_OGG       => array('video/ogv'),
    self::TYPE_VIDEO_H264      => array('video/h264'),
    self::TYPE_VIDEO_QUICKTIME => array('video/qt'),
    self::TYPE_VIDEO_DV        => array('video/dv'),
    self::TYPE_VIDEO_JPEG      => array('video/jpeg', 'video/jpeg2000'),
    self::TYPE_VIDEO_WEBM      => array('video/webm'),
  );

  private static $s_extensions_document = array(
    self::TYPE_DOCUMENT                   => array('application/*'),
    self::TYPE_DOCUMENT_PDF               => array('application/pdf'),
    self::TYPE_DOCUMENT_LIBRECHART        => array('application/vnd.oasis.opendocument.chart'),
    self::TYPE_DOCUMENT_LIBREFORMULA      => array('application/vnd.oasis.opendocument.formula'),
    self::TYPE_DOCUMENT_LIBREGRAPHIC      => array('application/vnd.oasis.opendocument.graphics'),
    self::TYPE_DOCUMENT_LIBREPRESENTATION => array('application/vnd.oasis.opendocument.presentation'),
    self::TYPE_DOCUMENT_LIBRESPREADSHEET  => array('application/vnd.oasis.opendocument.spreadsheet'),
    self::TYPE_DOCUMENT_LIBRETEXT         => array('application/vnd.oasis.opendocument.text'),
    self::TYPE_DOCUMENT_LIBREHTML         => array('application/vnd.oasis.opendocument.text-web'),
    self::TYPE_DOCUMENT_ABIWORD           => array('application/abiword', 'application/abiword-compressed'),
    self::TYPE_DOCUMENT_MSWORD            => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'),
    self::TYPE_DOCUMENT_MSEXCEL           => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/ms-excel'),
    self::TYPE_DOCUMENT_MSPOWERPOINT      => array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/ms-powerpoint'),
  );

  private static $s_extensions_container = array(
    self::TYPE_CONTAINER      => array('application/*'),
    self::TYPE_CONTAINER_TAR  => array('application/tar'),
    self::TYPE_CONTAINER_CPIO => array('application/cpio'),
    self::TYPE_CONTAINER_JAVA => array('application/java'),
  );

  private static $s_extensions_application = array(
    self::TYPE_APPLICATION => array('application/*'),
    self::TYPE_APPLICATION_OCSP_REQUEST  => array('application/ocsp-request'),
    self::TYPE_APPLICATION_OCSP_RESPONSE => array('application/ocsp-response'),
  );


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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($category)) {
      $result = NULL;

      if (array_key_exists($id, self::$s_names_provided)) {
        return c_base_return_array::s_new(self::$s_names_provided[$id]);
      }

      if (array_key_exists($id, self::$s_names_text)) {
        return c_base_return_array::s_new(self::$s_names_text[$id]);
      }

      if (array_key_exists($id, self::$s_names_audio)) {
        return c_base_return_array::s_new(self::$s_names_audio[$id]);
      }

      if (array_key_exists($id, self::$s_names_video)) {
        return c_base_return_array::s_new(self::$s_names_video[$id]);
      }

      if (array_key_exists($id, self::$s_names_document)) {
        return c_base_return_array::s_new(self::$s_names_document[$id]);
      }

      if (array_key_exists($id, self::$s_names_container)) {
        return c_base_return_array::s_new(self::$s_names_container[$id]);
      }

      if (array_key_exists($id, self::$s_names_application)) {
        return c_base_return_array::s_new(self::$s_names_application[$id]);
      }
    }
    else {
      if (!is_int($category)) {
        $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'category', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
      }

      if ($category == self::CATEGORY_PROVIDED) {
        if (array_key_exists($id, self::$s_names_provided)) {
          return c_base_return_array::s_new(self::$s_names_provided[$id]);
        }
      }
      elseif ($category == self::CATEGORY_TEXT) {
        if (array_key_exists($id, self::$s_names_text)) {
          return c_base_return_array::s_new(self::$s_names_text[$id]);
        }
      }
      elseif ($category == self::CATEGORY_IMAGE) {
        if (array_key_exists($id, self::$s_names_text)) {
          return c_base_return_array::s_new(self::$s_names_text[$id]);
        }
      }
      elseif ($category == self::CATEGORY_AUDIO) {
        if (array_key_exists($id, self::$s_names_audio)) {
          return c_base_return_array::s_new(self::$s_names_audio[$id]);
        }
      }
      elseif ($category == self::CATEGORY_VIDEO) {
        if (array_key_exists($id, self::$s_names_video)) {
          return c_base_return_array::s_new(self::$s_names_video[$id]);
        }
      }
      elseif ($category == self::CATEGORY_DOCUMENT) {
        if (array_key_exists($id, self::$s_names_document)) {
          return c_base_return_array::s_new(self::$s_names_document[$id]);
        }
      }
      elseif ($category == self::CATEGORY_CONTAINER) {
        if (array_key_exists($id, self::$s_names_container)) {
          return c_base_return_array::s_new(self::$s_names_container[$id]);
        }
      }
      elseif ($category == self::CATEGORY_APPLICATION) {
        if (array_key_exists($id, self::$s_names_application)) {
          return c_base_return_array::s_new(self::$s_names_application[$id]);
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
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'mime', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($lowercase)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'lowercase', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($lowercase) {
      $lower_mime = (string) c_base_utf8::s_lowercase($mime)->get_value();
    }
    else {
      $lower_mime = $mime;
    }

    $information = array(
      'id_category' => self::CATEGORY_PROVIDED,
      'id_type' => self::TYPE_PROVIDED,
      'name_category' => '*',
      'name_type' => '*',
    );

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
      $information['id_category'] = self::CATEGORY_TEXT;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      elseif ($parts[1] == 'html') {
        $information['id_type'] = self::TYPE_TEXT_HTML;
      }
      elseif ($parts[1] == 'plain') {
        $information['id_type'] = self::TYPE_TEXT_PLAIN;
      }
      elseif ($parts[1] == 'calendar') {
        $information['id_type'] = self::TYPE_TEXT_ICAL;
      }
      elseif ($parts[1] == 'csv') {
        $information['id_type'] = self::TYPE_TEXT_CSV;
      }
      elseif ($parts[1] == 'xml') {
        $information['id_type'] = self::TYPE_TEXT_XML;
      }
      elseif ($parts[1] == 'css') {
        $information['id_type'] = self::TYPE_TEXT_CSS;
      }
      elseif ($parts[1] == 'rtf') {
        $information['id_type'] = self::TYPE_TEXT_RICH;
      }
      elseif ($parts[1] == 'javascript') {
        $information['id_type'] = self::TYPE_TEXT_JS;
      }
      else {
        $information['id_type'] = self::TYPE_UNKNOWN;
      }
    }
    elseif ($parts[0] == 'application') {
      $information['id_category'] = self::CATEGORY_APPLICATION;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      elseif ($parts[1] == 'octet-stream') {
        $information['id_category'] = self::CATEGORY_STREAM;
        $information['id_type'] = self::TYPE_STREAM;
      }
      elseif ($parts[1] == 'pdf') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_APPLICATION_PDF;
      }
      elseif ($parts[1] == 'rss' || $parts[1] == 'rss+xml' || $parts[1] == 'rdf+xml' || $parts[1] == 'atom+xml') {
        $information['id_category'] = self::CATEGORY_TEXT;
        $information['id_type'] = self::TYPE_TEXT_RSS;
      }
      elseif ($parts[1] == 'xml') {
        $information['id_category'] = self::CATEGORY_TEXT;
        $information['id_type'] = self::TYPE_TEXT_XML;
      }
      elseif ($parts[1] == 'javascript') {
        $information['id_category'] = self::CATEGORY_TEXT;
        $information['id_type'] = self::TYPE_TEXT_JS;
      }
      elseif ($parts[1] == 'json') {
        $information['id_category'] = self::CATEGORY_TEXT;
        $information['id_type'] = self::TYPE_TEXT_JSON;
      }
      elseif ($parts[1] == 'xhtml' || $parts[1] == 'xhtml+xml') {
        $information['id_category'] = self::CATEGORY_TEXT;
        $information['id_type'] = self::TYPE_TEXT_XHTML;
      }
      elseif ($parts[1] == 'ps') {
        $information['id_category'] = self::CATEGORY_TEXT;
        $information['id_type'] = self::TYPE_TEXT_PS;
      }
      elseif ($parts[1] == 'tar') {
        $information['id_category'] = self::CATEGORY_CONTAINER;
        $information['id_type'] = self::TYPE_CONTAINER_TAR;
      }
      elseif ($parts[1] == 'cpio') {
        $information['id_category'] = self::CATEGORY_CONTAINER;
        $information['id_type'] = self::TYPE_CONTAINER_CPIO;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.chart') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBRECHART;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.formula') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBREFORMULA;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.graphics') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBREGRAPHIC;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.presentation') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBREPRESENTATION;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.spreadsheet') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBRESPREADSHEET;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.text') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBRETEXT;
      }
      elseif ($parts[1] == 'vnd.oasis.opendocument.text-web') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_LIBREHTML;
      }
      elseif ($parts[1] == 'abiword' || $parts[1] == 'abiword-compressed') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_ABIWORD;
      }
      elseif ($parts[1] == 'msword' || $parts[1] == 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_MSWORD;
      }
      elseif ($parts[1] == 'ms-excel' || $parts[1] == 'vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_MSEXCEL;
      }
      elseif ($parts[1] == 'ms-powerpoint' || $parts[1] == 'vnd.openxmlformats-officedocument.presentationml.presentation') {
        $information['id_category'] = self::CATEGORY_DOCUMENT;
        $information['id_type'] = self::TYPE_DOCUMENT_MSPOWERPOINT;
      }
      elseif ($parts[1] == 'java') {
        $information['id_category'] = self::CATEGORY_CONTAINER;
        $information['id_type'] = self::TYPE_CONTAINER_JAVA;
      }
      elseif ($parts[1] == 'ocsp-request') {
        $information['id_category'] = self::CATEGORY_APPLICATION;
        $information['id_type'] = self::TYPE_PACKET_OCSP_REQUEST;
      }
      elseif ($parts[1] == 'ocsp-response') {
        $information['id_category'] = self::CATEGORY_APPLICATION;
        $information['id_type'] = self::TYPE_PACKET_OCSP_RESPONSE;
      }
      else {
        $information['id_type'] = self::TYPE_UNKNOWN;
      }
    }
    elseif ($parts[0] == 'image') {
      $information['id_category'] = self::CATEGORY_IMAGE;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      elseif ($parts[1] == 'png') {
        $information['id_type'] = self::TYPE_IMAGE_PNG;
      }
      elseif ($parts[1] == 'jpeg' || $parts[1] == 'jpg' || $parts[1] == 'jpx') {
        $information['id_type'] = self::TYPE_IMAGE_JPEG;
      }
      elseif ($parts[1] == 'gif') {
        $information['id_type'] = self::TYPE_IMAGE_GIF;
      }
      elseif ($parts[1] == 'bmp') {
        $information['id_type'] = self::TYPE_IMAGE_BMP;
      }
      elseif ($parts[1] == 'svg') {
        $information['id_type'] = self::TYPE_IMAGE_SVG;
      }
      elseif ($parts[1] == 'tiff' || $parts[1] == 'tiff-fx') {
        $information['id_type'] = self::TYPE_IMAGE_TIFF;
      }
      else {
        $information['id_type'] = self::TYPE_UNKNOWN;
      }
    }
    elseif ($parts[0] == 'audio') {
      $information['id_category'] = self::CATEGORY_AUDIO;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      elseif ($parts[1] == 'ogg') {
        $information['id_type'] = self::TYPE_AUDIO_OGG;
      }
      elseif ($parts[1] == 'mpeg') {
        $information['id_type'] = self::TYPE_AUDIO_MP3;
      }
      elseif ($parts[1] == 'mp4') {
        $information['id_type'] = self::TYPE_AUDIO_MP4;
      }
      elseif ($parts[1] == 'wav') {
        $information['id_type'] = self::TYPE_AUDIO_WAV;
      }
      elseif ($parts[1] == 'midi') {
        $information['id_type'] = self::TYPE_AUDIO_MIDI;
      }
      else {
        $information['id_type'] = self::TYPE_UNKNOWN;
      }
    }
    elseif ($parts[0] == 'video') {
      $information['id_category'] = self::CATEGORY_VIDEO;

      if ($parts[1] == '*') {
        // nothing to change.
      }
      elseif ($parts[1] == 'mp4' || $parts[1] == 'mpeg') {
        $information['id_type'] = self::TYPE_VIDEO_MPEG;
      }
      elseif ($parts[1] == 'ogg') {
        $information['id_type'] = self::TYPE_VIDEO_OGG;
      }
      elseif ($parts[1] == 'h264') {
        $information['id_type'] = self::TYPE_VIDEO_H264;
      }
      elseif ($parts[1] == 'quicktime') {
        $information['id_type'] = self::TYPE_VIDEO_QUICKTIME;
      }
      elseif ($parts[1] == 'dv') {
        $information['id_type'] = self::TYPE_VIDEO_DV;
      }
      elseif ($parts[1] == 'jpeg' || $parts[1] == 'jpeg2000') {
        $information['id_type'] = self::TYPE_VIDEO_JPEG;
      }
      elseif ($parts[1] == 'webm') {
        $information['id_type'] = self::TYPE_VIDEO_WEBM;
      }
      else {
        $information['id_type'] = self::TYPE_UNKNOWN;
      }
    }
    else {
      $information['id_category'] = self::CATEGORY_UNKNOWN;
      $information['id_type'] = self::TYPE_UNKNOWN;
    }
    unset($parts);

    unset($lower_mime);
    return c_base_return_array::s_new($information);
  }
}
