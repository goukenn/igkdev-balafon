<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeViewCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Helper\ViewHelper;
use IGK\System\Console\Helper\ConsoleUtility;
use IGK\System\IO\FileHandler;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class MakeViewCommand extends AppExecCommand
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--make:view";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "make";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc  = "make new project's view";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options = [
        "--controller:controller" => "set controller to use",
        "--action" => "flag: enable action",
        "--style:[style]" => "flag: create a style, supported extension css|bcss|pcss default is bcss",
        "--dir" => "enable view dir",
        "--force" => "flag:force  file creation ",
        '--clear-cache' => 'flag: clear cache',
        "--scaffold:[scaffoldtype]" => "type of view to generate. default is null. or builder"
    ];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = "controller viewname | viewname [options]";
    /**
     * exec command. 
     * controller viewname
     */

    public function exec($command, $controller = null, $viewname = "")
    {
        $gctrl = igk_getv($command->options, "--controller");
        $controller = $controller ?? $gctrl;
        $style = property_exists($command->options, '--style') ?
            igk_getv($command->options, '--style', 'bcss')
            : null;
        if (is_string($style) && empty($style)){
            $style = 'pcss';
        }
        if (empty($controller)) {
            Logger::danger("controller required");
            return false;
        }
        if (empty($viewname)) {
            if ($gctrl) {
                $viewname = $controller;
                $controller = $gctrl;
            } else {
                Logger::danger("view name required");
                return false;
            }
        }
        Logger::info("make view for ... " . $controller);
        $author = $this->getAuthor($command);
        $action = property_exists($command->options, "--action");
        $is_dir = property_exists($command->options, "--dir");
        $v_cache_clear = property_exists($command->options, "--clear-cache");
        $ctrl = self::GetController(str_replace("/", "\\", $controller), false);
        if (!$ctrl) {
            Logger::danger("controller $controller not found");
            return false;
        }
        $dir = $ctrl->getViewDir();
        if ($is_dir) {
            $dir .=  "/$viewname";
            $viewname =  IGK_DEFAULT_VIEW;
        }
        if (($ext = igk_io_path_ext($viewname)) == "phtml") {
            $viewname = igk_io_remove_ext($viewname);
            $viewname .= IGK_VIEW_FILE_EXT;
        } else {
            $handlers = FileHandler::GetViewContextFileHandlers();
            if ($handlers && !in_array('.' . $ext, array_keys($handlers))) {
                $viewname .= '.phtml';
            }
        }
        $bind = [];
        $scaffold = igk_getv($command->options, '--scaffold');
        $force = property_exists($command->options, '--force');

        if ($style) {
            $bind[Path::Combine(
                $ctrl->getStylesDir(),
                igk_io_remove_ext($viewname). '.' . $style
            )] = function ($file) {
                $sb = new StringBuilder;
                $sb->appendLine(self::GetStyledDefData(igk_io_path_ext($file)));
                igk_io_w2file($file, $sb . '');
            };
        }

        $bind[$dir . "/{$viewname}"] = function ($file) use ($viewname, $author, $scaffold) {
            // TODO : FROM Scaffold generate the base document 
            $src = $this->getInitViewContent($viewname, $scaffold);
            $builder = new PHPScriptBuilder();
            $ext = igk_io_path_ext($viewname);
            if ($ext == 'phtml') {
                $fname = $viewname;
                $builder->type("function")->name($viewname)
                    ->author($author)
                    ->defs($src)
                    ->docs("view entry point")
                    ->file($fname)
                    ->desc(implode("\n", ["", " @view: " . igk_io_remove_ext($viewname)]));
                igk_io_w2file($file,  $builder->render());
            } else {
                $src = '';
                if ($handler = FileHandler::GetFileHandlerFromExtension('.' . $ext)) {
                    $src = $handler->initDefaultSource();
                }
                igk_io_w2file($file, $src);
            }
        };
        ConsoleUtility::MakeFiles($bind, $command, $force);
        if ($v_cache_clear) {
            \IGK\Helper\SysUtils::ClearCache();
        }
        Logger::info('CLI command: ');
        Logger::info('balafon --request:view  ' . $ctrl->getName() . ' ' . $viewname);
        if ($action){
            $v_cmd = new MakeActionCommand;
            $tf = igk_io_remove_ext($viewname);
            $v_t = $command->app::CreateCommand($command->app);
            $v_cmd->exec($v_t, $controller, $tf);
        }
        Logger::success("done\n");
    }

    /**
    * auto generate doc.
    */

    public function help()
    {
        Logger::print("-");
        Logger::info("Make new Balafon's PROJECT view");
        Logger::print("-\n");
        parent::help();
    }

    /**
    * auto generate doc.
    * @param string $viewname
    * @param null|string $type
    * @return string
    */

    public function getInitViewContent(string $viewname, ?string $type = null): string
    {
        if ($type) {
            if ($type == 'builder') {
                return "\$builder([\"View : $viewname\"]);";
            } else {
                // 
                if ($builder = ViewHelper::GetViewScaffold($type)) {
                    return $builder->initView($viewname);
                }
                igk_die('missing scaffold type');
            }
        }
        return  "\$t->div()->Content = 'View : $viewname';";
    }

    /**
    * auto generate doc.
    * @param string $ext
    * @return string
    */

    public static function GetStyledDefData(string $ext): string
    {
        $s = '';
        if ($ext=='pcss'){
            $s = implode("\n", [ '<?php' ]); 
        }
        return $s;
    }
}
