<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionScaffold.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Scaffold;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\System\Console\App;
use IGK\System\Console\Commands\MakeActionCommand;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;
use ModelBase;
class AuthScaffold extends ScaffoldBase
{
    var $description = "authentication scaffold";
    /**
     * Execute the authentication scaffold command.
     *
     * @param mixed       $command    The command object carrying options.
     * @param mixed|null  $controller The target controller identifier.
     * @param string|null $name       Optional name for the scaffold.
     * @return void
     */
    public function exec($command, $controller = null, ?string $name = null)
    {
        if (property_exists($command->options, "--help")) {
            $this->showHelp($command);
            return;
        }
        $this->run($command, ...array_slice(func_get_args(), 1));
    }
    /**
     * Display help information for the authentication scaffold command.
     *
     * @param mixed $command The command object used to display help output.
     * @return void
     */
    public function showHelp($command)
    {
        Logger::print(App::Gets(App::BLUE_I, "params"));
        Logger::print('$controller $name [--action|--model] [--force]');        
        Logger::print("--action:[action_name]\r\t\t\tset the model");
        Logger::print("--model:[model_name]\r\t\t\tset the model");
        Logger::print("--force \r\t\t\tfoce model creation");
    }
    /**
     * Run the scaffold generation for the given controller and name.
     *
     * @param mixed       $command    The command object carrying options.
     * @param mixed|null  $controller The target controller identifier.
     * @param string|null $name       Optional name for the scaffold.
     * @return bool|void
     */
    protected function run($command, $controller = null, ?string $name=null)
    {
        $model = igk_getv($command->options, "--model");
        $is_force = property_exists($command->options, "--force");
        $action_name = igk_getv($command->options, "--action");
        //as class = 
        $controller = igk_str_ns($controller); 
        if (!($ctrl = igk_getctrl($controller, false))) {
            Logger::danger(sprintf("controller %s not found", $controller));
            return false;
        }
        // $ctrl::register_autoload();
        $viewdir = $ctrl->getViewDir(); 
        $bind[$viewdir . "/ServiceLogin.phtml"] = function ($file) use ($model) {
            $sb = new StringBuilder;
            $sb->appendLine(implode('\n', [
            ]));
            igk_io_w2file($file, self::GenerateViewTemplate($file, $sb.''));
        };
        $bind[$viewdir . "/registerLogin.phtml"] = function ($file) use ($model) {
            $sb = new StringBuilder;
            $sb->appendLine(implode('\n',[]));
            igk_io_w2file($file, self::GenerateViewTemplate($file, $sb.''));
        };
        $bind[$viewdir . "/changeUserPwd.phtml"] = function ($file) use ($model) {
            $sb = new StringBuilder;
            $sb->appendLine(implode('\n',[
            ]));
            igk_io_w2file($file, self::GenerateViewTemplate($file, $sb.''));
        };
        $bind[$viewdir . "/confirmRegistration.phtml"] = function ($file) use ($model) {
            $sb = new StringBuilder;
            $sb->appendLine(implode('\n',[
            ]));
            igk_io_w2file($file, self::GenerateViewTemplate($file, $sb.''));
        };
        Utility::MakeBindFiles($command, $bind, $is_force);
        if ($model) {
            // $action->exec($command, get_class($ctrl), $name);
        }
        else if ($action_name){
            Logger::info("generate action");
            $action = new MakeActionCommand();
            $action->exec($command, get_class($ctrl), $action_name);
        }
        Logger::success("done. " . igk_sys_request_time());
    }
    /**
     * Generate a PHP view template script for the given file path.
     *
     * @param string      $file    The destination file path for the template.
     * @param string|null $content Optional content to embed in the template.
     * @return string The rendered PHP script content.
     */
    private static function GenerateViewTemplate($file, ?string $content = null)
    {
        $builder = new PHPScriptBuilder();
        $content = $content;
        $builder->type("function")
            ->desc("view template")
            ->filename(basename($file))
            ->defs($content);
        return $builder->render();
    }
}