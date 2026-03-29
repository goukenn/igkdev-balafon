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
/**
* Db drop command.
* @package IGK\System\Console\Commands
*/
class DbDropCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    public $command = "--db:droptables";
    /**
    * Property: category.
    * @var mixed
    */
    public $category = "db";
    /**
    * Property: desc.
    * @var mixed
    */
    public $desc = "drop project's stored tables";
    /**
    * Property: usage.
    * @var mixed
    */
    public $usage = "controller";
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    */
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