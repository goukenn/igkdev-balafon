<?php
// @author: C.A.D. BONDJE DOUE
// @filename: LaravelMix.php
// @date: 20220414 13:46:56
// @desc: laravel mix instataller
namespace IGK\System\Shell;
/**
* auto generate doc.
* @package IGK\System\Shell
*/
class OsWindowCommand extends OsShell{
    /**
    * Where.
    * @param mixed $cmd
    */
    public static function Where($cmd){ 
        return exec("where ".$cmd);
    }
}