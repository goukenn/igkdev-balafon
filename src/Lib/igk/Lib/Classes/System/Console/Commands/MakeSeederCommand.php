<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeSeederCommand.php
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
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class MakeSeederCommand extends AppExecCommand
{
    var $command = "--make:seeder";
    var $category = "make";
    var $desc  = "make project's seeder";
    var $options = [];
    /**
     * Execute the command to generate a seeder class for the given controller and model.
     *
     * @param mixed  $command    The command context object.
     * @param string $controller The controller identifier.
     * @param string $modelname  The model name to base the seeder on.
     * @return bool|void Returns false on validation failure, void on success.
     */
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
        $author = $this->getAuthor($command);
        $ctrl = self::GetController(str_replace("/", "\\", $controller), false);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }
        $clname = igk_str_add_suffix(ucfirst(igk_str_ns($modelname)),  "Seeder");
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
                ->defs("function run(){}")
                ->doc("seeder")
                ->file($fname)
                ->namespace($ns)
                ->extends(\IGK\System\Database\Seeds\SeederBase::class)
                ->desc("seeder " . $clname);
            igk_io_w2file($file,  $builder->render());
        };
        $force = property_exists($command->options, "--force");
        Utility::MakeBindFiles($command, $bind, $force);
        \IGK\Helper\SysUtils::ClearCache();
        Logger::success("done");
    }
    /**
     * Display usage help information for the make seeder command.
     *
     * @return void
     */
    public function help()
    {
        Logger::print("-");
        Logger::info("Make db module utility");
        Logger::print("-\n");
        Logger::print("Usage : " . App::Gets(App::GREEN, $this->command) . " controller name [options]");
        Logger::print("\n\n");
    }
}