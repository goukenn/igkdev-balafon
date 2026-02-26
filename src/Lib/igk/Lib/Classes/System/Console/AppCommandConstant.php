<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppCommandConstant.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console;

/**
* auto generate doc.
* @package IGK\System\Console
*/
abstract class AppCommandConstant{

    /**
    * auto generate doc.
    * @var mixed
    */
    const COMMAND_LIST= '.command.list.pinc';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ENV = '.balafon';
    /**
     * get cache file 
     * @return string 
     */

    public static function GetCacheFile():string{
        return App::GetAppBasePath()."/".self::ENV."/".self::COMMAND_LIST;  
    }
}