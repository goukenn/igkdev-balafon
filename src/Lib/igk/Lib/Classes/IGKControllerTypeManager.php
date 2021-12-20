<?php
// @file: IGKControllerTypeManager.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Controllers\BaseController;

final class IGKControllerTypeManager{
    static $tabManager;
    ///<summary></summary>
    public static function GetControllerTypes(){
        if(self::$tabManager == null){
            $tab=array();
            $exp="/^(IGK){0,1}(?P<name>[\w_-]+)(Ctrl|Controller)$/i";
            foreach(get_declared_classes() as $v){
                if(igk_reflection_class_extends($v, "IGKCtrlTypeBase") && igk_reflection_class_isabstract($v) && preg_match($exp, $v)){
                    preg_match_all($exp, $v, $t);
                    $tab[$t["name"][0]]=$v;
                }
            }
            self::$tabManager=$tab;
            return $tab;
        }
        return self::$tabManager;
    }
    ///<summary>Represente GetCustomConfigInfo function</summary>
    ///<param name="controller" type="BaseController"></param>
    public static function GetCustomConfigInfo(BaseController $controller){
        if(method_exists($controller, __FUNCTION__))
            return $controller->GetCustomConfigInfo();
    }
}
