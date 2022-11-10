<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EnvControllerCacheList.php
// @date: 20221031 10:22:32
// @desc: environment controller cache list
namespace IGK\System\Caches;

/**
 * 
 * @package IGK\System\Caches
 */
class EnvControllerCacheList{
    const FILE = ".env.controller.cache";

    /**
     * get declared class list
     * @return mixed 
     */
    public static function GetControllersClasses(){
        if (is_file($file = igk_io_cachedir() . "/" . self::FILE)){
            $tab = unserialize(file_get_contents($file));
        }else{
            $tab = get_declared_classes();            
        }
        return $tab;
    }
}