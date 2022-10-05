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
spl_autoload_register(function($n){    
    $fix_path = function($p, $sep=DIRECTORY_SEPARATOR){
        if ($sep=="/"){
            return str_replace("\\", "/", $p);
        }  
        return str_replace("/", "\\", $p);
    };
    if (strpos($n, $ns= IGK\Tests::class)===0){
        $cl = substr($n, strlen($ns));
        $f = $fix_path(__DIR__.$cl.".php");       
        if (file_exists($f)){
            include($f);
            if (!class_exists($n, false)){
                throw new \Exception("File exists but class not present");
            }
            return 1;
        }
    } 
    return 0;
});

// + | ---------------------------------------------------------------------------------------------------
// + | initilize environment variable 
foreach(["IGK_APP_DIR", "IGK_SESS_DIR", "IGK_BASE_DIR", "IGK_TEST_MODULE", "IGK_TEST_CONTROLER"] as $k){
    if (!defined($k)){
        if (($appdir = igk_getv($_ENV, $k)) && is_dir($appdir)){
            define($k, realpath($appdir));   
        }
    }
}  
$_SERVER["DOCUMENT_ROOT"] = IGK_BASE_DIR;
$_SERVER["SERVER_NAME"] = "local.test.com";
$_SERVER["SERVER_PORT"] = "8801"; 
$_SERVER["HTTP_USER_AGENT"] = "local.test.agent";

foreach(["IGK_NO_DBCACHE"] as $k){
    if (!defined($k) && ($t = igk_getv($_ENV, $k))){
        define($k, $t);                
    }
}
defined("IGK_PROJECT_DIR") || define("IGK_PROJECT_DIR", IGK_APP_DIR."/Projects");           
 
require_once(__DIR__."/PhpUnitApplication.php");
require_once(IGK_LIB_CLASSES_DIR."/ApplicationFactory.php");
require_once(IGK_LIB_CLASSES_DIR."/IGKEnvironment.php");
// load configuration file for unit testing
igk_environment()->setArray("extra_config", "configFiles", ["unittest"]);

ApplicationFactory::Register("phpunit", PhpUnitApplication::class);


// 
// //.session start for testing
// $s = session_start();
// // initialize application static folder
$app = ApplicationLoader::Boot("phpunit"); 

$app->run(__FILE__, false);