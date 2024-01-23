<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequireModuleCommand.php
// @date: 20231016 15:30:01
namespace IGK\System\Console\Commands\Project;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonConfiguration;
use stdClass;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Project
*/
class RequireModuleCommand extends AppExecCommand{
	var $command='--project:require';
	var $desc='add required to required configuration'; 
	/* var $options=[]; */
	var $category = 'project';
	var $usage = 'controller module [options]';
	
	public function exec($command, ?string $controller=null, ?string $module_name=null) {
		$project = self::GetController($controller);
		($module = igk_get_module($module_name)) || igk_die_exception(\IGKException::class, "missing module name");
	
		$m_module_config = $module->getModuleConfig();

		$configs = BalafonConfiguration::LoadConfig($project);
		$require = igk_conf_get($configs, 'required');
		if (!$require){
			$configs->required = (object)[];
		}
		$n = igk_str_ns(igk_getv($m_module_config, 'name'));
		$v =  igk_getv($m_module_config, 'version');
		$require->{$n} = $v;

		BalafonConfiguration::StoreConfig($project, $configs);
	
	}
}