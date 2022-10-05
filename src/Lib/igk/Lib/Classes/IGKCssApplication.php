<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCssApplication.php
// @date: 20220803 13:48:54
// @desc: 


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
    public function getNoEnvironment(){
        return true;
    }
    public function bootstrap() { 
        // + | activate the session
        // $this->library("session");
        $this->library("mysql");         
        Benchmark::$Enabled = false;
        require_once IGK_LIB_CLASSES_DIR . "/Css/CssContext.php";
        // IGKAppSystem::LoadEnvironment(igk_app()); 
    }

    /**
     * run css application
     * @param string $entryfile 
     * @param int $render 
     * @return mixed 
     */
    public function run(string $entryfile, $render = 1) {      
        igk_setting()->no_init_controller = file_exists(SystemUriActionController::GetCacheFile()); 
    }
    
}