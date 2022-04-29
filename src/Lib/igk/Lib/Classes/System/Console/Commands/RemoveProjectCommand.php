<?php
namespace IGK\System\Console\Commands;

use ClearCacheCommand;
use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;
use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGK\System\Installers\OsShell;
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


    public function exec($command, $projectName=null) { 
        if (empty($projectName)){
            Logger::danger("Project name is required");
            return false;
        }
        $c = igk_io_projectdir()."/".ucfirst($projectName);
        if (!is_dir($c)){
            Logger::danger("Project not found");
            return false;
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