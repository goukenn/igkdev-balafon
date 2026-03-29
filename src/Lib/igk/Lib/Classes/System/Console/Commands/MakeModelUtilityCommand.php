<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeModelUtilityCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\System\Console\App; 
use IGK\System\Console\AppExecCommand;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder; 
use IGKDbModelUtility;
/**
* Make model utility command.
* @package IGK\System\Console\Commands
*/
class MakeModelUtilityCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--make:model-utility";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "make";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc  = "make new project's model utility";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [];
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = "[controller] model_utility_name [options]";
    /**
     * Execute the command to generate a model utility class for the given controller.
     *
     * @param mixed       $command    The command context object.
     * @param string|null $controller The controller identifier.
     * @param string|null $modelname  The model utility name to create.
     * @return bool|void Returns false on validation failure, void on success.
     */
    public function exec($command,?string $controller = null, ?string $modelname = "")
    {
        $ctrl = null;
        if (empty($controller)) {
            return false;
        }
        if (empty($modelname)) {
            if (!($ctrl = self::ResolveController($command,null, false))){
                Logger::danger("model utility name required");
                return false;
            }
            $modelname = $controller;
        }
        Logger::info("make model utility class ..." . $controller);
        $author = $this->getAuthor($command);
        $ctrl = $ctrl ?? self::GetController($controller);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }
        $clname = ucfirst(igk_str_ns($modelname)) . "ModelUtility";
        $ns = $ctrl->getEntryNamespace();
        if (!empty($ns)) {
            $ns = str_replace("/", "\\", $ns . "/ModelUtilities");
        }
        $bind = [];
        $bind[$ctrl::classdir() . "/ModelUtilities/" . $clname . ".php"] = function ($file) use ($clname, $author, $ns) {
            $builder = new PHPScriptBuilder();
            $fname = basename($file);
            $builder->type("class")->name($clname)
                ->author($author)
                ->defs("")
                ->doc("view entry point")
                ->file($fname)
                ->namespace($ns)
                ->extends(IGKDbModelUtility::class)
                ->desc("module utility " . $clname);
            igk_io_w2file($file,  $builder->render());
        };
        Utility::MakeBindFiles($command, $bind, false);
        \IGK\Helper\SysUtils::ClearCache();
        Logger::success("done\n");
    }
}