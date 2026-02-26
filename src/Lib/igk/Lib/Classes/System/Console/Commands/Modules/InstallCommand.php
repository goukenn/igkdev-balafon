<?php
// @author: C.A.D. BONDJE DOUE
// @file: InstallCommand.php
// @date: 20230702 19:01:23
namespace IGK\System\Console\Commands\Modules;

use IGK\Helper\Activator;
use IGK\Helper\IO;
use IGK\Constants;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Installers\ModuleInstaller;
use IGK\System\IO\Path;
use IGK\System\Modules\ModuleManager;
use function igk_resources_gets as __;

/**
 * 
 * @package IGK\System\Console\Commands\Modules
 */
class InstallCommand extends AppExecCommand
{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--module:install';

    /**
    * Constant: url.
    * @var mixed
    */
    const URL = Constants::MODULE_PACKAGE_LIST_URI;

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'install module package';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		'--force'=>'flag: for new installation'
	];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'module';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $module_name
    */
    public function exec($command, ?string $module_name = null)
	{
		empty($module_name) && igk_die('required module name');
		$force = property_exists($command->options, '--force');
		$mod = igk_get_module($module_name);
		if (!$force && $mod){
			igk_die('module already exists');
		}


		if ($result = igk_curl_post_uri(self::URL . base64_encode($module_name), null, null, [
			'Content-Type:application/json'
		])) {
			$status = igk_curl_status();
			if ($status != 200) {
				igk_die("missing : .$status");
			}
			$info = igk_curl_info();
			$type = igk_getv($info, 'Content-Type') ;
			if ($type == 'application/json') {
				if (($res = Activator::CreateNewInstance(ModuleInstaller::class, json_decode($result))) instanceof ModuleInstaller) {
					Logger::info('Install : ' . $res->name);
					$file = igk_io_tempfile();
					$tempdir = tempnam(sys_get_temp_dir(), 'blfmod-');
					$rname = igk_getv(explode(':', $module_name), 0);
					@unlink($tempdir);
					IO::CreateDir($tempdir);
					Logger::warn('unzip to : ' . $tempdir);
					igk_io_w2file($file, $result);
					igk_zip_unzip($file, $tempdir);
					$target = Path::Combine(igk_get_module_dir(), $rname);
					if (is_dir($target)) {
						Logger::info('remove target ' . $target);
						IO::RmDir($target);
					}
					IO::CreateDir(dirname($target));
					rename($tempdir . "/application_module_controller", $target);
					IO::RmDir($tempdir);
					$res->install();
					ModuleManager::ResetModuleCache();
					Logger::success('install modules : ' . $module_name);
					return 1;
				}
			} else {
				Logger::danger(sprintf(__('module [%s] not found'), $module_name) );
				return -2;
			}
		} else {
			Logger::danger('missing or ...');
			return -1;
		}
	}
}
