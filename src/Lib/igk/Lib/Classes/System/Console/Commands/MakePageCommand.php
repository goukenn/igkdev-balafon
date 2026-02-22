<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakePageCommand.php
// @date: 20251209 14:49:36
namespace IGK\System\Console\Commands;

use IGK\Controllers\ControllerTask;
use IGK\Helper\StringUtility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

/**
 * 
 * @package IGK\System\Console\Commands
 * @author C.A.D. BONDJE DOUE
 */
class MakePageCommand extends AppExecCommand
{
	var $command = '--make:page';
	var $desc = 'make a project-controller page ';
	var $options = [];
	var $category = 'make';
	var $usage = 'controller [options]';
	public function exec($command, ?string $controller =null, ?string $page=null)
	{
		$ctrl = self::GetController($controller) ?? igk_die('missing controller'); 
		$page || igk_die('missing page name');
		Logger::info("make page:" . $ctrl);
		if (($c = igk_getctrl($ctrl, false)) || ($c = $ctrl::ctrl())) {
			$page = implode('', array_map('ucfirst', explode('_', StringUtility::FuncName($page))));
			$path = "Pages/" . ucfirst($page) . "Page";
			if (!($t = $c->resolveClass($path))) {
				$name = ucfirst($page);
				if (strrpos($name, "Page", 4) === false) {
					$name .= "Page";
				}
				$builder = new PHPScriptBuilder();
				$builder
					->author($command->app->getConfigs()->get("author", IGK_AUTHOR))
					->type("class")
					->file("$path.php")
					->name($name)
					->extends(ControllerTask::class)
					->implements()
					->desc(igk_getv($command->options, "--desc"))
					->defs("public function index(){\n}")
					->namespace($c::ns("Pages"));
				$file = $c::classdir() . "/{$path}.php";
				igk_io_w2file($file, $builder->render());
				Logger::success("complete page: " . $path);
				Logger::info("file: " . $file);
			}
			return 200;
		} else {
			Logger::danger("failed : controller not found");
		}
	}
}
