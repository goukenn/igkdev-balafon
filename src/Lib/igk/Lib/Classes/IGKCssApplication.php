<?php

use IGK\System\Http\RequestHandler;
use IGK\Helper\StringUtility;
use IGK\System\Html\HtmlRenderer;
use IGKCaches;

require_once IGK_LIB_CLASSES_DIR ."/IGKCaches.php";
/**
 * to initialize css application 
 * @package 
 */
class IGKCssApplication extends IGKApplicationBase
{
    public function bootstrap() { 
        // + | activate the session
        $this->library("session");
    }

    public function run(string $entryfile, $render = 1) { 
        // + | run application
    }
    
}