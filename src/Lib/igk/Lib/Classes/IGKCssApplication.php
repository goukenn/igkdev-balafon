<?php

use IGK\Cache\SystemFileCache;
use IGK\System\Configuration\Controllers\SystemUriActionController;
use IGK\System\Diagnostics\Benchmark;
 

require_once IGK_LIB_CLASSES_DIR ."/IGKCaches.php";
/**
 * to initialize css application 
 * @package 
 */
class IGKCssApplication extends IGKApplicationBase
{
    /**
     * disable environment loading
     * @return true 
     */
    public function getNoEnviroment(){
        return true;
    }
    public function bootstrap() { 
        // + | activate the session
        $this->library("session");
        $this->library("mysql");
        igk_setting()->no_init_controller = file_exists(SystemUriActionController::GetCacheFile());
 
        Benchmark::$Enabled = false;
        require_once IGK_LIB_CLASSES_DIR . "/Css/IGKCssContext.php";
        IGKAppSystem::LoadEnvironment(); 
    }

    public function run(string $entryfile, $render = 1) { 
        // + | run application
    }
    
}