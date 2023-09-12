<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApiCommand.php
// @date: 20230901 09:48:11
namespace IGK\System\Console\Commands\Api;

use IGK\Actions\ActionResolutionInfo;
use IGK\Helper\ArrayUtils;
use IGK\Helper\ViewHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\IO\File\PHPScriptBuilder;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Api
*/
class ApiCommand extends AppExecCommand{
	var $command='--api';
	var $desc='api utility ';
	var $options=[];
	var $category = 'api';
	var $usage = 'action [options]';
	public function exec($command, string $action=null) { 
		empty($action) ?? igk_die('action is required');
		if (method_exists($this,$fc = 'action_'.$action)){
			$this->$fc($command);
		}
	}
	public function help(){	
		parent::help();	 
	}
	/**
	 * list api url 
	 * @return void 
	 */
	public function action_ls(){
		$v_fc_showRoute = function($routes){
			foreach($routes as $k=>$r){
				echo $k;
				echo "\r\t\t\t\t\t".$r;
				echo PHP_EOL;
			}
		};
		if (file_exists($file = igk_io_cachedir().'/.api.routes.pinc') && 0){
			$routes = ViewHelper::Inc($file); 
			$routes && $v_fc_showRoute($routes);
			return;
		}
		if ($projects =  igk_sys_project_controllers()){
			$routes = [];
			foreach($projects as $l){
				$l->register_autoload();
				$api = $l->getConfigs()->api_route;
				if ($api){
					$routes[igk_uri_base_path($l->getAppUri($api))] = get_class($l);
					continue;
				}
				// if ($cl = $l->resolveClass(\Actions\Api\ApiAction::class)){
				// 	$tcl = $l::getActionHandler('api', $rep =new ActionResolutionInfo,['index']);
				// 	// class that handle api uri exists 
				// 	$routes[igk_uri_base_path($l->getAppUri('api'))] = $cl;
				// }
			}
			
			$builder = new PHPScriptBuilder;
			$builder->type('function')
			->defs(sprintf('return %s;', ArrayUtils::Export($routes)));
			igk_io_w2file($file, $builder->render());
			$routes && $v_fc_showRoute($routes);
		}
		
	}
}