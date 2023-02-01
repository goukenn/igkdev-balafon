<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbDropCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use function igk_resources_gets as __;

class DbListProjectTableCommand extends AppExecCommand{
    public $command = "--db:list-project-tables";
    public $category = "db";
    public $desc = "list project's stored tables";
    public function exec($command, ?string $projectName=null)
    { 
        if (is_null($projectName)){
            igk_die("require project");
        }
        DbCommandHelper::Init($command);
        if (!($c = SysUtils::GetControllerByName($projectName))) {
            Logger::danger("no controller found: " . $projectName);
            return -1;
        }
        Logger::info(__("List project [{$projectName}]'s tables"));
        if ($info = $c->getDataTableDefinition()){
            array_map(function($t){
                Logger::print($t);
            }, array_keys((array)$info->tables));
        }else{
            Logger::danger("definition info not found");
            return - 1;
        }

 
        
        Logger::success(__("done"));
        return 0;

    }
}