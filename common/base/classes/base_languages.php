<?php
/**
 * @file
 * Provides a class for managing the different supported languages.
 */

/**
 * A generic interface for managing the different supported languages.
 *
 * Additional known sub-languages, such as en-us, are added even though they do not appear in the iso standard.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
interface i_base_language {
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
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given id.
   */
  static function s_get_names_by_id($id);

  /**
   * Get the language names associated with the alias.
   *
   * @param string $alias
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given alias.
   */
  static function s_get_names_by_alias($alias);

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_name($name);

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_alias($alias);

  /**
   * Get the language aliases associated with the id.
   *
   * @param int $id
   *   The id of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given id.
   */
  static function s_get_aliases_by_id($id);

  /**
   * Get the language aliases associated with the name.
   *
   * @param string $name
   *   The language name of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given name.
   */
  static function s_get_aliases_by_name($name);

  /**
   * Get the id of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_id();

  /**
   * Get the name of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_string
   *   A string representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_name();
}

/**
 * A language class specifically for english only languages.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
final class c_base_language_us_only implements i_base_language {

  private static $s_aliases = array(
    self::ENGLISH_US              => array('en-us'),
    self::ENGLISH                 => array('eng', 'en'),
    self::UNDETERMINED            => array('und'),
    self::NOT_APPLICABLE          => array('zxx'),
  );

  private static $s_names = array(
    self::ENGLISH_US              => array('US English'),
    self::ENGLISH                 => array('English'),
    self::UNDETERMINED            => array('Undetermined'),
    self::NOT_APPLICABLE          => array('No Linguistic Content', 'Not Applicable'),
  );

  private static $s_ids = array(
    'en-us' => self::ENGLISH_US,
    'eng'   => self::ENGLISH,
    'en'    => self::ENGLISH,
    'und'   => self::UNDETERMINED,
    'zxx'   => self::NOT_APPLICABLE,
  );


  /**
   * Get the language names associated with the id.
   *
   * @param int $id
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given id.
   */
  static function s_get_names_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language names associated with the alias.
   *
   * @param string $alias
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given alias.
   */
  static function s_get_names_by_alias($alias) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($name, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$name]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'alias', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($alias, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$alias]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language aliases associated with the id.
   *
   * @param int $id
   *   The id of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given id.
   */
  static function s_get_aliases_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language aliases associated with the name.
   *
   * @param string $name
   *   The language name of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given name.
   */
  static function s_get_aliases_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($name, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$name]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_id() {
    return c_base_return_int::s_new(self::ENGLISH_US);
  }

  /**
   * Get the name of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_string
   *   A string representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_name() {
    return c_base_return_string::s_new($this->s_aliases[self::ENGLISH_US]);
  }
}

/**
 * A generic class for managing the different supported languages.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
final class c_base_language_us_limited implements i_base_language {

  private static $s_aliases = array(
    self::ENGLISH_US              => array('en-us'),
    self::ENGLISH                 => array('eng', 'en'),
    self::FRENCH                  => array('fre', 'fra', 'fr'),
    self::SPANISH                 => array('spa', 'es'),
    self::INDONESIAN              => array('ind', 'id'),
    self::RUSSIAN                 => array('rus', 'ru'),
    self::CHINESE                 => array('chi', 'zho', 'zh'),
    self::UNDETERMINED            => array('und'),
    self::NOT_APPLICABLE          => array('zxx'),
  );

  private static $s_names = array(
    self::ENGLISH_US              => array('US English'),
    self::ENGLISH                 => array('English'),
    self::FRENCH                  => array('French'),
    self::SPANISH                 => array('Spanish', 'Castilian'),
    self::INDONESIAN              => array('Indonesian'),
    self::RUSSIAN                 => array('Russian'),
    self::CHINESE                 => array('Chinese'),
    self::UNDETERMINED            => array('Undetermined'),
    self::NOT_APPLICABLE          => array('No Linguistic Content', 'Not Applicable'),
  );

  private static $s_ids = array(
    'en-us' => self::ENGLISH_US,
    'eng'   => self::ENGLISH,
    'en'    => self::ENGLISH,
    'fre'   => self::FRENCH,
    'fra'   => self::FRENCH,
    'fr'    => self::FRENCH,
    'spa'   => self::SPANISH,
    'es'    => self::SPANISH,
    'ind'   => self::INDONESIAN,
    'id'    => self::INDONESIAN,
    'rus'   => self::RUSSIAN,
    'ru'    => self::RUSSIAN,
    'chi'   => self::CHINESE,
    'zho'   => self::CHINESE,
    'zh'    => self::CHINESE,
    'und'   => self::UNDETERMINED,
    'zxx'   => self::NOT_APPLICABLE,
  );


  /**
   * Get the language names associated with the id.
   *
   * @param int $id
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given id.
   */
  static function s_get_names_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language names associated with the alias.
   *
   * @param string $alias
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given alias.
   */
  static function s_get_names_by_alias($alias) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'alias', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($name, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$name]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'alias', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($alias, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$alias]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language aliases associated with the id.
   *
   * @param int $id
   *   The id of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given id.
   */
  static function s_get_aliases_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language aliases associated with the name.
   *
   * @param string $name
   *   The language name of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given name.
   */
  static function s_get_aliases_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($name, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$name]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_id() {
    return c_base_return_int::s_new(self::ENGLISH_US);
  }

  /**
   * Get the name of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_string
   *   A string representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_name() {
    return c_base_return_string::s_new($this->s_aliases[self::ENGLISH_US]);
  }
}

/**
 * A generic class for managing the different supported languages.
 *
 * @see: http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
final class c_base_language_us_all implements i_base_language {

  private static $s_aliases = array(
    self::ENGLISH_US              => array('en-us'),
    self::ENGLISH                 => array('eng', 'en'),
    self::ENGLISH_CA              => array('en-ca'),
    self::ENGLISH_GB              => array('en-gb'),
    self::AFAR                    => array('aar', 'aa'),
    self::ABKHAZIAN               => array('abk', 'ab'),
    self::ACHINESE                => array('ace'),
    self::ACOLI                   => array('ach'),
    self::ADANGME                 => array('ada'),
    self::ADYGHE                  => array('ady'),
    self::AFRO_ASIATIC            => array('afa'),
    self::AFRIHILI                => array('afh'),
    self::AFRIKAANS               => array('afr', 'af'),
    self::AINU                    => array('ain'),
    self::AKAN                    => array('aka', 'ak'),
    self::AKKADIAN                => array('akk'),
    self::ALBANIAN                => array('alb', 'sqi', 'sq'),
    self::ALEUT                   => array('ale'),
    self::ALGONQUIAN              => array('alg'),
    self::SOUTHERN_ALTAI          => array('alt'),
    self::AMHARIC                 => array('amh', 'am'),
    self::ENGLISH_OLD             => array('ang'),
    self::ANGIKA                  => array('anp'),
    self::APACHE                  => array('apa'),
    self::ARABIC                  => array('ara', 'ar'),
    self::ARAMAIC                 => array('arc'),
    self::ARAGONESE               => array('arg', 'an'),
    self::ARMENIAN                => array('arm', 'hye', 'hy'),
    self::MAPUDUNGUN              => array('am'),
    self::ARAPAHO                 => array('arp'),
    self::ARTIFICIAL              => array('art'),
    self::ARAWAK                  => array('arw'),
    self::ASSAMESE                => array('asm', 'as'),
    self::ASTURIAN                => array('ast'),
    self::ATHAPASCAN              => array('ath'),
    self::AUSTRALIAN              => array('aus'),
    self::AVARIC                  => array('ava', 'av'),
    self::AVESTAN                 => array('ave', 'ae'),
    self::AWADHI                  => array('awa'),
    self::AYMARA                  => array('aym', 'ay'),
    self::AZERBAIJANI             => array('aze', 'az'),
    self::BANDA                   => array('bad'),
    self::BAMILEKE                => array('bai'),
    self::BASHKIR                 => array('bak', 'ba'),
    self::BALUCHI                 => array('bal'),
    self::BAMBARA                 => array('bam', 'bm'),
    self::BALINESE                => array('ban'),
    self::BASQUE                  => array('baq', 'eus', 'eu'),
    self::BASA                    => array('bas'),
    self::BALTIC                  => array('bat'),
    self::BEJA                    => array('bej'),
    self::BELARUSIAN              => array('bel', 'be'),
    self::BEMBA                   => array('bem'),
    self::BENGALI                 => array('ben', 'bn'),
    self::BERBER                  => array('ber'),
    self::BHOJPURI                => array('bho'),
    self::BIHARI                  => array('bih', 'bh'),
    self::BIKOL                   => array('bik'),
    self::BINI                    => array('bin'),
    self::BISLAMA                 => array('bis', 'bi'),
    self::SIKSIKA                 => array('bla'),
    self::BANTU                   => array('bnt'),
    self::TIBETAN                 => array('tib', 'bod', 'bo'),
    self::BOSNIAN                 => array('bos', 'bs'),
    self::BRAJ                    => array('bra'),
    self::BRETON                  => array('bre'),
    self::BATAK                   => array('btk'),
    self::BURIAT                  => array('bua'),
    self::BUGINESE                => array('bug'),
    self::BULGARIAN               => array('bul'),
    self::BURMESE                 => array('bur', 'mya', 'my'),
    self::BLIN                    => array('byn'),
    self::CADDO                   => array('cad'),
    self::AMERICAN_INDIAN_CENTRAL => array('cai'),
    self::GALIBI_CARIB            => array('car'),
    self::CATALAN                 => array('cat', 'ca'),
    self::CAUCASIAN               => array('cau'),
    self::CEBUANO                 => array('ceb'),
    self::CELTIC                  => array('cel'),
    self::CZECH                   => array('cze', 'ces', 'cs'),
    self::CHAMORRO                => array('cha', 'ch'),
    self::CHIBCHA                 => array('chb'),
    self::CHECHEN                 => array('che', 'ce'),
    self::CHAGATAI                => array('chg'),
    self::CHINESE                 => array('chi', 'zho', 'zh'),
    self::CHUUKESE                => array('chk'),
    self::MARI                    => array('chm'),
    self::CHINOOK_JARGON          => array('chn'),
    self::CHOCTAW                 => array('cho'),
    self::CHIPEWYAN               => array('chp'),
    self::CHEROKEE                => array('chr'),
    self::CHURCH_SLAVIC           => array('chu', 'cu'),
    self::CHUVASH                 => array('chv', 'cv'),
    self::CHEYENNE                => array('chy'),
    self::CHAMIC                  => array('cmc'),
    self::COPTIC                  => array('cop'),
    self::CORNISH                 => array('cor'),
    self::CORSICAN                => array('cos', 'co'),
    self::CREOLES_ENGLISH         => array('cpe'),
    self::CREOLES_FRENCH          => array('cpf'),
    self::CREOLES_PORTUGESE       => array('cpp'),
    self::CREE                    => array('cre', 'cr'),
    self::CRIMEAN_TATAR           => array('crh'),
    self::CREOLES                 => array('crp'),
    self::KASHUBIAN               => array('csb'),
    self::CUSHITIC                => array('cus'),
    self::WELSH                   => array('wel', 'cym', 'cy'),
    self::DAKOTA                  => array('dak'),
    self::DANISH                  => array('dan', 'da'),
    self::DARGWA                  => array('dar'),
    self::LAND_DAYAK              => array('day'),
    self::DELAWARE                => array('del'),
    self::SLAVE                   => array('den'),
    self::GERMAN                  => array('ger', 'deu', 'de'),
    self::DOGRIB                  => array('dgr'),
    self::DINKA                   => array('din'),
    self::DIVEHI                  => array('div', 'dv'),
    self::DOGRI                   => array('doi'),
    self::DRAVIDIAN               => array('dra'),
    self::LOWER_SORBIAN           => array('dsb'),
    self::DUALA                   => array('dua'),
    self::DUTCH_MIDDLE            => array('dum'),
    self::DUTCH_FLEMISH           => array('dut', 'nld', 'nl'),
    self::DYULA                   => array('dyu'),
    self::DZONGKHA                => array('dzo', 'dz'),
    self::EFIK                    => array('efi'),
    self::EGYPTIAN                => array('egy'),
    self::EKAJUK                  => array('eka'),
    self::GREEK_MODERN            => array('gre', 'ell', 'el'),
    self::ELAMITE                 => array('elx'),
    self::ENGLISH_MIDDLE          => array('enm'),
    self::ESPERANTO               => array('epo', 'eo'),
    self::ESTONIAN                => array('est', 'et'),
    self::EWE                     => array('ewe', 'ee'),
    self::EWONDO                  => array('ewo'),
    self::FANG                    => array('fan'),
    self::FAROESE                 => array('fao', 'fo'),
    self::PERSIAN                 => array('per', 'fas', 'fa'),
    self::FANTI                   => array('fat'),
    self::FIJIAN                  => array('fij', 'fj'),
    self::FILIPINO                => array('fil'),
    self::FINNISH                 => array('fin', 'fi'),
    self::FINNO_UGRIAN            => array('fiu'),
    self::FON                     => array('fon'),
    self::FRENCH                  => array('fre', 'fra', 'fr'),
    self::FRENCH_MIDDLE           => array('frm'),
    self::FRENCH_OLD              => array('fro'),
    self::FRISIAN_NORTHERN        => array('frr'),
    self::FRISIAN_EASTERN         => array('frs'),
    self::FRISIAN_WESTERN         => array('fry', 'fy'),
    self::FULAH                   => array('ful', 'ff'),
    self::FRIULIAN                => array('fur'),
    self::GA                      => array('gaa'),
    self::GAYO                    => array('gay'),
    self::GBAYA                   => array('gba'),
    self::GERMANIC                => array('gem'),
    self::GEORGIAN                => array('geo', 'kat', 'ka'),
    self::GEEZ                    => array('gez'),
    self::GILBERTESE              => array('gil'),
    self::GAELIC                  => array('gla', 'gd'),
    self::IRISH                   => array('gle', 'ga'),
    self::GALICIAN                => array('glg', 'gl'),
    self::MANX                    => array('glv', 'gv'),
    self::GERMAN_MIDDLE_HIGH      => array('gmh'),
    self::GERMAN_OLD_HIGH         => array('goh'),
    self::GONDI                   => array('gon'),
    self::GORONTALO               => array('gor'),
    self::GOTHIC                  => array('got'),
    self::GREBO                   => array('grb'),
    self::GREEK_ANCIENT           => array('grc'),
    self::GUARANI                 => array('grm', 'gn'),
    self::GERMAN_SWISS            => array('gsw'),
    self::GUJARATI                => array('guj', 'gu'),
    self::GWICHIN                 => array('gwi'),
    self::HAIDA                   => array('hai'),
    self::HAITIAN                 => array('hat', 'ht'),
    self::HAUSA                   => array('hau', 'ha'),
    self::HAWAIIAN                => array('haw'),
    self::HEBREW                  => array('heb', 'he'),
    self::HERERO                  => array('her', 'hz'),
    self::HILIGAYNON              => array('hil'),
    self::HIMACHALI               => array('him'),
    self::HINDI                   => array('hin', 'hi'),
    self::HITTITE                 => array('hit'),
    self::HMONG                   => array('hmn'),
    self::HIRI_MOTU               => array('hmo', 'ho'),
    self::CROATIAN                => array('hrv'),
    self::SORBIAN_UPPER           => array('hsb'),
    self::HUNGARIAN               => array('hun', 'hu'),
    self::HUPA                    => array('hup'),
    self::IBAN                    => array('iba'),
    self::IGBO                    => array('ibo', 'ig'),
    self::ICELANDIC               => array('ice', 'isl', 'is'),
    self::IDO                     => array('ido', 'io'),
    self::SICHUAN_YI              => array('iii', 'ii'),
    self::IJO                     => array('ijo'),
    self::INUKTITUT               => array('iku', 'iu'),
    self::INTERLINGUE             => array('ile', 'ie'),
    self::ILOKO                   => array('ilo'),
    self::INTERLINGUA             => array('ina', 'ia'),
    self::INDIC                   => array('inc'),
    self::INDONESIAN              => array('ind', 'id'),
    self::INDO_EUROPEAN           => array('ine'),
    self::INGUSH                  => array('inh'),
    self::INUPIAQ                 => array('ipk', 'ik'),
    self::IRANIAN                 => array('ira'),
    self::IROQUOIAN               => array('iro'),
    self::ITALIAN                 => array('ita', 'it'),
    self::JAVANESE                => array('jav', 'jv'),
    self::LOJBAN                  => array('jbo'),
    self::JAPANESE                => array('jpn', 'ja'),
    self::JUDEO_PERSIAN           => array('jpr'),
    self::JUDEO_ARABIC            => array('jrb'),
    self::KARA_KALPAK             => array('kaa'),
    self::KABYLE                  => array('kab'),
    self::KACHIN                  => array('kac'),
    self::KALAALLISUT             => array('kal', 'kl'),
    self::KAMBA                   => array('kam'),
    self::KANNADA                 => array('kan', 'kn'),
    self::KAREN                   => array('kar'),
    self::KASHMIRI                => array('kas', 'ks'),
    self::KANURI                  => array('kau', 'kr'),
    self::KAWI                    => array('kaw'),
    self::KAZAKH                  => array('kaz'),
    self::KABARDIAN               => array('kbd'),
    self::KHASI                   => array('kha'),
    self::KHOISAN                 => array('khi'),
    self::CENTRAL_KHMER           => array('khm', 'km'),
    self::KHOTANESE               => array('kho'),
    self::KIKUYU                  => array('kik', 'ki'),
    self::KINYARWANDA             => array('kin', 'rw'),
    self::KIRGHIZ                 => array('kir', 'ky'),
    self::KIMBUNDU                => array('kmb'),
    self::KONKANI                 => array('kok'),
    self::KOMI                    => array('kom', 'kv'),
    self::KONGO                   => array('kon', 'kg'),
    self::KOREAN                  => array('kor', 'ko'),
    self::KOSRAEAN                => array('kos'),
    self::KPELLE                  => array('kpe'),
    self::KARACHAY_BALKAR         => array('krc'),
    self::KARELIAN                => array('krl'),
    self::KRU                     => array('kro'),
    self::KURUKH                  => array('kru'),
    self::KUANYAMA                => array('kua', 'kj'),
    self::KUMYK                   => array('kum'),
    self::KURDISH                 => array('kur', 'ku'),
    self::KUTENAI                 => array('kut'),
    self::LADINO                  => array('lad'),
    self::LAHNDA                  => array('lah'),
    self::LAMBA                   => array('lam'),
    self::LAO                     => array('lao', 'lo'),
    self::LATIN                   => array('lat', 'la'),
    self::LATVIAN                 => array('lav', 'lv'),
    self::LEZGHIAN                => array('lez'),
    self::LIMBURGAN               => array('lim', 'li'),
    self::LINGALA                 => array('lin', 'ln'),
    self::LITHUANIAN              => array('lit', 'lt'),
    self::MONGO                   => array('lol'),
    self::LOZI                    => array('loz'),
    self::LUXEMBOURGISH           => array('ltz', 'lb'),
    self::LUBA_LULUA              => array('lua'),
    self::LUBA_KATANGA            => array('lub', 'lu'),
    self::GANDA                   => array('lug', 'lg'),
    self::LUISENO                 => array('lui'),
    self::LUNDA                   => array('lun'),
    self::LUO                     => array('luo'),
    self::LUSHAI                  => array('lus'),
    self::MACEDONIAN              => array('mac', 'mkd', 'mk'),
    self::MADURESE                => array('mad'),
    self::MAGAHI                  => array('mag'),
    self::MARSHALLESE             => array('mah'),
    self::MAITHILI                => array('mai'),
    self::MAKASAR                 => array('mak'),
    self::MALAYALAM               => array('mal'),
    self::MANDINGO                => array('man'),
    self::MAORI                   => array('mao', 'mri', 'mi'),
    self::AUSTRONESIAN            => array('map'),
    self::MARATHI                 => array('mar', 'mr'),
    self::MASAI                   => array('mas'),
    self::MALAY                   => array('may', 'msa', 'ms'),
    self::MOKSHA                  => array('mdf'),
    self::MANDAR                  => array('mdr'),
    self::MENDE                   => array('men'),
    self::IRISH_MIDDLE            => array('mga'),
    self::MIKMAQ                  => array('mic'),
    self::MINANGKABAU             => array('min'),
    self::UNCODED                 => array('mis'),
    self::MON_KHMER               => array('mkh'),
    self::MALAGASY                => array('mlg'),
    self::MALTESE                 => array('mlt'),
    self::MANCHU                  => array('mnc'),
    self::MANIPURI                => array('mni'),
    self::MANOBO                  => array('mno'),
    self::MOHAWK                  => array('moh'),
    self::MONGOLIAN               => array('mon', 'mn'),
    self::MOSSI                   => array('mos'),
    self::MULTIPLE                => array('mul'),
    self::MUNDA                   => array('mun'),
    self::CREEK                   => array('mus'),
    self::MIRANDESE               => array('mwl'),
    self::MARWARI                 => array('mwr'),
    self::MAYAN                   => array('myn'),
    self::ERZYA                   => array('myv'),
    self::NAHUATL                 => array('nah'),
    self::AMERICAN_INDIAN_NORTH   => array('nai'),
    self::NEAPOLITAN              => array('nap'),
    self::NAURU                   => array('nau', 'na'),
    self::NAVAJO                  => array('nav', 'nv'),
    self::NDEBELE_SOUTH           => array('nbl', 'nr'),
    self::NDEBELE_NORTH           => array('nde', 'nd'),
    self::NDONGA                  => array('ndo', 'ng'),
    self::LOW_GERMAN              => array('nds'),
    self::NEPALI                  => array('nep', 'ne'),
    self::NEPAL_BHASA             => array('new'),
    self::NIAS                    => array('nia'),
    self::NIGER_KORDOFANIAN       => array('nic'),
    self::NIUEAN                  => array('niu'),
    self::NORWEGIAN_NYNORSK       => array('nno', 'nn'),
    self::BOKMAL                  => array('nob', 'nb'),
    self::NOGAI                   => array('nog'),
    self::NORSE_OLD               => array('non'),
    self::NORWEGIAN               => array('nor', 'no'),
    self::NKO                     => array('nqo'),
    self::PEDI                    => array('nso'),
    self::NUBIAN                  => array('nub'),
    self::CLASSICAL_NEWARI        => array('nwc'),
    self::CHICHEWA                => array('nya', 'ny'),
    self::NYAMWEZI                => array('nym'),
    self::NYANKOLE                => array('nyn'),
    self::NYORO                   => array('nyo'),
    self::NZIMA                   => array('nzi'),
    self::OCCITAN                 => array('oci', 'oc'),
    self::OJIBWA                  => array('oji', 'oj'),
    self::ORIYA                   => array('ori', 'or'),
    self::OROMO                   => array('orm', 'om'),
    self::OSAGE                   => array('osa'),
    self::OSSETIAN                => array('oss', 'os'),
    self::OTTOMAN                 => array('ota'),
    self::OTOMIAN                 => array('oto'),
    self::PAPUAN                  => array('paa'),
    self::PANGASINAN              => array('pag'),
    self::PAHLAVI                 => array('pal'),
    self::PAMPANGA                => array('pam'),
    self::PANJABI                 => array('pan', 'pa'),
    self::PAPIAMENTO              => array('pap'),
    self::PALAUAN                 => array('pau'),
    self::PERSIAN_OLD             => array('peo'),
    self::PHILIPPINE              => array('phi'),
    self::PHOENICIAN              => array('phn'),
    self::PALI                    => array('pli', 'pi'),
    self::POLISH                  => array('pol', 'pl'),
    self::POHNPEIAN               => array('pon'),
    self::PORTUGUESE              => array('por', 'pt'),
    self::PRAKRIT                 => array('pra'),
    self::PROVENCAL               => array('pro'),
    self::PUSHTO                  => array('pus', 'ps'),
    self::QUECHUA                 => array('que', 'qu'),
    self::RAJASTHANI              => array('raj'),
    self::RAPANUI                 => array('rap'),
    self::RAROTONGAN              => array('rar'),
    self::ROMANCE                 => array('roa'),
    self::ROMANSH                 => array('roh', 'rm'),
    self::ROMANY                  => array('rom'),
    self::ROMANIAN                => array('rum', 'ron', 'ro'),
    self::RUNDI                   => array('run', 'rn'),
    self::AROMANIAN               => array('rup'),
    self::RUSSIAN                 => array('rus', 'ru'),
    self::SANDAWE                 => array('sad'),
    self::SANGO                   => array('sag', 'sg'),
    self::YAKUT                   => array('sah'),
    self::AMERICAN_INDIAN_SOUTH   => array('sai'),
    self::SALISHAN                => array('sal'),
    self::SAMARITAN               => array('sam'),
    self::SANSKRIT                => array('san', 'sa'),
    self::SASAK                   => array('sas'),
    self::SANTALI                 => array('sat'),
    self::SICILIAN                => array('scn'),
    self::SCOTS                   => array('sco'),
    self::SELKUP                  => array('sel'),
    self::SEMITIC                 => array('sem'),
    self::IRISH_OLD               => array('sga'),
    self::SIGN                    => array('sgn'),
    self::SHAN                    => array('shn'),
    self::SIDAMO                  => array('sid'),
    self::SINHALA                 => array('sin', 'si'),
    self::SIOUAN                  => array('sio'),
    self::SINO_TIBETAN            => array('sit'),
    self::SLAVIC                  => array('sla'),
    self::SLOVAK                  => array('slo', 'slk', 'sk'),
    self::SLOVENIAN               => array('slv', 'sl'),
    self::SAMI_SOUTHERN           => array('sma'),
    self::SAMI_NORTHERN           => array('sme', 'se'),
    self::SAMI                    => array('smi'),
    self::SAMI_LULE               => array('smj'),
    self::SAMI_IRARI              => array('smn'),
    self::SAMOAN                  => array('smo', 'sm'),
    self::SAMI_SKOLT              => array('sms'),
    self::SHONA                   => array('sna', 'sn'),
    self::SINDHI                  => array('snd', 'sd'),
    self::SONINKE                 => array('snk'),
    self::SOGDIAN                 => array('sog'),
    self::SOMALI                  => array('som', 'so'),
    self::SONGHAI                 => array('son'),
    self::SOTHO_SOUTHERN          => array('sot', 'st'),
    self::SPANISH                 => array('spa', 'es'),
    self::SARDINIAN               => array('srd', 'sc'),
    self::SRANAN_TONGO            => array('sm'),
    self::SERBIAN                 => array('srp', 'sr'),
    self::SERER                   => array('srr'),
    self::NILO_SAHARAN            => array('ssa'),
    self::SWATI                   => array('ssw', 'ss'),
    self::SUKUMA                  => array('suk'),
    self::SUNDANESE               => array('sun', 'su'),
    self::SUSU                    => array('sus'),
    self::SUMERIAN                => array('sux'),
    self::SWAHILI                 => array('swa', 'sw'),
    self::SWEDISH                 => array('swe', 'sv'),
    self::SYRIAC_CLASSICAL        => array('syc'),
    self::SYRIAC                  => array('syr'),
    self::TAHITIAN                => array('tah', 'ty'),
    self::TAI                     => array('tai'),
    self::TAMIL                   => array('tam', 'ta'),
    self::TATAR                   => array('tat', 'tt'),
    self::TELUGU                  => array('tel', 'te'),
    self::TIMNE                   => array('tem'),
    self::TERENO                  => array('ter'),
    self::TETUM                   => array('tet'),
    self::TAJIK                   => array('tgk', 'tg'),
    self::TAGALOG                 => array('tgl', 'tl'),
    self::THAI                    => array('tha', 'th'),
    self::TIGRE                   => array('tig'),
    self::TIGRINYA                => array('tir', 'ti'),
    self::TIV                     => array('tiv'),
    self::TOKELAU                 => array('tkl'),
    self::KLINGON                 => array('tlh'),
    self::TLINGIT                 => array('tli'),
    self::TAMASHEK                => array('tmh'),
    self::TONGA_NYASA             => array('tog'),
    self::TONGA_ISLANDS           => array('ton', 'to'),
    self::TOK_PISIN               => array('tpi'),
    self::TSIMSHIAN               => array('tsi'),
    self::TSWANA                  => array('tsn', 'tn'),
    self::TSONGA                  => array('tso', 'ts'),
    self::TURKMEN                 => array('tuk', 'tk'),
    self::TUMBUKA                 => array('tum'),
    self::TUPI                    => array('tup'),
    self::TURKISH                 => array('tur', 'tr'),
    self::ALTAIC                  => array('tut'),
    self::TUVALU                  => array('tvl'),
    self::TWI                     => array('twi', 'tw'),
    self::TUVINIAN                => array('tyv'),
    self::UDMURT                  => array('udm'),
    self::UGARITIC                => array('uga'),
    self::UIGHUR                  => array('uig', 'ug'),
    self::UKRAINIAN               => array('ukr', 'uk'),
    self::UMBUNDU                 => array('umb'),
    self::UNDETERMINED            => array('und'),
    self::URDU                    => array('urd', 'ur'),
    self::UZBEK                   => array('uzb', 'uz'),
    self::VAI                     => array('vai'),
    self::VENDA                   => array('ven', 've'),
    self::VIETNAMESE              => array('vie', 'vi'),
    self::VOLAPUK                 => array('vol', 'vo'),
    self::VOTIC                   => array('vot'),
    self::WAKASHAN                => array('wak'),
    self::WOLAITTA                => array('wal'),
    self::WARAY                   => array('war'),
    self::WASHO                   => array('was'),
    self::SORBIAN                 => array('wen'),
    self::WALLOON                 => array('wln', 'wa'),
    self::WOLOF                   => array('wol', 'wo'),
    self::KALMYK                  => array('xal'),
    self::XHOSA                   => array('xho', 'xh'),
    self::YAO                     => array('yao'),
    self::YAPESE                  => array('yap'),
    self::YIDDISH                 => array('yid', 'yi'),
    self::YORUBA                  => array('yor', 'yo'),
    self::YUPIK                   => array('ypk'),
    self::ZAPOTEC                 => array('zap'),
    self::BLISSYMBOLS             => array('zbl'),
    self::ZENAGA                  => array('zen'),
    self::MOROCCAN_TAMAZIGHT      => array('zgh'),
    self::ZHUANG                  => array('zha', 'za'),
    self::ZANDE                   => array('znd'),
    self::ZULU                    => array('zul', 'zu'),
    self::ZUNI                    => array('zun'),
    self::NOT_APPLICABLE          => array('zxx'),
    self::ZAZA                    => array('zza'),
  );

  private static $s_names = array(
    self::ENGLISH_US              => array('US English'),
    self::ENGLISH                 => array('English'),
    self::ENGLISH_CA              => array('Canadian English'),
    self::ENGLISH_GB              => array('British English'),
    self::AFAR                    => array('Afar'),
    self::ABKHAZIAN               => array('Abkhazian'),
    self::ACHINESE                => array('Achinese'),
    self::ACOLI                   => array('Acoli'),
    self::ADANGME                 => array('Adangme'),
    self::ADYGHE                  => array('Adyghe'),
    self::AFRO_ASIATIC            => array('Afro-Asiatic', 'Adygei'),
    self::AFRIHILI                => array('Afrihili'),
    self::AFRIKAANS               => array('Afrikaans'),
    self::AINU                    => array('Ainu'),
    self::AKAN                    => array('Akan'),
    self::AKKADIAN                => array('Akkadian'),
    self::ALBANIAN                => array('Albanian'),
    self::ALEUT                   => array('Aleut'),
    self::ALGONQUIAN              => array('Algonquian'),
    self::SOUTHERN_ALTAI          => array('Southern Altai'),
    self::AMHARIC                 => array('Amharic'),
    self::ENGLISH_OLD             => array('Old English'),
    self::ANGIKA                  => array('Angika'),
    self::APACHE                  => array('Apache'),
    self::ARABIC                  => array('Arabic'),
    self::ARAMAIC                 => array('Official Aramaic', 'Imperial Aramaic'),
    self::ARAGONESE               => array('Aragonese'),
    self::ARMENIAN                => array('Armenian'),
    self::MAPUDUNGUN              => array('Mapudungun', 'Mapuche'),
    self::ARAPAHO                 => array('Arapaho'),
    self::ARTIFICIAL              => array('Artificial'),
    self::ARAWAK                  => array('Arawak'),
    self::ASSAMESE                => array('Assamese'),
    self::ASTURIAN                => array('Asturian', 'Bable', 'Leonese', 'Asturleonese'),
    self::ATHAPASCAN              => array('Athapascan'),
    self::AUSTRALIAN              => array('Australian'),
    self::AVARIC                  => array('Avaric'),
    self::AVESTAN                 => array('Avestan'),
    self::AWADHI                  => array('Awadhi'),
    self::AYMARA                  => array('Aymara'),
    self::AZERBAIJANI             => array('Azerbaijani'),
    self::BANDA                   => array('Banda'),
    self::BAMILEKE                => array('Bamileke'),
    self::BASHKIR                 => array('Bashkir'),
    self::BALUCHI                 => array('Baluchi'),
    self::BAMBARA                 => array('Bambara'),
    self::BALINESE                => array('Balinese'),
    self::BASQUE                  => array('Basque'),
    self::BASA                    => array('Basa'),
    self::BALTIC                  => array('Baltic'),
    self::BEJA                    => array('Beja'),
    self::BELARUSIAN              => array('Belarusian'),
    self::BEMBA                   => array('Bemba'),
    self::BENGALI                 => array('Bengali'),
    self::BERBER                  => array('Berber'),
    self::BHOJPURI                => array('Bhojpuri'),
    self::BIHARI                  => array('Bihari'),
    self::BIKOL                   => array('Bikol'),
    self::BINI                    => array('Bini', 'Edo'),
    self::BISLAMA                 => array('Bislama'),
    self::SIKSIKA                 => array('Siksika'),
    self::BANTU                   => array('Bantu'),
    self::TIBETAN                 => array('Tibetan'),
    self::BOSNIAN                 => array('Bosnian'),
    self::BRAJ                    => array('Braj'),
    self::BRETON                  => array('Breton'),
    self::BATAK                   => array('Batak'),
    self::BURIAT                  => array('Buriat'),
    self::BUGINESE                => array('Buginese'),
    self::BULGARIAN               => array('Bulgarian'),
    self::BURMESE                 => array('Burmese'),
    self::BLIN                    => array('Blin', 'Bilin'),
    self::CADDO                   => array('Caddo'),
    self::AMERICAN_INDIAN_CENTRAL => array('Central American Indian'),
    self::GALIBI_CARIB            => array('Galibi Carib'),
    self::CATALAN                 => array('Catalan', 'Valencian'),
    self::CAUCASIAN               => array('Caucasian'),
    self::CEBUANO                 => array('Cebuano'),
    self::CELTIC                  => array('Celtic'),
    self::CZECH                   => array('Czech'),
    self::CHAMORRO                => array('Chamorro'),
    self::CHIBCHA                 => array('Chibcha'),
    self::CHECHEN                 => array('Chechen'),
    self::CHAGATAI                => array('Chagatai'),
    self::CHINESE                 => array('Chinese'),
    self::CHUUKESE                => array('Chuukese'),
    self::MARI                    => array('Mari'),
    self::CHINOOK_JARGON          => array('Chinook jargon'),
    self::CHOCTAW                 => array('Choctaw'),
    self::CHIPEWYAN               => array('Chipewyan', 'Dene Suline'),
    self::CHEROKEE                => array('Cherokee'),
    self::CHURCH_SLAVIC           => array('Church Slavic', 'Old Slavonic', 'Church Slavonic', 'Old Bulgarian', 'Old Church Slavonic'),
    self::CHUVASH                 => array('Chuvash'),
    self::CHEYENNE                => array('Cheyenne'),
    self::CHAMIC                  => array('Chamic'),
    self::COPTIC                  => array('Coptic'),
    self::CORNISH                 => array('Cornish'),
    self::CORSICAN                => array('Corsican'),
    self::CREOLES_ENGLISH         => array('Creoles and Pidgins, English Based'),
    self::CREOLES_FRENCH          => array('Creoles and Pidgins, French Based'),
    self::CREOLES_PORTUGESE       => array('Creoles and Pidgins, Portugese Based'),
    self::CREE                    => array('Cree'),
    self::CRIMEAN_TATAR           => array('Crimean Tatar', 'Crimean Turkish'),
    self::CREOLES                 => array('Creoles and Pidgins'),
    self::KASHUBIAN               => array('Kashubian'),
    self::CUSHITIC                => array('Cushitic'),
    self::WELSH                   => array('Welsh'),
    self::DAKOTA                  => array('Dakota'),
    self::DANISH                  => array('Danish'),
    self::DARGWA                  => array('Dargwa'),
    self::LAND_DAYAK              => array('Land Dayak'),
    self::DELAWARE                => array('Delaware'),
    self::SLAVE                   => array('Athapascan Slave'),
    self::GERMAN                  => array('German'),
    self::DOGRIB                  => array('Dogrib'),
    self::DINKA                   => array('Dinka'),
    self::DIVEHI                  => array('Divehi', 'Dhivehi', 'Maldivian'),
    self::DOGRI                   => array('Dogri'),
    self::DRAVIDIAN               => array('Dravidian'),
    self::LOWER_SORBIAN           => array('Lower Sorbian'),
    self::DUALA                   => array('Duala'),
    self::DUTCH_MIDDLE            => array('Middle Dutch'),
    self::DUTCH_FLEMISH           => array('Dutch', 'Flemish'),
    self::DYULA                   => array('Dyula'),
    self::DZONGKHA                => array('Dzongkha'),
    self::EFIK                    => array('Efik'),
    self::EGYPTIAN                => array('Ancient Egyptian'),
    self::EKAJUK                  => array('Ekajuk'),
    self::GREEK_MODERN            => array('Modern Greek'),
    self::ELAMITE                 => array('Elamite'),
    self::ENGLISH_MIDDLE          => array('Middle English'),
    self::ESPERANTO               => array('Esperanto'),
    self::ESTONIAN                => array('Estonian'),
    self::EWE                     => array('Ewe'),
    self::EWONDO                  => array('Ewondo'),
    self::FANG                    => array('Fang'),
    self::FAROESE                 => array('Faroese'),
    self::PERSIAN                 => array('Persian'),
    self::FANTI                   => array('Fanti'),
    self::FIJIAN                  => array('Fijian'),
    self::FILIPINO                => array('Filipino', 'Pilipino'),
    self::FINNISH                 => array('Finnish'),
    self::FINNO_UGRIAN            => array('Finno-Ugrian '),
    self::FON                     => array('Fon'),
    self::FRENCH                  => array('French'),
    self::FRENCH_MIDDLE           => array('Middle French'),
    self::FRENCH_OLD              => array('Old French'),
    self::FRISIAN_NORTHERN        => array('Northern Frisian'),
    self::FRISIAN_EASTERN         => array('Eastern Frisian'),
    self::FRISIAN_WESTERN         => array('Southern Frisian'),
    self::FULAH                   => array('Fulah'),
    self::FRIULIAN                => array('Friulian'),
    self::GA                      => array('Ga'),
    self::GAYO                    => array('Gayo'),
    self::GBAYA                   => array('Gbaya'),
    self::GERMANIC                => array('Germanic'),
    self::GEORGIAN                => array('Georgian'),
    self::GEEZ                    => array('Geez'),
    self::GILBERTESE              => array('Gilbertese'),
    self::GAELIC                  => array('Gaelic', 'Scottish Gaelic'),
    self::IRISH                   => array('Irish'),
    self::GALICIAN                => array('Galician'),
    self::MANX                    => array('Manx'),
    self::GERMAN_MIDDLE_HIGH      => array('Middle High German'),
    self::GERMAN_OLD_HIGH         => array('Old High German'),
    self::GONDI                   => array('Gondi'),
    self::GORONTALO               => array('Gorontalo'),
    self::GOTHIC                  => array('Gothic'),
    self::GREBO                   => array('Grebo'),
    self::GREEK_ANCIENT           => array('Ancient Greek'),
    self::GUARANI                 => array('Guarani'),
    self::GERMAN_SWISS            => array('Swiss German', 'Alemannic', 'Alsatian'),
    self::GUJARATI                => array('Gujarati'),
    self::GWICHIN                 => array('Gwich\'in'),
    self::HAIDA                   => array('Haida'),
    self::HAITIAN                 => array('Haitian', 'Haitian Creole'),
    self::HAUSA                   => array('Hausa'),
    self::HAWAIIAN                => array('Hawaiian'),
    self::HEBREW                  => array('Hebrew'),
    self::HERERO                  => array('Herero'),
    self::HILIGAYNON              => array('Hiligaynon'),
    self::HIMACHALI               => array('Himachali', 'Western Pahari'),
    self::HINDI                   => array('Hindi'),
    self::HITTITE                 => array('Hittite'),
    self::HMONG                   => array('Hmong', 'Mong'),
    self::HIRI_MOTU               => array('Hiri Motu'),
    self::CROATIAN                => array('Croatian'),
    self::SORBIAN_UPPER           => array('Upper Sorbian'),
    self::HUNGARIAN               => array('Hungarian'),
    self::HUPA                    => array('Hupa'),
    self::IBAN                    => array('Iban'),
    self::IGBO                    => array('Igbo'),
    self::ICELANDIC               => array('Icelandic'),
    self::IDO                     => array('Ido'),
    self::SICHUAN_YI              => array('Sichuan Yi', 'Nuosu'),
    self::IJO                     => array('Ijo'),
    self::INUKTITUT               => array('Inuktitut'),
    self::INTERLINGUE             => array('Interlingue'),
    self::ILOKO                   => array('Iloko'),
    self::INTERLINGUA             => array('Interlingua'),
    self::INDIC                   => array('Indic'),
    self::INDONESIAN              => array('Indonesian'),
    self::INDO_EUROPEAN           => array('Indo-European'),
    self::INGUSH                  => array('Ingush'),
    self::INUPIAQ                 => array('Inupiaq'),
    self::IRANIAN                 => array('Iranian'),
    self::IROQUOIAN               => array('Iroquoian'),
    self::ITALIAN                 => array('Italian'),
    self::JAVANESE                => array('Javanese'),
    self::LOJBAN                  => array('Lojban'),
    self::JAPANESE                => array('Japanese'),
    self::JUDEO_PERSIAN           => array('Judeo-Persian'),
    self::JUDEO_ARABIC            => array('Judeo-Arabic'),
    self::KARA_KALPAK             => array('Kara-Kalpak'),
    self::KABYLE                  => array('Kabyle'),
    self::KACHIN                  => array('Kachin', 'Jingpho'),
    self::KALAALLISUT             => array('Kalaallisut', 'Greenlandic'),
    self::KAMBA                   => array('Kamba'),
    self::KANNADA                 => array('Kannada'),
    self::KAREN                   => array('Karen'),
    self::KASHMIRI                => array('Kashmiri'),
    self::KANURI                  => array('Kanuri'),
    self::KAWI                    => array('Kawi'),
    self::KAZAKH                  => array('Kazakh'),
    self::KABARDIAN               => array('Kabardian'),
    self::KHASI                   => array('Khasi'),
    self::KHOISAN                 => array('Khoisan'),
    self::CENTRAL_KHMER           => array('Central Khmer'),
    self::KHOTANESE               => array('Khotanese', 'Sakan'),
    self::KIKUYU                  => array('Kikuyu', 'Gikuyu'),
    self::KINYARWANDA             => array('Kinyarwanda'),
    self::KIRGHIZ                 => array('Kirghiz', 'Kyrgyz'),
    self::KIMBUNDU                => array('Kimbundu'),
    self::KONKANI                 => array('Konkani'),
    self::KOMI                    => array('Komi'),
    self::KONGO                   => array('Kongo'),
    self::KOREAN                  => array('Korean'),
    self::KOSRAEAN                => array('Kosraean'),
    self::KPELLE                  => array('Kpelle'),
    self::KARACHAY_BALKAR         => array('Karachay-Balkar'),
    self::KARELIAN                => array('Karelian'),
    self::KRU                     => array('Kru'),
    self::KURUKH                  => array('Kurukh'),
    self::KUANYAMA                => array('Kuanyama', 'Kwanyama'),
    self::KUMYK                   => array('Kumyk'),
    self::KURDISH                 => array('Kurdish'),
    self::KUTENAI                 => array('Kutenai'),
    self::LADINO                  => array('Ladino'),
    self::LAHNDA                  => array('Lahnda'),
    self::LAMBA                   => array('Lamba'),
    self::LAO                     => array('Lao'),
    self::LATIN                   => array('Latin'),
    self::LATVIAN                 => array('Latvian'),
    self::LEZGHIAN                => array('Lezghian'),
    self::LIMBURGAN               => array('Limburgan', 'Limburger', 'Limburgish'),
    self::LINGALA                 => array('Lingala'),
    self::LITHUANIAN              => array('Lithuanian'),
    self::MONGO                   => array('Mongo'),
    self::LOZI                    => array('Lozi'),
    self::LUXEMBOURGISH           => array('Luxembourgish', 'Letzeburgesch'),
    self::LUBA_LULUA              => array('Luba-Lulua'),
    self::LUBA_KATANGA            => array('Luba-Katanga'),
    self::GANDA                   => array('Ganda'),
    self::LUISENO                 => array('Luiseno'),
    self::LUNDA                   => array('Lunda'),
    self::LUO                     => array('Luo'),
    self::LUSHAI                  => array('Lushai'),
    self::MACEDONIAN              => array('Macedonian'),
    self::MADURESE                => array('Madurese'),
    self::MAGAHI                  => array('Magahi'),
    self::MARSHALLESE             => array('Marshallese'),
    self::MAITHILI                => array('Maithili'),
    self::MAKASAR                 => array('Makasar'),
    self::MALAYALAM               => array('Malayalam'),
    self::MANDINGO                => array('Mandingo'),
    self::MAORI                   => array('Maori'),
    self::AUSTRONESIAN            => array('Austronesian'),
    self::MARATHI                 => array('Marathi'),
    self::MASAI                   => array('Masai'),
    self::MALAY                   => array('Malay'),
    self::MOKSHA                  => array('Moksha'),
    self::MANDAR                  => array('Mandar'),
    self::MENDE                   => array('Mende'),
    self::IRISH_MIDDLE            => array('Middle Irish'),
    self::MIKMAQ                  => array('Mi\'kmaq', 'Micmac'),
    self::MINANGKABAU             => array('Minangkabau'),
    self::UNCODED                 => array('Uncoded'),
    self::MON_KHMER               => array('Mon-Khmer'),
    self::MALAGASY                => array('Malagasy'),
    self::MALTESE                 => array('Maltese'),
    self::MANCHU                  => array('Manchu'),
    self::MANIPURI                => array('Manipuri'),
    self::MANOBO                  => array('Manobo'),
    self::MOHAWK                  => array('Mohawk'),
    self::MONGOLIAN               => array('Mongolian'),
    self::MOSSI                   => array('Mossi'),
    self::MULTIPLE                => array('Multiple'),
    self::MUNDA                   => array('Munda'),
    self::CREEK                   => array('Creek'),
    self::MIRANDESE               => array('Mirandese'),
    self::MARWARI                 => array('Marwari'),
    self::MAYAN                   => array('Mayan'),
    self::ERZYA                   => array('Erzya'),
    self::NAHUATL                 => array('Nahuatl'),
    self::AMERICAN_INDIAN_NORTH   => array('North American Indian'),
    self::NEAPOLITAN              => array('Neapolitan'),
    self::NAURU                   => array('Nauru'),
    self::NAVAJO                  => array('Navajo', 'Navaho'),
    self::NDEBELE_SOUTH           => array('South Ndebele'),
    self::NDEBELE_NORTH           => array('North Ndebele'),
    self::NDONGA                  => array('Ndonga'),
    self::LOW_GERMAN              => array('Low German', 'Low Saxon'),
    self::NEPALI                  => array('Nepali'),
    self::NEPAL_BHASA             => array('Nepal Bhasa', 'Newari'),
    self::NIAS                    => array('Nias'),
    self::NIGER_KORDOFANIAN       => array('Niger-Kordofanian'),
    self::NIUEAN                  => array('Niuean'),
    self::NORWEGIAN_NYNORSK       => array('Norwegian Nynorsk'),
    self::BOKMAL                  => array('Bokmål', 'Norwegian Bokmål'),
    self::NOGAI                   => array('Nogai'),
    self::NORSE_OLD               => array('Old Norse'),
    self::NORWEGIAN               => array('Norwegian'),
    self::NKO                     => array('N\'Ko'),
    self::PEDI                    => array('Pedi', 'Sepedi', 'Northern Sotho'),
    self::NUBIAN                  => array('Nubian'),
    self::CLASSICAL_NEWARI        => array('Classical Newari', 'Old Newari', 'Classical Nepal Bhasa'),
    self::CHICHEWA                => array('Chichewa', 'Chewa', 'Nyanja'),
    self::NYAMWEZI                => array('Nyamwezi'),
    self::NYANKOLE                => array('Nyankole'),
    self::NYORO                   => array('Nyoro'),
    self::NZIMA                   => array('Nzima'),
    self::OCCITAN                 => array('Occitan'),
    self::OJIBWA                  => array('Ojibwa'),
    self::ORIYA                   => array('Oriya'),
    self::OROMO                   => array('Oromo'),
    self::OSAGE                   => array('Osage'),
    self::OSSETIAN                => array('Ossetian', 'Ossetic'),
    self::OTTOMAN                 => array('Ottoman Turkish'),
    self::OTOMIAN                 => array('Otomian'),
    self::PAPUAN                  => array('Papuan'),
    self::PANGASINAN              => array('Pangasinan'),
    self::PAHLAVI                 => array('Pahlavi'),
    self::PAMPANGA                => array('Pampanga', 'Kapampangan'),
    self::PANJABI                 => array('Panjabi', 'Punjabi'),
    self::PAPIAMENTO              => array('Papiamento'),
    self::PALAUAN                 => array('Palauan'),
    self::PERSIAN_OLD             => array('Old Persian'),
    self::PHILIPPINE              => array('Philippine'),
    self::PHOENICIAN              => array('Phoenician'),
    self::PALI                    => array('Pali'),
    self::POLISH                  => array('Polish'),
    self::POHNPEIAN               => array('Pohnpeian'),
    self::PORTUGUESE              => array('Portuguese'),
    self::PRAKRIT                 => array('Prakrit'),
    self::PROVENCAL               => array('Old Provençal', 'Old Occitan'),
    self::PUSHTO                  => array('Pushto', 'Pashto'),
    self::QUECHUA                 => array('Quechua'),
    self::RAJASTHANI              => array('Rajasthani'),
    self::RAPANUI                 => array('Rapanui'),
    self::RAROTONGAN              => array('Rarotongan', 'Cook Islands Maori'),
    self::ROMANCE                 => array('Romance'),
    self::ROMANSH                 => array('Romansh'),
    self::ROMANY                  => array('Romany'),
    self::ROMANIAN                => array('Romanian', 'Moldavian', 'Moldovan'),
    self::RUNDI                   => array('Rundi'),
    self::AROMANIAN               => array('Aromanian', 'Arumanian', 'Macedo-Romanian'),
    self::RUSSIAN                 => array('Russian'),
    self::SANDAWE                 => array('Sandawe'),
    self::SANGO                   => array('Sango'),
    self::YAKUT                   => array('Yakut'),
    self::AMERICAN_INDIAN_SOUTH   => array('South American Indian'),
    self::SALISHAN                => array('Salishan'),
    self::SAMARITAN               => array('Samaritan'),
    self::SANSKRIT                => array('Sanskrit'),
    self::SASAK                   => array('Sasak'),
    self::SANTALI                 => array('Santali'),
    self::SICILIAN                => array('Sicilian'),
    self::SCOTS                   => array('Scots'),
    self::SELKUP                  => array('Selkup'),
    self::SEMITIC                 => array('Semitic'),
    self::IRISH_OLD               => array('Old Irish'),
    self::SIGN                    => array('Sign Language'),
    self::SHAN                    => array('Shan'),
    self::SIDAMO                  => array('Sidamo'),
    self::SINHALA                 => array('Sinhala', 'Sinhalese'),
    self::SIOUAN                  => array('Siouan'),
    self::SINO_TIBETAN            => array('Sino-Tibetan'),
    self::SLAVIC                  => array('Slavic'),
    self::SLOVAK                  => array('Slovak'),
    self::SLOVENIAN               => array('Slovenian'),
    self::SAMI_SOUTHERN           => array('Southern Sami'),
    self::SAMI_NORTHERN           => array('Northern Sami'),
    self::SAMI                    => array('Sami'),
    self::SAMI_LULE               => array('Lule Sami'),
    self::SAMI_IRARI              => array('Inari Sami'),
    self::SAMOAN                  => array('Samoan'),
    self::SAMI_SKOLT              => array('Skolt Sami'),
    self::SHONA                   => array('Shona'),
    self::SINDHI                  => array('Sindhi'),
    self::SONINKE                 => array('Soninke'),
    self::SOGDIAN                 => array('Sogdian'),
    self::SOMALI                  => array('Somali'),
    self::SONGHAI                 => array('Songhai'),
    self::SOTHO_SOUTHERN          => array('Southern Sotho'),
    self::SPANISH                 => array('Spanish', 'Castilian'),
    self::SARDINIAN               => array('Sardinian'),
    self::SRANAN_TONGO            => array('Sranan Tongo'),
    self::SERBIAN                 => array('Serbian'),
    self::SERER                   => array('Serer'),
    self::NILO_SAHARAN            => array('Nilo-Saharan'),
    self::SWATI                   => array('Swati'),
    self::SUKUMA                  => array('Sukuma'),
    self::SUNDANESE               => array('Sundanese'),
    self::SUSU                    => array('Susu'),
    self::SUMERIAN                => array('Sumerian'),
    self::SWAHILI                 => array('Swahili'),
    self::SWEDISH                 => array('Swedish'),
    self::SYRIAC_CLASSICAL        => array('Classical Syriac'),
    self::SYRIAC                  => array('Syriac'),
    self::TAHITIAN                => array('Tahitian'),
    self::TAI                     => array('Tai'),
    self::TAMIL                   => array('Tamil'),
    self::TATAR                   => array('Tatar'),
    self::TELUGU                  => array('Telugu'),
    self::TIMNE                   => array('Timne'),
    self::TERENO                  => array('Tereno'),
    self::TETUM                   => array('Tetum'),
    self::TAJIK                   => array('Tajik'),
    self::TAGALOG                 => array('Tagalog'),
    self::THAI                    => array('Thai'),
    self::TIGRE                   => array('Tigre'),
    self::TIGRINYA                => array('Tigrinya'),
    self::TIV                     => array('Tiv'),
    self::TOKELAU                 => array('Tokelau'),
    self::KLINGON                 => array('Klingon', 'tlhIngan-Hol'),
    self::TLINGIT                 => array('Tlingit'),
    self::TAMASHEK                => array('Tamashek'),
    self::TONGA_NYASA             => array('Nyasa Tonga'),
    self::TONGA_ISLANDS           => array('Tonga Islands Tonga', 'to'),
    self::TOK_PISIN               => array('Tok Pisin'),
    self::TSIMSHIAN               => array('Tsimshian'),
    self::TSWANA                  => array('Tswana'),
    self::TSONGA                  => array('Tsonga'),
    self::TURKMEN                 => array('Turkmen'),
    self::TUMBUKA                 => array('Tumbuka'),
    self::TUPI                    => array('Tupi'),
    self::TURKISH                 => array('Turkish'),
    self::ALTAIC                  => array('Altaic'),
    self::TUVALU                  => array('Tuvalu'),
    self::TWI                     => array('Twi'),
    self::TUVINIAN                => array('Tuvinian'),
    self::UDMURT                  => array('Udmurt'),
    self::UGARITIC                => array('Ugaritic'),
    self::UIGHUR                  => array('Uighur', 'Uyghur'),
    self::UKRAINIAN               => array('Ukrainian'),
    self::UMBUNDU                 => array('Umbundu'),
    self::UNDETERMINED            => array('Undetermined'),
    self::URDU                    => array('Urdu'),
    self::UZBEK                   => array('Uzbek'),
    self::VAI                     => array('Vai'),
    self::VENDA                   => array('Venda'),
    self::VIETNAMESE              => array('Vietnamese'),
    self::VOLAPUK                 => array('Volapük'),
    self::VOTIC                   => array('Votic'),
    self::WAKASHAN                => array('Wakashan'),
    self::WOLAITTA                => array('Wolaitta', 'Wolaytta'),
    self::WARAY                   => array('Waray'),
    self::WASHO                   => array('Washo'),
    self::SORBIAN                 => array('Sorbian'),
    self::WALLOON                 => array('Walloon'),
    self::WOLOF                   => array('Wolof'),
    self::KALMYK                  => array('Kalmyk', 'Oirat'),
    self::XHOSA                   => array('Xhosa'),
    self::YAO                     => array('Yao'),
    self::YAPESE                  => array('Yapese'),
    self::YIDDISH                 => array('Yiddish'),
    self::YORUBA                  => array('Yoruba'),
    self::YUPIK                   => array('Yupik'),
    self::ZAPOTEC                 => array('Zapotec'),
    self::BLISSYMBOLS             => array('Blissymbols', 'Blissymbolics', 'Bliss'),
    self::ZENAGA                  => array('Zenaga'),
    self::MOROCCAN_TAMAZIGHT      => array('Standard Moroccan Tamazight'),
    self::ZHUANG                  => array('Zhuang', 'Chuang'),
    self::ZANDE                   => array('Zande'),
    self::ZULU                    => array('Zulu'),
    self::ZUNI                    => array('Zuni'),
    self::NOT_APPLICABLE          => array('No Linguistic Content', 'Not Applicable'),
    self::ZAZA                    => array('Zaza', 'Dimili', 'Dimli', 'Kirdki', 'Kirmanjki', 'Zazaki'),
  );

  private static $s_ids = array(
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
  );


  /**
   * Get the language names associated with the id.
   *
   * @param int $id
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given id.
   */
  static function s_get_names_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_names)) {
      return c_base_return_array::s_new(self::$s_names[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language names associated with the alias.
   *
   * @param string $alias
   *   The id of the names to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of names or FALSE on error.
   *   FALSE without the error flag means that there are no names associated with the given alias.
   */
  static function s_get_names_by_alias($alias) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'alias', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($name, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$name]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id associated with the language name.
   *
   * @param string $name
   *   The string associated with the id
   *
   * @return c_base_return_status|c_base_return_int
   *   The numeric id or FALSE on error.
   *   FALSE without the error flag means that there are no ids associated with the given name.
   */
  static function s_get_id_by_alias($alias) {
    if (!is_string($alias)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'alias', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($alias, self::$s_ids)) {
      return c_base_return_int::s_new(self::$s_ids[$alias]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language aliases associated with the id.
   *
   * @param int $id
   *   The id of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given id.
   */
  static function s_get_aliases_by_id($id) {
    if (!is_int($id) && !is_numeric($id)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'id', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($id, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$id]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the language aliases associated with the name.
   *
   * @param string $name
   *   The language name of the aliases to return.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array of aliases or FALSE on error.
   *   FALSE without the error flag means that there are no aliases associated with the given name.
   */
  static function s_get_aliases_by_name($name) {
    if (!is_string($name)) {
      $error = c_base_error::s_log(NULL, array('arguments' => array(':argument_name' => 'name', ':function_name' => __CLASS__ . '->' . __FUNCTION__)), i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (array_key_exists($name, self::$s_aliases)) {
      return c_base_return_array::s_new(self::$s_aliases[$name]);
    }

    return new c_base_return_false();
  }

  /**
   * Get the id of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_id() {
    return c_base_return_int::s_new(self::ENGLISH_US);
  }

  /**
   * Get the name of the language considered to be default by the implementing class.
   *
   * @return c_base_return_status|c_base_return_string
   *   A string representing the default language.
   *   FALSE without the error flag means that there are no languages assigned as default.
   */
  static function s_get_default_name() {
    return c_base_return_string::s_new($this->s_aliases[self::ENGLISH_US]);
  }
}
