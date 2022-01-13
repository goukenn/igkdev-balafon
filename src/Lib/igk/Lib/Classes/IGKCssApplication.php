<?php
 
use IGK\System\Diagnostics\Benchmark;
 

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

        Benchmark::$Enabled = false;
    }

    public function run(string $entryfile, $render = 1) { 
        // + | run application
    }
    
}