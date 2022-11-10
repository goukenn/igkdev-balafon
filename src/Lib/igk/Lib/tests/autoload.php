<?php
// @author: C.A.D. BONDJE DOUE
// @filename: autoload.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\ApplicationFactory;
use IGK\ApplicationLoader;

if (!version_compare(PHP_VERSION, "7.2", ">")){
    die ("php require version must be greather that 7.2");
}
if (defined("IGK_TEST_INIT")){
    return;
}
if (!defined("PHPUNIT_COMPOSER_INSTALL")){
    die("phpunit not installed");
}
define("IGK_TEST_INIT", __FILE__);
if (!defined("IGK_LIB_DIR")){
    require_once(__DIR__."/../../igk_framework.php");
} 

// + | ----------------------------------------------------------------------
// + | test file auto register
// + |
spl_autoload_register(ApplicationLoader::TestClassesLoaderCallback());

// + | ---------------------------------------------------------------------------------------------------
// + | initilize constants
foreach(["IGK_APP_DIR", "IGK_SESS_DIR", "IGK_BASE_DIR", "IGK_TEST_MODULE", "IGK_TEST_CONTROLER"] as $k){
    if (!defined($k)){
        if (($appdir = igk_getv($_ENV, $k)) && is_dir($appdir)){
            define($k, realpath($appdir));   
        }
    }
}  
// + | ---------------------------------------------------------------------------------------------------
// + | init server test setting
$_SERVER["DOCUMENT_ROOT"] = IGK_BASE_DIR;
$_SERVER["SERVER_NAME"] = "local.test.com";
$_SERVER["SERVER_PORT"] = "8801"; 
$_SERVER["HTTP_USER_AGENT"] = "local.test.agent";
unset($_SERVER["REQUEST_URI"]);

// + | ---------------------------------------------------------------------------------------------------
// + | ensure constant from $_ENV
foreach(["IGK_NO_DBCACHE"] as $k){
    if (!defined($k) && ($t = igk_getv($_ENV, $k))){
        define($k, $t);                
    }
}
unset($k,$t, $appdir);
defined("IGK_PROJECT_DIR") || define("IGK_PROJECT_DIR", IGK_APP_DIR."/Projects");           
 
require_once(__DIR__."/PhpUnitApplication.php");
require_once(IGK_LIB_CLASSES_DIR."/ApplicationFactory.php");
require_once(IGK_LIB_CLASSES_DIR."/IGKEnvironment.php");
// load configuration file for unit testing
igk_environment()->setArray("extra_config", "configFiles", ["unittest"]);

// + | ---------------------------------------------------------------------------------------------------
// + | register phpunit application
ApplicationFactory::Register("phpunit", PhpUnitApplication::class);

// + | ---------------------------------------------------------------------------------------------------
// + | run phpunit app
ApplicationLoader::Boot("phpunit")->run(__FILE__, false);