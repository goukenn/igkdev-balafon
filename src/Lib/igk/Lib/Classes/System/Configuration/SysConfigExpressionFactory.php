<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SysConfigExpressionFactory.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Configuration;



/**
 * class factory for configuration
 * @package IGK\System\Configuration
 */
class SysConfigExpressionFactory{    

    const KEY = self::class."/factories";
    /**
     * register prefix
     * @param string $prefix prefix name. not sys|app
     * @param string $class class expression to bind
     * @return void 
     */
    public static function Register(string $prefix, string $class){
        igk_environment()->setArray(self::KEY, $prefix, $class);
    }
    /**
     * unregister prefix
     * @param mixed $prefix 
     * @return void 
     */
    public static function UnRegister($prefix){
        igk_environment()->unsetInArray(self::KEY, $prefix);
    }
    public static function GetRegisterRegex(){
        $s = ["sys","app"];
        if (is_array($t = igk_environment()->get(self::KEY))){
            $s = array_unique(array_merge($s, array_keys($t)));
        }
        return implode("|",$s);
    }
    public static function Create($name, $expression){
        if ($g = igk_environment()->getArray(self::KEY, $name)){
            if (strpos($expression, $name.".") === 0){
                $expression = substr($expression, strlen($name)+1);
            }
            if (class_exists($g) && !empty($expression)){
                return new $g($expression);
            }
        }
    }
}