<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbDropCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKException;

use function igk_resources_gets as __;

class DbDropCommand extends AppExecCommand{
    public $command = "--db:droptables";
    public $category = "db";
    public $desc = "drop project's stored tables";
    public $usage = "controller";
    public function exec($command, ?string $projectName=null)
    { 
        if (!$projectName){
            throw new IGKException('project required');
        }
        DbCommandHelper::Init($command);
        if (!($c = SysUtils::GetControllerByName($projectName))) {
            Logger::danger("no controller found: " . $projectName);
            return -1;
        }
        Logger::info(__("dropping project [{$projectName}]'s tables"));
        $c::dropDb(false, true);
        Logger::success(__("done"));
        return 0;

    }
}