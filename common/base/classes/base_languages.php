<?php
/**
 * @file
 * Provides a class for managing the different supported languages.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic interface for managing the different supported languages.
 *
 * Additional known sub-languages, such as en-us, are added even though they do not appear in the iso standard.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
interface i_base_languages {
  const NONE                    = 0;
  const AFAR                    = 1;   // aar, aa
  const ABKHAZIAN               = 2;   // abk, ab
  const ACHINESE                = 3;   // ace
  const ACOLI                   = 4;   // ach
  const ADANGME                 = 5;   // ada
  const ADYGHE                  = 6;   // ady
  const AFRO_ASIATIC            = 7;   // afa
  const AFRIHILI                = 8;   // afh
  const AFRIKAANS               = 9;   // afr, af
  const AINU                    = 10;  // ain
  const AKAN                    = 11;  // aka, ak
  const AKKADIAN                = 12;  // akk
  const ALBANIAN                = 13;  // alb (b), sqi (t), sq
  const ALEUT                   = 14;  // ale
  const ALGONQUIAN              = 15;  // alg
  const SOUTHERN_ALTAI          = 16;  // alt
  const AMHARIC                 = 17;  // amh, am
  const ENGLISH_OLD             = 18;  // ang
  const ANGIKA                  = 19;  // anp
  const APACHE                  = 20;  // apa
  const ARABIC                  = 21;  // ara, ar
  const ARAMAIC                 = 22;  // arc
  const ARAGONESE               = 23;  // arg, an
  const ARMENIAN                = 24;  // arm (b), hye (t), hy
  const MAPUDUNGUN              = 25;  // am
  const ARAPAHO                 = 26;  // arp
  const ARTIFICIAL              = 27;  // art
  const ARAWAK                  = 28;  // arw
  const ASSAMESE                = 29;  // asm, as
  const ASTURIAN                = 30;  // ast
  const ATHAPASCAN              = 31;  // ath
  const AUSTRALIAN              = 32;  // aus
  const AVARIC                  = 33;  // ava, av
  const AVESTAN                 = 34;  // ave, ae
  const AWADHI                  = 35;  // awa
  const AYMARA                  = 36;  // aym, ay
  const AZERBAIJANI             = 37;  // aze, az
  const BANDA                   = 38;  // bad
  const BAMILEKE                = 39;  // bai
  const BASHKIR                 = 40;  // bak, ba
  const BALUCHI                 = 41;  // bal
  const BAMBARA                 = 42;  // bam, bm
  const BALINESE                = 43;  // ban
  const BASQUE                  = 44;  // baq (b), eus (t), eu
  const BASA                    = 45;  // bas
  const BALTIC                  = 46;  // bat
  const BEJA                    = 47;  // bej
  const BELARUSIAN              = 48;  // bel, be
  const BEMBA                   = 49;  // bem
  const BENGALI                 = 50;  // ben, bn
  const BERBER                  = 51;  // ber
  const BHOJPURI                = 52;  // bho
  const BIHARI                  = 53;  // bih, bh
  const BIKOL                   = 54;  // bik
  const BINI                    = 55;  // bin
  const BISLAMA                 = 56;  // bis, bi
  const SIKSIKA                 = 57;  // bla
  const BANTU                   = 58;  // bnt
  const TIBETAN                 = 59;  // tib (b), bod (t), bo
  const BOSNIAN                 = 60;  // bos, bs
  const BRAJ                    = 61;  // bra
  const BRETON                  = 62;  // bre
  const BATAK                   = 63;  // btk
  const BURIAT                  = 64;  // bua
  const BUGINESE                = 65;  // bug
  const BULGARIAN               = 66;  // bul
  const BURMESE                 = 67;  // bur (b), mya (t), my
  const BLIN                    = 68;  // byn
  const CADDO                   = 69;  // cad
  const AMERICAN_INDIAN_CENTRAL = 70;  // cai
  const GALIBI_CARIB            = 71;  // car
  const CATALAN                 = 72;  // cat, ca
  const CAUCASIAN               = 73;  // cau
  const CEBUANO                 = 74;  // ceb
  const CELTIC                  = 75;  // cel
  const CZECH                   = 76;  // cze (b), ces (t), cs
  const CHAMORRO                = 77;  // cha, ch
  const CHIBCHA                 = 78;  // chb
  const CHECHEN                 = 79;  // che, ce
  const CHAGATAI                = 80;  // chg
  const CHINESE                 = 81;  // chi (b), zho (t), zh
  const CHUUKESE                = 82;  // chk
  const MARI                    = 83;  // chm
  const CHINOOK_JARGON          = 84;  // chn
  const CHOCTAW                 = 85;  // cho
  const CHIPEWYAN               = 86;  // chp
  const CHEROKEE                = 87;  // chr
  const CHURCH_SLAVIC           = 88;  // chu, cu
  const CHUVASH                 = 89;  // chv, cv
  const CHEYENNE                = 90;  // chy
  const CHAMIC                  = 91;  // cmc
  const COPTIC                  = 92;  // cop
  const CORNISH                 = 93;  // cor
  const CORSICAN                = 94;  // cos, co
  const CREOLES_ENGLISH         = 95;  // cpe
  const CREOLES_FRENCH          = 96;  // cpf
  const CREOLES_PORTUGESE       = 97;  // cpp
  const CREE                    = 98;  // cre, cr
  const CRIMEAN_TATAR           = 99;  // crh
  const CREOLES                 = 100; // crp
  const KASHUBIAN               = 101; // csb
  const CUSHITIC                = 102; // cus
  const WELSH                   = 103; // wel (b), cym (t), cy
  const DAKOTA                  = 104; // dak
  const DANISH                  = 105; // dan, da
  const DARGWA                  = 106; // dar
  const LAND_DAYAK              = 107; // day
  const DELAWARE                = 108; // del
  const SLAVE                   = 109; // den
  const GERMAN                  = 110; // ger (b), deu (t), de
  const DOGRIB                  = 111; // dgr
  const DINKA                   = 112; // din
  const DIVEHI                  = 113; // div, dv
  const DOGRI                   = 114; // doi
  const DRAVIDIAN               = 115; // dra
  const LOWER_SORBIAN           = 116; // dsb
  const DUALA                   = 117; // dua
  const DUTCH_MIDDLE            = 118; // dum
  const DUTCH_FLEMISH           = 119; // dut (b), nld (t), nl
  const DYULA                   = 120; // dyu
  const DZONGKHA                = 121; // dzo, dz
  const EFIK                    = 122; // efi
  const EGYPTIAN                = 123; // egy
  const EKAJUK                  = 124; // eka
  const GREEK_MODERN            = 125; // gre (b), ell (t), el
  const ELAMITE                 = 126; // elx
  const ENGLISH                 = 127; // eng, en
  const ENGLISH_MIDDLE          = 128; // enm
  const ESPERANTO               = 129; // epo, eo
  const ESTONIAN                = 130; // est, et
  const EWE                     = 131; // ewe, ee
  const EWONDO                  = 132; // ewo
  const FANG                    = 133; // fan
  const FAROESE                 = 134; // fao, fo
  const PERSIAN                 = 135; // per (b), fas (t), fa
  const FANTI                   = 136; // fat
  const FIJIAN                  = 137; // fij, fj
  const FILIPINO                = 138; // fil
  const FINNISH                 = 139; // fin, fi
  const FINNO_UGRIAN            = 140; // fiu
  const FON                     = 141; // fon
  const FRENCH                  = 142; // fre (b), fra (t), fr
  const FRENCH_MIDDLE           = 143; // frm
  const FRENCH_OLD              = 144; // fro
  const FRISIAN_NORTHERN        = 145; // frr
  const FRISIAN_EASTERN         = 146; // frs
  const FRISIAN_WESTERN         = 147; // fry, fy
  const FULAH                   = 148; // ful, ff
  const FRIULIAN                = 149; // fur
  const GA                      = 150; // gaa
  const GAYO                    = 151; // gay
  const GBAYA                   = 152; // gba
  const GERMANIC                = 153; // gem
  const GEORGIAN                = 154; // geo (b), kat (t), ka
  const GEEZ                    = 155; // gez
  const GILBERTESE              = 156; // gil
  const GAELIC                  = 157; // gla, gd
  const IRISH                   = 158; // gle, ga
  const GALICIAN                = 159; // glg, gl
  const MANX                    = 160; // glv, gv
  const GERMAN_MIDDLE_HIGH      = 161; // gmh
  const GERMAN_OLD_HIGH         = 162; // goh
  const GONDI                   = 163; // gon
  const GORONTALO               = 164; // gor
  const GOTHIC                  = 165; // got
  const GREBO                   = 166; // grb
  const GREEK_ANCIENT           = 167; // grc
  const GUARANI                 = 168; // grm, gn
  const GERMAN_SWISS            = 169; // gsw
  const GUJARATI                = 170; // guj, gu
  const GWICHIN                 = 171; // gwi
  const HAIDA                   = 172; // hai
  const HAITIAN                 = 173; // hat, ht
  const HAUSA                   = 174; // hau, ha
  const HAWAIIAN                = 175; // haw
  const HEBREW                  = 176; // heb, he
  const HERERO                  = 177; // her, hz
  const HILIGAYNON              = 178; // hil
  const HIMACHALI               = 179; // him
  const HINDI                   = 180; // hin, hi
  const HITTITE                 = 181; // hit
  const HMONG                   = 182; // hmn
  const HIRI_MOTU               = 183; // hmo, ho
  const CROATIAN                = 184; // hrv
  const SORBIAN_UPPER           = 185; // hsb
  const HUNGARIAN               = 186; // hun, hu
  const HUPA                    = 187; // hup
  const IBAN                    = 188; // iba
  const IGBO                    = 189; // ibo, ig
  const ICELANDIC               = 190; // ice (b), isl (t), is
  const IDO                     = 191; // ido, io
  const SICHUAN_YI              = 192; // iii, ii
  const IJO                     = 193; // ijo
  const INUKTITUT               = 194; // iku, iu
  const INTERLINGUE             = 195; // ile, ie
  const ILOKO                   = 196; // ilo
  const INTERLINGUA             = 197; // ina, ia
  const INDIC                   = 198; // inc
  const INDONESIAN              = 199; // ind, id
  const INDO_EUROPEAN           = 200; // ine
  const INGUSH                  = 201; // inh
  const INUPIAQ                 = 202; // ipk, ik
  const IRANIAN                 = 203; // ira
  const IROQUOIAN               = 204; // iro
  const ITALIAN                 = 205; // ita, it
  const JAVANESE                = 206; // jav, jv
  const LOJBAN                  = 207; // jbo
  const JAPANESE                = 208; // jpn, ja
  const JUDEO_PERSIAN           = 209; // jpr
  const JUDEO_ARABIC            = 210; // jrb
  const KARA_KALPAK             = 211; // kaa
  const KABYLE                  = 212; // kab
  const KACHIN                  = 213; // kac
  const KALAALLISUT             = 214; // kal, kl
  const KAMBA                   = 215; // kam
  const KANNADA                 = 216; // kan, kn
  const KAREN                   = 217; // kar
  const KASHMIRI                = 218; // kas, ks
  const KANURI                  = 219; // kau, kr
  const KAWI                    = 220; // kaw
  const KAZAKH                  = 221; // kaz
  const KABARDIAN               = 222; // kbd
  const KHASI                   = 223; // kha
  const KHOISAN                 = 224; // khi
  const CENTRAL_KHMER           = 225; // khm, km
  const KHOTANESE               = 226; // kho
  const KIKUYU                  = 227; // kik, ki
  const KINYARWANDA             = 228; // kin, rw
  const KIRGHIZ                 = 229; // kir, ky
  const KIMBUNDU                = 230; // kmb
  const KONKANI                 = 231; // kok
  const KOMI                    = 232; // kom, kv
  const KONGO                   = 233; // kon, kg
  const KOREAN                  = 234; // kor, ko
  const KOSRAEAN                = 235; // kos
  const KPELLE                  = 236; // kpe
  const KARACHAY_BALKAR         = 237; // krc
  const KARELIAN                = 238; // krl
  const KRU                     = 239; // kro
  const KURUKH                  = 240; // kru
  const KUANYAMA                = 241; // kua, kj
  const KUMYK                   = 242; // kum
  const KURDISH                 = 243; // kur, ku
  const KUTENAI                 = 244; // kut
  const LADINO                  = 245; // lad
  const LAHNDA                  = 246; // lah
  const LAMBA                   = 247; // lam
  const LAO                     = 248; // lao, lo
  const LATIN                   = 249; // lat, la
  const LATVIAN                 = 250; // lav, lv
  const LEZGHIAN                = 251; // lez
  const LIMBURGAN               = 252; // lim, li
  const LINGALA                 = 253; // lin, ln
  const LITHUANIAN              = 254; // lit, lt
  const MONGO                   = 255; // lol
  const LOZI                    = 256; // loz
  const LUXEMBOURGISH           = 257; // ltz, lb
  const LUBA_LULUA              = 258; // lua
  const LUBA_KATANGA            = 259; // lub, lu
  const GANDA                   = 260; // lug, lg
  const LUISENO                 = 261; // lui
  const LUNDA                   = 262; // lun
  const LUO                     = 263; // luo
  const LUSHAI                  = 264; // lus
  const MACEDONIAN              = 265; // mac (b), mkd (t), mk
  const MADURESE                = 266; // mad
  const MAGAHI                  = 267; // mag
  const MARSHALLESE             = 268; // mah
  const MAITHILI                = 269; // mai
  const MAKASAR                 = 270; // mak
  const MALAYALAM               = 271; // mal
  const MANDINGO                = 272; // man
  const MAORI                   = 273; // mao (b), mri (t), mi
  const AUSTRONESIAN            = 274; // map
  const MARATHI                 = 275; // mar, mr
  const MASAI                   = 276; // mas
  const MALAY                   = 277; // may (b), msa (t), ms
  const MOKSHA                  = 278; // mdf
  const MANDAR                  = 279; // mdr
  const MENDE                   = 280; // men
  const IRISH_MIDDLE            = 281; // mga
  const MIKMAQ                  = 282; // mic
  const MINANGKABAU             = 283; // min
  const UNCODED                 = 284; // mis
  const MON_KHMER               = 285; // mkh
  const MALAGASY                = 286; // mlg
  const MALTESE                 = 287; // mlt
  const MANCHU                  = 288; // mnc
  const MANIPURI                = 289; // mni
  const MANOBO                  = 290; // mno
  const MOHAWK                  = 291; // moh
  const MONGOLIAN               = 292; // mon, mn
  const MOSSI                   = 293; // mos
  const MULTIPLE                = 294; // mul
  const MUNDA                   = 295; // mun
  const CREEK                   = 296; // mus
  const MIRANDESE               = 297; // mwl
  const MARWARI                 = 298; // mwr
  const MAYAN                   = 299; // myn
  const ERZYA                   = 300; // myv
  const NAHUATL                 = 301; // nah
  const AMERICAN_INDIAN_NORTH   = 302; // nai
  const NEAPOLITAN              = 303; // nap
  const NAURU                   = 304; // nau, na
  const NAVAJO                  = 305; // nav, nv
  const NDEBELE_SOUTH           = 306; // nbl, nr
  const NDEBELE_NORTH           = 307; // nde, nd
  const NDONGA                  = 308; // ndo, ng
  const LOW_GERMAN              = 309; // nds
  const NEPALI                  = 310; // nep, ne
  const NEPAL_BHASA             = 311; // new
  const NIAS                    = 312; // nia
  const NIGER_KORDOFANIAN       = 313; // nic
  const NIUEAN                  = 314; // niu
  const NORWEGIAN_NYNORSK       = 315; // nno, nn
  const BOKMAL                  = 316; // nob, nb
  const NOGAI                   = 317; // nog
  const NORSE_OLD               = 318; // non
  const NORWEGIAN               = 319; // nor, no
  const NKO                     = 320; // nqo
  const PEDI                    = 321; // nso
  const NUBIAN                  = 322; // nub
  const CLASSICAL_NEWARI        = 323; // nwc
  const CHICHEWA                = 324; // nya, ny
  const NYAMWEZI                = 325; // nym
  const NYANKOLE                = 326; // nyn
  const NYORO                   = 327; // nyo
  const NZIMA                   = 328; // nzi
  const OCCITAN                 = 329; // oci, oc
  const OJIBWA                  = 330; // oji, oj
  const ORIYA                   = 331; // ori, or
  const OROMO                   = 332; // orm, om
  const OSAGE                   = 333; // osa
  const OSSETIAN                = 334; // oss, os
  const OTTOMAN                 = 335; // ota
  const OTOMIAN                 = 336; // oto
  const PAPUAN                  = 337; // paa
  const PANGASINAN              = 338; // pag
  const PAHLAVI                 = 339; // pal
  const PAMPANGA                = 340; // pam
  const PANJABI                 = 341; // pan, pa
  const PAPIAMENTO              = 342; // pap
  const PALAUAN                 = 342; // pau
  const PERSIAN_OLD             = 343; // peo
  const PHILIPPINE              = 344; // phi
  const PHOENICIAN              = 345; // phn
  const PALI                    = 346; // pli, pi
  const POLISH                  = 347; // pol, pl
  const POHNPEIAN               = 348; // pon
  const PORTUGUESE              = 349; // por, pt
  const PRAKRIT                 = 350; // pra
  const PROVENCAL               = 351; // pro
  const PUSHTO                  = 352; // pus, ps
  const QUECHUA                 = 353; // que, qu
  const RAJASTHANI              = 354; // raj
  const RAPANUI                 = 355; // rap
  const RAROTONGAN              = 356; // rar
  const ROMANCE                 = 357; // roa
  const ROMANSH                 = 358; // roh, rm
  const ROMANY                  = 359; // rom
  const ROMANIAN                = 360; // rum (b), ron (t), ro
  const RUNDI                   = 361; // run, rn
  const AROMANIAN               = 362; // rup
  const RUSSIAN                 = 363; // rus, ru
  const SANDAWE                 = 364; // sad
  const SANGO                   = 365; // sag, sg
  const YAKUT                   = 366; // sah
  const AMERICAN_INDIAN_SOUTH   = 367; // sai
  const SALISHAN                = 368; // sal
  const SAMARITAN               = 369; // sam
  const SANSKRIT                = 370; // san, sa
  const SASAK                   = 371; // sas
  const SANTALI                 = 372; // sat
  const SICILIAN                = 373; // scn
  const SCOTS                   = 374; // sco
  const SELKUP                  = 375; // sel
  const SEMITIC                 = 376; // sem
  const IRISH_OLD               = 377; // sga
  const SIGN                    = 378; // sgn
  const SHAN                    = 379; // shn
  const SIDAMO                  = 380; // sid
  const SINHALA                 = 381; // sin, si
  const SIOUAN                  = 382; // sio
  const SINO_TIBETAN            = 383; // sit
  const SLAVIC                  = 384; // sla
  const SLOVAK                  = 385; // slo (b), slk (t), sk
  const SLOVENIAN               = 386; // slv, sl
  const SAMI_SOUTHERN           = 387; // sma
  const SAMI_NORTHERN           = 388; // sme, se
  const SAMI                    = 389; // smi
  const SAMI_LULE               = 390; // smj
  const SAMI_IRARI              = 391; // smn
  const SAMOAN                  = 392; // smo, sm
  const SAMI_SKOLT              = 393; // sms
  const SHONA                   = 394; // sna, sn
  const SINDHI                  = 395; // snd, sd
  const SONINKE                 = 396; // snk
  const SOGDIAN                 = 397; // sog
  const SOMALI                  = 398; // som, so
  const SONGHAI                 = 399; // son
  const SOTHO_SOUTHERN          = 400; // sot, st
  const SPANISH                 = 401; // spa, es
  const SARDINIAN               = 402; // srd, sc
  const SRANAN_TONGO            = 403; // sm
  const SERBIAN                 = 404; // srp, sr
  const SERER                   = 405; // srr
  const NILO_SAHARAN            = 406; // ssa
  const SWATI                   = 407; // ssw, ss
  const SUKUMA                  = 408; // suk
  const SUNDANESE               = 409; // sun, su
  const SUSU                    = 410; // sus
  const SUMERIAN                = 411; // sux
  const SWAHILI                 = 412; // swa, sw
  const SWEDISH                 = 413; // swe, sv
  const SYRIAC_CLASSICAL        = 414; // syc
  const SYRIAC                  = 415; // syr
  const TAHITIAN                = 416; // tah, ty
  const TAI                     = 417; // tai
  const TAMIL                   = 418; // tam, ta
  const TATAR                   = 419; // tat, tt
  const TELUGU                  = 420; // tel, te
  const TIMNE                   = 421; // tem
  const TERENO                  = 422; // ter
  const TETUM                   = 423; // tet
  const TAJIK                   = 424; // tgk, tg
  const TAGALOG                 = 425; // tgl, tl
  const THAI                    = 426; // tha, th
  const TIGRE                   = 427; // tig
  const TIGRINYA                = 428; // tir, ti
  const TIV                     = 429; // tiv
  const TOKELAU                 = 430; // tkl
  const KLINGON                 = 431; // tlh
  const TLINGIT                 = 432; // tli
  const TAMASHEK                = 433; // tmh
  const TONGA_NYASA             = 434; // tog
  const TONGA_ISLANDS           = 435; // ton, to
  const TOK_PISIN               = 436; // tpi
  const TSIMSHIAN               = 437; // tsi
  const TSWANA                  = 438; // tsn, tn
  const TSONGA                  = 439; // tso, ts
  const TURKMEN                 = 440; // tuk, tk
  const TUMBUKA                 = 441; // tum
  const TUPI                    = 442; // tup
  const TURKISH                 = 443; // tur, tr
  const ALTAIC                  = 444; // tut
  const TUVALU                  = 445; // tvl
  const TWI                     = 446; // twi, tw
  const TUVINIAN                = 447; // tyv
  const UDMURT                  = 448; // udm
  const UGARITIC                = 449; // uga
  const UIGHUR                  = 450; // uig, ug
  const UKRAINIAN               = 451; // ukr, uk
  const UMBUNDU                 = 452; // umb
  const UNDETERMINED            = 453; // und
  const URDU                    = 454; // urd, ur
  const UZBEK                   = 455; // uzb, uz
  const VAI                     = 456; // vai
  const VENDA                   = 457; // ven, ve
  const VIETNAMESE              = 458; // vie, vi
  const VOLAPUK                 = 459; // vol, vo
  const VOTIC                   = 460; // vot
  const WAKASHAN                = 461; // wak
  const WOLAITTA                = 462; // wal
  const WARAY                   = 463; // war
  const WASHO                   = 464; // was
  const SORBIAN                 = 465; // wen
  const WALLOON                 = 466; // wln, wa
  const WOLOF                   = 467; // wol, wo
  const KALMYK                  = 468; // xal
  const XHOSA                   = 469; // xho, xh
  const YAO                     = 470; // yao
  const YAPESE                  = 471; // yap
  const YIDDISH                 = 472; // yid, yi
  const YORUBA                  = 473; // yor, yo
  const YUPIK                   = 474; // ypk
  const ZAPOTEC                 = 475; // zap
  const BLISSYMBOLS             = 476; // zbl
  const ZENAGA                  = 477; // zen
  const MOROCCAN_TAMAZIGHT      = 478; // zgh
  const ZHUANG                  = 479; // zha, za
  const ZANDE                   = 480; // znd
  const ZULU                    = 481; // zul, zu
  const ZUNI                    = 482; // zun
  const NOT_APPLICABLE          = 483; // zxx
  const ZAZA                    = 484; // zza
  const ENGLISH_CA              = 485; // en-ca
  const ENGLISH_GB              = 486; // en-gb
  const ENGLISH_US              = 487; // en-us


  /**
   * Get the language names associated with the id.
   *
   * @param int $id
   *   The id of the names to return.
   *
   * @return c_base_return_array
   *   An array of names.
   *   An empty array with the error bit set is returned on error.
   */
  public static function s_get_names_by_id($id);

  /**
   * Get the language names associated with the alias.
   *
   * @param string $alias
   *   The alias of the names to return.
   *
   * @return c_base_return_array
   *   An array of names.
   *   An empty array with the error bit set is returned on error.
   */
  public static function s_get_names_by_alias($alias);

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_int
   *   The numeric id.
   *   0 with the error bit set is returned on error.
   */
  public static function s_get_id_by_name($name);

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_int
   *   The numeric id.
   *   0 with the error bit set is returned on error.
   */
  public static function s_get_id_by_alias($alias);

  /**
   * Get the language aliases associated with the id.
   *
   * @param int $id
   *   The id of the aliases to return.
   *
   * @return c_base_return_array
   *   An array of aliases.
   *   An empty array with the error bit set is returned on error.
   */
  public static function s_get_aliases_by_id($id);

  /**
   * Get the language aliases associated with the name.
   *
   * @param string $name
   *   The language name of the aliases to return.
   *
   * @return c_base_return_array
   *   An array of aliases.
   *   An empty array with the error bit set is returned on error.
   */
  public static function s_get_aliases_by_name($name);

  /**
   * Get the id of the language considered to be default by the implementing class.
   *
   * @return c_base_return_int
   *   An integer representing the default language.
   *   0 without the error bit set is returned if there is no default language.
   *   0 with the error bit set is returned on error.
   */
  public static function s_get_default_id();

  /**
   * Get the name of the language considered to be default by the implementing class.
   *
   * @return c_base_return_string
   *   A string representing the default language.
   *   An empty string with the error bit set is returned on error.
   */
  public static function s_get_default_name();

  /**
   * Get an array of all ids associated with this class.
   *
   * @return c_base_return_array
   *   An array of ids, keyed by the unique ids.
   *   An empty array with the error bit set is returned on error.
   */
  public static function s_get_ids();

  /**
   * Get an array of all aliases associated with this class.
   *
   * @return c_base_return_array
   *   An array of aliases, keyed by the unique ids.
   *   An empty array with error bit set is returned on error.
   */
  public static function s_get_aliases();

  /**
   * Get an array of all names associated with this class.
   *
   * @return c_base_return_array
   *   An array of names, keyed by the unique ids.
   *   An empty array with error bit set is returned on error.
   */
  public static function s_get_names();

  /**
   * Get the language direction using the id.
   *
   * @param int $id
   *   The id of the language to process.
   *
   * @return c_base_return_status
   *   TRUE if LTR, FALSE if RTL.
   *   Error bit is set on error.
   */
  public static function s_get_ltr_by_id($id);
}

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
