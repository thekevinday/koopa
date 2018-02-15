<?php
/**
 * @file
 * Provides interfaces for managing the different supported languages.
 */
namespace n_koopa;

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
