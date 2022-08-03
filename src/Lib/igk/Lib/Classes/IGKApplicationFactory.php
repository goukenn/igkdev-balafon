<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApplicationFactory.php
// @date: 20220803 13:48:54
// @desc: 

 

abstract class IGKApplicationFactory{
    
    private static $sm_factory = [];

    public static function Register($name, $class){
        if (class_exists($class, false) && is_subclass_of($class, IGKApplicationBase::class)){
            self::$sm_factory[$name] = $class;
        }
    }
    /**
     * create Application
     * @param string $type 
     * @return null|object|IIGKApplication 
     */
    public static function Create(string $type){
        if (isset(self::$sm_factory[$type])){
            $cl = self::$sm_factory[$type];
        } else {
            $cl = "IGK".ucfirst($type)."Application";
            if (!class_exists($cl)){
                return null;
            }
        } 
        return new $cl();
    }
}