<?php
// @author: C.A.D. BONDJE DOUE
// @file: RemoveProjectCommand.php
// @date: 20231223 15:49:13
namespace IGK\System\Console\Commands\Projects;

use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use Illuminate\Database\Console\DbCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Projects
*/
class RemoveProjectCommand extends AppExecCommand{
	var $command='--project:remove';
	var $desc='remove install project';
	/* var $options=[]; */
	var $category = 'project';

	var $usage = 'controller [options]';

	public function exec($command, ?string $controller = null) { 
		$ctrl = self::GetController($controller);
		DbCommandHelper::Init($command);

		$sm = new RemoveProjectMiddleWare;
		// remove - all migration  
		$mig = new MigrationHandler($ctrl);
		$mig->down(false); 
		// drop tables 
		Logger::info('drop used datbase');
		ControllerExtension::dropDb($ctrl, false, true); 
 
		
		//+ move project to installed dir 
		Logger::info('move project to .removed project folder');
		IO::CreateDir($dir = IGK_PROJECT_DIR.'/.removed');
		$v_dec = $ctrl->getDeclaredDir();
		$v_folder = basename($v_dec); // ctrl->getDeclaredDir());
		rename($v_dec, $dir.'/'.$v_folder);
		
		// clear project cache 
		Logger::info('clear cache');
		SysUtils::ClearCache();

	}	
}


class RemoveProjectMiddleWare{
	private $m_chain;

	public function add(){

	}
	public function next(){
		
	}
	public function run(){
		$q = $this->m_chain; 
		while($q){
			$q = $q->next(); 
		}
	}
}