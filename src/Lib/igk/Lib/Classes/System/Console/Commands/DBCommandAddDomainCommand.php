<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ClearCacheCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use Exception;
use IGK\Models\Subdomains;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

use Models\Domains;

class DBCommandAddDomainCommand extends AppExecCommand
{
    var $command = "--domain";

    var $desc = "set controller as domain";

    var $category = "administration";

    /**
     * exec the command
     */
    public function exec($command, ?string $action = null, ?string $domainname = null, $controller = null)
    {
        if (is_null($domainname)) {
            Logger::danger("domain is empty");
            return -1;
        }
        if (is_null($controller)) {
            Logger::danger("controller is empty");
            return -2;
        }
        if (!($ctrl = igk_getctrl($controller, false))) {
            Logger::danger("controller not found");
            return -3;
        }
        DbCommandHelper::Init($command);
        try {
            if ($g = Subdomains::insert(["clName" => $domainname, "clCtrl" => $ctrl->getName()])) {
                Logger::success("domain successully added. " . $domainname);
            }
        } catch (Exception $ex) {
        }
        return 0;
    }
}
