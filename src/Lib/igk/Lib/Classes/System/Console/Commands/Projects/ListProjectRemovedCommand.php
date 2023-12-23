<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListProjectRemovedCommand.php
// @date: 20231223 16:56:28
namespace IGK\System\Console\Commands\Projects;

use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
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
        foreach(['restore'] as $n){ 
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
					rename($pdir, IGK_PROJECT_DIR."/".$name);
					Logger::info('clear cache');
					SysUtils::ClearCache();
					
				} else {
					Logger::danger(sprintf('missing controller project directory. %s', $name));
					igk_exit(-1);
				}
			}
			return;
		}
	}
}
