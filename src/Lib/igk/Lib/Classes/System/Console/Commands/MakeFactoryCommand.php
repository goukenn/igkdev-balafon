<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeFactoryCommand.php
// @date: 20220728 16:57:09
// @desc: make factory class

 
namespace IGK\System\Console\Commands;

use IGK\Controllers\SysDbController;
use IGK\System\Console\App; 
use IGK\System\Console\AppExecCommand;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder; 
use \IGKControllerManagerObject;
use IGKDbModelUtility;

class MakeFactoryCommand extends AppExecCommand
{
    var $command = "--make:factory";

    var $category = "make";

    var $desc  = "make project's factory. use %sys% for system controller.";

    var $options = [];

    var $usage = "[modelname --controller:controller]|[controller [modelname]] [option]";
    public function exec($command, $controller = "", $modelname = "")
    {
        $ctrl = null;
        if (empty($modelname) && !empty($controller)){
            $ctrl = self::ResolveController($command, null, false);
            $modelname = $controller;
            $controller = $ctrl->getName();
        } else if (empty($controller)) {
            return false;
        }
        if (empty($modelname)) {
            Logger::danger("model utility name required");
            return false;
        }
        if ($controller=="%sys%"){
            $controller = SysDbController::ctrl();
        }
        Logger::info("make factory class ... " . $controller);
        $author = $this->getAuthor($command);

        $ctrl = $ctrl ?? igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }
        $entry_path = "/Database/Factories";
        $clname = igk_str_add_suffix(ucfirst(igk_str_ns($modelname)), "Factory");
        $ns = $ctrl->getEntryNamespace().$entry_path;
        if (!empty($ns)) {
            $ns = str_replace("/", "\\", $ns);
        }

        $bind = [];
        $fields = []; 
        $ctrl->register_autoload(); 

        if ($g = $ctrl::model($modelname)){
            $fields = (array)$g::createEmptyRow();
        } else {
            Logger::warn(sprintf('missing model [%s]', $modelname));
        }
        $fields = var_export($fields, true);  
        $bind[$ctrl::classdir() . "/Database/Factories/" . $clname . ".php"] = function ($file) use ($clname, $author, $ns, $fields) {
            $builder = new PHPScriptBuilder();
            $fname = basename($file);
            $builder->type("class")->name($clname)
                ->author($author)
                ->defs("public function definition(): ?array{\n\treturn $fields;\n}")
                ->doc("factory")
                ->file($fname)
                ->namespace($ns)
                ->extends(\IGK\System\Database\Factories\FactoryBase::class)
                ->desc("factory " . $clname);
            igk_io_w2file($file,  $builder->render());
        };


        $force = property_exists($command->options, "--force");
        $gen  = false ;
        Utility::MakeBindFiles($command, $bind, $force);
      
        if ($gen){
            \IGK\Helper\SysUtils::ClearCache();
        }
        Logger::success("done\n");
    }
    public function help()
    {
        Logger::print("-");
        Logger::info("Make new db modeul utility");
        Logger::print("-\n");
        Logger::print("Usage : " . App::Gets(App::GREEN, $this->command) . " controller name [options]");
        Logger::print("\n\n");
    }
}
