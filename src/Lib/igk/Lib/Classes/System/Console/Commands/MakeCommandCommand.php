<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeCommandCommand.php
// @date: 20230302 07:14:26
namespace IGK\System\Console\Commands;

use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\Modules\MakeClassCommandCommand;
use IGK\System\Console\Logger;
use IGK\System\EntryClassResolution; 
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console\Commands
 */
class MakeCommandCommand extends AppExecCommand
{
    var $command = '--make:command';
    var $category = "make";
    var $desc = "help build command.contextual command.";
 
    public function __construct()
    {
        parent::__construct();
        $this->usage = implode("\n", [
            "[controller] command",
            "{in :module-context:} command",
        ]);
    }


    public function exec($command, ?string $controller = null, ?string $command_name = null)
    {

        $context = $command->app->getContext();
        if ($context == 'module') {
            //passing to module - 
            $c = new MakeClassCommandCommand;
            $module = igk_getv($command->options, "--module");
            return $c->exec($command, $module, $controller);
        }

        $ctrl = null;
        $c = func_num_args();
        if ($c == 2) {
            $command_name = $controller;
            $controller = null;
        }
        if (!empty($controller)) {
            $ctrl = self::GetController($controller, false);
        }
        $c = func_num_args();
        if (is_null($command_name)) {
            igk_die("command_name require");
        }
        if (!igk_str_endwith($command_name, 'Command')) {
            $command_name .= 'Command';
        }

        $ctrl = $ctrl ?? SysDbController::ctrl();
        $clpath = Path::Combine(EntryClassResolution::CommandEntryNS, $command_name);
        $cl = $ctrl->resolveClass($clpath);

        if (is_null($cl) || !class_exists($cl)){
            //make command  
            $g = new MakeClassCommand();
            $defs = new StringBuilder;
            $defs->appendLine('var $command=\'command-name\';');
            $defs->appendLine("/* var \$desc='desc'; */");
            $defs->appendLine("/* var \$options=[]; */");
            $defs->appendLine("/* var \$category = ''; */");
            $defs->appendLine("/* var \$usage = ''; */");
            $defs->appendLine("public function exec(\$command) { }");

            $command_new = self::CreateOptionsCommandFrom($command);
            $command_new->options = (object)[
                "--controller" => $ctrl->getName(),
                "--extends" => AppExecCommand::class,
                "--defs" => $defs,
            ];
            $g->exec($command_new, $clpath);
        } else {
            Logger::danger('class already exists.');
        }
    }
}
