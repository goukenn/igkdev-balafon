<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_framework.php
// @date: 01/01/2013
// @desc: core lib entry file 


defined("IGK_FRAMEWORK") && die("Framework already define");
define("IGK_FRAMEWORK", "IGKDEV-WFM");
define("IGK_LIB_DIR", str_replace("\\", "/", __DIR__));
define("IGK_LIB_FILE", __FILE__);

if (file_exists(IGK_LIB_DIR . "/igk_version.php")) {
    define("IGK_VERSION", file_get_contents(IGK_LIB_DIR . "/igk_version.php"));
}

define("IGK_CORE_ENTRY_NS", "IGK");

require_once IGK_LIB_DIR . "/igk_constants.php";
require_once IGK_LIB_DIR . "/igk_config.php";
require_once IGK_LIB_DIR . "/igk_core.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/modules.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/conf.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/environment.php";
// environment management
require_once IGK_LIB_CLASSES_DIR . "/IGKEnvironmentConstants.php";
require_once IGK_LIB_CLASSES_DIR . "/IGKEnvironment.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Http/StatusCode.php";
require_once IGK_LIB_CLASSES_DIR . "/System/IToArray.php";
require_once IGK_LIB_CLASSES_DIR . "/Server.php";
require_once IGK_LIB_CLASSES_DIR . "/Helper/StringUtility.php";  
require_once IGK_LIB_CLASSES_DIR . "/System/Facades/Facade.php";

// $uri_handler = \IGK\System\Facades\Facade::GetFacade(\IGK\System\Http\UriHandler::class);
// isset($_SERVER["REQUEST_URI"]) && $uri_handler && $uri_handler::Handle($_SERVER["REQUEST_URI"]);

require_once IGK_LIB_DIR . "/Lib/functions-helpers/array.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/engine.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/reflection.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/debug.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/sys.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/io.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/css.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/module.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/assets.php";
require_once IGK_LIB_DIR . "/Lib/functions-helpers/php.php";  
require_once IGK_LIB_DIR . "/Lib/functions-helpers/string.php";  
require_once(IGK_LIB_DIR."/Lib/functions-helpers/docs.php"); 
require_once IGK_LIB_DIR . "/igk_functions.php";

//----------------------------------------------------------------------------------
// controller requirement
//----------------------------------------------------------------------------------
require_once IGK_LIB_CLASSES_DIR . '/interfaces.php';
require_once IGK_LIB_CLASSES_DIR . '/IGKObject.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/ILibaryController.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/ControllerUriTrait.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/RootControllerBase.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/BaseController.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/ControllerTypeBase.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/PageControllerBase.php';
require_once IGK_LIB_CLASSES_DIR . '/Controllers/ApplicationController.php';


// require_once '../application/Lib/igk/Lib/Classes/IGKSession.php';
// application requirement
require_once IGK_LIB_CLASSES_DIR . '/ApplicationLoader.php';
require_once IGK_LIB_CLASSES_DIR . '/IGKApplication.php';
require_once IGK_LIB_CLASSES_DIR . '/IGKApp.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Applications/HookRegister.php';

\IGK\System\Applications\HookRegister::Init();
