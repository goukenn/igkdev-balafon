<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppCommandConstant.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console;

abstract class AppCommandConstant{
    /**
     * get cache file 
     * @return string 
     */
    public static function GetCacheFile():string{
        $fn = ".command.list.pinc";
        if ($cachedir = igk_io_cachedir()){
            return $cachedir."/".$fn; 
        }
        return IGK_LIB_DIR."/.Caches/".$fn;
    }
}