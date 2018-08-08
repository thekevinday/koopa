<?php
/**
 * @file
 * Provides a class for html markup.
 */
namespace n_koopa;

require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/base/classes/base_mime.php');
require_once('common/base/classes/base_charset.php');

require_once('common/base/traits/base_rfc_string.php');

/**
 * A generic class for html attribute types.
 *
 * @fixme: all aria-* tags are set to text, specific types need to be eventially defined.
 *
 * @see: https://www.w3.org/TR/html5/dom.html#index-aria-roles
 * @see: https://www.w3.org/TR/html5/dom.html#index-aria-global
 */
class c_base_markup_attributes {
  const ATTRIBUTE_NONE                   = 0;
  const ATTRIBUTE_ABBR                   = 1; // text
  const ATTRIBUTE_ACCESS_KEY             = 2; // single letter
  const ATTRIBUTE_ACCEPT                 = 3; // c_base_mime integer
  const ATTRIBUTE_ACCEPT_CHARACTER_SET   = 4; // c_base_charset integer
  const ATTRIBUTE_ACTION                 = 5; // text
  const ATTRIBUTE_ALTERNATE              = 6; // text
  const ATTRIBUTE_ASYNCHRONOUS           = 7; // async, use TRUE/FALSE
  const ATTRIBUTE_ATTRIBUTE_NAME         = 8; // text
  const ATTRIBUTE_AUTO_COMPLETE          = 9; // on or off, use TRUE/FALSE
  const ATTRIBUTE_AUTO_FOCUS             = 10; // autofocus, use TRUE/FALSE
  const ATTRIBUTE_AUTO_PLAY              = 11; // autoplay, use TRUE/FALSE
  const ATTRIBUTE_BY                     = 12; // text
  const ATTRIBUTE_CALCULATE_MODE         = 13; // text
  const ATTRIBUTE_CENTER_X               = 14; // number
  const ATTRIBUTE_CENTER_Y               = 15; // number
  const ATTRIBUTE_CLASS                  = 16; // array of strings
  const ATTRIBUTE_CLIP_PATH              = 17; // text
  const ATTRIBUTE_CLIP_PATH_UNITS        = 18; // text
  const ATTRIBUTE_CHALLENGE              = 19; // challenge, use TRUE/FALSE
  const ATTRIBUTE_CHARACTER_SET          = 20; // c_base_charset integer
  const ATTRIBUTE_CHECKED                = 21; // checked, use TRUE/FALSE
  const ATTRIBUTE_CITE                   = 22; // text
  const ATTRIBUTE_COLOR                  = 23; // text
  const ATTRIBUTE_COLUMNS                = 23; // number
  const ATTRIBUTE_COLUMN_SPAN            = 24; // number
  const ATTRIBUTE_CONTENT                = 25; // text
  const ATTRIBUTE_CONTENT_EDITABLE       = 26; // text: true, false, inherit
  const ATTRIBUTE_CONTROLS               = 27; // controls, use TRUE/FALSE
  const ATTRIBUTE_COORDINATES            = 28; // text
  const ATTRIBUTE_CROSS_ORIGIN           = 29; // anonymous, use-credentials
  const ATTRIBUTE_D                      = 30; // text
  const ATTRIBUTE_D_X                    = 31; // number
  const ATTRIBUTE_D_Y                    = 32; // number
  const ATTRIBUTE_DATA                   = 33; // text
  const ATTRIBUTE_DATE_TIME              = 34; // text
  const ATTRIBUTE_DEFAULT                = 35; // default, use TRUE/FALSE
  const ATTRIBUTE_DEFER                  = 36; // defer, use TRUE/FALSE
  const ATTRIBUTE_DIRECTION              = 37; // number representing id of language, may use NULL for 'auto'.
  const ATTRIBUTE_DIRECTION_NAME         = 38; // text, inputname.dir
  const ATTRIBUTE_DISABLED               = 39; // disabled, use TRUE/FALSE
  const ATTRIBUTE_DOWNLOAD               = 40; // text
  const ATTRIBUTE_DURATION               = 41; // text
  const ATTRIBUTE_ENCODING_TYPE          = 42; // c_base_mime integer
  const ATTRIBUTE_FILL                   = 43; // text
  const ATTRIBUTE_FILL_RULE              = 44; // text
  const ATTRIBUTE_FILL_STROKE            = 45; // text
  const ATTRIBUTE_FOCUS_X                = 46; // number
  const ATTRIBUTE_FOCUS_Y                = 47; // number
  const ATTRIBUTE_FONT_SPECIFICATION     = 48; // text
  const ATTRIBUTE_FOR                    = 49; // text: element id
  const ATTRIBUTE_FORM                   = 50; // text: form id
  const ATTRIBUTE_FORM_ACTION            = 51; // text: url
  const ATTRIBUTE_FORM_ENCODE_TYPE       = 52; // c_base_mime integer
  const ATTRIBUTE_FORM_METHOD            = 53; // text: get or post, use HTTP_METHOD_GET and HTTP_METHOD_POST
  const ATTRIBUTE_FORM_NO_VALIDATE       = 54; // formnovalidate, use TRUE/FALSE
  const ATTRIBUTE_FORM_TARGET            = 55; // text, _blank, _self, _parent, _top, URL
  const ATTRIBUTE_FORMAT                 = 56; // text
  const ATTRIBUTE_FROM                   = 57; // text
  const ATTRIBUTE_GLYPH_REFERENCE        = 58; // text
  const ATTRIBUTE_GRADIANT_TRANSFORM     = 59; // text
  const ATTRIBUTE_GRADIANT_UNITS         = 60; // text
  const ATTRIBUTE_GRAPHICS               = 61; // text
  const ATTRIBUTE_HEADERS                = 62; // text
  const ATTRIBUTE_HEIGHT                 = 63; // text
  const ATTRIBUTE_HIDDEN                 = 64; // TRUE/FALSE
  const ATTRIBUTE_HIGH                   = 65; // number
  const ATTRIBUTE_HREF                   = 66; // text
  const ATTRIBUTE_HREF_LANGUAGE          = 67; // i_base_languages
  const ATTRIBUTE_HREF_NO                = 68; // text
  const ATTRIBUTE_HTTP_EQUIV             = 69; // text
  const ATTRIBUTE_ICON                   = 70; // text
  const ATTRIBUTE_ID                     = 71; // text
  const ATTRIBUTE_IN                     = 72; // text
  const ATTRIBUTE_IN_2                   = 73; // text
  const ATTRIBUTE_IS_MAP                 = 74; // text
  const ATTRIBUTE_KEY_POINTS             = 75; // text
  const ATTRIBUTE_KEY_TYPE               = 76; // text: rsa, dsa, ec
  const ATTRIBUTE_KIND                   = 77; // text
  const ATTRIBUTE_LABEL                  = 78; // text
  const ATTRIBUTE_LANGUAGE               = 79; // i_base_languages, int
  const ATTRIBUTE_LENGTH_ADJUST          = 80; // text
  const ATTRIBUTE_LIST                   = 81; // text, datalist_id
  const ATTRIBUTE_LOCAL                  = 82; // text
  const ATTRIBUTE_LONG_DESCRIPTION       = 83; // text
  const ATTRIBUTE_LOOP                   = 84; // loop, use TRUE/FALSE
  const ATTRIBUTE_LOW                    = 85; // number
  const ATTRIBUTE_MARKERS                = 86; // text
  const ATTRIBUTE_MARKER_HEIGHT          = 87; // number
  const ATTRIBUTE_MARKER_UNITS           = 88; // text
  const ATTRIBUTE_MARKER_WIDTH           = 89; // number
  const ATTRIBUTE_MASK_CONTENT_UNITS     = 90; // text
  const ATTRIBUTE_MASK_UNITS             = 91; // text
  const ATTRIBUTE_MAXIMUM                = 92; // text: number, date
  const ATTRIBUTE_MAXIMUM_NUMBER         = 93; // number
  const ATTRIBUTE_MAXIMUM_LENGTH         = 94; // number
  const ATTRIBUTE_MEDIA                  = 95; // text
  const ATTRIBUTE_METHOD                 = 96; // text
  const ATTRIBUTE_MINIMUM                = 97; // text: number, date
  const ATTRIBUTE_MINIMUM_NUMBER         = 98; // number
  const ATTRIBUTE_MODE                   = 99; // text
  const ATTRIBUTE_MULTIPLE               = 100; // multiple, use TRUE/FALSE
  const ATTRIBUTE_MUTED                  = 101; // muted, use TRUE/FALSE
  const ATTRIBUTE_NAME                   = 102; // text
  const ATTRIBUTE_NO_VALIDATE            = 103; // novalidate, TRUE/FALSE
  const ATTRIBUTE_ON_ABORT               = 104; // text
  const ATTRIBUTE_ON_AFTER_PRINT         = 105; // text
  const ATTRIBUTE_ON_ANIMATION_END       = 106; // text
  const ATTRIBUTE_ON_ANIMATION_ITERATION = 107; // text
  const ATTRIBUTE_ON_ANIMATION_start     = 108; // text
  const ATTRIBUTE_ON_BEFORE_UNLOAD       = 109; // text
  const ATTRIBUTE_ON_BEFORE_PRINT        = 110; // text
  const ATTRIBUTE_ON_BLUR                = 111; // text
  const ATTRIBUTE_ON_CANCEL              = 112; // text
  const ATTRIBUTE_ON_CLICK               = 113; // text
  const ATTRIBUTE_ON_CONTEXT_MENU        = 114; // text
  const ATTRIBUTE_ON_COPY                = 115; // text
  const ATTRIBUTE_ON_CUT                 = 116; // text
  const ATTRIBUTE_ON_CAN_PLAY            = 117; // text
  const ATTRIBUTE_ON_CAN_PLAY_THROUGH    = 118; // text
  const ATTRIBUTE_ON_CHANGE              = 119; // text
  const ATTRIBUTE_ON_CUE_CHANGE          = 120; // text
  const ATTRIBUTE_ON_DOUBLE_CLICK        = 121; // text
  const ATTRIBUTE_ON_DRAG                = 122; // text
  const ATTRIBUTE_ON_DRAG_END            = 123; // text
  const ATTRIBUTE_ON_DRAG_ENTER          = 124; // text
  const ATTRIBUTE_ON_DRAG_LEAVE          = 125; // text
  const ATTRIBUTE_ON_DRAG_OVER           = 126; // text
  const ATTRIBUTE_ON_DRAG_START          = 127; // text
  const ATTRIBUTE_ON_DROP                = 128; // text
  const ATTRIBUTE_ON_DURATION_CHANGE     = 129; // text
  const ATTRIBUTE_ON_EMPTIED             = 130; // text
  const ATTRIBUTE_ON_ENDED               = 131; // text
  const ATTRIBUTE_ON_ERROR               = 132; // text
  const ATTRIBUTE_ON_FOCUS               = 133; // text
  const ATTRIBUTE_ON_FOCUS_IN            = 134; // text
  const ATTRIBUTE_ON_FOCUS_OUT           = 135; // text
  const ATTRIBUTE_ON_HASH_CHANGE         = 136; // text
  const ATTRIBUTE_ON_INPUT               = 137; // text
  const ATTRIBUTE_ON_INSTALLED           = 138; // text
  const ATTRIBUTE_ON_INVALID             = 139; // text
  const ATTRIBUTE_ON_KEY_DOWN            = 140; // text
  const ATTRIBUTE_ON_KEY_PRESS           = 141; // text
  const ATTRIBUTE_ON_KEY_UP              = 142; // text
  const ATTRIBUTE_ON_LOAD                = 143; // text
  const ATTRIBUTE_ON_LOADED_DATA         = 144; // text
  const ATTRIBUTE_ON_LOADED_META_DATA    = 145; // text
  const ATTRIBUTE_ON_LOAD_START          = 146; // text
  const ATTRIBUTE_ON_MOUSE_DOWN          = 147; // text
  const ATTRIBUTE_ON_MOUSE_ENTER         = 148; // text
  const ATTRIBUTE_ON_MOUSE_LEAVE         = 149; // text
  const ATTRIBUTE_ON_MOUSE_MOVE          = 150; // text
  const ATTRIBUTE_ON_MOUSE_OVER          = 151; // text
  const ATTRIBUTE_ON_MOUSE_OUT           = 152; // text
  const ATTRIBUTE_ON_MOUSE_UP            = 153; // text
  const ATTRIBUTE_ON_MESSAGE             = 154; // text
  const ATTRIBUTE_ON_MOUSE_WHEEL         = 155; // text
  const ATTRIBUTE_ON_OPEN                = 156; // text
  const ATTRIBUTE_ON_ONLINE              = 157; // text
  const ATTRIBUTE_ON_OFFLINE             = 158; // text
  const ATTRIBUTE_ON_PAGE_SHOW           = 159; // text
  const ATTRIBUTE_ON_PAGE_HIDE           = 160; // text
  const ATTRIBUTE_ON_PASTE               = 161; // text
  const ATTRIBUTE_ON_PAUSE               = 162; // text
  const ATTRIBUTE_ON_PLAY                = 163; // text
  const ATTRIBUTE_ON_PLAYING             = 164; // text
  const ATTRIBUTE_ON_PROGRESS            = 165; // text
  const ATTRIBUTE_ON_POP_STATE           = 166; // text
  const ATTRIBUTE_ON_RATED_CHANGE        = 167; // text
  const ATTRIBUTE_ON_RESIZE              = 168; // text
  const ATTRIBUTE_ON_RESET               = 169; // text
  const ATTRIBUTE_ON_RATE_CHANGE         = 170; // text
  const ATTRIBUTE_ON_SCROLL              = 171; // text
  const ATTRIBUTE_ON_SEARCH              = 172; // text
  const ATTRIBUTE_ON_SELECT              = 173; // text
  const ATTRIBUTE_ON_SUBMIT              = 174; // text
  const ATTRIBUTE_ON_SEEKED              = 175; // text
  const ATTRIBUTE_ON_SEEKING             = 176; // text
  const ATTRIBUTE_ON_STALLED             = 177; // text
  const ATTRIBUTE_ON_SUSPEND             = 178; // text
  const ATTRIBUTE_ON_SHOW                = 179; // text
  const ATTRIBUTE_ON_STORAGE             = 180; // text
  const ATTRIBUTE_ON_TIME_UPDATE         = 181; // text
  const ATTRIBUTE_ON_TRANSITION_END      = 182; // text
  const ATTRIBUTE_ON_TOGGLE              = 183; // text
  const ATTRIBUTE_ON_TOUCH_CANCEL        = 184; // text
  const ATTRIBUTE_ON_TOUCH_END           = 185; // text
  const ATTRIBUTE_ON_TOUCH_MOVE          = 186; // text
  const ATTRIBUTE_ON_TOUCH_START         = 187; // text
  const ATTRIBUTE_ON_UNLOAD              = 188; // text
  const ATTRIBUTE_ON_VOLUME_CHANGE       = 189; // text
  const ATTRIBUTE_ON_WAITING             = 190; // text
  const ATTRIBUTE_ON_WHEEL               = 191; // text
  const ATTRIBUTE_OFFSET                 = 192; // text
  const ATTRIBUTE_OPEN                   = 193; // text
  const ATTRIBUTE_OPTIMUM                = 194; // number
  const ATTRIBUTE_ORIENTATION            = 195; // text
  const ATTRIBUTE_PATTERN                = 196; // text, regular expression
  const ATTRIBUTE_PATTERN_CONTENT_UNITS  = 197; // text
  const ATTRIBUTE_PATTERN_TRANSFORM      = 198; // text
  const ATTRIBUTE_PATTERN_UNITS          = 199; // text
  const ATTRIBUTE_PATH                   = 200; // text
  const ATTRIBUTE_PATH_LENGTH            = 201; // text
  const ATTRIBUTE_PLACE_HOLDER           = 202; // text
  const ATTRIBUTE_POINTS                 = 203; // text
  const ATTRIBUTE_POSTER                 = 204; // text
  const ATTRIBUTE_PRELOAD                = 205; // text
  const ATTRIBUTE_PRESERVE_ASPECT_RATIO  = 196; // text
  const ATTRIBUTE_RADIO_GROUP            = 207; // text
  const ATTRIBUTE_RADIUS                 = 208; // number
  const ATTRIBUTE_RADIUS_X               = 209; // number
  const ATTRIBUTE_RADIUS_Y               = 210; // number
  const ATTRIBUTE_READONLY               = 211; // readonly, use TRUE/FALSE
  const ATTRIBUTE_REFERENCE_X            = 212; // number
  const ATTRIBUTE_REFERENCE_Y            = 213; // number
  const ATTRIBUTE_REL                    = 214; // text: alternate, author, dns-prefetch, help, icon, license, next, pingback, preconnect, prefetch, preload, prerender, prev, search, stylesheet
  const ATTRIBUTE_RENDERING_INTENT       = 215; // text
  const ATTRIBUTE_REPEAT_COUNT           = 216; // text
  const ATTRIBUTE_REQUIRED               = 217; // required, use TRUE/FALSE
  const ATTRIBUTE_REVERSED               = 218; // reversed, use TRUE/FALSE
  const ATTRIBUTE_ROLE                   = 219; // text
  const ATTRIBUTE_ROTATE                 = 220; // text
  const ATTRIBUTE_ROWS                   = 221; // number
  const ATTRIBUTE_ROW_SPAN               = 222; // number
  const ATTRIBUTE_SANDBOX                = 223; // text
  const ATTRIBUTE_SCOPE                  = 224; // text
  const ATTRIBUTE_SCOPED                 = 225; // scoped, use TRUE/FALSE
  const ATTRIBUTE_SELECTED               = 226; // selected, use TRUE/FALSE
  const ATTRIBUTE_SHAPE                  = 227; // text
  const ATTRIBUTE_SIZE                   = 228; // number
  const ATTRIBUTE_SIZES                  = 229; // text: HeightxWidth, any
  const ATTRIBUTE_SORTABLE               = 230; // sortable, use TRUE/FALSE
  const ATTRIBUTE_SORTED                 = 231; // text
  const ATTRIBUTE_SOURCE                 = 232; // text: url
  const ATTRIBUTE_SOURCE_DOCUMENT        = 233; // text
  const ATTRIBUTE_SOURCE_LANGUAGE        = 234; // i_base_languages
  const ATTRIBUTE_SOURCE_SET             = 235; // text
  const ATTRIBUTE_SPAN                   = 236; // text
  const ATTRIBUTE_SPELLCHECK             = 237; // TRUE/FALSE
  const ATTRIBUTE_SPREAD_METHOD          = 238; // text
  const ATTRIBUTE_START                  = 239; // number
  const ATTRIBUTE_STEP                   = 240; // number
  const ATTRIBUTE_STOP_COLOR             = 241; // text
  const ATTRIBUTE_STOP_OPACITY           = 242; // text
  const ATTRIBUTE_STYLE                  = 243; // text
  const ATTRIBUTE_TAB_INDEX              = 244; // number
  const ATTRIBUTE_TARGET                 = 245; // text, _blank, _self, _parent, _top, URL
  const ATTRIBUTE_TEXT_LENGTH            = 246; // text
  const ATTRIBUTE_TEXT_CONTENT_ELEMENTS  = 247; // text
  const ATTRIBUTE_TITLE                  = 248; // text
  const ATTRIBUTE_TRANSFORM              = 249; // text
  const ATTRIBUTE_TRANSLATE              = 250; // text
  const ATTRIBUTE_TO                     = 251; // text
  const ATTRIBUTE_TYPE                   = 252; // mime type
  const ATTRIBUTE_TYPE_BUTTON            = 253; // text
  const ATTRIBUTE_TYPE_LABEL             = 254; // text
  const ATTRIBUTE_TYPE_LIST              = 255; // text
  const ATTRIBUTE_TYPE_SVG               = 256; // text
  const ATTRIBUTE_USE_MAP                = 257; // text
  const ATTRIBUTE_VALUE                  = 258; // text
  const ATTRIBUTE_VALUE_NUMBER           = 259; // number
  const ATTRIBUTE_VIEW_BOX               = 260; // text
  const ATTRIBUTE_WIDTH                  = 261; // text
  const ATTRIBUTE_WRAP                   = 262; // hard, soft
  const ATTRIBUTE_X                      = 263; // number
  const ATTRIBUTE_X_1                    = 264; // number
  const ATTRIBUTE_X_2                    = 265; // number
  const ATTRIBUTE_X_LINK_ACTUATE         = 266; // number
  const ATTRIBUTE_X_LINK_HREF            = 267; // number
  const ATTRIBUTE_X_LINK_SHOW            = 268; // number
  const ATTRIBUTE_Y                      = 269; // number
  const ATTRIBUTE_Y_1                    = 270; // number
  const ATTRIBUTE_Y_2                    = 271; // number
  const ATTRIBUTE_XML                    = 272; // text
  const ATTRIBUTE_XMLNS                  = 273; // text
  const ATTRIBUTE_XMLNS_XLINK            = 274; // text
  const ATTRIBUTE_XML_SPACE              = 275; // text
  const ATTRIBUTE_ZOOM_AND_PAN           = 276; // text

  // wai-air roles/attributes/etc..
  const ATTRIBUTE_ARIA_ATOMIC            = 277; // text
  const ATTRIBUTE_ARIA_AUTOCOMPLETE      = 278; // text
  const ATTRIBUTE_ARIA_ACTIVE_DESCENDANT = 279; // text
  const ATTRIBUTE_ARIA_BUSY              = 280; // text
  const ATTRIBUTE_ARIA_CHECKED           = 281; // text
  const ATTRIBUTE_ARIA_CONTROLS          = 282; // text
  const ATTRIBUTE_ARIA_DESCRIBED_BY      = 283; // text
  const ATTRIBUTE_ARIA_DISABLED          = 284; // text
  const ATTRIBUTE_ARIA_DROP_EFFECT       = 285; // text
  const ATTRIBUTE_ARIA_EXPANDED          = 286; // text
  const ATTRIBUTE_ARIA_FLOW_TO           = 287; // text
  const ATTRIBUTE_ARIA_GRABBED           = 288; // text
  const ATTRIBUTE_ARIA_HAS_POPUP         = 289; // text
  const ATTRIBUTE_ARIA_HIDDEN            = 290; // text
  const ATTRIBUTE_ARIA_INVALID           = 291; // text
  const ATTRIBUTE_ARIA_LABEL             = 292; // text
  const ATTRIBUTE_ARIA_LABELLED_BY       = 293; // text
  const ATTRIBUTE_ARIA_LEVEL             = 294; // text
  const ATTRIBUTE_ARIA_LIVE              = 295; // text
  const ATTRIBUTE_ARIA_MULTI_LINE        = 296; // text
  const ATTRIBUTE_ARIA_MULTI_SELECTABLE  = 297; // text
  const ATTRIBUTE_ARIA_ORIENTATION       = 298; // text
  const ATTRIBUTE_ARIA_OWNS              = 299; // text
  const ATTRIBUTE_ARIA_POSITION_INSET    = 300; // text
  const ATTRIBUTE_ARIA_PRESSED           = 301; // text
  const ATTRIBUTE_ARIA_READONLY          = 302; // text
  const ATTRIBUTE_ARIA_RELEVANT          = 303; // text
  const ATTRIBUTE_ARIA_REQUIRED          = 304; // text
  const ATTRIBUTE_ARIA_SELECTED          = 305; // text
  const ATTRIBUTE_ARIA_SET_SIZE          = 306; // text
  const ATTRIBUTE_ARIA_SORT              = 307; // text
  const ATTRIBUTE_ARIA_VALUE_MAXIMUM     = 308; // text
  const ATTRIBUTE_ARIA_VALUE_MINIMIM     = 309; // text
  const ATTRIBUTE_ARIA_VALUE_NOW         = 310; // text
  const ATTRIBUTE_ARIA_VALUE_TEXT        = 311; // text

  // xml attributes
  const ATTRIBUTE_XLINK_SHOW    = 312; // text
  const ATTRIBUTE_XLINK_ACTUATE = 313; // text
  const ATTRIBUTE_XLINK_HREF    = 314; // text
}

/**
 * A generic class for html sanitization filters.
 */
class c_base_markup_filters {
  const FILTER_NONE                 = 0;
  const FILTER_ALPHA_NUMERIC        = 1;
  const FILTER_DATE                 = 2;
  const FILTER_EMAIL                = 3;
  const FILTER_MONTH                = 4;
  const FILTER_NUMERIC              = 5;
  const FILTER_TEXT                 = 6;
  const FILTER_TEXT_PLAIN           = 7;
  const FILTER_TEXT_HTML            = 8;
  const FILTER_TYPE_DATE_TIME_LOCAL = 9;
  const FILTER_WEEK                 = 10;
}

/**
 * A generic class for html tags.
 *
 * The structure and attributes may be used to communicate information, therefore the attributes extend to both input and output (theme).
 * This class is not intended to be used for generate the theme but is instead intended to be used as a base class for both the input and the output classes for their respective purposes.
 *
 * Each tag has an internal id that is expected to be processed.
 * This is not the same as the HTML 'id' attribute in that is is only allowed to be a positive integer.
 *
 * @warning: I haven't completely figured out how I am going to manage the types.
 * For now all tag types have been created, but this is essentially mostly informative as most of this is to be handled by the theme classes.
 * Thus, this is highly subject to change upon review.
 *
 * @todo: add support for non-standard tag attributes, which will just be a string or NULL.
 */
class c_base_markup_tag extends c_base_return {
  use t_base_rfc_string;

  const TYPE_NONE                       = 0;
  const TYPE_A                          = 1;
  const TYPE_ABBR                       = 2;
  const TYPE_ADDRESS                    = 3;
  const TYPE_ALTERNATE_GLYPH            = 4;
  const TYPE_ALTERNATE_GLYPH_DEFINITION = 5;
  const TYPE_ALTERNATE_GLYPH_ITEM       = 6;
  const TYPE_ANIMATE                    = 7;
  const TYPE_ANIMATE_MOTION             = 8;
  const TYPE_ANIMATE_TRANSFORM          = 9;
  const TYPE_AREA                       = 10;
  const TYPE_ARTICLE                    = 11;
  const TYPE_ASIDE                      = 12;
  const TYPE_AUDIO                      = 13;
  const TYPE_BOLD                       = 14;
  const TYPE_BASE                       = 15;
  const TYPE_BDI                        = 16;
  const TYPE_BDO                        = 17;
  const TYPE_BLOCKQUOTE                 = 18;
  const TYPE_BREAK                      = 19;
  const TYPE_BUTTON                     = 20;
  const TYPE_CANVAS                     = 21;
  const TYPE_CAPTION                    = 22;
  const TYPE_CHECKBOX                   = 23;
  const TYPE_CIRCLE                     = 24;
  const TYPE_CITE                       = 25;
  const TYPE_CLIP_PATH                  = 26;
  const TYPE_CODE                       = 27;
  const TYPE_COLUMN                     = 28;
  const TYPE_COLUMN_GROUP               = 29;
  const TYPE_COLOR                      = 30;
  const TYPE_COLOR_PROFILE              = 31;
  const TYPE_CURSOR                     = 32;
  const TYPE_DATA                       = 33;
  const TYPE_DATA_LIST                  = 34;
  const TYPE_DATE                       = 35;
  const TYPE_DATE_TIME_LOCAL            = 36;
  const TYPE_DEFS                       = 37;
  const TYPE_DEL                        = 38;
  const TYPE_DESCRIPTION                = 39;
  const TYPE_DETAILS                    = 40;
  const TYPE_DFN                        = 41;
  const TYPE_DIALOG                     = 42;
  const TYPE_DIVIDER                    = 43;
  const TYPE_DEFINITION_LIST            = 44;
  const TYPE_ELLIPSE                    = 45;
  const TYPE_EM                         = 46;
  const TYPE_EMAIL                      = 47;
  const TYPE_EMBED                      = 48;
  const TYPE_FE_BLEND                   = 49;
  const TYPE_FIELD_SET                  = 50;
  const TYPE_FIGURE                     = 51;
  const TYPE_FIGURE_CAPTION             = 52;
  const TYPE_FILE                       = 53;
  const TYPE_FOOTER                     = 54;
  const TYPE_FORM                       = 55;
  const TYPE_GROUP                      = 56;
  const TYPE_H1                         = 57;
  const TYPE_H2                         = 58;
  const TYPE_H3                         = 59;
  const TYPE_H4                         = 60;
  const TYPE_H5                         = 61;
  const TYPE_H6                         = 62;
  const TYPE_HX                         = 63;
  const TYPE_HEADER                     = 64;
  const TYPE_HIDDEN                     = 65;
  const TYPE_HORIZONTAL_RULER           = 66;
  const TYPE_ITALICS                    = 67;
  const TYPE_INLINE_FRAME               = 68;
  const TYPE_IMAGE                      = 69;
  const TYPE_IMAGE_SVG                  = 70;
  const TYPE_INPUT                      = 71;
  const TYPE_INS                        = 72;
  const TYPE_KEYBOARD                   = 73;
  const TYPE_KEY_GEN                    = 74;
  const TYPE_LABEL                      = 75;
  const TYPE_LEGEND                     = 76;
  const TYPE_LIST_ITEM                  = 77;
  const TYPE_LINE                       = 78;
  const TYPE_LINEAR_GRADIENT            = 79;
  const TYPE_LINK                       = 80;
  const TYPE_MAIN                       = 81;
  const TYPE_MAP                        = 82;
  const TYPE_MARK                       = 83;
  const TYPE_MARKER                     = 84;
  const TYPE_MASK                       = 85;
  const TYPE_MENU                       = 86;
  const TYPE_MENU_ITEM                  = 87;
  const TYPE_MATH                       = 88;
  const TYPE_META                       = 89;
  const TYPE_METER                      = 90;
  const TYPE_MONTH                      = 91;
  const TYPE_NAVIGATION                 = 92;
  const TYPE_NO_SCRIPT                  = 93;
  const TYPE_NUMBER                     = 94;
  const TYPE_OBJECT                     = 95;
  const TYPE_ORDERED_LIST               = 96;
  const TYPE_OPTIONS_GROUP              = 97;
  const TYPE_OPTION                     = 98;
  const TYPE_OUTPUT                     = 99;
  const TYPE_PARAGRAPH                  = 100;
  const TYPE_PARAM                      = 101;
  const TYPE_PASSWORD                   = 102;
  const TYPE_PATH                       = 103;
  const TYPE_PATTERN                    = 104;
  const TYPE_PICTURE                    = 105;
  const TYPE_POLYGON                    = 106;
  const TYPE_POLYLINE                   = 107;
  const TYPE_PREFORMATTED               = 108;
  const TYPE_PROGRESS                   = 109;
  const TYPE_QUOTE                      = 110;
  const TYPE_RADIAL_GRADIENT            = 111;
  const TYPE_RADIO                      = 112;
  const TYPE_RANGE                      = 113;
  const TYPE_RECTANGLE                  = 114;
  const TYPE_RESET                      = 115;
  const TYPE_RUBY                       = 116;
  const TYPE_RUBY_PARENTHESIS           = 117;
  const TYPE_RUBY_PRONUNCIATION         = 118;
  const TYPE_STRIKE_THROUGH             = 119;
  const TYPE_SAMPLE                     = 120;
  const TYPE_SCRIPT                     = 121;
  const TYPE_SEARCH                     = 122;
  const TYPE_SECTION                    = 123;
  const TYPE_SELECT                     = 124;
  const TYPE_SMALL                      = 125;
  const TYPE_SOURCE                     = 126;
  const TYPE_SPAN                       = 127;
  const TYPE_STOP                       = 128;
  const TYPE_STRONG                     = 129;
  const TYPE_STYLE                      = 130;
  const TYPE_SUB_SCRIPT                 = 131;
  const TYPE_SUBMIT                     = 132;
  const TYPE_SUPER_SCRIPT               = 133;
  const TYPE_SVG                        = 134;
  const TYPE_TABLE                      = 135;
  const TYPE_TABLE_BODY                 = 136;
  const TYPE_TABLE_CELL                 = 137;
  const TYPE_TABLE_FOOTER               = 138;
  const TYPE_TABLE_HEADER               = 139;
  const TYPE_TABLE_HEADER_CELL          = 140;
  const TYPE_TABLE_ROW                  = 141;
  const TYPE_TELEPHONE                  = 142;
  const TYPE_TEMPLATE                   = 143;
  const TYPE_TERM_DESCRIPTION           = 144;
  const TYPE_TERM_NAME                  = 145;
  const TYPE_TEXT                       = 146;
  const TYPE_TEXT_AREA                  = 147;
  const TYPE_TEXT_REFERENCE             = 148;
  const TYPE_TEXT_SPAN                  = 149;
  const TYPE_TEXT_SVG                   = 150;
  const TYPE_TIME                       = 151;
  const TYPE_TITLE                      = 152;
  const TYPE_TRACK                      = 153;
  const TYPE_UNDERLINE                  = 154;
  const TYPE_UNORDERED_LIST             = 155;
  const TYPE_URL                        = 156;
  const TYPE_USE                        = 157;
  const TYPE_VARIABLE                   = 158;
  const TYPE_VIDEO                      = 159;
  const TYPE_WEEK                       = 160;
  const TYPE_WIDE_BREAK                 = 161;

  protected $attributes;
  protected $tags;
  protected $tags_total;
  protected $text;
  protected $type;
  protected $encode_text;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->attributes = [];
    $this->tags = [];
    $this->tags_total = 0;
    $this->text = NULL;
    $this->type = static::TYPE_TEXT;
    $this->encode_text = TRUE;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->attributes);
    unset($this->tags);
    unset($this->tags_total);
    unset($this->text);
    unset($this->type);
    unset($this->encode_text);

    parent::__destruct();
  }

  /**
   * Assign the specified tag.
   *
   * @param int $attribute
   *   The attribute to assign.
   * @param $value
   *   The value of the attribute.
   *   The actual value type is specific to each attribute type.
   *   Set to NULL to unassign/remove any given attribute.
   *   Set to c_base_markup_attributes::ATTRIBUTE_NONE to remove all attribute values.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_attribute($attribute, $value) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (is_null($value)) {
      unset($this->attribute[$attribute]);
      return new c_base_return_true();
    }

    switch ($attribute) {
      case c_base_markup_attributes::ATTRIBUTE_NONE:
        // when attribute none is specified, the entire attributes array is to be reset.
        unset($this->attributes);
        $this->attributes = [];
        return new c_base_return_true();

      case c_base_markup_attributes::ATTRIBUTE_ABBR:
      case c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY:
      case c_base_markup_attributes::ATTRIBUTE_ACTION:
      case c_base_markup_attributes::ATTRIBUTE_ALTERNATE:
      case c_base_markup_attributes::ATTRIBUTE_BY:
      case c_base_markup_attributes::ATTRIBUTE_CALCULATE_MODE:
      case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH:
      case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_CITE:
      case c_base_markup_attributes::ATTRIBUTE_COLOR:
      case c_base_markup_attributes::ATTRIBUTE_COORDINATES:
      case c_base_markup_attributes::ATTRIBUTE_CONTENT:
      case c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE:
      case c_base_markup_attributes::ATTRIBUTE_CROSS_ORIGIN:
      case c_base_markup_attributes::ATTRIBUTE_D:
      case c_base_markup_attributes::ATTRIBUTE_DATA:
      case c_base_markup_attributes::ATTRIBUTE_DATE_TIME:
      case c_base_markup_attributes::ATTRIBUTE_DIRECTION_NAME:
      case c_base_markup_attributes::ATTRIBUTE_DOWNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_DURATION:
      case c_base_markup_attributes::ATTRIBUTE_FILL:
      case c_base_markup_attributes::ATTRIBUTE_FILL_RULE:
      case c_base_markup_attributes::ATTRIBUTE_FILL_STROKE:
      case c_base_markup_attributes::ATTRIBUTE_FONT_SPECIFICATION:
      case c_base_markup_attributes::ATTRIBUTE_FOR:
      case c_base_markup_attributes::ATTRIBUTE_FORM:
      case c_base_markup_attributes::ATTRIBUTE_FORM_ACTION:
      case c_base_markup_attributes::ATTRIBUTE_FORM_TARGET:
      case c_base_markup_attributes::ATTRIBUTE_FORMAT:
      case c_base_markup_attributes::ATTRIBUTE_FROM:
      case c_base_markup_attributes::ATTRIBUTE_GLYPH_REFERENCE:
      case c_base_markup_attributes::ATTRIBUTE_GRADIANT_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_GRADIANT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_GRAPHICS:
      case c_base_markup_attributes::ATTRIBUTE_HEADERS:
      case c_base_markup_attributes::ATTRIBUTE_HEIGHT:
      case c_base_markup_attributes::ATTRIBUTE_HREF:
      case c_base_markup_attributes::ATTRIBUTE_HREF_NO:
      case c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV:
      case c_base_markup_attributes::ATTRIBUTE_ICON:
      case c_base_markup_attributes::ATTRIBUTE_ID:
      case c_base_markup_attributes::ATTRIBUTE_IN:
      case c_base_markup_attributes::ATTRIBUTE_IN_2:
      case c_base_markup_attributes::ATTRIBUTE_IS_MAP:
      case c_base_markup_attributes::ATTRIBUTE_KEY_POINTS:
      case c_base_markup_attributes::ATTRIBUTE_KEY_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_KIND:
      case c_base_markup_attributes::ATTRIBUTE_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_LENGTH_ADJUST:
      case c_base_markup_attributes::ATTRIBUTE_LIST:
      case c_base_markup_attributes::ATTRIBUTE_LOCAL:
      case c_base_markup_attributes::ATTRIBUTE_LONG_DESCRIPTION:
      case c_base_markup_attributes::ATTRIBUTE_MARKERS:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MASK_CONTENT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MASK_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM:
      case c_base_markup_attributes::ATTRIBUTE_MEDIA:
      case c_base_markup_attributes::ATTRIBUTE_METHOD:
      case c_base_markup_attributes::ATTRIBUTE_MODE:
      case c_base_markup_attributes::ATTRIBUTE_MINIMUM:
      case c_base_markup_attributes::ATTRIBUTE_NAME:
      case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
      case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
      case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
      case c_base_markup_attributes::ATTRIBUTE_ON_BLUR:
      case c_base_markup_attributes::ATTRIBUTE_ON_CANCEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_CLICK:
      case c_base_markup_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
      case c_base_markup_attributes::ATTRIBUTE_ON_COPY:
      case c_base_markup_attributes::ATTRIBUTE_ON_CUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
      case c_base_markup_attributes::ATTRIBUTE_ON_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_CUE_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_ENTER:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_OVER:
      case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_DROP:
      case c_base_markup_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_EMPTIED:
      case c_base_markup_attributes::ATTRIBUTE_ON_ENDED:
      case c_base_markup_attributes::ATTRIBUTE_ON_ERROR:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_IN:
      case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_OUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_HASH_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_INPUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_INSTALLED:
      case c_base_markup_attributes::ATTRIBUTE_ON_INVALID:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_DOWN:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_PRESS:
      case c_base_markup_attributes::ATTRIBUTE_ON_KEY_UP:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_DATA:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
      case c_base_markup_attributes::ATTRIBUTE_ON_LOAD_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OVER:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OUT:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_UP:
      case c_base_markup_attributes::ATTRIBUTE_ON_MESSAGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_OPEN:
      case c_base_markup_attributes::ATTRIBUTE_ON_ONLINE:
      case c_base_markup_attributes::ATTRIBUTE_ON_OFFLINE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_HIDE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PASTE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PAUSE:
      case c_base_markup_attributes::ATTRIBUTE_ON_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_ON_PLAYING:
      case c_base_markup_attributes::ATTRIBUTE_ON_PROGRESS:
      case c_base_markup_attributes::ATTRIBUTE_ON_POP_STATE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RATED_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RESIZE:
      case c_base_markup_attributes::ATTRIBUTE_ON_RESET:
      case c_base_markup_attributes::ATTRIBUTE_ON_RATE_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_SCROLL:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEARCH:
      case c_base_markup_attributes::ATTRIBUTE_ON_SELECT:
      case c_base_markup_attributes::ATTRIBUTE_ON_SUBMIT:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEEKED:
      case c_base_markup_attributes::ATTRIBUTE_ON_SEEKING:
      case c_base_markup_attributes::ATTRIBUTE_ON_STALLED:
      case c_base_markup_attributes::ATTRIBUTE_ON_SUSPEND:
      case c_base_markup_attributes::ATTRIBUTE_ON_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_ON_STORAGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TIME_UPDATE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TRANSITION_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOGGLE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_END:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
      case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_START:
      case c_base_markup_attributes::ATTRIBUTE_ON_UNLOAD:
      case c_base_markup_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
      case c_base_markup_attributes::ATTRIBUTE_ON_WAITING:
      case c_base_markup_attributes::ATTRIBUTE_ON_WHEEL:
      case c_base_markup_attributes::ATTRIBUTE_OFFSET:
      case c_base_markup_attributes::ATTRIBUTE_OPEN:
      case c_base_markup_attributes::ATTRIBUTE_ORIENTATION:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_CONTENT_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_PATTERN_UNITS:
      case c_base_markup_attributes::ATTRIBUTE_PATH:
      case c_base_markup_attributes::ATTRIBUTE_PATH_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_PLACE_HOLDER:
      case c_base_markup_attributes::ATTRIBUTE_POINTS:
      case c_base_markup_attributes::ATTRIBUTE_POSTER:
      case c_base_markup_attributes::ATTRIBUTE_PRELOAD:
      case c_base_markup_attributes::ATTRIBUTE_PRESERVE_ASPECT_RATIO:
      case c_base_markup_attributes::ATTRIBUTE_RADIO_GROUP:
      case c_base_markup_attributes::ATTRIBUTE_SANDBOX:
      case c_base_markup_attributes::ATTRIBUTE_SCOPE:
      case c_base_markup_attributes::ATTRIBUTE_SHAPE:
      case c_base_markup_attributes::ATTRIBUTE_REL:
      case c_base_markup_attributes::ATTRIBUTE_RENDERING_INTENT:
      case c_base_markup_attributes::ATTRIBUTE_REPEAT_COUNT:
      case c_base_markup_attributes::ATTRIBUTE_ROLE:
      case c_base_markup_attributes::ATTRIBUTE_ROTATE:
      case c_base_markup_attributes::ATTRIBUTE_SIZE:
      case c_base_markup_attributes::ATTRIBUTE_SIZES:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_DOCUMENT:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_SET:
      case c_base_markup_attributes::ATTRIBUTE_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_SPREAD_METHOD:
      case c_base_markup_attributes::ATTRIBUTE_STOP_COLOR:
      case c_base_markup_attributes::ATTRIBUTE_STOP_OPACITY:
      case c_base_markup_attributes::ATTRIBUTE_STYLE:
      case c_base_markup_attributes::ATTRIBUTE_TARGET:
      case c_base_markup_attributes::ATTRIBUTE_TEXT_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_TEXT_CONTENT_ELEMENTS:
      case c_base_markup_attributes::ATTRIBUTE_TITLE:
      case c_base_markup_attributes::ATTRIBUTE_TRANSFORM:
      case c_base_markup_attributes::ATTRIBUTE_TRANSLATE:
      case c_base_markup_attributes::ATTRIBUTE_TO:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_BUTTON:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_LIST:
      case c_base_markup_attributes::ATTRIBUTE_TYPE_SVG:
      case c_base_markup_attributes::ATTRIBUTE_USE_MAP:
      case c_base_markup_attributes::ATTRIBUTE_VALUE:
      case c_base_markup_attributes::ATTRIBUTE_VIEW_BOX:
      case c_base_markup_attributes::ATTRIBUTE_WIDTH:
      case c_base_markup_attributes::ATTRIBUTE_WRAP:
      case c_base_markup_attributes::ATTRIBUTE_XML:
      case c_base_markup_attributes::ATTRIBUTE_XMLNS:
      case c_base_markup_attributes::ATTRIBUTE_XMLNS_XLINK:
      case c_base_markup_attributes::ATTRIBUTE_XML_SPACE:
      case c_base_markup_attributes::ATTRIBUTE_ZOOM_AND_PAN:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ARIA_ATOMIC:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_AUTOCOMPLETE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_ACTIVE_DESCENDANT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_BUSY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_CHECKED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_CONTROLS:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DESCRIBED_BY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DISABLED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_DROP_EFFECT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_EXPANDED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_FLOW_TO:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_GRABBED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_HAS_POPUP:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_HIDDEN:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_INVALID:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LABEL:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LABELLED_BY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LEVEL:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_LIVE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_LINE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_SELECTABLE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_ORIENTATION:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_OWNS:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_POSITION_INSET:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_PRESSED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_READONLY:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_RELEVANT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_REQUIRED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SELECTED:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SET_SIZE:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_SORT:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MAXIMUM:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MINIMIM:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_NOW:
      case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_TEXT:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_XLINK_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_XLINK_ACTUATE:
      case c_base_markup_attributes::ATTRIBUTE_XLINK_HREF:
        if (!is_string($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ASYNCHRONOUS:
      case c_base_markup_attributes::ATTRIBUTE_ATTRIBUTE_NAME:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_COMPLETE:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS:
      case c_base_markup_attributes::ATTRIBUTE_AUTO_PLAY:
      case c_base_markup_attributes::ATTRIBUTE_CHALLENGE:
      case c_base_markup_attributes::ATTRIBUTE_CONTROLS:
      case c_base_markup_attributes::ATTRIBUTE_CHECKED:
      case c_base_markup_attributes::ATTRIBUTE_DEFAULT:
      case c_base_markup_attributes::ATTRIBUTE_DEFER:
      case c_base_markup_attributes::ATTRIBUTE_DISABLED:
      case c_base_markup_attributes::ATTRIBUTE_FORM_NO_VALIDATE:
      case c_base_markup_attributes::ATTRIBUTE_HIDDEN:
      case c_base_markup_attributes::ATTRIBUTE_LOOP:
      case c_base_markup_attributes::ATTRIBUTE_MULTIPLE:
      case c_base_markup_attributes::ATTRIBUTE_MUTED:
      case c_base_markup_attributes::ATTRIBUTE_NO_VALIDATE:
      case c_base_markup_attributes::ATTRIBUTE_READONLY:
      case c_base_markup_attributes::ATTRIBUTE_REQUIRED:
      case c_base_markup_attributes::ATTRIBUTE_REVERSED:
      case c_base_markup_attributes::ATTRIBUTE_SCOPED:
      case c_base_markup_attributes::ATTRIBUTE_SELECTED:
      case c_base_markup_attributes::ATTRIBUTE_SORTABLE:
      case c_base_markup_attributes::ATTRIBUTE_SORTED:
      case c_base_markup_attributes::ATTRIBUTE_SPELLCHECK:
        if (!is_bool($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ACCEPT:
      case c_base_markup_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_ENCODING_TYPE:
      case c_base_markup_attributes::ATTRIBUTE_TYPE:
        if (!$this->pr_validate_value_mime_type($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET:
      case c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET:
        if (!$this->pr_validate_value_character_set($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_DIRECTION:
        if (!is_null($value) && !is_int($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_CENTER_X:
      case c_base_markup_attributes::ATTRIBUTE_CENTER_Y:
      case c_base_markup_attributes::ATTRIBUTE_COLUMNS:
      case c_base_markup_attributes::ATTRIBUTE_COLUMN_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_D_X:
      case c_base_markup_attributes::ATTRIBUTE_D_Y:
      case c_base_markup_attributes::ATTRIBUTE_FOCUS_X:
      case c_base_markup_attributes::ATTRIBUTE_FOCUS_Y:
      case c_base_markup_attributes::ATTRIBUTE_HIGH:
      case c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_LOW:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_HEIGHT:
      case c_base_markup_attributes::ATTRIBUTE_MARKER_WIDTH:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
      case c_base_markup_attributes::ATTRIBUTE_MINIMUM_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_OPTIMUM:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS_X:
      case c_base_markup_attributes::ATTRIBUTE_RADIUS_Y:
      case c_base_markup_attributes::ATTRIBUTE_REFERENCE_X:
      case c_base_markup_attributes::ATTRIBUTE_REFERENCE_Y:
      case c_base_markup_attributes::ATTRIBUTE_ROWS:
      case c_base_markup_attributes::ATTRIBUTE_ROW_SPAN:
      case c_base_markup_attributes::ATTRIBUTE_SOURCE_LANGUAGE:
      case c_base_markup_attributes::ATTRIBUTE_START:
      case c_base_markup_attributes::ATTRIBUTE_STEP:
      case c_base_markup_attributes::ATTRIBUTE_TAB_INDEX:
      case c_base_markup_attributes::ATTRIBUTE_VALUE_NUMBER:
      case c_base_markup_attributes::ATTRIBUTE_X:
      case c_base_markup_attributes::ATTRIBUTE_X_1:
      case c_base_markup_attributes::ATTRIBUTE_X_2:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_ACTUATE:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_HREF:
      case c_base_markup_attributes::ATTRIBUTE_X_LINK_SHOW:
      case c_base_markup_attributes::ATTRIBUTE_Y:
      case c_base_markup_attributes::ATTRIBUTE_Y_1:
      case c_base_markup_attributes::ATTRIBUTE_Y_2:
        if (!is_int($value)) {
          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_CLASS:
        if (!is_array($value)) {
          if (is_string($value)) {
            if (!isset($this->attributes[$attribute])) {
              $this->attributes[$attribute] = [];
            }

            $this->attributes[$attribute][] = $value;
            return new c_base_return_true();
          }

          return new c_base_return_false();
        }
        break;

      case c_base_markup_attributes::ATTRIBUTE_FORM_METHOD:
        if (!$this->pr_validate_value_http_method($value)) {
          return new c_base_return_false();
        }
        break;

      default:
        return new c_base_return_false();
    }

    $this->attributes[$attribute] = $value;
    return new c_base_return_true();
  }

  /**
   * Get the attributes assigned to this object.
   *
   * @return c_base_return_array
   *   The attributes assigned to this class.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attributes() {
    if (!isset($this->attributes) && !is_array($this->attributes)) {
      $this->attributes = [];
    }

    return c_base_return_array::s_new($this->attributes);
  }

  /**
   * Get the value of a single attribute assigned to this object.
   *
   * @param int $attribute
   *   The attribute to get.
   *
   * @return c_base_return_int|c_base_return_string|c_base_return_bool|c_base_return_status
   *   The value assigned to the attribute (the data type is different per attribute).
   *   FALSE is returned if the element does not exist.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attribute($attribute) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!isset($this->attributes) && !is_array($this->attributes)) {
      $this->attributes = [];
    }

    if (array_key_exists($attribute, $this->attributes)) {
      switch ($attribute) {
        case c_base_markup_attributes::ATTRIBUTE_NONE:
          // should not be possible, so consider this an error (when attribute is set to NONE, the entire attributes array is unset).
          $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'name', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
          return c_base_return_error::s_false($error);

        case c_base_markup_attributes::ATTRIBUTE_ABBR:
        case c_base_markup_attributes::ATTRIBUTE_ACCESS_KEY:
        case c_base_markup_attributes::ATTRIBUTE_ACTION:
        case c_base_markup_attributes::ATTRIBUTE_ALTERNATE:
        case c_base_markup_attributes::ATTRIBUTE_BY:
        case c_base_markup_attributes::ATTRIBUTE_CALCULATE_MODE:
        case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH:
        case c_base_markup_attributes::ATTRIBUTE_CLIP_PATH_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_CITE:
        case c_base_markup_attributes::ATTRIBUTE_COLOR:
        case c_base_markup_attributes::ATTRIBUTE_COORDINATES:
        case c_base_markup_attributes::ATTRIBUTE_CONTENT:
        case c_base_markup_attributes::ATTRIBUTE_CONTENT_EDITABLE:
        case c_base_markup_attributes::ATTRIBUTE_CROSS_ORIGIN:
        case c_base_markup_attributes::ATTRIBUTE_D:
        case c_base_markup_attributes::ATTRIBUTE_DATA:
        case c_base_markup_attributes::ATTRIBUTE_DATE_TIME:
        case c_base_markup_attributes::ATTRIBUTE_DIRECTION_NAME:
        case c_base_markup_attributes::ATTRIBUTE_DOWNLOAD:
        case c_base_markup_attributes::ATTRIBUTE_DURATION:
        case c_base_markup_attributes::ATTRIBUTE_FILL:
        case c_base_markup_attributes::ATTRIBUTE_FILL_RULE:
        case c_base_markup_attributes::ATTRIBUTE_FILL_STROKE:
        case c_base_markup_attributes::ATTRIBUTE_FONT_SPECIFICATION:
        case c_base_markup_attributes::ATTRIBUTE_FOR:
        case c_base_markup_attributes::ATTRIBUTE_FORM:
        case c_base_markup_attributes::ATTRIBUTE_FORM_ACTION:
        case c_base_markup_attributes::ATTRIBUTE_FORM_TARGET:
        case c_base_markup_attributes::ATTRIBUTE_FORMAT:
        case c_base_markup_attributes::ATTRIBUTE_FROM:
        case c_base_markup_attributes::ATTRIBUTE_GLYPH_REFERENCE:
        case c_base_markup_attributes::ATTRIBUTE_GRADIANT_TRANSFORM:
        case c_base_markup_attributes::ATTRIBUTE_GRADIANT_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_GRAPHICS:
        case c_base_markup_attributes::ATTRIBUTE_HEADERS:
        case c_base_markup_attributes::ATTRIBUTE_HEIGHT:
        case c_base_markup_attributes::ATTRIBUTE_HREF:
        case c_base_markup_attributes::ATTRIBUTE_HREF_NO:
        case c_base_markup_attributes::ATTRIBUTE_HTTP_EQUIV:
        case c_base_markup_attributes::ATTRIBUTE_ICON:
        case c_base_markup_attributes::ATTRIBUTE_ID:
        case c_base_markup_attributes::ATTRIBUTE_IN:
        case c_base_markup_attributes::ATTRIBUTE_IN_2:
        case c_base_markup_attributes::ATTRIBUTE_IS_MAP:
        case c_base_markup_attributes::ATTRIBUTE_KEY_POINTS:
        case c_base_markup_attributes::ATTRIBUTE_KEY_TYPE:
        case c_base_markup_attributes::ATTRIBUTE_KIND:
        case c_base_markup_attributes::ATTRIBUTE_LABEL:
        case c_base_markup_attributes::ATTRIBUTE_LENGTH_ADJUST:
        case c_base_markup_attributes::ATTRIBUTE_LIST:
        case c_base_markup_attributes::ATTRIBUTE_LOCAL:
        case c_base_markup_attributes::ATTRIBUTE_LONG_DESCRIPTION:
        case c_base_markup_attributes::ATTRIBUTE_MARKERS:
        case c_base_markup_attributes::ATTRIBUTE_MARKER_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_MASK_CONTENT_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_MASK_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_MAXIMUM:
        case c_base_markup_attributes::ATTRIBUTE_MEDIA:
        case c_base_markup_attributes::ATTRIBUTE_METHOD:
        case c_base_markup_attributes::ATTRIBUTE_MODE:
        case c_base_markup_attributes::ATTRIBUTE_MINIMUM:
        case c_base_markup_attributes::ATTRIBUTE_NAME:
        case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
        case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
        case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
        case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
        case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
        case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
        case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
        case c_base_markup_attributes::ATTRIBUTE_ON_ABORT:
        case c_base_markup_attributes::ATTRIBUTE_ON_AFTER_PRINT:
        case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_END:
        case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_ITERATION:
        case c_base_markup_attributes::ATTRIBUTE_ON_ANIMATION_start:
        case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_UNLOAD:
        case c_base_markup_attributes::ATTRIBUTE_ON_BEFORE_PRINT:
        case c_base_markup_attributes::ATTRIBUTE_ON_BLUR:
        case c_base_markup_attributes::ATTRIBUTE_ON_CANCEL:
        case c_base_markup_attributes::ATTRIBUTE_ON_CLICK:
        case c_base_markup_attributes::ATTRIBUTE_ON_CONTEXT_MENU:
        case c_base_markup_attributes::ATTRIBUTE_ON_COPY:
        case c_base_markup_attributes::ATTRIBUTE_ON_CUT:
        case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY:
        case c_base_markup_attributes::ATTRIBUTE_ON_CAN_PLAY_THROUGH:
        case c_base_markup_attributes::ATTRIBUTE_ON_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_CUE_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_DOUBLE_CLICK:
        case c_base_markup_attributes::ATTRIBUTE_ON_DRAG:
        case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_END:
        case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_ENTER:
        case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_LEAVE:
        case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_OVER:
        case c_base_markup_attributes::ATTRIBUTE_ON_DRAG_START:
        case c_base_markup_attributes::ATTRIBUTE_ON_DROP:
        case c_base_markup_attributes::ATTRIBUTE_ON_DURATION_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_EMPTIED:
        case c_base_markup_attributes::ATTRIBUTE_ON_ENDED:
        case c_base_markup_attributes::ATTRIBUTE_ON_ERROR:
        case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS:
        case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_IN:
        case c_base_markup_attributes::ATTRIBUTE_ON_FOCUS_OUT:
        case c_base_markup_attributes::ATTRIBUTE_ON_HASH_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_INPUT:
        case c_base_markup_attributes::ATTRIBUTE_ON_INSTALLED:
        case c_base_markup_attributes::ATTRIBUTE_ON_INVALID:
        case c_base_markup_attributes::ATTRIBUTE_ON_KEY_DOWN:
        case c_base_markup_attributes::ATTRIBUTE_ON_KEY_PRESS:
        case c_base_markup_attributes::ATTRIBUTE_ON_KEY_UP:
        case c_base_markup_attributes::ATTRIBUTE_ON_LOAD:
        case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_DATA:
        case c_base_markup_attributes::ATTRIBUTE_ON_LOADED_META_DATA:
        case c_base_markup_attributes::ATTRIBUTE_ON_LOAD_START:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_DOWN:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_ENTER:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_LEAVE:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_MOVE:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OVER:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_OUT:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_UP:
        case c_base_markup_attributes::ATTRIBUTE_ON_MESSAGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_MOUSE_WHEEL:
        case c_base_markup_attributes::ATTRIBUTE_ON_OPEN:
        case c_base_markup_attributes::ATTRIBUTE_ON_ONLINE:
        case c_base_markup_attributes::ATTRIBUTE_ON_OFFLINE:
        case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_SHOW:
        case c_base_markup_attributes::ATTRIBUTE_ON_PAGE_HIDE:
        case c_base_markup_attributes::ATTRIBUTE_ON_PASTE:
        case c_base_markup_attributes::ATTRIBUTE_ON_PAUSE:
        case c_base_markup_attributes::ATTRIBUTE_ON_PLAY:
        case c_base_markup_attributes::ATTRIBUTE_ON_PLAYING:
        case c_base_markup_attributes::ATTRIBUTE_ON_PROGRESS:
        case c_base_markup_attributes::ATTRIBUTE_ON_POP_STATE:
        case c_base_markup_attributes::ATTRIBUTE_ON_RATED_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_RESIZE:
        case c_base_markup_attributes::ATTRIBUTE_ON_RESET:
        case c_base_markup_attributes::ATTRIBUTE_ON_RATE_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_SCROLL:
        case c_base_markup_attributes::ATTRIBUTE_ON_SEARCH:
        case c_base_markup_attributes::ATTRIBUTE_ON_SELECT:
        case c_base_markup_attributes::ATTRIBUTE_ON_SUBMIT:
        case c_base_markup_attributes::ATTRIBUTE_ON_SEEKED:
        case c_base_markup_attributes::ATTRIBUTE_ON_SEEKING:
        case c_base_markup_attributes::ATTRIBUTE_ON_STALLED:
        case c_base_markup_attributes::ATTRIBUTE_ON_SUSPEND:
        case c_base_markup_attributes::ATTRIBUTE_ON_SHOW:
        case c_base_markup_attributes::ATTRIBUTE_ON_STORAGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_TIME_UPDATE:
        case c_base_markup_attributes::ATTRIBUTE_ON_TRANSITION_END:
        case c_base_markup_attributes::ATTRIBUTE_ON_TOGGLE:
        case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_CANCEL:
        case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_END:
        case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_MOVE:
        case c_base_markup_attributes::ATTRIBUTE_ON_TOUCH_START:
        case c_base_markup_attributes::ATTRIBUTE_ON_UNLOAD:
        case c_base_markup_attributes::ATTRIBUTE_ON_VOLUME_CHANGE:
        case c_base_markup_attributes::ATTRIBUTE_ON_WAITING:
        case c_base_markup_attributes::ATTRIBUTE_ON_WHEEL:
        case c_base_markup_attributes::ATTRIBUTE_OFFSET:
        case c_base_markup_attributes::ATTRIBUTE_OPEN:
        case c_base_markup_attributes::ATTRIBUTE_ORIENTATION:
        case c_base_markup_attributes::ATTRIBUTE_PATTERN:
        case c_base_markup_attributes::ATTRIBUTE_PATTERN_CONTENT_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_PATTERN_TRANSFORM:
        case c_base_markup_attributes::ATTRIBUTE_PATTERN_UNITS:
        case c_base_markup_attributes::ATTRIBUTE_PATH:
        case c_base_markup_attributes::ATTRIBUTE_PATH_LENGTH:
        case c_base_markup_attributes::ATTRIBUTE_PLACE_HOLDER:
        case c_base_markup_attributes::ATTRIBUTE_POINTS:
        case c_base_markup_attributes::ATTRIBUTE_POSTER:
        case c_base_markup_attributes::ATTRIBUTE_PRELOAD:
        case c_base_markup_attributes::ATTRIBUTE_PRESERVE_ASPECT_RATIO:
        case c_base_markup_attributes::ATTRIBUTE_RADIO_GROUP:
        case c_base_markup_attributes::ATTRIBUTE_SANDBOX:
        case c_base_markup_attributes::ATTRIBUTE_SCOPE:
        case c_base_markup_attributes::ATTRIBUTE_SHAPE:
        case c_base_markup_attributes::ATTRIBUTE_REL:
        case c_base_markup_attributes::ATTRIBUTE_RENDERING_INTENT:
        case c_base_markup_attributes::ATTRIBUTE_REPEAT_COUNT:
        case c_base_markup_attributes::ATTRIBUTE_ROLE:
        case c_base_markup_attributes::ATTRIBUTE_ROTATE:
        case c_base_markup_attributes::ATTRIBUTE_SIZE:
        case c_base_markup_attributes::ATTRIBUTE_SIZES:
        case c_base_markup_attributes::ATTRIBUTE_SOURCE:
        case c_base_markup_attributes::ATTRIBUTE_SOURCE_DOCUMENT:
        case c_base_markup_attributes::ATTRIBUTE_SOURCE_SET:
        case c_base_markup_attributes::ATTRIBUTE_SPAN:
        case c_base_markup_attributes::ATTRIBUTE_SPREAD_METHOD:
        case c_base_markup_attributes::ATTRIBUTE_STOP_COLOR:
        case c_base_markup_attributes::ATTRIBUTE_STOP_OPACITY:
        case c_base_markup_attributes::ATTRIBUTE_STYLE:
        case c_base_markup_attributes::ATTRIBUTE_TARGET:
        case c_base_markup_attributes::ATTRIBUTE_TEXT_LENGTH:
        case c_base_markup_attributes::ATTRIBUTE_TEXT_CONTENT_ELEMENTS:
        case c_base_markup_attributes::ATTRIBUTE_TITLE:
        case c_base_markup_attributes::ATTRIBUTE_TRANSFORM:
        case c_base_markup_attributes::ATTRIBUTE_TRANSLATE:
        case c_base_markup_attributes::ATTRIBUTE_TO:
        case c_base_markup_attributes::ATTRIBUTE_TYPE_BUTTON:
        case c_base_markup_attributes::ATTRIBUTE_TYPE_LABEL:
        case c_base_markup_attributes::ATTRIBUTE_TYPE_LIST:
        case c_base_markup_attributes::ATTRIBUTE_TYPE_SVG:
        case c_base_markup_attributes::ATTRIBUTE_USE_MAP:
        case c_base_markup_attributes::ATTRIBUTE_VALUE:
        case c_base_markup_attributes::ATTRIBUTE_VIEW_BOX:
        case c_base_markup_attributes::ATTRIBUTE_WIDTH:
        case c_base_markup_attributes::ATTRIBUTE_WRAP:
        case c_base_markup_attributes::ATTRIBUTE_XML:
        case c_base_markup_attributes::ATTRIBUTE_XMLNS:
        case c_base_markup_attributes::ATTRIBUTE_XMLNS_XLINK:
        case c_base_markup_attributes::ATTRIBUTE_XML_SPACE:
        case c_base_markup_attributes::ATTRIBUTE_ZOOM_AND_PAN:
          return c_base_return_string::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_ARIA_ATOMIC:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_AUTOCOMPLETE:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_ACTIVE_DESCENDANT:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_BUSY:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_CHECKED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_CONTROLS:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_DESCRIBED_BY:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_DISABLED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_DROP_EFFECT:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_EXPANDED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_FLOW_TO:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_GRABBED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_HAS_POPUP:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_HIDDEN:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_INVALID:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_LABEL:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_LABELLED_BY:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_LEVEL:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_LIVE:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_LINE:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_MULTI_SELECTABLE:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_ORIENTATION:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_OWNS:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_POSITION_INSET:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_PRESSED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_READONLY:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_RELEVANT:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_REQUIRED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_SELECTED:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_SET_SIZE:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_SORT:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MAXIMUM:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_MINIMIM:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_NOW:
        case c_base_markup_attributes::ATTRIBUTE_ARIA_VALUE_TEXT:
          return c_base_return_string::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_XLINK_SHOW:
        case c_base_markup_attributes::ATTRIBUTE_XLINK_ACTUATE:
        case c_base_markup_attributes::ATTRIBUTE_XLINK_HREF:
          return c_base_return_string::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_ASYNCHRONOUS:
        case c_base_markup_attributes::ATTRIBUTE_ATTRIBUTE_NAME:
        case c_base_markup_attributes::ATTRIBUTE_AUTO_COMPLETE:
        case c_base_markup_attributes::ATTRIBUTE_AUTO_FOCUS:
        case c_base_markup_attributes::ATTRIBUTE_AUTO_PLAY:
        case c_base_markup_attributes::ATTRIBUTE_CHALLENGE:
        case c_base_markup_attributes::ATTRIBUTE_CONTROLS:
        case c_base_markup_attributes::ATTRIBUTE_CHECKED:
        case c_base_markup_attributes::ATTRIBUTE_DEFAULT:
        case c_base_markup_attributes::ATTRIBUTE_DEFER:
        case c_base_markup_attributes::ATTRIBUTE_DISABLED:
        case c_base_markup_attributes::ATTRIBUTE_FORM_NO_VALIDATE:
        case c_base_markup_attributes::ATTRIBUTE_HIDDEN:
        case c_base_markup_attributes::ATTRIBUTE_LOOP:
        case c_base_markup_attributes::ATTRIBUTE_MULTIPLE:
        case c_base_markup_attributes::ATTRIBUTE_MUTED:
        case c_base_markup_attributes::ATTRIBUTE_NO_VALIDATE:
        case c_base_markup_attributes::ATTRIBUTE_READONLY:
        case c_base_markup_attributes::ATTRIBUTE_REQUIRED:
        case c_base_markup_attributes::ATTRIBUTE_REVERSED:
        case c_base_markup_attributes::ATTRIBUTE_SCOPED:
        case c_base_markup_attributes::ATTRIBUTE_SELECTED:
        case c_base_markup_attributes::ATTRIBUTE_SORTABLE:
        case c_base_markup_attributes::ATTRIBUTE_SORTED:
        case c_base_markup_attributes::ATTRIBUTE_SPELLCHECK:
          return c_base_return_bool::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_ACCEPT:
        case c_base_markup_attributes::ATTRIBUTE_FORM_ENCODE_TYPE:
        case c_base_markup_attributes::ATTRIBUTE_ENCODING_TYPE:
        case c_base_markup_attributes::ATTRIBUTE_TYPE:
          return c_base_return_int::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_ACCEPT_CHARACTER_SET:
        case c_base_markup_attributes::ATTRIBUTE_CHARACTER_SET:
          return c_base_return_int::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_DIRECTION:
          if (is_int($this->attributes[$attribute])) {
            return c_base_return_int::s_new($this->attributes[$attribute]);
          }
          else if (is_null($this->attributes[$attribute])) {
            return new c_base_return_null();
          }
          break;

        case c_base_markup_attributes::ATTRIBUTE_CENTER_X:
        case c_base_markup_attributes::ATTRIBUTE_CENTER_Y:
        case c_base_markup_attributes::ATTRIBUTE_COLUMNS:
        case c_base_markup_attributes::ATTRIBUTE_COLUMN_SPAN:
        case c_base_markup_attributes::ATTRIBUTE_D_X:
        case c_base_markup_attributes::ATTRIBUTE_D_Y:
        case c_base_markup_attributes::ATTRIBUTE_FOCUS_X:
        case c_base_markup_attributes::ATTRIBUTE_FOCUS_Y:
        case c_base_markup_attributes::ATTRIBUTE_HIGH:
        case c_base_markup_attributes::ATTRIBUTE_HREF_LANGUAGE:
        case c_base_markup_attributes::ATTRIBUTE_LANGUAGE:
        case c_base_markup_attributes::ATTRIBUTE_LOW:
        case c_base_markup_attributes::ATTRIBUTE_MARKER_HEIGHT:
        case c_base_markup_attributes::ATTRIBUTE_MARKER_WIDTH:
        case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_NUMBER:
        case c_base_markup_attributes::ATTRIBUTE_MAXIMUM_LENGTH:
        case c_base_markup_attributes::ATTRIBUTE_MINIMUM_NUMBER:
        case c_base_markup_attributes::ATTRIBUTE_OPTIMUM:
        case c_base_markup_attributes::ATTRIBUTE_RADIUS:
        case c_base_markup_attributes::ATTRIBUTE_RADIUS_X:
        case c_base_markup_attributes::ATTRIBUTE_RADIUS_Y:
        case c_base_markup_attributes::ATTRIBUTE_REFERENCE_X:
        case c_base_markup_attributes::ATTRIBUTE_REFERENCE_Y:
        case c_base_markup_attributes::ATTRIBUTE_ROWS:
        case c_base_markup_attributes::ATTRIBUTE_ROW_SPAN:
        case c_base_markup_attributes::ATTRIBUTE_SOURCE_LANGUAGE:
        case c_base_markup_attributes::ATTRIBUTE_START:
        case c_base_markup_attributes::ATTRIBUTE_STEP:
        case c_base_markup_attributes::ATTRIBUTE_TAB_INDEX:
        case c_base_markup_attributes::ATTRIBUTE_VALUE_NUMBER:
        case c_base_markup_attributes::ATTRIBUTE_X:
        case c_base_markup_attributes::ATTRIBUTE_X_1:
        case c_base_markup_attributes::ATTRIBUTE_X_2:
        case c_base_markup_attributes::ATTRIBUTE_X_LINK_ACTUATE:
        case c_base_markup_attributes::ATTRIBUTE_X_LINK_HREF:
        case c_base_markup_attributes::ATTRIBUTE_X_LINK_SHOW:
        case c_base_markup_attributes::ATTRIBUTE_Y:
        case c_base_markup_attributes::ATTRIBUTE_Y_1:
        case c_base_markup_attributes::ATTRIBUTE_Y_2:
          return c_base_return_int::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_FORM_METHOD:
          return c_base_return_int::s_new($this->attributes[$attribute]);

        case c_base_markup_attributes::ATTRIBUTE_CLASS:
          return c_base_return_array::s_new($this->attributes[$attribute]);

        default:
          return new c_base_return_false();
      }
    }

    return new c_base_return_false();
  }

  /**
   * Checks that the assigned HTML attribute 'value' is valid according to the tag type.
   *
   * @param int $attribute
   *   The attribute to assign.
   * @param bool $sanitize
   *   (optional) When TRUE, the text is altered and replaced with new text.
   *   When FALSE, no changes are made.
   * @param int|null $type
   *   (optional) The filter type to sanitize as.
   *   When NULL, evaluate the attribute based on the tag type (if supported, only a few form tags are supported).
   * @param int|string|null $sub_type
   *   The sub-type to validate "value" as.
   *   This is not directly implemented by this class and is provided so that extending classes may have more fine-tuned control.
   *   This should accept either an int, a string, or NULL.
   * @param array $options
   *   (optional) any additional options that are specific to a given sanitization type.
   *
   * @return c_base_return_status
   *   NULL is returned if not defined.
   *   TRUE if changes were made, when $sanitize is TRUE.
   *   TRUE on valid string, when $sanitize is FALSE.
   *   TRUE with error bit set is returned on error, when $sanitize is TRUE.
   *   FALSE if no changes, when $sanitize is TRUE.
   *   FALSE on invalid string, when $sanitize is FALSE.
   *   FALSE with error bit set is returned on error and no changes were made.
   *
   * @see: self::pr_check_attribute_as_text()
   */
  public function check_attribute($attribute, $sanitize = FALSE, $type = NULL, $sub_type = NULL, $options = []) {
    if (!is_int($attribute)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'attribute', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_bool($sanitize)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'sanitize', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($sub_type) && !is_int($sub_type) && !is_string($sub_type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'sub_type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($options)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'options', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->attributes) || !array_key_exists($attribute, $this->attributes)) {
      return new c_base_return_null();
    }

    if (is_null($type)) {
      if ($this->type === TYPE_CHECKBOX) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_COLOR) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_DATE) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_DATE_TIME_LOCAL) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_EMAIL) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_HIDDEN) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_IMAGE) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_INPUT) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_MONTH) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_NUMBER) {
        return $this->pr_check_attribute_as_numeric($sanitize);
      }

      if ($this->type === TYPE_OPTION) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_PASSWORD) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_RADIO) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_SEARCH) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_TELEPHONE) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_TEXT) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_TEXT_AREA) {
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_TIME) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_URL) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      if ($this->type === TYPE_WEEK) {
        // @todo: sanitize specifically to possible types.
        return $this->pr_check_attribute_as_text($sanitize);
      }

      // for all other types, do nothing.
      return new c_base_return_false();
    }

    // when type is not null, process based on passed validation filter code.
    if ($type === c_base_markup_filters::FILTER_TEXT) {
      return $this->pr_check_attribute_as_text($sanitize);
    }
    else if ($type === c_base_markup_filters::FILTER_NUMERIC) {
      return $this->pr_check_attribute_as_numeric($sanitize);
    }

    // @todo: finish this.

    // for all other types, do nothing.
    return new c_base_return_false();
  }

  /**
   * Add or append a given tag to the object.
   *
   * @param c_base_markup_tag $tag
   *   The tag to assign.
   * @param int|null $index
   *   (optional) A position within the children array to assign the tag.
   *   If NULL, then the tag is appended to the end of the children array.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE is returned if an tag at the specified index already exists.
   *   FALSE with error bit set is returned on error.
   */
  public function set_tag($tag, $index = NULL) {
    if (!($tag instanceof c_base_markup_tag)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'tag', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_null($index) && (!is_int($index) && $index < 0)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->tags)) {
      $this->tags = [];
    }

    if (is_null($index)) {
      $this->tags[] = $tag;
    }
    else {
      $this->tags[$index] = $tag;
    }

    $this->tags_total++;
    return new c_base_return_true();
  }

  /**
   * Remove a tag at the given index.
   *
   * @param int|null $index
   *   A position within the children array to assign the tag.
   *   If NULL, then the tag at the end of the children array is removed.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE is returned if there is no tag at the specified index.
   *   FALSE with error bit set is returned on error.
   */
  public function unset_tag($index) {
    if (!is_null($index) && (!is_int($index) && $index < 0)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!is_array($this->tags)) {
      $this->tags = [];
    }

    if (is_null($index)) {
      if (!empty($this->tags)) {
        array_pop($this->tags);
      }
      else {
        return new c_base_return_false();
      }
    }
    else {
      if (array_key_exists($index, $this->tags)) {
        unset($this->tags[$index]);
      }
      else {
        return new c_base_return_false();
      }
    }

    $this->tags_total--;
    return new c_base_return_true();
  }

  /**
   * Get the tag at the given position.
   *
   * @param int $index
   *   The position of the child tag to get.
   *
   * @return c_base_markup_tag|c_base_return_status
   *   The tag at the given index.
   *   FALSE is returned without error bit set if there is no tag at the given index.
   *   FALSE with error bit set is returned on error.
   */
  public function get_tag($index) {
    if (!is_int($index) && $index < 0) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'index', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if (!array_key_exists($index, $this->tags)) {
      return new c_base_return_false();
    }

    return $this->tags[$index];
  }

  /**
   * Get all child tags associated with this object.
   *
   * @return c_base_return_array|c_base_return_status
   *   An array of child tags.
   *   FALSE with error bit set is returned on error.
   */
  public function get_tags() {
    if (!is_array($this->tags)) {
      $this->tags = [];
    }

    return c_base_return_array::s_new($this->tags);
  }

  /**
   * Assign basic text to this tag.
   *
   * This is considered the 'content' of the tag.
   *
   * @param string $text
   *   The text to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_text($text) {
    if (!is_string($text)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->text = $text;
    return new c_base_return_true();
  }

  /**
   * Retrieve basic text to this tag.
   *
   * This is considered the 'content' of the tag.
   *
   * @return c_base_return_string
   *   The text string.
   *   FALSE with error bit set is returned on error.
   */
  public function get_text() {
    if (!is_string($this->text)) {
      $this->text = '';
    }

    return c_base_return_string::s_new($this->text);
  }

  /**
   * Assign the specified tag type.
   *
   * @param int $type
   *   The tag type to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type($type) {
    if (!is_int($type)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'type', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    if ($type < 0) {
      return new c_base_return_false();
    }

    $this->type = $type;
    return new c_base_return_true();
  }

  /**
   * Get the tag type assigned to this object.
   *
   * @return c_base_return_int
   *   The tag type assigned to this class.
   *   FALSE with error bit set is returned on error.
   */
  public function get_type() {
    if (!isset($this->type)) {
      $this->type = static::TYPE_NONE;
    }

    return c_base_return_int::s_new($this->type);
  }

  /**
   * Assign the specified encode tag text option.
   *
   * When enabled, the text will be auto-encoding prior to output.
   * When disabled, the text will not be auto-encoded prior to output.
   *
   * This is generally disabled for text that is already known to be encoded.
   *
   * @param int $type
   *   The tag type to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_encode_text($encode_text) {
    if (!is_bool($encode_text)) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{argument_name}' => 'encode_text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_ARGUMENT);
      return c_base_return_error::s_false($error);
    }

    $this->encode_text = $encode_text;
  }

  /**
   * Get the encode text value assigned to this object.
   *
   * @return c_base_return_bool
   *   The tag type assigned to this class.
   *   FALSE with error bit set is returned on error.
   */
  public function get_encode_text() {
    if (!isset($this->encode_text)) {
      $this->encode_text = TRUE;
    }

    return c_base_return_bool::s_new($this->encode_text);
  }

  /**
   * Protected function for mime values.
   *
   * @param int $value
   *   The value of the attribute populate from c_base_mime.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE otherwise.
   */
  protected function pr_validate_value_mime_type($value) {
    if (!is_int($value)) {
      return FALSE;
    }

    switch ($value) {
      case c_base_mime::CATEGORY_UNKNOWN:
      case c_base_mime::CATEGORY_PROVIDED:
      case c_base_mime::CATEGORY_STREAM:
      case c_base_mime::CATEGORY_TEXT:
      case c_base_mime::CATEGORY_IMAGE:
      case c_base_mime::CATEGORY_AUDIO:
      case c_base_mime::CATEGORY_VIDEO:
      case c_base_mime::CATEGORY_DOCUMENT:
      case c_base_mime::CATEGORY_CONTAINER:
      case c_base_mime::CATEGORY_APPLICATION:
      case c_base_mime::TYPE_UNKNOWN:
      case c_base_mime::TYPE_PROVIDED:
      case c_base_mime::TYPE_STREAM:
      case c_base_mime::TYPE_TEXT_PLAIN:
      case c_base_mime::TYPE_TEXT_HTML:
      case c_base_mime::TYPE_TEXT_RSS:
      case c_base_mime::TYPE_TEXT_ICAL:
      case c_base_mime::TYPE_TEXT_CSV:
      case c_base_mime::TYPE_TEXT_XML:
      case c_base_mime::TYPE_TEXT_CSS:
      case c_base_mime::TYPE_TEXT_JS:
      case c_base_mime::TYPE_TEXT_JSON:
      case c_base_mime::TYPE_TEXT_RICH:
      case c_base_mime::TYPE_TEXT_XHTML:
      case c_base_mime::TYPE_TEXT_PS:
      case c_base_mime::TYPE_IMAGE_PNG:
      case c_base_mime::TYPE_IMAGE_GIF:
      case c_base_mime::TYPE_IMAGE_JPEG:
      case c_base_mime::TYPE_IMAGE_BMP:
      case c_base_mime::TYPE_IMAGE_SVG:
      case c_base_mime::TYPE_IMAGE_TIFF:
      case c_base_mime::TYPE_AUDIO_WAV:
      case c_base_mime::TYPE_AUDIO_OGG:
      case c_base_mime::TYPE_AUDIO_MP3:
      case c_base_mime::TYPE_AUDIO_MP4:
      case c_base_mime::TYPE_AUDIO_MIDI:
      case c_base_mime::TYPE_VIDEO_MPEG:
      case c_base_mime::TYPE_VIDEO_OGG:
      case c_base_mime::TYPE_VIDEO_H264:
      case c_base_mime::TYPE_VIDEO_QUICKTIME:
      case c_base_mime::TYPE_VIDEO_DV:
      case c_base_mime::TYPE_VIDEO_JPEG:
      case c_base_mime::TYPE_VIDEO_WEBM:
      case c_base_mime::TYPE_DOCUMENT_LIBRECHART:
      case c_base_mime::TYPE_DOCUMENT_LIBREFORMULA:
      case c_base_mime::TYPE_DOCUMENT_LIBREGRAPHIC:
      case c_base_mime::TYPE_DOCUMENT_LIBREPRESENTATION:
      case c_base_mime::TYPE_DOCUMENT_LIBRESPREADSHEET:
      case c_base_mime::TYPE_DOCUMENT_LIBRETEXT:
      case c_base_mime::TYPE_DOCUMENT_LIBREHTML:
      case c_base_mime::TYPE_DOCUMENT_PDF:
      case c_base_mime::TYPE_DOCUMENT_ABIWORD:
      case c_base_mime::TYPE_DOCUMENT_MSWORD:
      case c_base_mime::TYPE_DOCUMENT_MSEXCEL:
      case c_base_mime::TYPE_DOCUMENT_MSPOWERPOINT:
      case c_base_mime::TYPE_CONTAINER_TAR:
      case c_base_mime::TYPE_CONTAINER_CPIO:
      case c_base_mime::TYPE_CONTAINER_JAVA:
        break;
      default:
        return FALSE;
    }

    return TRUE;
  }

  /**
   * Protected function for character set values.
   *
   * @param int $value
   *   The value of the attribute populate from c_base_charset.
   *
   * @return bool
   *   TRUE on success.
   *   FALSE otherwise.
   */
  protected function pr_validate_value_character_set($value) {
    if (!is_int($value)) {
      return FALSE;
    }

    switch ($value) {
      case c_base_charset::UNDEFINED:
      case c_base_charset::ASCII:
      case c_base_charset::UTF_8:
      case c_base_charset::UTF_16:
      case c_base_charset::UTF_32:
      case c_base_charset::ISO_8859_1:
      case c_base_charset::ISO_8859_2:
      case c_base_charset::ISO_8859_3:
      case c_base_charset::ISO_8859_4:
      case c_base_charset::ISO_8859_5:
      case c_base_charset::ISO_8859_6:
      case c_base_charset::ISO_8859_7:
      case c_base_charset::ISO_8859_8:
      case c_base_charset::ISO_8859_9:
      case c_base_charset::ISO_8859_10:
      case c_base_charset::ISO_8859_11:
      case c_base_charset::ISO_8859_12:
      case c_base_charset::ISO_8859_13:
      case c_base_charset::ISO_8859_14:
      case c_base_charset::ISO_8859_15:
      case c_base_charset::ISO_8859_16:
        break;
      default:
        return FALSE;
    }

    return TRUE;
  }

  /**
   * Removes all invalid characters for text fields.
   *
   * @param bool $sanitize
   *   (optional) When TRUE, the text is altered and replaced with new text.
   *   When FALSE, no changes are made.
   *
   * @return c_base_return_status
   *   TRUE if changes were made, when $sanitize is TRUE.
   *   TRUE on valid string, when $sanitize is FALSE.
   *   TRUE with error bit set is returned on error, when $sanitize is TRUE.
   *   FALSE if no changes, when $sanitize is TRUE.
   *   FALSE on invalid string, when $sanitize is FALSE.
   *   FALSE with error bit set is returned on error and no changes were made.
   *
   * @see: self::check_attribute()
   */
  protected function pr_check_attribute_as_text($attribute, $sanitize = TRUE) {
    if (is_string($this->attributes[$attribute])) {
      $value = $this->attributes[$attribute];
    }
    else if (is_numeric($this->attributes[$attribute])) {
      $value = (string) $this->attributes[$attribute];
    }
    else {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{format_name}' => 'value attribute', ':{expected_format}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    $prepared = $this->pr_rfc_string_prepare($value);
    if ($prepared instanceof c_base_return_false) {
      unset($prepared);
      unset($value);

      $this->attributes[$attribute] = NULL;

      $error = c_base_error::s_log(NULL, ['arguments' => [':{format_name}' => 'value attribute', ':{expected_format}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_FORMAT);

      if ($sanitize) {
        return c_base_return_error::s_true($error);
      }

      return c_base_return_error::s_false($error);
    }
    unset($value);

    $text = $prepared->get_value_exact();
    unset($prepared);

    $invalid = FALSE;
    $changed = FALSE;
    $sanitized = '';
    $current = 0;
    $stop = count($ordinals);

    for (; $current < $stop; $current++) {
      if (!array_key_exists($current, $ordinals) || !array_key_exists($current, $characters)) {
        $invalid = TRUE;
        break;
      }

      $code = $ordinals[$current];

      if (!$this->pr_rfc_char_is_text($code)) {
        $invalid = TRUE;
        $changed = TRUE;
        continue;
      }

      $sanitized .= $characters[$current];
    }
    unset($code);
    unset($current);
    unset($stop);

    if ($sanitize && $changed) {
      $this->attributes[$attribute] = $sanitized;
    }
    unset($sanitized);

    if ($invalid) {
      unset($invalid);

      $error = c_base_error::s_log(NULL, ['arguments' => [':{format_name}' => 'value attribute', ':{expected_format}' => 'text', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_FORMAT);
      if ($changed) {
        unset($changed);
        return c_base_return_error::s_true($error);
      }
      unset($changed);

      return c_base_return_error::s_false($error);
    }
    unset($invalid);

    if ($changed) {
      unset($changed);
      return new c_base_return_true();
    }
    unset($changed);

    return new c_base_return_false();
  }

  /**
   * Removes all invalid characters for numeric fields.
   *
   * @param bool $sanitize
   *   (optional) When TRUE, the text is altered and replaced with new text.
   *   When FALSE, no changes are made.
   *
   * @return c_base_return_status
   *   TRUE if changes were made, when $sanitize is TRUE.
   *   TRUE on valid string, when $sanitize is FALSE.
   *   TRUE with error bit set is returned on error, when $sanitize is TRUE.
   *   FALSE if no changes, when $sanitize is TRUE.
   *   FALSE on invalid string, when $sanitize is FALSE.
   *   FALSE with error bit set is returned on error and no changes were made.
   *
   * @see: self::check_attribute()
   */
  protected function pr_check_attribute_as_numeric($attribute, $sanitize = TRUE) {
    if (!is_numeric($this->attributes[$attribute])) {
      $error = c_base_error::s_log(NULL, ['arguments' => [':{format_name}' => 'value attribute', ':{expected_format}' => 'number', ':{function_name}' => __CLASS__ . '->' . __FUNCTION__]], i_base_error_messages::INVALID_FORMAT);
      return c_base_return_error::s_false($error);
    }

    // @fixme: this is just a quick and dirty implementation, come back and re-design this more effectively.

    $value = floatval($this->attributes[$attribute]);
    if (floor($value) == $value) {
      $value = (int) $value;
    }

    if ($this->attribute[$attribute] == $value) {
      unset($value);

      if ($sanitize) {
        return new c_base_return_false();
      }

      return new c_base_return_true();
    }

    if ($sanitize) {
      $this->attribute[$attribute] = $value;
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }
}
