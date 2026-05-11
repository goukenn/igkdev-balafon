<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionScaffold.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Scaffold;
use Exception;
use IGK\Controllers\BaseController;
use IGK\System\Console\App;
use IGK\System\Console\Commands\MakeActionCommand;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKException;
use ReflectionException;
/**
* auto generate doc.
* @package IGK\System\Console\Scaffold
*/
/**
* auto generate doc.
* @package IGK\System\Console\Scaffold
*/
class ActionScaffold extends ScaffoldBase
{
    /**
    * auto generate doc.
    * @var string
    */
    var $description = "generate REST action";
    /**
    * auto generate doc.
    * @param null|string $name
    * @return mixed
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
    * show help
    * @param mixed $command
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
    * Runs.
    * @param mixed $command
    * @param null|mixed $controller
    * @param null|string $name
    */
    protected function run($command, $controller = null, ?string $name=null)
    {
        $model = igk_getv($command->options, "--model");
        $is_force = property_exists($command->options, "--force");
        $action_name = igk_getv($command->options, "--action");
        if (is_null($controller)) {
            Logger::danger("controller not provided");
            return false;
        }
        $controller = igk_str_ns($controller);
        if (!($ctrl = igk_getctrl($controller, false))) {
            Logger::danger(sprintf("controller %s not found", $controller));
            return false;
        }
        $viewdir = $ctrl->getViewDir() . "/$name";
        $bind = [];
        if ($model) {
            $model = $ctrl::model($model);
        }
        $bind[$viewdir . "/default.phtml"] = function ($file) use ($model) {
            igk_io_w2file($file, self::GenerateViewTemplate($file));
        };
        $bind[$viewdir . "/details.phtml"] = function ($file) use ($model) {
            igk_io_w2file($file, self::GenerateViewTemplate($file));
        };
        $bind[$viewdir . "/edit.phtml"] = function ($file) use ($model) {
            igk_io_w2file($file, self::GenerateViewTemplate($file));
        };
        $bind[$viewdir . "/list.phtml"] = function ($file) use ($model) {
            igk_io_w2file($file, self::GenerateViewTemplate($file));
        };
        $bind[$viewdir . "/create.phtml"] = function ($file) use ($model) {
            igk_io_w2file($file, self::GenerateViewTemplate($file));
        };
        $bind[$viewdir . "/delete.phtml"] = function ($file) use ($model) {
            igk_io_w2file($file, self::GenerateViewTemplate($file));
        };
        Utility::MakeBindFiles($command, $bind, $is_force);
        if ($model) {
            $action = new MakeActionCommand();
            $action->uses = function()use($model){
                $m_cl = get_class($model);
                $ctrl_type = BaseController::class;
                return [$m_cl, $ctrl_type];
            };
            $action->definition = function () use ($model) {
                $m_cl = get_class($model);
                $ctrl_type = BaseController::class;
                return <<<EOF
/**
* @var \\{$m_cl} reference model
*/
var \$model;
protected function initialize(\\{$ctrl_type} \$ctrl){
    parent::initialize(\$ctrl);
    \$this->model = \\{$m_cl}::model();
}  
public function index(?int \$index=null){
    return \$this->get(\$index);
}
public function get(?int \$index=null){
    if (is_null(\$index)) 
        return \$this->model::select_all();
    return \$this->model::select_row(\$index);
}
protected function delete_post(\\{$m_cl} \$item){
    \$n = \$this->model::name();
    return \$this->handleBool(\$item::delete(),
        sprintf(__("%s %s removed"), \$n , \$item->id()),
        sprintf(__("%s %s not removed"), \$n, \$item->id())
	); 
}
protected function update_post(\\{$m_cl} \$item, \$model){
    if (\$data = json_decode(igk_io_get_uploaded_data())){
        \$item->bind(\$data); 
        \$item->save();
    }
    return \$item;
}
protected function update_patch(\\{$ctrl_type} \$item){
    return \$item;
}
EOF;
            };
            $action->exec($command, get_class($ctrl), $name);
        }
        else if ($action_name){
            Logger::info("generate action");
            $action = new MakeActionCommand();
            $action->exec($command, get_class($ctrl), $action_name);
        }
        Logger::success("Done. " . igk_sys_request_time());
    }
    /**
    * auto generate doc.
    * @param mixed $file
    * @param null|string $content
    * @return mixed
    */
    private static function GenerateViewTemplate($file, ?string $content = null)
    {
        $builder = new PHPScriptBuilder();
        $content = $content ?? "\$t->clearChilds();";
        $builder->type("function")
            ->desc("view template")
            ->filename(basename($file))
            ->defs($content);
        return $builder->render();
    }
}