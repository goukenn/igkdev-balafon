<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApplicationFactory.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK;

/**
 * application factory to create and register application base
 * @package IGK
 */
abstract class ApplicationFactory{
     
    private static $sm_factory = [
        'framework'=>\IGK\System\Framework\Application::class
    ];
    public static function Register($name, $class){
        if (class_exists($class, false) && is_subclass_of($class, \IGKApplicationBase::class)){
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
            $cl = \IGK\System\Applications::class."\\".ucfirst($type)."Application";
            if (!class_exists($cl)){
                return null;
            }
        } 
        return new $cl();
    }
}