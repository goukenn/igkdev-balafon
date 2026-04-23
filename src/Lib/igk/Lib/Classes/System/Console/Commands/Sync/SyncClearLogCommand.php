<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SyncClearLogCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands\Sync; 
use IGK\System\Console\Logger;

/**
 * clear sites session 
 *  */
class SyncClearLogCommand extends SyncAppExecCommandBase{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--sync:clearlogs";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "sync";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "sync:ftp clear logs";
    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command) { 
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        }
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        Logger::info(sprintf("remove cache from ftp://%s%s",$setting["server"], $setting["application_dir"]));
        $this->_removeLogs($h, $setting["application_dir"]."/Data");
        ftp_close($h);
        error_clear_last();
    }
    /**
     * remove logs 
     * @param mixed $h 
     * @param string $dir 
     * @return void 
     */
    private function _removeLogs($h, string $dir){
        $this->emptyDir($h, $dir); 
    }
}