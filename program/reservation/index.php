<?php
namespace n_reservation;
use n_koopa;

// assign custom include path.
set_include_path('/var/git/koopa');

// load the project-specific global defaults file.
require_once('program/reservation/reservation_defaults_global.php');

require_once('common/standard/classes/standard_index.php');


/**
 * The standard class for use in index.php or equivalent.
 */
class c_reservation_index extends \n_koopa\c_standard_index {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->settings['database_name']        = 'reservation';
    $this->settings['database_user_public'] = 'u_reservation_public';

    $this->settings['session_system'] = 'reservation';

    $this->settings['cookie_name'] = 'reservation-session';

    $this->settings['system_name'] = $this->settings['session_system'];
    $this->settings['base_css'] = 'reservation-';
  }
}

$index = new c_reservation_index();
$index->do_execute();
unset($index);
