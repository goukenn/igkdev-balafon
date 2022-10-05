<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ServiceController.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Controllers;

use ApplicationLoader;

/**
 * used to register service
 * @package IGK\Controllers
 */
class ServiceController extends NonVisibleControllerBase{
    public static function register_autoload()
    {
        $services = igk_app()->session->getServices();
        if ($services){
            foreach($services as $v){
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