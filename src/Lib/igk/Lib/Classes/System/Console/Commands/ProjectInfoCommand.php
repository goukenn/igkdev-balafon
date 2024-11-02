<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInfoCommand.php
// @date: 20230313 21:45:12
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Configuration\ProjectInfo;
use IGK\System\Configuration\ProjectConfiguration;
use IGK\Helper\Activator;
use IGK\System\Composer\ComposerPackage;
use IGK\System\Console\App;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGK\System\Npm\JsonPackage;
use IGKConstants;

///<summary></summary>
/**
 * project configuration information 
 * @package IGK\System\Console\Commands
 */
class ProjectInfoCommand extends AppExecCommand
{
	var $command = '--project:info';
	var $desc = 'view project\'s store information';
	var $options = [
		'--base-dir' => 'flag: render only declared directory',
		'--logo' => 'flag: render only svg logo',
	];
	var $category = "project";
	const CNF_FILE = IGKConstants::PROJECT_CONF_FILE;

	public function exec($command, string $controller = null)
	{
		$ctrl = ($controller ? self::GetController($controller) : null) ?? die("missing controller");

		$dir = $ctrl->getDeclaredDir();
		if (property_exists($command->options, '--base-dir')) {
			echo $dir;
			return 0;
		}
		if (property_exists($command->options, '--logo')) {
			$tf = [$ctrl->getDataDir() . "/assets/logo.svg",
			IGK_LIB_DIR . '/Data/R/svg/favicon.svg'];
			while(count($tf)>0){
				$f = array_shift($tf);
				if (file_exists($f)) {
					readfile($f);
					break;
				}
			}
			return 0;
		}

		$inf = new ProjectInfo;
		$inf->base_dir = $dir;
		$inf->name = $ctrl->getName();
		$se = [];
		$cnf = $ctrl->getConfigs();
		// + | -----------------------------------------------------
		// + | filter settings information 
		// + |
		foreach ($cnf->to_array() as $p => $v) {
			if (strpos($p, "env.") === 0) {
				continue;
			}
			if ($cnf->isSecret($p)) {
				$se[$p] = '<< secret >>';
				continue;
			}
			$se[$p] = $v;
		}
		$inf->settings = $se;

		$f = Path::Combine($dir, self::CNF_FILE);
		if (is_file($f)) {
			$inf->configs = Activator::CreateNewInstance(
				ProjectConfiguration::class,
				json_decode(file_get_contents($f))
			);
			if (is_null($inf->configs->name)) {
				$inf->configs->name = $inf->name;
			}
		}
		if (is_file($f = $dir . "/package.json")) {
			if (false !== ($c = JsonPackage::Load($f))) {
				$inf->package_json = $c;
			}
		}

		if (is_file($f = $dir . "/composer.json")) {
			if (false !== ($c = ComposerPackage::Load($f))) {
				$inf->composer = $c;
			}
		}
		$sb = json_encode($inf, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		echo  str_replace('"<< secret >>"', App::Gets(App::GRAY, '"<< secret >>"'), $sb);

		// Logger::danger('data: ');
		echo PHP_EOL;
	}
}
