<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListProjectRemovedCommand.php
// @date: 20231223 16:56:28
namespace IGK\System\Console\Commands\Projects;

use IGK\ApplicationLoader;
use IGK\Helper\SysUtils;
use IGK\System\Caches\EnvControllerCacheList;
use IGK\System\Caches\InitEnvControllerChain;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use IGKAppSystem;
use IGKSysUtil;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console\Commands\Projects
 */
class ListProjectRemovedCommand extends AppExecCommand
{
	var $command = '--project:removed';
	var $desc = 'list removed project';
	var $options = [
		'--restore' => 'flag restore removed project list '
	];
	var $usage = 'action* [options]';
	public function showUsage(){
		parent::showUsage();
		Logger::info('');
        Logger::info('action* command: ');
        foreach(['restore','install','ls'] as $n){ 
            Logger::print("\t".substr($n, 8)); 
        }
	}
	/* var $category='project'; */
	public function exec($command, ?string $action = null)
	{

		DbCommandHelper::Init($command);
		$dir = IGK_PROJECT_DIR . '/.removed';
		if (empty($action) || ($action == "ls")) {
			if ($hdir = opendir($dir)) {
				while ($c = readdir($hdir)) {
					if (($c == '.') || ($c == '..'))
						continue;
					Logger::print($c);
				}
				closedir($hdir);
			} else {
				Logger::warn('missing .removed directory');
			}
			return;
		}
		if ($action=='restore'){
			$name = func_get_arg(2);
			if ($name){
				$pdir = $dir."/".$name;
				if (is_dir($pdir)){
					rename($pdir, $i_dir =  IGK_PROJECT_DIR."/".$name);
					Logger::info('clear cache');
					SysUtils::ClearCache();
					//+ install project - 
					self::InstallProject($i_dir);
					
				} else {
					Logger::danger(sprintf('missing controller project directory. %s', $name));
					igk_exit(-1);
				}
			}
			return;
		}
		if (($action == 'install') ){
			if ((func_num_args()>=2)){
				$name = func_get_arg(2);
			}else{
				Logger::danger("missing project name or folder");
				igk_exit(-1);
			}
			

			$i_dir =  IGK_PROJECT_DIR."/".$name;
			Logger::info('clear cache');
			SysUtils::ClearCache();
			if (self::InstallProject($i_dir)){
				Logger::success('done');
			}
			return;
		}
		Logger::warn(sprintf('missing action [%s]', $action));
	}
	/**
	 * install project 
	 * @param string $project_dir 
	 * @return void 
	 */
	public static function InstallProject(string $project_dir): bool{
		$v_o = false;
		//+----------------------------------------------
		//+ get controller in director.
		//+----------------------------------------------
		
		$loaded_fields = igk_loadlib($project_dir);
		if ($loaded_fields){ 
			// $manager = igk_app()->getControllerManager();
			// $loader = ApplicationLoader::getInstance();
			// $c = new InitEnvControllerChain;

			// $tab =  EnvControllerCacheList::GetControllersClasses(); 
        	// $c->load($tab, $manager, $loader); 
			
			$list = igk_sys_get_projects_controllers();
			$ctrl = null;
			foreach($list as $c){
				if ($c->getDeclaredDir() == $project_dir){
					$ctrl = $c;
					break;
				}
			}
			$g = $ctrl; //igk_getctrl('PrismaDemoController');

			if ($g){
				/// TODO: Project Installer
				$g->initDb(true);
				$install = new MigrationHandler($g); 
				$install->migrate('up');
			}

			$v_o = true;
		}

		return $v_o;
	}
}
