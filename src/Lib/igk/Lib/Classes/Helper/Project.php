<?php
// @author: C.A.D. BONDJE DOUE
// @file: Project.php
// @date: 20221119 04:56:32
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\System\Console\Logger;
use IGK\System\Delegates\InvocatorListDelegate;

///<summary></summary>
/**
* project helper
* @package IGK\Helper
*/
class Project{
    /**
     * get default project ignore library
     * @return array 
     */
    public static function IgnoreDefaultDir(){
        return array_fill_keys([
            '.git', '.vscode', 'node_modules', '.DS_Store'
        ],1);
    }
    public static function GetProjectInvocatorInitDbList(BaseController $sysdb)
    {
        $sysdb_adapter = $sysdb->getDataAdapterName();
        $projects = InvocatorListDelegate::Create(
            array_filter(array_map(
                function ($a) use ($sysdb, $sysdb_adapter) {
                    if (($sysdb == $a) || !$a->getCanInitDb() || ($a->getDataAdapterName() != $sysdb_adapter))
                        return null;
                    $a::register_autoload();
                    return $a;
                },
                igk_app()->getControllerManager()->getControllers()
            )),
            function ($b, $func, $arguments) {
                Logger::print("$func : " . get_class($b));
                return call_user_func_array([get_class($b), $func], $arguments);
            }
        );
        return $projects;
    }
}