<?php

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