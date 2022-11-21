<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeSeederCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\App; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder; 
use \IGKControllerManagerObject;
use IGKDbUtility;

class MakeSeederCommand extends AppExecCommand
{
    var $command = "--make:seeder";

    var $category = "make";

    var $desc  = "make project's seeder";

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
        Logger::info("make seeder class ... " . $controller);
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);

        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }

        $clname = ucfirst(igk_str_ns($modelname)) . "Seeder";
        $ns = $ctrl->getEntryNamespace();
        if (!empty($ns)) {
            $ns = str_replace("/", "\\", $ns . "/Database/Seeds");
        }else{
            $ns = "Database\\Seeds";
        }

        $bind = [];
        $bind[$ctrl::classdir() . "/Database/Seeds/" . $clname . ".php"] = function ($file) use ($clname, $author, $ns) {
            $builder = new PHPScriptBuilder();
            $fname = basename($file);
            $builder->type("class")->name($clname)
                ->author($author)
                ->defs("")
                ->doc("seeder")
                ->file($fname)
                ->namespace($ns)
                ->extends(\IGK\System\Database\Seeds\SeederBase::class)
                ->desc("seeder " . $clname);
            igk_io_w2file($file,  $builder->render());
        };


        $force = property_exists($command->options, "--force");
        foreach ($bind as $n => $c) {
            if (!file_exists($n) || $force) {
                $c($n, $command);
                Logger::success("generate : " . $n);
            }
        }

        \IGK\Helper\SysUtils::ClearCache();
        Logger::success("done\n");
    }
    public function help()
    {
        Logger::print("-");
        Logger::info("Make db module utility");
        Logger::print("-\n");
        Logger::print("Usage : " . App::Gets(App::GREEN, $this->command) . " ctrl name [options]");
        Logger::print("\n\n");
    }
}
