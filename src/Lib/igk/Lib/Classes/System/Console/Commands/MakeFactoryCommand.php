<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeFactoryCommand.php
// @date: 20220728 16:57:09
// @desc: make factory class

 
namespace IGK\System\Console\Commands;

use IGK\System\Console\App; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder; 
use \IGKControllerManagerObject;
use IGKDbUtility;

class MakeFactoryCommand extends AppExecCommand
{
    var $command = "--make:factory";

    var $category = "make";

    var $desc  = "make project's factory";

    var $options = [];
    public function exec($command, $controller = "", $modelname = "")
    {
        if (empty($controller)) {
            return false;
        }
        if (empty($modelname)) {
            Logger::danger("model utility name required");
            return false;
        }
        Logger::info("make factory class ... " . $controller);
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);

        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }
        $entry_path = "/Database/Factories";
        $clname = ucfirst(igk_str_ns($modelname)) . "Factory";
        $ns = $ctrl->getEntryNamespace().$entry_path;
        if (!empty($ns)) {
            $ns = str_replace("/", "\\", $ns);
        }

        $bind = [];
        $fields = [];
        if ($g = $ctrl::model($modelname)){
            $fields = (array)$g::createEmptyRow();
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
        foreach ($bind as $n => $c) {
            if (!file_exists($n) || $force) {
                $c($n, $command);
                Logger::success("generate : " . $n);
                $gen = true;
            }
        }
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
        Logger::print("Usage : " . App::gets(App::GREEN, $this->command) . " ctrl name [options]");
        Logger::print("\n\n");
    }
}
