<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExecCommandInSyncCommand.php
// @date: 20250612 15:30:43
namespace IGK\System\Console\Commands\Sync;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* 
* @package IGK\System\Console\Commands\Sync
* @author C.A.D. BONDJE DOUE
*/
class ExecCommandInSyncCommand extends SyncAppExecCommandBase{
	var $command='--sync:command';
	var $desc='execute balafon sync command';
	/* var $options=[]; */
	/* var $category = ''; */
	/* var $usage = ''; */
	public function exec($command, ...$args) {

		($h = $this->start($command, $setting)) || igk_die('missing config to sync');
   		if ($install_source = self::GetScriptInstall([			
            'sync/sync-command.pinc',
        ], $token)){
			$tmpfile = igk_io_tempfile('sync-cmd');
			igk_io_w2file($tmpfile, $install_source);
     		$pdir = $setting["public_dir"];
			ftp_put($h, $pdir . "/command.php",  $tmpfile, FTP_BINARY);
			@unlink($tmpfile);

			$uri = $setting["site_uri"];

			sleep(2);
            $response = igk_curl_post_uri($uri."/command.php", 
                [
                    "corelib"=>$setting["lib_dir"],
                    "token"=>$token,
                    "app_dir"=>$setting["application_dir"],
                    "home_dir"=>$setting["home_dir"],
                    "root_dir"=>$setting["public_dir"],
                    self::SITE_DIR=>$setting["site_dir"], 
					"cmd_arg"=>$args
                ], null, [
                "install-token"=>$token
            ]);
			Logger::info('response');
			print_r($response);
			Logger::print('');
		}

        ftp_close($h);
        Logger::success("done"); 
	}
}