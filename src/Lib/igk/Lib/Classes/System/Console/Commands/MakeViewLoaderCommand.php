<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeViewLoaderCommand.php
// @date: 20250514 14:25:50
namespace IGK\System\Console\Commands;
use IGK\Controllers\ViewLayoutLoader;
use IGK\Helper\ViewHelper;
use IGK\System\Console\AppExecCommand;
use igk\System\Console\Commands\Utility;
use IGK\System\Cron\CommandHelper;
use IGK\System\EntryClassResolution;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\WinUI\IViewLayoutLoader;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class MakeViewLoaderCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--make:view-layout';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='make a view layout class';
	/* var $options=[]; */

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'make';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller name [options]';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $name
    */
    public function exec($command, ?string $controller=null, ?string $name=null) { 
		$ctrl = self::GetController($controller);
		if (empty($name)){
			igk_die('missing name');
		}
		$force = property_exists($command->options, '--force');
		$n = ViewHelper::TreatViewNameForClassDefinition($name);
		$cl = $ctrl::resolveClass($path = sprintf(EntryClassResolution::WinUI_ViewLayoutFormat, ucfirst($n)));
		if ($force || !$cl){
			$dir = $ctrl::classdir();
		$bind[Path::Combine($dir, $path.'.php')] = function($file)use($ctrl, $path){
			$build = new PHPScriptBuilder;
            $ns = $ctrl->getEntryNamespace();
			$tns = trim(dirname(igk_dir($path)), DIRECTORY_SEPARATOR);
			if ($ns){
				$ns = igk_ns_name( implode("\\", [$ns, $tns]));
			}else {
				$ns = $tns;
			}
			$build->type('class')
			->name(basename($path))
			->extends(ViewLayoutLoader::class )
			->implements(IViewLayoutLoader::class)
			->namespace($ns);
			igk_io_w2file($file, $build->render());
		};
	}
		Utility::MakeBindFiles($command, $bind, $force);
	}
}