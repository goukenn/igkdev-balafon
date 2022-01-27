<?php
namespace IGK\Controllers;

use IGKApplicationLoader;

class ServiceController extends NonVisibleControllerBase{
    public static function register_autoload()
    {
        $services = igk_app()->session->getServices();
        if ($services){
            foreach($services as $k=>$v){
                if (file_exists($fc = igk_io_expand_path($v))){
                    require_once $fc;
                }
            }
        }
    }
    public static function register($classname, $file){
        $g = igk_app()->session->getServices();
        if ($g === null)
            $g = [];
        $g[$classname] = $file;
        igk_app()->session->setServices($g);
    }
    /**
     * clear services
     * @return void 
     */
    public static function clear(){
        igk_app()->session->setServices(null);
    }
}