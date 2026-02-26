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
/**
* 
* @package IGK\System\Console\Commands\Api
*/
class ApiCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--api';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='api utility ';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'api';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'action [options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $action
    */
    public function exec($command, ?string $action=null) { 
		empty($action) ?? igk_die('action is required');
		if (method_exists($this,$fc = 'action_'.$action)){
			$this->$fc($command);
		}
	}

    /**
    * auto generate doc.
    */
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
		if (igk_io_file_exists($file = igk_io_cachedir().'/.api.routes.pinc') && 0){
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
			}
			$builder = new PHPScriptBuilder;
			$builder->type('function')
			->defs(sprintf('return %s;', ArrayUtils::Export($routes)));
			igk_io_w2file($file, $builder->render());
			$routes && $v_fc_showRoute($routes);
		}
	}
}