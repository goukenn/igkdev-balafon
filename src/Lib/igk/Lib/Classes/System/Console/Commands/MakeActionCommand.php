<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeActionCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Actions\ActionBase; 
use IGK\Actions\ApiActionBase;
use IGK\Actions\MiddlewireActionBase;
use IGK\Actions\ProjectDefaultAction;
use IGK\Helper\StringUtility; 
use igk\System\Console\Commands\Utility as CommandsUtility;

class MakeActionCommand extends AppExecCommand
{
    var $command = "--make:action";
    var $category = "make";
    var $desc = "make new project's action. Contextual command.";
    var $options = [
        "--type:type" => "defaut Model type class. 'api'|'def'|'project'|'middlewire'",
        "--force" => "flag: force create action"
    ]; 
    /**
     * 
     * @var callable
     */
    var $definition; // definition callback
    /**
     * array of uses
     * @var ?array|?string
     */
    var $uses;
    var $usage = 'controller action_name [options]';
    /**
     * @var string $controller Controller
     * @var string $actionName the action to create 
     */
    public function exec($command, ?string $controller = null, ?string $action_name = null)
    {
        if (is_null($action_name) && !empty($controller)) {
            if (property_exists($command->options, "--controller")) {
                $ctrl = self::ResolveController($command, null, false) ?? igk_die("missing controller");
                $action_name = $controller;
                $controller = $ctrl->getName();
            } else {
                igk_die('required balafon\'s controller project');
            }
        }
        if (empty($controller)) {
            return false;
        }
        if (empty($action_name)) {
            Logger::danger("action name required");
            return false;
        }
        Logger::info("make action ..." . $controller);
        $author = $this->getAuthor($command);
        $type = igk_str_ns(igk_getv($command->options, "--type", ActionBase::class));
        $type = igk_getv([
            "project" => ProjectDefaultAction::class,
            "def" => ActionBase::class,
            "middlewire" => MiddlewireActionBase::class,
            'api' => ApiActionBase::class,
        ], strtolower($type), $type);
        $ctrl = self::GetController(str_replace("/", "\\", $controller), false);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }
        if (!$type || !class_exists($type) || !(($type == ActionBase::class) || is_subclass_of($type, ActionBase::class))) {
            Logger::danger("type class not found : [$type] ");
            return false;
        }
        $ns = $ctrl->getEntryNamespace();
        $dir = $ctrl::classdir();
        $bind = [];
        $action_name = implode("/", array_map('ucfirst', explode('/', $action_name)));
        if ((($pos = strrpos(strtolower($action_name), 'action')) > 0) && (($pos + 6) == strlen($action_name))) {
            $action_name = substr($action_name, 0, -6);
        }
        $action_name = preg_replace("/[^a-z0-9\/]/i", "", $action_name);
        $path = $action_name;
        $tcl =  explode("/", StringUtility::Uri($path));
        array_pop($tcl);
        if (!empty($ns)) {
            $ns .= "\\";
        }
        $ns .= "Actions";
        if (count($tcl)) {
            $ns .= "\\" . implode("\\", $tcl);
        }
        $acfile = $dir . "/Actions/{$path}Action.php";
        $bind[$acfile] = function ($file) use (
            $action_name,
            $author,
            $ns,
            $type
        ) {
            $content = $this->_getContent();
            $v_uses = $this->_getUses() ?? [];
            $builder = new PHPScriptBuilder();
            $fname = $action_name . IGK_VIEW_FILE_EXT;
            $builder->type("class")->name(igk_io_basenamewithoutext($file))
                ->uses($v_uses)
                ->author($author)
                ->namespace($ns)
                ->defs($content)
                ->doc("view action")
                ->file($fname)
                ->extends($type)
                ->desc("view action " . $action_name);
            igk_io_w2file($file,  $builder->render());
        };
        CommandsUtility::MakeBindFiles($command, $bind, property_exists($command->options, "--force"));
        if (property_exists($command->options, "--clearcache"))
            \IGK\Helper\SysUtils::ClearCache();
        Logger::info('action file : ' . $acfile);
        Logger::success("Done - Make Action");
    }
    private function _getContent()
    {
        if ($def = $this->definition) {
            return $def();
        }
        return "";
    }
    private function _getUses()
    {
        if ($uses = $this->uses) {
            return $uses();
        }
        return [];
    }
     
}
