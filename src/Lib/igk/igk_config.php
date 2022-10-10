<?php

// @file:igk_config.php
// description: Represent configuration and constants settings
// @author: C.A.D. BONDJE DOUE
// create: 01/01/2013
// license: view license.txt
// copyright: igkdev @ 2019

if (defined('IGK_SYS_CONFIG_FILE'))
    return;




//+ |----------------------------------------------------------- 
//+ | Configuration data
//+ |-----------------------------------------------------------
define('IGK_ENV_DB_INIT_CTRL', 'sys://env/init_ctrl'); 
define('IGK_LOCAL_DEBUGGING', 1);
define('IGK_NODESTROY_ON_FATAL', 1);
define('IGK_SYS_CONFIG_FILE', 1);
define('IGK_NO_TRACELOG', 1);
// + define('IGK_NO_SESSION', 1); 
// +  define("IGK_TRACE", 1);
// + define('IGK_ENV_PRODUCTION', 1);
// + | global disable view cache
// + define("IGK_NO_VIEW_CACHE", 1);

// + | global disable handle page cache
// + defined"IGK_NO_PAGE_CACHE") || define("IGK_NO_PAGE_CACHE", 1);
//+ |----------------------------------------------------------- 
//+ | ENVIRONMENT SETTING KEYS - DO NOT REPLACE THAT VALUES
//+ |----------------------------------------------------------- 
define('IGK_AJX_BINDSTYLES', 1);
define('IGK_ATTACHCHILD_FLAG', 2);
define('IGK_ATTACHDISPOSE_FLAG', 3);
define('IGK_ATTRS_FLAG', 4);
define('IGK_AUTH_FLAG', 5);
define('IGK_AUTODINDEX_FLAG', 6);
define('IGK_BASE_EVENT', 7);
define('IGK_CALLBACK_FLAG', 8);
define('IGK_CHILDS_FLAG', 9);
define('IGK_COMPONENT_ID_KEY', 10);
define('IGK_COMPONENT_ID_PARAM', 11);
define('IGK_COMPONENT_REG_FUNC_KEY', 12);
define('IGK_COMP_NOT_FOUND_EVENT', 13);
define('IGK_CONFIG_FLAG', 14);
define('IGK_CONF_PAGEFOLDER_CHANGED_EVENT', 15);
define('IGK_CONF_USER_CHANGE_EVENT', 16);
define('IGK_CREATE_AT', 17);
define('IGK_CTRL_IDENTIFIER', 18);
define('IGK_CTRL_LANG', 19);
define('IGK_CTRL_TABLE_INFO_KEY', 20);
define('IGK_CTRL_TG_NODE', 21);
define('IGK_CTRL_VIEW_CONTEXT_PARAM_KEY', 22);
define('IGK_APP_CURRENT_DOC_INDEX_ID', 23);
define('IGK_CURRENT_DOC_PARAM_KEY', 24);
define('IGK_DATA_ADAPTER_CTRL', 25);
define('IGK_DEFINEDNS_FLAG', 26);
define('IGK_DESC_FLAG', 27);
define('IGK_DOC_CONF_ID', 28);
define('IGK_DOC_ERROR_ID', 29);
define('IGK_DOC_ID_PARAM', 30);
define('IGK_DROP_CTRL_EVENT', 31);
define('IGK_ENV_APP_CONTEXT', 32);
define('IGK_ENV_APP_INIT', 33);
define('IGK_ENV_CALLBACK_KEYS', 34);
define('IGK_ENV_COMPONENT_DISPLAY_NAMES_KEY', 35);
define('IGK_ENV_COMPONENT_REFDIRS_KEY', 36);
define('IGK_ENV_CTRL_VIEW', 37);
define('IGK_ENV_CURRENT_RENDERING_DOC', 38);
define('IGK_ENV_GLOBAL_SETTING', 39);
define('IGK_ENV_HTML_COMPONENTS', 40);
define('IGK_ENV_HTML_NS_PREFIX', 41);
define('IGK_ENV_INVOKE_ARGS', 42);
define('IGK_ENV_LANG_CHANGED', 43);
define('IGK_ENV_NEW_DOC_CREATED', 44);
define('IGK_ENV_NO_AJX_TEST', 45);
define('IGK_ENV_NO_COOKIE_KEY', 46);
define('IGK_ENV_NO_TRACE_KEY', 47);
define('IGK_ENV_PAGEFOLDER_CHANGED_KEY', 48);
define('IGK_ENV_PARAM_KEY', 49);
define('IGK_ENV_REQUEST_METHOD', 50);
define('IGK_ENV_SETTING_CHANGED', 51);
define('IGK_ENV_THEME_CHANGED', 52);
define('IGK_ENV_URI_PATTERN_KEY', 53);
define('IGK_ENV_WIDGETS_KEY', 54);
define('IGK_FIRSTUSE_FLAG', 55);
define('IGK_HOOK_DB_CHANGED', 56);
define('IGK_HOOK_DB_TABLECREATED', 57);
define('IGK_INVOKE_URI_CTRL', 58);
define('IGK_ISDISPOSING_FLAG', 59);
define('IGK_ISINIT_FLAG', 60);
define('IGK_ISLOADING_FLAG', 61);
define('IGK_ISVISIBLE_FLAG', 62);
define('IGK_KEY_CSS_NOCLEAR', 63);
define('IGK_KEY_DOC_NO_STORE_RENDERING', 64);
define('IGK_KEY_FORCEVIEW', 65);
define('IGK_KEY_LASTDOC', 66);
define('IGK_KEY_PARAM_SESSION_START_AT', 67);
define('IGK_KEY_SYSDB_CTRL', 68);
define('IGK_KEY_TOOLS', 69);
define('IGK_KEY_VIEW_FORCED', 70);
define('IGK_LAST_EVAL_KEY', 71);
define('IGK_LF_KEY', 72);
define('IGK_LOADINGCONTEXT_FLAG', 73);
define('IGK_LOG_VERBOSITY', 74);
define('IGK_LOG_VERBOSITY_NONE', 75);
define('IGK_LOG_VERBOSITY_SOURCE_FILE', 76);
define('IGK_LOG_VERBOSITY_SOURCE_LOCATION', 77);
define('IGK_MSBOX_CTRL', 79);
define('IGK_NAMED_ID_PARAM', 80);
define('IGK_NAMED_NODE_PARAM', 81);
define('IGK_NODECONTENT_FLAG', 82);
define('IGK_NODETAG_FLAG', 83);
define('IGK_NODETYPENAME_FLAG', 84);
define('IGK_NODETYPE_FLAG', 85);
define('IGK_NODE_CREATE_ARGS_FLAG', 86);
define('IGK_NODE_DISPOSED_EVENT', 87);
define('IGK_NODE_FLAG', 88);
define('IGK_NOTIFICATION_APP_DOWNLOADED', 89);
define('IGK_NOTIFICATION_DB_TABLEDROPPED', 90);
define('IGK_NOTIFICATION_INITTABLE', 91);
define('IGK_NOTIFICATION_USER_CHANGED', 92);
define('IGK_NSFC_FLAG', 93);
define('IGK_NS_PARAM_KEY', 94);
define('IGK_OBJ_TYPE_CALLBACK', 95);
define('IGK_OBJ_TYPE_CLASS', 96);
define('IGK_OBJ_TYPE_EXPRESSION', 97);
define('IGK_OBJ_TYPE_FILE', 98);
define('IGK_OBJ_TYPE_FUNC', 99);
define('IGK_OBJ_TYPE_NODE', 100);
define('IGK_OTHER_MENU_CTRL', 101);
define('IGK_PAGE_CONF_CTRL', 102);
define('IGK_PARAMS_FLAG', 103);
define('IGK_PARENTHOST_FLAG', 104);
define('IGK_PARENT_FLAG', 105);
define('IGK_PREVIOUS_CIBLING_FLAG', 106);
define('IGK_SERVER_INFO', 107);
define('IGK_SESSION_ID', 108);
define('IGK_SESS_FLAG', 109);
define('IGK_SESS_UNKCOLOR_KEY', 110);
define('IGK_SORTREQUIRED_FLAG', 111);
define('IGK_STYLE_FLAG', 112);
define('IGK_SUBDOMAIN_CTRL', 113);
define('IGK_SUBDOMAIN_CTRL_INFO', 114);
define('IGK_SVG_REGNODE_KEY', 115);

define('IGK_VERSION_ID', 117);
define('IGK_VIEW_ARGS', 118);
define('IGK_VIEW_MODE_FLAG', 119);
define('IGK_VIEW_OPTIONS', 120);
define('IGK_XML_CREATOR_PARENT_KEY', 121);
define('IGK_XML_HTML_TEMPLATE_PARENT_KEY', 122);
define('IGK_ZINDEX_FLAG', 123);
define('IGK_KEY_DOCUMENTS', 124);
define('IGK_CSS_TEMP_FILES_KEY', 125);
define('IGK_HOOK_VIEW_MODE_CHANGED', 126);
define('IGK_CURRENT_CTRL_VIEW', 127);
define('IGK_FORM_CREF', 128);
define('IGK_CFG_USER', 129);
define('IGK_TOOLS', 130);
define('IGK_SESS_ROUTES', 131);
define('IGK_ENV_CSS_GLOBAL_CONF_FILES', 132);
define('IGK_CLIENT_IP', 133);
define('IGK_CLIENT_AGENT', 134);
define('IGK_CONNEXION_FRAME', 193);
define('IGK_XML_CREATOR_NODE_RESULT', 194);
define('IGK_LAST_EVAL_LINE', 195);
define('IGK_XML_CREATOR_SKIP_ADD', 196);
define('IGK_ENV_CONFIG_ENTRIES', 197);
// application info : request uri
define('IGK_APP_REQUEST_URI', 198);
define('VIEW_EXTRA_ARGS', 199);
define('IGK_APP_INFO_TYPE', 200);

define("IGK_ENV_SESS_DOM_LIST", 0x201);
define("IGK_CUSTOM_CTRL_PARAM", 0x400);
//-------------------------------------------------------
// | start environment constant key
define("IGK_ENV_KEY", 0xE0);


define('IGK_ENV_REQUIREMENT_KEY', 200);
define('IGK_DOCUMENT_CLASS', 201);
define('IGK_ENV_TRACE_LEVEL', 202);
define('IGK_ENV_QUERY_LIST', 203);

define('IGK_HTML_NOTAG_ELEMENT', "NoTagNode");

/**
 * basic user setting.
 */
define('IGK_USER_SETTING', 0xE00);
//+ -----------------------------------------------------------------------
//+ flags value
//+ -----------------------------------------------------------------------

//+ define('IGK_APP', '1');
//+ define('IGK_APP_PLUGIN', '1');
//+ define('IGK_BASE_DIR', '1');
//+ define('IGK_CACHE_REQUIRE', '1');
//+ define('IGK_CACHE_URI', '1');
//+ define('IGK_COMPONENT_CTRL_FLAG', '1');
//+ define('IGK_CONFIG_PAGE', '1');
//+ define('IGK_CONF_CONNECT', '1'); 
//+ define('IGK_CREF', '1');
//+ define('IGK_CURRENT_PAGEFOLDER', '1');
//+ define('IGK_DB_GRANT_CAN_INIT', '1');
//+ define('IGK_DESIGN_MAINPAGE', '1');
//+ define('IGK_DOCUMENT_ROOT', '1');
//+ define('IGK_DOC_FAVICON_FLAG', '1');
//+ define('IGK_DOC_LINKMANAGER_FLAG', '1');
//+ define('IGK_DOC_LOADED_SCRIPT_FLAG', '1');
//+ define('IGK_DOC_METAMANAGER_FLAG', '1');
//+ define('IGK_DOC_SCRIPTMANAGER_FLAG', '1');
//+ define('IGK_DOC_TITLE_FLAG', '1');
//+ define('IGK_DOC_TYPE_FLAG', '1');
//+ define('IGK_DOMAINBASEDIR_SESS_PARAM', '1');
//+ define('IGK_ENV', '1');
//+ define('IGK_ENV_PARAM_LANGCHANGE_KEY', '1'); 
//+ define('IGK_ERROR_OP_NOT_ALLOWED', '1');
//+ define('IGK_ERROR_REQUEST_NOT_FROM_BALAFON_SERVER', '1');
//+ define('IGK_ERR_FILE_NOT_SUPPORTED', '1');
//+ define('IGK_ERR_FUNCNOTAVAILABLE', '1');
//+ define('IGK_FILE_NAME', '1');
//+ define('IGK_FORCSS', '1');
//+ define('IGK_FRAMEWORK_ATOMIC', '1');
//+ define('IGK_IE11_ENGINE', '1');
//+ define('IGK_INIT', '1');
//+ define('IGK_INSTANCES_SESS_PARAM', '1');
//+ define('IGK_JS_TRIMSCRIPTS', '1');
//+ define('IGK_KEY_DOCUMENTS', '1');
//+ define('IGK_KEY_LAST_RENDERED_DOC', '1');
//+ define('IGK_LOG_ERROR_FILE', '1');
//+ define('IGK_LOG_FILE', '1');
//+ define('IGK_MAIN_FILE', '1');
//+ define('IGK_MODULE_DIR', '1');
//+ define('IGK_MSQL', '1');
//+ define('IGK_MSQL_DB_A', '1');
//+ define('IGK_MYSQL_DATETIME_FORMAT', '1');
//+ define('IGK_NOCURRENTPAGE', '1');
//+ define('IGK_HOOK_DB_CHANGED', '1');
//+ define('IGK_NO_BASEURL', '1');
//+ define('IGK_NO_CACHE_LIB', '1');
//+ define('IGK_NO_LIB_EXTENSION', '1');
//+ define('IGK_NO_REST_ACTION', '1');
//+ define('IGK_NO_SESSION', '1');
//+ define('IGK_NO_SESSION_BUTTON', '1');
//+ define('IGK_NO_WEB', '1');
//+ define('IGK_NO_WEB_REDIRECT', '1');
//+ define('IGK_PADDING_HEADER', '1');
//+ define('IGK_PHAR_CONTEXT', '1');
//+ define('IGK_PROJECT_DIR', '1');
//+ define('IGK_REDIRECTION', '1');
//+ define('IGK_REDIRECTION_SESS_PARAM', '1');
//+ define('IGK_REDIRECT_ACCCESS', '1');
//+ define('IGK_REWRITE_MOD', '1');
//+ define('IGK_SERVER', '1');
//+ define('IGK_SESS_DIR', '1');
//+ define('IGK_SINGLE_CONTROLLER_APP', '1');
//+ define('IGK_TESTING', '1');
//+ define('IGK_TRACE_CLEAN', '1');
//+ define('IGK_UPLOADFILE', '1');
//+ define('IGK_UP_FILE_SIZE', '1');
//+ define('IGK_UP_FILE_TYPE', '1');
//+ define('IGK_VERBOSE', '1');
//+ define('IGK_WRITE_LOG', '1');
//+ define('IGK_X_REQUESTED_WITH', '1'); 
// define("IGK_AJX_BINDSTYLES", 0x001C);
// define("IGK_COMPONENT_ID_KEY", 0x001D);
// define("IGK_COMPONENT_REG_FUNC_KEY", 0x0023);
// define("IGK_COMP_NOT_FOUND_EVENT", 0x0027); 
define("IGK_DEFAULT_TIMEZONE", 'Europe/Brussels');
define("IGK_CSS_DEFAULT_STYLE_FUNC_KEY", 'sys://css/function/defaultStyle');
// define("IGK_CTRL_TABLE_INFO_KEY", 0x001A);
// define("IGK_CURRENT_DOC_PARAM_KEY", 0x0024);
// define("IGK_DOC_CONF_ID", 0x0037);
// define("IGK_DOC_ERROR_ID", 0x0036); 
// define("IGK_ENV_CALLBACK_KEYS", 0x0016);
// define("IGK_ENV_COMPONENT_DISPLAY_NAMES_KEY", 0x0014);
// define("IGK_ENV_COMPONENT_REFDIRS_KEY", 0x0015);
// define("IGK_ENV_CTRL_VIEW", 0x0012);
// define("IGK_ENV_CURRENT_RENDERING_DOC", 0x0038); 
// define("IGK_ENV_HTML_COMPONENTS", 0x000B);
// define("IGK_ENV_HTML_NS_PREFIX", 0x000C);
// define("IGK_ENV_INVOKE_ARGS", 0x0013);
// define("IGK_ENV_NO_AJX_TEST", 0x000A); 
// define("IGK_ENV_NO_TRACE_KEY", 0x0010);
// define("IGK_ENV_PAGEFOLDER_CHANGED_KEY", 0x001F);
// define("IGK_ENV_PARAM_KEY", 0x001E);
// define("IGK_ENV_URI_PATTERN_KEY", 0x0021);
// define("IGK_ENV_WIDGETS_KEY", 0x0018);
// define("IGK_DROP_CTRL_EVENT", 0x0026);
// define("IGK_KEY_CSS_NOCLEAR", 0x0008);
// define("IGK_KEY_DOC_NO_STORE_RENDERING", 0x0009);
// define("IGK_KEY_FORCEVIEW", 0x0004);
// define("IGK_KEY_TOOLS", 0x0006);
// define("IGK_KEY_VIEW_FORCED", 0x0005);
// define("IGK_LAST_EVAL_KEY", 0x0017); 
// define("IGK_NAMED_ID_PARAM", 0x002C);
// define("IGK_NAMED_NODE_PARAM", 0x002B);
// define("IGK_NODE_DISPOSED_EVENT", 0x0029);

// define("IGK_NOTIFICATION_DB_TABLEDROPPED", 0x0032);
// define("IGK_NOTIFICATION_INITTABLE", 0x0035);
// define("IGK_NOTIFICATION_USER_CHANGED", 0x0034);
// define("IGK_NS_PARAM_KEY", 0x0022);
// define("IGK_SESS_UNKCOLOR_KEY", 0x002D);
// define("IGK_SVG_REGNODE_KEY", 0x000E); 
// define('IGK_ENV_DB_INIT_CTRL', "sys://db_init_table/ctrl");
// define("IGK_TODAY", date("Y-m-d"));
define("IGK_DEFAULT_LANG_FOLDER", IGK_LIB_DIR . "/Default/Lang/");

define("IGK_SESSION_FILE_PREFIX", "blf_sess_");
defined('IGK_APP_SESSION_KEY') || define("IGK_APP_SESSION_KEY", "igk");
defined('IGK_DEFAULT_APP_COOKIE_NAME') || define('IGK_DEFAULT_APP_COOKIE_NAME', 'blf-c');
define("IGK_LOG_SYS", "BLF");
define("IGK_COMPONENT_NAMESFILE", IGK_LIB_DIR . "/Data/References/Components/Inc/names.pinc");
define("IGK_DEFAULT_THEME_ID", "theme://document");
define("IGK_DIE_DEFAULT_MSG", "die call");
// define("IGK_LOG_VERBOSITY_NONE", 0);
// define("IGK_LOG_VERBOSITY_SOURCE_FILE", 1);
// define("IGK_LOG_VERBOSITY_SOURCE_LOCATION", 2);
// define("IGK_LOG_VERBOSITY", 3);
define("IGK_DB", "Db");
define("IGK_INIT_COMPLETE_METHOD", "InitComplete");
define("IGK_PROTECT_ACCESS", "defined('IGK_FRAMEWORK') || die('direct access not allowed');\n");
define("IGK_KEY_APP", "igk");
//    define("IGK_KEY_GLOBALVARS", "sys://igk/globalvars");
//    define("IGK_KEY_FORCEVIEW", "sys://igk/forceview");
//    define("IGK_KEY_VIEW_FORCED", "sys://igk/viewforced");
//    define("IGK_KEY_TOOLS", "sys://igk/tools");
//    define("IGK_KEY_CSS_NOCLEAR", "sys://css/noclear");
define("IGK_KEY_APP_SELECTED_USER_PROFILE", "app://selectedUserProfile");
//    define("IGK_KEY_DOC_NO_STORE_RENDERING", "sys://document/NOSTORERENDERING");
define("IGK_ROOT_SERVER", "http://www.igkdev.com");
//    define("IGK_ENV_NO_AJX_TEST", "sys://env/no_ajx_test");
//    define("IGK_ENV_HTML_COMPONENTS", "sys://env/html/components");
//    define("IGK_ENV_HTML_NS_PREFIX", "sys://env/html/prefix");
// define("IGK_BASE_EVENT", 0xe00);
// define("IGK_ENV_SETTING_CHANGED", IGK_BASE_EVENT + 1);
// define("IGK_ENV_APP_INIT", IGK_BASE_EVENT + 2);
// define("IGK_ENV_NEW_DOC_CREATED", IGK_BASE_EVENT + 3);
// define("IGK_ENV_LANG_CHANGED", IGK_BASE_EVENT + 4);
// define("IGK_ENV_THEME_CHANGED", IGK_BASE_EVENT + 5);
//    define("IGK_SVG_REGNODE_KEY", "sys://node/svg_register"); 
//    define("IGK_ENV_NO_TRACE_KEY", "sys://no_trace"); 
//    define("IGK_ENV_CTRL_VIEW", "sys://igk_ctrl_view/mode");
//    define("IGK_ENV_INVOKE_ARGS", "sys://igk/invokeuri/args");
//    define("IGK_ENV_COMPONENT_DISPLAY_NAMES_KEY", "sys://components/displaynames");
//    define("IGK_ENV_COMPONENT_REFDIRS_KEY", "sys://components/refdirs");
//    define("IGK_ENV_CALLBACK_KEYS", "sys://env/callbacks");
//    define("IGK_LAST_EVAL_KEY", "sys://lasteval");
//    define("IGK_ENV_WIDGETS_KEY", "sys://widgets");
define("IGK_CACHE_HTML", ".enable-html");
define("IGK_CT_PLAIN_TEXT", "text/plain");
define("IGK_SQL_DEFAULT_DATE_TIME", "0001-01-01 00:00:00");
define("IGK_SQL_DEFAULT_TIME", "00:00:00");
define("IGK_CSS_VAR_COLOR_PREFIX", "--igk-cl-");
define("IGK_CSS_VAR_PROPERTY_PREFIX", "--igk-prop-");
//    define("IGK_CSS_DEFAULT_STYLE_FUNC_KEY", "sys://css/function/defaultStyle");
define("IGK_CSS_MEDIA_TYPE_CLASS", ".igk-media-type:before");
define("IGK_FC_GETVALUE", "getValue");
define("IGK_GLOBAL_EVENT", "@global");
define("IGK_FUNC_KEY", "func");
define("IGK_LANG_FILE_EXTENSION", ".presx");

//+ flags
// define("IGK_VIEW_MODE_FLAG", 0x01);
// define("IGK_FIRSTUSE_FLAG", IGK_VIEW_MODE_FLAG + 2);
// define("IGK_ISINIT_FLAG", IGK_VIEW_MODE_FLAG +3 );
// define("IGK_AUTH_FLAG", IGK_VIEW_MODE_FLAG +4); 
// define("IGK_SESSION_ID", IGK_VIEW_MODE_FLAG + 7); 
// define("IGK_KEY_DOCUMENTS", dechex(IGK_VIEW_MODE_FLAG + 10)); //+ store document index
//+ config flags
// define("IGK_CONFIG_FLAG", 0xA0);
// define("IGK_SUBDOMAIN_CTRL", IGK_CONFIG_FLAG + 3);
// define("IGK_SUBDOMAIN_CTRL_INFO", IGK_CONFIG_FLAG + 4);
// define("IGK_INVOKE_URI_CTRL", IGK_CONFIG_FLAG + 5);
// define("IGK_SERVER_INFO", IGK_CONFIG_FLAG + 6);
// define("IGK_TOOLS_CTRL", IGK_CONFIG_FLAG + 7);

//+node flag
// define("IGK_NODE_FLAG", 0x10);
// define("IGK_ISVISIBLE_FLAG", IGK_NODE_FLAG + 1);
// define("IGK_AUTODINDEX_FLAG", IGK_NODE_FLAG + 2);
// define("IGK_ZINDEX_FLAG", IGK_NODE_FLAG + 3);
// define("IGK_ISLOADING_FLAG", IGK_NODE_FLAG + 4);
// define("IGK_NODETYPENAME_FLAG", IGK_NODE_FLAG + 7);
// define("IGK_NODETAG_FLAG", IGK_NODE_FLAG + 8);
// define("IGK_NODECONTENT_FLAG", IGK_NODE_FLAG + 9);
// define("IGK_SORTREQUIRED_FLAG", IGK_NODE_FLAG + 10); 
// define("IGK_PREVIOUS_CIBLING_FLAG", IGK_NODE_FLAG + 12);
// define("IGK_DESC_FLAG", IGK_NODE_FLAG + 13);
// define("IGK_STYLE_FLAG", IGK_NODE_FLAG + 14);
// define("IGK_ISDISPOSING_FLAG", IGK_NODE_FLAG + 15);
// define("IGK_ATTACHDISPOSE_FLAG", IGK_NODE_FLAG + 16);
// define("IGK_ATTACHCHILD_FLAG", IGK_NODE_FLAG + 17);
// define("IGK_NSFC_FLAG", IGK_NODE_FLAG + 18);
// define("IGK_CALLBACK_FLAG", IGK_NODE_FLAG + 19);
// define("IGK_PARAMS_FLAG", IGK_NODE_FLAG + 20);
// define("IGK_PARENT_FLAG", IGK_NODE_FLAG + 21);
// define("IGK_CHILDS_FLAG", IGK_NODE_FLAG + 22);
// define("IGK_ATTRS_FLAG", IGK_NODE_FLAG + 23);
// define("IGK_DEFINEDNS_FLAG", IGK_NODE_FLAG + 24);
// define("IGK_NODE_CREATE_ARGS_FLAG", IGK_NODE_FLAG + 25);
// define("IGK_DOC_ID_PARAM", IGK_NODE_FLAG + 26);
// define("IGK_COMPONENT_ID_PARAM", IGK_NODE_FLAG + 27);
// define("IGK_KEY_LASTDOC", IGK_NODE_FLAG + 41);
// define("IGK_KEY_SYSDB_CTRL", IGK_NODE_FLAG + 42);
define('IGK_OBJ_TYPE_FD', "T");
// define("IGK_OBJ_TYPE_CLASS", 1);
// define("IGK_OBJ_TYPE_CALLBACK", 2);
// define("IGK_OBJ_TYPE_FUNC", 4);
// define("IGK_OBJ_TYPE_EXPRESSION", 3);
// define("IGK_OBJ_TYPE_NODE", 6);
// define("IGK_OBJ_TYPE_FILE", 7);
define('IGK_LANG_FILE_PREFIX', "lang.");
//+ controller session parameter
//+ controller environment parameter
// define("IGK_CTRL_LANG", 3);
// define("IGK_CTRL_TG_NODE", 240);
define("IGK_COMPONENT_TYPE_FUNCTION", "f");
define("IGK_COMPONENT_TYPE_CLASS", "c");
define("IGK_GET_VALUE_METHOD", "getValue");
//    define("IGK_CTRL_TABLE_INFO_KEY", "sys://ctrl/tabinfokey");
define("IGK_CSS_XSM_SCREEN", 320);
define("IGK_CSS_SM_SCREEN", 710);
define("IGK_CSS_LG_SCREEN", 1024);
define("IGK_CSS_XLG_SCREEN", 1300);
define("IGK_CSS_XXLG_SCREEN", 1600);
define("IGK_CSS_CTN_LG_SIZE", 844);
define("IGK_CSS_CTN_XLG_SIZE", 1280);
define("IGK_CSS_CTN_XXLG_SIZE", 1580);
define("IGK_PWD_LENGTH", 8);
//    define("IGK_AJX_BINDSTYLES", "sys://css/ajx/temp/files");
//    define("IGK_COMPONENT_ID_KEY", "sys://component/id");
define("IGK_ENCODINGTYPE", "text/html; charset=utf-8");
define("IGK_SERVERNAME", "IGKDEV");
define("IGK_STR_EMPTY", "");
define("IGK_MAX_CONFIG_PWD_LENGHT", 5);
defined("IGK_DEFAULT_FOLDER_MASK") || define("IGK_DEFAULT_FOLDER_MASK", '0755');
defined("IGK_DEFAULT_FILE_MASK") || define("IGK_DEFAULT_FILE_MASK", '0775');
define("IGK_LF", "\n");
define("IGK_CLF", "\r\n");
define("IGK_DATA_FOLDER", "Data");
define("IGK_CONF_FOLDER", "Configs");
define("IGK_CONF_DATA", IGK_DATA_FOLDER . "/configs.php");
define("IGK_CHANGE_CONF_DATA", IGK_DATA_FOLDER . "/changes.csv");
define("IGK_UPLOAD_DATA", "Data/upload.csv");
define("IGK_USER_LOGIN", "bondje.doue@igkdev.com");
define("IGK_DB_PREFIX_TABLE_NAME", "IGK_DB_P_TABLE");
define("IGK_CTRL_PARAM_CSS_INIT", "css-init");
define("IGK_DATABINDING_RESPONSE_NAME", "IGK_DATABINDING_RESPONSE_NAME");
define("IGK_ADD_PREFIX", "add");
define("IGK_MENU_CONF_DATA", IGK_DATA_FOLDER . "/menuconf.csv");
define("IGK_MENUS_REGEX", "/menu(?P<name>(.)+)conf.csv/i");
define("IGK_PHP_RESERVEDNAME_REGEX", "/^((a(bstract|nd|rray|s))|(c(a(llable|se|tch)|l(ass|one)|on(st|tinue)))|(d(e(clare|fault)|ie|o))|(e(cho|lse(if)?|mpty|nd(declare|for(each)?|if|switch|while)|val|x(it|tends)))|(f(inal|or(each)?|unction))|(g(lobal|oto))|(i(f|mplements|n(clude(_once)?|st(anceof|eadof)|terface)|sset))|" . "(n(amespace|ew))|(p(r(i(nt|vate)|otected)|ublic))|(re(quire(_once)?|turn))|(s(tatic|witch))|(t(hrow|r(ait|y)))|(u(nset|se))|" . "(__halt_compiler|break|list|(x)?or|var|while))$/i");
define("IGK_IDENTIFIER_RX", "([a-z]|[_]+[a-z0-9])([a-z0-9_]*)");
define("IGK_IDENTIFIER_PATTERN", "[a-z_][a-z0-9_]*");
define("IGK_IDENTIFIER_TAG_CHARS", "_:0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("IGK_XML_IDENTIFIER_RX", "([a-z]+:)*([a-z]|[_]+[a-z0-9])([a-z0-9_-]*)");
define("IGK_ISIDENTIFIER_REGEX", "/^" . IGK_IDENTIFIER_RX . "$/i");
define("IGK_IS_NS_IDENTIFIER_REGEX", "/^((_*[0-9a-z][0-9a-z_]*)\\\\)+(_*[0-9a-z][0-9a-z_]*)$/i");
define("IGK_FQN_NS_RX", "((" . IGK_IDENTIFIER_RX . ")\.)*(" . IGK_IDENTIFIER_RX . ")");
define("IGK_IS_FQN_NS_REGEX", "/^((" . IGK_IDENTIFIER_RX . ")\.)*(" . IGK_IDENTIFIER_RX . ")$/i");
define("IGK_TAGNAME_REGEX", "[0-9a-z\-\:]+");
define("IGK_TAGNAME_CHAR_REGEX", "[0-9a-z\-\:_\.]");
define("IGK_NAME_SPACE_REGEX", "/[a-z_][a-z0-9_\.]+/i");
define("IGK_FORMAT_STR_REGEX", "/\{\\s*(?P<value>([0-9]+|[_a-z]([_a-z0-9]+)?)\\s*([^\}\{]+)?)\}/i");
define("IGK_LOCALIZE_EXPRESSION_REGEX", "/(?P<a>@)?(\'|\")(?P<v>([^'\"]*))\\2(\\s*\|[^\|]\\s*(?P<pipe>([^\|]+\\s*\|?)+))?$/i");
define("IGK_PIPED_EXPRESSION_REGEX", "/(\\s*\|\\s*(?P<pipe>([^\|]+\\s*\|?)+))$/i");
define("IGK_DOMAIN_NAME_REGEX", "/((http(s)?:\/\/)?|(\.)?)?(?P<domain>([^\. \/]+)\.([^\. \/]+))(\/(.)+)?$/i");
define("IGK_DOMAIN_REGEX", "/^((http(s)?:\/\/)|(\.))?(?P<domain>([^ \/]+)\.([^\. \/]+))(\/(.)+)?$/i");
!defined("IGK_EXPRESSION_START_MARKER") && define("IGK_EXPRESSION_START_MARKER", '\{\{');
!defined("IGK_EXPRESSION_END_MARKER") && define("IGK_EXPRESSION_END_MARKER", '\}\}');
!defined("IGK_EXPRESSION_ESCAPE_MARKER") && define("IGK_EXPRESSION_ESCAPE_MARKER", "'");
define("IGK_TEMPLATE_EXPRESSION_REGEX", '/((?P<scope>@*)(?P<escape>[' . IGK_EXPRESSION_ESCAPE_MARKER . '])?' . IGK_EXPRESSION_START_MARKER . '(?P<expression>([^\}\{])+)' . IGK_EXPRESSION_END_MARKER . ')/');
define("IGK_TEMPLATE_GLOBAL_EXPRESSION_REGEX", '/(\[{0,1})(?P<exp>\[\s*(?P<name>\w+)\s*:(?P<value>([^\]])+)\])(\]{0,1})/i');
define("IGK_HOME", "home");
//    define("IGK_ENV_PARAM_KEY", "sys://EnvParam");
//    define("IGK_ENV_PAGEFOLDER_CHANGED_KEY", "sys://current_page_folder_changed");
//    define("IGK_ENV_NO_COOKIE_KEY", "sys://no_cookie");
//    define("IGK_ENV_URI_PATTERN_KEY", "sys://env/systemuri/patterninfo");
//    define("IGK_NS_PARAM_KEY", "sys://html/namespace");
define("IGK_DEFAULT_ARTICLE", "default");
define("IGK_DEFAULT_LANG", "fr");
define("IGK_HTML_CONTENT_TYPE", "Content-Type: text/html; charset=utf-8");
define("IGK_SCHEMA_TAGNAME", "data-schemas");
define("IGK_DATA_DEF_TAGNAME", "DataDefinition");
define("IGK_ENTRIES_TAGNAME", "Entries");
define("IGK_RELATIONS_TAGNAME", "Relations"); 
define("IGK_GEN_COLUMS", "GenColumn"); 
define("IGK_ROW_TAGNAME", "Row");
define("IGK_ROWS_TAGNAME", "Rows");
define("IGK_CNF_TAG", "config");
define("IGK_SCHEMA_FILENAME", "data.schema.xml");
define("IGK_SITEMAP_FUNC", "sitemap");
define("IGK_EVALUATE_URI_FUNC", "evaluateUri");
define("IGK_INITENV_FUNC", "InitEnvironment");
// define("IGK_DATETIME_FORMAT", "Y-d-m_H:i:s");
define("IGK_DATETIME_FORMAT", "Y-m-d_H:i:s");
//    define("IGK_COMPONENT_REG_FUNC_KEY", "sys://components/functions");
//    define("IGK_CURRENT_DOC_PARAM_KEY", "sys://current_document");
define("IGK_COLUMN_TAGNAME", "Column");
define("IGK_HTML_ITEMBASE_CLASS", \IGK\System\Html\Dom\HtmlItemBase::class);
define("IGK_CONFIG_MODE", "Configs");
define("IGK_CONFIG_PAGEFOLDER", "Configs");
define("IGK_CONFIG_ROUTE", "/Configs");
define("IGK_REG_ACTION_METH", "(/:function(/:params+|/)?|/)?");
define("IGK_REG_ACTION_METH_OPTIONS", IGK_REG_ACTION_METH . "(;(:options))?");
define("IGK_REG_ROUTE_PATTERN", "^(/)?(/:function(/|/:params+)?)?((;(:query))+)?");
define("IGK_REFERENCE_FOLDER", __DIR__ . "/Data/References");
define("IGK_FUNC_NODE_PREFIX", "igk_html_node_");
define("IGK_FUNC_NODE_DESC_PREFIX", "igk_html_desc_");
define("IGK_FUNC_DEMO_PREFIX", "igk_html_demo_");
define("IGK_FUNC_DESC_PREFIX", "igk_html_desc_");
define("IGK_FUNC_NODE_EXTENSION_PREFIX", "igk_html_extension_");
define("IGK_FUNC_CALL_IN_CONTEXT", "call_incontext");
define("IGK_HOME_PAGEFOLDER", "home");
define("IGK_HOME_PAGE", "home");
define("IGK_FIELD_PREFIX", "cl");
define("IGK_TABLE_PREFIX", "tb");
define("IGK_BALAFON_CONFIG", "balafon.config.xml");
//+ engine
define("IGK_ENGINE_EXPRESSION_NODE", "igk:expression-node"); // used internally to replace data with expression node
define("IGK_ENGINE_ATTR_EXPRESSION_NODE", "igk:attr-expression");
define("IGK_ENGINE_ATTR_TEMPLATE_CONTENT", "igk:template-content");

define("IGK_APP_FORM_CONTENT", "Application/x-www-form-urlencoded");
define("IGK_JS_VOID", "javascript:void();");
//+ !defined("IGK_DOC_TYPE") && define("IGK_DOC_TYPE", "html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"");
!defined("IGK_DOC_TYPE") && define("IGK_DOC_TYPE", "html");
define("IGK_EVENT_DOC_BEFORE_RENDER", "sys://document/beforerender");
define("IGK_EVENT_DROP_CTRL", "sys://event/dropctrl");
//    define("IGK_DROP_CTRL_EVENT", "sys://ctrl/ondrop");
define("IGK_FORCEVIEW_EVENT", "sys://notify/forceview");
//    define("IGK_COMP_NOT_FOUND_EVENT", "sys://component/notfound");
//    define("IGK_NODE_DISPOSED_EVENT", "sys://node/disposed"); 
//    define("IGK_NAMED_NODE_PARAM", "sys://node/namedchilds");
//    define("IGK_NAMED_ID_PARAM", "sys://node/namedchilds/id");
define("IGK_CTRL_CNF_TITLE", "clTitle");
define("IGK_CTRL_CNF_APPNAME", "clAppName");
define("IGK_CTRL_CNF_BASEURIPATTERN", "clBasicUriPattern");
define("IGK_CTRL_CNF_TABLEPREFIX", "clDataTablePrefix");
define("IGK_CTRL_CNF_APPNOTACTIVE", "clAppNotActive");
//    define("IGK_SESS_UNKCOLOR_KEY", "sys://session/theme/UnknownColor");
define('IGK_HTML_EMAIL_PATTERN', "[0-9a-zA-Z]+(\.[0-9a-zA-Z]+)*@(.)+\.([a-zA-Z]{2,})");
define('IGK_HTML_PHONE_PATTERN', "[0-9]{6,14}");

define("IGK_FD_ID", IGK_FIELD_PREFIX . "Id");
define("IGK_FD_NAME", IGK_FIELD_PREFIX . "Name");
define("IGK_FD_DESC", IGK_FIELD_PREFIX . "Description");
define("IGK_FD_TYPELEN", IGK_FIELD_PREFIX . "TypeLength");
define("IGK_FD_TYPE", IGK_FIELD_PREFIX . "Type");
define("IGK_FD_PASSWORD", IGK_FIELD_PREFIX . "Pwd");
define("IGK_FD_USER_ID", IGK_FIELD_PREFIX . "User_Id");
define("IGK_FD_PRODUCT_ID", IGK_FIELD_PREFIX . "Product_Id");
define("IGK_FD_GROUP_ID", IGK_FIELD_PREFIX . "Group_Id");
define("IGK_FD_AUTH_ID", IGK_FIELD_PREFIX . "Auth_Id");
define("IGK_DEFAULT_DB_PREFIX", "tbigk_");
define("IGK_UINFO_TOKENID", "TOKENID");
define("IGK_CTRLBASECLASS", IGK\Controllers\BaseController::class);
define("IGK_ROOT_CTRLBASECLASS", IGK\Controllers\RootControllerBase::class);
define("IGK_CTRLNONATOMICTYPEBASECLASS", NonAtomicTypeBase::class);
define("IGK_CTRLWEBPAGEBASECLASS", DefaultPageController::class);
define("IGK_CSV_SEPARATOR", "IGK_CSV_SEPARATOR");
define("IGK_CSV_SEPARATORS", ",|.|\t|;");
define("IGK_CSV_FIELD_SEPARATORS", "'|\"");
define("IGK_HTML_WHITESPACE", "&nbsp;");
define("IGK_HTML_CHAR_ZERO", "&#x30;");
define("IGK_HTML_ENCTYPE", "multipart/form-data");
define("IGK_MYSQL_DATAADAPTER", "MYSQL");
define("IGK_CSV_DATAADAPTER", "CSV");
define("IGK_CTRL_CONF_FILE", "config.xml");
define("IGK_CTRL_DBCONF_FILE", "data.xml");
define("IGK_CTRL_BASE", IGK\Controllers\BaseController::class);
define("IGK_HTML_BINDING_EVAL_CONTEXT", "igk:evaluation_context");

 
define("IGK_STYLE_FOLDER", "Styles");
define("IGK_ARTICLES_FOLDER", "Articles");
define("IGK_VIEW_FOLDER", "Views");
define("IGK_CGI_BIN_FOLDER", "cgi-bin");
define("IGK_CONTENT_FOLDER", "Contents");
define("IGK_PAGE_FOLDER", "Pages");
define("IGK_MODS_FOLDER", "Mods");
define("IGK_DIST_FOLDER", "dist");
define("IGK_PROJECTS_FOLDER", "Projects");
define("IGK_PACKAGES_FOLDER", "Packages");
define("IGK_RES_FOLDER", "assets");
define("IGK_LAYOUT_FOLDER", IGK_RES_FOLDER . "/layouts");
define("IGK_INC_FOLDER", "Inc");
define("IGK_LIB_FOLDER", "Lib");
define("IGK_SCRIPT_FOLDER", "Scripts");
define("IGK_PLUGINS_FOLDER", "Plugins");
define("IGK_CLASSES_FOLDER", "Classes");
define("IGK_TESTS_FOLDER", "Tests");
define("IGK_MODULE_FOLDER", "Modules");
define("IGK_RES_FONTS", IGK_RES_FOLDER . "/fonts");
define("IGK_DEFAULT_THEME_FOLDER", IGK_LIB_DIR . "/Default/Themes");
define("IGK_CACHE_FOLDER", ".Caches");
define("IGK_VENDOR_FOLDER", "vendor");
define("IGK_BACKUP_FOLDER", IGK_DATA_FOLDER . "/Backup");
define("IGK_TEMPLATES_FOLDER", IGK_DATA_FOLDER . "/Templates"); 
define("IGK_FILE_CTRL_CACHE", IGK_CACHE_FOLDER . "/.controller.cache");
define("IGK_FILE_PROJECT_CTRL_CACHE", IGK_CACHE_FOLDER . "/.project.cache");
define("IGK_FILE_LIB_CACHE", IGK_CACHE_FOLDER . "/.lib.files.cache");
define("IGK_ADAPTER_CACHE", IGK_CACHE_FOLDER . "/.adapter.cache");
define("IGK_CACHE_DATAFILE", IGK_CACHE_FOLDER . "/.datafile.cache");
define("IGK_FC_CALL_INC", IGK_LIB_DIR . "/" . IGK_INC_FOLDER . "/.igk.fc.call.inc");
define("IGK_PIC_EXTENSIONS", ".png;.jpeg;.jpg;.bmp;.tiff;.gif;.ico;.ani");
define("IGK_PLUGIN_FILE_EXTENSIONS", ".pbal");
define("IGK_PLUGIN_ZP_FILE_EXTENSIONS", ".zpbal");
define("IGK_TEMPLATE_EXTENSIONS", ".template");
define("IGK_ARTICLE_TEMPLATE_REGEX", "/\.(template|html|phtml)$/");
define("IGK_ALLOWED_EXTENSIONS", IGK_PIC_EXTENSIONS . ";.avi;.mov;.flv;");
define("IGK_HTML_SPACE", IGK_HTML_WHITESPACE);
define("IGK_DEFAULT", "default");
define("IGK_DEFAULT_VIEW_EXT", 'phtml');
define("IGK_DEFAULT_STYLE_EXT", 'pcss');
define("IGK_DEFAULT_VIEW", "default");
define("IGK_DEFAULT_VIEW_FILE", IGK_DEFAULT_VIEW . "." . IGK_DEFAULT_VIEW_EXT);
define("IGK_INC_VIEWS_FUNC", IGK_LIB_DIR . "/Inc/igk_views_func.pinc");
define("IGK_HTML_CLASS_NODE_FORMAT", "IGKHtml{0}Item");
define("IGK_HTML_NODE_REGEX", "/^IGKHtml(?<name>(.)+)Item$/i");


/**
 * configuration controller 
 */
define('IGK_SYS_PAGE_CTRL', "c_syspc");
define('IGK_HUMAN_CTRL', "c_sys_hc");
define('SYS_CTRL_BASE', 0x0F);
define('IGK_SYS_API_CTRL', "c_api");
define('IGK_INFOS_CTRL', "c_info");
define('IGK_CHANGE_MAN_CTRL', "c_chm");

define('IGK_ERROR_CTRL', "c_er");
define('IGK_THEME_CTRL', "c_th");
define('IGK_FILE_MAN_CTRL', "c_fm");
define('IGK_PALETTE_CTRL', "{b1ce0523-aa7f-fbc8-ad7c-a6fc391338e9}");
define('IGK_USERVARS_CTRL', "c_uv");
define('IGK_DATA_TYPE_CTRL', "c_dtp");
define('IGK_LANG_CTRL', "c_l");
define('IGK_LOG_CTRL', "c_lo");
define('IGK_CUSTOM_CTRL_MAN_CTRL', "c_cu");
define('IGK_LAYOUT_CTRL', "c_ly");
define('IGK_REFERENCE_CTRL', "c_rf");
define('IGK_SCRIPT_CTRL', "c_scpt");
define('IGK_UCB_REF_CTRL', "c_ucbref");
define('IGK_SHARED_CONTENT_CTRL', "c_shc");
define('IGK_DOC_CTRL', "c_docs");
define('IGK_BDCONFIGS_CTRL', "c_configs");
define('IGK_FILEMAN_CTRL', "c_fman");
define('IGK_PAGEMAN_CTRL', "c_pgman");
define('IGK_FRAME_CTRL', "{a4131596-fa31-499e-758d-bdf45da0f918}");
define('IGK_PIC_RES_CTRL', "{0ff2ac71-ef43-86bc-c73a-39c9f76b19c9}");
define('IGK_USER_GROUPS_CTRL', "{7d545f74-a157-106e-aa29-df64c4346b96}");
define('IGK_SYS_CTRL', "{0b3a8f0c-9030-fb31-e150-5f1f2e224a39}");
define('MODULE_CNF_CTRL', "{14166020-7B69-1DDD-CC2A-6E701570D1B5}");
define('IGK_SYSDB_CTRL', "{f9c1857e-eef2-f762-e56d-be7be1a58b4f}" );
define('IGK_MENU_CTRL', "{dc5acdce-638f-5004-00cf-e4344277689a}");
define('IGK_CONF_CTRL', "{a4918130-ce95-8e6b-c4a0-7b906dcf8c51}");
define('IGK_NOTIFICATION_CTRL', "{74c38caa-5a96-b14f-a627-9972c4273741}");
define('IGK_CTRL_MANAGER', "{de8aac7f-cec1-821e-5ed2-e0dc4f38cada}");
define('IGK_CTRL_TOOLS', "{912679e9-ad55-936d-27ee-73a76e54f49b}");
define('IGK_CTRL_SESSION_MANAGER', "{5d1f178b-7734-102c-e649-4dc74edcf296}");
define('IGK_CA_CTRL', "{a9664083-ae06-9b92-1e8b-65b1b2b46d9a}");
define('IGK_SYSACTION_CTRL', "{ebae0f37-f193-5ecf-3300-81e7a6cddf69}");
define('IGK_COMPONENT_MANAGER_CTRL', "{1dc02db3-b214-2bd7-8c56-8e6149617328}");
define('IGK_DEBUG_CTRL', "{826befbf-9dd7-edf2-c70e-79c07f14803a}");
define('IGK_CB_REF_CTRL', "{cf13442f-c787-3a9f-f184-d424beb334f2}");
define('IGK_LANGUAGE_CTRL', "{6ee01cf0-ee68-428e-cb01-2576862a40a6}");
define('IGK_SESSION_CTRL', "{78c6c622-f002-cb20-f30f-db9c31edf853}"); 
define('IGK_TOOLS_CTRL', '{45adbc77-bd7b-a3b6-6db3-34a0624f117d}');
define('IGK_USER_CTRL', "{c6d43450-9e1c-ae9e-432d-f99557a004c7}");
define('IGK_MYSQL_DB_CTRL', "{8c8d510a-63d4-5bd9-b5d0-413d5b7dc0bb}");
define('IGK_SUBDOMAINNAME_CTRL', "{7228a553-6d08-cf77-e4a7-d4a966901b44}");
define('IGK_CACHE_CTRL', "{ede0378f-a1c5-571e-aa66-a2a47cfc3a40}");
define('IGK_AUTH_CTRL', "{e6416c6b-ba4a-32dc-8de8-8fdbea042593}");
define('IGK_MAIL_CTRL', "{de18f39a-4474-a8c1-76f6-673d8858468b}");
define('IGK_COMPOSER_CTRL', "{f50822d4-a0db-4bea-0a09-c254bfe4aedf}");
define('IGK_USERGROUP_CTRL', "{f73dbe59-2357-de3c-b19f-a2cdefbb194d}");

// defi'e'"IGK_OTHER_MENU_CTRL", SYS_CTRL_BASE + 1);
// defi'e'"IGK_MSBOX_CTRL", SYS_CTRL_BASE + 2);
// defi'e'"IGK_DATA_ADAPTER_CTRL", SYS_CTRL_BASE + 3);
// defi'e'"IGK_CTRL_IDENTIFIER", 0xB1);
define('IGK_AJX_METHOD_SUFFIX', "_ajx");
define('IGK_CONFIRM_TITLE', "title.confirm");
define('IGK_PAGE_TITLE', "title.default.webpage");
define('IGK_MSG_DELETEALLFONT_QUESTION', "Msg.DeleteAllFontQuestion");
define('IGK_MSG_DELETEFILE_QUESTION', "Msg.DELETEFILEQUESTION");
define('IGK_MSG_DELETEDIR_QUESTION', "Msg.DELELETEDIRQUESTION");
define('IGK_MSG_DELETECTRL_QUESTION', "Msg.DELETECTRLQUESTION");
define('IGK_MSG_ALLPICS_QUESTION', "Msg.DELETEALLPICSQUESTION");
define('IGK_MSG_DELETEMENU_QUESTION', "Msg.DELETEALLMENUQUESTION");
define('IGK_DELETEALLTABLE_QUESTION', "Msg.DELETEALLTABLEQUESTION");
define('IGK_MSG_DELETEALLDATABASEBACKUP_QUESTION', "Msg.DELETEALLDATABASEBACKUPQUESTION");
define('IGK_MSG_DELETESINGLETABLE_QUESTION', "Msg.DELETESINGLETABLEQUESTION_1");
define('IGK_MSG_DELETEABACKUPFILE_QUESTION', "Msg.DELETEABACKUPFILEQUESTION");
define('IGK_MSG_RESTOREBACKUPFILE_QUESTION', "Msg.RESTOREBACKUPFILEQUESTION");
define('IGK_MSG_DROPALL_QUESTION', "Msg.DROPALLQUESTION");
define('IGK_CONF_PAGE_TITLE', "title.CONFIGPAGE");
define('IGK_ALL_LANG', "all-lang");
define('IGK_ENTRY_FILES', "index.php,index.phtml,index.ptml,main.php");
define('IGK_TB_GROUPS', "%prefix%groups");
define('IGK_TB_REF_MODELS', "%prefix%reference_models");
define('IGK_TB_USERS', "%prefix%users");
define('IGK_TB_INFOS', "%prefix%infos");
define('IGK_TB_USER_REF_MODELS', "%prefix%users_reference_models");
define('IGK_TB_USER_INFOS', "%prefix%user_infos");
define('IGK_TB_USER_INFO_TYPES', "%prefix%user_info_types");
define('IGK_TB_WHO_USES', "%prefix%who_uses");
define('IGK_TB_AUTHORISATIONS', "%prefix%authorizations");
define('IGK_TB_SUBDOMAINS', "%prefix%subdomains");
define('IGK_TB_USERGROUPS', "%prefix%usergroups");
define('IGK_TB_GROUPAUTHS', "%prefix%groupauthorizations");
define('IGK_TB_GUIDS', "%prefix%guids");
define('IGK_TB_DATATYPES', "%prefix%data_types");
define('IGK_TB_HUMAN', "%prefix%humans");
define('IGK_TB_CONFIGS', "%prefix%configurations");
define('IGK_TB_COMMUNITY', "%prefix%community");
define('IGK_TB_SYSTEMURI', "%prefix%systemuri");
define('IGK_TB_COOKIESTORE', "%prefix%cookie_storages");
define('IGK_TB_MIGRATIONS', "%prefix%migrations");
define('IGK_TB_TEMPLATES', "%prefix%templates");
define('IGK_START_COMMENT', "/*");
define('IGK_END_COMMENT', "*/");
define('IGK_IPV4_REGEX', "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(:[0-9]+)?$/i");
define('IGK_CSS_TREAT_REGEX', '/\[\s*(?P<name>[\w\-]+)\s*:\s*(?P<value>([a-zA-Z0-9_,\/\\\.\- \(\)%:\#\!]+|[^\]]+))\s*\](?P<stop>(\s*(,|;)))?/i');
define('IGK_CSS_TREAT_REGEX_2', '/\{\s*(?P<name>(sys)\s*:\s*[\w:;\-_,\!\s%]+)\s*\}\s*(;)*/i');
// define("IGK_CSS_CHILD_EXPRESSION_REGEX", "/\s*\((sys)?:(?P<name>([a-z0-9_\-\.]+))\)\s*/i");
//add ; on selection 
define('IGK_CSS_CHILD_EXPRESSION_REGEX', "/\s*\(\s*((?P<type>(sys|th))?(\.(?P<def>[^:]+))?)?:(?P<name>([a-z0-9_\-\.]+))\)\s*;?/i");
define('IGK_SUBDOMAIN_URI_NAME_REGEX', "/^(?P<name>[\.\-_0-9a-z]+)\.([^\.]+)\.([^\.]+)$/i");
define('IGK_ALL_REGEX', "/(.)*/i");
define('IGK_VIEW_FILE_EXT', '.phtml');
define('IGK_VIEW_FILE_EXT_REGEX', "phtml|bvhtml");
define('IGK_VIEW_FILE_END_REGEX', "/(.)+(\.(" . IGK_VIEW_FILE_EXT_REGEX . "))?$/i");
define('IGK_APP_LOGO', "/" . IGK_RES_FOLDER . "/Img/app_logo.png");

// define("IGK_PAGE_CONF_CTRL", 10);

// if(!function_exists("igk_define_error")){
// function igk_define_error($msg, $code, $msg_key=null){
// igk_error_def_error($msg, $code, $msg_key);
// }
// }
// igk_define_error("IGK_ERR_USERERROR", 10400);
// igk_define_error("IGK_ERR_NOUSERFOUND", igk_geterror_code(IGK_ERR_USERERROR) + 1, "ERR.NoUserFOUND");
// igk_define_error("IGK_ERR_PAGENULLOREMPTY", igk_geterror_code(IGK_ERR_USERERROR) + 0x0002, "ERR.PAGENULLOREMPTY");
// igk_define_error("IGK_ERR_LOGORPWDNOTVALID", igk_geterror_code(IGK_ERR_USERERROR) + 0x0003, "ERR.LoginOrPWDNotValid");
// igk_define_error("IGK_ERR_NOT_FROM_LOCAL", igk_geterror_code(IGK_ERR_USERERROR) + 0x0100, "ERR.REQUESTFROMNONLOCAL");
// igk_define_error("IGK_ERR_REQUEST_NOT_FROM_BALAFON_SERVER", igk_geterror_code(IGK_ERR_USERERROR) + 0x0101, "ERR.REQUESTFROMABALAFONSERVER");
// igk_define_error("IGK_ERR_PERMISSION", 2100, "e.youdonthavecorrectperm");
// igk_define_error("IGK_ERR_FUNCNOTAVAILABLE", igk_geterror_code(IGK_ERR_PERMISSION) + 1, "e.funcnotavailable");
// igk_define_error("IGK_ERR_FILE_NOT_SUPPORTED", 10080);
// igk_define_error("IGK_ERR_SCRIPT_ERROR", 110100);
define('IGK_ERR_CTRL_', 65536);
define('IGK_ERR_NO_PAGEVIEW', 65546);
define('IGK_ERR_FUNCNOTAVAILABLE', 65547);

// igk_set_error_msg(array("en"=>array(IGK_ERR_CTRL_=>"Controller error")));
// igk_set_error_msg(array("en"=>array(IGK_ERR_NO_PAGEVIEW=>"No pageview defined for {0}. your class probably doesn't call the base construct")));


//    define("IGK_DOC_ERROR_ID", "sys://document/ids/error");
//    define("IGK_DOC_CONF_ID", "sys://document/ids/config");


//+ system user login
!defined("IGK_SYS_USER_LOGIN") &&  define("IGK_SYS_USER_LOGIN", "igk.system@igkdev.com");

//
define("IGK_ENV_GLOBAL_SCRIPT_KEY", "sys://globalscript");

// $tab = get_defined_constants();
// ksort($tab);
// $flag = 0;
// $c = 0;
// $excludes = [
// ,'IGK_LOCAL_DEBUGGING'
// ,'IGK_NODESTROY_ON_FATAL'
// ,'IGK_NO_TRACELOG'
// ,'IGK_SYS_CONFIG_FILE'
// ,'IGK_TRACE' 
// 'IGK_WEBFRAMEWORK', 
// 'IGK_DEFAULT_FILE_MASK'];

// foreach($tab as $k=>$v){
// if (in_array($k, $excludes))
// continue;
// if (is_numeric($v) && (strpos( $k, "IGK_")===0) && (strpos( $k, "IGK_ERR") === false) && (strpos( $k, "IGK_CSS") === false)){
// $c++;
// echo "define('".$k."', ".$c.");<br />\n";
// }
// }

defined('IGK_PWD_PREFIX') || define("IGK_PWD_PREFIX", "(!)8Zmb90-&");
define('IGK_LIB_CLASSES_DIR', IGK_LIB_DIR . "/" . IGK_LIB_FOLDER . "/" . IGK_CLASSES_FOLDER);
defined('IGK_CONF_DEF_PWD') || define('IGK_CONF_DEF_PWD', "admin123");
