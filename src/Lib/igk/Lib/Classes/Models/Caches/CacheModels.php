<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CacheModels.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\Models\Caches;

/**
 * chache model result
 * @package IGK\Models\Caches
 */
class CacheModels{
    static $sm_caches;
    public static function & _GetCaches(){
        if (self::$sm_caches == null){
            self::$sm_caches = [];
        }
        return self::$sm_caches;
    }
    public static function Get(string $key){
        $_v = self::_GetCaches();
        return igk_getv($_v, $key);
    }
    /**
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     */
    public static function Register($key, $value){
        $_v = & self::_GetCaches();
        if ($value===null){
            unset($_v[$key]);
        }
        $_v[$key] = $value;
    }
    /**
     * ClearModel Cache
     */
    public static function Clear(){
        self::$sm_caches = [];
    }
}