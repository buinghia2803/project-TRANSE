<?php

// System success code
const CODE_SUCCESS_200 = 200;
const CODE_SUCCESS_201 = 201;
const CODE_SUCCESS_203 = 203;
const CODE_SUCCESS_204 = 204;

// System error code
const CODE_ERROR_400 = 400;
const CODE_ERROR_401 = 401;
const CODE_ERROR_403 = 403;
const CODE_ERROR_404 = 404;
const CODE_ERROR_500 = 500;

// Action system
const LOGIN = 'login';
const LOGOUT = 'logout';
const PROFILE = 'profile';
const INDEX = 'index';
const SHOW = 'show';
const DETAIL = 'detail';
const CREATE = 'create';
const STORE = 'store';
const UPDATE = 'update';
const UPDATE_STATUS = 'update_status';
const REMOVE = 'remove';
const DELETE = 'delete';
const COMMENT = 'comment';
const REJECT = 'reject';
const CONFIRM = 'confirm';
const COUNT = 'count';
const DUPLICATE_EMAIL = 'duplicate email';
const DUPLICATE_NAME = 'duplicate name';
const BACK_URL = 'back-url';
const PAYMENT = 'payment';
const DRAFT = 'draft';
const SAVE = 'save';
const SUBMIT = 'submit';
const SUBMIT_SUPERVISOR = 'submit_supervisor';
const CANCEL = 'cancel';
const ADMIN_ROLE = 'admin';
const TOKEN_EXPIRED = 5256000; // 10 years
const ANKEN_TOP = 'anken-top';

// Filesystem
define("LOCAL_PUBLIC_FOLDER", env('LOCAL_PUBLIC_FOLDER', '/uploads'));

// Sort
const SORT_TYPE_ASC = 'asc';
const SORT_TYPE_DESC = 'desc';

const SORT_BY_ASC = 'asc';
const SORT_BY_DESC = 'desc';

// Search
const SEARCH = 1;
const SEARCH_TEXT = 'text';
const SEARCH_SELECT = 'select';
const SEARCH_DATERANGE = 'daterange';

// Status message
const MESSAGE_ERROR = 'danger';
const MESSAGE_WARNING = 'warning';
const MESSAGE_SUCCESS = 'success';

const HISTORY_TYPE = [
    'crud_dairinin_set' => 7,
];

const HISTORY_ACTION_ADD = 1;
const HISTORY_ACTION_MODIFY = 2;
const IS_CONFIRM = 2;
const IS_CONFIRM_TRUE = 1;
const IS_COMPLETED_TRUE = 1;


// Japan Nation ID
const NATION_JAPAN_ID = 1;

// Type Trademark
const TYPE_TRADEMARK_CHARACTERS = 1;
const TYPE_TRADEMARK_OTHERS = 2;

// status period register trademark
const PERIOD_REGISTRATION_FIVE_YEAR = 1;
const PERIOD_REGISTRATION_TEN_YEAR = 2;

const YEAR_5 = 5;
const YEAR_10 = 10;

// label trademark register time
const LABEL_FIVE_YEAR = '5年';
const LABEL_TEN_YEAR = '10年';

const TYPE_APP_TRADEMARK_PRODUCT_UNCHECKED = 0;
const TYPE_APP_TRADEMARK_PRODUCT_CHECKED = 1;

const TYPE_APP_TRADEMARK = 1; // 出願
const TYPE_SFT = 2; // はじめからサポート お申し込み
const TYPE_SFT_AMS = 3; // はじめからサポートサービス：AMSからの提案
const TYPE_PRE_CHECK = 4; // プレチェックサービス
const TYPE_PRE_CHECK_AMS = 5; // プレチェックサービス：AMSからのレポート
const TYPE_MATCHING_RESULT = 6; // 拒絶理由通知対応
const TYPE_MATCHING_RESULT_SELECTION = 7; // 拒絶理由通知対応：方針案選択
const TYPE_TRADEMARK_REGISTRATION = 8; // 商標登録
const TYPE_NOTIFY_DEADLINE_PAYMENT = 9; // 後期納付期限のお知らせ・納付手続きのお申込み
const TYPE_RENEWAL_DEADLINE = 10;
const TYPE_LIST_CHANGE_ADDRESS = 11; // 出願人名・住所変更
const TYPE_LIST_CHANGE_ADDRESS_KENRISHA = 12; // 登録名義人名・住所変更
const TYPE_EXTENSION_OF_PERIOD = 13; // 期限日前期間延長のお申込み
const TYPE_SELECT_PLAN_02 = 14; // 登録可能性評価レポート＆拒絶理由通知対応お申込み

const TYPE_FREE_HISTORY = 15;

const STATUS_ADMIN_CONFIRM = 3;
const STATUS_WAITING_FOR_USER_CONFIRM = 2;

/**
 * Type page of My Folder
 */
// 1: u011b, 2: u011b_31, 3: u021b, 4: u021b_31, 5: u031, 6: u031edit, 7: u031b, 8: u031c, 9: u031edit_with_number, 10: u031d
const TYPE_PAGE_SFT = 1;
const TYPE_PAGE_SFT_AMS = 2;
const TYPE_PAGE_PRECHECK = 3;
const TYPE_PAGE_PRECHECK_AMS = 4;
const TYPE_PAGE_APP_TRADEMARK_U031 = 5;
const TYPE_PAGE_APP_TRADEMARK_U031EDIT = 6;
const TYPE_PAGE_APP_TRADEMARK_U031B = 7;
const TYPE_PAGE_APP_TRADEMARK_U031C = 8;
const TYPE_PAGE_APP_TRADEMARK_NUMBER = 9;
const TYPE_PAGE_APP_TRADEMARK_U031D = 10;

/**
 * Info_type_acc
 */
const INFO_TYPE_ACC_GROUP = 1;
const INFO_TYPE_ACC_INDIVIDUAL = 2;

/**
 * Contact_type_acc
 */
const CONTACT_TYPE_ACC_GROUP = 1;
const CONTACT_TYPE_ACC_INDIVIDUAL = 2;

const URL_STORATE_FILE_PROFILE = 'user/profiles/files/';

const URL_STORATE_FILE_PROFILE_UPDATE = 'user/profiles/updates/';

const TIME_LIMIT_EDIT_EMAIL_USER = 1800;

//payment_type payer_infos
const PAYMENT_TYPE_CREDIT = 1;
const PAYMENT_TYPE_TRANSFER = 2;

const GENDER_MALE = 2;
const GENDER_FEMALE = 1;

//payer_type payer_infos
const PAYER_TYPE_TAX_AGENT = 1;
const PAYER_TYPE_REGIS_ADDRESS_OVERSEAS = 2;

// is choice admin support first time
const IS_CHOICE_ADMIN_CHECKED = 1;
const IS_CHOICE_ADMIN_UN_CHECKED = 0;

// Trademark
const TRADEMARK_TYPE_LETTER = 1;
const TRADEMARK_TYPE_OTHER = 2;

const TRADEMARK = 'trademark';

// Folder
const IS_MAX_FOLDER = 5;

// Search AI
const FROM_SEARCH_AI = 1;
const FROM_SUPPORT_FIRST_TIME = 2;
const FROM_PRECHECK = 3;
const FROM_USER_TOP = 4;
const FROM_U031B = 5;

const SEARCH_AI_CREATE = 1;
const SEARCH_AI_EDIT = 2;
const SEARCH_AI_REGISTER = 3;
const SEARCH_AI_PRECHECK = 4;

const SESSION_REFERER_SEARCH_AI = 'referer-search-ai';
const SESSION_SEARCH_AI = 'search-ai';
const SESSION_ADDITION_PRODUCT = 'addition-product-id';
const SESSION_SUGGEST_PRODUCT = 'suggest-product-id';
const SESSION_QUOTE = 'quote-product-id';
const SESSION_ANKEN_TOP = 'anken-top-product-id';
const SESSION_MPRODUCT_FORM_U021C = 'session_mproduct_from_u021c';
const SESSION_MPRODUCT_NAME = 'session_mproduct_name';
const SESSION_PRECHECK_FROM_A021KAN = 'session_precheck_from_a021kan';
const SESSION_SEARCH_TOP = 'search-top';
const SESSION_GO_TO_A021RUI_SHU = 'session_go_to_ao21_rui_shu';
const SESSION_NOTICE = 'session-notice';
const SESSION_U031B_REDIRECT_U020B = 'session-u031b-redirect-u020b';
const SESSION_NOTICE_A302_402_5YR_KOUKI_G = 'session-notice-a302-402-5yr-kouki-g';
const SESSION_NOTICE_A402 = 'session-notice-a402';
const SESSION_NOTICE_PDF_A301 = 'session-notice-pdf-a301';

// New Apply
const REDIRECT_TO_SUPPORT_FIRST_TIME = 1;
const REDIRECT_TO_PRECHECK = 2;
const REDIRECT_TO_SEARCH_AI = 3;
const REDIRECT_TO_REGISTER_APPLY_TRADEMARK = 4;
const REDIRECT_TO_MEMBER_REGISTER_PRE = 5;
// QA
const DRAFT_QA = 'draft';
const CONFIRM_QA = 'confirm';
const QUESTION_TYPE_CUSTOMER = 1;
const PAGINATE_NUMBER = 5;
// Forgot password
const SESSION_RESET_PASSWORD_REGISTRATION_EMAIL = 'reset-password-registration-email';
// Apply Trademark With Number
const SESSION_APPLY_TRADEMARK_WITH_NUMBER = 'apply-trademark-with-number';
const SESSION_APPLY_TRADEMARK_EDIT = 'apply-trademark-edit';

//key cookie list id product is selected
const KEY_SESSION_IDS_PRODUCT = 'key-session-ids-product';
const TIME_LIFE_COOKIE = 24 * 60 * 60;

// type product and code
const ORIGINAL_CLEAN = 1;
const REGISTER_CLEAN = 2;
const CREATIVE_CLEAN = 3;
const SEMI_CLEAN = 4;

// quantity charged extra charge limit
const NUMBER_PRODUCT_EXTRA_FEE_LIMIT = 3;

// 処理タイプ. 1: CHECK | 2: CAPTURE | 3: AUTH | 4: SAUTH
const GMO_JOB_CD_CHECK = 1;
const GMO_JOB_CD_CAPTURE = 2;
const GMO_JOB_CD_AUTH = 3;
const GMO_JOB_CD_SAUTH = 4;

// Payment GMO status
const PAYMENT_GMO_SUCCESS = 1;
const PAYMENT_GMO_FAIL = 2;

// Type acc for Notice
const NOTIFICATION_TYPE_ACC_USER = 1;  // 1: ユーザー,
const NOTIFICATION_TYPE_ACC_JIMU = 2;  // 2: 事務担当,
const NOTIFICATION_TYPE_ACC_TANTO = 3;  // 3: 担当者,
const NOTIFICATION_TYPE_ACC_SEKI = 4;  // 4: 責任者

// Notification type
const N_TYPE_TODO_LIST = 1; // 1: Todo list
const N_TYPE_NOTIFY_LIST = 2; // 2: Notify list

// U000 Anken Top
const RESPONSE_DEADLINE_DAY = 4;
const SORT_DESC_ANKEN = 2;
const SORT_ASC_ANKEN = 1;
const FROM_U000_TOP = 'u000top';
const FROM_U000_LIST = 'u000list';
// Notification flow type
const N_FLOW_TYPE_SFT = 1; // 1: はじめからサポート,
const N_FLOW_TYPE_PRECHECK = 2; // 2:  プレチェックサービス,
const N_FLOW_TYPE_FREE_HISTORY = 3; // 3: フリー履歴,
const N_FLOW_TYPE_QA = 4; // 4: Q&A,
const N_FLOW_TYPE_APP = 5; // 5: 出願,
const N_FLOW_TYPE_REASON_REFUSAL = 6; // 6: 拒絶理由通知対応,
const N_FLOW_TYPE_REGISTRATION = 7; // 7: 登録,
const N_FLOW_TYPE_RENEWAL = 8; // 8: 更新(5年, 10年),
const N_FLOW_TYPE_EXTEND = 9; // 9: 期限日前期間延長,

//screen from
const SCREEN_PRECHECK_N = 'screen-precheck-n';
const SCREEN_PRECHECK_RES = 'screen-precheck-res';
const _QUOTES = '_quotes';
const _ANKEN = '_anken';
const _PAYMENT = '_payment';

//const code screen
const U021 = 'u021';
const U021N = 'u021n';
const U021C = 'u021c';
const U021B_31 = 'u021b_31';
const U021B = 'u021b';
const U011B_31 = 'u011b_31';
const U011 = 'u011';
const U011B = 'u011b';
const U020A = 'u020a';
const U020B = 'u020b';
const U020C = 'u020c';

const U000TOP = 'u000top';
const U000ANKEN_TOP = 'u000anken_top';
const U000FREE = 'u000free';
const U031 = 'u031';
const U031B = 'u031b';
const U031D = 'u031d';
const U031C = 'u031c';
const U032 = 'u032';
const U032_CANCEL = 'u032_cancel';
const A031 = 'a031';

const U000LIST_CHANGE_ADDRESS = 'u000list_change_address';
const U000LIST_CHANGE_ADDRESS_02 = 'u000list_change_address_02';
const U000LIST_CHANGE_ADDRESS_02_KENRISHA = 'u000list_change_address_02_kenrisha';
const U031EDIT = 'u031edit';
const U031_EDIT_WITH_NUMBER = 'u031_edit_with_number';
const U201SELECT02 = 'u201select02';
const A000FREE_S = 'a000free_s';

const A000NEWS_EDIT = 'a000news_edit';

const A000FREE02 = 'a000free02';
const U202KAKUNIN = 'u202kakunin';
const U202KAKUNIN_DRAFT = 'u202_kakunin_draft';
const U202 = 'u202';
const U202_DRAFT = 'u202_draft';
const U202_DRAFT_TO_KAKUNIN = 'u202_draft_redirect_to_kakunin';
const U202N = 'u202n';
const U202N_DRAFT = 'u202n_draft';
const U202N_DRAFT_REDIRECT_KAKUNIN = 'u202n_draft_redirect_to_kakunin';
const U202N_KAKUNIN = 'u202n_kakunin';
const U202N_KAKUNIN_DRAFT = 'u202n_kakunin_draft';
const U201_SIMPLE = 'u201simple01';
const U201_SELECT_01 = 'u201select01';
const U201_SELECT_01_N = 'u201select01n';
const U201B_CANCEL = 'u201b_cancel';
const U201_SIMPLE01_ALERT = 'u201_simple_alert';
const U201_SIMPLE01_OVER = 'u201_simple_over';
const U201_SELECT01_ALERT = 'u201_select_01_alert';
const U201_SELECT01_OVER = 'u201_select_01_over';
const U203C = 'u203c';
const U203C_N = 'u203c_n';
const U203 = 'u203';
const U203N = 'u203n';
const U203B02 = 'u203b02';
const A201A = 'a201a';
const A201B = 'a201b';
const A201B02 = 'a201b02';
const A201B02S = 'a201b02s';
const A201B02_N = 'a201b02_n';
const A201B02_S_N = 'a201b02_s_n';
const A202N = 'a202n';
const A202S = 'a202s';
const A203 = 'a203';
const A203SHU = 'a203shu';
const A203n = 'a203n';
const A203C = 'a203c';
const A203C_RUI = 'a203c_rui';
const U210_ALERT_02 = 'u210alert02';
const U210_OVER_02 = 'u210over02';
const A203S = 'a203s';
const A203CHECK = 'a203check';
const A203SASHI = 'a203sashi';
const A202N_S = 'a202n_s';
const A204HAN_N = 'a204han_n';
const A205 = 'a205';
const A205_KAKUNIN = 'a205_kakunin';
const A205_SASHI = 'a205_sashi';
const A205_HIKI = 'a205_hiki';
const A205_SHU = 'a205_shu';
const A205S = 'a205s';
const A000Top = 'a000top';

const A000ANKEN_TOP = 'a000anken_top';

const U207Kyo = 'u207_kyo';
const QUOTE = 'quote';
const COMMON_PAYMENT = 'common_payment';
const U204 = 'u204';
const U204N = 'u204n';
const U205 = 'u205';
const A210Alert = 'a210alert';
const A210Over = 'a210over';
const U210_Encho = 'u210_encho';
const A301 = 'a301';
const A700_SHUTSUGANNIN03 = 'a700_shusugannin03';
const A700_SHUTSUGANNIN02 = 'a700_shusugannin02';
const A700KENRISHA03 = 'a700kenrisha03';
const A302_HOSEI02 = 'a302_hosei02';
const A302_HOSEI02_SKIP = 'a302_hosei02_skip';
const A302_HOSEI01_WINDOW = 'a302_hosei01_window';
const A302_402_5YR_KOUKI = 'a302_402_5yr_kouki';
const A402 = 'a402';
const A402_HOSOKU_02 = 'a402_hosoku02';
const A303 = 'a303';
const U302 = 'u302';
const U302CANCEL = 'u302cancel';
const A302 = 'a302';
const A011S = 'a011s';
const A021KAN = 'a021kan';
const A021S = 'a021s';
const A021RUI_SHU = 'a021rui_shu';
const A203C_SHU = 'a203c_shu';
const U302_402 = 'u302_402';
const U302_402TSUINO = 'u302_402tsuino';
const U302_402_5YR_KOUKI = 'u302_402_5yr_kouki';
const U302_402TSUINO_5YR_KOUKI = 'u302_402tsuino_5yr_kouki';
const U302_402KAKUNIN_INJO = 'u302_402kakunin_injo';
const U402 = 'u402';
const U402CANCEL = 'u402cancel';
const U402TSUINO = 'u402tsuino';
const U303 = 'u303';
const U304 = 'u304';

// Trademark Table Type
const TYPE_1 = '1';
const TYPE_2 = '2';
const TYPE_3 = '3';
const TYPE_4 = '4';
const TYPE_5 = '5';
const TYPE_6 = '6';
const TYPE_7 = '7';
const TYPE_8 = '8';
const SHOW_EDIT_REFERENCE_NUMBER = 'show_edit_reference_number';
const SHOW_LINK_ANKEN_TOP = 'show_link_anken_top';

const TYPE_ADMIN_1 = 'admin_1';
const TYPE_ADMIN_2 = 'admin_2';
const TYPE_ADMIN_3 = 'admin_3';
const TYPE_ADMIN_4 = 'admin_4';

// Session
const FROM_PAGE_U021 = 'U021';

// Agent group map type
const AGENT_SELECTION_TYPE = 1;
const APPOINTED_AGENT_SELECTION_TYPE = 2;

// Precheck Result
const NO_REGISTED = 0;
const LIKELY_TO_BE_REGISTERED = 1;
const LOOK_FORWARD_TO_REGISTERING = 2;
const LESS_LIKELY_TO_BE_REGISTERED = 3;
const DIFFICULT_TO_REGISTER = 4;
const IS_REGISTER_PRODUCT = 1;
const IS_NO_REGISTER_PRODUCT = 0;

const FROM_A021SHIKISHU_TO_A021S = 'from_a021shikishu_to_a021s';
const SEND_TO_USER = 'send_to_user';
const TYPE_CHECK_SIMPLE_OR_SIMILAR = 'check_simple_or_similar';

// Admin Role

const ROLE_OFFICE_MANAGER = 1; // Jimu
const ROLE_MANAGER = 2; // Tantou
const ROLE_SUPERVISOR = 3; // Seki

// Page limit
const PAGE_LIMIT_3 = 3;
const PAGE_LIMIT_5 = 5;
const PAGE_LIMIT_10 = 10;
const PAGE_LIMIT_50 = 50;
const PAGE_LIMIT_100 = 100;

const NOTICE_CODE = 'notice';

// Change List Address 02
const BANK_TRANSFER = 2;
const TYPE_CHANGE_NAME = 1;
const TYPE_CHANGE_ADDRESS = 2;
const TYPE_CHANG_DOUBLE = 3;
const DEFAULT_COST_SERVICE_BASE_LIST_CHANGE_ADDRESS = 0;
const IS_SEND_TRUE = 1;
const IS_SEND_FALSE = 0;
const REDIRECT_TO_COMMON_PAYMENT = 1;
const REDIRECT_TO_COMMON_QUOTE = 2;
const REDIRECT_TO_KAKUNIN = 5;
const APPLICATION = 1;
const REGISTER = 2;

// List Change Address 02 Kenrisha
const REDIRECT_TO_COMMON_PAYMENT_KENRISHA = 3;
const REDIRECT_TO_COMMON_QUOTE_KENRISHA = 4;
const TRADEMARK_TABLE_KENRISHA = 'table_list_change_address_kenrisha';
const U000_FREE = 'u000_free';
// Cost Default
const COST_DEFAULT = 0;
const DAY_EXPIRED = 3;

//free history
const TYPE_NO_REPORT_CUSTOMER_AGENT_PROCEDURES = 1;
const TYPE_NO_REPORT_CUSTOMER_NO_AGENT_PROCEDURES = 2;
const TYPE_REPORT_CUSTOMER_NO_AGENT_PROCEDURES = 3;
const TYPE_CUSTOMER_FEEDBACK_REQUIRED = 4;
const IS_CHECK_AMOUNT = 1;
const IS_NOT_CHECK_AMOUNT = 0;

// Import
const SESSION_IMPORT_01 = 'session_to_import_01';
// Payment
const ID_DEFAULT = 0;

// Refusal
const OPEN_MODAL = 'open_modal';
const IS_CANCEL_TRUE = 1;

// Simple 01
const REDIRECT_TO_COMMON_PAYMENT_SIMPLE = 1;
const REDIRECT_TO_QUOTE_SIMPLE = 2;
const REDIRECT_TO_ANKEN_TOP = 3;
const FLAG_SIMPLE = 'u201_simple01';
const IS_EXT_PERIOD_TRUE = 1;
// Select 01
const REDIRECT_TO_COMMON_PAYMENT_SELECT = 4;
const REDIRECT_TO_QUOTE_SELECT = 5;
const REDIRECT_TO_ANKEN_TOP_SELECT = 8;
const FLAG_PROD_ADD = 'prod_add';
const PLAG_SELECT_01 = 'u201select01';
// Select 01 n
const REDIRECT_TO_COMMON_PAYMENT_SELECT_01N = 6;
const REDIRECT_TO_QUOTE_SELECT_01N = 7;
const REDIRECT_TO_ANKEN_TOP_SELECT_01_N = 9;
const PLAG_SELECT_01_N = 'select_01_n';
// A031
const TRADEMARK_TABLE_A031 = 'A031';
// Type Button
const VIEW = 'view';
const ACTION = 'action';
const EDIT = 'edit';
// From Page
const FROM_PAGE_U032 = 'u032';

//const status submit a202n_s
const SAVE_SUBMIT = 'save-submit';
const SAVE_DRAFT = 'save-draft';
const SAVE_TO_END_USER = 'save-to-end-user';
const SAVE_COMPLATE_QUESTION = 'save-complate-question';
const IS_REGISTER_FALSE = 0;
const IS_REGISTER_TRUE = 1;
const U201 = 'u201';
const IS_ADD_TRUE = 1;
const IS_DISTINCT_SETTLEMENT = 1;
const LEAVE_STATUS_TYPES = [
    1 => '残す',
    2 => '削除',
    3 => '※',
    4 => '-',
    5 => 'NG',
    6 => '追加',
    7 => '追加せず',
    8 => '※（追加）',
    9 => '※（追加せず）',
];
const LABEL_LEAVE_STATUS_DELETE = '追加しない';
const TEXT_REVOLUTION = 'text_revolution';
const IS_CHOICE = 1;
const  NOT_LEAVE_ALL = 0;
const  IS_LEAVE_ALL = 1;

const LEAVE_STATUS_2 = 2;
const LEAVE_STATUS_3 = 3;
const LEAVE_STATUS_4 = 4;
const LEAVE_STATUS_5 = 5;
const LEAVE_STATUS_6 = 6;
const LEAVE_STATUS_7 = 7;
const LEAVE_STATUS_9 = 9;
//TRADEMARK PLAN
const TRADEMARK_PLAN_IS_CONFIRM_TRUE = 1;
const TRADEMARK_PLAN_IS_REJECT_TRUE = 1;
const TRADEMARK_PLAN_IS_REGISTER_TRUE = 1;
// U205
const IS_WRITTEN_OPINION = 1;
const IS_NOT_WRITTEN_OPINION = 0;

const IS_NOT_REJECT = 0;
const CREATE_A205S = 'create_a205s';
const CREATE_A205SHASHI = 'create_a205shashi';

//Folder update a205
const FOLDER_UPLOAD_FILE_A205 = '/uploads/a205/files/';
const FOLDER_TEMP = LOCAL_PUBLIC_FOLDER . '/temp';
const FOLDER_MATERIAL = LOCAL_PUBLIC_FOLDER . '/materials';
const ATTACH = 'attach';
const URL = 'url';
const SAVE_DRAFT_A205Hiki = 1;
const SUBMIT_A205Hiki = 2;
const FLAG_ROLE = 2;
const PACK_C = 3;

// import XML
const IS_REFUSAL = 2;

const ERROR_PAYMENT_TYPE_CREDIT = 'error_credit';
const ERROR_PAYMENT_TYPE_TRANSFER = 'error_transfer';
//  Trademark Block By
const ALERT_01 = 'alert01';
const OVER_03 = 'over03';
const OVER_04 = 'over04';
const OVER_04B = 'over04b';
const OVER_05 = 'over05';

const SEND_NOTICE_USER = 1;
const SEND_NOTICE_BENRISHI = 2;
const FLAG_ROLE_SEKI = 2;
const SFT_IS_CONFIRM = 1;
const TRADEMARK_PLAN_IS_CANCEL = 1;
const DOC_SUBMISSION_IS_REJECT = 1;
const DOC_SUBMISSION_IS_CONFIRM = 1;

// a201b02xx
const MAX_PRODUCT_NAME = 30;

/**
 * Flow
 */
const FLOW_UPDATE_U302_402_5YR_KOUKI = 'flow_update_u302_402_5yr_kouki';
const FLOW_REGISTER_U402 = 'flow_register_u402';

/**
 * String is_apply
 */
const APPLY = 'apply';
const NOT_APPLY = 'not_apply';
const ALL_APPLY = 'all_apply';

const IS_CONFIRM_HISTORY = 1;

/**
 * Start invoice_number
 */
const START_INVOICE_NUMBER = 50;
const DEFAULT_INVOICE_NUMBER = 'S';
const DEFAULT_RECEIPT_NUMBER = 'R';

const PAYMENT_STATUS_0 = 0;
const PAYMENT_STATUS_1 = 1;
const PAYMENT_STATUS_2 = 2;

const PAYMENT_STATUS_3 = 3;

const SETTING_PUBLISHER_POSTAL_CODE = 'publisher_postal_code';
const SETTING_PUBLISHER_ADDRESS_FIRST = 'publisher_address_first';
const SETTING_PUBLISHER_ADDRESS_SECOND = 'publisher_address_second';
const SETTING_PUBLISHER_TELL = 'publisher_tel';
const SETTING_PUBLISHER_TAX = 'publisher_fax';
const SETTING_STAMP = 'stamp';
const SETTING_PUBLISHER_REGISTRATION_NUMBER = 'publisher_registration_number';
const SETTING_BANK_INFORMATION = 'bank_information';

const OLD_VALUE_BIRTHDAY_USER = 'old-value-birthday-user';

//payment type
const PAYMENT_TYPE_1 = 1;
const PAYMENT_TYPE_2 = 2;
const PAYMENT_TYPE_3 = 3;
const PAYMENT_TYPE_4 = 4;
const PAYMENT_TYPE_5 = 5;

const VALUE_0 = 0;
const VALUE_1 = 1;
const VALUE_2 = 2;
const VALUE_3 = 3;
const VALUE_4 = 4;
const VALUE_5 = 5;

const IS_UPDATED_TRUE = 1;

const IS_ANSWER_TRUE = 1;
const LIMIT_ADD_ROW = 100;

const EQUAL = 'equal';
const START_FROM = 'start_from';
const CONSISTS_OF = 'consists_of';
const IS_GREATER_THAN = 'is_greater_than';
const IS_LESS_THAN = 'is_less_than';

//option search
const SEARCH_DISTINCTION_NAME = 1;
const SEARCH_PRODUCT_NAME = 2;
const SEARCH_CODE_NAME = 3;
const SEARCH_CONCEPT = 4;

//type where
const WHERE_AND = 'and';
const WHERE_OR = 'or';
const LIKE = 'like';

//const status delete
const NOT_DELETE = 0;
const IS_DELETE = 1;

// display_info_status
const LAW = 1;
const CHANGE_NAME = 2;
const CHANGE_ADDRESS = 3;

// TYPE EXPORT
const TEXT = 'text';
const TITLE = 'title';
const IMAGE = 'image';
const MULTI_IMAGE = 'multi_image';
