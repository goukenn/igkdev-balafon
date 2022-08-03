<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RemoveProjectCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use ClearCacheCommand;
use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;
use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGK\System\Installers\OsShell;
use IGKException;
use IGKModuleListMigration;

/**
 * remove project
 * @package IGK\System\Console\Commands
 */
class RemoveProjectCommand extends AppExecCommand{

    var $command = "--project:rm";
    var $desc = "Remove project";
    var $help = [
        "Project Name"
    ];
    var $category = "project";

    var $options =[ 
    ];


    /**
     * 
     * @param mixed $command 
     * @param ?string $projectName project entry directory or controller name
     * @return false|void 
     * @throws IGKException 
     */
    public function exec($command, $projectName=null) { 
        if (empty($projectName)){
            Logger::danger("Project name is required");
            return false;
        }
        $c = igk_io_projectdir()."/".ucfirst($projectName);
        $found = false;
        if (!is_dir($c)){

            $c = igk_sys_get_projects_controllers(); 
            $t = [];

            foreach ($c as $m) {
                if (get_class($m) == $projectName){
                    $found = true;
                    $c = $m->getDeclaredDir();
                    break;
                }
            }
            if (!$found){
                Logger::danger("Project not found");
                return false;
            }
        } 
        IO::RmDir($c);  
        $c = new ClearCacheCommand;
        $c->exec($command);
    }

    protected function showUsage()
    {
        $c = basename(igk_getv(igk_getv($_SERVER,"argv"), 0));
        Logger::info("Usage :{$c} --project:rm project_dirname");
    }

}