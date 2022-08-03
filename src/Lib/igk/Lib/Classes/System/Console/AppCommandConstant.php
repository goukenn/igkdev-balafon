<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppCommandConstant.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console;

abstract class AppCommandConstant{
    public static function GetCacheFile(){
        return igk_io_applicationdir()."/Data/Caches/.command.list.php";
    }
}