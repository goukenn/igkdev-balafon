<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvironmentCommand.php
// @date: 20240914 11:23:33
namespace IGK\System\Console\Commands;

use Google\Service\TrafficDirectorService\RegexMatcher;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Colorize;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;
use IGKConstants;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class EnvironmentCommand extends AppExecCommand{
	var $command='--env';
	var $desc='view environment definition'; 
	var $options=[
		"--no-color"=>"flag: disable use of color"
	]; 
	var $category; 
	static function Environment(){
		$env = []; 
		$tenv = getenv();

		foreach($tenv as $k => $v){
			if (preg_match("/^\bIGK_[A-Z_]+\b/", $k)){
				$env[$k] = $v;
			}
		}
		return $env;
	}
	public function exec($command) { 
		$def = (object)array_fill_keys(['controller','project'],null);
		$def->version = IGK_VERSION;
		$def->workingDirectory = getcwd(); //$command->app->getConfigs(); //$command->workingDirectory;
		$ctrl = self::GetController(igk_getv($command->options,'--controller'), false);

		$def->controller = $ctrl ? $ctrl->getName() : null;//  getCurrentController();
		$def->env = self::Environment();
		// $def->currentUser = igk_get_system_user();
		$def->config = (object)igk_configs()->getEntries();

		if ($ctrl){
			$cnf = $ctrl->getDeclaredDir()."/". IGKConstants::PROJECT_CONF_FILE;
			if (file_exists($cnf)){
				$def->project = json_decode(file_get_contents($cnf));

			}
		}

	 


		$s = json_encode($def, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		if (!property_exists($command->options, '--no-color')){
			// 
			$colorizer = $this->getColorizer();
			$s = $colorizer($s); 
		}
		Logger::print( $s );
		Logger::success('done');
	}
}