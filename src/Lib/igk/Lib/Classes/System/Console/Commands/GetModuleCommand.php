<?php
// @author: C.A.D. BONDJE DOUE
// @file: GetModuleCommand.php
// @date: 20230403 23:45:37
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class GetModuleCommand extends AppExecCommand{
	var $command='--get-module';
	var $desc='get module command';
	/* var $options=[]; */
	/* var \$category; */
	public function exec($command, string $name = null, string $package_site=null) { 
		empty($name) && igk_die("require module name");


		$site = "https://igkdev.com/balafon/get-modules";
		// check module exists
		$mod = igk_get_module($name);
		$v = null;
		if ($mod){
			$v = $mod->config('version');
		}

		if ($ref = igk_curl_post_uri($site, ['name'=>$name, 'version'=>$v])){
			Logger::info("found and install. ".$name);

			// extract 
			Logger::print($ref);

			// runr 
		} else {
			Logger::danger(igk_curl_lasterror());
		}
	}
}