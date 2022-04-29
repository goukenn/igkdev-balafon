<?php
// @author: C.A.D. BONDJE DOUE
// @filename: LaravelMix.php
// @date: 20220414 13:46:56
// @desc: laravel mix instataller
namespace IGK\System\Installers;
 
class OsUnixCommand extends OsShell{
    public static function Where($cmd){ 
        return exec("which ".$cmd);
    }
}