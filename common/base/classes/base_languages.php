<?php
/**
 * @file
 * Provides classes for managing the different supported languages.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

require_once('common/base/interfaces/base_languages.php');

/**
 * A language class specifically for english only languages.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
final class c_base_languages_us_only implements i_base_languages {

  private static $s_aliases = [
    self::ENGLISH_US              => ['en-us'],
    self::ENGLISH                 => ['eng', 'en'],
    self::UNDETERMINED            => ['und'],
    self::NOT_APPLICABLE          => ['zxx'],
  ];

  private static $s_names = [
    self::ENGLISH_US              => ['US English'],
    self::ENGLISH                 => ['English'],
    self::UNDETERMINED            => ['Undetermined'],
    self::NOT_APPLICABLE          => ['No Linguistic Content', 'Not Applicable'],
  ];

  private static $s_ids = [
    'en-us' => self::ENGLISH_US,
    'eng'   => self::ENGLISH,
    'en'    => self::ENGLISH,
    'und'   => self::UNDETERMINED,
    'zxx'   => self::NOT_APPLICABLE,
  ];

  private static $s_rtl_ids = [
  ];


  /**
   * Implementation of s_get_names_by_id().
   */
  public static function s_get_names_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($id, self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$id]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_names_by_alias().
   */
  public static function s_get_names_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($alias, self::$s_ids) && array_key_exists(self::$s_ids[$alias], self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$self::$s_ids[$alias]]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_id_by_name().
   */
  public static function s_get_id_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    if (array_key_exists($name, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$name]);
    }

    return new c_base_return_int();
  }

  /**
   * Implementation of s_get_id_by_alias().
   */
  public static function s_get_id_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    if (array_key_exists($alias, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$alias]);
    }

    return new c_base_return_int();
  }

  /**
   * Implementation of s_get_aliases_by_id().
   */
  public static function s_get_aliases_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_aliases_by_name().
   */
  public static function s_get_aliases_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($name, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$name]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_default_id().
   */
  public static function s_get_default_id() {
    return c_base_return_int::s_new(self::ENGLISH_US);
  }

  /**
   * Implementation of s_get_default_name().
   */
  public static function s_get_default_name() {
    return c_base_return_string::s_new($this->s_aliases[self::ENGLISH_US]);
  }

  /**
   * Implementation of s_get_ids().
   */
  public static function s_get_ids() {
    $ids = [];
    foreach (self::$s_aliases as $key => $value) {
      $ids[$key] = $key;
    }
    unset($key);
    unset($value);

    return c_base_return_array::s_new($ids);
  }

  /**
   * Implementation of s_get_aliases().
   */
  public static function s_get_aliases() {
    return c_base_return_array::s_new(self::$s_aliases);
  }

  /**
   * Implementation of s_get_names().
   */
  public static function s_get_names() {
    return c_base_return_array::s_new(self::$s_names);
  }

  /**
   * Implementation of s_get_ltr_by_id().
   */
  public static function s_get_ltr_by_id($id) {
    if (array_key_exists($id, self::$s_rtl_ids)) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }
}

/**
 * A generic class for managing the different supported languages.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
final class c_base_languages_limited implements i_base_languages {

  private static $s_aliases = [
    self::ENGLISH_US              => ['en-us'],
    self::ENGLISH                 => ['eng', 'en'],
    self::FRENCH                  => ['fre', 'fra', 'fr'],
    self::GAELIC                  => ['gla', 'gd'],
    self::IRISH                   => ['gle', 'ga'],
    self::SPANISH                 => ['spa', 'es'],
    self::INDONESIAN              => ['ind', 'id'],
    self::JAPANESE                => ['jpn', 'ja'],
    self::RUSSIAN                 => ['rus', 'ru'],
    self::CHINESE                 => ['chi', 'zho', 'zh'],
    self::UNDETERMINED            => ['und'],
    self::NOT_APPLICABLE          => ['zxx'],
  ];

  private static $s_names = [
    self::ENGLISH_US              => ['US English'],
    self::ENGLISH                 => ['English'],
    self::FRENCH                  => ['French'],
    self::GAELIC                  => ['Gaelic', 'Scottish Gaelic'],
    self::IRISH                   => ['Irish'],
    self::SPANISH                 => ['Spanish', 'Castilian'],
    self::INDONESIAN              => ['Indonesian'],
    self::JAPANESE                => ['Japanese'],
    self::RUSSIAN                 => ['Russian'],
    self::CHINESE                 => ['Chinese'],
    self::UNDETERMINED            => ['Undetermined'],
    self::NOT_APPLICABLE          => ['No Linguistic Content', 'Not Applicable'],
  ];

  private static $s_ids = [
    'en-us' => self::ENGLISH_US,
    'eng'   => self::ENGLISH,
    'en'    => self::ENGLISH,
    'fre'   => self::FRENCH,
    'fra'   => self::FRENCH,
    'fr'    => self::FRENCH,
    'gla'   => self::GAELIC,
    'ga'    => self::GAELIC,
    'gle'   => self::IRISH,
    'ga'    => self::IRISH,
    'spa'   => self::SPANISH,
    'es'    => self::SPANISH,
    'ind'   => self::INDONESIAN,
    'id'    => self::INDONESIAN,
    'jpn'   => self::JAPANESE,
    'ja'    => self::JAPANESE,
    'rus'   => self::RUSSIAN,
    'ru'    => self::RUSSIAN,
    'chi'   => self::CHINESE,
    'zho'   => self::CHINESE,
    'zh'    => self::CHINESE,
    'und'   => self::UNDETERMINED,
    'zxx'   => self::NOT_APPLICABLE,
  ];

  private static $s_rtl_ids = [
    // @todo: populate this with $id => $id.
  ];


  /**
   * Implementation of s_get_names_by_id().
   */
  public static function s_get_names_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($id, self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$id]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_names_by_alias().
   */
  public static function s_get_names_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($alias, self::$s_ids) && array_key_exists(self::$s_ids[$alias], self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$self::$s_ids[$alias]]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_id_by_name().
   */
  public static function s_get_id_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    if (array_key_exists($name, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$name]);
    }

    return new c_base_return_int();
  }

  /**
   * Implementation of s_get_id_by_alias().
   */
  public static function s_get_id_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    if (array_key_exists($alias, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$alias]);
    }

    return new c_base_return_int();
  }

  /**
   * Implementation of s_get_aliases_by_id().
   */
  public static function s_get_aliases_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_aliases_by_name().
   */
  public static function s_get_aliases_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($name, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$name]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_default_id().
   */
  public static function s_get_default_id() {
    return c_base_return_int::s_new(self::ENGLISH_US);
  }

  /**
   * Implementation of s_get_default_name().
   */
  public static function s_get_default_name() {
    return c_base_return_string::s_new($this->s_aliases[self::ENGLISH_US]);
  }

  /**
   * Implementation of s_get_ids().
   */
  public static function s_get_ids() {
    $ids = [];
    foreach (self::$s_aliases as $key => $value) {
      $ids[$key] = $key;
    }
    unset($key);
    unset($value);

    return c_base_return_array::s_new($ids);
  }

  /**
   * Implementation of s_get_aliases().
   */
  public static function s_get_aliases() {
    return c_base_return_array::s_new(self::$s_aliases);
  }

  /**
   * Implementation of s_get_names().
   */
  public static function s_get_names() {
    return c_base_return_array::s_new(self::$s_names);
  }

  /**
   * Implementation of s_get_ltr_by_id().
   */
  public static function s_get_ltr_by_id($id) {
    if (array_key_exists($id, self::$s_rtl_ids)) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }
}

/**
 * A generic class for managing the different supported languages.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
final class c_base_languages_all implements i_base_languages {

  private static $s_aliases = [
    self::ENGLISH_US              => ['en-us'],
    self::ENGLISH                 => ['eng', 'en'],
    self::ENGLISH_CA              => ['en-ca'],
    self::ENGLISH_GB              => ['en-gb'],
    self::AFAR                    => ['aar', 'aa'],
    self::ABKHAZIAN               => ['abk', 'ab'],
    self::ACHINESE                => ['ace'],
    self::ACOLI                   => ['ach'],
    self::ADANGME                 => ['ada'],
    self::ADYGHE                  => ['ady'],
    self::AFRO_ASIATIC            => ['afa'],
    self::AFRIHILI                => ['afh'],
    self::AFRIKAANS               => ['afr', 'af'],
    self::AINU                    => ['ain'],
    self::AKAN                    => ['aka', 'ak'],
    self::AKKADIAN                => ['akk'],
    self::ALBANIAN                => ['alb', 'sqi', 'sq'],
    self::ALEUT                   => ['ale'],
    self::ALGONQUIAN              => ['alg'],
    self::SOUTHERN_ALTAI          => ['alt'],
    self::AMHARIC                 => ['amh', 'am'],
    self::ENGLISH_OLD             => ['ang'],
    self::ANGIKA                  => ['anp'],
    self::APACHE                  => ['apa'],
    self::ARABIC                  => ['ara', 'ar'],
    self::ARAMAIC                 => ['arc'],
    self::ARAGONESE               => ['arg', 'an'],
    self::ARMENIAN                => ['arm', 'hye', 'hy'],
    self::MAPUDUNGUN              => ['am'],
    self::ARAPAHO                 => ['arp'],
    self::ARTIFICIAL              => ['art'],
    self::ARAWAK                  => ['arw'],
    self::ASSAMESE                => ['asm', 'as'],
    self::ASTURIAN                => ['ast'],
    self::ATHAPASCAN              => ['ath'],
    self::AUSTRALIAN              => ['aus'],
    self::AVARIC                  => ['ava', 'av'],
    self::AVESTAN                 => ['ave', 'ae'],
    self::AWADHI                  => ['awa'],
    self::AYMARA                  => ['aym', 'ay'],
    self::AZERBAIJANI             => ['aze', 'az'],
    self::BANDA                   => ['bad'],
    self::BAMILEKE                => ['bai'],
    self::BASHKIR                 => ['bak', 'ba'],
    self::BALUCHI                 => ['bal'],
    self::BAMBARA                 => ['bam', 'bm'],
    self::BALINESE                => ['ban'],
    self::BASQUE                  => ['baq', 'eus', 'eu'],
    self::BASA                    => ['bas'],
    self::BALTIC                  => ['bat'],
    self::BEJA                    => ['bej'],
    self::BELARUSIAN              => ['bel', 'be'],
    self::BEMBA                   => ['bem'],
    self::BENGALI                 => ['ben', 'bn'],
    self::BERBER                  => ['ber'],
    self::BHOJPURI                => ['bho'],
    self::BIHARI                  => ['bih', 'bh'],
    self::BIKOL                   => ['bik'],
    self::BINI                    => ['bin'],
    self::BISLAMA                 => ['bis', 'bi'],
    self::SIKSIKA                 => ['bla'],
    self::BANTU                   => ['bnt'],
    self::TIBETAN                 => ['tib', 'bod', 'bo'],
    self::BOSNIAN                 => ['bos', 'bs'],
    self::BRAJ                    => ['bra'],
    self::BRETON                  => ['bre'],
    self::BATAK                   => ['btk'],
    self::BURIAT                  => ['bua'],
    self::BUGINESE                => ['bug'],
    self::BULGARIAN               => ['bul'],
    self::BURMESE                 => ['bur', 'mya', 'my'],
    self::BLIN                    => ['byn'],
    self::CADDO                   => ['cad'],
    self::AMERICAN_INDIAN_CENTRAL => ['cai'],
    self::GALIBI_CARIB            => ['car'],
    self::CATALAN                 => ['cat', 'ca'],
    self::CAUCASIAN               => ['cau'],
    self::CEBUANO                 => ['ceb'],
    self::CELTIC                  => ['cel'],
    self::CZECH                   => ['cze', 'ces', 'cs'],
    self::CHAMORRO                => ['cha', 'ch'],
    self::CHIBCHA                 => ['chb'],
    self::CHECHEN                 => ['che', 'ce'],
    self::CHAGATAI                => ['chg'],
    self::CHINESE                 => ['chi', 'zho', 'zh'],
    self::CHUUKESE                => ['chk'],
    self::MARI                    => ['chm'],
    self::CHINOOK_JARGON          => ['chn'],
    self::CHOCTAW                 => ['cho'],
    self::CHIPEWYAN               => ['chp'],
    self::CHEROKEE                => ['chr'],
    self::CHURCH_SLAVIC           => ['chu', 'cu'],
    self::CHUVASH                 => ['chv', 'cv'],
    self::CHEYENNE                => ['chy'],
    self::CHAMIC                  => ['cmc'],
    self::COPTIC                  => ['cop'],
    self::CORNISH                 => ['cor'],
    self::CORSICAN                => ['cos', 'co'],
    self::CREOLES_ENGLISH         => ['cpe'],
    self::CREOLES_FRENCH          => ['cpf'],
    self::CREOLES_PORTUGESE       => ['cpp'],
    self::CREE                    => ['cre', 'cr'],
    self::CRIMEAN_TATAR           => ['crh'],
    self::CREOLES                 => ['crp'],
    self::KASHUBIAN               => ['csb'],
    self::CUSHITIC                => ['cus'],
    self::WELSH                   => ['wel', 'cym', 'cy'],
    self::DAKOTA                  => ['dak'],
    self::DANISH                  => ['dan', 'da'],
    self::DARGWA                  => ['dar'],
    self::LAND_DAYAK              => ['day'],
    self::DELAWARE                => ['del'],
    self::SLAVE                   => ['den'],
    self::GERMAN                  => ['ger', 'deu', 'de'],
    self::DOGRIB                  => ['dgr'],
    self::DINKA                   => ['din'],
    self::DIVEHI                  => ['div', 'dv'],
    self::DOGRI                   => ['doi'],
    self::DRAVIDIAN               => ['dra'],
    self::LOWER_SORBIAN           => ['dsb'],
    self::DUALA                   => ['dua'],
    self::DUTCH_MIDDLE            => ['dum'],
    self::DUTCH_FLEMISH           => ['dut', 'nld', 'nl'],
    self::DYULA                   => ['dyu'],
    self::DZONGKHA                => ['dzo', 'dz'],
    self::EFIK                    => ['efi'],
    self::EGYPTIAN                => ['egy'],
    self::EKAJUK                  => ['eka'],
    self::GREEK_MODERN            => ['gre', 'ell', 'el'],
    self::ELAMITE                 => ['elx'],
    self::ENGLISH_MIDDLE          => ['enm'],
    self::ESPERANTO               => ['epo', 'eo'],
    self::ESTONIAN                => ['est', 'et'],
    self::EWE                     => ['ewe', 'ee'],
    self::EWONDO                  => ['ewo'],
    self::FANG                    => ['fan'],
    self::FAROESE                 => ['fao', 'fo'],
    self::PERSIAN                 => ['per', 'fas', 'fa'],
    self::FANTI                   => ['fat'],
    self::FIJIAN                  => ['fij', 'fj'],
    self::FILIPINO                => ['fil'],
    self::FINNISH                 => ['fin', 'fi'],
    self::FINNO_UGRIAN            => ['fiu'],
    self::FON                     => ['fon'],
    self::FRENCH                  => ['fre', 'fra', 'fr'],
    self::FRENCH_MIDDLE           => ['frm'],
    self::FRENCH_OLD              => ['fro'],
    self::FRISIAN_NORTHERN        => ['frr'],
    self::FRISIAN_EASTERN         => ['frs'],
    self::FRISIAN_WESTERN         => ['fry', 'fy'],
    self::FULAH                   => ['ful', 'ff'],
    self::FRIULIAN                => ['fur'],
    self::GA                      => ['gaa'],
    self::GAYO                    => ['gay'],
    self::GBAYA                   => ['gba'],
    self::GERMANIC                => ['gem'],
    self::GEORGIAN                => ['geo', 'kat', 'ka'],
    self::GEEZ                    => ['gez'],
    self::GILBERTESE              => ['gil'],
    self::GAELIC                  => ['gla', 'gd'],
    self::IRISH                   => ['gle', 'ga'],
    self::GALICIAN                => ['glg', 'gl'],
    self::MANX                    => ['glv', 'gv'],
    self::GERMAN_MIDDLE_HIGH      => ['gmh'],
    self::GERMAN_OLD_HIGH         => ['goh'],
    self::GONDI                   => ['gon'],
    self::GORONTALO               => ['gor'],
    self::GOTHIC                  => ['got'],
    self::GREBO                   => ['grb'],
    self::GREEK_ANCIENT           => ['grc'],
    self::GUARANI                 => ['grm', 'gn'],
    self::GERMAN_SWISS            => ['gsw'],
    self::GUJARATI                => ['guj', 'gu'],
    self::GWICHIN                 => ['gwi'],
    self::HAIDA                   => ['hai'],
    self::HAITIAN                 => ['hat', 'ht'],
    self::HAUSA                   => ['hau', 'ha'],
    self::HAWAIIAN                => ['haw'],
    self::HEBREW                  => ['heb', 'he'],
    self::HERERO                  => ['her', 'hz'],
    self::HILIGAYNON              => ['hil'],
    self::HIMACHALI               => ['him'],
    self::HINDI                   => ['hin', 'hi'],
    self::HITTITE                 => ['hit'],
    self::HMONG                   => ['hmn'],
    self::HIRI_MOTU               => ['hmo', 'ho'],
    self::CROATIAN                => ['hrv'],
    self::SORBIAN_UPPER           => ['hsb'],
    self::HUNGARIAN               => ['hun', 'hu'],
    self::HUPA                    => ['hup'],
    self::IBAN                    => ['iba'],
    self::IGBO                    => ['ibo', 'ig'],
    self::ICELANDIC               => ['ice', 'isl', 'is'],
    self::IDO                     => ['ido', 'io'],
    self::SICHUAN_YI              => ['iii', 'ii'],
    self::IJO                     => ['ijo'],
    self::INUKTITUT               => ['iku', 'iu'],
    self::INTERLINGUE             => ['ile', 'ie'],
    self::ILOKO                   => ['ilo'],
    self::INTERLINGUA             => ['ina', 'ia'],
    self::INDIC                   => ['inc'],
    self::INDONESIAN              => ['ind', 'id'],
    self::INDO_EUROPEAN           => ['ine'],
    self::INGUSH                  => ['inh'],
    self::INUPIAQ                 => ['ipk', 'ik'],
    self::IRANIAN                 => ['ira'],
    self::IROQUOIAN               => ['iro'],
    self::ITALIAN                 => ['ita', 'it'],
    self::JAVANESE                => ['jav', 'jv'],
    self::LOJBAN                  => ['jbo'],
    self::JAPANESE                => ['jpn', 'ja'],
    self::JUDEO_PERSIAN           => ['jpr'],
    self::JUDEO_ARABIC            => ['jrb'],
    self::KARA_KALPAK             => ['kaa'],
    self::KABYLE                  => ['kab'],
    self::KACHIN                  => ['kac'],
    self::KALAALLISUT             => ['kal', 'kl'],
    self::KAMBA                   => ['kam'],
    self::KANNADA                 => ['kan', 'kn'],
    self::KAREN                   => ['kar'],
    self::KASHMIRI                => ['kas', 'ks'],
    self::KANURI                  => ['kau', 'kr'],
    self::KAWI                    => ['kaw'],
    self::KAZAKH                  => ['kaz'],
    self::KABARDIAN               => ['kbd'],
    self::KHASI                   => ['kha'],
    self::KHOISAN                 => ['khi'],
    self::CENTRAL_KHMER           => ['khm', 'km'],
    self::KHOTANESE               => ['kho'],
    self::KIKUYU                  => ['kik', 'ki'],
    self::KINYARWANDA             => ['kin', 'rw'],
    self::KIRGHIZ                 => ['kir', 'ky'],
    self::KIMBUNDU                => ['kmb'],
    self::KONKANI                 => ['kok'],
    self::KOMI                    => ['kom', 'kv'],
    self::KONGO                   => ['kon', 'kg'],
    self::KOREAN                  => ['kor', 'ko'],
    self::KOSRAEAN                => ['kos'],
    self::KPELLE                  => ['kpe'],
    self::KARACHAY_BALKAR         => ['krc'],
    self::KARELIAN                => ['krl'],
    self::KRU                     => ['kro'],
    self::KURUKH                  => ['kru'],
    self::KUANYAMA                => ['kua', 'kj'],
    self::KUMYK                   => ['kum'],
    self::KURDISH                 => ['kur', 'ku'],
    self::KUTENAI                 => ['kut'],
    self::LADINO                  => ['lad'],
    self::LAHNDA                  => ['lah'],
    self::LAMBA                   => ['lam'],
    self::LAO                     => ['lao', 'lo'],
    self::LATIN                   => ['lat', 'la'],
    self::LATVIAN                 => ['lav', 'lv'],
    self::LEZGHIAN                => ['lez'],
    self::LIMBURGAN               => ['lim', 'li'],
    self::LINGALA                 => ['lin', 'ln'],
    self::LITHUANIAN              => ['lit', 'lt'],
    self::MONGO                   => ['lol'],
    self::LOZI                    => ['loz'],
    self::LUXEMBOURGISH           => ['ltz', 'lb'],
    self::LUBA_LULUA              => ['lua'],
    self::LUBA_KATANGA            => ['lub', 'lu'],
    self::GANDA                   => ['lug', 'lg'],
    self::LUISENO                 => ['lui'],
    self::LUNDA                   => ['lun'],
    self::LUO                     => ['luo'],
    self::LUSHAI                  => ['lus'],
    self::MACEDONIAN              => ['mac', 'mkd', 'mk'],
    self::MADURESE                => ['mad'],
    self::MAGAHI                  => ['mag'],
    self::MARSHALLESE             => ['mah'],
    self::MAITHILI                => ['mai'],
    self::MAKASAR                 => ['mak'],
    self::MALAYALAM               => ['mal'],
    self::MANDINGO                => ['man'],
    self::MAORI                   => ['mao', 'mri', 'mi'],
    self::AUSTRONESIAN            => ['map'],
    self::MARATHI                 => ['mar', 'mr'],
    self::MASAI                   => ['mas'],
    self::MALAY                   => ['may', 'msa', 'ms'],
    self::MOKSHA                  => ['mdf'],
    self::MANDAR                  => ['mdr'],
    self::MENDE                   => ['men'],
    self::IRISH_MIDDLE            => ['mga'],
    self::MIKMAQ                  => ['mic'],
    self::MINANGKABAU             => ['min'],
    self::UNCODED                 => ['mis'],
    self::MON_KHMER               => ['mkh'],
    self::MALAGASY                => ['mlg'],
    self::MALTESE                 => ['mlt'],
    self::MANCHU                  => ['mnc'],
    self::MANIPURI                => ['mni'],
    self::MANOBO                  => ['mno'],
    self::MOHAWK                  => ['moh'],
    self::MONGOLIAN               => ['mon', 'mn'],
    self::MOSSI                   => ['mos'],
    self::MULTIPLE                => ['mul'],
    self::MUNDA                   => ['mun'],
    self::CREEK                   => ['mus'],
    self::MIRANDESE               => ['mwl'],
    self::MARWARI                 => ['mwr'],
    self::MAYAN                   => ['myn'],
    self::ERZYA                   => ['myv'],
    self::NAHUATL                 => ['nah'],
    self::AMERICAN_INDIAN_NORTH   => ['nai'],
    self::NEAPOLITAN              => ['nap'],
    self::NAURU                   => ['nau', 'na'],
    self::NAVAJO                  => ['nav', 'nv'],
    self::NDEBELE_SOUTH           => ['nbl', 'nr'],
    self::NDEBELE_NORTH           => ['nde', 'nd'],
    self::NDONGA                  => ['ndo', 'ng'],
    self::LOW_GERMAN              => ['nds'],
    self::NEPALI                  => ['nep', 'ne'],
    self::NEPAL_BHASA             => ['new'],
    self::NIAS                    => ['nia'],
    self::NIGER_KORDOFANIAN       => ['nic'],
    self::NIUEAN                  => ['niu'],
    self::NORWEGIAN_NYNORSK       => ['nno', 'nn'],
    self::BOKMAL                  => ['nob', 'nb'],
    self::NOGAI                   => ['nog'],
    self::NORSE_OLD               => ['non'],
    self::NORWEGIAN               => ['nor', 'no'],
    self::NKO                     => ['nqo'],
    self::PEDI                    => ['nso'],
    self::NUBIAN                  => ['nub'],
    self::CLASSICAL_NEWARI        => ['nwc'],
    self::CHICHEWA                => ['nya', 'ny'],
    self::NYAMWEZI                => ['nym'],
    self::NYANKOLE                => ['nyn'],
    self::NYORO                   => ['nyo'],
    self::NZIMA                   => ['nzi'],
    self::OCCITAN                 => ['oci', 'oc'],
    self::OJIBWA                  => ['oji', 'oj'],
    self::ORIYA                   => ['ori', 'or'],
    self::OROMO                   => ['orm', 'om'],
    self::OSAGE                   => ['osa'],
    self::OSSETIAN                => ['oss', 'os'],
    self::OTTOMAN                 => ['ota'],
    self::OTOMIAN                 => ['oto'],
    self::PAPUAN                  => ['paa'],
    self::PANGASINAN              => ['pag'],
    self::PAHLAVI                 => ['pal'],
    self::PAMPANGA                => ['pam'],
    self::PANJABI                 => ['pan', 'pa'],
    self::PAPIAMENTO              => ['pap'],
    self::PALAUAN                 => ['pau'],
    self::PERSIAN_OLD             => ['peo'],
    self::PHILIPPINE              => ['phi'],
    self::PHOENICIAN              => ['phn'],
    self::PALI                    => ['pli', 'pi'],
    self::POLISH                  => ['pol', 'pl'],
    self::POHNPEIAN               => ['pon'],
    self::PORTUGUESE              => ['por', 'pt'],
    self::PRAKRIT                 => ['pra'],
    self::PROVENCAL               => ['pro'],
    self::PUSHTO                  => ['pus', 'ps'],
    self::QUECHUA                 => ['que', 'qu'],
    self::RAJASTHANI              => ['raj'],
    self::RAPANUI                 => ['rap'],
    self::RAROTONGAN              => ['rar'],
    self::ROMANCE                 => ['roa'],
    self::ROMANSH                 => ['roh', 'rm'],
    self::ROMANY                  => ['rom'],
    self::ROMANIAN                => ['rum', 'ron', 'ro'],
    self::RUNDI                   => ['run', 'rn'],
    self::AROMANIAN               => ['rup'],
    self::RUSSIAN                 => ['rus', 'ru'],
    self::SANDAWE                 => ['sad'],
    self::SANGO                   => ['sag', 'sg'],
    self::YAKUT                   => ['sah'],
    self::AMERICAN_INDIAN_SOUTH   => ['sai'],
    self::SALISHAN                => ['sal'],
    self::SAMARITAN               => ['sam'],
    self::SANSKRIT                => ['san', 'sa'],
    self::SASAK                   => ['sas'],
    self::SANTALI                 => ['sat'],
    self::SICILIAN                => ['scn'],
    self::SCOTS                   => ['sco'],
    self::SELKUP                  => ['sel'],
    self::SEMITIC                 => ['sem'],
    self::IRISH_OLD               => ['sga'],
    self::SIGN                    => ['sgn'],
    self::SHAN                    => ['shn'],
    self::SIDAMO                  => ['sid'],
    self::SINHALA                 => ['sin', 'si'],
    self::SIOUAN                  => ['sio'],
    self::SINO_TIBETAN            => ['sit'],
    self::SLAVIC                  => ['sla'],
    self::SLOVAK                  => ['slo', 'slk', 'sk'],
    self::SLOVENIAN               => ['slv', 'sl'],
    self::SAMI_SOUTHERN           => ['sma'],
    self::SAMI_NORTHERN           => ['sme', 'se'],
    self::SAMI                    => ['smi'],
    self::SAMI_LULE               => ['smj'],
    self::SAMI_IRARI              => ['smn'],
    self::SAMOAN                  => ['smo', 'sm'],
    self::SAMI_SKOLT              => ['sms'],
    self::SHONA                   => ['sna', 'sn'],
    self::SINDHI                  => ['snd', 'sd'],
    self::SONINKE                 => ['snk'],
    self::SOGDIAN                 => ['sog'],
    self::SOMALI                  => ['som', 'so'],
    self::SONGHAI                 => ['son'],
    self::SOTHO_SOUTHERN          => ['sot', 'st'],
    self::SPANISH                 => ['spa', 'es'],
    self::SARDINIAN               => ['srd', 'sc'],
    self::SRANAN_TONGO            => ['sm'],
    self::SERBIAN                 => ['srp', 'sr'],
    self::SERER                   => ['srr'],
    self::NILO_SAHARAN            => ['ssa'],
    self::SWATI                   => ['ssw', 'ss'],
    self::SUKUMA                  => ['suk'],
    self::SUNDANESE               => ['sun', 'su'],
    self::SUSU                    => ['sus'],
    self::SUMERIAN                => ['sux'],
    self::SWAHILI                 => ['swa', 'sw'],
    self::SWEDISH                 => ['swe', 'sv'],
    self::SYRIAC_CLASSICAL        => ['syc'],
    self::SYRIAC                  => ['syr'],
    self::TAHITIAN                => ['tah', 'ty'],
    self::TAI                     => ['tai'],
    self::TAMIL                   => ['tam', 'ta'],
    self::TATAR                   => ['tat', 'tt'],
    self::TELUGU                  => ['tel', 'te'],
    self::TIMNE                   => ['tem'],
    self::TERENO                  => ['ter'],
    self::TETUM                   => ['tet'],
    self::TAJIK                   => ['tgk', 'tg'],
    self::TAGALOG                 => ['tgl', 'tl'],
    self::THAI                    => ['tha', 'th'],
    self::TIGRE                   => ['tig'],
    self::TIGRINYA                => ['tir', 'ti'],
    self::TIV                     => ['tiv'],
    self::TOKELAU                 => ['tkl'],
    self::KLINGON                 => ['tlh'],
    self::TLINGIT                 => ['tli'],
    self::TAMASHEK                => ['tmh'],
    self::TONGA_NYASA             => ['tog'],
    self::TONGA_ISLANDS           => ['ton', 'to'],
    self::TOK_PISIN               => ['tpi'],
    self::TSIMSHIAN               => ['tsi'],
    self::TSWANA                  => ['tsn', 'tn'],
    self::TSONGA                  => ['tso', 'ts'],
    self::TURKMEN                 => ['tuk', 'tk'],
    self::TUMBUKA                 => ['tum'],
    self::TUPI                    => ['tup'],
    self::TURKISH                 => ['tur', 'tr'],
    self::ALTAIC                  => ['tut'],
    self::TUVALU                  => ['tvl'],
    self::TWI                     => ['twi', 'tw'],
    self::TUVINIAN                => ['tyv'],
    self::UDMURT                  => ['udm'],
    self::UGARITIC                => ['uga'],
    self::UIGHUR                  => ['uig', 'ug'],
    self::UKRAINIAN               => ['ukr', 'uk'],
    self::UMBUNDU                 => ['umb'],
    self::UNDETERMINED            => ['und'],
    self::URDU                    => ['urd', 'ur'],
    self::UZBEK                   => ['uzb', 'uz'],
    self::VAI                     => ['vai'],
    self::VENDA                   => ['ven', 've'],
    self::VIETNAMESE              => ['vie', 'vi'],
    self::VOLAPUK                 => ['vol', 'vo'],
    self::VOTIC                   => ['vot'],
    self::WAKASHAN                => ['wak'],
    self::WOLAITTA                => ['wal'],
    self::WARAY                   => ['war'],
    self::WASHO                   => ['was'],
    self::SORBIAN                 => ['wen'],
    self::WALLOON                 => ['wln', 'wa'],
    self::WOLOF                   => ['wol', 'wo'],
    self::KALMYK                  => ['xal'],
    self::XHOSA                   => ['xho', 'xh'],
    self::YAO                     => ['yao'],
    self::YAPESE                  => ['yap'],
    self::YIDDISH                 => ['yid', 'yi'],
    self::YORUBA                  => ['yor', 'yo'],
    self::YUPIK                   => ['ypk'],
    self::ZAPOTEC                 => ['zap'],
    self::BLISSYMBOLS             => ['zbl'],
    self::ZENAGA                  => ['zen'],
    self::MOROCCAN_TAMAZIGHT      => ['zgh'],
    self::ZHUANG                  => ['zha', 'za'],
    self::ZANDE                   => ['znd'],
    self::ZULU                    => ['zul', 'zu'],
    self::ZUNI                    => ['zun'],
    self::NOT_APPLICABLE          => ['zxx'],
    self::ZAZA                    => ['zza'],
  ];

  private static $s_names = [
    self::ENGLISH_US              => ['US English'],
    self::ENGLISH                 => ['English'],
    self::ENGLISH_CA              => ['Canadian English'],
    self::ENGLISH_GB              => ['British English'],
    self::AFAR                    => ['Afar'],
    self::ABKHAZIAN               => ['Abkhazian'],
    self::ACHINESE                => ['Achinese'],
    self::ACOLI                   => ['Acoli'],
    self::ADANGME                 => ['Adangme'],
    self::ADYGHE                  => ['Adyghe'],
    self::AFRO_ASIATIC            => ['Afro-Asiatic', 'Adygei'],
    self::AFRIHILI                => ['Afrihili'],
    self::AFRIKAANS               => ['Afrikaans'],
    self::AINU                    => ['Ainu'],
    self::AKAN                    => ['Akan'],
    self::AKKADIAN                => ['Akkadian'],
    self::ALBANIAN                => ['Albanian'],
    self::ALEUT                   => ['Aleut'],
    self::ALGONQUIAN              => ['Algonquian'],
    self::SOUTHERN_ALTAI          => ['Southern Altai'],
    self::AMHARIC                 => ['Amharic'],
    self::ENGLISH_OLD             => ['Old English'],
    self::ANGIKA                  => ['Angika'],
    self::APACHE                  => ['Apache'],
    self::ARABIC                  => ['Arabic'],
    self::ARAMAIC                 => ['Official Aramaic', 'Imperial Aramaic'],
    self::ARAGONESE               => ['Aragonese'],
    self::ARMENIAN                => ['Armenian'],
    self::MAPUDUNGUN              => ['Mapudungun', 'Mapuche'],
    self::ARAPAHO                 => ['Arapaho'],
    self::ARTIFICIAL              => ['Artificial'],
    self::ARAWAK                  => ['Arawak'],
    self::ASSAMESE                => ['Assamese'],
    self::ASTURIAN                => ['Asturian', 'Bable', 'Leonese', 'Asturleonese'],
    self::ATHAPASCAN              => ['Athapascan'],
    self::AUSTRALIAN              => ['Australian'],
    self::AVARIC                  => ['Avaric'],
    self::AVESTAN                 => ['Avestan'],
    self::AWADHI                  => ['Awadhi'],
    self::AYMARA                  => ['Aymara'],
    self::AZERBAIJANI             => ['Azerbaijani'],
    self::BANDA                   => ['Banda'],
    self::BAMILEKE                => ['Bamileke'],
    self::BASHKIR                 => ['Bashkir'],
    self::BALUCHI                 => ['Baluchi'],
    self::BAMBARA                 => ['Bambara'],
    self::BALINESE                => ['Balinese'],
    self::BASQUE                  => ['Basque'],
    self::BASA                    => ['Basa'],
    self::BALTIC                  => ['Baltic'],
    self::BEJA                    => ['Beja'],
    self::BELARUSIAN              => ['Belarusian'],
    self::BEMBA                   => ['Bemba'],
    self::BENGALI                 => ['Bengali'],
    self::BERBER                  => ['Berber'],
    self::BHOJPURI                => ['Bhojpuri'],
    self::BIHARI                  => ['Bihari'],
    self::BIKOL                   => ['Bikol'],
    self::BINI                    => ['Bini', 'Edo'],
    self::BISLAMA                 => ['Bislama'],
    self::SIKSIKA                 => ['Siksika'],
    self::BANTU                   => ['Bantu'],
    self::TIBETAN                 => ['Tibetan'],
    self::BOSNIAN                 => ['Bosnian'],
    self::BRAJ                    => ['Braj'],
    self::BRETON                  => ['Breton'],
    self::BATAK                   => ['Batak'],
    self::BURIAT                  => ['Buriat'],
    self::BUGINESE                => ['Buginese'],
    self::BULGARIAN               => ['Bulgarian'],
    self::BURMESE                 => ['Burmese'],
    self::BLIN                    => ['Blin', 'Bilin'],
    self::CADDO                   => ['Caddo'],
    self::AMERICAN_INDIAN_CENTRAL => ['Central American Indian'],
    self::GALIBI_CARIB            => ['Galibi Carib'],
    self::CATALAN                 => ['Catalan', 'Valencian'],
    self::CAUCASIAN               => ['Caucasian'],
    self::CEBUANO                 => ['Cebuano'],
    self::CELTIC                  => ['Celtic'],
    self::CZECH                   => ['Czech'],
    self::CHAMORRO                => ['Chamorro'],
    self::CHIBCHA                 => ['Chibcha'],
    self::CHECHEN                 => ['Chechen'],
    self::CHAGATAI                => ['Chagatai'],
    self::CHINESE                 => ['Chinese'],
    self::CHUUKESE                => ['Chuukese'],
    self::MARI                    => ['Mari'],
    self::CHINOOK_JARGON          => ['Chinook jargon'],
    self::CHOCTAW                 => ['Choctaw'],
    self::CHIPEWYAN               => ['Chipewyan', 'Dene Suline'],
    self::CHEROKEE                => ['Cherokee'],
    self::CHURCH_SLAVIC           => ['Church Slavic', 'Old Slavonic', 'Church Slavonic', 'Old Bulgarian', 'Old Church Slavonic'],
    self::CHUVASH                 => ['Chuvash'],
    self::CHEYENNE                => ['Cheyenne'],
    self::CHAMIC                  => ['Chamic'],
    self::COPTIC                  => ['Coptic'],
    self::CORNISH                 => ['Cornish'],
    self::CORSICAN                => ['Corsican'],
    self::CREOLES_ENGLISH         => ['Creoles and Pidgins, English Based'],
    self::CREOLES_FRENCH          => ['Creoles and Pidgins, French Based'],
    self::CREOLES_PORTUGESE       => ['Creoles and Pidgins, Portugese Based'],
    self::CREE                    => ['Cree'],
    self::CRIMEAN_TATAR           => ['Crimean Tatar', 'Crimean Turkish'],
    self::CREOLES                 => ['Creoles and Pidgins'],
    self::KASHUBIAN               => ['Kashubian'],
    self::CUSHITIC                => ['Cushitic'],
    self::WELSH                   => ['Welsh'],
    self::DAKOTA                  => ['Dakota'],
    self::DANISH                  => ['Danish'],
    self::DARGWA                  => ['Dargwa'],
    self::LAND_DAYAK              => ['Land Dayak'],
    self::DELAWARE                => ['Delaware'],
    self::SLAVE                   => ['Athapascan Slave'],
    self::GERMAN                  => ['German'],
    self::DOGRIB                  => ['Dogrib'],
    self::DINKA                   => ['Dinka'],
    self::DIVEHI                  => ['Divehi', 'Dhivehi', 'Maldivian'],
    self::DOGRI                   => ['Dogri'],
    self::DRAVIDIAN               => ['Dravidian'],
    self::LOWER_SORBIAN           => ['Lower Sorbian'],
    self::DUALA                   => ['Duala'],
    self::DUTCH_MIDDLE            => ['Middle Dutch'],
    self::DUTCH_FLEMISH           => ['Dutch', 'Flemish'],
    self::DYULA                   => ['Dyula'],
    self::DZONGKHA                => ['Dzongkha'],
    self::EFIK                    => ['Efik'],
    self::EGYPTIAN                => ['Ancient Egyptian'],
    self::EKAJUK                  => ['Ekajuk'],
    self::GREEK_MODERN            => ['Modern Greek'],
    self::ELAMITE                 => ['Elamite'],
    self::ENGLISH_MIDDLE          => ['Middle English'],
    self::ESPERANTO               => ['Esperanto'],
    self::ESTONIAN                => ['Estonian'],
    self::EWE                     => ['Ewe'],
    self::EWONDO                  => ['Ewondo'],
    self::FANG                    => ['Fang'],
    self::FAROESE                 => ['Faroese'],
    self::PERSIAN                 => ['Persian'],
    self::FANTI                   => ['Fanti'],
    self::FIJIAN                  => ['Fijian'],
    self::FILIPINO                => ['Filipino', 'Pilipino'],
    self::FINNISH                 => ['Finnish'],
    self::FINNO_UGRIAN            => ['Finno-Ugrian '],
    self::FON                     => ['Fon'],
    self::FRENCH                  => ['French'],
    self::FRENCH_MIDDLE           => ['Middle French'],
    self::FRENCH_OLD              => ['Old French'],
    self::FRISIAN_NORTHERN        => ['Northern Frisian'],
    self::FRISIAN_EASTERN         => ['Eastern Frisian'],
    self::FRISIAN_WESTERN         => ['Southern Frisian'],
    self::FULAH                   => ['Fulah'],
    self::FRIULIAN                => ['Friulian'],
    self::GA                      => ['Ga'],
    self::GAYO                    => ['Gayo'],
    self::GBAYA                   => ['Gbaya'],
    self::GERMANIC                => ['Germanic'],
    self::GEORGIAN                => ['Georgian'],
    self::GEEZ                    => ['Geez'],
    self::GILBERTESE              => ['Gilbertese'],
    self::GAELIC                  => ['Gaelic', 'Scottish Gaelic'],
    self::IRISH                   => ['Irish'],
    self::GALICIAN                => ['Galician'],
    self::MANX                    => ['Manx'],
    self::GERMAN_MIDDLE_HIGH      => ['Middle High German'],
    self::GERMAN_OLD_HIGH         => ['Old High German'],
    self::GONDI                   => ['Gondi'],
    self::GORONTALO               => ['Gorontalo'],
    self::GOTHIC                  => ['Gothic'],
    self::GREBO                   => ['Grebo'],
    self::GREEK_ANCIENT           => ['Ancient Greek'],
    self::GUARANI                 => ['Guarani'],
    self::GERMAN_SWISS            => ['Swiss German', 'Alemannic', 'Alsatian'],
    self::GUJARATI                => ['Gujarati'],
    self::GWICHIN                 => ['Gwich\'in'],
    self::HAIDA                   => ['Haida'],
    self::HAITIAN                 => ['Haitian', 'Haitian Creole'],
    self::HAUSA                   => ['Hausa'],
    self::HAWAIIAN                => ['Hawaiian'],
    self::HEBREW                  => ['Hebrew'],
    self::HERERO                  => ['Herero'],
    self::HILIGAYNON              => ['Hiligaynon'],
    self::HIMACHALI               => ['Himachali', 'Western Pahari'],
    self::HINDI                   => ['Hindi'],
    self::HITTITE                 => ['Hittite'],
    self::HMONG                   => ['Hmong', 'Mong'],
    self::HIRI_MOTU               => ['Hiri Motu'],
    self::CROATIAN                => ['Croatian'],
    self::SORBIAN_UPPER           => ['Upper Sorbian'],
    self::HUNGARIAN               => ['Hungarian'],
    self::HUPA                    => ['Hupa'],
    self::IBAN                    => ['Iban'],
    self::IGBO                    => ['Igbo'],
    self::ICELANDIC               => ['Icelandic'],
    self::IDO                     => ['Ido'],
    self::SICHUAN_YI              => ['Sichuan Yi', 'Nuosu'],
    self::IJO                     => ['Ijo'],
    self::INUKTITUT               => ['Inuktitut'],
    self::INTERLINGUE             => ['Interlingue'],
    self::ILOKO                   => ['Iloko'],
    self::INTERLINGUA             => ['Interlingua'],
    self::INDIC                   => ['Indic'],
    self::INDONESIAN              => ['Indonesian'],
    self::INDO_EUROPEAN           => ['Indo-European'],
    self::INGUSH                  => ['Ingush'],
    self::INUPIAQ                 => ['Inupiaq'],
    self::IRANIAN                 => ['Iranian'],
    self::IROQUOIAN               => ['Iroquoian'],
    self::ITALIAN                 => ['Italian'],
    self::JAVANESE                => ['Javanese'],
    self::LOJBAN                  => ['Lojban'],
    self::JAPANESE                => ['Japanese'],
    self::JUDEO_PERSIAN           => ['Judeo-Persian'],
    self::JUDEO_ARABIC            => ['Judeo-Arabic'],
    self::KARA_KALPAK             => ['Kara-Kalpak'],
    self::KABYLE                  => ['Kabyle'],
    self::KACHIN                  => ['Kachin', 'Jingpho'],
    self::KALAALLISUT             => ['Kalaallisut', 'Greenlandic'],
    self::KAMBA                   => ['Kamba'],
    self::KANNADA                 => ['Kannada'],
    self::KAREN                   => ['Karen'],
    self::KASHMIRI                => ['Kashmiri'],
    self::KANURI                  => ['Kanuri'],
    self::KAWI                    => ['Kawi'],
    self::KAZAKH                  => ['Kazakh'],
    self::KABARDIAN               => ['Kabardian'],
    self::KHASI                   => ['Khasi'],
    self::KHOISAN                 => ['Khoisan'],
    self::CENTRAL_KHMER           => ['Central Khmer'],
    self::KHOTANESE               => ['Khotanese', 'Sakan'],
    self::KIKUYU                  => ['Kikuyu', 'Gikuyu'],
    self::KINYARWANDA             => ['Kinyarwanda'],
    self::KIRGHIZ                 => ['Kirghiz', 'Kyrgyz'],
    self::KIMBUNDU                => ['Kimbundu'],
    self::KONKANI                 => ['Konkani'],
    self::KOMI                    => ['Komi'],
    self::KONGO                   => ['Kongo'],
    self::KOREAN                  => ['Korean'],
    self::KOSRAEAN                => ['Kosraean'],
    self::KPELLE                  => ['Kpelle'],
    self::KARACHAY_BALKAR         => ['Karachay-Balkar'],
    self::KARELIAN                => ['Karelian'],
    self::KRU                     => ['Kru'],
    self::KURUKH                  => ['Kurukh'],
    self::KUANYAMA                => ['Kuanyama', 'Kwanyama'],
    self::KUMYK                   => ['Kumyk'],
    self::KURDISH                 => ['Kurdish'],
    self::KUTENAI                 => ['Kutenai'],
    self::LADINO                  => ['Ladino'],
    self::LAHNDA                  => ['Lahnda'],
    self::LAMBA                   => ['Lamba'],
    self::LAO                     => ['Lao'],
    self::LATIN                   => ['Latin'],
    self::LATVIAN                 => ['Latvian'],
    self::LEZGHIAN                => ['Lezghian'],
    self::LIMBURGAN               => ['Limburgan', 'Limburger', 'Limburgish'],
    self::LINGALA                 => ['Lingala'],
    self::LITHUANIAN              => ['Lithuanian'],
    self::MONGO                   => ['Mongo'],
    self::LOZI                    => ['Lozi'],
    self::LUXEMBOURGISH           => ['Luxembourgish', 'Letzeburgesch'],
    self::LUBA_LULUA              => ['Luba-Lulua'],
    self::LUBA_KATANGA            => ['Luba-Katanga'],
    self::GANDA                   => ['Ganda'],
    self::LUISENO                 => ['Luiseno'],
    self::LUNDA                   => ['Lunda'],
    self::LUO                     => ['Luo'],
    self::LUSHAI                  => ['Lushai'],
    self::MACEDONIAN              => ['Macedonian'],
    self::MADURESE                => ['Madurese'],
    self::MAGAHI                  => ['Magahi'],
    self::MARSHALLESE             => ['Marshallese'],
    self::MAITHILI                => ['Maithili'],
    self::MAKASAR                 => ['Makasar'],
    self::MALAYALAM               => ['Malayalam'],
    self::MANDINGO                => ['Mandingo'],
    self::MAORI                   => ['Maori'],
    self::AUSTRONESIAN            => ['Austronesian'],
    self::MARATHI                 => ['Marathi'],
    self::MASAI                   => ['Masai'],
    self::MALAY                   => ['Malay'],
    self::MOKSHA                  => ['Moksha'],
    self::MANDAR                  => ['Mandar'],
    self::MENDE                   => ['Mende'],
    self::IRISH_MIDDLE            => ['Middle Irish'],
    self::MIKMAQ                  => ['Mi\'kmaq', 'Micmac'],
    self::MINANGKABAU             => ['Minangkabau'],
    self::UNCODED                 => ['Uncoded'],
    self::MON_KHMER               => ['Mon-Khmer'],
    self::MALAGASY                => ['Malagasy'],
    self::MALTESE                 => ['Maltese'],
    self::MANCHU                  => ['Manchu'],
    self::MANIPURI                => ['Manipuri'],
    self::MANOBO                  => ['Manobo'],
    self::MOHAWK                  => ['Mohawk'],
    self::MONGOLIAN               => ['Mongolian'],
    self::MOSSI                   => ['Mossi'],
    self::MULTIPLE                => ['Multiple'],
    self::MUNDA                   => ['Munda'],
    self::CREEK                   => ['Creek'],
    self::MIRANDESE               => ['Mirandese'],
    self::MARWARI                 => ['Marwari'],
    self::MAYAN                   => ['Mayan'],
    self::ERZYA                   => ['Erzya'],
    self::NAHUATL                 => ['Nahuatl'],
    self::AMERICAN_INDIAN_NORTH   => ['North American Indian'],
    self::NEAPOLITAN              => ['Neapolitan'],
    self::NAURU                   => ['Nauru'],
    self::NAVAJO                  => ['Navajo', 'Navaho'],
    self::NDEBELE_SOUTH           => ['South Ndebele'],
    self::NDEBELE_NORTH           => ['North Ndebele'],
    self::NDONGA                  => ['Ndonga'],
    self::LOW_GERMAN              => ['Low German', 'Low Saxon'],
    self::NEPALI                  => ['Nepali'],
    self::NEPAL_BHASA             => ['Nepal Bhasa', 'Newari'],
    self::NIAS                    => ['Nias'],
    self::NIGER_KORDOFANIAN       => ['Niger-Kordofanian'],
    self::NIUEAN                  => ['Niuean'],
    self::NORWEGIAN_NYNORSK       => ['Norwegian Nynorsk'],
    self::BOKMAL                  => ['Bokmål', 'Norwegian Bokmål'],
    self::NOGAI                   => ['Nogai'],
    self::NORSE_OLD               => ['Old Norse'],
    self::NORWEGIAN               => ['Norwegian'],
    self::NKO                     => ['N\'Ko'],
    self::PEDI                    => ['Pedi', 'Sepedi', 'Northern Sotho'],
    self::NUBIAN                  => ['Nubian'],
    self::CLASSICAL_NEWARI        => ['Classical Newari', 'Old Newari', 'Classical Nepal Bhasa'],
    self::CHICHEWA                => ['Chichewa', 'Chewa', 'Nyanja'],
    self::NYAMWEZI                => ['Nyamwezi'],
    self::NYANKOLE                => ['Nyankole'],
    self::NYORO                   => ['Nyoro'],
    self::NZIMA                   => ['Nzima'],
    self::OCCITAN                 => ['Occitan'],
    self::OJIBWA                  => ['Ojibwa'],
    self::ORIYA                   => ['Oriya'],
    self::OROMO                   => ['Oromo'],
    self::OSAGE                   => ['Osage'],
    self::OSSETIAN                => ['Ossetian', 'Ossetic'],
    self::OTTOMAN                 => ['Ottoman Turkish'],
    self::OTOMIAN                 => ['Otomian'],
    self::PAPUAN                  => ['Papuan'],
    self::PANGASINAN              => ['Pangasinan'],
    self::PAHLAVI                 => ['Pahlavi'],
    self::PAMPANGA                => ['Pampanga', 'Kapampangan'],
    self::PANJABI                 => ['Panjabi', 'Punjabi'],
    self::PAPIAMENTO              => ['Papiamento'],
    self::PALAUAN                 => ['Palauan'],
    self::PERSIAN_OLD             => ['Old Persian'],
    self::PHILIPPINE              => ['Philippine'],
    self::PHOENICIAN              => ['Phoenician'],
    self::PALI                    => ['Pali'],
    self::POLISH                  => ['Polish'],
    self::POHNPEIAN               => ['Pohnpeian'],
    self::PORTUGUESE              => ['Portuguese'],
    self::PRAKRIT                 => ['Prakrit'],
    self::PROVENCAL               => ['Old Provençal', 'Old Occitan'],
    self::PUSHTO                  => ['Pushto', 'Pashto'],
    self::QUECHUA                 => ['Quechua'],
    self::RAJASTHANI              => ['Rajasthani'],
    self::RAPANUI                 => ['Rapanui'],
    self::RAROTONGAN              => ['Rarotongan', 'Cook Islands Maori'],
    self::ROMANCE                 => ['Romance'],
    self::ROMANSH                 => ['Romansh'],
    self::ROMANY                  => ['Romany'],
    self::ROMANIAN                => ['Romanian', 'Moldavian', 'Moldovan'],
    self::RUNDI                   => ['Rundi'],
    self::AROMANIAN               => ['Aromanian', 'Arumanian', 'Macedo-Romanian'],
    self::RUSSIAN                 => ['Russian'],
    self::SANDAWE                 => ['Sandawe'],
    self::SANGO                   => ['Sango'],
    self::YAKUT                   => ['Yakut'],
    self::AMERICAN_INDIAN_SOUTH   => ['South American Indian'],
    self::SALISHAN                => ['Salishan'],
    self::SAMARITAN               => ['Samaritan'],
    self::SANSKRIT                => ['Sanskrit'],
    self::SASAK                   => ['Sasak'],
    self::SANTALI                 => ['Santali'],
    self::SICILIAN                => ['Sicilian'],
    self::SCOTS                   => ['Scots'],
    self::SELKUP                  => ['Selkup'],
    self::SEMITIC                 => ['Semitic'],
    self::IRISH_OLD               => ['Old Irish'],
    self::SIGN                    => ['Sign Language'],
    self::SHAN                    => ['Shan'],
    self::SIDAMO                  => ['Sidamo'],
    self::SINHALA                 => ['Sinhala', 'Sinhalese'],
    self::SIOUAN                  => ['Siouan'],
    self::SINO_TIBETAN            => ['Sino-Tibetan'],
    self::SLAVIC                  => ['Slavic'],
    self::SLOVAK                  => ['Slovak'],
    self::SLOVENIAN               => ['Slovenian'],
    self::SAMI_SOUTHERN           => ['Southern Sami'],
    self::SAMI_NORTHERN           => ['Northern Sami'],
    self::SAMI                    => ['Sami'],
    self::SAMI_LULE               => ['Lule Sami'],
    self::SAMI_IRARI              => ['Inari Sami'],
    self::SAMOAN                  => ['Samoan'],
    self::SAMI_SKOLT              => ['Skolt Sami'],
    self::SHONA                   => ['Shona'],
    self::SINDHI                  => ['Sindhi'],
    self::SONINKE                 => ['Soninke'],
    self::SOGDIAN                 => ['Sogdian'],
    self::SOMALI                  => ['Somali'],
    self::SONGHAI                 => ['Songhai'],
    self::SOTHO_SOUTHERN          => ['Southern Sotho'],
    self::SPANISH                 => ['Spanish', 'Castilian'],
    self::SARDINIAN               => ['Sardinian'],
    self::SRANAN_TONGO            => ['Sranan Tongo'],
    self::SERBIAN                 => ['Serbian'],
    self::SERER                   => ['Serer'],
    self::NILO_SAHARAN            => ['Nilo-Saharan'],
    self::SWATI                   => ['Swati'],
    self::SUKUMA                  => ['Sukuma'],
    self::SUNDANESE               => ['Sundanese'],
    self::SUSU                    => ['Susu'],
    self::SUMERIAN                => ['Sumerian'],
    self::SWAHILI                 => ['Swahili'],
    self::SWEDISH                 => ['Swedish'],
    self::SYRIAC_CLASSICAL        => ['Classical Syriac'],
    self::SYRIAC                  => ['Syriac'],
    self::TAHITIAN                => ['Tahitian'],
    self::TAI                     => ['Tai'],
    self::TAMIL                   => ['Tamil'],
    self::TATAR                   => ['Tatar'],
    self::TELUGU                  => ['Telugu'],
    self::TIMNE                   => ['Timne'],
    self::TERENO                  => ['Tereno'],
    self::TETUM                   => ['Tetum'],
    self::TAJIK                   => ['Tajik'],
    self::TAGALOG                 => ['Tagalog'],
    self::THAI                    => ['Thai'],
    self::TIGRE                   => ['Tigre'],
    self::TIGRINYA                => ['Tigrinya'],
    self::TIV                     => ['Tiv'],
    self::TOKELAU                 => ['Tokelau'],
    self::KLINGON                 => ['Klingon', 'tlhIngan-Hol'],
    self::TLINGIT                 => ['Tlingit'],
    self::TAMASHEK                => ['Tamashek'],
    self::TONGA_NYASA             => ['Nyasa Tonga'],
    self::TONGA_ISLANDS           => ['Tonga Islands Tonga', 'to'],
    self::TOK_PISIN               => ['Tok Pisin'],
    self::TSIMSHIAN               => ['Tsimshian'],
    self::TSWANA                  => ['Tswana'],
    self::TSONGA                  => ['Tsonga'],
    self::TURKMEN                 => ['Turkmen'],
    self::TUMBUKA                 => ['Tumbuka'],
    self::TUPI                    => ['Tupi'],
    self::TURKISH                 => ['Turkish'],
    self::ALTAIC                  => ['Altaic'],
    self::TUVALU                  => ['Tuvalu'],
    self::TWI                     => ['Twi'],
    self::TUVINIAN                => ['Tuvinian'],
    self::UDMURT                  => ['Udmurt'],
    self::UGARITIC                => ['Ugaritic'],
    self::UIGHUR                  => ['Uighur', 'Uyghur'],
    self::UKRAINIAN               => ['Ukrainian'],
    self::UMBUNDU                 => ['Umbundu'],
    self::UNDETERMINED            => ['Undetermined'],
    self::URDU                    => ['Urdu'],
    self::UZBEK                   => ['Uzbek'],
    self::VAI                     => ['Vai'],
    self::VENDA                   => ['Venda'],
    self::VIETNAMESE              => ['Vietnamese'],
    self::VOLAPUK                 => ['Volapük'],
    self::VOTIC                   => ['Votic'],
    self::WAKASHAN                => ['Wakashan'],
    self::WOLAITTA                => ['Wolaitta', 'Wolaytta'],
    self::WARAY                   => ['Waray'],
    self::WASHO                   => ['Washo'],
    self::SORBIAN                 => ['Sorbian'],
    self::WALLOON                 => ['Walloon'],
    self::WOLOF                   => ['Wolof'],
    self::KALMYK                  => ['Kalmyk', 'Oirat'],
    self::XHOSA                   => ['Xhosa'],
    self::YAO                     => ['Yao'],
    self::YAPESE                  => ['Yapese'],
    self::YIDDISH                 => ['Yiddish'],
    self::YORUBA                  => ['Yoruba'],
    self::YUPIK                   => ['Yupik'],
    self::ZAPOTEC                 => ['Zapotec'],
    self::BLISSYMBOLS             => ['Blissymbols', 'Blissymbolics', 'Bliss'],
    self::ZENAGA                  => ['Zenaga'],
    self::MOROCCAN_TAMAZIGHT      => ['Standard Moroccan Tamazight'],
    self::ZHUANG                  => ['Zhuang', 'Chuang'],
    self::ZANDE                   => ['Zande'],
    self::ZULU                    => ['Zulu'],
    self::ZUNI                    => ['Zuni'],
    self::NOT_APPLICABLE          => ['No Linguistic Content', 'Not Applicable'],
    self::ZAZA                    => ['Zaza', 'Dimili', 'Dimli', 'Kirdki', 'Kirmanjki', 'Zazaki'],
  ];

  private static $s_ids = [
    'en-us' => self::ENGLISH_US,
    'en'    => self::ENGLISH,
    'eng'   => self::ENGLISH,
    'en-ca' => self::ENGLISH_CA,
    'en-gb' => self::ENGLISH_GB,
    'aar'   => self::AFAR,
    'aa'    => self::AFAR,
    'abk'   => self::ABKHAZIAN,
    'ab'    => self::ABKHAZIAN,
    'ace'   => self::ACHINESE,
    'ach'   => self::ACOLI,
    'ada'   => self::ADANGME,
    'ady'   => self::ADYGHE,
    'afa'   => self::AFRO_ASIATIC,
    'afh'   => self::AFRIHILI,
    'afr'   => self::AFRIKAANS,
    'af'    => self::AFRIKAANS,
    'ain'   => self::AINU,
    'aka'   => self::AKAN,
    'ak'    => self::AKAN,
    'akk'   => self::AKKADIAN,
    'alb'   => self::ALBANIAN,
    'sqi'   => self::ALBANIAN,
    'sq'    => self::ALBANIAN,
    'ale'   => self::ALEUT,
    'alg'   => self::ALGONQUIAN,
    'alt'   => self::SOUTHERN_ALTAI,
    'amh'   => self::AMHARIC,
    'am'    => self::AMHARIC,
    'ang'   => self::ENGLISH_OLD,
    'anp'   => self::ANGIKA,
    'apa'   => self::APACHE,
    'ara'   => self::ARABIC,
    'arc'   => self::ARAMAIC,
    'arg'   => self::ARAGONESE,
    'arm'   => self::ARMENIAN,
    'hye'   => self::ARMENIAN,
    'hy'    => self::ARMENIAN,
    'am'    => self::MAPUDUNGUN,
    'arp'   => self::ARAPAHO,
    'art'   => self::ARTIFICIAL,
    'arw'   => self::ARAWAK,
    'asm'   => self::ASSAMESE,
    'as'    => self::ASSAMESE,
    'ast'   => self::ASTURIAN,
    'ath'   => self::ATHAPASCAN,
    'aus'   => self::AUSTRALIAN,
    'ava'   => self::AVARIC,
    'av'    => self::AVARIC,
    'ave'   => self::AVESTAN,
    'ae'    => self::AVESTAN,
    'awa'   => self::AWADHI,
    'aym'   => self::AYMARA,
    'ay'    => self::AYMARA,
    'aze'   => self::AZERBAIJANI,
    'az'    => self::AZERBAIJANI,
    'bad'   => self::BANDA,
    'bai'   => self::BAMILEKE,
    'bak'   => self::BASHKIR,
    'ba'    => self::BASHKIR,
    'bal'   => self::BALUCHI,
    'bam'   => self::BAMBARA,
    'ba'    => self::BAMBARA,
    'ban'   => self::BALINESE,
    'baq'   => self::BASQUE,
    'eus'   => self::BASQUE,
    'eu'    => self::BASQUE,
    'bas'   => self::BASA,
    'bat'   => self::BALTIC,
    'bej'   => self::BEJA,
    'bel'   => self::BELARUSIAN,
    'be'    => self::BELARUSIAN,
    'bem'   => self::BEMBA,
    'ben'   => self::BENGALI,
    'bn'    => self::BENGALI,
    'ber'   => self::BERBER,
    'bho'   => self::BHOJPURI,
    'bih'   => self::BIHARI,
    'bh'    => self::BIHARI,
    'bik'   => self::BIKOL,
    'bin'   => self::BINI,
    'bis'   => self::BISLAMA,
    'bi'    => self::BISLAMA,
    'bla'   => self::SIKSIKA,
    'bnt'   => self::BANTU,
    'tib'   => self::TIBETAN,
    'bod'   => self::TIBETAN,
    'bo'    => self::TIBETAN,
    'bos'   => self::BOSNIAN,
    'bs'    => self::BOSNIAN,
    'bra'   => self::BRAJ,
    'bre'   => self::BRETON,
    'btk'   => self::BATAK,
    'bua'   => self::BURIAT,
    'bug'   => self::BUGINESE,
    'bul'   => self::BULGARIAN,
    'bur'   => self::BURMESE,
    'mya'   => self::BURMESE,
    'my'    => self::BURMESE,
    'byn'   => self::BLIN,
    'cad'   => self::CADDO,
    'cai'   => self::AMERICAN_INDIAN_CENTRAL,
    'car'   => self::GALIBI_CARIB,
    'cat'   => self::CATALAN,
    'ca'    => self::CATALAN,
    'cau'   => self::CAUCASIAN,
    'ceb'   => self::CEBUANO,
    'cel'   => self::CELTIC,
    'cze'   => self::CZECH,
    'ces'   => self::CZECH,
    'cs'    => self::CZECH,
    'cha'   => self::CHAMORRO,
    'ch'    => self::CHAMORRO,
    'chb'   => self::CHIBCHA,
    'che'   => self::CHECHEN,
    'ce'    => self::CHECHEN,
    'chg'   => self::CHAGATAI,
    'chi'   => self::CHINESE,
    'zho'   => self::CHINESE,
    'zh'    => self::CHINESE,
    'chk'   => self::CHUUKESE,
    'chm'   => self::MARI,
    'chn'   => self::CHINOOK_JARGON,
    'cho'   => self::CHOCTAW,
    'chp'   => self::CHIPEWYAN,
    'chr'   => self::CHEROKEE,
    'chu'   => self::CHURCH_SLAVIC,
    'cu'    => self::SLAVIC,
    'chv'   => self::CHUVASH,
    'cv'    => self::CHUVASH,
    'chy'   => self::CHEYENNE,
    'cmc'   => self::CHAMIC,
    'cop'   => self::COPTIC,
    'cor'   => self::CORNISH,
    'cos'   => self::CORSICAN,
    'co'    => self::CORSICAN,
    'cpe'   => self::CREOLES_ENGLISH,
    'cpf'   => self::CREOLES_FRENCH,
    'cpp'   => self::CREOLES_PORTUGESE,
    'cre'   => self::CREE,
    'cr'    => self::CREE,
    'crh'   => self::CRIMEAN_TATAR,
    'crp'   => self::CREOLES,
    'csb'   => self::KASHUBIAN,
    'cus'   => self::CUSHITIC,
    'wel'   => self::WELSH,
    'cym'   => self::WELSH,
    'cy'    => self::WELSH,
    'dak'   => self::DAKOTA,
    'dan'   => self::DANISH,
    'da'    => self::DANISH,
    'dar'   => self::DARGWA,
    'day'   => self::LAND_DAYAK,
    'del'   => self::DELAWARE,
    'den'   => self::SLAVE,
    'ger'   => self::GERMAN,
    'deu'   => self::GERMAN,
    'de'    => self::GERMAN,
    'dgr'   => self::DOGRIB,
    'din'   => self::DINKA,
    'div'   => self::DIVEHI,
    'dv'    => self::DIVEHI,
    'doi'   => self::DOGRI,
    'dra'   => self::DRAVIDIAN,
    'dsb'   => self::LOWER_SORBIAN,
    'dua'   => self::DUALA,
    'dum'   => self::DUTCH_MIDDLE,
    'dut'   => self::DUTCH_FLEMISH,
    'nld'   => self::DUTCH_FLEMISH,
    'nl'    => self::DUTCH_FLEMISH,
    'dyu'   => self::DYULA,
    'dzo'   => self::DZONGKHA,
    'dz'    => self::DZONGKHA,
    'efi'   => self::EFIK,
    'egy'   => self::EGYPTIAN,
    'eka'   => self::EKAJUK,
    'gre'   => self::GREEK_MODERN,
    'ell'   => self::GREEK_MODERN,
    'el'    => self::GREEK_MODERN,
    'elx'   => self::ELAMITE,
    'enm'   => self::ENGLISH_MIDDLE,
    'epo'   => self::ESPERANTO,
    'eo'    => self::ESPERANTO,
    'est'   => self::ESTONIAN,
    'et'    => self::ESTONIAN,
    'ewe'   => self::EWE,
    'ew'    => self::EWE,
    'ewo'   => self::EWONDO,
    'fan'   => self::FANG,
    'fao'   => self::FAROESE,
    'fo'    => self::FAROESE,
    'per'   => self::PERSIAN,
    'fas'   => self::PERSIAN,
    'fa'    => self::PERSIAN,
    'fat'   => self::FANTI,
    'fij'   => self::FIJIAN,
    'fj'    => self::FIJIAN,
    'fil'   => self::FILIPINO,
    'fin'   => self::FINNISH,
    'fi'    => self::FINNISH,
    'fiu'   => self::FINNO_UGRIAN,
    'fon'   => self::FON,
    'fre'   => self::FRENCH,
    'fra'   => self::FRENCH,
    'fr'    => self::FRENCH,
    'frm'   => self::FRENCH_MIDDLE,
    'fro'   => self::FRENCH_OLD,
    'frr'   => self::FRISIAN_NORTHERN,
    'frs'   => self::FRISIAN_EASTERN,
    'fry'   => self::FRISIAN_WESTERN,
    'fy'    => self::FRISIAN_WESTERN,
    'ful'   => self::FULAH,
    'ff'    => self::FULAH,
    'fur'   => self::FRIULIAN,
    'gaa'   => self::GA,
    'gay'   => self::GAYO,
    'gba'   => self::GBAYA,
    'gem'   => self::GERMANIC,
    'geo'   => self::GEORGIAN,
    'kat'   => self::GEORGIAN,
    'ka'    => self::GEORGIAN,
    'gez'   => self::GEEZ,
    'gil'   => self::GILBERTESE,
    'gla'   => self::GAELIC,
    'ga'    => self::GAELIC,
    'gle'   => self::IRISH,
    'ga'    => self::IRISH,
    'glg'   => self::GALICIAN,
    'gl'    => self::GALICIAN,
    'glv'   => self::MANX,
    'gv'    => self::MANX,
    'gmh'   => self::GERMAN_MIDDLE_HIGH,
    'goh'   => self::GERMAN_OLD_HIGH,
    'gon'   => self::GONDI,
    'gor'   => self::GORONTALO,
    'got'   => self::GOTHIC,
    'grb'   => self::GREBO,
    'grc'   => self::GREEK_ANCIENT,
    'grm'   => self::GUARANI,
    'gn'    => self::GUARANI,
    'gsw'   => self::GERMAN_SWISS,
    'guj'   => self::GUJARATI,
    'gu'    => self::GUJARATI,
    'gwi'   => self::GWICHIN,
    'hai'   => self::HAIDA,
    'hat'   => self::HAITIAN,
    'ht'    => self::HAITIAN,
    'hau'   => self::HAUSA,
    'ha'    => self::HAUSA,
    'haw'   => self::HAWAIIAN,
    'heb'   => self::HEBREW,
    'he'    => self::HEBREW,
    'her'   => self::HERERO,
    'hz'    => self::HERERO,
    'hil'   => self::HILIGAYNON,
    'him'   => self::HIMACHALI,
    'hin'   => self::HINDI,
    'hi'    => self::HINDI,
    'hit'   => self::HITTITE,
    'hmn'   => self::HMONG,
    'hmo'   => self::HIRI_MOTU,
    'ho'    => self::HIRI_MOTU,
    'hrv'   => self::CROATIAN,
    'hr'    => self::CROATIAN,
    'hsb'   => self::SORBIAN_UPPER,
    'hun'   => self::HUNGARIAN,
    'hu'    => self::HUNGARIAN,
    'hup'   => self::HUPA,
    'iba'   => self::IBAN,
    'ibo'   => self::IGBO,
    'ig'    => self::IGBO,
    'ice'   => self::ICELANDIC,
    'isl'   => self::ICELANDIC,
    'is'    => self::ICELANDIC,
    'ido'   => self::IDO,
    'io'    => self::IDO,
    'iii'   => self::SICHUAN_YI,
    'ii'    => self::SICHUAN_YI,
    'ijo'   => self::IJO,
    'iku'   => self::INUKTITUT,
    'iu'    => self::INUKTITUT,
    'ile'   => self::INTERLINGUE,
    'ie'    => self::INTERLINGUE,
    'ilo'   => self::ILOKO,
    'ina'   => self::INTERLINGUA,
    'ia'    => self::INTERLINGUA,
    'inc'   => self::INDIC,
    'ind'   => self::INDONESIAN,
    'id'    => self::INDONESIAN,
    'ine'   => self::INDO_EUROPEAN,
    'inh'   => self::INGUSH,
    'ipk'   => self::INUPIAQ,
    'ik'    => self::INUPIAQ,
    'ira'   => self::IRANIAN,
    'iro'   => self::IROQUOIAN,
    'ita'   => self::ITALIAN,
    'it'    => self::ITALIAN,
    'jav'   => self::JAVANESE,
    'jv'    => self::JAVANESE,
    'jbo'   => self::LOJBAN,
    'jpn'   => self::JAPANESE,
    'ja'    => self::JAPANESE,
    'jpr'   => self::JUDEO_PERSIAN,
    'jrb'   => self::JUDEO_ARABIC,
    'kaa'   => self::KARA_KALPAK,
    'kab'   => self::KABYLE,
    'kac'   => self::KACHIN,
    'kal'   => self::KALAALLISUT,
    'kl'    => self::KALAALLISUT,
    'kam'   => self::KAMBA,
    'kan'   => self::KANNADA,
    'kn'    => self::KANNADA,
    'kar'   => self::KAREN,
    'kas'   => self::KASHMIRI,
    'ks'    => self::KASHMIRI,
    'kau'   => self::KANURI,
    'kk'    => self::KANURI,
    'kaw'   => self::KAWI,
    'kaz'   => self::KAZAKH,
    'kz'    => self::KAZAKH,
    'kbd'   => self::KABARDIAN,
    'kha'   => self::KHASI,
    'khi'   => self::KHOISAN,
    'khm'   => self::CENTRAL_KHMER,
    'km'    => self::CENTRAL_KHMER,
    'kho'   => self::KHOTANESE,
    'kik'   => self::KIKUYU,
    'ki'    => self::KIKUYU,
    'kin'   => self::KINYARWANDA,
    'rw'    => self::KINYARWANDA,
    'kir'   => self::KIRGHIZ,
    'ky'    => self::KIRGHIZ,
    'kmb'   => self::KIMBUNDU,
    'kok'   => self::KONKANI,
    'kom'   => self::KOMI,
    'kv'    => self::KOMI,
    'kon'   => self::KONGO,
    'kg'    => self::KONGO,
    'kor'   => self::KOREAN,
    'ko'    => self::KOREAN,
    'kos'   => self::KOSRAEAN,
    'kpe'   => self::KPELLE,
    'krc'   => self::KARACHAY_BALKAR,
    'krl'   => self::KARELIAN,
    'kro'   => self::KRU,
    'kru'   => self::KURUKH,
    'kua'   => self::KUANYAMA,
    'kj'    => self::KUANYAMA,
    'kum'   => self::KUMYK,
    'kur'   => self::KURDISH,
    'ku'    => self::KURDISH,
    'kut'   => self::KUTENAI,
    'lad'   => self::LADINO,
    'lah'   => self::LAHNDA,
    'lam'   => self::LAMBA,
    'lao'   => self::LAO,
    'lo'    => self::LAO,
    'lat'   => self::LATIN,
    'la'    => self::LATIN,
    'lav'   => self::LATVIAN,
    'la'    => self::LATVIAN,
    'lez'   => self::LEZGHIAN,
    'lv'    => self::LEZGHIAN,
    'lim'   => self::LIMBURGAN,
    'li'    => self::LIMBURGAN,
    'lin'   => self::LINGALA,
    'li'    => self::LINGALA,
    'lit'   => self::LITHUANIAN,
    'lt'    => self::LITHUANIAN,
    'lol'   => self::MONGO,
    'loz'   => self::LOZI,
    'ltz'   => self::LUXEMBOURGISH,
    'lb'    => self::LUXEMBOURGISH,
    'lua'   => self::LUBA_LULUA,
    'lub'   => self::LUBA_KATANGA,
    'lu'    => self::LUBA_KATANGA,
    'lug'   => self::GANDA,
    'lg'    => self::GANDA,
    'lui'   => self::LUISENO,
    'lun'   => self::LUNDA,
    'luo'   => self::LUO,
    'lus'   => self::LUSHAI,
    'mac'   => self::MACEDONIAN,
    'mkd'   => self::MACEDONIAN,
    'mk'    => self::MACEDONIAN,
    'mad'   => self::MADURESE,
    'mag'   => self::MAGAHI,
    'mah'   => self::MARSHALLESE,
    'mh'    => self::MARSHALLESE,
    'mai'   => self::MAITHILI,
    'mak'   => self::MAKASAR,
    'mal'   => self::MALAYALAM,
    'ml'    => self::MALAYALAM,
    'man'   => self::MANDINGO,
    'mao'   => self::MAORI,
    'mri'   => self::MAORI,
    'mi'    => self::MAORI,
    'map'   => self::AUSTRONESIAN,
    'mar'   => self::MARATHI,
    'mr'    => self::MARATHI,
    'mas'   => self::MASAI,
    'may'   => self::MALAY,
    'msa'   => self::MALAY,
    'ms'    => self::MALAY,
    'mdf'   => self::MOKSHA,
    'mdr'   => self::MANDAR,
    'men'   => self::MENDE,
    'mga'   => self::IRISH_MIDDLE,
    'mic'   => self::MIKMAQ,
    'min'   => self::MINANGKABAU,
    'mis'   => self::UNCODED,
    'mkh'   => self::MON_KHMER,
    'mlg'   => self::MALAGASY,
    'mg'    => self::MALAGASY,
    'mlt'   => self::MALTESE,
    'mt'    => self::MALTESE,
    'mnc'   => self::MANCHU,
    'mni'   => self::MANIPURI,
    'mno'   => self::MANOBO,
    'moh'   => self::MOHAWK,
    'mon'   => self::MONGOLIAN,
    'mn'    => self::MONGOLIAN,
    'mos'   => self::MOSSI,
    'mul'   => self::MULTIPLE,
    'mun'   => self::MUNDA,
    'mus'   => self::CREEK,
    'mwl'   => self::MIRANDESE,
    'mwr'   => self::MARWARI,
    'myn'   => self::MAYAN,
    'myv'   => self::ERZYA,
    'nah'   => self::NAHUATL,
    'nai'   => self::AMERICAN_INDIAN_NORTH,
    'nap'   => self::NEAPOLITAN,
    'nau'   => self::NAURU,
    'na'    => self::NAURU,
    'nav'   => self::NAVAJO,
    'nv'    => self::NAVAJO,
    'nbl'   => self::NDEBELE_SOUTH,
    'nr'    => self::NDEBELE_SOUTH,
    'nde'   => self::NDEBELE_NORTH,
    'nd'    => self::NDEBELE_NORTH,
    'ndo'   => self::NDONGA,
    'ng'    => self::NDONGA,
    'nds'   => self::LOW_GERMAN,
    'nep'   => self::NEPALI,
    'ne'    => self::NEPALI,
    'new'   => self::NEPAL_BHASA,
    'nia'   => self::NIAS,
    'nic'   => self::NIGER_KORDOFANIAN,
    'niu'   => self::NIUEAN,
    'nno'   => self::NORWEGIAN_NYNORSK,
    'nn'    => self::NORWEGIAN_NYNORSK,
    'nob'   => self::BOKMAL,
    'nb'    => self::BOKMAL,
    'nog'   => self::NOGAI,
    'non'   => self::NORSE_OLD,
    'nor'   => self::NORWEGIAN,
    'no'    => self::NORWEGIAN,
    'nqo'   => self::NKO,
    'nso'   => self::PEDI,
    'nub'   => self::NUBIAN,
    'nwc'   => self::CLASSICAL_NEWARI,
    'nya'   => self::CHICHEWA,
    'nym'   => self::NYAMWEZI,
    'nyn'   => self::NYANKOLE,
    'nyo'   => self::NYORO,
    'nzi'   => self::NZIMA,
    'oci'   => self::OCCITAN,
    'oc'    => self::OCCITAN,
    'oji'   => self::OJIBWA,
    'oj'    => self::OJIBWA,
    'ori'   => self::ORIYA,
    'or'    => self::ORIYA,
    'orm'   => self::OROMO,
    'om'    => self::OROMO,
    'osa'   => self::OSAGE,
    'oss'   => self::OSSETIAN,
    'os'    => self::OSSETIAN,
    'ota'   => self::OTTOMAN,
    'oto'   => self::OTOMIAN,
    'paa'   => self::PAPUAN,
    'pag'   => self::PANGASINAN,
    'pal'   => self::PAHLAVI,
    'pam'   => self::PAMPANGA,
    'pan'   => self::PANJABI,
    'pa'    => self::PANJABI,
    'pap'   => self::PAPIAMENTO,
    'pau'   => self::PALAUAN,
    'peo'   => self::PERSIAN_OLD,
    'phi'   => self::PHILIPPINE,
    'phn'   => self::PHOENICIAN,
    'pli'   => self::PALI,
    'pi'    => self::PALI,
    'pol'   => self::POLISH,
    'pl'    => self::POLISH,
    'pon'   => self::POHNPEIAN,
    'por'   => self::PORTUGUESE,
    'pt'    => self::PORTUGUESE,
    'pra'   => self::PRAKRIT,
    'pro'   => self::PROVENCAL,
    'pus'   => self::PUSHTO,
    'ps'    => self::PUSHTO,
    'que'   => self::QUECHUA,
    'qu'    => self::QUECHUA,
    'raj'   => self::RAJASTHANI,
    'rap'   => self::RAPANUI,
    'rar'   => self::RAROTONGAN,
    'roa'   => self::ROMANCE,
    'roh'   => self::ROMANSH,
    'rm'    => self::ROMANSH,
    'rom'   => self::ROMANY,
    'rum'   => self::ROMANIAN,
    'ron'   => self::ROMANIAN,
    'ro'    => self::ROMANIAN,
    'run'   => self::RUNDI,
    'rn'    => self::RUNDI,
    'rup'   => self::AROMANIAN,
    'rus'   => self::RUSSIAN,
    'ru'    => self::RUSSIAN,
    'sad'   => self::SANDAWE,
    'sag'   => self::SANGO,
    'sg'    => self::SANGO,
    'sah'   => self::YAKUT,
    'sai'   => self::AMERICAN_INDIAN_SOUTH,
    'sal'   => self::SALISHAN,
    'sam'   => self::SAMARITAN,
    'san'   => self::SANSKRIT,
    'sa'    => self::SANSKRIT,
    'sas'   => self::SASAK,
    'sat'   => self::SANTALI,
    'scn'   => self::SICILIAN,
    'sco'   => self::SCOTS,
    'sel'   => self::SELKUP,
    'sem'   => self::SEMITIC,
    'sga'   => self::IRISH_OLD,
    'sgn'   => self::SIGN,
    'shn'   => self::SHAN,
    'sid'   => self::SIDAMO,
    'sin'   => self::SINHALA,
    'si'    => self::SINHALA,
    'sio'   => self::SIOUAN,
    'sit'   => self::SINO_TIBETAN,
    'sla'   => self::SLAVIC,
    'slo'   => self::SLOVAK,
    'slk'   => self::SLOVAK,
    'sk'    => self::SLOVAK,
    'slv'   => self::SLOVENIAN,
    'sl'    => self::SLOVENIAN,
    'sma'   => self::SAMI_SOUTHERN,
    'sme'   => self::SAMI_NORTHERN,
    'se'    => self::SAMI_NORTHERN,
    'smi'   => self::SAMI,
    'smj'   => self::SAMI_LULE,
    'smn'   => self::SAMI_IRARI,
    'smo'   => self::SAMOAN,
    'sm'    => self::SAMOAN,
    'sms'   => self::SAMI_SKOLT,
    'sna'   => self::SHONA,
    'sn'    => self::SHONA,
    'snd'   => self::SINDHI,
    'sd'    => self::SINDHI,
    'snk'   => self::SONINKE,
    'sog'   => self::SOGDIAN,
    'som'   => self::SOMALI,
    'so'    => self::SOMALI,
    'son'   => self::SONGHAI,
    'sot'   => self::SOTHO_SOUTHERN,
    'st'    => self::SOTHO_SOUTHERN,
    'spa'   => self::SPANISH,
    'es'    => self::SPANISH,
    'srd'   => self::SARDINIAN,
    'sc'    => self::SARDINIAN,
    'sm'    => self::SRANAN_TONGO,
    'srp'   => self::SERBIAN,
    'sr'    => self::SERBIAN,
    'srr'   => self::SERER,
    'ssa'   => self::NILO_SAHARAN,
    'ssw'   => self::SWATI,
    'ss'    => self::SWATI,
    'suk'   => self::SUKUMA,
    'sun'   => self::SUNDANESE,
    'su'    => self::SUNDANESE,
    'sus'   => self::SUSU,
    'sux'   => self::SUMERIAN,
    'swa'   => self::SWAHILI,
    'sw'    => self::SWAHILI,
    'swe'   => self::SWEDISH,
    'sv'    => self::SWEDISH,
    'syc'   => self::SYRIAC_CLASSICAL,
    'syr'   => self::SYRIAC,
    'tah'   => self::TAHITIAN,
    'ty'    => self::TAHITIAN,
    'tai'   => self::TAI,
    'tam'   => self::TAMIL,
    'ta'    => self::TAMIL,
    'tat'   => self::TATAR,
    'tt'    => self::TATAR,
    'tel'   => self::TELUGU,
    'te'    => self::TELUGU,
    'tem'   => self::TIMNE,
    'ter'   => self::TERENO,
    'tet'   => self::TETUM,
    'tgk'   => self::TAJIK,
    'tg'    => self::TAJIK,
    'tgl'   => self::TAGALOG,
    'tl'    => self::TAGALOG,
    'tha'   => self::THAI,
    'th'    => self::THAI,
    'tig'   => self::TIGRE,
    'tir'   => self::TIGRINYA,
    'ti'    => self::TIGRINYA,
    'tiv'   => self::TIV,
    'tkl'   => self::TOKELAU,
    'tlh'   => self::KLINGON,
    'tli'   => self::TLINGIT,
    'tmh'   => self::TAMASHEK,
    'tog'   => self::TONGA_NYASA,
    'ton'   => self::TONGA_ISLANDS,
    'to'    => self::TONGA_ISLANDS,
    'tpi'   => self::TOK_PISIN,
    'tsi'   => self::TSIMSHIAN,
    'tsn'   => self::TSWANA,
    'tn'    => self::TSWANA,
    'tso'   => self::TSONGA,
    'ts'    => self::TSONGA,
    'tuk'   => self::TURKMEN,
    'tk'    => self::TURKMEN,
    'tum'   => self::TUMBUKA,
    'tup'   => self::TUPI,
    'tur'   => self::TURKISH,
    'tr'    => self::TURKISH,
    'tut'   => self::ALTAIC,
    'tvl'   => self::TUVALU,
    'twi'   => self::TWI,
    'tw'    => self::TWI,
    'tyv'   => self::TUVINIAN,
    'udm'   => self::UDMURT,
    'uga'   => self::UGARITIC,
    'uig'   => self::UIGHUR,
    'ug'    => self::UIGHUR,
    'ukr'   => self::UKRAINIAN,
    'uk'    => self::UKRAINIAN,
    'umb'   => self::UMBUNDU,
    'und'   => self::UNDETERMINED,
    'urd'   => self::URDU,
    'ur'    => self::URDU,
    'uzb'   => self::UZBEK,
    'uz'    => self::UZBEK,
    'vai'   => self::VAI,
    'ven'   => self::VENDA,
    've'    => self::VENDA,
    'vie'   => self::VIETNAMESE,
    'vi'    => self::VIETNAMESE,
    'vol'   => self::VOLAPUK,
    'vo'    => self::VOLAPUK,
    'vot'   => self::VOTIC,
    'wak'   => self::WAKASHAN,
    'wal'   => self::WOLAITTA,
    'war'   => self::WARAY,
    'was'   => self::WASHO,
    'wen'   => self::SORBIAN,
    'wln'   => self::WALLOON,
    'wa'    => self::WALLOON,
    'wol'   => self::WOLOF,
    'wo'    => self::WOLOF,
    'xal'   => self::KALMYK,
    'xho'   => self::XHOSA,
    'xh'    => self::XHOSA,
    'yao'   => self::YAO,
    'yap'   => self::YAPESE,
    'yid'   => self::YIDDISH,
    'yi'    => self::YIDDISH,
    'yor'   => self::YORUBA,
    'yo'    => self::YORUBA,
    'ypk'   => self::YUPIK,
    'zap'   => self::ZAPOTEC,
    'zbl'   => self::BLISSYMBOLS,
    'zen'   => self::ZENAGA,
    'zgh'   => self::MOROCCAN_TAMAZIGHT,
    'zha'   => self::ZHUANG,
    'za'    => self::ZHUANG,
    'znd'   => self::ZANDE,
    'zul'   => self::ZULU,
    'zu'    => self::ZULU,
    'zun'   => self::ZUNI,
    'zxx'   => self::NOT_APPLICABLE,
    'zza'   => self::ZAZA,
  ];

  private static $s_rtl_ids = [
    // @todo: populate this with $id => $id.
  ];


  /**
   * Implementation of s_get_names_by_id().
   */
  public static function s_get_names_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($id, self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$id]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_names_by_alias().
   */
  public static function s_get_names_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($alias, self::$s_ids) && array_key_exists(self::$s_ids[$alias], self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$self::$s_ids[$alias]]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_id_by_name().
   */
  public static function s_get_id_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    if (array_key_exists($name, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$name]);
    }

    return new c_base_return_int();
  }

  /**
   * Implementation of s_get_id_by_alias().
   */
  public static function s_get_id_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'alias', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value(0, 'c_base_return_int', $error);
    }

    if (array_key_exists($alias, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$alias]);
    }

    return new c_base_return_int();
  }

  /**
   * Implementation of s_get_aliases_by_id().
   */
  public static function s_get_aliases_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'id', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_aliases_by_name().
   */
  public static function s_get_aliases_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_value([], 'c_base_return_array', $error);
    }

    if (array_key_exists($name, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$name]);
    }

    return new c_base_return_array();
  }

  /**
   * Implementation of s_get_default_id().
   */
  public static function s_get_default_id() {
    return c_base_return_int::s_new(self::ENGLISH_US);
  }

  /**
   * Implementation of s_get_default_name().
   */
  public static function s_get_default_name() {
    return c_base_return_string::s_new($this->s_aliases[self::ENGLISH_US]);
  }

  /**
   * Implementation of s_get_ids().
   */
  public static function s_get_ids() {
    $ids = [];
    foreach (self::$s_aliases as $key => $value) {
      $ids[$key] = $key;
    }
    unset($key);
    unset($value);

    return c_base_return_array::s_new($ids);
  }

  /**
   * Implementation of s_get_aliases().
   */
  public static function s_get_aliases() {
    return c_base_return_array::s_new(self::$s_aliases);
  }

  /**
   * Implementation of s_get_names().
   */
  public static function s_get_names() {
    return c_base_return_array::s_new(self::$s_names);
  }

  /**
   * Implementation of s_get_ltr_by_id().
   */
  public static function s_get_ltr_by_id($id) {
    if (array_key_exists($id, self::$s_rtl_ids)) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }
}
