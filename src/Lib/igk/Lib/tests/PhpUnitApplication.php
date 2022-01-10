<?php

/**
 * represent the php unit test application
 * @package 
 */

class PhpUnitApplication extends IGKApplicationBase{
    public function bootstrap() { 
        $this->library("mysql");
        $this->library("zip");
    }
    public function run(string $entryfile, $render = 1) { 
        IGKApp::StartEngine($this);
        $p = igk_sys_project_controllers();        
        if ($p){
            foreach($p as $m){
                $m::register_autoload();  
            } 
        }
    }

}