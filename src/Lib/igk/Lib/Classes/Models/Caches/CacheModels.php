<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CacheModels.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\Models\Caches;
use IGK\Models\ModelBase;
/**
 * chache model result
 * @package IGK\Models\Caches
 */
class CacheModels
{

    /**
    * Cache: caches.
    * @var mixed
    */
    static $sm_caches;

    /**
    * Get caches.
    */
    public static function &_GetCaches()
    {
        if (self::$sm_caches == null) {
            self::$sm_caches = [];
        }
        return self::$sm_caches;
    }

    /**
    * Returns.
    * @param string $key
    */
    public static function Get(string $key)
    {
        $_v = self::_GetCaches();
        return igk_getv($_v, $key);
    }
    /**
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     */

    public static function Register($key, $value)
    {
        $_v = &self::_GetCaches();
        if ($value === null) {
            unset($_v[$key]);
        }
        $_v[$key] = $value;
    }
    /**
     * ClearModel Cache
     */

    public static function Clear()
    {
        self::$sm_caches = [];
    }
    /**
     * retrieve model cached key
     * @param ModelBase $model 
     * @param mixed $column 
     * @param mixed $value 
     * @return string 
     */

    public static function GetCacheKey(ModelBase $model, $column, $value): string
    {
        return  "cache://" . igk_uri(get_class($model) . "/" . $column . "/" . $value);
    }
}