<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_constants.php
// @date: 20220803 13:34:31
// @desc: core constant definition

// + | store here 

//----------------------------------------------------------------------------------------
// + | -- define core constant 
//----------------------------------------------------------------------------------------
define("IGK_BALAFON_JS_VERSION", "4.6.0.0408");
defined("IGK_FRAMEWORK") || define("IGK_FRAMEWORK", "IGKDEV-WFM");
!defined("IGK_WEBFRAMEWORK") && define("IGK_WEBFRAMEWORK", "18.0"); 
!defined("IGK_VERSION") && define("IGK_VERSION", IGK_WEBFRAMEWORK.".0.0118");
define("IGK_AUTHOR", "C.A.D. BONDJE DOUE");
define("IGK_AUTHOR_CONTACT", "bondje.doue@igkdev.com");
define("IGK_AUTHOR_2", "R. TCHATCHO");
define("IGK_AUTHOR_CONTACT_2", "gerald.romeo@tbnsolaris.com");
define("IGK_AUTHORS", "C.A.D. BONDJE DOUE & R. TCHATCHO");
define("IGK_PLATEFORM_NAME", "BALAFON");
define("IGK_RELEASE_DATE", "30/12/2023");
define("IGK_START_DATE", "01/01/2013");
defined("IGK_COPYRIGHT") || define("IGK_COPYRIGHT", "IGKDEV &copy; 2011-".date('Y')." all rights reserved");
define("IGK_WEB_SITE", "https://www.igkdev.com");
define("IGK_SCHEMA_NS", "https://schemas.igkdev.com/balafon");
define("IGK_DOMAIN", "igkdev.com"); 
define("IGK_COMPANY", "igkdev");
define("IGK_CODE_NAME", "BALAFON");
define("IGK_BALAFON_JS", "balafonjs");
define("IGK_BALAFON_JS_CORE_FILE", IGK_LIB_DIR."/Scripts/igk.js");
define("IGK_LIB_CGI_BIN_DIR", IGK_LIB_DIR."/cgi-bin");
define("IGK_LIB_BIN", IGK_LIB_DIR."/bin/balafon");
define("IGK_LIB_RUNFILE", IGK_LIB_DIR."/igk_run_script.php");
define("IGK_LIB_MODS_DIR", IGK_LIB_DIR."/Modules");
define("IGK_GIT_URL", "https://github.com/goukenn/igkdev-balafon");
define("IGK_GIT_DRSSTUDIO_URL", "https://github.com/goukenn/DrSStudio.git");
define('IGK_PHP_MIN_VERSION', "7.3.0");
define('IGK_FORM_FIELD_BLOCK_TAG_NAME','div');
 
define('IGK_SERVICE_PREFIX_PATH', 'sys://services/');

define('IGK_AUTHOR_COMMUNITY_INFO',[
    "mail" => "mailto:" . IGK_AUTHOR_CONTACT,
    "linkedin" => "https://www.linkedin.com/in/igkdevbondjedoue/",
    "twitter" => "https://twitter.com/@IGKDEV",
    "facebook" => "https://facebook.com/IGKDEV",
    "github" => "https://github.com/goukenn",
    "youtube" => "https://www.youtube.com/channel/UC5qYUsVFf3Mbc8giPY5V-4A",
]);