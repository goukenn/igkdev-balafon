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

/**
* Dbcommand add domain command.
* @package IGK\System\Console\Commands
*/
class DBCommandAddDomainCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--domain";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "set controller as domain";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "administration";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [];
    /**
    * Shows Usage.
    */
    public function showUsage(){
        Logger::print(sprintf( "%s domain_name controller [options]", $this->command));
    }
    /**
    * exec the command
    * @param mixed $command
    * @param ?string $domainname
    * @param mixed $controller
    */
    public function exec($command, ?string $domainname = null, $controller = null)
    {
        if (is_null($domainname)) {
            Logger::danger("domain is empty");
            return -1;
        }
        if (is_null($controller)) {
            Logger::danger("controller is empty");
            return -2;
        }
        if (!($ctrl = self::GetController($controller, false))) {
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