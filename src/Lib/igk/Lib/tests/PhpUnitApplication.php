<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PhpUnitApplication.php
// @date: 20220620 13:53:43
// @desc: Entry unit test application

/**
 * represent the php unit test application
 * @package 
 */
class PhpUnitApplication extends IGKApplicationBase{
    public function bootstrap() { 
        $this->library("mysql");
        $this->library("zip");
        // init server definition
        igk_server()->REQUEST_URI = "/";
    }
    public function run(string $entryfile, $render = 1) { 
        IGKApp::StartEngine($this);
        $p = igk_sys_project_controllers();        
        if ($p){
            foreach($p as $m){
                $m::register_autoload();  
            } 
        }
        if ($tmodule = igk_getv($_ENV,'IGK_TEST_MODULE')){
            $tmodule = igk_require_module($tmodule);
        } 
        if ($m = igk_getv($_ENV,'IGK_TEST_CONTROLER')){
            $m = igk_getctrl($m);
            $m::register_autoload();
        } 
    }

}