<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DashboardBase.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\WinUI;

use IGK\System\Html\Dom\HtmlNode;

/**
 * 
 * @package IGK\System\WinUI
 */
class DashboardBase extends HtmlNode{
    private static $sm_register = [];
    public function getCanAddChilds(){ 
        return false;
    }
    public static function Register($name, $class){
        self::$sm_register[$name] = $class;
    } 
    public static function UnRegister($name){
        unset(self::$sm_register[$name]);
    }  
    public static function RegisterList(){
        return self::$sm_register;
    }

    public static function Create(string $name){
        $cl = null;
        if (isset(self::$sm_register[$name])){
            $cl = self::$sm_register[$name];
        } else {
            // auto determine
           $cl = implode("\\", array_filter([igk_get_class_namespace(static::class), ucfirst($name)."Dashboard"]));
        } 
        if (($cl === null) || !class_exists($cl)){
            return null;
        }

        return new $cl();
    }
    /**
     * init parametere
     * @param array $params 
     * @return void 
     */
    public function initParam(array $params){

    }
}
