<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DashboardBase.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\WinUI;
use IGK\System\Html\Dom\HtmlNode;
/**
* auto generate doc.
* @package IGK\System\WinUI
*/
class DashboardBase extends HtmlNode{
    /**
    * Property: register.
    * @var mixed
    */
    private static $sm_register = [];
    /**
    * Returns Can Add Childs.
    */
    public function getCanAddChilds(){ 
        return false;
    }
    /**
    * Registers.
    * @param mixed $name
    * @param mixed $class
    */
    public static function Register($name, $class){
        self::$sm_register[$name] = $class;
    }
    /**
    * Un register.
    * @param mixed $name
    */
    public static function UnRegister($name){
        unset(self::$sm_register[$name]);
    }
    /**
    * Registers List.
    */
    public static function RegisterList(){
        return self::$sm_register;
    }
    /**
    * Creates.
    * @param string $name
    */
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