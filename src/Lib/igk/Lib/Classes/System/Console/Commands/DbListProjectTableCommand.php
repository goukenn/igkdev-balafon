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

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class DbListProjectTableCommand extends AppExecCommand{
    public $command = "--db:list-project-tables";
    public $category = "db";
    public $desc = "list project's stored tables";
    public $usage = 'controller [options]';
    /**
     * Execute the command to list all tables defined for a given project controller.
     *
     * @param mixed       $command    The command context object.
     * @param string|null $controller The controller name to look up.
     * @return int Returns 0 on success or -1 on failure.
     */
    public function exec($command, ?string $controller=null)
    {
        if (is_null($controller)){
            $controller = '%sys%';
            // igk_die("require project");
        }
        DbCommandHelper::Init($command);
        if (!($c = SysUtils::GetControllerByName($controller))) {
            Logger::danger("no controller found: " . $controller);
            return -1;
        }
        Logger::info(__("List project [{$controller}]'s tables"));
        if ($info = $c->getDataTableDefinition()){
            $cp = array_keys((array)$info->tables);
            sort($cp);
            array_map(function($t){
                Logger::print($t);
            }, $cp);
        }else{
            Logger::danger("definition info not found");
            return - 1;
        }
        Logger::success(__("done"));
        return 0;
    }
}