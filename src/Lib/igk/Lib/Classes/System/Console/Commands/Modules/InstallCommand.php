<?php
// @author: C.A.D. BONDJE DOUE
// @file: InstallCommand.php
// @date: 20230702 19:01:23
namespace IGK\System\Console\Commands\Modules;

use IGK\Helper\Activator;
use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Installers\ModuleInstaller;
use IGK\System\IO\Path;
use IGK\System\Modules\ModuleManager;
use IGKConstants;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Modules
*/
class InstallCommand extends AppExecCommand{
	var $command='--module:install';
	const URL = IGKConstants::MODULE_PACKAGE_LIST_URI;
	var $desc='install module package info';
	/* var $options=[]; */
	var $category = 'module';
	public function exec($command, string $module_name = null) { 

		empty($module_name) && igk_die('required module name');
 
		if ($result = igk_curl_post_uri(self::URL.$module_name,null,null,[
			'Content-Type:application/json'
		])){
			$status = igk_curl_status();			
			if ($status != 200){
				igk_die("missing : .$status");			
			}
			$info = igk_curl_info();
			if (($res = Activator::CreateNewInstance(ModuleInstaller::class, json_decode($result))) instanceof ModuleInstaller){
				Logger::info('Install : '. $res->name);

				$file = igk_io_tempfile();
				$tempdir = tempnam(sys_get_temp_dir(), 'blfmod-');
				$rname = igk_getv(explode(':', $module_name), 0);
				@unlink($tempdir);
				IO::CreateDir($tempdir);
				Logger::warn('unzip to : '.$tempdir);
				igk_io_w2file($file, $result);
				igk_zip_unzip($file, $tempdir );   
				
				$target = Path::Combine(igk_get_module_dir(), $rname);
				if (is_dir($target)){
					Logger::info('remove target '. $target);
					IO::RmDir($target);
				}
				IO::CreateDir(dirname($target));
				rename($tempdir."/application_module_controller", $target);
				IO::RmDir($tempdir);
				$res->install(); 
				ModuleManager::ResetModuleCache();
				Logger::success('install modules : '.$module_name);
				return 1;

			} 
		} else {
			Logger::danger('missing or ...');
			return -1;
		}

	}
}