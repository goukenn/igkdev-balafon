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
    public function exec($command, ?string $controller=null)
    { 
        if (!$controller){
            throw new IGKException('project required');
        }
        DbCommandHelper::Init($command);
        if (!($c = SysUtils::GetControllerByName($controller))) {
            Logger::danger("no controller found: " . $controller);
            return -1;
        }
        Logger::info(__("dropping project [{$controller}]'s tables"));
        $c::dropDb(false, true);
        Logger::success(__("done"));
        return 0;

    }
}