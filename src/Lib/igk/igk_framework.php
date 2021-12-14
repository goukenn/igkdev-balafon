<?php

defined("IGK_FRAMEWORK") && die("Framework already define");
define("IGK_FRAMEWORK", "IGKDEV-WFM");
define("IGK_LIB_DIR", __DIR__);

require_once IGK_LIB_DIR."/igk_constants.php";
require_once IGK_LIB_DIR."/igk_config.php";
require_once IGK_LIB_DIR."/igk_core.php";
require_once IGK_LIB_DIR."/igk_functions.php";  
require_once IGK_LIB_CLASSES_DIR."/IGKApplicationLoader.php";  
require_once IGK_LIB_CLASSES_DIR."/IGKApplicationBase.php";  
require_once IGK_LIB_CLASSES_DIR."/IGKApplication.php";  
 