<?php
// @author: C.A.D. BONDJE DOUE
// @file: BackupsCommand.php
// @date: 20260822 18:36:41
namespace IGK\System\Console\Commands\Core;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* 
* @package IGK\System\Console\Commands\Core
* @author C.A.D. BONDJE DOUE
*/
class BackupsCommand extends AppExecCommand{
	var $command='--backups';
	var $desc='backup management';
	/* var $options=[]; */
	/* var $category = ''; */
	var $usage = 'action [options]';
	public function exec($command, ?string $action= null) { 
		$action ?? igk_die('require action');
		if (method_exists($this, $fc = '_action_'.$action)){
			return call_user_func_array([$this, $fc], array_merge([$command], array_slice(func_get_args(), 2)));
		}
	}
	public function help()
	{
		$r = parent::help();
		$func = [];
		foreach(igk_sys_reflect_class($this)->getMethods() as $m){
			if ($m->isStatic())continue;
			$n = $m->getName();
			if (preg_match('/_action_.+/', $n)){
				$n = substr($n, strlen('_action_'));
				$func[] = $n;
			}
		}
		echo "Location: ".IGK_LIB_DIR."\n";
		sort($func);
		if ($func){
			echo "Available actions";
			echo "\n".implode("\n", $func);
			echo "\n\n";
		}
		return $r;
	}

	/**
	 * 
	 * @param mixed $a 
	 * @return void 
	 */
	private function _view_file($a){
		return sprintf('%s - %s', basename($a), IO::GetFileSize(filesize($a)));
	}
	protected function _action_ls(){
		$r = IO::GetFiles(dirname(IGK_LIB_DIR), '/\.zip$/', false);
		if ($r){
			$T = 0;
			echo implode("\n", array_map(function($a)use(& $T){
				$T += filesize($a);
				return $this->_view_file($a);
			}, $r))."\n";	

			echo sprintf("Result ( %s / %s)". count($r), IO::GetFileSize($T)). "\n";

		} else{
			echo "(no backup)".PHP_EOL;
		}
	}
	protected function _action_rm($command, ?string $file = null){
		if (empty($file) || (count(explode('/', igk_uri($file),2))!=1)){
			Logger::danger('file not valid');
			return;
		}
		$f = dirname(IGK_LIB_DIR)."/".$file;
		if (file_exists($f)){
			@unlink($f);
		}
		return $this->_action_ls();
	}
	protected function _action_clear(){
		$r = IO::GetFiles(dirname(IGK_LIB_DIR), '/\.zip$/', false);
		if ($r){
			foreach($r as $t){
				@unlink($t);
			}
		}
	}
}