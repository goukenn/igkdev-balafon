<?php
// + | --------------------------------------------------------------------
// + | INIT - LOADING ENVIRONMENT FILES
// + |

if (!version_compare(PHP_VERSION, "7.3", ">=")) {
    die("mandory version required. 7.3<=");
}
require_once(__DIR__ . "/igk_framework.php");
require_once(IGK_LIB_CLASSES_DIR . "/interfaces.php");
require_once(IGK_LIB_CLASSES_DIR . "/IGKObject.php");
require_once(IGK_LIB_CLASSES_DIR . "/IGKApplicationBase.php");
require_once(IGK_LIB_CLASSES_DIR . "/Resources/R.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Console/ICLICommandApp.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Console/App.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Console/BalafonApplication.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Console/Logger.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Console/ConsoleLogger.php");
require_once(IGK_LIB_CLASSES_DIR . "/ApplicationFactory.php");
require_once(IGK_LIB_CLASSES_DIR . "/IGKEnvironmentConstants.php");
require_once(IGK_LIB_CLASSES_DIR . "/IGKEnvironment.php");
require_once(IGK_LIB_CLASSES_DIR . "/Server.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Console/AppConfigs.php");
require_once(IGK_LIB_CLASSES_DIR . "/ApplicationLoader.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/conf.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/environment.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/engine.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/reflection.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/debug.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/sys.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/io.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/string.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/db.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/regex.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/docs.php");
require_once(IGK_LIB_DIR . "/igk_functions.php");
require_once(IGK_LIB_DIR . "/Lib/functions-helpers/balafon-cli.php");