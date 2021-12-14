<?php
namespace IGK\System\Console;

abstract class AppCommandConstant{
    public static function GetCacheFile(){
        return igk_io_applicationdir()."/Data/Caches/.command.list.php";
    }
}