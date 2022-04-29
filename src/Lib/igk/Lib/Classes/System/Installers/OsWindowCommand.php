<?php
// @author: C.A.D. BONDJE DOUE
// @filename: LaravelMix.php
// @date: 20220414 13:46:56
// @desc: laravel mix instataller
namespace IGK\System\Installers;

use IGK\Helper\IO;
use IGK\System\Console\Logger;
use IGKException;

 
class OsWindowCommand extends OsShell{
    public static function Where($cmd){ 
        return exec("where ".$cmd);
    }
}