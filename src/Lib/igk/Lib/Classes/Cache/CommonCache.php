<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CommonCache.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Cache;

///<summary>common cache abstract class </summary>
/// contain only static method
abstract class CommonCache{
    /**
     * 
     * @return string|null return lib cache file
     */
    public static function CacheFile(){
        return null;
    }
}