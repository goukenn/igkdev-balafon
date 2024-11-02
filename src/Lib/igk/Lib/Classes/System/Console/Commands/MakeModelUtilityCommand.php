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
use \IGKControllerManagerObject;
use IGKDbModelUtility;

class MakeModelUtilityCommand extends AppExecCommand
{
    var $command = "--make:model-utility";
    var $category = "make";
    var $desc  = "make new project's model utility";
    var $options = [];
    var $usage = "[controller] model_utility_name [options]";
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
