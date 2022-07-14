<?php

namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use function igk_resources_gets as __;

class DbDropCommand extends AppExecCommand{
    public $command = "--db:droptables";
    public $category = "db";
    public $desc = "drop project's stored tables";
    public function exec($command, ?string $projectName=null)
    { 
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