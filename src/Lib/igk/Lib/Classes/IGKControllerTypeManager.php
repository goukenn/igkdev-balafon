<?php
// @file: IGKControllerTypeManager.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerTypeBase;
use IGK\Helper\IO;

/**
* Igkcontroller type manager.
*/
final class IGKControllerTypeManager{

    /**
    * Property: tab manager.
    * @var mixed
    */
    static $tabManager;

    /**
    * Returns Controller Types.
    */

    public static function GetControllerTypes(){
        if(self::$tabManager == null){
            $tab=array();
            $exp="/^(IGK){0,1}(?P<name>[\w_-]+)(Ctrl|Controller)$/i";
            $cl = ControllerTypeBase::class;
            foreach(get_declared_classes() as $v){
                $n = basename(IO::GetDir($v));              
                if(igk_reflection_class_extends($v, $cl) && igk_reflection_class_isabstract($v) && preg_match($exp, $n)){
                    preg_match_all($exp, $n, $t);
                    $tab[$t["name"][0]]=$v;
                }
            }
            self::$tabManager=$tab;
            return $tab;
        }
        return self::$tabManager;
    }

    /**
    * Returns Custom Config Info.
    * @param BaseController $controller
    */

    public static function GetCustomConfigInfo(BaseController $controller){
        if(method_exists($controller, __FUNCTION__))
            return $controller->GetCustomConfigInfo();
    }
}