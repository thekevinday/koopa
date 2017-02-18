<?php
/**
 * @file
 * Provides reservation response functions.
 */
  require_once('../../common/base/classes/base_error.php');
  require_once('../../common/base/classes/base_return.php');

function reservation_build_respone($markup) {
  $http->set_response_content($markup);
}
