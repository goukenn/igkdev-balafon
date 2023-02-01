<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettings.php
// @date: 20230128 20:23:57
namespace IGK\System\Console\Commands\Sync;

use IGK\Helper\Activator;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Sync
*/
class ProjectSettings{
    /**
     * ignore directory list 
     * @var array
     */
    var $ignoredirs = [];
    /**
     * create project setting
     * @param mixed $jsond_data 
     * @return self 
     */
    public static function Load($jsond_data){
        return Activator::CreateNewInstance(self::class, $jsond_data);
    }
}